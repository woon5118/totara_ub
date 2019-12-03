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

namespace pathway_criteria_group;

use totara_competency\aggregation_users_table;
use totara_competency\entities\pathway as pathway_entity;
use pathway_criteria_group\entities\criteria_group as criteria_group_entity;
use pathway_criteria_group\entities\criteria_group_criterion as criteria_group_criterion_entity;
use totara_competency\entities\competency_assignment_user as competency_assignment_user_entity;


class aggregation_helper {

    /**
     * Add entries to totara_competency_aggregation_queue for each user in competencies with a pathway with
     * any of the provided criteria
     *
     * @param array $user_criteria_ids Criteria ids per user
     */
    public static function mark_for_reaggregate_from_criteria(array $user_criteria_ids) {
        global $DB;

        if (empty($user_criteria_ids)) {
            return;
        }

        $all_criteria_ids = array_unique(array_merge(...$user_criteria_ids));

        $criteria = criteria_group_criterion_entity::repository()
            ->as('cgc')
            ->join([criteria_group_entity::TABLE, 'cg'], 'cgc.criteria_group_id', 'cg.id')
            ->join([pathway_entity::TABLE, 'pw'], 'cg.id', 'pw.path_instance_id')
            ->select('cgc.criterion_id')
            ->add_select('pw.comp_id')
            ->where('cgc.criterion_id', $all_criteria_ids)
            ->get();

        if (!$criteria->count()) {
            return;
        }

        $competency_criteria = [];
        foreach ($criteria as $criterion) {
            if (!isset($competency_criteria[$criterion->comp_id])) {
                $competency_criteria[$criterion->comp_id] = [];
            }
            $competency_criteria[$criterion->comp_id][] = $criterion->criterion_id;
        }

        $key = 'DISTINCT ' .
            $DB->sql_concat_join("'_'", ['tcau.competency_id', 'tcau.user_id']) .
            ' AS competency_user_key';

        $competency_ids = $criteria->pluck('comp_id');
        $assignments = competency_assignment_user_entity::repository()
            ->as('tcau')
            ->select_raw($key)
            ->add_select('competency_id')
            ->add_select('user_id')
            ->where('competency_id', $competency_ids)
            ->where('user_id', array_keys($user_criteria_ids))
            ->get();

        $to_reaggregate = [];
        foreach ($assignments as $assignment) {
            $common = array_intersect($user_criteria_ids[$assignment->user_id], $competency_criteria[$assignment->competency_id]);
            if (!empty($common)) {
                // User is assigned and has changed criteria achievement in one or more of the competency's criteria
                $to_reaggregate[] = ['user_id' => $assignment->user_id, 'competency_id' => $assignment->competency_id];
            }
        }

        if (!empty($to_reaggregate)) {
            $aggregation_table = new aggregation_users_table();
            $aggregation_table->queue_multiple_for_aggregation($to_reaggregate);
        }
    }

}
