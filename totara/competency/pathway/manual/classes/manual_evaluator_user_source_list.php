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

namespace pathway_manual;

use totara_competency\entities\pathway_achievement;
use totara_competency\pathway;
use totara_competency\pathway_evaluator_user_source_list;

class manual_evaluator_user_source_list extends pathway_evaluator_user_source_list {

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

        // TODO: This is good query to use for creation of indexes / other performance enhancement options
        //       We need the group by to ensure we get the user only once if there were more than 1 rating given for the user
        //       There will be at most 1 active pathway_achievement
        $sql =
            "SELECT usr.id as user_id, 
                    tcpa.id as achievement_id,
                    tcpa.scale_value_id
               FROM {user} usr
          LEFT JOIN {pathway_manual_rating} pmr
                 ON pmr.user_id = usr.id
                AND pmr.comp_id = :compid     
          LEFT JOIN {totara_competency_pathway_achievement} tcpa
                 ON tcpa.pathway_id = :pathwayid
                AND tcpa.user_id = usr.id
                AND tcpa.status = :currentstatus
              WHERE usr.id {$user_sql}
                AND ((pmr.id IS NULL AND tcpa.id IS NULL)
                  OR (pmr.id IS NOT NULL AND tcpa.id IS NULL)
                  OR pmr.date_assigned > tcpa.last_aggregated)
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
