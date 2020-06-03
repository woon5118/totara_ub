<?php
/**
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
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Jaron Steenson <jaron.steenson@totaralearning.com>
 * @package mod_perform
 */

namespace mod_perform\task\expand_task;

use core\collection;
use Iterator;
use mod_perform\entities\activity\track_user_assignment;

/**
 * Represent expand task assignment parameters.
 */
class assignment_parameters_collection extends collection {

    /**
     * Create and fill a new instance from user_ids.
     *
     * @param array|Iterator $user_ids
     * @return static
     */
    public static function create_from_user_ids($user_ids): self {
        $collection = new static();

        foreach ($user_ids as $user_id) {
            $collection->append(assignment_parameters::create_from_user_id($user_id));
        }

        return $collection;
    }

    /**
     * Create and fill a new instance from job_assignment rows (stdClass).
     *
     * @param array|Iterator $job_assignments
     * @return static
     */
    public static function create_from_job_assignments($job_assignments): self {
        $collection = new static();

        foreach ($job_assignments as $job_assignment) {
            $collection->append(assignment_parameters::create_from_job_assignment($job_assignment));
        }

        return $collection;
    }

    /**
     * Return a copy with  all entries that match the supplied track user assignments removed.
     *
     * @param collection|track_user_assignment[] $user_assignments
     * @return assignment_parameters_collection|assignment_parameters[]
     */
    public function remove_matching_user_assignments(collection $user_assignments): assignment_parameters_collection {
        return $this->filter(
            function (assignment_parameters $assignment_parameters) use ($user_assignments) {
                $found = $user_assignments->find(
                    function (track_user_assignment $track_user_assignment) use ($assignment_parameters) {
                        return $assignment_parameters->matches_track_user_assignment($track_user_assignment);
                    }
                );

                return !$found;
            }
        );
    }

    /**
     * Get an array of all user ids in the assignment parameters.
     *
     * @return int[]
     */
    public function pluck_user_ids(): array {
        return $this->map(function (assignment_parameters $parameters) {
            return $parameters->get_user_id();
        })->all();
    }

    /**
     * Try find a set of assignment parameters based on track user assignment.
     *
     * @param track_user_assignment $track_user_assignment
     * @return assignment_parameters|null
     */
    public function find_from_track_user_assignment(track_user_assignment $track_user_assignment): ?assignment_parameters {
        return $this->find(function (assignment_parameters $assignment_parameters) use ($track_user_assignment) {
            return $assignment_parameters->matches_track_user_assignment($track_user_assignment);
        });
    }

}