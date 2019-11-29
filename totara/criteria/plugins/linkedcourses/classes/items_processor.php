<?php
/*
 * This file is part of Totara Learn
 *
 * Copyright (C) 2019 onwards Totara Learning Solutions LTD
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Brendan Cox <brendan.cox@totaralearning.com>
 * @author Riana Rossouw <riana.rossouw@totaralearning.com>
 * @package totara_criteria
 */

namespace criteria_linkedcourses;

use core\orm\query\builder;
use totara_competency\achievement_configuration;
use totara_competency\aggregation_users_table;
use totara_competency\entities\competency as competency_entitiy;
use totara_competency\entities\configuration_change;
use totara_core\advanced_feature;
use totara_criteria\entities\criteria_metadata;
use totara_criteria\entities\criterion;
use totara_criteria\entities\criterion as criterion_entity;
use totara_criteria\entities\criteria_item as item_entity;
use totara_competency\linked_courses;

class items_processor {

    /**
     * Update the items linked to the criterion to reflect the courses currently linked to the competency
     * @param int $competency_id Id of the competency whose linked course items must be updated
     */
    public static function update_items(int $competency_id) {
        global $DB;

        $item_type = (new linkedcourses())->get_items_type();

        // Get all linkedcourses criteria linked to the competency.
        $criteria = criterion_entity::repository()
            ->join([criteria_metadata::TABLE, 'cm'], 'id', 'criterion_id')
            ->where('cm.metakey', linkedcourses::METADATA_COMPETENCY_KEY)
            ->where('cm.metavalue', $competency_id)
            ->with('items')
            ->where('plugin_type', 'linkedcourses')
            ->get();

        if (empty($criteria)) {
            return;
        }

        // We use the same action time for all updates to ensure that we only log changes once
        // for competencies with more than one linkedcourses criteria
        $now = time();

        // We get a configuration dump before we start making changes for a specific competency
        // so that we can dump history if anything did change
        $configuration_dump = achievement_configuration::get_current_configuration_dump($competency_id);
        $linked_course_ids = linked_courses::get_linked_course_ids($competency_id);
        $configuration_has_changed = false;
        $updated_criteria_ids = [];

        $transaction = $DB->start_delegated_transaction();

        $course_ids = [];
        /** @var criterion $criterion */
        foreach ($criteria as $criterion) {
            // Get the existing items - This is ok for now as all items linked to a specific criterion currently
            // will have the same item_type
            $current_item_ids = $criterion->items->pluck('item_id');

            $to_add = array_diff($linked_course_ids, $current_item_ids);
            $to_delete = array_diff($current_item_ids, $linked_course_ids);

            // This criterion is already up to date so nothing to do here
            if (empty($to_add) && empty($to_delete)) {
                continue;
            }

            $configuration_has_changed = true;
            $updated_criteria_ids[] = $criterion->id;

            foreach ($to_add as $to_add_id) {
                $item = new item_entity();
                $item->criterion_id = $criterion->id;
                $item->item_type = $item_type;
                $item->item_id = $to_add_id;
                $item->save();
            }

            // This will do a cascade delete on item_records as well
            if (!empty($to_delete)) {
                builder::table(item_entity::TABLE)
                    ->where('criterion_id', $criterion->id)
                    ->where('item_type', $item_type)
                    ->where_in('item_id', $to_delete)
                    ->delete();
            }

            $criterion->criterion_modified = $now;
            $criterion->save();

            // We need to consider the removed items for reaggregation as well
            $course_ids = array_merge($course_ids, $linked_course_ids, $to_delete);
        }

        if ($configuration_has_changed) {
            if (advanced_feature::is_enabled('competency_assignment')) {
                // Queue all users assigned to the competency
                (new aggregation_users_table())->queue_all_assigned_users_for_aggregation($competency_id);
            } else {
                // In this case we only want to queue a subset of users as in learn-only
                // we do not use the assignments. We rather look at the completions directly.
                $queue_items = self::get_items_to_queue(array_unique($course_ids), $competency_id);
                (new aggregation_users_table())->queue_multiple_for_aggregation($queue_items);
            }

            // Dump the configuration history and log the configuration change
            $config = new achievement_configuration(new competency_entitiy($competency_id));
            $config->save_configuration_history($now, $configuration_dump);
            // Queued already so skip queueing for this one.
            configuration_change::add_competency_entry($competency_id, configuration_change::CHANGED_CRITERIA, $now, false);
        }

        $transaction->allow_commit();
    }

    /**
     * Get users who have completion records in given courses which we want to queue
     * and return an array of user and competency id we can use for queueing
     *
     * @param array $course_ids
     * @param int $competency_id
     * @return array
     */
    private static function get_items_to_queue(array $course_ids, int $competency_id): array {
        if (empty($course_ids)) {
            return [];
        }

        global $DB;

        $queue_items = [];

        [$courses_to_queue_sql, $courses_to_queue_params] = $DB->get_in_or_equal($course_ids, SQL_PARAMS_NAMED);

        // We only load users who completed a linked course as this is the only
        // reason a value can change in learn-only
        $sql = "
            SELECT cc.userid
                FROM {comp_criteria} coc
                JOIN {course_completions} cc ON cc.course = coc.iteminstance
                WHERE coc.itemtype = :item_type 
                    AND coc.competencyid = :competency_id 
                    AND coc.iteminstance {$courses_to_queue_sql}
                    AND cc.timecompleted > 0
        ";

        $params = [
            'competency_id' => $competency_id,
            'item_type' => 'coursecompletion',
        ];

        $user_ids_to_queue = $DB->get_fieldset_sql($sql, array_merge($courses_to_queue_params, $params));

        foreach ($user_ids_to_queue as $user_id) {
            $queue_items[] = [
                'user_id' => (int) $user_id,
                'competency_id' => (int) $competency_id
            ];
        }

        return $queue_items;
    }

}
