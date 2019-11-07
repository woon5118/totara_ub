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

namespace tassign_competency\observers;

use tassign_competency\event\assignment_user;
use tassign_competency\event\assignment_user_archived;
use tassign_competency\event\assignment_user_assigned;
use tassign_competency\event\assignment_user_unassigned;
use totara_competency\models;

defined('MOODLE_INTERNAL') || die();

/**
 * Event observer
 */
class user_log {

    /**
     * Triggered via assignment_user_archived event.
     *
     * @param assignment_user $event
     * @return bool true on success
     */
    public static function log(assignment_user $event) {
        $data = $event->get_data();
        $assignment_id = $data['other']['assignment_id'] ?? null;
        $competency_id = $data['other']['competency_id'] ?? null;
        $assignment_type = $data['other']['type'] ?? null;
        $user_id = $data['relateduserid'];

        if ($assignment_id && $user_id) {
            $log = new models\assignment_user_log($assignment_id, $user_id, $competency_id, $assignment_type);
            switch (get_class($event)) {
                case assignment_user_archived::class:
                    // Log this action
                    $log->log_archive();
                    break;
                case assignment_user_assigned::class:
                    // Log this action
                    $log->log_assign();
                    break;
                case assignment_user_unassigned::class:
                    // Log this action
                    $log->log_unassign_user_group();
                    break;
            }
        }

        return true;
    }

}
