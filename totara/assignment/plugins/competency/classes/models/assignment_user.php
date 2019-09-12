<?php
/*
 * This file is part of Totara Learn
 *
 * Copyright (C) 2018 onwards Totara Learning Solutions LTD
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
 * @author Fabian Derschatta <fabian.derschatta@totaralearning.com>
 * @package tassign_competency
 */

namespace tassign_competency\models;

use core\orm\entity\repository;
use core\orm\collection;
use totara_competency\entities\assignment;
use totara_competency\entities\competency_assignment_user;
use tassign_competency\expand_task;
use totara_assignment\entities\user;
use totara_assignment\user_groups;

class assignment_user {

    /**
     * @var int
     */
    private $user_id;

    /**
     * @param int $user_id
     */
    public function __construct(int $user_id) {
        $this->user_id = $user_id;
    }

    /**
     * Get user id
     *
     * @return int
     */
    public function get_id() {
        return $this->user_id;
    }

    /**
     * Creates a system assignment for a user for a competency in case no assignment is left
     * and the user is not deleted
     *
     * @param int $competency_id
     */
    public function create_system_assignment(int $competency_id) {
        global $DB;

        // If the user does not have any active assignments for this competency anymore
        if (!$this->has_active_assignments($competency_id)) {
            // Only for non deleted users we create a new assignment
            $user = new user($this->user_id);
            if (!$user->deleted) {
                // Create special system assignment
                $assignment = new assignment();
                $assignment->type = assignment::TYPE_SYSTEM;
                $assignment->user_group_type = user_groups::USER;
                $assignment->user_group_id = $this->user_id;
                $assignment->competency_id = $competency_id;
                $assignment->status = assignment::STATUS_ACTIVE;
                $assignment->created_by = 0;
                $assignment->save();

                // It should be safe here to expand the assignment directly
                // without an adhoc task as it's only one assignment for one user.
                (new expand_task($DB))->expand_single($assignment->id);
            }
        }
    }

    /**
     * Check if the user has any active assignments for a competency
     *
     * @param int|null $competency_id
     * @return bool
     */
    public function has_active_assignments(int $competency_id): bool {
        return count($this->get_active_assignments_for_competency($competency_id)) > 0;
    }

    public function has_archived_assignments(int $competency_id): bool {
        return count($this->fetch_archived_assignments($competency_id)) > 0;
    }

    /**
     * Returns active assignments for a single competency
     *
     * @param int $competency_id
     * @return collection
     */
    public function get_active_assignments_for_competency(int $competency_id): collection {
        return $this->fetch_active_assignments($competency_id);
    }

    /**
     * Returns all active assignments for multiple competencies
     *
     * @param array $competency_ids
     * @return collection
     */
    public function get_active_assignments_for_competencies(array $competency_ids): collection {
        return $this->fetch_active_assignments($competency_ids);
    }

    /**
     * Returns activate assignments for the user
     *
     * @param null|array|int $competency_ids
     * @return collection
     */
    private function fetch_active_assignments($competency_ids = null): collection {
        return assignment::repository()
            ->join('totara_assignment_competency_users', 'id', 'assignment_id')
            ->where('totara_assignment_competency_users.user_id', $this->user_id)
            ->where('status', assignment::STATUS_ACTIVE)
            ->when(!is_null($competency_ids), function (repository $repository) use ($competency_ids) {
                $repository->where('competency_id', $competency_ids);
            })
            ->get();
    }

    /**
     * Returns activate assignments for the user
     *
     * @param null|array|int $competency_ids
     * @return collection
     */
    private function fetch_archived_assignments($competency_ids = null): collection {
        return assignment::repository()
            ->join('totara_assignment_competencies_users_log', 'id', 'assignment_id')
            ->where('totara_assignment_competencies_users_log.user_id', $this->user_id)
            ->where('status', assignment::STATUS_ARCHIVED)
            ->when(!is_null($competency_ids), function (repository $repository) use ($competency_ids) {
                $repository->where('competency_id', $competency_ids);
            })
            ->get();
    }

    /**
     * delete one or all assignment entries for given user
     *
     * @param int|null $assignment_id if null all entries for user are deleted
     */
    public function delete(?int $assignment_id = null) {
        $repo = competency_assignment_user::repository()
            ->where('user_id', $this->user_id);
        if ($assignment_id) {
            $repo->where('assignment_id', $assignment_id);
        }
        $repo->delete();
    }

}