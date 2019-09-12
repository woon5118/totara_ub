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
 * @author Riana Rossouw <riana.rossouw@totaralearning.com>
 * @package totara_competency
 */

namespace pathway_learning_plan;

use totara_competency\entities\pathway_achievement;
use totara_competency\pathway;
use totara_competency\pathway_evaluator_user_source_list;

class learning_plan_evaluator_user_source_list extends pathway_evaluator_user_source_list {

    /**
     * Find users to reaggregate. This takes the time that the users were rated into consideration
     * @param pathway $pathway
     * @param int $aggregation_time
     */
    public function get_users_to_reaggregate(pathway $pathway, int $aggregation_time): \moodle_recordset {
        global $DB;

        if (empty($this->user_id_list)) {
            return [];
        }

        // Aggregate if
        //     the user has neither a rating, nor an achievement record (will result in the user getting an achievement record during aggregation),
        //  OR the user has a rating, but not yet an achievement record
        //  OR the user has a new rating
        [$user_sql, $user_params] = $DB->get_in_or_equal($this->user_id_list, SQL_PARAMS_NAMED);

        $sql =
            "SELECT usr.id as user_id, 
                    tcpa.id as achievement_id,
                    tcpa.scale_value_id
               FROM {user} usr
          LEFT JOIN {dp_plan_competency_value} dpplan
                 ON dpplan.user_id = usr.id
                AND dpplan.competency_id = :compid     
          LEFT JOIN {totara_competency_pathway_achievement} tcpa
                 ON tcpa.pathway_id = :pathwayid
                AND tcpa.user_id = usr.id
                AND tcpa.status = :currentstatus
              WHERE usr.id {$user_sql}
                AND ((dpplan.id IS NULL AND tcpa.id IS NULL)
                  OR (dpplan.id IS NOT NULL AND tcpa.id IS NULL)
                  OR dpplan.date_assigned > tcpa.last_aggregated)
               GROUP BY usr.id, tcpa.id, tcpa.scale_value_id";

        $params = array_merge(
            $user_params,
            [
                'compid' => $pathway->get_competency()->id,
                'pathwayid' => $pathway->get_id(),
                'currentstatus' => pathway_achievement::STATUS_CURRENT,
            ]);

        return $DB->get_recordset_sql($sql, $params);
    }

}
