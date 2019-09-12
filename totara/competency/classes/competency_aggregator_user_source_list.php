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

class competency_aggregator_user_source_list implements competency_aggregator_user_source {

    /** @var array $user_id_list  */
    protected $user_id_list = null;

    /** @var bool $full_user_set */
    protected $full_user_set = false;

    /**
     * Constructor.
     * @param array $user_id_list Source containing the user ids
     * @param bool $full_user_set Does this source contain all users?
     */
    final public function __construct(array $user_id_list, bool $full_user_set = false) {
        $this->user_id_list = $user_id_list;
        $this->full_user_set = $full_user_set;
    }

    /**
     * Archive achievements of users no longer assigned
     * @param int $competency_id
     * @param int $aggregation_time
     */
    public function archive_non_assigned_achievements(int $competency_id, int $aggregation_time) {
        global $DB;

        if (is_null($this->user_id_list)) {
            return;
        }

        if (!empty($this->user_id_list)) {
            [$users_in_wh, $users_in_params] = $DB->get_in_or_equal($this->user_id_list, SQL_PARAMS_NAMED, 'userin');
            if (!empty($users_in_wh)) {
                $users_in_wh = ' AND user_id ' . $users_in_wh;
            }

            [$users_not_in_wh, $users_not_in_params] = $DB->get_in_or_equal($this->user_id_list, SQL_PARAMS_NAMED, 'usernotin', false);
            if (!empty($users_not_in_wh)) {
                $users_not_in_wh = ' AND user_id ' . $users_not_in_wh;
            }
        } else {
            $users_in_wh = '';
            $users_in_params = [];
            $users_not_in_wh = '';
            $users_not_in_params = [];
        }

        // If this is a full list - archive all users not in the list
        if ($this->full_user_set) {
            $sql =
                "UPDATE {totara_competency_achievement}
                    SET status = :newstatus,
                        time_status = :timestatus
                  WHERE comp_id = :compid
                    AND status = :currentstatus
                        {$users_not_in_wh}";
            $params = array_merge(
                [
                    'compid' => $competency_id,
                    'newstatus' => competency_achievement::ARCHIVED_ASSIGNMENT,
                    'currentstatus' => competency_achievement::ACTIVE_ASSIGNMENT,
                    'timestatus' => $aggregation_time,
                ],
                $users_not_in_params
            );

            $DB->execute($sql, $params);
        }

        // We also need to always archive competency_achievements linked to assignments that are no longer active / available
        $sql =
            "UPDATE {totara_competency_achievement}
                SET status = :newstatus,
                    time_status = :timestatus
              WHERE comp_id = :compid1
                AND status = :currentstatus
                    {$users_in_wh}
                AND assignment_id NOT IN (
                    SELECT tacu.assignment_id
                      FROM {totara_assignment_competency_users} tacu
                     WHERE tacu.competency_id = :compid2)";

            $params = array_merge(
                [
                    'compid1' => $competency_id,
                    'compid2' => $competency_id,
                    'newstatus' => competency_achievement::ARCHIVED_ASSIGNMENT,
                    'currentstatus' => competency_achievement::ACTIVE_ASSIGNMENT,
                    'timestatus' => $aggregation_time,
                ],
                $users_in_params
            );

            $DB->execute($sql, $params);
    }

    /**
     * Get users to consider for reaggregation
     * @param int $competency_id
     * @param int $aggregation_time
     */
    public function get_users_to_reaggregate(int $competency_id, int $aggregation_time): \moodle_recordset {
        global $DB;

        // Find assignments of all users that were marked as having changes
        if (!empty($this->user_id_list)) {
            [$user_id_wh, $params] = $DB->get_in_or_equal($this->user_id_list, SQL_PARAMS_NAMED);
        } else {
            $user_id_wh = '';
            $params = [];
        }
        if (!empty($user_id_wh)) {
            $user_id_wh = "AND tacu.user_id {$user_id_wh}";
        }

        $params['newstatus'] = competency_achievement::ACTIVE_ASSIGNMENT;
        $params['competencyid'] = $competency_id;

        $sql ="SELECT tacu.id,
                    tacu.user_id,
                    tacu.assignment_id,
                    COALESCE(ca.id, NULL) AS comp_achievement_id,
                    COALESCE(ca.scale_value_id, NULL) AS scale_value_id
                 FROM {totara_assignment_competency_users} tacu
            LEFT JOIN {totara_competency_achievement} ca
                   ON tacu.user_id = ca.user_id
                  AND tacu.assignment_id = ca.assignment_id
                  AND ca.status = :newstatus
                WHERE tacu.competency_id = :competencyid
                      {$user_id_wh}";

        return $DB->get_recordset_sql($sql, $params);
    }

}
