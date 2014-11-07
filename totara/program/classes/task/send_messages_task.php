<?php
/*
 * This file is part of Totara LMS
 *
 * Copyright (C) 2010 onwards Totara Learning Solutions LTD
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
 * @author Ben Lobo <ben.lobo@kineo.com>
 * @author Valerii Kuznetsov <valerii.kuznetsov@totaralms.com>
 * @package totara_program
 */

namespace totara_program\task;

/**
 * Sends any messages that are due to be sent
 */
class send_messages_task extends \core\task\scheduled_task {
    /**
     * Get a descriptive name for this task (shown to admins).
     *
     * @return string
     */
    public function get_name() {
        return get_string('sendmessagestask', 'totara_program');
    }

    /**
     * These functions are all globbed together to avoid instantiating programs
     * over and over unnecessarily
     */
    public function execute() {
        global $CFG;
        require_once($CFG->dirroot . '/totara/program/lib.php');
        require_once($CFG->dirroot . '/backup/util/includes/restore_includes.php');
        require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');

        // Don't run programs cron if programs and certifications are disabled.
        if (totara_feature_disabled('programs') &&
            totara_feature_disabled('certifications')) {
            return false;
        }

        // This will be populated with programs and keyed with the program id as
        // the programs are instantiated. This will save us having to instantiate
        // the same program more than once.
        $programs = array();

        // Send alerts if any programs are due.
        $this->program_cron_programs_due($programs);

        // Send alerts if any course sets are due.
        $this->program_cron_coursesets_due($programs);

        // Send alerts if any programs are overdue.
        $this->program_cron_programs_overdue($programs);

        // Send alerts if any course sets are overdue.
        $this->program_cron_coursesets_overdue($programs);

        // Send alerts if any course sets are overdue.
        $this->program_cron_learner_followups($programs);

        // Send alerts if any programs have outstanding exceptions.
        $this->program_cron_exceptions_raised($programs);
    }

    /**
     * Checks if any program due messages need to be sent and sends them
     *
     * @param array $programs An array of program objects. This is passed by reference so that it can be populated and re-used
     */
    protected function program_cron_programs_due(&$programs) {
        global $DB;

        if (debugging()) {
            mtrace('Checking programs that are due to be completed');
        }

        $now = time();

        // Query to retrieve all users who need to be sent program due messages
        // based on when the program is due to be completed and whether or not
        // there are any program due messages defined by the program with trigger
        // times that match the user's due dates.
        $sql = "SELECT u.*, pc.programid, pc.timedue, pm.id AS messageid, pm.triggertime
                FROM {user} u
                INNER JOIN {prog_completion} pc ON u.id = pc.userid
                INNER JOIN {prog_user_assignment} pua ON (pc.userid = pua.userid AND pc.programid = pua.programid)
                INNER JOIN {prog_message} pm ON pc.programid = pm.programid
                WHERE pc.timecompleted = ?
                AND pc.coursesetid = ?
                AND pm.messagetype = ?
                AND pc.timedue > 0 AND (pc.timedue - pm.triggertime) < ?
                ORDER BY pc.programid, u.id";

        // Get the records.
        $rs = $DB->get_recordset_sql($sql, array(0, 0, MESSAGETYPE_PROGRAM_DUE, $now));

        foreach ($rs as $user) {
            if (isset($programs[$user->programid])) {
                // Use the existing program object if it is available.
                $program = $programs[$user->programid];
            } else {
                // Create a new program object and store it if it has not already been instantiated.
                $program = new \program($user->programid);
                $programs[$user->programid] = $program;
            }

            $messagesmanager = $program->get_messagesmanager();
            $messages = $messagesmanager->get_messages();
            $isviewable = $program->is_viewable($user);

            // Send program due notifications to user and (optionally) the user's manager.
            foreach ($messages as $message) {
                if ($message->id == $user->messageid && $message->messagetype == MESSAGETYPE_PROGRAM_DUE && $isviewable) {
                    $message->send_message($user);
                }
            }
        }
        $rs->close();
    }

    /**
     * Checks if any course set due messages need to be sent and sends them
     *
     * @param array $programs An array of program objects. This is passed by reference so that it can be populated and re-used
     */
    protected function program_cron_coursesets_due(&$programs) {
        global $DB;

        if (debugging()) {
            mtrace('Checking course sets that are due to be completed');
        }

        $now = time();

        // Query to retrieve all users who need to be sent course set due messages
        // based on when the course set is due to be completed and whether or not
        // there are any course set due messages defined by the program with trigger
        // times that match the user's due dates.
        $sql = "SELECT u.*, pc.programid, pc.timedue, pm.id AS messageid, pm.triggertime, pc.coursesetid
                FROM {user} u
                INNER JOIN {prog_completion} pc ON u.id = pc.userid
                INNER JOIN {prog_user_assignment} pua ON (pc.userid = pua.userid AND pc.programid = pua.programid)
                INNER JOIN {prog_message} pm ON pc.programid = pm.programid
                WHERE pc.timecompleted = ?
                AND pc.coursesetid <> ?
                AND pm.messagetype = ?
                AND pc.timedue > 0 AND (pc.timedue - pm.triggertime) < ?
                ORDER BY pc.programid, u.id";

        // Get the records.
        $rs = $DB->get_recordset_sql($sql, array(0, 0, MESSAGETYPE_COURSESET_DUE, $now));

        foreach ($rs as $user) {

            if (isset($programs[$user->programid])) {
                // Use the existing program object if it is available.
                $program = $programs[$user->programid];
            } else {
                // Create a new program object and store it if it has not already been instantiated.
                $program = new \program($user->programid);
                $programs[$user->programid] = $program;
            }

            $messagesmanager = $program->get_messagesmanager();
            $messages = $messagesmanager->get_messages();
            $isviewable = $program->is_viewable($user);

            // Send course set due notifications to user and (optionally) the user's manager.
            foreach ($messages as $message) {
                if ($message->id == $user->messageid && $message->messagetype == MESSAGETYPE_COURSESET_DUE && $isviewable) {
                    $message->send_message($user, null, array('coursesetid' => $user->coursesetid));
                }
            }
        }
        $rs->close();
    }

    /**
     * Checks if any program overdue messages need to be sent and sends them
     *
     * @global object $CFG
     * @param array $programs An array of program objects. This is passed by reference so that it can be populated and re-used
     */
    protected function program_cron_programs_overdue(&$programs) {
        global $DB;

        if (debugging()) {
            mtrace('Checking programs that are overdue');
        }

        $now = time();

        // Query to retrieve all users who need to be sent overdue messages
        // based on their program due dates and the trigger dates in any program
        // overdue messages that are defined by the program.
        $sql = "SELECT u.*, pc.programid, pc.timedue, pm.id AS messageid, pm.triggertime
                FROM {user} u
                INNER JOIN {prog_completion} pc ON u.id = pc.userid
                INNER JOIN {prog_user_assignment} pua ON (pc.userid = pua.userid AND pc.programid = pua.programid)
                INNER JOIN {prog_message} pm ON pc.programid = pm.programid
                WHERE pc.timecompleted = ?
                AND pc.coursesetid = ?
                AND pm.messagetype = ?
                AND pc.timedue > 0 AND (pc.timedue + pm.triggertime) < ?
                ORDER BY pc.programid, u.id";

        // Get the records.
        $rs = $DB->get_recordset_sql($sql, array(0, 0, MESSAGETYPE_PROGRAM_OVERDUE, $now));

        foreach ($rs as $user) {

            if (isset($programs[$user->programid])) {
                // Use the existing program object if it is available.
                $program = $programs[$user->programid];
            } else {
                // Create a new program object and store it if it has not already been instantiated.
                $program = new \program($user->programid);
                $programs[$user->programid] = $program;
            }

            $messagesmanager = $program->get_messagesmanager();
            $messages = $messagesmanager->get_messages();
            $isviewable = $program->is_viewable($user);

            // Send program overdue notifications to user and (optionally) the user's manager.
            foreach ($messages as $message) {
                if ($message->id == $user->messageid && $message->messagetype == MESSAGETYPE_PROGRAM_OVERDUE && $isviewable) {
                    $message->send_message($user);
                }
            }
        }
        $rs->close();
    }

    /**
     * Checks if any course set overdue messages need to be sent and sends them
     *
     * @param array $programs An array of program objects. This is passed by reference so that it can be populated and re-used
     */
    protected function program_cron_coursesets_overdue(&$programs) {
        global $DB;

        if (debugging()) {
            mtrace('Checking course sets that are overdue');
        }

        $now = time();

        // Query to retrieve all users who need to be sent overdue messages
        // based on their course set due dates and the trigger dates in any course set
        // overdue messages that are defined by the program.
        $sql = "SELECT u.*, pc.programid, pc.timedue, pm.id AS messageid, pm.triggertime, pc.coursesetid
                FROM {user} u
                INNER JOIN {prog_completion} pc ON u.id = pc.userid
                INNER JOIN {prog_user_assignment} pua ON (pc.userid = pua.userid AND pc.programid = pua.programid)
                INNER JOIN {prog_message} pm ON pc.programid = pm.programid
                WHERE pc.timecompleted = ?
                AND pc.coursesetid <> ?
                AND pm.messagetype = ?
                AND pc.timedue > 0 AND (pc.timedue + pm.triggertime) < ?
                ORDER BY pc.programid, u.id";

        // Get the records.
        $rs = $DB->get_recordset_sql($sql, array(0, 0, MESSAGETYPE_COURSESET_OVERDUE, $now));

        foreach ($rs as $user) {

            if (isset($programs[$user->programid])) {
                // Use the existing program object if it is available.
                $program = $programs[$user->programid];
            } else {
                // Create a new program object and store it if it has not already been instantiated.
                $program = new \program($user->programid);
                $programs[$user->programid] = $program;
            }

            $messagesmanager = $program->get_messagesmanager();
            $messages = $messagesmanager->get_messages();
            $isviewable = $program->is_viewable($user);

            // Send course set overdue notifications to user and (optionally) the user's manager.
            foreach ($messages as $message) {
                if ($message->id == $user->messageid && $message->messagetype == MESSAGETYPE_COURSESET_OVERDUE && $isviewable) {
                    $message->send_message($user, null, array('coursesetid' => $user->coursesetid));
                }
            }
        }
        $rs->close();
    }

    /**
     * Checks if any course set overdue messages need to be sent and sends them
     *
     * @param array $programs An array of program objects. This is passed by reference so that it can be populated and re-used
     */
    protected function program_cron_learner_followups(&$programs) {
        global $DB;

        if (debugging()) {
            mtrace('Checking for any follow-up messages to be sent');
        }

        $now = time();

        // Query to retrieve all users who need to be sent follow-up messages
        // based on their course completion dates and the trigger dates in any
        // follow-up messages that are defined by the program.
        $sql = "SELECT u.id, pc.programid, pc.timecompleted, pm.id AS messageid, pm.triggertime
                FROM {user} u
                INNER JOIN {prog_completion} pc ON u.id = pc.userid
                INNER JOIN {prog_user_assignment} pua ON (pc.userid = pua.userid AND pc.programid = pua.programid)
                INNER JOIN {prog_message} pm ON pc.programid = pm.programid
                WHERE pc.status = ?
                AND pm.messagetype = ?
                AND (pc.timecompleted + pm.triggertime) < ?
                ORDER BY pc.programid, u.id";

        // Get the records.
        $rs = $DB->get_recordset_sql($sql, array(STATUS_PROGRAM_COMPLETE, MESSAGETYPE_LEARNER_FOLLOWUP, $now));

        foreach ($rs as $user) {

            if (isset($programs[$user->programid])) {
                // Use the existing program object if it is available.
                $program = $programs[$user->programid];
            } else {
                // Create a new program object and store it if it has not already been instantiated.
                $program = new \program($user->programid);
                $programs[$user->programid] = $program;
            }

            $messagesmanager = $program->get_messagesmanager();
            $messages = $messagesmanager->get_messages();
            $isviewable = $program->is_viewable($user);

            // Send course set overdue notifications to user and (optionally) the user's manager.
            foreach ($messages as $message) {
                if ($message->id == $user->messageid && $message->messagetype == MESSAGETYPE_LEARNER_FOLLOWUP && $isviewable) {
                    $message->send_message($user);
                }
            }
        }
        $rs->close();
    }

    /**
     * Checks if any unhandled exceptions exist in any programs and send an alert to the admin
     *
     * @param array $programs An array of program objects. This is passed by reference so that it can be populated and re-used
     */
    protected function program_cron_exceptions_raised(&$programs) {
        global $DB;

        if (debugging()) {
            mtrace('Checking if any exceptions exist');
        }

        if (!$admin = get_admin()) {
            mtrace('Unable to determine admin user in program_cron_exceptions_raised. Not checking for exceptions.');
            return;
        }

        // Query to retrieve any programs that have unhandled exceptions.
        $sql = "SELECT DISTINCT(p.id) AS id
                FROM {prog} p
                JOIN {prog_exception} pe
                   ON p.id = pe.programid
                WHERE p.exceptionssent = ?";

        $progsfound = $DB->get_records_sql($sql, array(0));

        foreach ($progsfound as $progfound) {

            if (isset($programs[$progfound->id])) {
                // Use the existing program object if it is available.
                $program = $programs[$progfound->id];
            } else {
                // Create a new program object and store it if it has not already been instantiated.
                $program = new \program($progfound->id);
                $programs[$progfound->id] = $program;
            }

            $messagesmanager = $program->get_messagesmanager();
            $messages = $messagesmanager->get_messages();

            // Send alerts for each program to the admin user.

            foreach ($messages as $message) {
                if ($message->messagetype == MESSAGETYPE_EXCEPTION_REPORT) {

                    // Update program with exceptions sent.
                    $prog_notify_todb = new \stdClass;
                    $prog_notify_todb->id = $message->programid;
                    $prog_notify_todb->exceptionssent = 1;
                    $DB->update_record('prog', $prog_notify_todb);

                    $message->send_message($admin);
                }
            }
        }
    }
}

