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

use tassign_competency\entities\competency_assignment_user_log;

class assignment_user_log {

    /**
     * @var int
     */
    private $assignment_id;

    /**
     * @var int
     */
    private $user_id;

    public function __construct(int $assignment_id, int $user_id) {
        $this->assignment_id = $assignment_id;
        $this->user_id = $user_id;
    }

    /**
     * Log action on an assignment
     *
     * @param int $action
     * @return void
     */
    public function log(int $action) {
        $user_log = new competency_assignment_user_log();
        $user_log->assignment_id = $this->assignment_id;
        $user_log->user_id = $this->user_id;
        $user_log->action = $action;
        $user_log->save();
    }

    /**
     * Log the given action for given user
     *
     */
    public function log_assign() {
        $this->log(competency_assignment_user_log::ACTION_ASSIGNED);
    }

    /**
     * Log the given action for given user
     *
     */
    public function log_unassign_user_group() {
        $this->log(competency_assignment_user_log::ACTION_UNASSIGNED_USER_GROUP);
    }

    /**
     * Log the given action for given user
     *
     */
    public function log_archive() {
        $this->log(competency_assignment_user_log::ACTION_UNASSIGNED_ARCHIVED);
    }

}