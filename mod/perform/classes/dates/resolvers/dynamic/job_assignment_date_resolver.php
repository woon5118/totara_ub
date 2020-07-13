<?php
/*
 * This file is part of Totara Learn
 *
 * Copyright (C) 2020 onwards Totara Learning Solutions LTD
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
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Samantha Jayasinghe <samantha.jayasinghe@totaralearning.com>
 * @package mod_perform
 */

namespace mod_perform\dates\resolvers\dynamic;

use mod_perform\dates\resolvers\date_resolver;

interface job_assignment_date_resolver extends date_resolver {

    /**
     * @param array $reference_job_assignment_ids
     * @return dynamic_date_resolver
     */
    public function set_job_assignments(
        array $reference_job_assignment_ids): dynamic_date_resolver;

    /**
     * Get the start date for a user's job assignment.
     *
     * @param int $user_id
     * @param int|null $job_assignment_id
     * @return int
     */
    public function get_start_for_job_assignment(int $user_id, ?int $job_assignment_id): ?int;

    /**
     * Get the end date for a user's job assignment.
     *
     * @param int $user_id
     * @param int|null $job_assignment_id
     * @return int
     */
    public function get_end_for_job_assignment(int $user_id, ?int $job_assignment_id): ?int;

}
