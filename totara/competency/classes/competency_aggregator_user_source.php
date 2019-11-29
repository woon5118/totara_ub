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

use core\orm\collection;
use core\orm\query\builder;
use stdClass;
use totara_competency\entities\assignment;
use totara_competency\entities\competency_achievement;
use totara_competency\entities\competency_assignment_user;
use totara_core\advanced_feature;

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
        // We do not use the assignments in learn-only so we can skip this
        if (!advanced_feature::is_enabled('competency_assignment')) {
            return;
        }

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
            $sql = "
                UPDATE {totara_competency_achievement}
                SET status = :newstatus, time_status = :timestatus
                WHERE comp_id = :compid
                    AND status = :currentstatus
                    AND user_id NOT IN (
                        SELECT {$temp_user_id_column}
                        FROM {" . $temp_table_name . "}
                            {$temp_wh}
                    )
            ";
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
        $sql = "
            UPDATE {totara_competency_achievement}
            SET status = :newstatus, time_status = :timestatus
            WHERE comp_id = :compid
                AND status = :currentstatus
                AND user_id IN (
                    SELECT {$temp_user_id_column}
                    FROM {" . $temp_table_name . "}
                        {$temp_wh}
                )
                AND assignment_id NOT IN (
                    SELECT assignment_id
                    FROM {totara_competency_assignment_users}
                    WHERE competency_id = :compid2
                )
        ";

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
     * @return collection
     */
    public function get_users_to_reaggregate(int $competency_id) {
        if (!advanced_feature::is_enabled('competency_assignment')) {
            return $this->get_users_to_reaggregate_legacy($competency_id);
        }

        // Find assignments of all users that were marked as having changes
        $subquery = builder::table($this->temp_user_table->get_table_name())
            ->select($this->temp_user_table->get_user_id_column())
            ->where_raw(...$this->temp_user_table->get_filter_sql_with_params("", false, 1));

        // First we get all user assignments for the competency where we have users
        // in the queue table who are marked as having changes
        $assignment_users = competency_assignment_user::repository()
            ->where('competency_id', $competency_id)
            ->where('user_id', 'in', $subquery)
            ->get();

        // Now for all of the assignments / user combinations query
        // the active achievements. This reduces the number of queries
        // down to only two and avoids the N+1 problem here
        $achievements = competency_achievement::repository()
            ->where('assignment_id', $assignment_users->pluck('assignment_id'))
            ->where('user_id', 'in', $assignment_users->pluck('user_id'))
            ->where('status', competency_achievement::ACTIVE_ASSIGNMENT)
            ->get();

        // Now combine the two results returning, mapping the achievement to the assignment user record
        return $assignment_users->map(function (competency_assignment_user $assignment_user) use ($achievements) {
            $achievement = $achievements->find(function (competency_achievement $achievement) use ($assignment_user) {
                return $achievement->assignment_id == $assignment_user->assignment_id
                    && $achievement->user_id == $assignment_user->user_id;
            });

            return (object) [
                'user_id' => $assignment_user->user_id,
                'assignment_id' => $assignment_user->assignment_id,
                'achievement' => $achievement
            ];
        });
    }

    /**
     * Get users to consider for reaggregation. We load all users who can potentially
     * receive a value due the competency being in a learning plan or completed a course linked to a competency
     *
     * @param int $competency_id
     * @return collection
     */
    private function get_users_to_reaggregate_legacy(int $competency_id) {
        global $DB, $CFG;

        [$q_cond, $q_params] = $this->temp_user_table->get_filter_sql_with_params("q", false, 1);

        $table_name = $this->temp_user_table->get_table_name();
        $user_id_column = $this->temp_user_table->get_user_id_column();
        $competency_id_column = $this->temp_user_table->get_competency_id_column();

        $exists_sql = [];
        if ($CFG->enablecompletion) {
            $exists_sql[] = "
                EXISTS (
                    -- with completion records in courses linked to competencies (or children)
                    SELECT coc.id
                    FROM {comp_criteria} coc
                    JOIN {course_completions} cc ON cc.course = coc.iteminstance
                    JOIN {comp} c ON coc.competencyid = c.id
                    WHERE coc.itemtype = 'coursecompletion'
                        AND cc.timecompleted > 0
                        AND (c.id = q.{$competency_id_column} OR c.parentid = q.{$competency_id_column})
                        AND cc.userid = q.{$user_id_column}
                )            
            ";
        }

        // Only consider learning plans if its enabled
        if (advanced_feature::is_enabled('learningplans')) {
            $exists_sql[] = "
                EXISTS (
                    -- with competencies (or children) assigned to learning plans
                    SELECT pcv.id
                    FROM {dp_plan_competency_value} pcv
                    JOIN {comp} c ON pcv.competency_id = c.id
                    WHERE (c.id = q.{$competency_id_column} OR c.parentid = q.{$competency_id_column})
                         AND pcv.user_id = q.{$user_id_column}
                )
            ";
        }

        // Nothing to query for so return empty handed
        if (empty($exists_sql)) {
            return new collection();
        }

        $exists_sql = implode(" OR ", $exists_sql);

        // Find assignments of all users that were marked as having changes
        $sql = "
            SELECT q.{$user_id_column} as user_id, tca.id as assignment_id
            FROM {{$table_name}} q 
            LEFT JOIN {totara_competency_assignments} tca 
                ON q.{$user_id_column} = tca.user_group_id 
                    AND q.{$competency_id_column} = tca.competency_id
                    AND tca.type = :assignment_type  
                    AND tca.user_group_type = :user_group_type  
            WHERE {$q_cond}
                AND (
                    {$exists_sql}
                )
        ";

        $params = [
            'competency_id' => $competency_id,
            'assignment_type' => assignment::TYPE_LEGACY,
            'user_group_type' => user_groups::USER,
        ];

        $params = array_merge($params, $q_params);

        $assignments = collection::new($DB->get_records_sql($sql, $params));

        // Now for all of the assignments / user combinations query
        // the active achievements. This reduces the number of queries
        // down to only two and avoids the N+1 problem here
        $achievements = competency_achievement::repository()
            ->where('comp_id', $competency_id)
            ->where('user_id', 'in', $assignments->pluck('user_id'))
            ->where('status', competency_achievement::ACTIVE_ASSIGNMENT)
            ->get();

        // Now combine the two results returning, mapping the achievement to the assignment user record
        return $assignments->map(function (stdClass $assignment) use ($achievements) {
            $achievement = $achievements->find('user_id', $assignment->user_id);

            return (object) [
                'user_id' => $assignment->user_id,
                'assignment_id' => $assignment->assignment_id,
                'achievement' => $achievement
            ];
        });
    }

}
