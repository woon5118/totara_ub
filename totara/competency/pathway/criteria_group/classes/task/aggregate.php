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
 * @package pathway_criteria_group
 */

namespace pathway_criteria_group\task;

use core\task\scheduled_task;
use pathway_criteria_group\criteria_group;
use totara_competency\pathway;
use totara_competency\pathway_aggregator;
use totara_criteria\criterion;
use totara_criteria\item_evaluator;

class aggregate extends scheduled_task {

    public function get_name() {
        return get_string('aggregatetask', 'pathway_criteria_group');
    }

    public function execute() {
        global $DB;

        $now = time();

        // Calculate for those who achievement record may be out of date.
        $criteria_paths = $DB->get_recordset('totara_competency_pathway',
            ['path_type' => 'criteria_group', 'status' => pathway::PATHWAY_STATUS_ACTIVE],
            '',
            'id');

        foreach ($criteria_paths as $criteria_path) {
            // Before we instantiate the path, check if there are any users, since instantiating could mean several more queries.
            $user_ids = $this->get_users_requiring_aggregation($criteria_path->id);

            if (count($user_ids) > 0) {
                $pathway = criteria_group::fetch($criteria_path->id);
                (new pathway_aggregator($pathway))->aggregate($user_ids, $now);
            }
        }

        $criteria_paths->close();

        // Check for missing item records and add those with criterion_met = 0
        // They will be assessed when the relevant criterion runs.
        $to_create = [];

        $to_calculate = $this->get_users_to_add_item_records_for();
        foreach ($to_calculate as $ids) {
            if (!isset($to_create[$ids->id])) {
                $to_create[$ids->id] = [];
            }
            $to_create[$ids->id][] = $ids->user_id;
        }
        $to_calculate->close();

        foreach ($to_create as $item_id => $user_ids) {
            item_evaluator::create_item_records($item_id, $user_ids);
        }
    }

    public function get_users_requiring_aggregation($criteria_path_id) {
        global $DB;

        return $DB->get_fieldset_sql(
            "
               SELECT DISTINCT tacu.user_id
                 FROM {totara_competency_assignment_users} tacu
                 JOIN {totara_competency_pathway} cp
                   ON tacu.competency_id = cp.comp_id
            LEFT JOIN {totara_competency_pathway_achievement} cupa
                   ON tacu.user_id = cupa.user_id
                  AND cp.id = cupa.pathway_id
                 JOIN {totara_criteria_item_record} tcir
                   ON tacu.user_id = tcir.user_id
                 JOIN {totara_criteria_item} tci
                   ON tcir.criterion_item_id = tci.id
                 JOIN {totara_criteria} tc
                   ON tci.criterion_id = tc.id
                 JOIN {pathway_criteria_group_criterion} pcgc
                   ON tc.id = pcgc.criterion_id
                 JOIN {pathway_criteria_group} pcg
                   ON pcgc.criteria_group_id = pcg.id
                  AND cp.path_instance_id = pcg.id
                WHERE cp.id = ?
                   AND (tcir.timeevaluated > cupa.last_aggregated
                        OR cupa.id IS NULL
                        OR tc.criterion_modified > cupa.last_aggregated)",
            [$criteria_path_id]
        );
    }

    public function get_users_to_add_item_records_for() {
        global $DB;

        $sql = "
               SELECT " . $DB->sql_concat_join("'_'", ["tacu.user_id", "tci.id"]) . ", tacu.user_id, tci.id
                 FROM {totara_competency_assignment_users} tacu
                 JOIN {totara_competency_pathway} cp
                   ON tacu.competency_id = cp.comp_id
                  AND path_type = 'criteria_group'
                 JOIN {pathway_criteria_group} pcg
                   ON cp.path_instance_id = pcg.id
                 JOIN {pathway_criteria_group_criterion} pcgc
                   ON pcgc.criteria_group_id = pcg.id
                 JOIN {totara_criteria} tc
                   ON tc.id = pcgc.criterion_id
                 JOIN {totara_criteria_item} tci
                   ON tci.criterion_id = tc.id
            LEFT JOIN {totara_criteria_item_record} tcir
                   ON tcir.criterion_item_id = tci.id
                  AND tacu.user_id = tcir.user_id
                WHERE tcir.id IS NULL";

        return $DB->get_recordset_sql($sql);
    }
}
