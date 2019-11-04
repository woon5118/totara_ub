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
use totara_competency\entities\competency as competency_entitiy;
use totara_competency\entities\configuration_change;
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

        // Get all linkedcourses criteria linked to the competency .
        $criteria = criterion_entity::repository()
            ->set_filter('competency', $competency_id)
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

        foreach ($criteria as $criterion) {
            // Get the existing items - This is ok for now as all items linked to a specific criterion currently
            // will have the same item_type
            $current_item_ids = $criterion->items
                ->pluck('item_id');

            $to_add = array_diff($linked_course_ids, $current_item_ids);
            $to_delete = array_diff($current_item_ids, $linked_course_ids);

            if (empty($to_add) && empty($to_delete)) {
                break 1;
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
        }

        if ($configuration_has_changed) {
            // Dump the configuration history and log the configuration change
            $config = new achievement_configuration(new competency_entitiy($competency_id));
            $config->save_configuration_history($now, $configuration_dump);
            configuration_change::add_competency_entry($competency_id, configuration_change::CHANGED_CRITERIA, $now);
        }

        $transaction->allow_commit();
    }

}
