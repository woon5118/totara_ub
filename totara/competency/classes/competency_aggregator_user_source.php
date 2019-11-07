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

namespace totara_competency;

use totara_competency\entities\competency_achievement;

class competency_aggregator_user_source {

    /** @var aggregation_users_table $temp_user_table  */
    protected $temp_user_table = null;

    /** @var bool $full_user_set */
    protected $full_user_set = false;

    /**
     * Constructor.
     * @param aggregation_users_table $temp_user_table Source containing the user ids
     * @param bool $full_user_set Does this source contain all users?
     */
    public function __construct(aggregation_users_table $temp_user_table, bool $full_user_set = false) {
        $this->temp_user_table = $temp_user_table;
        $this->full_user_set = $full_user_set;
    }

    public function set_competency_id_value(?int $competency_id) {
        $this->temp_user_table->set_comptency_id_value($competency_id);
    }

    /**
     * Archive achievements of users no longer assigned
     * @param int $competency_id
     * @param int $aggregation_time
     */
    public function archive_non_assigned_achievements(int $competency_id, int $aggregation_time) {
        global $DB;

        if (is_null($this->temp_user_table)) {
            return;
        }

        $temp_table_name = $this->temp_user_table->get_table_name();
        $temp_user_id_column = $this->temp_user_table->get_user_id_column();
        [$temp_wh, $temp_wh_params] = $this->temp_user_table->get_filter_sql_with_params('', false, null);
        if (!empty($temp_wh)) {
            $temp_wh = ' WHERE ' . $temp_wh;
        }

        // If this is a full list - archive all users not assigned
        if ($this->full_user_set) {
            $sql =
                "UPDATE {totara_competency_achievement}
                    SET status = :newstatus,
                        time_status = :timestatus
                  WHERE comp_id = :compid
                    AND status = :currentstatus
                    AND user_id NOT IN (
                        SELECT {$temp_user_id_column}
                          FROM {" . $temp_table_name . "}
                          {$temp_wh}
                        )";
            $params = array_merge(
                [
                    'compid' => $competency_id,
                    'newstatus' => competency_achievement::ARCHIVED_ASSIGNMENT,
                    'currentstatus' => competency_achievement::ACTIVE_ASSIGNMENT,
                    'timestatus' => $aggregation_time,
                ],
                $temp_wh_params
            );

            $DB->execute($sql, $params);
        }

        // We also need to always archive competency_achievements linked to assignments that are no longer active / available
        $sql =
            "UPDATE {totara_competency_achievement}
                SET status = :newstatus,
                    time_status = :timestatus
              WHERE comp_id = :compid
                AND status = :currentstatus
                AND user_id IN (
                    SELECT {$temp_user_id_column}
                      FROM {" . $temp_table_name . "}
                      {$temp_wh}
                    )
                AND assignment_id NOT IN (
                    SELECT tacu.assignment_id
                      FROM {totara_assignment_competency_users} tacu
                     WHERE tacu.competency_id = :compid2
                    )";

        $params = array_merge(
            [
                'compid' => $competency_id,
                'compid2' => $competency_id,
                'newstatus' => competency_achievement::ARCHIVED_ASSIGNMENT,
                'currentstatus' => competency_achievement::ACTIVE_ASSIGNMENT,
                'timestatus' => $aggregation_time,
            ],
            $temp_wh_params
        );

        $DB->execute($sql, $params);
    }

    /**
     * Get users to consider for reaggregation
     *
     * @param int $competency_id
     * @param int $aggregation_time
     * @return \moodle_recordset
     */
    public function get_users_to_reaggregate(int $competency_id, int $aggregation_time): \moodle_recordset {
        global $DB;

        // Find assignments of all users that were marked as having changes
        $temp_alias = 'tmp';
        $temp_tablename = $this->temp_user_table->get_table_name();
        $temp_user_id_column = $this->temp_user_table->get_user_id_column();
        $temp_competency_id_column = $this->temp_user_table->get_competency_id_column();
        [$user_id_wh, $params] = $this->temp_user_table->get_filter_sql_with_params($temp_alias, false, 1);
        $params['newstatus'] = competency_achievement::ACTIVE_ASSIGNMENT;
        $params['competencyid'] = $competency_id;

        $sql = "SELECT tacu.id,
                    tacu.user_id,
                    tacu.assignment_id,
                    COALESCE(ca.id, NULL) AS comp_achievement_id,
                    COALESCE(ca.scale_value_id, NULL) AS scale_value_id
                 FROM {totara_assignment_competency_users} tacu
                 JOIN {" . $temp_tablename . "} {$temp_alias} 
                   ON tacu.user_id = {$temp_alias}.{$temp_user_id_column} 
                  AND tacu.competency_id = {$temp_alias}.{$temp_competency_id_column}
            LEFT JOIN {totara_competency_achievement} ca
                   ON tacu.user_id = ca.user_id
                  AND tacu.assignment_id = ca.assignment_id
                  AND ca.status = :newstatus
                WHERE tacu.competency_id = :competencyid
                  AND {$user_id_wh}";

        return $DB->get_recordset_sql($sql, $params);
    }

}
