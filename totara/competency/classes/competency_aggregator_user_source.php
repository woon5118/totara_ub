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

interface competency_aggregator_user_source {

    /**
     * Archive achievements of users no longer assigned
     * @param int $competency_id
     * @param int $aggregation_time
     */
    public function archive_non_assigned_achievements(int $competency_id, int $aggregation_time);

    /**
     * Get users to consider for reaggregation
     * @param int $competency_id
     * @param int $aggregation_time
     */
    public function get_users_to_reaggregate(int $competency_id, int $aggregation_time): \moodle_recordset;

}
