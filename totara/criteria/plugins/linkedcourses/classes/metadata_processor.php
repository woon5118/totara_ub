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

use totara_competency\achievement_configuration;
use totara_competency\entities\competency;
use totara_competency\entities\configuration_change;
use totara_competency\linked_courses;
use totara_competency\pathway;
use totara_criteria\criterion;

class metadata_processor {

    /**
     * @param ?int $competency_id If null, update items for all competencies with linkedcourses criteria
     *                            - This couples this to competency pretty tightly. Todo: There might be a better way
     *                              to do this with more metadata.
     */
    public static function update_item_links(?int $competency_id = null) {
        global $DB;

        // Get all linkedcourses criteria linked to a specific or any competency .
        $competency_where = '';
        $competency_params = [];

        if (!is_null($competency_id)) {
            $competency_where = ' AND tcm_comp.metavalue = :comp_id';
            $competency_params = ['comp_id' => $competency_id];
        }

        // Although we need to perform the actions per competency,
        // not doing an order bt on the query but rather in code to keep the query as
        // inexpensive as possible
        $select_criteria_sql =
            "SELECT tc.id AS criterion_id,
                    tcm_comp.metavalue AS comp_id
               FROM {totara_criteria} tc
               JOIN {totara_criteria_metadata} tcm_comp
                 ON tc.id = tcm_comp.criterion_id
                AND tcm_comp.metakey = :compkey
                    $competency_where
              WHERE tc.plugin_type = :plugintype";
        $select_criteria_params = array_merge(
            $competency_params,
            [
                'plugintype' => 'linkedcourses',
                'compkey' => criterion::METADATA_COMPETENCY_KEY,
            ]
        );

        $criteria_rows = $DB->get_records_sql($select_criteria_sql, $select_criteria_params);

        if (empty($criteria_rows)) {
            return;
        }

        // Doing the grouping per competency here
        $competency_criteria = [];

        foreach ($criteria_rows as $row) {
            if (!isset($competency_criteria[$row->comp_id])) {
                $competency_criteria[$row->comp_id] = [];
            }

            $competency_criteria[$row->comp_id][] = $row;
        }

        // We use the same action time for all updates to ensure that we only log changes once
        // for competencies with more than one linkedcourses criteria
        // (Alternative would be to sort the retrieved competencies by comp_id, but that
        // slows down the query a lot)
        $now = time();

        foreach ($competency_criteria as $comp_id => $criteria) {
            // We get a configuration dump before we start making changes for a specific competency
            // so that we can dump history if anything did change
            $configuration_dump = achievement_configuration::get_current_configuration_dump($comp_id);
            $linked_courses_records = linked_courses::get_linked_courses($comp_id);
            $configuration_has_changed = false;

            $transaction = $DB->start_delegated_transaction();

            foreach ($criteria as $criterion) {
                $criterion_has_changed = false;

                // Get the existing items
                $items = $DB->get_records_menu('totara_criteria_item',
                    ['criterion_id' => $criterion->criterion_id, 'item_type' => 'course'],
                    '',
                    'item_id, id');

                // Make sure all courses have items
                foreach ($linked_courses_records as $linked_courses_record) {
                    if (!isset($items[$linked_courses_record->id])) {
                        $criterion_has_changed = true;

                        $item = new \stdClass();
                        $item->criterion_id = $criterion->criterion_id;
                        $item->item_type = 'course';
                        $item->item_id = $linked_courses_record->id;
                        $DB->insert_record('totara_criteria_item', $item);
                    }

                    // Unset as we'll be deleting any left over in $items since they'll be considered not needed.
                    unset($items[$linked_courses_record->id]);
                }

                if (!empty($items)) {
                    $criterion_has_changed = true;

                    $DB->delete_records_list('totara_criteria_item_record', 'criterion_item_id', $items);
                    $DB->delete_records_list('totara_criteria_item', 'id', $items);
                }

                if ($criterion_has_changed) {
                    $DB->set_field('totara_criteria', 'criterion_modified', $now, ['id' => $criterion->criterion_id]);
                }

                $configuration_has_changed = $configuration_has_changed || $criterion_has_changed;
            }

            if ($configuration_has_changed) {
                // Dump the configuration history and log the configuration change

                $config = new achievement_configuration(new competency($comp_id));
                $config->save_configuration_history($now, $configuration_dump);
                configuration_change::add_competency_entry(
                    $config->get_competency()->id,
                    configuration_change::CHANGED_CRITERIA,
                    $now
                );
            }

            $transaction->allow_commit();
        }
    }

}
