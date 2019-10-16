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

use core\task\manager;
use totara_competency\pathway;
use totara_competency\pathway_evaluator_factory;
use totara_competency\pathway_evaluator_user_source_list;
use totara_competency\task\competency_achievement_aggregation_adhoc;

class aggregation_helper {

    /**
     * Retrieve the pathways containing the specified criterion ids. Only return pathways linked to competencies
     * to which the user is assigned.
     * @param int $user_id
     * @param array $criteria_ids
     * @return array of pathways
     */
    private static function get_pathways_containing_criteria(int $user_id, array $criteria_ids): array {
        global $DB;

        if (empty($criteria_ids)) {
            return [];
        }

        // Get the ids of the competencies to which these criteria are linked AND the user is assigned ot
        [$criteria_id_sql, $params] = $DB->get_in_or_equal($criteria_ids, SQL_PARAMS_NAMED);

        $sql = "SELECT DISTINCT tcp.id
                  FROM {totara_competency_pathway} tcp
                  JOIN {pathway_criteria_group} pcg
                    ON pcg.id = tcp.path_instance_id
                  JOIN {pathway_criteria_group_criterion} pcgc
                    ON pcgc.criteria_group_id = pcg.id
                 WHERE tcp.path_type = :pathtype
                   AND tcp.status = :activestatus
                   AND tcp.comp_id IN (
                       SELECT DISTINCT tacu.competency_id
                         FROM {totara_assignment_competency_users} tacu
                        WHERE user_id = :userid
                       )
                   AND pcgc.criterion_id {$criteria_id_sql}";
        $params['pathtype'] = 'criteria_group';
        $params['activestatus'] = pathway::PATHWAY_STATUS_ACTIVE;
        $params['userid'] = $user_id;

        $pathway_ids = $DB->get_fieldset_sql($sql, $params);

        $pathways = [];
        foreach ($pathway_ids as $pathway_id) {
            $pathways[] = criteria_group::fetch($pathway_id);
        }

        return $pathways;
    }

    /**
     * Determine which pathways contain the specified criteria
     * Evaluate the user's pathway_achievement on each pathway
     * Then trigger an ad-hoc task to aggregate the competency achievement(s) to which these pathways belong
     *
     * @param $user_id
     * @param $criteria_ids
     * @throws \coding_exception
     */
    public static function aggregate_based_on_criteria($user_id, $criteria_ids) {
        global $DB;

        if (empty($criteria_ids)) {
            return;
        }

        $pathways = static::get_pathways_containing_criteria($user_id, $criteria_ids);
        if (empty($pathways)) {
            return;
        }

        $user_id_source = new pathway_evaluator_user_source_list([$user_id], false);
        $competency_ids = [];
        $aggregation_time = time();

        foreach ($pathways as $pathway) {
            $pw_evaluator = pathway_evaluator_factory::create($pathway, $user_id_source);
            $pw_evaluator->aggregate($aggregation_time);
            $competency_ids[$pathway->get_competency()->id] = $pathway->get_competency()->id;
        }

        if (!empty($competency_ids)) {
            $data = (object)[
                'user_id' => $user_id,
                'competency_ids' => array_values($competency_ids)
            ];
            $task = new competency_achievement_aggregation_adhoc();
            $task->set_custom_data($data);
            manager::queue_adhoc_task($task);
        }
    }

}
