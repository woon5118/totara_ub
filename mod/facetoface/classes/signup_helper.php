<?php
/*
 * This file is part of Totara LMS
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
 * @author  Valerii Kuznetsov <valerii.kuznetsov@totaralearning.com>
 * @package mod_facetoface
 */

namespace mod_facetoface;

use \stdClass;
use mod_facetoface\signup_status;
use mod_facetoface\exception\signup_exception;
use mod_facetoface\signup\state\{
    state,
    not_set,
    booked,
    requested,
    requestedrole,
    waitlisted,
    user_cancelled,
    attendance_state
};
use totara_job\job_assignment;

defined('MOODLE_INTERNAL') || die();

/**
 * Manage signups
 * Create new signup with all required parameters
 */
final class signup_helper {
    /**
     * Attempt to perform signup process.
     * Check that user can sign up must be done separately.
     * @param signup $signup
     * @return signup
     */
    public static function signup(signup $signup) : signup {
        global $DB;

        // User cannot signup - no effect.
        if (!self::can_signup($signup)) {
            throw new signup_exception("Cannot signup.");
        }

        $trans = $DB->start_delegated_transaction();
        $signup->save();


        $signup->switch_state(booked::class, waitlisted::class, requested::class, requestedrole::class);

        static::trigger_event($signup);
        static::set_default_job_assignment($signup);
        static::withdraw_interest($signup);

        $trans->allow_commit();

        return $signup;
    }

    /**
     * Check if user can signup.
     *
     * User can signup if it is their initial signup and they match all requirements
     * or if it is subsequential signup and state is cancelled and they match all requirements.
     *
     * @param signup $signup
     * @return bool
     */
    public static function can_signup(signup $signup) : bool {
        // Cannot sign up when already signed up.
        if ($signup->get_state() instanceof booked
            || $signup->get_state() instanceof waitlisted) {
            return false;
        }
        return $signup->can_switch(booked::class, waitlisted::class, requested::class, requestedrole::class);
    }

    /**
     * Get expected state upon signup
     *
     * @param signup $signup
     * @return bool
     */
    public static function expected_signup_state(signup $signup) : state {
        $oldstate = $signup->get_state();
        if ($oldstate->can_switch(booked::class, waitlisted::class, requested::class, requestedrole::class)) {
            return $oldstate->switch_to(booked::class, waitlisted::class, requested::class, requestedrole::class);
        }
        return $oldstate;
    }
    /**
     * Get the reasons a signup is failing
     * @param signup $signup
     * @return array
     */
    public static function get_failures(signup $signup) : array {
        // Cannot sign up when already signed up.
        if ($signup->get_state() instanceof booked
            || $signup->get_state() instanceof waitlisted) {
            return ['addalreadysignedupattendee' => get_string('error:addalreadysignedupattendee', 'mod_facetoface')];
        }
        return $signup->get_failures(booked::class, waitlisted::class, requested::class, requestedrole::class);
    }

    /**
     * Cancel a user signup to a seminar event.
     *
     * @param signup $signup
     * @param string $cancellationreason
     * @return signup
     */
    public static function user_cancel(signup $signup, string $cancellationreason = '') : signup {
        global $DB;

        // User cannot cancel their own signup - no effect.
        if (!self::can_user_cancel($signup)) {
            throw new signup_exception("Cannot cancel signup.");
        }

        $seminarevent = $signup->get_seminar_event();
        $trans = $DB->start_delegated_transaction();

        $signup->switch_state(user_cancelled::class);

        // Write or update the cancellation field when necessary/possible.
        if (!empty($cancellationreason)) {
            $params = array('shortname' => 'cancellationnote', 'datatype' => 'text');
            if ($cancelfieldid = $DB->get_field('facetoface_cancellation_info_field', 'id', $params)) {
                $canceldataparams = array('fieldid' => $cancelfieldid, 'facetofacecancellationid' => $signup->get_id());
                if ($DB->record_exists('facetoface_cancellation_info_data', $canceldataparams)) {
                    $DB->set_field('facetoface_cancellation_info_data', 'data', $cancellationreason, $canceldataparams);
                } else {
                    $todb = new stdClass();
                    $todb->data = $cancellationreason;
                    $todb->fieldid = $cancelfieldid;
                    $todb->facetofacecancellationid = $signup->get_id();
                    $DB->insert_record('facetoface_cancellation_info_data', $todb);
                }
            }
        }

        // Remove the calendar entry for the seminar event.
        \mod_facetoface\calendar::remove_seminar_event($seminarevent, 0, $signup->get_userid());

        // Open the spot up for anyone on the waitlist.
        self::update_attendees($seminarevent);

        $trans->allow_commit();

        return $signup;
    }

    /**
     * A simple function to check whether a user has cancelled their signup or not.
     * @param signup $signup
     * @return bool
     */
    public static function is_cancelled(signup $signup) : bool {
        $state = $signup->get_state();
        return $state instanceof \mod_facetoface\signup\state\user_cancelled;
    }

    /**
     * Check if the user can cancel their signup or not.
     * @param signup $signup
     * @return bool
     */
    public static function can_user_cancel(signup $signup) : bool {
        return $signup->can_switch(user_cancelled::class);
    }

    /**
     * Process the attendance records for a seminar event. This is for event taking attendance.
     *
     * @param seminar_event $seminarevent
     * @param array         $attendance     an array containing [ signup::id => state::get_code() ]
     * @param array         $grades         an array containing [ signup::id => grade or null ]
     *                                      only valid for seminars with manual event grading
     * @return bool
     */
    public static function process_attendance(seminar_event $seminarevent, array $attendance, array $grades = null) : bool {
        if ($grades === null) {
            $grades = [];
        }
        $eventgradingmanual = $seminarevent->get_seminar()->get_eventgradingmanual() != 0;

        foreach ($attendance as $signupid => $statuscode) {
            $grade = $grades[$signupid] ?? null;
            $signup = new signup($signupid);
            $desiredstate = state::from_code($statuscode);

            if (!self::switch_state_and_grade($seminarevent, $signup, $desiredstate, $eventgradingmanual, $grade)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Switch state and grade.
     *
     * @param seminar_event $seminarevent
     * @param signup        $signup
     * @param string        $desiredstate       the class name of a new state
     * @param bool          $eventgradingmanual true to use $grade value, false to use default value instead of $grade value
     * @param float         $grade              new grade or null to use default by $desiredstate::get_grade()
     * @return bool
     */
    private static function switch_state_and_grade(seminar_event $seminarevent, signup $signup, string $desiredstate, bool $eventgradingmanual, float $grade = null) : bool {
        $currentstate = $signup->get_state();

        if ($desiredstate == not_set::class) {
            // If current state is attendance, try fallback to booked, otherwise leave it as is
            if ($currentstate instanceof attendance_state) {
                $desiredstate = booked::class;
            } else {
                $desiredstate = get_class($currentstate);
            }
        }

        if (!$eventgradingmanual) {
            $grade = $desiredstate::get_grade();
        }

        if ($signup->can_switch($desiredstate)) {
            $signup->switch_state_with_grade($grade, null, $desiredstate);
        } else {
            $same_state = get_class($currentstate) === $desiredstate;
            if ($same_state) {
                $signupstatus = signup_status::from_current($signup);
                // same states but different grade
                if ($signupstatus->get_grade() !== $grade) {
                    $signupstatus = signup_status::create($signup, $currentstate, 0, $grade, null);
                    $signupstatus->save();
                }
            }

            // do not error_log() when attempting to switch to the same state
            if (!$same_state) {
                error_log(
                    sprintf(
                        "Seminar: could not switch signup #%d from '%s' to '%s'",
                        $signup->get_id(), get_class($currentstate), $desiredstate
                    )
                );
                return false;
            }
        }

        return self::grade_signup($seminarevent, $signup);
    }

    /**
     * Calculate a user's final grade.
     *
     * @param \stdClass|seminar $facetoface
     * @param int $userid
     * @return float|null a grade value or null if nothing applicable
     */
    public static function compute_final_grade($facetoface, int $userid) : ?float {
        global $DB;

        if ($facetoface instanceof seminar) {
            $f2fid = $facetoface->get_id();
            $grading_method = $facetoface->get_eventgradingmethod();
        } else if ($facetoface instanceof \stdClass) {
            $f2fid = $facetoface->id;
            $grading_method = $facetoface->eventgradingmethod ?? 0; // default to 0 (highest)
        } else {
            throw new \coding_exception('$facetoface must be a signup object or a database record');
        }

        switch ($grading_method) {
            case seminar::GRADING_METHOD_GRADEHIGHEST:
                $select_grade = 'MAX(sus.grade) AS grade';
                $order_by = '';
                break;
            case seminar::GRADING_METHOD_GRADELOWEST:
                $select_grade = 'MIN(sus.grade) AS grade';
                $order_by = '';
                break;
            case seminar::GRADING_METHOD_EVENTFIRST:
                $select_grade = 'sus.grade';
                $order_by = 'ORDER BY m.mintimestart';
                break;
            case seminar::GRADING_METHOD_EVENTLAST:
                $select_grade = 'sus.grade';
                $order_by = 'ORDER BY m.maxtimefinish DESC';
                break;

            default:
                throw new \coding_exception(sprintf(
                    "Grading method %d of seminar #%d is not defined",
                    $grading_method, $f2fid
                ));
        }

        $sql = '
            SELECT ' . $select_grade . '
            FROM {facetoface_signups_status} sus
            JOIN {facetoface_signups} su ON su.id = sus.signupid
            JOIN {facetoface_sessions} s ON s.id = su.sessionid
            LEFT JOIN (
                SELECT
                    fsd.sessionid,
                    MIN(fsd.timestart) AS mintimestart,
                    MAX(fsd.timefinish) AS maxtimefinish
                FROM {facetoface_sessions_dates} fsd
                WHERE (1=1)
                GROUP BY fsd.sessionid
            ) m ON m.sessionid = s.id
            JOIN {user} u ON u.id = su.userid
            WHERE u.id = :uid AND s.facetoface = :f2f AND sus.superceded = 0 AND sus.grade IS NOT NULL
            ' . $order_by;

        $set = $DB->get_recordset_sql($sql, ['uid' => $userid, 'f2f' => $f2fid], 0, 1);
        try {
            if ($set->valid()) {
                return $set->current()->grade;
            }
            return null;
        } finally {
            $set->close();
        }
    }

    /**
     * Create grade item for given sign-up.
     *
     * @param seminar_event $seminarevent
     * @param signup        $signup
     * @return bool
     */
    private static function grade_signup(seminar_event $seminarevent, signup $signup) : bool {
        global $CFG, $USER;

        // Necessary for facetoface_grade_item_update()
        require_once($CFG->dirroot . '/mod/facetoface/lib.php');

        $seminar = $seminarevent->get_seminar();

        $finalgrade = self::compute_final_grade($seminar, $signup->get_userid());

        $timenow = time();

        $grade = new \stdclass();
        $grade->userid = $signup->get_userid();
        $grade->rawgrade = $finalgrade;
        // TODO: support scale, pass, min, max
        $grade->rawgrademin = 0;
        $grade->rawgrademax = 100;
        $grade->timecreated = $timenow;
        $grade->timemodified = $timenow;
        $grade->usermodified = $USER->id;

        $facetoface = $seminar->get_properties();

        // Grade functions stay in lib file.
        if (!facetoface_grade_item_update($facetoface, $grade)) {
            error_log("F2F: could not grade signup '{$signup->get_id()}' as '$grade'");
            return false;
        }
        return true;
    }

    /**
     * Update attendees status regarding new event settingss
     * @param seminar_event $seminarevent
     */
    public static function update_attendees(seminar_event $seminarevent) {
        if ($seminarevent->is_started()) {
            return;
        }

        $helper = new attendees_helper($seminarevent);

        // Just need the list of attendees without associated user id at array key.
        $users = array_values($helper->get_attendees_with_codes([booked::get_code(), waitlisted::get_code()]));
        $reservedusers = $helper->get_reservations();

        // We need to add reservation into this list too, because the seminar event is being udpated, and reservation
        // without the attendee to fill up the space need to be udpated as well.
        foreach ($reservedusers as $reserveduser) {
            if ($reserveduser->has_bookedby() && !$reserveduser->is_valid()) {
                // We only want the free space reservation, those reservation that had filled up would probably
                // already included in the list of attendees_with_codes.
                $users[] = $reserveduser;
                continue;
            }
        }

        \core_collator::asort_objects_by_property($users, 'timesignedup', \core_collator::SORT_NUMERIC);

        if ($users) {
            // We want to book users from waitlist...
            $oldstate = waitlisted::class;
            $newstate = booked::class;
            // Unless there no sessions, in which case we want to waitlist booked users.
            if (!$seminarevent->is_sessions()) {
                $oldstate = booked::class;
                $newstate = waitlisted::class;
            }

            foreach ($users as $user) {
                $signup = new \mod_facetoface\signup((int)$user->submissionid);
                $signup->set_actorid($signup->get_userid());
                $state = $signup->get_state();
                if ($state instanceof $oldstate) {
                    if ($state->can_switch($newstate)) {
                        $signup->switch_state($newstate);
                    }
                }
            }
        }
    }

    /**
     * Add default job assignment if required
     */
    protected static function set_default_job_assignment(signup $signup) {
        $seminar = $signup->get_seminar_event()->get_seminar();
        $selectjobassignmentonsignupglobal = get_config(null, 'facetoface_selectjobassignmentonsignupglobal');
        $jobassignmentrequired = !empty($selectjobassignmentonsignupglobal) && !empty($seminar->get_selectjobassignmentonsignup());

        if ($jobassignmentrequired) {
            $jobassignment = \totara_job\job_assignment::get_first($signup->get_userid(), false);

            if (!empty($jobassignment)) {
                $signup->set_jobassignmentid((int)$jobassignment->id);
            }
        }
    }

    /**
     * Trigger signup event
     * @param signup $signup
     */
    protected static function trigger_event(signup $signup) {
        $cm = $signup->get_seminar_event()->get_seminar()->get_coursemodule();
        $context = \context_module::instance($cm->id);
        \mod_facetoface\event\session_signup::create_from_signup($signup, $context)->trigger();
    }

    /**
     * Remove user expression of interest since they are already signed up
     *
     * @param signup $signup
     */
    protected static function withdraw_interest(signup $signup) {
        $interest = interest::from_seminar($signup->get_seminar_event()->get_seminar(), $signup->get_userid());
        $interest->withdraw();
    }

    /**
     * Confirms waitlisted users from an array as booked on a session.
     *
     * @param seminar_event $seminarevent
     * @param array  $userids    Array of user ids to confirm
     * @return array $result success|failures
     */
    public static function confirm_waitlist(\mod_facetoface\seminar_event $seminarevent, $userids) {
        global $DB;

        $errors = [];
        foreach ($userids as $userid) {
            $signup = signup::create($userid, $seminarevent);
            if ($signup->get_state() instanceof \mod_facetoface\signup\state\not_set) {
                continue;
            }

            if ($signup->can_switch(\mod_facetoface\signup\state\booked::class)) {
                $signup->switch_state(\mod_facetoface\signup\state\booked::class);
                $conditions = array('sessionid' => $seminarevent->get_id(), 'userid' => $userid);
                $existingsignup = $DB->get_record('facetoface_signups', $conditions, '*', MUST_EXIST);
                notice_sender::confirm_booking(new signup($existingsignup->id), $existingsignup->notificationtype);
            } else {
                $failures = $signup->get_failures(\mod_facetoface\signup\state\booked::class);
                if (!empty($failures)) {
                    $errors[$signup->get_userid()] = current($failures);
                }
            }
        }
        $result = [];
        if (empty($errors)) {
            $result['result'] = 'success';
        } else {
            $result['result'] = 'failure';
            $errormsgs = [];
            list($sqlin, $inparams) = $DB->get_in_or_equal(array_keys($errors));
            $users = $DB->get_records_sql('SELECT * FROM {user} WHERE id '.$sqlin, $inparams);
            foreach ($users as $user) {
                $errormsgs[] = get_string(
                    'error:cannotchangestateuser',
                    'mod_facetoface',
                    (object)['user' => fullname($user), 'error' => $errors[$user->id]]
                );
            }
            $result['content'] = \html_writer::alist($errormsgs);
        }
        return $result;
    }

    /**
     * Randomly books waitlisted users on to a session.
     *
     * @param seminar_event $seminarevent
     * @param array $userids a list of user ids
     * @return array $result success|failure
     */
    public static function confirm_waitlist_randomly(\mod_facetoface\seminar_event $seminarevent, $userids) {
        $helper = new attendees_helper($seminarevent);
        $signupcount = $helper->count_attendees();
        $numtoconfirm = $seminarevent->get_capacity() - $signupcount;

        if (count($userids) <= $seminarevent->get_capacity()) {
            $winners = $userids;
        } else {
            $winners = array_rand(array_flip($userids), $numtoconfirm);
            if ($numtoconfirm == 1) {
                $winners = array($winners);
            }
        }
        return self::confirm_waitlist($seminarevent, $winners);
    }

    /**
     * Cancels waitlisted users from an array on a session
     * @param seminar_event $seminarevent
     * @param array  $userids    Array of user ids to cancel
     */
    public static function cancel_waitlist(\mod_facetoface\seminar_event $seminarevent, $userids) {

        foreach ($userids as $userid) {
            $signup = signup::create($userid, $seminarevent);
            if ($signup->get_state() instanceof \mod_facetoface\signup\state\not_set) {
                continue;
            }
            if ($signup->can_switch(\mod_facetoface\signup\state\user_cancelled::class)) {
                $signup->switch_state(\mod_facetoface\signup\state\user_cancelled::class);
            }
        }
    }

    /**
     * @param signup $signup
     * @return stdClass[]
     */
    public static function find_managers_from_signup(signup $signup): array {
        global $DB;

        if (!$signup->exists()) {
            // Prevent the path that throws exception with empty signup id. Just don't bother to find the managers of
            // a user within signup, if the signup does not exists in the system though.
            return [];
        }

        $managerselect = get_config(null, 'facetoface_managerselect');
        $selectjobassignmentonsignupglobal = get_config(null, 'facetoface_selectjobassignmentonsignupglobal');

        $managers = [];

        if ($managerselect && $signup->has_manager()) {
            // Check if they selected a manager for their signup.
            $managerid = $signup->get_managerid();
            $managers[] = $DB->get_record('user', ['id' => $managerid]);
        } else if ($selectjobassignmentonsignupglobal && $signup->has_jobassignment()) {
            // The job assignment could not be found here, because the system admin might had deleted
            // the job assignment record, but did not update the seminar signup record here.

            // This could mean that, seminar system is not able to notify this user's manager here.
            // However, when deleting the job assignment of a user, this could indicate that this
            // user is no longer being managed by the same manager anymore. Unless, deleting job
            $jobasssignmentid = $signup->get_jobassignmentid();
            $ja = job_assignment::get_with_id($jobasssignmentid, false);

            if (null !== $ja && $ja->managerid) {
                $managers[] = $DB->get_record('user', ['id' => $ja->managerid]);
            }
        } else {
            $userid = $signup->get_userid();
            $managerids = job_assignment::get_all_manager_userids($userid);

            if (!empty($managerids)) {
                [$psql, $params] = $DB->get_in_or_equal($managerids, SQL_PARAMS_NAMED);
                $managers = $DB->get_records_select('user', "id {$psql}", $params);
            }
        }

        array_walk($managers, function (stdClass &$manager) {
            $manager->fullname = fullname($manager);
        });

        return $managers;
    }

    /**
     * Get user details.
     * @param int $userid
     * @return stdClass
     */
    public static function get_user_details(int $userid): \stdClass {
        global $DB;

        return $DB->get_record('user', ['id' => $userid], '*', MUST_EXIST);
    }
}
