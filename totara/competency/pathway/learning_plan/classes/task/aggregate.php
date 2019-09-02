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
 * @package pathway_learning_plan
 */

namespace pathway_learning_plan\task;

use core\task\scheduled_task;
use pathway_criteria_group\criteria_group;
use totara_competency\pathway;
use totara_competency\pathway_aggregator;

class aggregate extends scheduled_task {

    public function get_name() {
        return get_string('aggregatetask', 'pathway_learning_plan');
    }

    public function execute() {
        global $DB;

        $now = time();

        $learning_plan_paths = $DB->get_recordset(
            'totara_competency_pathway',
            ['path_type' => 'learning_plan', 'status' => pathway::PATHWAY_STATUS_ACTIVE],
            '',
            'id'
        );

        foreach ($learning_plan_paths as $learning_path) {
            $user_ids = $this->get_users_requiring_aggregation($learning_path->id);

            if (count($user_ids) > 0) {
                $pathway = criteria_group::fetch($learning_path->id);
                (new pathway_aggregator($pathway))->aggregate($user_ids, $now);
            }
        }

        $learning_plan_paths->close();
    }

    public function get_users_requiring_aggregation($path_id) {
        global $DB;

        // Gets user ids for users who:
        //  - are assigned to the competency
        //  - have a record in plan competency value
        //  - ... that record is newer than pathway achievement last_aggregated,
        //    or there is no corresponding pathway achievement

        return $DB->get_fieldset_sql(
            "
               SELECT DISTINCT tacu.user_id
                 FROM {totara_assignment_competency_users} tacu
                 JOIN {dp_plan_competency_value} pcv
                   ON tacu.competency_id = pcv.competency_id
                 JOIN {totara_competency_pathway} cp
                   ON tacu.competency_id = cp.comp_id
            LEFT JOIN {totara_competency_pathway_achievement} cupa
                   ON tacu.user_id = cupa.user_id
                  AND cp.id = cupa.pathway_id
                WHERE cp.id = ?
                  AND (pcv.date_assigned > cupa.last_aggregated
                        OR cupa.id IS NULL)",
            [$path_id]
        );
    }
}
