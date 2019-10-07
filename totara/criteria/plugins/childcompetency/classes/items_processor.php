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

use totara_competency\achievement_configuration;
use totara_competency\entities\competency;
use totara_competency\entities\configuration_change;
use totara_competency\pathway;
use totara_criteria\criterion;

class items_processor {

    /**
     * Update the criterion items so that a criterion_item exist for each direct child competency of the appicable
     * competency
     *
     * @param int $competency_id If null, update items for all competencies with childcompetency criteria
     */
    public static function update_items(?int $competency_id = null) {
        global $DB;

        // Get all childcompetency criteria linked to a specific or any competency.
        $competency_where = '';
        $competency_params = [];

        if (!is_null($competency_id)) {
            $competency_where = ' AND tcm.metavalue = :comp_id';
            $competency_params = ['comp_id' => $competency_id];
        }

        $sql =
            "SELECT tc.id, 
                    tcm.metavalue as comp_id
               FROM {totara_criteria} tc 
               JOIN {totara_criteria_metadata} tcm 
                 ON tcm.criterion_id = tc.id 
                AND tcm.metakey = :metakey
              WHERE tc.plugin_type = :plugintype
                    $competency_where";

        // Although we need to perform the actions per competency,
        // not doing an order by on the query but rather in code to
        // keep the query as inexpensive as possible
        $params = array_merge(
            $competency_params,
            ['metakey' => criterion::METADATA_COMPETENCY_KEY, 'plugintype' => 'childcompetency']
        );

        $criteria_rows = $DB->get_records_sql_menu($sql, $params);

        if (empty($criteria_rows)) {
            // Nothing to do
            return;
        }

        // We use the same action time for all updates to ensure that we only log changes once
        // for competencies with more than one childcompetency criteria
        $now = time();

        $competencies = [];
        $transaction = $DB->start_delegated_transaction();

        foreach ($criteria_rows as $criterion_id => $comp_id) {
            if (!isset($competencies[$comp_id])) {
                $comp = new \stdClass();

                // We get a configuration dump before we start making changes for a specific competency
                // so that we can dump history if anything did change
                $comp->dump = achievement_configuration::get_current_configuration_dump($comp_id);
                $comp->child_competencies = competency::repository()
                    ->where('parentid', $comp_id)
                    ->get();
                $comp->configuration_has_changed = false;

                $competencies[$comp_id] = $comp;
            }

            $comp = &$competencies[$comp_id];

            $criterion_has_changed = false;

            // Get the existing items
            $items = $DB->get_records_menu('totara_criteria_item',
                ['criterion_id' => $criterion_id, 'item_type' => 'competency'],
                '',
                'item_id, id');

            // Make sure all child competencies have items
            foreach ($comp->child_competencies as $child_competency) {
                if (!isset($items[$child_competency->id])) {
                    $criterion_has_changed = true;

                    $item = new \stdClass();
                    $item->criterion_id = $criterion_id;
                    $item->item_type = 'competency';
                    $item->item_id = $child_competency->id;
                    $DB->insert_record('totara_criteria_item', $item);
                }

                // Unset as we'll be deleting any left over in $items since they'll be considered not needed.
                unset($items[$child_competency->id]);
            }

            if (!empty($items)) {
                $criterion_has_changed = true;

                $DB->delete_records_list('totara_criteria_item_record', 'criterion_item_id', $items);
                $DB->delete_records_list('totara_criteria_item', 'id', $items);
            }

            if ($criterion_has_changed) {
                $DB->set_field('totara_criteria', 'criterion_modified', $now, ['id' => $criterion_id]);
            }

            $comp->configuration_has_changed = $comp->configuration_has_changed || $criterion_has_changed;
        }

        // Now write competency history and change logs
        foreach ($competencies as $comp_id => $comp) {
            if ($comp->configuration_has_changed) {
                $config = new achievement_configuration(new competency($comp_id));
                $config->save_configuration_history($now, $comp->dump);
                configuration_change::add_competency_entry($comp_id, configuration_change::CHANGED_CRITERIA, $now);
            }
        }

        $transaction->allow_commit();
    }
}
