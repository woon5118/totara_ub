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
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Yuliya Bozhko <yuliya.bozhko@totaralearning.com>
 * @package totara_program
 */

namespace totara_program\exception;

abstract class base {

    public $id;
    public $programid;
    public $exceptiontype;
    public $userid;
    public $timeraised;
    public $assignmentid;

    public function __construct(int $programid, $exceptionob = null) {
        if (is_object($exceptionob)) {
            $this->id = $exceptionob->id;
            $this->programid = $exceptionob->programid;
            $this->exceptiontype = $exceptionob->exceptiontype;
            $this->userid = $exceptionob->userid;
            $this->timeraised = $exceptionob->timeraised;
            $this->assignmentid = $exceptionob->assignmentid;
        } else {
            $this->id = 0;
            $this->programid = $programid;
            $this->exceptiontype = 0;
            $this->userid = 0;
            $this->timeraised = time();
            $this->assignmentid = 0;
        }
    }

    /**
     * Add exception record to the database.
     *
     * @param int $programid
     * @param int $exceptiontype
     * @param int $userid
     * @param int $assignmentid
     * @param int $timeraised
     *
     * @return bool|int
     */
    public static function insert_exception(int $programid, int $exceptiontype, int $userid, int $assignmentid, int $timeraised = null) {
        global $DB;

        if (!$timeraised) {
            $timeraised = time();
        }

        $exception = new \stdClass();
        $exception->programid = $programid;
        $exception->exceptiontype = $exceptiontype;
        $exception->userid = $userid;
        $exception->timeraised = $timeraised;
        $exception->assignmentid = $assignmentid;

        if ($exceptionid = $DB->insert_record('prog_exception', $exception)) {
            $prog_notify_todb = new \stdClass();
            $prog_notify_todb->id = $programid;
            $prog_notify_todb->exceptionssent = 0;
            $DB->update_record('prog', $prog_notify_todb);

            return $exceptionid;
        } else {
            return false;
        }
    }

    /**
     *  Checks if an exception exists
     *
     * @param int $programid
     * @param int $exceptiontype
     * @param int $userid
     *
     * @return bool True if exception exists
     */
    public static function exception_exists(int $programid, int $exceptiontype, int $userid): bool {
        global $DB;

        return $DB->record_exists('prog_exception', ['programid' => $programid, 'exceptiontype' => $exceptiontype, 'userid' => $userid]);
    }

    /**
     *  Deletes and exception given an ID
     *
     * @param int $exceptionid
     *
     * @return bool Success status
     */
    public static function delete_exception(int $exceptionid): bool {
        global $DB;

        return $DB->delete_records('prog_exception', ['id' => $exceptionid]);
    }

    public function handles(int $action): bool {
        return $action == manager::SELECTIONACTION_DISMISS_EXCEPTION;
    }

    public function handle(int $action = null) {
        if (!$this->handles($action)) {
            return true;
        }

        switch ($action) {
            case manager::SELECTIONACTION_DISMISS_EXCEPTION:
                return $this->dismiss_exception();
                break;
            default:
                return true;
                break;
        }
    }

    /**
     * Override exception and add an assignment to the program.
     *
     * @return bool Success status
     */
    protected function override_and_add_program(): bool {
        global $DB;

        $assignid = $DB->get_field('prog_user_assignment', 'id', ['assignmentid' => $this->assignmentid, 'userid' => $this->userid]);

        if (!empty($assignid)) {
            $learner_assign_todb = new \stdClass();
            $learner_assign_todb->id = $assignid;
            $learner_assign_todb->timeassigned = time();
            $learner_assign_todb->exceptionstatus = PROGRAM_EXCEPTION_RESOLVED;

            if (!$DB->update_record('prog_user_assignment', $learner_assign_todb)) {
                return false;
            }

            // Record the change in the program completion log.
            prog_log_completion(
                $this->programid,
                $this->userid,
                'Assignment exception overridden and user assigned despite problem'
            );

            // Event trigger to send notification when exception is resolved.
            $event = \totara_program\event\program_assigned::create(
                [
                    'objectid' => $this->programid,
                    'context'  => \context_program::instance($this->programid),
                    'userid'   => $this->userid,
                ]
            );
            $event->trigger();
        }

        return self::delete_exception($this->id);
    }

    /**
     * Work out a viable due date and then proceed with the assignment
     * @return boolean success
     */
    protected function set_auto_time_allowance() {
        global $CFG, $DB;

        require_once($CFG->dirroot . '/totara/program/program.class.php');

        $program = new \program($this->programid);

        $assignment_record = $DB->get_record('prog_assignment', ['id' => $this->assignmentid]);
        if (!$assignment_record) {
            return false;
        }

        // Get the total time allowed for the content in the program.
        require_once($CFG->dirroot . '/totara/certification/lib.php');
        $certifpath = get_certification_path_user($program->certifid, $this->userid);
        $total_time_allowed = $program->content->get_total_time_allowance($certifpath);

        // Give the user this much time plus one week.
        $timedue = time() + $total_time_allowed + 604800;

        // Update prog_completion.
        if (!$program->set_timedue($this->userid, $timedue, 'Due date updated while automatically resolving time allowance exception')) {
            return false;
        }

        // Update user_assignment.
        $assignid = $DB->get_field('prog_user_assignment', 'id', ['assignmentid' => $this->assignmentid, 'userid' => $this->userid]);

        if (!empty($assignid)) {
            $learner_assign_todb = new \stdClass();
            $learner_assign_todb->id = $assignid;
            $learner_assign_todb->timeassigned = time();
            $learner_assign_todb->exceptionstatus = PROGRAM_EXCEPTION_RESOLVED;

            $DB->update_record('prog_user_assignment', $learner_assign_todb);

            // Record the change in the program completion log.
            prog_log_completion(
                $this->programid,
                $this->userid,
                'User assigned and due date exception automatically resolved'
            );

            $event = \totara_program\event\program_assigned::create(
                [
                    'objectid' => $this->programid,
                    'context'  => \context_program::instance($this->programid),
                    'userid'   => $this->userid,
                ]
            );
            $event->trigger();
        }

        return self::delete_exception($this->id);
    }

    /**
     * Dismiss and ignore this exception
     *
     * @return boolean success
     */
    private function dismiss_exception(): bool {
        global $CFG, $DB;
        require_once($CFG->dirroot . '/totara/program/program.class.php');

        // Update user_assignment.
        $assignid = $DB->get_field('prog_user_assignment', 'id', ['assignmentid' => $this->assignmentid, 'userid' => $this->userid]);

        if (!empty($assignid)) {
            $learner_assign_todb = new \stdClass();
            $learner_assign_todb->id = $assignid;
            $learner_assign_todb->exceptionstatus = PROGRAM_EXCEPTION_DISMISSED;
            if (!$DB->update_record('prog_user_assignment', $learner_assign_todb)) {
                return false;
            }
        }

        // Record the change in the program completion log.
        prog_log_completion(
            $this->programid,
            $this->userid,
            'Assignment exception dismissed without assigning user'
        );

        return self::delete_exception($this->id);
    }
}
