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
use totara_competency\pathway;
use totara_core\advanced_feature;

class aggregation_helper {

    /**
     * Add entries to totara_competency_aggregation_queue for each user in competencies with a pathway with
     * any of the provided criteria
     *
     * @param array $user_criteria_ids Criteria ids per user
     */
    public static function mark_for_reaggregate_from_criteria(array $user_criteria_ids) {
        if (empty($user_criteria_ids)) {
            return;
        }

        global $DB;

        $all_criteria_ids = array_unique(array_merge(...$user_criteria_ids));

        [$criteria_ids_sql, $criteria_ids_params] = $DB->get_in_or_equal($all_criteria_ids, SQL_PARAMS_NAMED);
        [$user_ids_sql, $user_ids_params] = $DB->get_in_or_equal(array_keys($user_criteria_ids), SQL_PARAMS_NAMED);

        $unique_id = $DB->sql_concat_join("'_'", ['tcau.competency_id', 'tcau.user_id']);

        $assignment_users_table = \totara_competency\aggregation_helper::get_assigned_users_sql_table();

        $sql = "SELECT DISTINCT {$unique_id} as id,
                       tcau.competency_id, 
                       tcau.user_id
                  FROM {$assignment_users_table} tcau
                  JOIN {totara_competency_pathway} tcp
                    ON tcau.competency_id = tcp.comp_id
                  JOIN {pathway_criteria_group_criterion} pcgc
                    ON pcgc.criteria_group_id = tcp.path_instance_id
                 WHERE tcp.path_type = :pathtype
                   AND tcp.status = :activestatus
                   AND pcgc.criterion_id {$criteria_ids_sql}
                   AND tcau.user_id {$user_ids_sql}";

        $params = [
            'pathtype' => 'criteria_group',
            'activestatus' => pathway::PATHWAY_STATUS_ACTIVE,
        ];

        $params = array_merge($params, $criteria_ids_params, $user_ids_params);

        $rows = $DB->get_records_sql($sql, $params);

        $to_queue = [];
        foreach ($rows as $row) {
            $to_queue[] = [
                'user_id' => $row->user_id,
                'competency_id' => $row->competency_id
            ];
        }

        (new aggregation_users_table())->queue_multiple_for_aggregation($to_queue);
    }

}
