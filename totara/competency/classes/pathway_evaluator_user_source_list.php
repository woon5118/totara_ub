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

use totara_competency\entities\pathway_achievement;

class pathway_evaluator_user_source_list implements pathway_evaluator_user_source {

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
     * Return the source type
     *@return string
     */
    public function get_source_type(): string {
        return 'list';
    }

    /**
     * Return the user_id_source
     * @return mixed Source used for obtaining currently assigned users
     */
    public function get_source() {
        return $this->user_id_list;
    }

    /**
     * Does the source hold a full set of user ids?
     * @return bool
     */
    public function is_full_user_set(): bool {
        return $this->full_user_set;
    }

    /**
     * Archive patwhays achievement records of users no longer assigned
     * @param pathway $pathway
     * @param int $aggregation_time
     */
    public function archive_non_assigned_achievements(pathway $pathway, int $aggregation_time) {
        global $DB;

        if (empty($this->full_user_set)) {
            return;
        }

        if (!empty($this->user_id_list)) {
            [$user_id_sql, $user_id_params] = $DB->get_in_or_equal($this->user_id_list, SQL_PARAMS_NAMED, 'param', false);
            if (!empty($user_id_sql)) {
                $user_id_sql = " AND user_id {$user_id_sql}";
            }
        } else {
            $user_id_sql = '';
            $user_id_params = [];
        }

        $sql =
            "UPDATE {totara_competency_pathway_achievement}
                SET status = :archived,
                    last_aggregated = :aggregationtime
              WHERE pathway_id = :pathwayid
                AND status = :currentstatus
                    {$user_id_sql}";

        $params = array_merge(
            [
                'pathwayid' => $pathway->get_id(),
                'archived' => pathway_achievement::STATUS_ARCHIVED,
                'aggregationtime' => $aggregation_time,
                'currentstatus' => pathway_achievement::STATUS_CURRENT,
            ],
            $user_id_params
        );

        $DB->execute($sql, $params);
    }

    /**
     * Mark users who don't yet have a pathway_achievement record as having changed
     * @param pathway $pathway
     */
    public function mark_newly_assigned_users(pathway $pathway) {
        // List is not using has_changed marking
    }

    /**
     * Mark users who needs to be reaggregated
     * @param pathway $pathway
     */
    public function mark_users_to_reaggregate(pathway $pathway) {
        // List is not using has_changed marking
    }

    /**
     * Set the operation key to distinguish between different pathways
     * @param $update_operation_value
     */
    public function set_update_operation_value($update_operation_value) {
        // Update operation value not use by lists
    }

    /**
     * Get users to reaggregate
     * @param pathway $pathway
     * @param int $aggregation_time
     */
    public function get_users_to_reaggregate(pathway $pathway, int $aggregation_time): \moodle_recordset {
        global $DB;

        if (empty($this->user_id_list)) {
            return [];
        }

        [$user_sql, $user_params] = $DB->get_in_or_equal($this->user_id_list, SQL_PARAMS_NAMED);
        $sql =
            "SELECT usr.id as user_id, 
                    tcpa.id as achievement_id,
                    tcpa.scale_value_id
               FROM {user} usr
          LEFT JOIN {totara_competency_pathway_achievement} tcpa
                 ON tcpa.pathway_id = :pathwayid
                AND tcpa.user_id = usr.id
                AND tcpa.status = :currentstatus
              WHERE usr.id {$user_sql}";

        $params = array_merge(
            $user_params,
            [
                'pathwayid' => $pathway->get_id(),
                'currentstatus' => pathway_achievement::STATUS_CURRENT,
            ]);

        return $DB->get_recordset_sql($sql, $params);
    }

}
