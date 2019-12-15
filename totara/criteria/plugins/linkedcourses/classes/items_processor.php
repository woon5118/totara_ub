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

        if (!$criteria->count()) {
            return;
        }

        // We only need to determine whether there were any changes once
        // All linkedcourses criteria on the same competency have the same items
        $linked_course_ids = linked_courses::get_linked_course_ids($competency_id);

        $current_item_ids = $criteria
            ->first()
            ->items
            ->pluck('item_id');

        $to_add = array_diff($linked_course_ids, $current_item_ids);
        $to_delete = array_diff($current_item_ids, $linked_course_ids);

        // We need to consider the removed items for reaggregation as well
        $course_ids = array_merge($linked_course_ids, $to_delete);

        if (empty($to_add) && empty($to_delete)) {
            // Nothing to do
            return;
        }

        // We use the same action time for all updates to ensure that we only log changes once
        // for competencies with more than one linkedcourses criteria
        $now = time();

        // We get a configuration dump before we start making changes for a specific competency
        // so that we can dump history if anything did change
        $configuration_dump = achievement_configuration::get_current_configuration_dump($competency_id);

        // Saving through criteria_linkedcourses instance instead of entity to ensure validation checks are done
        $transaction = $DB->start_delegated_transaction();

        /** @var criterion $criterion */
        foreach ($criteria as $criterion) {
            $linkedcourses = linkedcourses::fetch_from_entity($criterion);
            $linkedcourses->set_item_ids($linked_course_ids);
            $linkedcourses->save();
        }

        // Not triggering validity_changed here as it will be triggered through saving each criterion.
        // Although a bulk trigger would be better for performance, we can't avoid calling save on the criterion
        // as the items also needs to be updated

        // TODO: Reduce dependancy between totara_criteria and totara_competency here
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
        configuration_change::add_competency_entry($competency_id, configuration_change::CHANGED_CRITERIA, $now);

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

        // We only load users who completed a linked course as this is the only
        // reason a value can change in learn-only
        $user_ids_to_queue = builder::table('course_completions')
            ->select_raw('DISTINCT userid as userid')
            ->where('course', $course_ids)
            ->where('timecompleted', '>', 0)
            ->get()
            ->pluck('userid');

        $queue_items = [];
        foreach ($user_ids_to_queue as $user_id) {
            $queue_items[] = [
                'user_id' => (int) $user_id,
                'competency_id' => (int) $competency_id
            ];
        }

        return $queue_items;
    }

}
