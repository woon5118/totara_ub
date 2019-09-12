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


interface pathway_evaluator_user_source {

    /**
     * Return the source type
     *@return string
     */
    public function get_source_type(): string;

    /**
     * Return the user_id_source
     * @return mixed Source used for obtaining currently assigned users
     */
    public function get_source();

    /**
     * Does the source hold a full set of user ids?
     * @return bool
     */
    public function is_full_user_set(): bool;

    /**
     * Archive pathways achievement records of users no longer assigned
     * @param pathway $pathway
     * @param int $aggregation_time
     */
    public function archive_non_assigned_achievements(pathway $pathway, int $aggregation_time);

    /**
     * Mark users who don't yet have a pathway_achievement record as having changed
     * @param pathway $pathway
     */
    public function mark_newly_assigned_users(pathway $pathway);

    /**
     * Mark users who needs to be reaggregated
     * @param pathway $pathway
     */
    public function mark_users_to_reaggregate(pathway $pathway);

    /**
     * Set the operation key to distinguish between different pathways
     * @param $update_operation_value
     */
    public function set_update_operation_value($update_operation_value);

    /**
     * Get users to consider for reaggregation
     * @param pathway $pathway
     * @param int $aggregation_time
     */
    public function get_users_to_reaggregate(pathway $pathway, int $aggregation_time): \moodle_recordset;

}
