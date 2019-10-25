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

namespace criteria_childcompetency;

use core\orm\query\builder;
use totara_competency\achievement_configuration;
use totara_competency\entities\configuration_change;
use totara_competency\entities\competency as competency_entity;
use totara_criteria\entities\criterion as criterion_entity;
use totara_criteria\entities\criteria_item as item_entity;
use totara_criteria\event\criteria_items_updated;

class items_processor {

    /**
     * Update the criterion items so that a criterion_item exist for each direct child competency of the applicable
     * competency
     * Not generating any events to be picked up by modules that use this type of criteria (e.g. criteria_group)
     * as the change is not user specific and therefore may result in a lot of work to be completed by the observers.
     *
     * @param int $competency_id Update items of this
     */
    public static function update_items(int $competency_id) {
        global $DB;

        $item_type = (new childcompetency())->get_items_type();

        // Get all childcompetency criteria linked to the competency.
        $criteria = criterion_entity::repository()
            ->set_filter('competency', $competency_id)
            ->where('plugin_type', 'childcompetency')
            ->get();

        if (empty($criteria)) {
            // Nothing to do
            return;
        }

        // We use the same action time for all updates to ensure that we only log changes once
        // if the competency has more than one childcompetency criteria
        $now = time();

        // We get a configuration dump before we start making changes for the specific competency
        // so that we can dump history if anything did change
        $competency_dump = achievement_configuration::get_current_configuration_dump($competency_id);
        $child_competency_ids = competency_entity::repository()
            ->where('parentid', $competency_id)
            ->get()
            ->pluck('id');
        $configuration_has_changed = false;
        $updated_criteria_ids = [];

        $transaction = $DB->start_delegated_transaction();

        foreach ($criteria as $criterion) {
            // Get the existing items - This is ok for now as all items linked to a specific criterion currently
            // will have the same item_type
            $current_item_ids = $criterion->items
                ->pluck('item_id');

            $to_add = array_diff($child_competency_ids, $current_item_ids);
            $to_delete = array_diff($current_item_ids, $child_competency_ids);

            if (empty($to_add) && empty($to_delete)) {
                break 1;
            }

            $updated_criteria_ids[] = $criterion->id;
            $configuration_has_changed = true;

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

        // Now write competency history and change logs
        if ($configuration_has_changed) {
            $config = new achievement_configuration(new competency_entity($competency_id));
            $config->save_configuration_history($now, $competency_dump);
            configuration_change::add_competency_entry($competency_id, configuration_change::CHANGED_CRITERIA, $now);

            criteria_items_updated::create_with_ids($updated_criteria_ids)->trigger();
        }

        $transaction->allow_commit();
    }
}
