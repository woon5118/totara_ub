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

namespace totara_competency\models;

use totara_competency\entities\assignment;
use totara_competency\entities\competency_assignment_user_log;

class assignment_user_log {
    /**
     * @var string
     */
    protected $assignment_type;

    /**
     * @var int
     */
    private $assignment_id;

    /**
     * @var int
     */
    private $user_id;

    /**
     * @var int
     */
    private $competency_id;

    public function __construct(int $assignment_id, int $user_id, ?int $competency_id = null, ?string $assignment_type = null) {
        $this->assignment_id = $assignment_id;
        $this->user_id = $user_id;
        $this->competency_id = $competency_id;
        $this->assignment_type = $assignment_type;
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
        $this->log_tracking_start();
    }

    /**
     * Log the given action for given user
     *
     */
    public function log_unassign_user_group() {
        $this->log(competency_assignment_user_log::ACTION_UNASSIGNED_USER_GROUP);
        $this->log_tracking_end();
    }

    /**
     * Log the given action for given user
     *
     */
    public function log_archive() {
        $this->log(competency_assignment_user_log::ACTION_UNASSIGNED_ARCHIVED);
        $this->log_tracking_end();
    }

    /**
     * Log tracking start if the given assignment is the first active one
     */
    public function log_tracking_start() {
        // We ignore the continuous tracking assignment
        if (!is_null($this->competency_id) && $this->assignment_type !== assignment::TYPE_SYSTEM) {
            $assignment_user = new assignment_user($this->user_id);
            $assignments = $assignment_user->get_active_assignments_for_competency($this->competency_id);
            // If the current assignment is the only one, tracking just got started
            if ($assignments->count() === 1 && $assignments->first()->id == $this->assignment_id) {
                $this->log(competency_assignment_user_log::ACTION_TRACKING_START);
            }
        }
    }

    /**
     * Log tracking end if there are no active assignments left
     */
    public function log_tracking_end() {
        if (!is_null($this->competency_id)) {
            $assignment_user = new assignment_user($this->user_id);
            if (!$assignment_user->has_active_assignments($this->competency_id)) {
                $this->log(competency_assignment_user_log::ACTION_TRACKING_END);
            }
        }
    }

}