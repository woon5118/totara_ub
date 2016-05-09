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
 * @author Petr Skoda <petr.skoda@totaralms.com>
 * @package mod_facetoface
 */

namespace mod_facetoface\task;

/**
 * Send facetoface notifications
 */
class cleanup_task extends \core\task\scheduled_task {

    /**
     * Get a descriptive name for this task (shown to admins).
     *
     * @return string
     */
    public function get_name() {
        return get_string('cleanuptask', 'mod_facetoface');
    }

    /**
     * Periodic cron cleanup.
     */
    public function execute() {
        global $DB, $CFG;
        require_once($CFG->dirroot.'/mod/facetoface/lib.php');

        $conditions = array('component' => 'mod_facetoface', 'classname' => '\mod_facetoface\task\cleanup_task');
        $lastcron = $DB->get_field('task_scheduled', 'lastruntime', $conditions);

        // Cancel sessions of all suspended or deleted users,
        // who are not already cancelled.
        // this solves skipped events, direct db edits and upgrades.

        $sql = "SELECT u.id, u.suspended, u.deleted, fs.sessionid, fss.statuscode
                  FROM {user} u
                  JOIN {facetoface_signups} fs ON fs.userid = u.id
                  JOIN {facetoface_signups_status} fss ON fss.signupid = fs.id
                 WHERE (u.deleted <> 0 OR u.suspended <> 0)
                   AND u.timemodified >= :lastcron
                   AND fss.superceded = 0
                   AND fss.statuscode <> :usercancelled
                   AND fss.statuscode <> :sessioncancelled";
        $params = array(
            'lastcron'      => $lastcron,
            'usercancelled' => MDL_F2F_STATUS_USER_CANCELLED,
            'sessioncancelled' => MDL_F2F_STATUS_SESSION_CANCELLED
        );

        $rs = $DB->get_recordset_sql($sql, $params);
        $timenow = time();

        foreach ($rs as $user) {
            $session = facetoface_get_session($user->sessionid);
            $error = null; // Passed by reference.
            $safetocancel = true;
            if ($user->deleted) {
                $reason = get_string('userdeletedcancel', 'facetoface');
            } else {
                $reason = get_string('usersuspendedcancel', 'facetoface');
                // Check if it is safe to cancel the user.
                if (!empty($session->sessiondates) && facetoface_has_session_started($session, $timenow) && facetoface_is_session_in_progress($session, $timenow)) {
                    // Session in progress.
                    $safetocancel = false;
                } else if (!empty($session->sessiondates) && facetoface_has_session_started($session, $timenow)) {
                    // Session is over, don't remove user's records.
                    $safetocancel = false;
                } else if (facetoface_is_user_on_waitlist($session, $user->id)) {
                    // Session is wait-listed.
                    $safetocancel = true;
                } else {
                    // Booking open.
                    $safetocancel = true;
                }
            }
            if ($safetocancel) {
                facetoface_user_cancel($session, $user->id, false, $error, $reason);
            }
        }
        $rs->close();
        $this->remove_unused_custom_rooms();
    }

    /**
     * Clean custom rooms that are no longer used (therefore not available to be choosen).
     */
    protected function remove_unused_custom_rooms() {
        global $DB;
        $lifetime = time() - 86400; // Allow one day for unassigned room as it can be just created and not stored in f2f session yet.
        // Get all custom rooms that are not assigned to any date.
        $sql = "SELECT fr.id
                FROM {facetoface_room} fr
                LEFT JOIN {facetoface_sessions_dates} fsd ON (fsd.roomid = fr.id)
                WHERE fsd.id IS NULL
                  AND fr.custom > 0
                  AND fr.timecreated < {$lifetime}
                ";
        // Do in transaction to avoid assigning during room removal.
        $transaction = $DB->start_delegated_transaction();
        $roomids = $DB->get_fieldset_sql($sql);
        if ($roomids) {
            list($delsql, $delparams) = $DB->get_in_or_equal($roomids, SQL_PARAMS_NAMED);
            $DB->delete_records_select('facetoface_room', "id $delsql", $delparams);
        }
        $transaction->allow_commit();
    }
}
