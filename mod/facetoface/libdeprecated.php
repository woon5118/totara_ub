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
 * @author  Oleg Demeshev <oleg.demeshev@totaralearning.com>
 * @package mod_facetoface
 */

// Signup status codes (remember to update $MDL_F2F_STATUS)
define('MDL_F2F_STATUS_USER_CANCELLED',     10);
define('MDL_F2F_STATUS_SESSION_CANCELLED',  20);
define('MDL_F2F_STATUS_DECLINED',           30);
define('MDL_F2F_STATUS_REQUESTED',          40);
define('MDL_F2F_STATUS_REQUESTEDADMIN',     45); // For 2 step approval.
define('MDL_F2F_STATUS_APPROVED',           50);
define('MDL_F2F_STATUS_WAITLISTED',         60);
define('MDL_F2F_STATUS_BOOKED',             70);
define('MDL_F2F_STATUS_NO_SHOW',            80);
define('MDL_F2F_STATUS_PARTIALLY_ATTENDED', 90);
define('MDL_F2F_STATUS_FULLY_ATTENDED',     100);
define('MDL_F2F_STATUS_NOT_SET',            110);

// This array must match the status codes above, and the values
// must equal the end of the constant name but in lower case
global $MDL_F2F_STATUS;
$MDL_F2F_STATUS = array(
    MDL_F2F_STATUS_USER_CANCELLED       => 'user_cancelled',
    MDL_F2F_STATUS_SESSION_CANCELLED    => 'session_cancelled',
    MDL_F2F_STATUS_DECLINED             => 'declined',
    MDL_F2F_STATUS_REQUESTED            => 'requested',
    MDL_F2F_STATUS_REQUESTEDADMIN       => 'requestedadmin',
    MDL_F2F_STATUS_APPROVED             => 'approved',
    MDL_F2F_STATUS_WAITLISTED           => 'waitlisted',
    MDL_F2F_STATUS_BOOKED               => 'booked',
    MDL_F2F_STATUS_NO_SHOW              => 'no_show',
    MDL_F2F_STATUS_PARTIALLY_ATTENDED   => 'partially_attended',
    MDL_F2F_STATUS_FULLY_ATTENDED       => 'fully_attended',
    MDL_F2F_STATUS_NOT_SET              => 'not_set'
);

define('APPROVAL_NONE', 0);
define('APPROVAL_SELF', 1);
define('APPROVAL_ROLE', 2);
define('APPROVAL_MANAGER', 4);
define('APPROVAL_ADMIN', 8);

// Calendar-related constants
if (!defined('CALENDAR_MAX_NAME_LENGTH')) {
    // Admins may override this in config.php if necessary.
    define('CALENDAR_MAX_NAME_LENGTH', 256);
}

/**
 * Define allow cancellation option constants.
 *
 * @deprecated since Totara 12.0
 * please use seminar_event::ALLOW_CANCELLATION_X constants instead
 */
define('MDL_F2F_ALLOW_CANCELLATION_NEVER', 0);
define('MDL_F2F_ALLOW_CANCELLATION_ANY_TIME', 1);
define('MDL_F2F_ALLOW_CANCELLATION_CUT_OFF', 2);

/**
 * Turn undefined manager messages into empty strings and deal with checkboxes
 *
 * @deprecated since Totara 12.0
 */
function facetoface_fix_settings($facetoface) {

    debugging('facetoface_fix_settings() function has been deprecated, this functionality is moved to mod_form::get_data()',
        DEBUG_DEVELOPER);

    if (empty($facetoface->completionstatusrequired)) {
        $facetoface->completionstatusrequired = null;
    }
    if (empty($facetoface->reservecancel)) {
        $facetoface->reservecanceldays = 0;
    }
    if (empty($facetoface->emailmanagerconfirmation)) {
        $facetoface->confirmationinstrmngr = null;
    }
    if (empty($facetoface->emailmanagerreminder)) {
        $facetoface->reminderinstrmngr = null;
    }
    if (empty($facetoface->emailmanagercancellation)) {
        $facetoface->cancellationinstrmngr = null;
    }
    if (empty($facetoface->usercalentry)) {
        $facetoface->usercalentry = 0;
    }
    if (empty($facetoface->thirdpartywaitlist)) {
        $facetoface->thirdpartywaitlist = 0;
    }
    if (!empty($facetoface->shortname)) {
        // This needs to match the actual database field size in mod/facetoface/db/install.xml file.
        $facetoface->shortname = core_text::substr($facetoface->shortname, 0, 32);
    }
    if (empty($facetoface->declareinterest)) {
        $facetoface->declareinterest = 0;
    }
    if (empty($facetoface->interestonlyiffull) || !$facetoface->declareinterest) {
        $facetoface->interestonlyiffull = 0;
    }
    if (empty($facetoface->selectjobassignmentonsignup) || !$facetoface->selectjobassignmentonsignup) {
        $facetoface->selectjobassignmentonsignup = 0;
    }
    if (empty($facetoface->forceselectjobassignment) || !$facetoface->forceselectjobassignment) {
        $facetoface->forceselectjobassignment = 0;
    }
}

/**
 * Determines whether an activity requires the user to recieve approval before signup.
 *
 * @param  object $facetoface A database fieldset object for the facetoface activity
 * @return boolean whether a person needs someones approval to sign up
 *
 * @deprecated since Totara 12.0
 */
function facetoface_approval_required($facetoface) {

    debugging('facetoface_approval_required function has been deprecated, please use seminar::approval_required()',
        DEBUG_DEVELOPER);

    return $facetoface->approvaltype == APPROVAL_MANAGER
        || $facetoface->approvaltype == APPROVAL_ROLE
        || $facetoface->approvaltype == APPROVAL_ADMIN;
}

/**
 * Given a facetoface object from the edit form this function
 * transforms the approvaloptions data into the database friendly format.
 *
 * @param object $facetoface
 *
 * @deprecated since Totara 12.0
 */
function facetoface_approval_settings($facetoface) {

    debugging('facetoface_approval_settings() function has been deprecated, this functionality is moved to mod_form::get_data()',
        DEBUG_DEVELOPER);

    if ($facetoface->approvaloptions == 'approval_none') {
        $facetoface->approvaltype = APPROVAL_NONE;
    } else if ($facetoface->approvaloptions == 'approval_self') {
        $facetoface->approvaltype = APPROVAL_SELF;
    } else if (preg_match('/approval_role_/', $facetoface->approvaloptions)) {
        $split = explode('_', $facetoface->approvaloptions);
        $facetoface->approvaltype = APPROVAL_ROLE;
        $facetoface->approvalrole = $split[2];
    } else if ($facetoface->approvaloptions == 'approval_manager') {
        $facetoface->approvaltype = APPROVAL_MANAGER;
    } else if ($facetoface->approvaloptions == 'approval_admin') {
        $facetoface->approvaltype = APPROVAL_ADMIN;
        $selected = empty($facetoface->selectedapprovers) ? array() : explode(',', $facetoface->selectedapprovers);
        $facetoface->approvaladmins = implode(',', $selected);
    }

    if (isset($facetoface->approval_termsandconds)) {
        $facetoface->approvalterms = s($facetoface->approval_termsandconds);
    }
}

/**
 * Determines whether the user has already expressed interest in this activity.
 *
 * @param  object $facetoface A database fieldset object for the facetoface activity
 * @param  object $userid     Default to current user if null
 * @return boolean whether a person needs a manager to sign up for that activity
 *
 * @deprecated since Totara 12.0
 */
function facetoface_user_declared_interest($facetoface, $userid = null) {
    global $DB, $USER;

    debugging('facetoface_user_declared_interest() function has been deprecated, please use interest::user_declared() instead',
        DEBUG_DEVELOPER);

    if (is_null($userid)) {
        $userid = $USER->id;
    }

    return $DB->record_exists('facetoface_interest', array('facetoface' => $facetoface->id, 'userid' => $userid));
}

/**
 * Determines whether the user can declare interest in the activity.
 *
 * @param  object $facetoface A database fieldset object for the facetoface activity
 * @param  object $userid     Default to current user if null
 * @return boolean whether a person needs a manager to sign up for that activity
 *
 * @deprecated since Totara 12.0
 */
function facetoface_activity_can_declare_interest($facetoface, $userid = null) {
    global $DB, $USER;

    debugging('facetoface_activity_can_declare_interest() function has been deprecated, please use interest::can_user_declare() instead',
        DEBUG_DEVELOPER);

    // "Declare interest" must be turned on for the activity.
    if (!$facetoface->declareinterest) {
        return false;
    }

    // Check that the user has no existing signup.
    if (is_null($userid)) {
        $userid = $USER->id;
    }

    // If user already declared interest, cannot declare again.
    if (facetoface_user_declared_interest($facetoface, $userid)) {
        return false;
    }

    // If "only when full" is turned on, allow only when all sessions are fully booked.
    if ($facetoface->interestonlyiffull) {
        // If user signed - no declare interest.
        $submission = facetoface_get_user_submissions($facetoface->id, $userid,
            MDL_F2F_STATUS_REQUESTED, MDL_F2F_STATUS_FULLY_ATTENDED);
        if (!empty($submission)) {
            return false;
        }

        $now = time();
        $sql = "
            SELECT DISTINCT fs.id
            FROM {facetoface_sessions} fs
                INNER JOIN {facetoface_sessions_dates} fsd ON (fsd.sessionid = fs.id)
            WHERE fsd.timestart > :now
                AND fs.facetoface = :facetoface
        ";

        $sessions = $DB->get_records_sql($sql, array('now' => $now, 'facetoface' => $facetoface->id));
        foreach ($sessions as $sessionrec) {
            $session = facetoface_get_session($sessionrec->id);

            if (facetoface_can_user_signup($session, $userid, $now)) {
                return false;
            }
        }
    }
    return true;
}

/**
 * Declares interest in a facetoface activity for a user.
 * Assume we have already checked that no existing decleration exists
 * And all the necessary permissions
 *
 * @param  object $facetoface A database fieldset object for the facetoface activity
 * @param  string $reason     Reason provided by user
 * @param  object $userid     Default to current user if null
 * @return bool|int           Result of the insert
 *
 * @deprecated since Totara 12.0
 */
function facetoface_declare_interest($facetoface, $reason = '', $userid = null)
{
    global $DB, $USER;

    debugging('facetoface_declare_interest() function has been deprecated, please use interest::declare() instead',
        DEBUG_DEVELOPER);

    if (is_null($userid)) {
        $userid = $USER->id;
    }

    $toinsert = (object)array(
        'facetoface' => $facetoface->id,
        'userid' => $userid,
        'timedeclared' => time(),
        'reason' => $reason,
    );

    return $DB->insert_record('facetoface_interest', $toinsert);
}

/**
 * Delete reservations for a given session and manager
 *
 * @param int $sessionid
 * @param int $managerid
 *
 * @return bool True if dng of the reservations succeeded
 *
 * @deprecated since Totara 12.0
 */
function facetoface_delete_reservations($sessionid, $managerid) {
    global $DB;

    debugging('facetoface_delete_reservations() function has been deprecated, please use reservations::delete()',
        DEBUG_DEVELOPER);

    $signups = $DB->get_records_sql('SELECT id FROM {facetoface_signups} WHERE userid = 0 AND sessionid = :sessionid AND bookedby = :managerid',
        array('sessionid' => $sessionid, 'managerid' => $managerid));

    $transaction = $DB->start_delegated_transaction();
    $result = true;

    if ($signups) {
        list($signupwhere, $signupparams) = $DB->get_in_or_equal(array_keys($signups));
        // Delete signup status records.
        $result = $DB->delete_records_select('facetoface_signups_status', 'signupid ' . $signupwhere
            , $signupparams);
    }

    // Delete signups.
    $result = $result && $DB->delete_records('facetoface_signups',
            array('userid' => 0, 'sessionid' => $sessionid, 'bookedby' => $managerid));

    $transaction->allow_commit();

    return $result;
}

/**
 * Get a count of the number of spaces reserved by each manager
 * for a given session.
 *
 * @param int $sessionid
 *
 * @return array Array of reservations
 *
 * @deprecated since Totara 12.0
 */
function facetoface_get_session_reservations($sessionid) {
    global $DB;

    debugging('facetoface_get_session_reservations() function has been deprecated, please use reservations::get()',
        DEBUG_DEVELOPER);

    $userfields =  get_all_user_name_fields(true, 'u');

    $reservation_sql = "SELECT bookedby, COUNT(fs.id) as reservedspaces, sessionid, {$userfields}
        FROM {facetoface_signups} fs
        JOIN {user} u ON fs.bookedby = u.id
        WHERE bookedby != :bookedby
        AND userid = :userid
        AND sessionid = :sessionid
        GROUP BY
        bookedby, sessionid, {$userfields}";

    $reservation_params = array('bookedby' => 0, 'userid' => 0, 'sessionid' => $sessionid);

    $reservations = $DB->get_records_sql($reservation_sql, $reservation_params);

    return $reservations;
}

/**
 * Returns details of whether or not the user can reserve or allocate spaces for their team.
 * Note - an exception is throw if the managerid is set to another user and the current user is missing the
 * 'reserveother' capability
 *
 * @param object $facetoface
 * @param object[] $sessions
 * @param context $context
 * @param int $managerid optional defaults to current user
 * @throws moodle_exception
 * @return array with values 'allocate' - array how many spare allocations there are, per sesion + 'all'
 *                                        (false if not able to allocate)
 *                           'allocated' - array how many spaces have been allocated by this manager, per session + 'all'
 *                           'maxallocate' - the maximum number of spaces this manager could allocate, per session + 'all'
 *                           'reserve' - array how many spare reservations there are, per session + 'all'
 *                                       (false if not able to reserve)
 *                           'reserved' - array how many spaces have been reserved by this manager, per session + 'all'
 *                           'maxreserve' - array the maximum number of spaces this manager could still allocate, per session + 'all'
 *                           'reservedeadline' - any sessions that start after this date are able to reserve places
 *                           'reservecancel' - any sessions that before this date will have all reservations deleted
 *
 * @deprecated since Totara 12.0
 */
function facetoface_can_reserve_or_allocate($facetoface, $sessions, $context, $managerid = null) {
    global $USER;

    debugging('facetoface_can_reserve_or_allocate() function has been deprecated, please use reservations::can_reserve_or_allocate()',
        DEBUG_DEVELOPER);

    $reserveother = has_capability('mod/facetoface:reserveother', $context);
    if (!$managerid || $managerid == $USER->id) {
        $managerid = $USER->id;
    } else {
        if (!$reserveother) {
            throw new moodle_exception('cannotreserveother', 'mod_facetoface');
        }
    }

    $ret = array(
        'allocate' => false, 'allocated' => array('all' => 0), 'maxallocate' => array('all' => 0),
        'reserve' => false, 'reserved' => array('all' => 0), 'maxreserve' => array('all' => 0),
        'reservedeadline' => 0, 'reservecancel' => 0, 'reserveother' => false
    );
    if (!$facetoface->managerreserve) {
        return $ret; // Manager reservations disabled for this activity.
    }

    $ret['reserveother'] = $reserveother;
    $ret['reservedeadline'] = time() + ($facetoface->reservedays * DAYSECS);
    $ret['reservecancel'] = time() + ($facetoface->reservecanceldays * DAYSECS);

    if (!has_capability('mod/facetoface:reservespace', $context, $managerid)) {
        return $ret; // Manager is not allowed to reserve/allocate any spaces.
    }

    if (!\totara_job\job_assignment::has_staff($managerid)) {
        return $ret; // No staff to allocate spaces to.
    }

    // Allowed to make allocations / reservations - gather some details about the spaces remaining.
    $allocations = facetoface_count_allocations($facetoface, $managerid);
    $reservations = facetoface_count_reservations($facetoface, $managerid);
    foreach ($sessions as $session) {
        if (!isset($allocations[$session->id])) {
            $allocations[$session->id] = 0;
        }
        if (!isset($reservations[$session->id])) {
            $reservations[$session->id] = 0;
        }
    }
    $ret['allocate'] = array();
    $ret['allocated'] = $allocations;
    $ret['maxallocate'] = array();
    $ret['reserve'] = array();
    $ret['reserved'] = $reservations;
    $ret['maxreserve'] = array();

    foreach ($allocations as $sid => $allocation) {
        $reservation = isset($reservations[$sid]) ? $reservations[$sid] : 0;
        // Max allocation = overall max - allocations for other sessions - reservations for other sessions.
        $ret['maxallocate'][$sid] = $facetoface->maxmanagerreserves - ($allocations['all'] - $allocation);
        $ret['maxallocate'][$sid] -= ($reservations['all'] - $reservation);
        $ret['allocate'][$sid] = $ret['maxallocate'][$sid] - $allocation; // Number left to allocate.

        // Max reservations = overall max - allocations (all) - reservations for other sessions
        $ret['maxreserve'][$sid] = $facetoface->maxmanagerreserves - $allocations['all'];
        $ret['maxreserve'][$sid] -= ($reservations['all'] - $reservation);
        $ret['reserve'][$sid] = $ret['maxreserve'][$sid] - $reservation; // Number left to reserve.

        // Make sure no values are < 0 (e.g. if the allocation limit has changed).
        $ret['maxallocate'][$sid] = max(0, $ret['maxallocate'][$sid]);
        $ret['allocate'][$sid] = max(0, $ret['allocate'][$sid]);
        $ret['maxreserve'][$sid] = max(0, $ret['maxreserve'][$sid]);
        $ret['reserve'][$sid] = max(0, $ret['reserve'][$sid]);
    }

    return $ret;
}

/**
 * Given the number of spaces the manager has reserved / allocated (from 'can_reserve_or_allocate')
 * and the overall remaining capacity of the particular session, work out how many spaces they can
 * actually reserve/allocate for this session.
 *
 * @param int $sessionid
 * @param array $reserveinfo
 * @param int $capacityleft
 * @return array - see facetoface_can_reserve_or_allocate for details
 *
 * @deprecated since Totara 12.0
 */
function facetoface_limit_reserveinfo_to_capacity_left($sessionid, $reserveinfo, $capacityleft) {

    debugging('facetoface_limit_reserveinfo_to_capacity_left() function has been deprecated, please use reservations::limit_info_to_capacity_left()',
        DEBUG_DEVELOPER);

    if (!empty($reserveinfo['reserve'])) {
        if ($reserveinfo['reserve'][$sessionid] > $capacityleft) {
            $reserveinfo['reserve'][$sessionid] = $capacityleft;
            $reserveinfo['maxreserve'][$sessionid] = $reserveinfo['reserve'][$sessionid] + $reserveinfo['reserved'][$sessionid];
        }
    }
    return $reserveinfo;
}

/**
 * Given the session details, determines if reservations are still allowed, or if the deadline has now passed.
 *
 * @param array $reserveinfo
 * @param object $session
 * @return array - see facetoface_can_reserve_or_allocate for details, but adds two new values:
 *                  'reservepastdeadline' - true if the deadline for adding new reservations has passed
 *                  'reservepastcancel' - true if all existing reservations should be cancelled
 *
 * @deprecated since Totara 12.0
 */
function facetoface_limit_reserveinfo_by_session_date($reserveinfo, $session) {

    debugging('facetoface_limit_reserveinfo_by_session_date() function has been deprecated, please use reservations::limit_info_by_session_date()',
        DEBUG_DEVELOPER);

    $reserveinfo['reservepastdeadline'] = false;
    $reserveinfo['reservepastcancel'] = false;
    if ($session->mintimestart) {
        $firstdate = reset($session->sessiondates);
        if (!isset($reserveinfo['reservedeadline']) || $firstdate->timestart <= $reserveinfo['reservedeadline']) {
            $reserveinfo['reservepastdeadline'] = true;
        }
        if (!isset($reserveinfo['reservecancel']) || $firstdate->timestart <= $reserveinfo['reservecancel']) {
            $reserveinfo['reservepastcancel'] = true;
        }
    }

    return $reserveinfo;
}

/**
 * Add the number of reservations requested (it is assumed that all capacity checks have
 * already been done by this point, so no extra checking is performed).
 *
 * @param object $session the session the reservations are for
 * @param int $bookedby the user making the reservations
 * @param int $number how many reservations to make
 * @param int $waitlisted how many reservations to add to the waitlist (not included in $number)
 *
 * @deprecated since Totara 12.0
 */
function facetoface_add_reservations($session, $bookedby, $number, $waitlisted) {
    global $DB;

    debugging('facetoface_add_reservations() function has been deprecated, please use reservations::add()',
        DEBUG_DEVELOPER);

    $usersignup = (object)array(
        'sessionid' => $session->id,
        'userid' => 0,
        'notificationtype' => MDL_F2F_NOTIFICATION_AUTO,
        'archived' => 0,
        'bookedby' => $bookedby,
    );

    for ($i=0; $i<($number+$waitlisted); $i++) {
        $usersignup->id = $DB->insert_record('facetoface_signups', $usersignup);
        if ($session->mintimestart && ($i < $number)) {
            $status = MDL_F2F_STATUS_BOOKED;
        } else {
            $status = MDL_F2F_STATUS_WAITLISTED;
        }
        facetoface_update_signup_status($usersignup->id, $status, $bookedby);
    }

    facetoface_update_attendees($session);
}

/**
 * Remove the (up to) the given number of reservations originally made by the given user.
 *
 * @param object $facetoface
 * @param object $session the session to remove the reservations from
 * @param int $bookedby the user who made the original reservations
 * @param int $number the number of reservations to remove
 * @param bool $sendnotification
 *
 * @deprecated since Totara 12.0
 */
function facetoface_remove_reservations($facetoface, $session, $bookedby, $number, $sendnotification = false) {
    global $DB;

    debugging('facetoface_remove_reservations() function has been deprecated, please use reservations::remove()',
        DEBUG_DEVELOPER);

    $sql = 'SELECT su.id
              FROM {facetoface_signups} su
              JOIN {facetoface_signups_status} sus ON sus.signupid = su.id AND sus.superceded = 0
             WHERE su.sessionid = :sessionid AND su.userid = 0 AND su.bookedby = :bookedby
             ORDER BY sus.statuscode ASC, id DESC';
    // Start by deleting low-status reservations (cancelled, waitlisted), then order by most recently booked.
    $params = array('sessionid' => $session->id, 'bookedby' => $bookedby);

    $reservations = $DB->get_records_sql($sql, $params, 0, $number);
    $removecount = count($reservations);
    foreach ($reservations as $reservation) {
        $DB->delete_records('facetoface_signups_status', array('signupid' => $reservation->id));
        $DB->delete_records('facetoface_signups', array('id' => $reservation->id));
    }

    if ($removecount && $sendnotification) {
        $params = array(
            'facetofaceid' => $facetoface->id,
            'type' => MDL_F2F_NOTIFICATION_AUTO,
            'conditiontype' => MDL_F2F_CONDITION_RESERVATION_CANCELLED,
        );
        facetoface_send_notice($facetoface, $session, $bookedby, $params);
    }

    facetoface_update_attendees($session);
}

/**
 * Returns the details of all the other reservations made in the current face to face
 * by the given manager
 *
 * @param object $facetoface
 * @param object $session
 * @param int $managerid
 * @return object[]
 *
 * @deprecated since Totara 12.0
 */
function facetoface_get_other_reservations($facetoface, $session, $managerid) {
    global $DB;

    debugging('facetoface_get_other_reservations() function has been deprecated, please use reservations::get_others()',
        DEBUG_DEVELOPER);

    $usernamefields = get_all_user_name_fields(true, 'u');
    // Get a list of all the bookings the manager has made (not including the current session).
    $sql = "SELECT su.id, s.id AS sessionid, u.id AS userid, {$usernamefields}
              FROM {facetoface_signups} su
              JOIN {facetoface_sessions} s ON s.id = su.sessionid
              JOIN {facetoface_signups_status} sus ON sus.signupid = su.id AND sus.superceded = 0
                                                   AND sus.statuscode > :cancelled
              LEFT JOIN {user} u ON u.id = su.userid
             WHERE su.bookedby = :managerid AND su.sessionid <> :sessionid AND s.facetoface = :facetofaceid
             ORDER BY s.id";
    $params = array('managerid' => $managerid, 'sessionid' => $session->id, 'facetofaceid' => $facetoface->id,
        'cancelled' => MDL_F2F_STATUS_USER_CANCELLED);

    return $DB->get_records_sql($sql, $params);
}

/**
 * Get a list of staff who can be allocated / deallocated + reasons why other users cannot be allocated.
 *
 * @param object $facetoface
 * @param object $session
 * @param int $managerid optional
 * @return object containing potential - list of users who could be allocated
 *                           current - list of users who are already allocated
 *                           othersession - users allocated to another sesssion
 *                           cannotunallocate - users who cannot be unallocated (also listed in 'current')
 *
 * @deprecated since Totara 12.0
 */
function facetoface_get_staff_to_allocate($facetoface, $session, $managerid = null) {
    global $DB, $USER;

    debugging('facetoface_get_staff_to_allocate() function has been deprecated, please use reservations::get_staff_to_allocate()',
        DEBUG_DEVELOPER);

    if (!$managerid) {
        $managerid = $USER->id;
    }

    $ret = (object)array('potential' => array(), 'current' => array(), 'othersession' => array(), 'cannotunallocate' => array());
    $staff = \totara_job\job_assignment::get_staff_userids($managerid);
    if (empty($staff)) {
        return $ret;
    }

    // Get facetoface "multiple signups per session" setting.
    $multiplesignups = $facetoface->multiplesessions;

    list($usql, $params) = $DB->get_in_or_equal($staff, SQL_PARAMS_NAMED);
    // Get list of signed-ups that already exist for these users.
    $uidchar = $DB->sql_cast_2char('u.id');
    $sessionidchar = $DB->sql_cast_2char('su.sessionid');
    $sql = 'SELECT CASE
                   WHEN su.sessionid IS NULL THEN '. $uidchar .'
                   ELSE '. $DB->sql_concat($uidchar, "'_'", $sessionidchar) . ' END
                   AS uniqueid , u.*, su.sessionid, su.bookedby, b.firstname AS bookedbyfirstname, b.lastname AS bookedbylastname,
                   su.statuscode
              FROM {user} u
              LEFT JOIN (
                  SELECT xsu.sessionid, xsu.bookedby, xsu.userid, sus.statuscode
                    FROM {facetoface_signups} xsu
                    JOIN {facetoface_signups_status} sus ON sus.signupid = xsu.id AND sus.superceded = 0
                    JOIN {facetoface_sessions} s ON s.id = xsu.sessionid
                   WHERE s.facetoface = :facetofaceid AND sus.statuscode > :status
              ) su ON su.userid = u.id
              LEFT JOIN {user} b ON b.id = su.bookedby
             WHERE u.id ' . $usql . '
          ORDER BY u.lastname ASC, u.firstname ASC';

    $params['facetofaceid'] = $facetoface->id;
    // Statuses greater than declined to handle cases where people change their mind.
    $params['status'] = MDL_F2F_STATUS_DECLINED;
    $users = $DB->get_records_sql($sql, $params);

    foreach ($staff as $member) {
        // Get the signups for the user in this activity.
        $usersignups = totara_search_for_value($users, 'id', TOTARA_SEARCH_OP_EQUAL, $member);

        // Get signup for this user in this session (if exists).
        $usersignupsession = totara_search_for_value($usersignups, 'sessionid', TOTARA_SEARCH_OP_EQUAL, $session->id);

        // Remove current sign-up for session from $usersignups.
        if (!empty($usersignupsession)) {
            $usersignupsession = reset($usersignupsession);
            unset($usersignups[$usersignupsession->uniqueid]);
        }

        // Loop through all user sessions except the current session $session.
        foreach ($usersignups as $user) {
            // If sessionid is null, nothing to do here.
            if ($user->sessionid === null) {
                continue;
            }

            facetoface_user_can_be_unallocated($user, $managerid);
            $ret->othersession[$user->id] = $user;
        }

        // If the user doesn't have a sign-up for this session check if we can put him in the potential list.
        // Otherwise, verify if the user can or cannot be unallocated.
        if (empty($usersignupsession)) {
            // Multiple sign-ups on OR user has not sign-ups for other sessions in the facetoface.
            $currentuser = reset($usersignups);
            if ($multiplesignups) {
                $ret->potential[$member] = $currentuser;
            } else if (array_key_exists($currentuser->id, $ret->othersession) === false) {
                $ret->potential[$member] = $currentuser;
            }
        } else {
            if (!facetoface_user_can_be_unallocated($usersignupsession, $managerid)) {
                $ret->cannotunallocate[$member] = $usersignupsession;
            }
            $ret->current[$member] = $usersignupsession;
        }
    }

    return $ret;
}

/**
 * Given a user, determine if he can be unallocated from the list.
 * If he/she cannot be unallocated, add the reason why.
 *
 * This function is used by facetoface_get_staff_to_allocate.
 *
 * @param object $user A user object that must contain id, bookedby and status code
 * @param int $managerid The user's manager ID.
 * @return bool True if the user can be unallocated, false otherwise.
 *
 * @deprecated since Totara 12.0
 */
function facetoface_user_can_be_unallocated(&$user, $managerid) {

    debugging('facetoface_user_can_be_unallocated() function has been deprecated, please use reservations::user_can_be_unallocated()',
        DEBUG_DEVELOPER);

    // Booked by someone else or self booking - cannot be unbooked.
    if ($user->bookedby != $managerid) {
        $user->cannotremove = ($user->bookedby == 0) ? 'selfbooked' : 'otherbookedby';
        return false;
    } else if ($user->statuscode && $user->statuscode > MDL_F2F_STATUS_BOOKED) {
        $user->cannotremove = 'attendancetaken'; // Attendance taken - cannot be unbooked.
        return false;
    }

    return true;
}

/**
 * Replace the manager reservations for this session with allocations for the given userids.
 * The list of userids still to be allocated will be returned.
 * Note: There are no checks made to see if the given users have already booked on a session, etc. -
 * it is assumed that any such checks have been completed before calling this function.
 *
 * @param object $session
 * @param object $facetoface
 * @param object $course
 * @param int $bookedby
 * @param int[] $userids
 * @throws moodle_exception
 * @return int[]
 *
 * @deprecated since Totara 12.0
 */
function facetoface_replace_reservations($session, $facetoface, $course, $bookedby, $userids) {
    global $DB, $CFG;

    debugging('facetoface_replace_reservations() function has been deprecated, please use reservations::replace()',
        DEBUG_DEVELOPER);

    $facetoface->approvalreqd = false; // Make sure they are directly signed-up.

    $sql = 'SELECT su.id, sus.statuscode, su.discountcode, su.notificationtype
              FROM {facetoface_signups} su
              JOIN {facetoface_signups_status} sus ON sus.signupid = su.id AND sus.superceded = 0
             WHERE su.sessionid = :sessionid AND su.userid = 0 AND su.bookedby = :bookedby
             ORDER BY sus.statuscode DESC, id DESC';
    // Prioritise allocating high-status reservations (booked) over lower-status reservations (waitinglist)
    $params = array('sessionid' => $session->id, 'bookedby' => $bookedby);
    $reservations = $DB->get_records_sql($sql, $params, 0, count($userids));

    foreach ($reservations as $reservation) {
        $userid = array_shift($userids);
        // Make sure that the user is enroled in the course
        $context = context_course::instance($course->id);
        if (!is_enrolled($context, $userid)) {
            $defaultlearnerrole = $DB->get_record('role', array('id' => $CFG->learnerroleid));
            if (!enrol_try_internal_enrol($course->id, $userid, $defaultlearnerrole->id, time())) {
                throw new moodle_exception('unabletoenrol', 'mod_facetoface');
            }
        }

        if ($oldbooking = $DB->get_record('facetoface_signups', array('sessionid' => $session->id, 'userid' => $userid))) {
            // This could happen if a user booked themselves, then cancelled and are now being allocated by their manager.

            // Delete the reservation completely.
            $DB->delete_records('facetoface_signups_status', array('signupid' => $reservation->id));
            $DB->delete_records('facetoface_signups', array('id' => $reservation->id));

            // Update the bookedby field.
            $DB->set_field('facetoface_signups', 'bookedby', $bookedby, array('id' => $oldbooking->id));

        } else {
            // Switch the booking over to the given user.
            $upd = (object)array(
                'id' => $reservation->id,
                'userid' => $userid,
                'sessionid' => $session->id,
            );
            $DB->update_record('facetoface_signups', $upd);
        }

        // Make sure the status is set and the correct notification messages are sent.
        facetoface_user_signup($session, $facetoface, $course, $reservation->discountcode, $reservation->notificationtype,
            $reservation->statuscode, $userid);
    }

    return $userids;
}

/**
 * Allocate spaces to all the users specified.
 * Note: there are no checks done against the user's allocation limit.
 *
 * @param object $session
 * @param object $facetoface
 * @param object $course
 * @param int $bookedby
 * @param int[] $userids
 * @param int $capacityleft how much (non-waitlist) space there is left on the session
 * @throws moodle_exception
 *
 * @deprecated since Totara 12.0
 */
function facetoface_allocate_spaces($session, $facetoface, $course, $bookedby, $userids, $capacityleft) {
    global $DB, $CFG;

    debugging('facetoface_allocate_spaces() function has been deprecated, please use reservations::allocate_spaces()',
        DEBUG_DEVELOPER);

    $facetoface->approvaltype = APPROVAL_NONE; // Make sure they are directly signed-up.

    foreach ($userids as $userid) {
        // Make sure that the user is enroled in the course
        $context = context_course::instance($course->id);
        if (!is_enrolled($context, $userid)) {
            $defaultlearnerrole = $DB->get_record('role', array('id' => $CFG->learnerroleid));
            if (!enrol_try_internal_enrol($course->id, $userid, $defaultlearnerrole->id, time())) {
                throw new moodle_exception('unabletoenrol', 'mod_facetoface');
            }
        }

        $status = MDL_F2F_STATUS_BOOKED;
        if ($capacityleft <= 0) {
            $status = MDL_F2F_STATUS_WAITLISTED;
        }

        // Make sure the status is set and the correct notification messages are sent.
        if (facetoface_user_signup($session, $facetoface, $course, null, MDL_F2F_BOTH, $status, $userid)) {
            $DB->set_field('facetoface_signups', 'bookedby', $bookedby, array('sessionid' => $session->id, 'userid' => $userid));
        }
        $capacityleft--;
    }
}

/**
 * Remove the given allocations and, optionally, convert them back into reservations.
 *
 * @param object $session
 * @param object $facetoface
 * @param object $course
 * @param int[] $userids
 * @param bool $converttoreservations if true, convert allocations to reservations, if false, just cancel
 * @param int $managerid optional defaults to current user
 *
 * @deprecated since Totara 12.0
 */
function facetoface_remove_allocations($session, $facetoface, $course, $userids, $converttoreservations, $managerid = null) {
    global $DB, $USER;

    debugging('facetoface_remove_allocations() function has been deprecated, please use reservations::remove_allocations()',
        DEBUG_DEVELOPER);

    if (!$managerid) {
        $managerid = $USER->id;
    }

    foreach ($userids as $userid) {
        $transaction = $DB->start_delegated_transaction();
        $userisinwaitlist = facetoface_is_user_on_waitlist($session, $userid);

        facetoface_user_cancel($session, $userid);

        if ($converttoreservations) {
            // Add one reservation.
            $book = 1;
            $waitlist = 0;
            if ($userisinwaitlist) {
                $book = 0;
                $waitlist = 1;
            }
            facetoface_add_reservations($session, $managerid, $book, $waitlist);
        }
        $transaction->allow_commit();

        // Send notification.
        if (!empty($session->sessiondates) && $userisinwaitlist === false) {
            facetoface_send_cancellation_notice($facetoface, $session, $userid);
        }
    }
}

/**
 * Count how many spaces the current user has reserved in the given face to face instance.
 * @param object $facetoface
 * @param int $managerid
 * @return array 'all' => total count; sessionid => session count
 *
 * @deprecated since Totara 12.0
 */
function facetoface_count_reservations($facetoface, $managerid) {
    global $DB;
    static $reservations = array();

    debugging('facetoface_count_reservations function has been deprecated, please use reservations::count()',
        DEBUG_DEVELOPER);

    if (!isset($reservations[$facetoface->id])) {
        $sql = 'SELECT s.id, COUNT(*) AS reservecount
                  FROM {facetoface_sessions} s
                  JOIN {facetoface_signups} su ON su.sessionid = s.id
                 WHERE s.facetoface = :facetofaceid AND su.bookedby = :userid AND su.userid = 0
                 GROUP BY s.id';
        $params = array('facetofaceid' => $facetoface->id, 'userid' => $managerid);
        $reservations[$facetoface->id] = $DB->get_records_sql_menu($sql, $params);
        $reservations[$facetoface->id]['all'] = array_sum($reservations[$facetoface->id]);
    }

    return $reservations[$facetoface->id];
}

/**
 * Count how many allocations the current user has made in the given face to face instance.
 * @param object $facetoface
 * @param int $managerid
 * @return array 'all' => total count; sessionid => session count
 *
 * @deprecated since Totara 12.0
 */
function facetoface_count_allocations($facetoface, $managerid) {
    global $DB;
    static $allocations = array();

    debugging('facetoface_count_allocations() function has been deprecated, please use reservations::count_allocations()',
        DEBUG_DEVELOPER);

    if (!isset($allocations[$facetoface->id])) {
        $sql = 'SELECT s.id, COUNT(*) AS allocatecount
                  FROM {facetoface_sessions} s
                  JOIN {facetoface_signups} su ON su.sessionid = s.id
                  JOIN {facetoface_signups_status} sus ON sus.signupid = su.id AND sus.superceded = 0
                                                       AND sus.statuscode > :cancelled
                 WHERE s.facetoface = :facetofaceid AND su.bookedby = :userid AND su.userid <> 0
                 GROUP BY s.id';
        $params = array('facetofaceid' => $facetoface->id, 'userid' => $managerid, 'cancelled' => MDL_F2F_STATUS_USER_CANCELLED);
        $allocations[$facetoface->id] = $DB->get_records_sql_menu($sql, $params);
        $allocations[$facetoface->id]['all'] = array_sum($allocations[$facetoface->id]);
    }

    return $allocations[$facetoface->id];
}

/**
 * Find any reservations that are too close to the start of the session and delete them.
 *
 * @deprecated since Totara 12.0
 */
function facetoface_remove_reservations_after_deadline($testing) {
    global $DB;
    debugging('facetoface_remove_reservations_after_deadline() function has been deprecated, please use reservations::remove_after_deadline()',
        DEBUG_DEVELOPER);

    $sql = "SELECT DISTINCT su.id, s.id AS sessionid, f.id AS facetofaceid, su.bookedby
                  FROM {facetoface} f
                  JOIN {facetoface_sessions} s ON s.facetoface = f.id
                  JOIN {facetoface_sessions_dates} sd ON sd.sessionid = s.id
                  JOIN {facetoface_signups} su ON su.sessionid = s.id AND su.userid = 0
                  JOIN {facetoface_signups_status} sus ON sus.signupid = su.id AND sus.superceded = 0
                 WHERE f.reservecanceldays > 0 AND sd.timestart < (:timenow + (f.reservecanceldays * :daysecs))";
    $params = array('timenow' => time(), 'daysecs' => DAYSECS);
    $signups = $DB->get_recordset_sql($sql, $params);

    if ($signups) {
        $tonotify = array();
        $signupids = array();
        if (!$testing) {
            mtrace('Removing unconfirmed face to face reservations for sessions that will be starting soon');
        }
        foreach ($signups as $signup) {
            if (!$testing) {
                mtrace("- signupid: {$signup->id}, sessionid: {$signup->sessionid}, facetofaceid: {$signup->facetofaceid}");
            }
            if (!isset($tonotify[$signup->facetofaceid])) {
                $tonotify[$signup->facetofaceid] = array();
            }
            if (!isset($tonotify[$signup->facetofaceid][$signup->sessionid])) {
                $tonotify[$signup->facetofaceid][$signup->sessionid] = array();
            }
            $tonotify[$signup->facetofaceid][$signup->sessionid][$signup->bookedby] = $signup->bookedby;
            $signupids[] = $signup->id;
        }
        $signups->close();
        $DB->delete_records_list('facetoface_signups_status', 'signupid', $signupids);
        $DB->delete_records_list('facetoface_signups', 'id', $signupids);

        // Send notifications if enabled.
        $notificationdisable = get_config(null, 'facetoface_notificationdisable');
        if (empty($notificationdisable)) {
            $notifyparams = array(
                'type' => MDL_F2F_NOTIFICATION_AUTO,
                'conditiontype' => MDL_F2F_CONDITION_RESERVATION_ALL_CANCELLED,
            );
            foreach ($tonotify as $facetofaceid => $sessions) {
                $facetoface = $DB->get_record('facetoface', array('id' => $facetofaceid));
                $notifyparams['facetofaceid'] = $facetoface->id;
                foreach ($sessions as $sessionid => $managers) {
                    $session = facetoface_get_session($sessionid);
                    foreach ($managers as $managerid) {
                        facetoface_send_notice($facetoface, $session, $managerid, $notifyparams);
                    }
                }
            }
        }
    }
}

/**
 * Output for a removable approver in the facetoface mod_form.
 *
 * @param int       $user           The user object for the approver being displayed
 * @param boolean   $activity       Whether the approver is activity level or site level
 * @return string                   The html output for the approver
 *
 * @deprecated since Totara 12.0
 */
function facetoface_display_approver($user, $activity = false) {
    global $PAGE;

    debugging('facetoface_display_approver() function has been deprecated, please use renderer::display_approver()',
        DEBUG_DEVELOPER);

    $renderer = $PAGE->get_renderer('mod_facetoface');

    $uniqueid = "facetoface_approver_{$user->id}";
    if ($activity) {
        $classname = 'activity_approver';
        $delete = $renderer->action_icon('', new pix_icon('/t/delete', get_string('remove')), null,
            array('class' => 'activity_approver_del', 'id' => $user->id));
        $content = get_string('approval_activityapprover', 'mod_facetoface', fullname($user)) . ' ' . $delete;
    } else {
        $classname = 'system_approver';
        $content = get_string('approval_siteapprover', 'mod_facetoface', fullname($user));
    }

    return html_writer::tag('div', $content, array('id' => $uniqueid, 'class' => $classname));
}

/**
 * Check if the user has any signups that don't have any of the following
 *     not being archived
 *     cancelled by user
 *     declined
 *     session cancelled
 *     status not set
 * @param int $facetofaceid
 * @param int $userid
 * @return bool
 *
 * @deprecated since Totara 12.0
 */
function facetoface_has_unarchived_signups($facetofaceid, $userid) {
    global $DB;

    debugging('facetoface_has_unarchived_signups() function has been deprecated, please use seminar::has_unarchived_signups()',
        DEBUG_DEVELOPER);

    $sql  = "SELECT 1 FROM {facetoface_signups} ftf_sign
               JOIN {facetoface_sessions} sess
                    ON sess.facetoface = :facetofaceid
               JOIN {facetoface_signups_status} sign_stat
                    ON sign_stat.signupid = ftf_sign.id
                    AND sign_stat.superceded <> 1
              WHERE ftf_sign.userid = :userid
                AND ftf_sign.sessionid = sess.id
                AND ftf_sign.archived <> 1
                AND sign_stat.statuscode > :statusdeclined
                AND sign_stat.statuscode <> :statusnotset";

    $params = array('facetofaceid' => $facetofaceid,
        'userid' => $userid,
        'statusdeclined' => MDL_F2F_STATUS_DECLINED,
        'statusnotset' => MDL_F2F_STATUS_NOT_SET);

    // Check if user is already signed up to a session in the facetoface and it has not been archived.
    return $DB->record_exists_sql($sql, $params);
}

/**
 * Print the details of a session for calendar
 *
 * @param object $event         Record from calendar event
 * @return string|null html markup when return is true
 *
 * @deprecated since Totara 12.0
 */
function facetoface_print_calendar_session($event) {
    global $DB, $CFG, $USER;

    debugging('facetoface_print_calendar_session() function has been deprecated, please use core_calendar_renderer::facetoface_print_calendar_session()',
        DEBUG_DEVELOPER);

    $session = facetoface_get_session($event->uuid);
    $facetoface = $DB->get_record('facetoface', array('id' => $session->facetoface));

    if (empty($facetoface->showoncalendar) && empty($facetoface->usercalentry)) {
        return '';
    }

    $output = facetoface_print_session($session, false, true);
    $users = facetoface_get_attendees($session->id);

    if ($facetoface->usercalentry && array_key_exists($USER->id, $users)) {
        // Better way is to get an user status and display it.
        $linkurl = $CFG->wwwroot . "/mod/facetoface/signup.php?s=$session->id";
        $output .= get_string("calendareventdescriptionbooking", 'facetoface', $linkurl);
    } else  if ($facetoface->showoncalendar == F2F_CAL_SITE || $facetoface->showoncalendar == F2F_CAL_COURSE) {
        // If the user has not signed up before.
        if (!facetoface_has_unarchived_signups($session->facetoface, $USER->id)
            || $facetoface->multiplesessions == '1') {
            $linkurl = new moodle_url('/mod/facetoface/signup.php', array('s' => $session->id));
            $linktext = get_string('signupforthissession', 'facetoface');
            $output .= html_writer::link($linkurl, $linktext);
        }
    } else {
        $output = '';
    }

    return $output;
}

/**
 * Delete grade item for given facetoface
 *
 * @param object $facetoface object
 * @return object facetoface
 *
 * @deprecated since Totara 12.0
 */
function facetoface_grade_item_delete($facetoface) {

    debugging('facetoface_grade_item_delete() function has been deprecated, please use seminar::grade_item_delete()',
        DEBUG_DEVELOPER);

    $retcode = grade_update('mod/facetoface', $facetoface->course, 'mod', 'facetoface',
        $facetoface->id, 0, NULL, array('deleted' => 1));
    return ($retcode === GRADE_UPDATE_OK);
}

/**
 * Create a new entry in the facetoface_sessions table
 *
 * @deprecated since Totara 12.0
 */
function facetoface_add_session($session, $sessiondates) {
    global $DB;

    debugging('facetoface_add_session() function has been deprecated, please use seminar_event::save()',
        DEBUG_DEVELOPER);

    $session->timemodified = $session->timecreated = time();
    $session = facetoface_cleanup_session_data($session);

    $session->id = $DB->insert_record('facetoface_sessions', $session);

    facetoface_save_dates($session->id, $sessiondates);

    return $session->id;
}

/**
 * Modify an entry in the facetoface_sessions table
 *
 * @deprecated since Totara 12.0
 */
function facetoface_update_session($session, $sessiondates) {
    global $DB;

    debugging('facetoface_update_session() function has been deprecated, please use seminar_event::save()',
        DEBUG_DEVELOPER);

    $session->timemodified = time();
    $session = facetoface_cleanup_session_data($session);

    $DB->update_record('facetoface_sessions', $session);
    facetoface_save_dates($session, $sessiondates);

    return $session->id;
}

/**
 * Prepare the user data to go into the database.
 *
 * @deprecated since Totara 12.0
 */
function facetoface_cleanup_session_data($session) {

    debugging('facetoface_cleanup_session_data() function has been deprecated, seminar_event::cleanup_capacity()',
        DEBUG_DEVELOPER);

    // Only numbers allowed here
    $session->capacity = preg_replace('/[^\d]/', '', $session->capacity);
    $MAX_CAPACITY = 100000;
    if ($session->capacity < 1) {
        $session->capacity = 1;
    }
    elseif ($session->capacity > $MAX_CAPACITY) {
        $session->capacity = $MAX_CAPACITY;
    }

    return $session;
}

/**
 * Print the list of a sessions
 *
 * @param integer $courseid
 * @param object $facetoface
 * @param object $sessions
 *
 * @return string html sting
 *
 * @deprecated since Totara 12.0
 */
function facetoface_print_session_list($courseid, $facetoface, $sessions) {
    global $USER, $OUTPUT, $PAGE;

    debugging('facetoface_print_session_list() function has been deprecated, please use renderer::print_session_list()',
        DEBUG_DEVELOPER);

    $timenow = time();
    $output = '';

    $cm = get_coursemodule_from_instance('facetoface', $facetoface->id, $courseid, false, MUST_EXIST);
    $context = context_module::instance($cm->id);

    /** @var mod_facetoface_renderer $f2f_renderer */
    $f2f_renderer = $PAGE->get_renderer('mod_facetoface');
    $f2f_renderer->setcontext($context);

    $viewattendees = has_capability('mod/facetoface:viewattendees', $context);
    $editevents = has_capability('mod/facetoface:editevents', $context);

    $bookedsession = null;
    $submissions = facetoface_get_user_submissions($facetoface->id, $USER->id);
    if (!$facetoface->multiplesessions) {
        $submission = array_shift($submissions);
        $bookedsession = $submission;
    }

    $upcomingarray = array();
    $previousarray = array();

    if ($sessions) {
        foreach ($sessions as $session) {
            $sessiondata = $session;
            if ($facetoface->multiplesessions) {
                $submission = facetoface_get_user_submissions($facetoface->id, $USER->id,
                    MDL_F2F_STATUS_REQUESTED, MDL_F2F_STATUS_FULLY_ATTENDED, $session->id);
                $bookedsession = array_shift($submission);
            }
            $sessiondata->bookedsession = $bookedsession;

            // Is session waitlisted
            if (!$session->cntdates ) {
                $upcomingarray[] = $sessiondata;
            } else {
                // Only sessions that are over should go to the previous session section.
                if (facetoface_is_session_over($session, $timenow)) {
                    $previousarray[] = $sessiondata;
                } else {
                    // Session is in progress or has not yet started.
                    // Normal scheduled session.
                    $upcomingarray[] = $sessiondata;
                }
            }
        }
    }

    $displaytimezones = get_config(null, 'facetoface_displaysessiontimezones');

    if ($editevents) {
        $output .= html_writer::link(
            new moodle_url('events/add.php', array('f' => $facetoface->id, 'backtoallsessions' => 1)), get_string('addsession', 'facetoface'),
            array('class' => 'btn btn-default')
        );
    }

    // Upcoming sessions
    $output .= $OUTPUT->heading(get_string('upcomingsessions', 'facetoface'), 3);
    if (empty($upcomingarray)) {
        print_string('noupcoming', 'facetoface');
    } else {
        $reserveinfo = array();
        if (!empty($facetoface->managerreserve)) {
            // Include information about reservations when drawing the list of sessions.
            $reserveinfo = facetoface_can_reserve_or_allocate($facetoface, $sessions, $context);
            $output .= html_writer::tag('p', get_string('lastreservation', 'mod_facetoface', $facetoface));
        }

        $sessionlist = $f2f_renderer->print_session_list_table(
            $upcomingarray, $viewattendees, $editevents, $displaytimezones, $reserveinfo, $PAGE->url
        );
        $output .= html_writer::div($sessionlist, 'upcomingsessionlist');
    }

    // Previous sessions
    if (!empty($previousarray)) {
        $output .= $OUTPUT->heading(get_string('previoussessions', 'facetoface'), 3);
        $sessionlist = $f2f_renderer->print_session_list_table(
            $previousarray, $viewattendees, $editevents, $displaytimezones, [], $PAGE->url
        );
        $output .= html_writer::div($sessionlist, 'previoussessionlist');
    }

    return $output;
}

/**
 * Delete signups and related data.
 *
 * @param array $signupids
 *
 * @deprecated since Totara 12.0
 */
function facetoface_delete_signups(array $signupids) {
    global $DB;

    debugging('facetoface_delete_signups() function has been deprecated, signup::delete()',
        DEBUG_DEVELOPER);

    if (empty($signupids)) {
        return;
    }

    list($signupsqlin, $signupinparams) = $DB->get_in_or_equal($signupids);

    // Get all associated signup customfield data to delete.
    $signupinfoids = $DB->get_fieldset_select(
        'facetoface_signup_info_data',
        'id',
        "facetofacesignupid {$signupsqlin}",
        $signupinparams
    );

    if (!empty($signupinfoids)) {
        list($sqlin, $inparams) = $DB->get_in_or_equal($signupinfoids);
        $DB->delete_records_select('facetoface_signup_info_data_param', "dataid {$sqlin}", $inparams);
        $DB->delete_records_select('facetoface_signup_info_data', "id {$sqlin}", $inparams);
    }

    // Get all associated cancellation customfield data to delete.
    $cancellationids = $DB->get_fieldset_select(
        'facetoface_cancellation_info_data',
        'id',
        "facetofacecancellationid {$signupsqlin}",
        $signupinparams
    );

    if (!empty($cancellationids)) {
        list($sqlin, $inparams) = $DB->get_in_or_equal($cancellationids);
        $DB->delete_records_select('facetoface_cancellation_info_data_param', "dataid {$sqlin}", $inparams);
        $DB->delete_records_select('facetoface_cancellation_info_data', "id {$sqlin}", $inparams);
    }

    $DB->delete_records_select('facetoface_signups_status', "signupid {$signupsqlin}", $signupinparams);
    $DB->delete_records_select('facetoface_signups', "id {$signupsqlin}", $signupinparams);
}

/**
 * Delete all signups and related data for given session id.
 *
 * @param int $sessionid
 *
 * @deprecated since Totara 12.0
 */
function facetoface_delete_signups_for_session(int $sessionid) {
    global $DB;

    debugging('facetoface_delete_signups_for_session() function has been deprecated, please use signup_list::delete()',
        DEBUG_DEVELOPER);

    $signupids = $DB->get_fieldset_select(
        'facetoface_signups',
        'id',
        'sessionid = :sessionid',
        ['sessionid' => $sessionid]
    );
    facetoface_delete_signups($signupids);
}

/**
 * Import user and signup to session
 *
 * @access  public
 * @param   object  $course             Record from the course table
 * @param   object  $facetoface         Record from the facetoface table
 * @param   object  $session            Session to signup user to
 * @param   mixed   $userid             User to signup (normally int)
 * @param   array   $params             Optional suppressemail, ignoreconflicts, bulkaddsource, discountcode, notificationtype, autoenrol
 *          boolean $suppressemail      Suppress notifications flag
 *          boolean $ignoreconflicts    Ignore booking conflicts flag
 *          string  $bulkaddsource      Flag to indicate if $userid is actually another field
 *          string  $discountcode       Optional A user may specify a discount code
 *          integer $notificationtype   Optional A user may choose the type of notifications they will receive
 *          boolean $autoenrol          Optional If user not enrolled on the course then enrols them manually (default true)
 * @return  array
 * @deprecated since Totara 12.0
 */
function facetoface_user_import($course, $facetoface, $session, $userid, $params = array()) {
    global $DB, $CFG, $USER;

    debugging('facetoface_user_import() function has been deprecated, please use signup_helper::signup()', DEBUG_DEVELOPER);

    $result = array();
    $result['id'] = $userid;

    $suppressemail    = (isset($params['suppressemail'])    ? $params['suppressemail']    : false);
    $ignoreconflicts  = (isset($params['ignoreconflicts'])  ? $params['ignoreconflicts']  : false);
    $bulkaddsource    = (isset($params['bulkaddsource'])    ? $params['bulkaddsource']    : 'bulkaddsourceuserid');
    $discountcode     = (isset($params['discountcode'])     ? $params['discountcode']     : '');
    $notificationtype = (isset($params['notificationtype']) ? $params['notificationtype'] : MDL_F2F_BOTH);
    $autoenrol        = (isset($params['autoenrol'])        ? $params['autoenrol']        : true);

    if (isset($params['approvalreqd'])) {
        // Overwrite default behaviour as bulkadd_* is requested
        $facetoface->approvaltype = $params['approvalreqd'];
    }
    // Comes from "Suppress notifications to manager about added and removed attendees" as 0 value.
    if (isset($params['ccmanager'])) {
        $facetoface->ccmanager = $params['ccmanager'];
    } else {
        // Do not set any value here, value is set in facetoface_notification->ccmanager table.
    }

    // Check parameters.
    if ($bulkaddsource == 'bulkaddsourceuserid') {
        if (!is_int($userid) && !ctype_digit($userid)) {
            $result['name'] = '';
            $result['result'] = get_string('error:userimportuseridnotanint', 'facetoface', $userid);
            return $result;
        }
    }

    // Get user.
    switch ($bulkaddsource) {
        case 'bulkaddsourceuserid':
            $user = $DB->get_record('user', array('id' => $userid));
            break;
        case 'bulkaddsourceidnumber':
            $user = $DB->get_record('user', array('idnumber' => $userid));
            break;
        case 'bulkaddsourceusername':
            $user = $DB->get_record('user', array('username' => $userid));
            break;
    }
    if (!$user) {
        $result['name'] = '';
        $a = array('fieldname' => get_string($bulkaddsource, 'facetoface'), 'value' => $userid);
        $result['result'] = get_string('userdoesnotexist', 'facetoface', $a);
        return $result;
    }

    $result['name'] = fullname($user);

    if (isguestuser($user)) {
        $a = array('fieldname' => get_string($bulkaddsource, 'facetoface'), 'value' => $userid);
        $result['result'] = get_string('cannotsignupguest', 'facetoface', $a);
        return $result;
    }

    // Make sure that the user is enroled in the course.
    $cm = get_coursemodule_from_instance('facetoface', $facetoface->id, $course->id, false, MUST_EXIST);
    $context = context_module::instance($cm->id);
    if (!is_enrolled($context, $user) && $autoenrol) {

        $defaultlearnerrole = $DB->get_record('role', array('id' => $CFG->learnerroleid));

        if (!enrol_try_internal_enrol($course->id, $user->id, $defaultlearnerrole->id, time())) {
            $result['result'] = get_string('error:enrolmentfailed', 'facetoface', fullname($user));
            return $result;
        }
    }

    $facetoface_allowwaitlisteveryone = get_config(null, 'facetoface_allowwaitlisteveryone');
    if ($session->waitlisteveryone && !empty($facetoface_allowwaitlisteveryone)) {
        $status = MDL_F2F_STATUS_WAITLISTED;
    } else if (!facetoface_session_has_capacity($session, $context)) {
        if ($session->allowoverbook) {
            $status = MDL_F2F_STATUS_WAITLISTED;
        }
    }

    // Check if we are waitlisting or booking
    if ($session->mintimestart) {
        if (!isset($status)) {
            $status = MDL_F2F_STATUS_BOOKED;
        }
    } else {
        $status = MDL_F2F_STATUS_WAITLISTED;
    }

    $jobassignment = null;

    $selectjobassignmentonsignupglobal = get_config(null, 'facetoface_selectjobassignmentonsignupglobal');
    if (!empty($selectjobassignmentonsignupglobal) && !empty($facetoface->selectjobassignmentonsignup)) {

        if (!empty($params['jobassignmentid'])) {
            $jobassignment = \totara_job\job_assignment::get_with_id($params['jobassignmentid']);
            if ($jobassignment->userid != $userid) {
                // There was an error!
            }
        } else {
            $jobassignment = \totara_job\job_assignment::get_first($userid, false);
        }

        // If we still don't have a job assignment and it's mandated then error.
        if (!$jobassignment && !empty($facetoface->forceselectjobassignment)) {
            $result['result'] = get_string('error:nojobassignmentselected', 'facetoface');
            $result['nogoodpos'] = true;
            return $result;
        }
    }

    $managerselect = get_config(null, 'facetoface_managerselect');
    $managerid = null;
    if ($managerselect && isset($params['managerselect'])) {
        $managerid = $params['managerselect'];
    }

    // Do general validation checks for user import to a session.
    $error = facetoface_validate_user_import($user, $context, $facetoface, $session, $ignoreconflicts);
    if (!empty($error)) {
        return $error;
    }

    // Finally attempt to enrol
    if (!facetoface_user_signup(
        $session,
        $facetoface,
        $course,
        $discountcode,
        $notificationtype,
        $status,
        $user->id,
        !$suppressemail,
        null,
        $jobassignment,
        $managerid)) {
        $result['result'] = get_string('error:addattendee', 'facetoface', fullname($user));
        return $result;
    }

    $result['result'] = true;
    return $result;
}

/**
 * Add a record to the facetoface submissions table and sends out an
 * email confirmation
 *
 * @param object $session record from the facetoface_sessions table
 * @param object $facetoface record from the facetoface table
 * @param object $course record from the course table
 * @param string $discountcode code entered by the user
 * @param integer $notificationtype type of notifications to send to user
 * @see {{MDL_F2F_INVITE}}
 * @param integer $statuscode Status code to set
 * @param integer|bool $userid user to signup
 * @param bool $notifyuser whether or not to send an email confirmation
 * @param object $fromuser User object describing who the email is from.
 * @param \totara_job\job_assignment $jobassignment object containing the selected job assignment
 * @param int $managerid Manager id selected by user
 * @return bool
 * @deprecated since Totara 12.0
 */
function facetoface_user_signup($session, $facetoface, $course, $discountcode,
                                $notificationtype, $statuscode, $userid = false,
                                $notifyuser = true, $fromuser = null, $jobassignment = null, $managerid = null) {

    global $DB, $USER;

    debugging('facetoface_user_signup() function has been deprecated, please use signup_helper::signup()', DEBUG_DEVELOPER);

    // Get user id
    if (!$userid) {
        $userid = $USER->id;
    }

    $timenow = time();

    // Check to see if a signup already exists
    if ($existingsignup = $DB->get_record('facetoface_signups', array('sessionid' => $session->id, 'userid' => $userid))) {
        $usersignup = $existingsignup;
    } else {
        // Otherwise, prepare a signup object
        $usersignup = new stdClass();
        $usersignup->sessionid = $session->id;
        $usersignup->userid = $userid;
    }

    $usersignup->bookedby = $userid == $USER->id ? 0 : $USER->id;
    $usersignup->mailedreminder = 0;
    $usersignup->notificationtype = $notificationtype;

    // If the selected job assignment information hasn't been supplied then we need to try to default it
    // we won't throw errors if it's not present as all we can do is throw exceptions that won't be handled and may break cron
    // in theory the only routes here that don't go through facetoface_user_import handle reservations which handled by a manager
    // or come from a wait list and so a job assignment should always be available.
    $selectjobassignmentonsignupglobal = get_config(null, 'facetoface_selectjobassignmentonsignupglobal');
    $jobassignmentrequired = !empty($selectjobassignmentonsignupglobal) && !empty($facetoface->selectjobassignmentonsignup);

    if ($jobassignmentrequired) {
        if ($jobassignment === null) {
            $jobassignment = \totara_job\job_assignment::get_first($userid, false);
        }

        if (!empty($jobassignment)) {
            $usersignup->jobassignmentid = $jobassignment->id;
        }
    }

    // If no job assignment is wanted by the face to face or none is provided then record all info as null.
    if (!$jobassignmentrequired || empty($jobassignment)) {
        $usersignup->jobassignmentid = null;
    }

    $managerselect = get_config(null, 'facetoface_managerselect');
    if ($managerselect && !empty($managerid)) {
        $usersignup->managerid = $managerid;
    }

    $usersignup->discountcode = trim(strtoupper($discountcode));
    if (empty($usersignup->discountcode)) {
        $usersignup->discountcode = null;
    }

    // Update/insert the signup record
    if (!empty($usersignup->id)) {
        $success = $DB->update_record('facetoface_signups', $usersignup);
    } else {
        $usersignup->id = $DB->insert_record('facetoface_signups', $usersignup);
        $success = (bool)$usersignup->id;
    }

    if (!$success) {
        print_error('error:couldnotupdatef2frecord', 'facetoface');
    }

    // Work out which status to use

    // If approval not required or self approval enabled.
    if (!facetoface_approval_required($facetoface)) {
        $new_status = $statuscode;
    } else {
        // Manager approval is required.
        // Before we can accept the new status we need to check if the user must first be approved by there manager,
        // Get current status (if any) so that we can decide what to do.
        $currentstatus =  $DB->get_field('facetoface_signups_status', 'statuscode', array('signupid' => $usersignup->id, 'superceded' => 0));
        if (empty($currentstatus)) {
            // If they don't already have a status then they must require approval.
            $currentstatus = MDL_F2F_STATUS_REQUESTED;
        }
        // The following statuses have already received approval.
        $alreadyapproved = array(
            MDL_F2F_STATUS_WAITLISTED, // The user is waitlisted - they must have been approved already.
            MDL_F2F_STATUS_BOOKED // The user is booked - they must have been approved already.
        );
        // The following statuses still need to seek approval.
        $mustapprove = array(
            MDL_F2F_STATUS_REQUESTED, // They are currently waiting for approval already.
            MDL_F2F_STATUS_USER_CANCELLED, // They cancelled and are coming back again - seek approval once more.
            MDL_F2F_STATUS_SESSION_CANCELLED, // They session was cancelled but we are back? seek approval to be sure.
            MDL_F2F_STATUS_DECLINED, // Persistent learner, won't take no for an answer
        );
        if (in_array($currentstatus, $alreadyapproved)) {
            // The user is an already approved state - no need to seek there approval again.
            // We will use the given status.
            $new_status = $statuscode;
        } else if (in_array($currentstatus, $mustapprove)) {
            // The user is not in an approved state, in which case they must be sent through for approval.
            $new_status = MDL_F2F_STATUS_REQUESTED;
        } else {
            // Hmm this is a little worrying I wonder what they are doing.
            // As we don't know let us throw a debugging notice and take a safe path.
            // This may lead to notifications.
            debugging('Unexpected status encountered when updated users facetoface signup', DEBUG_DEVELOPER);
            $new_status = MDL_F2F_STATUS_REQUESTED;
        }
    }

    // Update status.
    if (!facetoface_update_signup_status($usersignup->id, $new_status, $USER->id)) {
        print_error('error:f2ffailedupdatestatus', 'facetoface');
    }

    // Add to user calendar -- if facetoface usercalentry is set to true
    if ($facetoface->usercalentry && in_array($new_status, array(MDL_F2F_STATUS_BOOKED, MDL_F2F_STATUS_WAITLISTED))) {
        facetoface_add_session_to_calendar($session, $facetoface, 'user', $userid, 'booking');
    }

    // If session has already started, do not send a notification
    if (facetoface_has_session_started($session, $timenow)) {
        $notifyuser = false;
    }

    // Send notification.
    $notifytype = ((int)$notificationtype == MDL_F2F_NONE ? false : true);
    $session->notifyuser = $notifyuser && $notifytype;

    switch ($new_status) {
        case MDL_F2F_STATUS_BOOKED:
            $error = facetoface_send_confirmation_notice($facetoface, $session, $userid, $notificationtype, false, $fromuser);
            break;

        case MDL_F2F_STATUS_WAITLISTED:
            $error = facetoface_send_confirmation_notice($facetoface, $session, $userid, $notificationtype, true);
            break;

        case MDL_F2F_STATUS_REQUESTED:
            if ($facetoface->approvaltype == APPROVAL_ROLE) {
                // Send the booking requested message to the user.
                $error = facetoface_send_rolerequest_notice($facetoface, $session, $userid);
            } else if ($facetoface->approvaltype == APPROVAL_ADMIN) {
                // Send the booking requested message to the user.
                $error = facetoface_send_adminrequest_notice($facetoface, $session, $userid);
            } else {
                $error = facetoface_send_request_notice($facetoface, $session, $userid);
            }
            break;
    }

    if (!empty($error)) {
        if ($error == 'userdoesnotexist') {
            print_error($error, 'facetoface');
        } else {
            // Don't fail if email isn't sent, just display a warning
            debugging(get_string($error, 'facetoface'), DEBUG_NORMAL);
        }
    }

    if ($session->notifyuser) {
        if (!$DB->update_record('facetoface_signups', $usersignup)) {
            print_error('error:couldnotupdatef2frecord', 'facetoface');
        }
    }

    // Update course completion.
    if (in_array($new_status, array(MDL_F2F_STATUS_BOOKED, MDL_F2F_STATUS_WAITLISTED))) {

        $completion = new completion_info($course);
        if ($completion->is_enabled()) {

            $ccdetails = array(
                'course' => $course->id,
                'userid' => $userid,
            );

            $cc = new completion_completion($ccdetails);
            $cc->mark_inprogress($timenow);
        }
    }

    facetoface_withdraw_interest($facetoface, $userid);

    // Add log entry.
    $cm = get_coursemodule_from_instance('facetoface', $facetoface->id, $course->id);
    $context = context_module::instance($cm->id);
    \mod_facetoface\event\session_signup::create_from_instance($usersignup, $context)->trigger();

    return true;
}

/**
 * Determines whether an activity requires the user to have a manager (either for
 * manager approval or to send notices to the manager)
 *
 * @param  object $facetoface A database fieldset object for the facetoface activity
 * @return boolean whether a person needs a manager to sign up for that activity
 * @deprecated since Totara 12.0
 */
function facetoface_manager_needed($facetoface){
    debugging('Function facetoface_manager_needed() is deprecated. Use seminar::is_manager_required().', DEBUG_DEVELOPER);
    return $facetoface->approvaltype == APPROVAL_MANAGER || $facetoface->approvaltype == APPROVAL_ADMIN;
}

/**
 * Check if user can signup to current event.
 * @param stdClass $session object (must be prepared by see::facetoface_get_session())
 * @param int $userid user id
 * @param int $time time of check (by default now)
 * @deprecated Since Totara 12.0
 */
function facetoface_can_user_signup($session, $userid, $time = 0) {
    debugging('Function facetoface_can_user_signup() is deprecated. Use signup::can_signup().', DEBUG_DEVELOPER);

    if (!$time) {
        $time = time();
    }
    if (!empty($session->cancelledstatus)) {
        return false;
    }

    if (facetoface_has_session_started($session, $time)) {
        return false;
    }

    $submission = facetoface_get_user_submissions($session->facetoface, $userid,
        MDL_F2F_STATUS_REQUESTED, MDL_F2F_STATUS_FULLY_ATTENDED, $session->id);
    if (!empty($submission)) {
        return false;
    }

    if (!$cm = get_coursemodule_from_instance('facetoface', $session->facetoface)) {
        print_error('error:incorrectcoursemodule', 'facetoface');
    }
    $contextmodule = context_module::instance($cm->id);

    if (!$session->allowoverbook && !facetoface_session_has_capacity($session, $contextmodule, MDL_F2F_STATUS_BOOKED, $userid)) {
        return false;
    }

    if (!empty($session->registrationtimestart) && $session->registrationtimestart > $time) {
        return false;
    }

    if (!empty($session->registrationtimefinish) && $session->registrationtimefinish < $time) {
        return false;
    }
    return true;
}

/**
 * Download the list of users attending at least one of the sessions
 * for a given facetoface activity
 *
 * @param string $facetofacename Seminar name
 * @param integer $facetofaceid Seminar ID
 * @param string $unused Previously it was $location but that was deprecated in 9 and removed in 11.
 * @param string $format Download format
 *
 * @return null
 *
 * @deprecated since Totara 12.0
 */
function facetoface_download_attendance($facetofacename, $facetofaceid, $unused = null, $format) {
    global $CFG, $DB;

    debugging('facetoface_download_attendance() function has been deprecated, please use attendees/export.php',
        DEBUG_DEVELOPER);

    $timenow = time();
    $timeformat = str_replace(' ', '_', get_string('strftimedate', 'langconfig'));
    $downloadfilename = clean_filename($facetofacename.'_'.userdate($timenow, $timeformat));

    $dateformat = 0;
    if ('ods' === $format) {
        // OpenDocument format (ISO/IEC 26300)
        require_once($CFG->dirroot.'/lib/odslib.class.php');
        $downloadfilename .= '.ods';
        $workbook = new MoodleODSWorkbook('-');
    } else {
        // Excel format
        require_once($CFG->dirroot.'/lib/excellib.class.php');
        $downloadfilename .= '.xls';
        $workbook = new MoodleExcelWorkbook('-');
        $dateformat = $workbook->add_format();
        $dateformat->set_num_format(MoodleExcelWorkbook::NUMBER_FORMAT_STANDARD_DATE);
    }

    $workbook->send($downloadfilename);
    $worksheet = $workbook->add_worksheet('attendance');
    $courseid = $DB->get_field('facetoface', 'course', array('id' => $facetofaceid));
    $coursecontext = context_course::instance($courseid);
    facetoface_write_worksheet_header($worksheet, $coursecontext);
    facetoface_write_activity_attendance($worksheet, $coursecontext, 1, $facetofaceid, null, '', '', $dateformat);
    $workbook->close();
    exit;
}

/**
 * Add the appropriate column headers to the given worksheet
 *
 * @param object $worksheet  The worksheet to modify (passed by reference)
 * @param object $context the course context of the facetoface instance
 * @returns integer The index of the next column
 *
 * @deprecated since Totara 12.0
 */
function facetoface_write_worksheet_header(&$worksheet, $context)
{

    debugging('facetoface_write_worksheet_header() function has been deprecated, please use attendees/export.php',
        DEBUG_DEVELOPER);

    $pos = 0;
    $customfields = customfield_get_fields_definition('facetoface_session', array('hidden' => 0));
    foreach ($customfields as $field) {
        if (!empty($field->showinsummary)) {
            $worksheet->write_string(0, $pos++, $field->fullname);
        }
    }
    $worksheet->write_string(0, $pos++, get_string('sessionstartdateshort', 'facetoface'));
    $worksheet->write_string(0, $pos++, get_string('sessionfinishdateshort', 'facetoface'));
    $worksheet->write_string(0, $pos++, get_string('room', 'facetoface'));
    $worksheet->write_string(0, $pos++, get_string('timestart', 'facetoface'));
    $worksheet->write_string(0, $pos++, get_string('timefinish', 'facetoface'));
    $worksheet->write_string(0, $pos++, get_string('duration', 'facetoface'));
    $worksheet->write_string(0, $pos++, get_string('status', 'facetoface'));

    if ($trainerroles = facetoface_get_trainer_roles($context)) {
        foreach ($trainerroles as $role) {
            $worksheet->write_string(0, $pos++, get_string('role') . ': ' . $role->localname);
        }
    }

    $userfields = facetoface_get_userfields();
    foreach ($userfields as $shortname => $fullname) {
        $worksheet->write_string(0, $pos++, $fullname);
    }

    $selectjobassignmentonsignupglobal = get_config(null, 'facetoface_selectjobassignmentonsignupglobal');
    if (!empty($selectjobassignmentonsignupglobal)) {
        $worksheet->write_string(0, $pos++, get_string('selectedjobassignment', 'mod_facetoface'));
    }

    $worksheet->write_string(0, $pos++, get_string('attendance', 'facetoface'));
    $worksheet->write_string(0, $pos++, get_string('datesignedup', 'facetoface'));

    return $pos;
}

/**
 * Update site/course and user calendar entries.
 *
 * @param object $session
 * @param object $facetoface, optional
 * @return bool
 *
 * @deprecated since Totara 12.0
 */
function facetoface_update_calendar_entries($session, $facetoface = null) {
    global $USER, $DB;

    debugging('facetoface_update_calendar_entries() function has been deprecated, please use calendar::update_entries()',
        DEBUG_DEVELOPER);

    // Do not re-create calendars as they already removed from cancelled session.
    if ((bool)$session->cancelledstatus) {
        return true;
    }

    if (empty($facetoface)) {
        $facetoface = $DB->get_record('facetoface', array('id' => $session->facetoface));
    }

    // Remove from all calendars.
    facetoface_delete_user_calendar_events($session, 'booking');
    facetoface_delete_user_calendar_events($session, 'session');
    facetoface_remove_session_from_calendar($session, $facetoface->course);
    facetoface_remove_session_from_calendar($session, SITEID);

    if (empty($facetoface->showoncalendar) && empty($facetoface->usercalentry)) {
        return true;
    }

    // Add to NEW calendartype.
    if ($facetoface->usercalentry) {
        // Get ALL enrolled/booked users.
        $users  = facetoface_get_attendees($session->id);
        if (!in_array($USER->id, $users)) {
            facetoface_add_session_to_calendar($session, $facetoface, 'user', $USER->id, 'session');
        }

        foreach ($users as $user) {
            $eventtype = $user->statuscode == MDL_F2F_STATUS_BOOKED ? 'booking' : 'session';
            facetoface_add_session_to_calendar($session, $facetoface, 'user', $user->id, $eventtype);
        }
    }

    if ($facetoface->showoncalendar == F2F_CAL_COURSE) {
        facetoface_add_session_to_calendar($session, $facetoface, 'course');
    } else if ($facetoface->showoncalendar == F2F_CAL_SITE) {
        facetoface_add_session_to_calendar($session, $facetoface, 'site');
    }

    return true;
}

/**
 *Delete all user level calendar events for a face to face session
 *
 * @param class     $session    Record from the facetoface_sessions table
 * @param string    $eventtype  Type of the event (booking or session)
 *
 * @deprecated since Totara 12.0
 */
function facetoface_delete_user_calendar_events($session, $eventtype) {
    global $DB;

    debugging('facetoface_delete_user_calendar_events() function has been deprecated, please use calendar::delete_user_events()',
        DEBUG_DEVELOPER);

    $whereclause = "modulename = 'facetoface' AND
                    eventtype = 'facetoface$eventtype' AND
                    instance = ?";

    $whereparams = array($session->facetoface);

    if ('session' == $eventtype) {
        $likestr = "%attendees.php?s={$session->id}%";
        $likeold = $DB->sql_like('description', '?');
        $whereparams[] = $likestr;

        $likestr = "%view.php?s={$session->id}%";
        $likenew = $DB->sql_like('description', '?');
        $whereparams[] = $likestr;

        $whereclause .= " AND ($likeold OR $likenew)";
    }

    //users calendar
    $users = $DB->get_records_sql("SELECT DISTINCT userid FROM {event} WHERE $whereclause", $whereparams);
    if ($users && count($users) > 0) {
        // Delete the existing events
        $DB->delete_records_select('event', $whereclause, $whereparams);
    }

    return $users;
}

/**
 * Remove all entries in the course calendar which relate to this session.
 *
 * @param class $session    Record from the facetoface_sessions table
 * @param integer $userid   ID of the user
 *
 * @deprecated since Totara 12.0
 */
function facetoface_remove_session_from_calendar($session, $courseid = 0, $userid = 0) {
    global $DB;

    debugging('facetoface_remove_session_from_calendar() function has been deprecated, please use calendar::remove_seminar_event()',
        DEBUG_DEVELOPER);

    $params = array($session->facetoface, $userid, $courseid, $session->id);

    return $DB->delete_records_select('event', "modulename = 'facetoface' AND
                                                instance = ? AND
                                                userid = ? AND
                                                courseid = ? AND
                                                uuid = ?", $params);
}

/**
 * Add a link to the session to the courses calendar.
 *
 * @param stdclass $session      Output from {@see facetoface_get_session} function
 * @param stdclass $eventname    Name to display for this event
 * @param string   $calendartype Which calendar to add the event to (user, course, site)
 * @param int      $userid       Optional param for user calendars
 * @param string   $eventtype    Optional param for user calendar (booking/session)
 *
 * @deprecated since Totara 12.0
 */
function facetoface_add_session_to_calendar($session, $facetoface, $calendartype = 'none', $userid = 0, $eventtype = 'session') {
    global $CFG, $DB;

    debugging('facetoface_add_session_to_calendar() function has been deprecated, please use calendar::add_seminar_event()',
        DEBUG_DEVELOPER);

    if (empty($session->mintimestart)) {
        return true; //date unkown, can't add to calendar
    }

    if (empty($facetoface->showoncalendar) && empty($facetoface->usercalentry)) {
        return true; //facetoface calendar settings prevent calendar
    }

    $description = '';
    if (!empty($facetoface->description)) {
        $description .= html_writer::tag('p', clean_param($facetoface->description, PARAM_CLEANHTML));
    }
    $description .= facetoface_print_session($session, false, true, true);
    $linkurl = new moodle_url('/mod/facetoface/signup.php', array('s' => $session->id));
    $linktext = get_string('signupforthissession', 'facetoface');

    if ($calendartype == 'site' && $facetoface->showoncalendar == F2F_CAL_SITE) {
        $courseid = SITEID;
        $description .= html_writer::link($linkurl, $linktext);
    } else if ($calendartype == 'course' && $facetoface->showoncalendar == F2F_CAL_COURSE) {
        $courseid = $facetoface->course;
        $description .= html_writer::link($linkurl, $linktext);
    } else if ($calendartype == 'user' && $facetoface->usercalentry) {
        $courseid = 0;
        $urlvar = ($eventtype == 'session') ? 'attendees' : 'signup';
        $linkurl = $CFG->wwwroot . "/mod/facetoface/" . $urlvar . ".php?s=$session->id";
        $description .= get_string("calendareventdescription{$eventtype}", 'facetoface', $linkurl);
    } else {
        return true;
    }

    $shortname = $facetoface->shortname;
    if (empty($shortname)) {
        $shortname = shorten_text($facetoface->name, CALENDAR_MAX_NAME_LENGTH);
    }

    // Remove all calendar events related to current session and user before adding new event to avoid duplication.
    facetoface_remove_session_from_calendar($session, $courseid, $userid);

    $result = true;
    foreach ($session->sessiondates as $date) {
        $newevent = new stdClass();
        $newevent->name = $shortname;
        $newevent->description = $description;
        $newevent->format = FORMAT_HTML;
        $newevent->courseid = $courseid;
        $newevent->groupid = 0;
        $newevent->userid = $userid;
        $newevent->uuid = "{$session->id}";
        $newevent->instance = $session->facetoface;
        $newevent->modulename = 'facetoface';
        $newevent->eventtype = "facetoface{$eventtype}";
        $newevent->timestart = $date->timestart;
        $newevent->timeduration = $date->timefinish - $date->timestart;
        $newevent->visible = 1;
        $newevent->timemodified = time();

        $result = $result && $DB->insert_record('event', $newevent);
    }

    return $result;
}

/**
 * Remove all entries in the course calendar which relate to this session.
 *
 * Note: the user/course ID is nominally an integer but it is not right for the
 * code to assume its value will always > 0. This is why default values for the
 * parameters are null, NOT 0. In other words, if a caller passes in a non null
 * user ID, then the assumption is the caller wants to remove calendar entries
 * for that specific userid. It is this contract that works around a problem in
 * `facetoface_remove_session_from_calendar` - where a course/user ID is always
 * used even if it is 0.
 *
 * @param \stdClass $session record from the facetoface_sessions table.
 * @param integer $courseid identifies the specific course whose calendar entry
 *        is to be removed. If null, it is ignored.
 * @param integer $userid identifies the specific user whose calendar entry is
 *        to be removed. If null, it is ignored.
 *
 * @return boolean true if the removal succeeded.
 *
 * @deprecated since Totara 12.0
 */
function facetoface_remove_all_calendar_entries($session, $courseid = null, $userid = null) {
    global $DB;

    debugging('facetoface_remove_all_calendar_entries() function has been deprecated, please use calendar::remove_all_entries()',
        DEBUG_DEVELOPER);

    $initial = new \stdClass();
    $initial->whereClause = "modulename = 'facetoface'";
    $initial->params = array();

    $fragments = array(
        array('instance', $session->facetoface),
        array('uuid',     $session->id),
        array('courseid', $courseid),
        array('userid',   $userid)
    );

    $final = array_reduce($fragments,
        function (\stdClass $accumulated, array $fragment) {

            list($field, $value) = $fragment;
            if (is_null($value)) {
                return $accumulated;
            }

            $accumulated->whereClause = sprintf('%s AND %s = ?', $accumulated->whereClause, $field);
            $accumulated->params[] = $value;

            return $accumulated;
        },

        $initial
    );

    return $DB->delete_records_select('event', $final->whereClause, $final->params);
}

/**
 * Update the date/time of events in the Moodle Calendar when a
 * session's dates are changed.
 *
 * @param class  $session    Record from the facetoface_sessions table
 * @param string $eventtype  Type of the event (booking or session)
 *
 * @deprecated since Totara 12.0
 */
function facetoface_update_user_calendar_events($session, $eventtype) {
    global $DB;

    debugging('facetoface_update_user_calendar_events() function has been deprecated',
        DEBUG_DEVELOPER);

    $facetoface = $DB->get_record('facetoface', array('id' => $session->facetoface));

    if (empty($facetoface->usercalentry) || $facetoface->usercalentry == 0) {
        return true;
    }

    $users = facetoface_delete_user_calendar_events($session, $eventtype);

    // Add this session to these users' calendar
    foreach ($users as $user) {
        facetoface_add_session_to_calendar($session, $facetoface, 'user', $user->userid, $eventtype);
    }
    return true;
}

/**
 * Get custom field filters that are currently selected in facetoface settings
 *
 * @return array Array of objects if any filter is found, empty array otherwise
 *
 * @deprecated since Totara 12.0
 */
function facetoface_get_customfield_filters() {
    global $DB;

    debugging('facetoface_get_customfield_filters() function has been deprecated, please use calendar::get_customfield_filters()',
        DEBUG_DEVELOPER);

    $sessfields = array();
    $roomfields = array();
    $allsearchfields = get_config(null, 'facetoface_calendarfilters');
    if ($allsearchfields) {
        $customfieldids = array('sess' => array(), 'room' => array());
        $allsearchfields = explode(',', $allsearchfields);

        foreach ($allsearchfields as $filterkey) {
            // Customfields are prefixed with room_ and sess_ strings
            // @see settings.php refer to facetoface_calendarfilters setting for details.
            if (strpos($filterkey, 'sess_') === 0) {
                $customfieldids['sess'][] = explode('_', $filterkey)[1];
            }
            if (strpos($filterkey, 'room_') === 0) {
                $customfieldids['room'][] = explode('_', $filterkey)[1];
            }
        }
        if (!empty($customfieldids['sess'])) {
            list($cfids, $cfparams) = $DB->get_in_or_equal($customfieldids['sess']);
            $sql = "SELECT * FROM {facetoface_session_info_field} WHERE id $cfids";
            $sessfields = $DB->get_records_sql($sql, $cfparams);
        }
        if (!empty($customfieldids['room'])) {
            list($cfids, $cfparams) = $DB->get_in_or_equal($customfieldids['room']);
            $sql = "SELECT * FROM {facetoface_room_info_field} WHERE id $cfids";
            $roomfields = $DB->get_records_sql($sql, $cfparams);
        }
    }

    return array('sess' => $sessfields, 'room' => $roomfields);
}

/**
 * Update attendee list status' on booking size change
 * @param stdClass $session
 * @deprecated since Totara 12.0
 */
function facetoface_update_attendees($session) {
    global $USER, $DB;

    debugging('facetoface_update_attendees() function has been deprecated, please use signup_helper::update_attendees()',
        DEBUG_DEVELOPER);

    // Check that the session has not started. We do not want to update the attendees list after the session has started.
    // We check this first to save queries.
    $timenow = time();

    if (facetoface_has_session_started($session, $timenow)) {
        // The session has started, no updating the attendees list.
        return $session->id;
    }

    // Get facetoface
    $facetoface = $DB->get_record('facetoface', array('id' => $session->facetoface));

    // Get course
    $course = $DB->get_record('course', array('id' => $facetoface->course));

    // Update user status'
    $users = facetoface_get_attendees($session->id, array(MDL_F2F_STATUS_BOOKED, MDL_F2F_STATUS_WAITLISTED), true);
    core_collator::asort_objects_by_property($users, 'timesignedup', core_collator::SORT_NUMERIC);

    if ($users) {
        // No/deleted session dates
        if (empty($session->sessiondates)) {

            // Convert any bookings to waitlists
            foreach ($users as $user) {
                if ($user->statuscode == MDL_F2F_STATUS_BOOKED) {

                    if (!$user->id) {
                        // Cope with reserved spaces.
                        facetoface_update_signup_status($user->signupid, MDL_F2F_STATUS_WAITLISTED, $USER->id);
                    } else if (!facetoface_user_signup($session, $facetoface, $course, $user->discountcode, $user->notificationtype, MDL_F2F_STATUS_WAITLISTED, $user->id, true, null)) {
                        // rollback_sql();
                        return false;
                    }
                }
            }

            // Session dates exist
        } else {
            // Convert earliest signed up users to booked, and make the rest waitlisted
            $capacity = $session->capacity;

            // Count number of booked users
            $booked = 0;
            foreach ($users as $user) {
                if ($user->statuscode == MDL_F2F_STATUS_BOOKED) {
                    $booked++;
                }
            }

            // If booked less than capacity, book some new users
            $facetoface_allowwaitlisteveryone = get_config(null, 'facetoface_allowwaitlisteveryone');
            if ($booked < $capacity && (!$session->waitlisteveryone || empty($facetoface_allowwaitlisteveryone))) {

                // Get the no reply user object so this can be used
                // as the from email address further down the process.
                $fromuser = core_user::get_noreply_user();

                foreach ($users as $user) {
                    if ($booked >= $capacity) {
                        break;
                    }

                    if ($user->statuscode == MDL_F2F_STATUS_WAITLISTED) {
                        if (!$user->id) {
                            // Cope with reserved spaces.
                            facetoface_update_signup_status($user->signupid, MDL_F2F_STATUS_BOOKED, $USER->id);
                        } else if (!facetoface_user_signup($session, $facetoface, $course, $user->discountcode, $user->notificationtype, MDL_F2F_STATUS_BOOKED, $user->id, true, $fromuser)) {
                            // rollback_sql();
                            return false;
                        }
                        $booked++;
                    }
                }
            }
        }
    }

    return $session->id;
}

/*
 * Determine if sign-ups to this session should place users on the
 * waitlist or book them.
 *
 * @param object $session A session object
 * @return bool True if the sign-up should be by waitlist, false otherwise.
 * @deprecated Since Totara 12.0
 */
function facetoface_is_signup_by_waitlist($session) {

    // See the $signup text in session_options_signup_link() renderer function for an example.
    debugging('facetoface_is_signup_by_waitlist() function has been deprecated, please use signup::can_switch()',
        DEBUG_DEVELOPER);


    // Users will be waitlisted if the session date is unknown.
    if (empty($session->mintimestart)) {
        return true;
    }

    if (!$cm = get_coursemodule_from_instance('facetoface', $session->facetoface)) {
        print_error('error:incorrectcoursemoduleid', 'facetoface');
    }
    $context = context_module::instance($cm->id);
    // Get waitlisteveryone setting for the session.
    $facetoface_allowwaitlisteveryone = get_config(null, 'facetoface_allowwaitlisteveryone');
    $waitlisteveryone = !empty($facetoface_allowwaitlisteveryone) && $session->waitlisteveryone;
    // If user has capability to overbook?
    $overbook = has_capability('mod/facetoface:signupwaitlist', $context);
    if ($waitlisteveryone || facetoface_get_num_attendees($session->id) >= $session->capacity) {
        return ($overbook ? false : true);
    }

    return false;
}

/**
 * Mark the fact that the user attended the facetoface session by
 * giving that user a grade of 100
 *
 * @param array $data array containing the sessionid under the 's' key
 *                    and every submission ID to mark as attended
 *                    under the 'submissionid_XXXX' keys where XXXX is
 *                     the ID of the signup
 * @deprecated Since Totara 12.0
 */
function facetoface_take_attendance($data) {
    global $USER, $DB;

    debugging('facetoface_take_attendance() function has been deprecated, please use signup_helper::process_attendance()',
        DEBUG_DEVELOPER);

    $sessionid = $data->s;

    // Load session
    if (!$session = facetoface_get_session($sessionid)) {
        error_log('F2F: Could not load facetoface session');
        return false;
    }

    // Check facetoface has finished
    if ($session->mintimestart && !facetoface_has_session_started($session, time())) {
        error_log('F2F: Can not take attendance for a session that has not yet started');
        return false;
    }

    // Record the selected attendees from the user interface - the other attendees will need their grades set
    // to zero, to indicate non attendance, but only the ticked attendees come through from the web interface.
    // Hence the need for a diff
    $selectedsubmissionids = array();

    // FIXME: This is not very efficient, we should do the grade
    // query outside of the loop to get all submissions for a
    // given Face-to-face ID, then call
    // facetoface_grade_item_update with an array of grade
    // objects.
    foreach ($data as $key => $value) {

        $submissionidcheck = substr($key, 0, 13);
        if ($submissionidcheck == 'submissionid_') {
            $submissionid = substr($key, 13);
            $selectedsubmissionids[$submissionid]=$submissionid;

            if (!$DB->record_exists('facetoface_signups', array('id' => $submissionid, 'sessionid' => $session->id))) {
                // The data is inconsistent, hacker?
                error_log("F2F: could not mark signup id '$submissionid' because it does not match session id $session->id");
                continue;
            }

            // Update status
            switch ($value) {

                case MDL_F2F_STATUS_NO_SHOW:
                    $grade = 0;
                    break;

                case MDL_F2F_STATUS_PARTIALLY_ATTENDED:
                    $grade = 50;
                    break;

                case MDL_F2F_STATUS_FULLY_ATTENDED:
                    $grade = 100;
                    break;

                default:
                    // This use has not had attendance set
                    // Jump to the next item in the foreach loop
                    continue 2;
            }

            facetoface_update_signup_status($submissionid, $value, $USER->id, $grade);

            if (!facetoface_take_individual_attendance($submissionid, $grade)) {
                error_log("F2F: could not mark '$submissionid' as ".$value);
                return false;
            }
        }
    }

    return true;
}

/*
 * Set the grading for an individual submission, to either 0 or 100 to indicate attendance
 * @param $submissionid The id of the submission in the database
 * @param $grading Grade to set
 * @deprecated Since Totara 12.0
 */
function facetoface_take_individual_attendance($submissionid, $grading) {
    global $USER, $CFG, $DB;

    debugging('facetoface_take_individual_attendance() function has been deprecated, please use signup_helper::process_attendance()',
        DEBUG_DEVELOPER);

    $timenow = time();

    $record = $DB->get_record_sql("SELECT f.*, s.userid
                                FROM {facetoface_signups} s
                                JOIN {facetoface_sessions} fs ON s.sessionid = fs.id
                                JOIN {facetoface} f ON f.id = fs.facetoface
                                JOIN {course_modules} cm ON cm.instance = f.id
                                JOIN {modules} m ON m.id = cm.module
                                WHERE s.id = ? AND m.name='facetoface'",
        array($submissionid));

    $grade = new stdclass();
    $grade->userid = $record->userid;
    $grade->rawgrade = $grading;
    $grade->rawgrademin = 0;
    $grade->rawgrademax = 100;
    $grade->timecreated = $timenow;
    $grade->timemodified = $timenow;
    $grade->usermodified = $USER->id;

    return facetoface_grade_item_update($record, $grade);
}

/**
 * Checks whether the user can be imported and returns the details of any errors.
 *
 * @param  object  $user           User record from the database
 * @param  object  $context        Context data object
 * @param  object  $facetoface     Facetoface record from the database
 * @param  object  $session        Facetoface session record from the database
 * @param  boolean $ignoreconflics Whether to ignore signup conflicts or not
 * @return array
 * @deprecated since 12.0
 */
function facetoface_validate_user_import($user, $context, $facetoface, $session, $ignoreconflicts = null) {
    global $USER;

    debugging('facetoface_validate_user_import() function has been deprecated, please use signup::debug_transitions()', DEBUG_DEVELOPER);

    $seminar = new \mod_facetoface\seminar($facetoface->id);

    if ($user->deleted) {
        $result['name'] = fullname($user);
        $result['result'] = get_string('error:userdeleted', 'facetoface', fullname($user));
        return $result;
    }

    if ($user->suspended) {
        $result['name'] = fullname($user);
        $result['result'] = get_string('error:usersuspended', 'facetoface', fullname($user));
        return $result;
    }

    $result = array(
        'id' => $user->id,
        'name' =>fullname($user)
    );

    // Check if they are already signed up
    $minimumstatus = ($session->mintimestart) ? MDL_F2F_STATUS_BOOKED : MDL_F2F_STATUS_REQUESTED;
    // If multiple sessions are allowed then just check against this session
    // Otherwise check against all sessions
    $multisessionid = ($facetoface->multiplesessions ? $session->id : null);
    if (facetoface_get_user_submissions($facetoface->id, $user->id, $minimumstatus, MDL_F2F_STATUS_FULLY_ATTENDED, $multisessionid)
        && empty($facetoface->multiplesessions) && $seminar->has_unarchived_signups($user->id)) {
        if ($user->id == $USER->id) {
            $result['result'] = get_string('error:addalreadysignedupattendeeaddself', 'facetoface');
        } else {
            $result['result'] = get_string('error:addalreadysignedupattendee', 'facetoface');
        }
        return $result;
    }

    $facetoface_allowwaitlisteveryone = get_config(null, 'facetoface_allowwaitlisteveryone');
    if (empty($session->waitlisteveryone) && empty($facetoface_allowwaitlisteveryone)
        && !facetoface_session_has_capacity($session, $context) && empty($session->allowoverbook)) {
        $result['result'] = get_string('full', 'facetoface');
        return $result;
    }

    // Check if there are any date conflicts
    if (empty($ignoreconflicts)) {
        $dates = facetoface_get_session_dates($session->id);
        if ($availability = facetoface_get_sessions_within($dates, $user->id)) {
            $result['result'] = facetoface_get_session_involvement($user, $availability);
            $result['conflict'] = true;
            return $result;
        }
    }

    // No errors, so just return an empty array.
    return array();
}

/**
 * Cancel a user who signed up earlier
 *
 * @param stdClass $session    Record from the facetoface_sessions table
 * @param integer $userid      ID of the user to remove from the session
 * @param bool $forcecancel    Forces cancellation of sessions that have already occurred
 * @param string $errorstr     Passed by reference. For setting error string in calling function
 * @param string $cancelreason Optional justification for cancelling the signup
 * @deprecated since 12.0
 */
function facetoface_user_cancel($session, $userid=false, $forcecancel=false, &$errorstr=null, $cancelreason='') {
    if (!$userid) {
        global $USER;
        $userid = $USER->id;
    }

    // if $forcecancel is set, cancel session even if already occurred
    // used by facetotoface_delete_session()
    if (!$forcecancel) {
        $timenow = time();
        // don't allow user to cancel a session that has already occurred
        if (facetoface_has_session_started($session, $timenow)) {
            $errorstr = get_string('error:eventoccurred', 'facetoface');
            return false;
        }
    }

    if (facetoface_user_cancel_submission($session->id, $userid, $cancelreason)) {
        facetoface_remove_session_from_calendar($session, 0, $userid);

        facetoface_update_attendees($session);

        return true;
    }

    $errorstr = get_string('error:cancelbooking', 'facetoface');
    return false;
}

/**
 * Cancel users' submission to a facetoface session
 *
 * @param integer $sessionid   ID of the facetoface_sessions record
 * @param integer $userid      ID of the user record
 * @param string $cancelreason Short justification for cancelling the signup
 * @return boolean success
 * @deprecated since 12.0
 */
function facetoface_user_cancel_submission($sessionid, $userid, $cancelreason='') {
    global $DB, $USER;

    debugging('facetoface_user_cancel_submission() function has been deprecated, please use signup_helper::user_cancel()', DEBUG_DEVELOPER);

    if (!$session = facetoface_get_session($sessionid)) {
        debugging("Could not load Face-to-face session with ID: {$sessionid}", DEBUG_DEVELOPER);
        return false;
    }
    if (!$facetoface = $DB->get_record('facetoface', array('id' => $session->facetoface))) {
        debugging("Could not load Face-to-face instance with ID: {$session->facetoface}", DEBUG_DEVELOPER);
        return false;
    }

    if (!$signup = $DB->get_record('facetoface_signups', array('sessionid' => $sessionid, 'userid' => $userid))) {
        debugging("No user with ID: {$userid} has signed-up for the session ID: {$sessionid}.", DEBUG_DEVELOPER);
        return true; // not signed up, nothing to do
    }

    // If user status already changed to cancelled.
    if (facetoface_is_signup_cancelled((int)$signup->id)) {
        debugging('User status already changed to cancelled.', DEBUG_DEVELOPER);
        return true;
    }

    if ($result = facetoface_update_signup_status($signup->id, MDL_F2F_STATUS_USER_CANCELLED, $USER->id)) {
        // Save cancellation note if cancellation note exists to keep the function working as before.
        if (!empty($cancelreason)) {
            $params = array('shortname' => 'cancellationnote', 'datatype' => 'text');
            if ($cancelfieldid = $DB->get_field('facetoface_cancellation_info_field', 'id', $params)) {
                $canceldataparams = array('fieldid' => $cancelfieldid, 'facetofacecancellationid' => $signup->id);
                if ($DB->record_exists('facetoface_cancellation_info_data', $canceldataparams)) {
                    $DB->set_field('facetoface_cancellation_info_data', 'data', $cancelreason, $canceldataparams);
                } else {
                    $todb = new stdClass();
                    $todb->data = $cancelreason;
                    $todb->fieldid = $cancelfieldid;
                    $todb->facetofacecancellationid = $signup->id;
                    $DB->insert_record('facetoface_cancellation_info_data', $todb);
                }
            }
        }
    }

    return $result;
}

/**
 * Cancel entry from the facetoface_sessions table
 *
 * @param stdClass $session Object from facetoface_get_session()
 * @param stdClass $fromform data from mod_facetoface_cancelsession_form
 * @return bool
 * @deprecated since 12.0
 */
function facetoface_cancel_session($session, $fromform) {
    global $DB, $USER;

    debugging('facetoface_cancel_session() function has been deprecated, please use seminar_event::cancel()', DEBUG_DEVELOPER);

    $seminarevent = new \mod_facetoface\seminar_event($session->id);
    if (facetoface_has_session_started($session, time())) {
        // Session can be cancelled only before it starts.
        return false;
    }
    $sessionobj = $session;

    // Let's get the real DB record, we will need it later for event.
    $session = $DB->get_record('facetoface_sessions', array('id' => $sessionobj->id), '*', MUST_EXIST);
    if ($session->cancelledstatus != 0) {
        // Already cancelled!
        return false;
    }

    $facetoface = $DB->get_record('facetoface', array('id' => $session->facetoface), '*', MUST_EXIST);
    $cm = get_coursemodule_from_instance('facetoface', $facetoface->id);
    $context = context_module::instance($cm->id);

    // List of users affected by cancellation.
    $notifyusers = array();
    $notifytrainers = array();

    // Use transactions here, we need to make sure that all DB updates happen together.
    $trans = $DB->start_delegated_transaction();

    // Save the custom fields.
    if ($fromform) {
        $fromform->id = $session->id;
        customfield_save_data($fromform, 'facetofacesessioncancel', 'facetoface_sessioncancel');
    }

    // Update field cancelledstatus.
    $session->cancelledstatus = '1';
    $sessionobj->cancelledstatus = $session->cancelledstatus;
    $DB->set_field('facetoface_sessions', 'cancelledstatus', $session->cancelledstatus, array('id' => $session->id));

    // Unlink rooms, orphaned custom rooms are deleted from cleanup task.
    $DB->set_field('facetoface_sessions_dates', 'roomid', 0, array('sessionid' => $session->id));

    // Unlink assets, orphaned custom assets are deleted from cleanup task.
    $dateids = $DB->get_fieldset_select('facetoface_sessions_dates', 'id', "sessionid = :sessionid", array('sessionid' => $session->id));
    foreach($dateids as $dateid) {
        $DB->delete_records('facetoface_asset_dates', array('sessionsdateid' => $dateid));
    }

    // Remove entries from the calendars.
    \mod_facetoface\calendar::remove_all_entries($seminarevent);

    // Change all user sign-up statuses, the only exceptions are previously cancelled users and declined users.
    $sql = "SELECT DISTINCT s.userid, s.id as signupid, ss.statuscode as signupstatus 
              FROM {facetoface_signups} s
              JOIN {facetoface_signups_status} ss ON ss.signupid = s.id
             WHERE s.sessionid = :sessionid AND
                   ss.superceded = 0 AND
                   ss.statuscode <> :statususercanceled AND
                   ss.statuscode <> :statususerdeclined";
    $params = array(
        'sessionid' => $session->id,
        'statususercanceled' => MDL_F2F_STATUS_USER_CANCELLED,
        'statususerdeclined' => MDL_F2F_STATUS_DECLINED
    );
    $signedupusers = $DB->get_recordset_sql($sql, $params);
    foreach ($signedupusers as $user) {
        // We record this change as being triggered by the current user.
        facetoface_update_signup_status($user->signupid, MDL_F2F_STATUS_SESSION_CANCELLED, $USER->id);
        $notifyusers[$user->userid] = $user;
    }
    $signedupusers->close();

    // All necessary DB updates are finished, let's commit.
    $trans->allow_commit();

    \mod_facetoface\event\session_cancelled::create_from_session($session, $context)->trigger();

    // Notify trainers assigned to the session too.
    $sql = "SELECT DISTINCT sr.userid
              FROM {facetoface_session_roles} sr
              JOIN {user} u ON (u.id = sr.userid)
             WHERE sr.sessionid = :sessionid AND u.deleted = 0";
    $trainers = $DB->get_recordset_sql($sql, array('sessionid' => $session->id));
    foreach ($trainers as $trainer) {
        $notifytrainers[$trainer->userid] = $trainer;
    }
    $trainers->close();

    $seminarevent = new seminar_event($session->id);

    // Notify affected users.
    foreach ($notifyusers as $id => $user) {
        // Check if the user is waitlisted we should not attach an iCal.
        $invite = $user->signupstatus != MDL_F2F_STATUS_WAITLISTED;
        notice_sender::event_cancellation($id, $seminarevent, $invite);
    }

    // Notify affected trainers.
    foreach ($notifytrainers as $id => $trainer) {
        notice_sender::event_cancellation($id, $seminarevent);
    }
    // Notify managers who had reservations.
    facetoface_notify_reserved_session_deleted($facetoface, $session);

    return true;
}

/**
 * Find out if signup for the given id is cancelled.
 *
 * @param int $signupid
 * @return bool
 * @deprecated since 12.0
 */
function facetoface_is_signup_cancelled(int $signupid): bool {
    global $DB;

    debugging('facetoface_is_signup_cancelled() function has been deprecated, please user signup_helper::is_cancelled()',
        DEBUG_DEVELOPER);

    $sql = "signupid = ? AND superceded = ? AND statuscode = ?";
    $params = [$signupid, 0, MDL_F2F_STATUS_USER_CANCELLED];

    return $DB->record_exists_select('facetoface_signups_status', $sql, $params);
}

/**
 * Mark users' booking requests as declined or approved
 *
 * @param array $data array containing the sessionid under the 's' key
 *                    and an array of request approval/denies
 * @return bool|string[] returns false if there are issues loading the
 *                  data needed to process requests or an array of errors if there
 *                  are issues with the attendee or the request doesn't exist.
 * @deprecated since 12.0
 */
function facetoface_approve_requests($data) {
    global $USER, $DB;

    debugging('facetoface_approve_requests() function has been deprecated, please use signup::switch_state()', DEBUG_DEVELOPER);

    // Check request data
    if (empty($data->requests) || !is_array($data->requests)) {
        error_log('F2F: No request data supplied');
        return false;
    }

    $sessionid = $data->s;

    list($session, $facetoface, $course, $cm, $context) = facetoface_get_env_session($sessionid);

    $approved = array();
    $rejected = array();
    $errors = array();

    // Loop through requests
    foreach ($data->requests as $key => $value) {
        // Check key/value
        if (!is_numeric($key) || !is_numeric($value)) {
            continue;
        }

        // Load user submission
        if (!$attendee = facetoface_get_attendee($sessionid, $key)) {
            $errors[$attendee->id] = 'approverinactive';
            continue;
        }

        // Double-check request exists and not already approved or declined.
        $params = array(
            'signupid' => $attendee->submissionid,
            'superceded' => 0
        );
        if ($signupstatus = $DB->get_record('facetoface_signups_status', $params)) {
            if ($signupstatus->statuscode == MDL_F2F_STATUS_REQUESTED || $signupstatus->statuscode == MDL_F2F_STATUS_REQUESTEDADMIN) {
                $currentstatus = $signupstatus->statuscode;
            } else {
                $errors[$attendee->id] = 'approvalinvalidstatus';
                continue;
            }
        } else {
            $errors[$attendee->id] = 'approvalinvalidstatus';
            continue;
        }

        switch ($value) {
            // Decline
            case 1:
                facetoface_update_signup_status(
                    $attendee->submissionid,
                    MDL_F2F_STATUS_DECLINED,
                    $USER->id
                );

                // Declined users.
                $rejected[$attendee->id] = $attendee->id;

                // Send a decline notice to the user.
                facetoface_send_decline_notice($facetoface, $session, $attendee->id);
                break;

            // Approve
            case 2:
                if ($facetoface->approvaltype == APPROVAL_ADMIN && !facetoface_is_adminapprover($USER->id, $facetoface)) {
                    // Non-admin approves - now need admin approval
                    // The user is on the first step of the 2 step approval process.
                    facetoface_update_signup_status(
                        $attendee->submissionid,
                        MDL_F2F_STATUS_REQUESTEDADMIN,
                        $USER->id
                    );
                } else {
                    // Check if there is capacity
                    if (facetoface_session_has_capacity($session, $context)) {
                        $facetoface_allowwaitlisteveryone = get_config(null, 'facetoface_allowwaitlisteveryone');
                        if (!empty($facetoface_allowwaitlisteveryone) && $session->waitlisteveryone) {
                            // If waitlist everyone is set then send all users to waitlist.
                            $status = MDL_F2F_STATUS_WAITLISTED;
                        } else if ($session->cntdates) {
                            // If there is a session date/time set then user is booked.
                            $status = MDL_F2F_STATUS_BOOKED;
                        } else {
                            // If not then they are waitlisted.
                            $status = MDL_F2F_STATUS_WAITLISTED;
                        }
                    } else {
                        if ($session->allowoverbook) {
                            $status = MDL_F2F_STATUS_WAITLISTED;
                        } else {
                            $url = new moodle_url('/mod/facetoface/attendees.php',
                                array('s' => $sessionid, 'action' => 'approvalrequired'));
                            totara_set_notification(get_string('error:cannotapprovefull', 'facetoface'), $url);
                        }
                    }

                    facetoface_update_signup_status(
                        $attendee->submissionid,
                        MDL_F2F_STATUS_APPROVED,
                        $USER->id
                    );

                    // Signup user
                    if (!facetoface_user_signup(
                        $session,
                        $facetoface,
                        $course,
                        $attendee->discountcode,
                        $attendee->notificationtype,
                        $status,
                        $attendee->id,
                        true,
                        null
                    )) {
                        break;
                    }

                    // Approved users.
                    $approved[$attendee->id] = $attendee->id;
                    break;
                }
            case 0:
            default:
                // Change nothing
                break;
        }
    }

    // Trigger events for approving or declining request in this session
    if (!empty($approved)) {
        $data = array('sessionid' => $session->id, 'userids' => implode(', ', $approved));
        \mod_facetoface\event\booking_requests_approved::create_from_data($data, $context)->trigger();
    }
    if (!empty($rejected)) {
        $data = array('sessionid' => $session->id, 'userids' => implode(', ', $rejected));
        \mod_facetoface\event\booking_requests_rejected::create_from_data($data, $context)->trigger();
    }

    if (!empty($errors)) {
        return $errors;
    }

    return true;
}

/**
 * Update the signup status of a particular signup
 *
 * @param integer $signupid ID of the signup to be updated
 * @param integer $statuscode Status code to be updated to
 * @param integer $createdby User ID of the user causing the status update
 * @param int $grade Grade
 * @param bool $usetransaction Set to true if database transactions are to be used
 *
 * @return integer ID of newly created signup status, or false
 * @deprecated since 12.0
 */
function facetoface_update_signup_status($signupid, $statuscode, $createdby, $grade=NULL) {
    global $DB;

    debugging('facetoface_update_signup_status() function has been deprecated, please use signup::switch_state()',
        DEBUG_DEVELOPER);

    $timenow = time();

    $signupstatus = new stdClass();
    $signupstatus->signupid = $signupid;
    $signupstatus->statuscode = $statuscode;
    $signupstatus->createdby = $createdby;
    $signupstatus->timecreated = $timenow;
    $signupstatus->grade = $grade;
    $signupstatus->superceded = 0;
    $signupstatus->mailed = 0;

    if ($statusid = $DB->insert_record('facetoface_signups_status', $signupstatus)) {
        // mark any previous signup_statuses as superceded
        $where = "signupid = ? AND ( superceded = 0 OR superceded IS NULL ) AND id != ?";
        $whereparams = array($signupid, $statusid);
        $DB->set_field_select('facetoface_signups_status', 'superceded', 1, $where, $whereparams);

        // Check for completions.
        $sql = "SELECT f2f.id, f2f.course, f2fs.userid
                FROM {facetoface_signups} f2fs
                    LEFT JOIN {facetoface_sessions} f2fses ON (f2fses.id = f2fs.sessionid)
                    LEFT JOIN {facetoface} f2f ON (f2f.id = f2fses.facetoface)
                WHERE f2fs.id = ?";

        $status = $DB->get_record_sql($sql, array($signupid));
        facetoface_set_completion($status, $status->userid, COMPLETION_UNKNOWN);

        // Get course module so we can get context for event.
        $signupsql = "SELECT f2fsignup.*, cm.id as cmid
            FROM {facetoface_signups} f2fsignup
            JOIN {facetoface_sessions} f2fsess
                ON f2fsignup.sessionid = f2fsess.id
            JOIN {course_modules} cm
                ON cm.instance = f2fsess.facetoface
            JOIN {modules} m
                ON m.id = cm.module AND m.name = 'facetoface'
            WHERE f2fsignup.id = :signupid";

        $signup = $DB->get_record_sql($signupsql, array('signupid' => $signupid));
        $context = context_module::instance($signup->cmid);
        $signupstatus->id = $statusid;

        unset($signup->cmid);

        \mod_facetoface\event\signup_status_updated::create_from_signup($signupstatus, $context, $signup)->trigger();

        return $statusid;
    } else {
        return false;
    }
}

/**
 * Displays a bulk actions selector
 *
 * @deprecated since Totara 12.0
 */
function facetoface_display_bulk_actions_picker() {
    global $OUTPUT, $MDL_F2F_STATUS;

    debugging('facetoface_display_bulk_actions_picker() function has been deprecated, please use renderer::display_bulk_actions_picker()',
        DEBUG_DEVELOPER);

    $status_options = facetoface_get_attendance_status();
    unset($status_options[$MDL_F2F_STATUS[MDL_F2F_STATUS_NOT_SET]]);
    $out = $OUTPUT->container_start('facetoface-bulk-actions-picker');
    $select = html_writer::select($status_options, 'bulkattendanceop', '',
        array('' => get_string('bulkactions', 'facetoface')), array('class' => 'bulkactions'));
    $label = get_string('mark_selected_as', 'facetoface');
    $error = get_string('selectoptionbefore', 'facetoface');
    $hidenlabel = html_writer::tag('span', $error, array('id' => 'selectoptionbefore', 'class' => 'hide error'));
    $out .= $label;
    $out .= $select;
    $out .= $hidenlabel;
    $out .= $OUTPUT->container_end();

    return $out;
}

/**
 * Get attendance status
 *
 * @deprecated since Totara 12.0
 */
function facetoface_get_attendance_status() {
    global $MDL_F2F_STATUS;

    debugging('facetoface_get_attendance_status() function has been deprecated, please use attendees_list_helper::get_status()',
        DEBUG_DEVELOPER);

    // Look for status fully_attended, partially_attended and no_show.
    $statusoptions = array();
    foreach ($MDL_F2F_STATUS as $key => $value) {
        if ($key <= MDL_F2F_STATUS_BOOKED) {
            continue;
        }
        $statusoptions[$key] = get_string('status_' . $value, 'facetoface');
    }

    return array_reverse($statusoptions, true);
}

/**
 * Returns the human readable code for a face-to-face status
 *
 * @param int $statuscode One of the MDL_F2F_STATUS* constants
 * @return string Human readable code
 *
 * @deprecated since Totara 12.0
 */
function facetoface_get_status($statuscode) {
    global $MDL_F2F_STATUS;

    debugging('facetoface_get_status() function has been deprecated, please use state::from_code($statuscode)::get_string()',
        DEBUG_DEVELOPER);

    // Check code exists
    if (!isset($MDL_F2F_STATUS[$statuscode])) {
        print_error('F2F status code does not exist: '.$statuscode);
    }

    // Get code
    $string = $MDL_F2F_STATUS[$statuscode];

    // Check to make sure the status array looks to be up-to-date
    if (constant('MDL_F2F_STATUS_'.strtoupper($string)) != $statuscode) {
        print_error('F2F status code array does not appear to be up-to-date: '.$statuscode);
    }

    return $string;
}

/**
 * Print the details of a session
 *
 * @param object $session         Record from facetoface_sessions
 * @param boolean $showcapacity   Show the capacity (true) or only the seats available (false)
 * @param boolean $calendaroutput Whether the output should be formatted for a calendar event
 * @param boolean $return         Whether to return (true) the html or print it directly (true)
 * @param boolean $hidesignup     Hide any messages relating to signing up
 * @param string  $class          Custom css class for dl
 * @return string|null html markup when return is true
 * @deprecated since Totara 12.0
 */
function facetoface_print_session($session, $showcapacity, $calendaroutput=false, $return=true, $hidesignup=false, $class='f2f') {
    global $DB, $PAGE, $USER;

    debugging('facetoface_print_session() function has been deprecated, please use renderer::render_seminar_event()',
        DEBUG_DEVELOPER);

    $renderer = $PAGE->get_renderer('mod_facetoface');
    $output = html_writer::start_tag('dl', array('class' => $class));

    // Print customfields.
    $customfields = customfield_get_data($session, 'facetoface_session', 'facetofacesession', true, array('extended' => true));
    if (!empty($customfields)) {
        foreach ($customfields as $cftitle => $cfvalue) {
            $output .= html_writer::tag('dt', str_replace(' ', '&nbsp;', $cftitle));
            $output .= html_writer::tag('dd', $cfvalue);
        }
    }

    $displaytimezones = get_config(null, 'facetoface_displaysessiontimezones');

    $rooms = \mod_facetoface\room_list::get_event_rooms($session->id);

    $strdatetime = str_replace(' ', '&nbsp;', get_string('sessiondatetime', 'facetoface'));
    if ($session->mintimestart) {
        foreach ($session->sessiondates as $date) {
            $output .= html_writer::empty_tag('br');

            $sessionobj = facetoface_format_session_times($date->timestart, $date->timefinish, $date->sessiontimezone);
            if ($sessionobj->startdate == $sessionobj->enddate) {
                $html = $sessionobj->startdate . ', ';
            } else {
                $html = $sessionobj->startdate . ' - ' . $sessionobj->enddate . ', ';
            }

            $sessiontimezonestr = !empty($displaytimezones) ? $sessionobj->timezone : '';
            $html .= $sessionobj->starttime . ' - ' . $sessionobj->endtime . ' ' . $sessiontimezonestr;

            $output .= html_writer::tag('dt', $strdatetime);
            $output .= html_writer::tag('dd', $html);

            $output .= html_writer::tag('dt', get_string('duration', 'facetoface'));
            $output .= html_writer::tag('dd', format_time((int)$date->timestart - (int)$date->timefinish));

            if (!$date->roomid or !$rooms->contains($date->roomid)) {
                continue;
            }
            // Display room information
            $room = $rooms->get($date->roomid);
            $backurl = $PAGE->has_set_url() ? $PAGE->url : null;
            $roomstring = $renderer->get_room_details_html($room, $backurl);

            $systemcontext = context_system::instance();
            $descriptionhtml = file_rewrite_pluginfile_urls($room->get_description(), 'pluginfile.php', $systemcontext->id, 'mod_facetoface', 'room', $room->get_id());
            $roomstring .= format_text($descriptionhtml, FORMAT_HTML);
            $output .= html_writer::tag('dt', get_string('room', 'facetoface'));
            $output .= html_writer::tag('dd', html_writer::tag('span', $roomstring, array('class' => 'roomdescription')));
        }

        $output .= html_writer::empty_tag('br');
    } else {
        $output .= html_writer::tag('dt', $strdatetime);
        $output .= html_writer::tag('dd', html_writer::tag('em', get_string('wait-listed', 'facetoface')));
    }

    $signupcount = facetoface_get_num_attendees($session->id);
    $placesleft = $session->capacity - $signupcount;

    if ($showcapacity) {
        $output .= html_writer::tag('dt', get_string('maxbookings', 'facetoface'));

        if ($session->allowoverbook) {
            $output .= html_writer::tag('dd', get_string('capacityallowoverbook', 'facetoface', $session->capacity));
        } else {
            $output .= html_writer::tag('dd', $session->capacity);
        }
    } else if (!$calendaroutput) {
        $output .= html_writer::tag('dt', get_string('seatsavailable', 'facetoface'));
        $output .= html_writer::tag('dd', max(0, $placesleft));
    }

    // Display requires approval notification
    $facetoface = $DB->get_record('facetoface', array('id' => $session->facetoface));

    // Display job assignments.
    if (get_config(null, 'facetoface_selectjobassignmentonsignupglobal') &&
        ($facetoface->selectjobassignmentonsignup || $facetoface->forceselectjobassignment)) {
        if (isset($session->bookedsession->jobassignmentid) && $session->bookedsession->jobassignmentid) {
            $jobassignment = \totara_job\job_assignment::get_with_id($session->bookedsession->jobassignmentid);
            $output .= html_writer::empty_tag('br');
            $output .= html_writer::tag('dt', get_string('jobassignment', 'facetoface'));
            $output .= html_writer::tag('dd', $jobassignment->fullname);
            $output .= html_writer::empty_tag('br');
        }
    }

    // Display waitlist notification
    if (!$hidesignup && $session->allowoverbook && $placesleft < 1) {
        $output .= html_writer::tag('dd', get_string('userwillbewaitlisted', 'facetoface'));
    }

    // Display managers.
    if ($facetoface->approvaltype != APPROVAL_NONE && $facetoface->approvaltype != APPROVAL_SELF) {
        $approver = facetoface_get_approvaltype_string($facetoface->approvaltype, $facetoface->approvalrole);
        $output .= html_writer::tag('dt', get_string('approvalrequiredby', 'facetoface'));
        $output .= html_writer::tag('dd', $approver);

        if (isset($session->bookedsession->managerid) && $session->bookedsession->managerid) {
            $manager = core_user::get_user($session->bookedsession->managerid);
            $manager_url = new moodle_url('/user/view.php', array('id' => $manager->id));
            $output .= html_writer::tag('dt', get_string('managername', 'facetoface'));
            $output .= html_writer::tag('dd', html_writer::link($manager_url, fullname($manager)));
        } else {
            if (!isset($session->managerids)) {
                $session->managerids   = \totara_job\job_assignment::get_all_manager_userids($USER->id);
            }
            if (!empty($session->managerids)) {
                $managers = array();
                foreach ($session->managerids as $managerid) {
                    $manager = core_user::get_user($managerid);
                    $manager_url = new moodle_url('/user/view.php', array('id' => $manager->id));
                    $managers[] = html_writer::link($manager_url, fullname($manager));
                }
                $output .= html_writer::tag('dt', get_string('managername', 'facetoface'));
                $output .= html_writer::tag('dd', implode(', ', $managers));
            }
        }
    }
    // Display trainers.
    if (!isset($session->trainerroles)) {
        $session->trainerroles = facetoface_get_trainer_roles(context_course::instance($facetoface->course));
    }
    if (!isset($session->trainers)) {
        $session->trainers = facetoface_get_trainers($session->id);
    }
    foreach ((array)$session->trainerroles as $role => $rolename) {

        if (empty($session->trainers[$role])) {
            continue;
        }

        $trainer_names = array();
        $rolename = $rolename->localname;
        foreach ($session->trainers[$role] as $trainer) {
            $trainer_url = new moodle_url('/user/view.php', array('id' => $trainer->id));
            $trainer_names[] = html_writer::link($trainer_url, fullname($trainer));
        }
        $output .= html_writer::tag('dt', $rolename);
        $output .= html_writer::tag('dd', implode(', ', $trainer_names));
    }

    if (!get_config(null, 'facetoface_hidecost') && !empty($session->normalcost)) {
        $output .= html_writer::tag('dt', get_string('normalcost', 'facetoface'));
        $output .= html_writer::tag('dd', format_string($session->normalcost));

        if (!get_config(null, 'facetoface_hidediscount') && !empty($session->discountcost)) {
            $output .= html_writer::tag('dt', get_string('discountcost', 'facetoface'));
            $output .= html_writer::tag('dd', format_string($session->discountcost));
        }
    }

    if (!empty($session->details)) {
        if ($cm = get_coursemodule_from_instance('facetoface', $facetoface->id, $facetoface->course)) {
            $context = context_module::instance($cm->id);
            $session->details = file_rewrite_pluginfile_urls($session->details, 'pluginfile.php', $context->id, 'mod_facetoface', 'session', $session->id);
            $session->details = format_text($session->details, FORMAT_HTML);
        }
        $details = format_text($session->details, FORMAT_HTML);
        $output .= html_writer::tag('dt', get_string('details', 'facetoface'));
        $output .= html_writer::tag('dd', $details);
    }

    $output .= html_writer::end_tag('dl');

    if ($return) {
        return $output;
    }

    echo $output;
}

/**
 * Returns true if the user has registered for a session in the given
 * facetoface activity
 *
 * @global class $USER used to get the current userid
 * @param int $facetofaceid
 * @param int $sessionid session id if facetoface allows multiple sessions
 * @returns integer The session id that we signed up for, false otherwise
 * @deprecated since Totara 12.0
 */
function facetoface_check_signup($facetofaceid, $sessionid = null) {
    global $USER;
    debugging('facetoface_check_signup() function has been deprecated, please use state::is_not_happening()',
        DEBUG_DEVELOPER);

    if ($submissions = facetoface_get_user_submissions($facetofaceid, $USER->id, MDL_F2F_STATUS_REQUESTED, MDL_F2F_STATUS_FULLY_ATTENDED, $sessionid)) {
        return reset($submissions)->sessionid;
    } else {
        return false;
    }
}

/**
 * Kohl's KW - WP06A - Google calendar integration
 * If the unassigned user belongs to a course with an upcoming
 * face-to-face session and they are signed-up to attend, cancel
 * the sign-up (and trigger notification).
 *
 * @deprecated since Totara 12.0
 */
function facetoface_eventhandler_role_unassigned($ra) {
    global $DB;

    debugging('facetoface_eventhandler_role_unassigned() function has been deprecated as unused', DEBUG_DEVELOPER);

    $now = time();

    $ctx = context::instance_by_id($ra->contextid);
    if ($ctx->contextlevel == CONTEXT_COURSE) {
        // get all face-to-face activites in the course
        $activities = $DB->get_records('facetoface', array('course' => $ctx->instanceid));
        if ($activities) {
            foreach ($activities as $facetoface) {
                // get all upcoming sessions for each face-to-face
                $sql = "SELECT s.id
                        FROM {facetoface_sessions} s
                        LEFT JOIN {facetoface_sessions_dates} d ON s.id = d.sessionid
                        WHERE
                            s.facetoface = ? AND d.sessionid = s.id AND
                            (d.timestart IS NULL OR d.timestart > ?)
                        ORDER BY d.timestart
                ";

                if ($sessions = $DB->get_records_sql($sql, array($facetoface->id, $now))) {
                    $cancelreason = "Unenrolled from course";
                    foreach ($sessions as $sessiondata) {
                        $session = facetoface_get_session($sessiondata->id); // load dates etc.
                        $seminarevent = new seminar_event($session->id);

                        // remove trainer session assignments for user (if any exist)
                        if ($trainers = facetoface_get_trainers($session->id)) {
                            foreach ($trainers as $role_id => $users) {
                                foreach ($users as $user_id => $trainer) {
                                    if ($trainer->id == $ra->userid) {
                                        $form = $trainers;
                                        unset($form[$role_id][$user_id]); // remove trainer
                                        facetoface_update_trainers($session->id, $form);
                                        break;
                                    }
                                }
                            }
                        }

                        // cancel learner signup for user (if any exist)
                        $errorstr = '';
                        $signup = signup::create($ra->userid, $seminarevent);
                        if (signup_helper::can_user_cancel($signup)) {
                            signup_helper::user_cancel($signup);
                            notice_sender::signup_cancellation(signup::create($ra->userid, $seminarevent));
                        }
                    }
                }
            }
        }
    } else if ($ctx->contextlevel == CONTEXT_PROGRAM) {
        // nothing to do (probably)
    }

    return true;
}

/**
 * Sync the list of assets for a given seminar event date
 *
 * @param integer $date Seminar date Id
 * @param array $assets List of asset Ids
 * @return bool
 *
 * @deprecated since Totara 13.0
 */
function facetoface_sync_assets($date, array $assets = []) {
    global $DB;

    debugging('facetoface_sync_assets() function has been deprecated, please use asset_helper::sync()',
        DEBUG_DEVELOPER);

    if (empty($assets)) {
        return $DB->delete_records('facetoface_asset_dates', ['sessionsdateid' => $date]);
    }

    $oldassets = $DB->get_fieldset_select('facetoface_asset_dates', 'assetid', 'sessionsdateid = :date_id', ['date_id' => $date]);

    // WIPE THEM AND RECREATE if certain conditions have been met.
    if ((count($assets) == count($oldassets)) && empty(array_diff($assets, $oldassets))) {
        return true;
    }

    $res = $DB->delete_records('facetoface_asset_dates', ['sessionsdateid' => $date]);

    foreach ($assets as $asset) {
        $res &= $DB->insert_record('facetoface_asset_dates', (object) [
            'sessionsdateid' => $date,
            'assetid' => intval($asset)
        ],false);
    }
    return !!$res;
}

/**
 * Withdraws interest from a facetoface activity for a user.
 * @param  object $facetoface A database fieldset object for the facetoface activity
 * @param  int    $userid     Default to current user if null
 * @return boolean            Success
 *
 * @deprecated since Totara 13.0
 */
function facetoface_withdraw_interest($facetoface, $userid = null) {
    global $DB, $USER;

    debugging('facetoface_withdraw_interest() function has been deprecated, please use signup_helper::withdraw_interest()',
        DEBUG_DEVELOPER);

    if (is_null($userid)) {
        $userid = $USER->id;
    }

    return $DB->delete_records('facetoface_interest', array('facetoface' => $facetoface->id, 'userid' => $userid));
}

/** Download data in ODS format
 *
 * @param array $fields Array of column headings
 * @param string $datarows Array of data to populate table with
 * @param string $file Name of file for exportig
 * @return Returns the ODS file
 *
 * @deprecated since Totara 13.0
 */
function facetoface_download_ods($fields, $datarows, $file=null) {
    global $CFG;

    debugging('facetoface_download_ods() function has been deprecated, please use export_helper::download_ods()',
        DEBUG_DEVELOPER);

    require_once("$CFG->libdir/odslib.class.php");
    $filename = clean_filename($file . '.ods');

    header("Content-Type: application/download\n");
    header("Content-Disposition: attachment; filename=$filename");
    header("Expires: 0");
    header("Cache-Control: must-revalidate,post-check=0,pre-check=0");
    header("Pragma: public");

    $workbook = new MoodleODSWorkbook('-');
    $workbook->send($filename);

    $worksheet = array();

    $worksheet[0] = $workbook->add_worksheet('');
    $row = 0;
    $col = 0;

    foreach ($fields as $field) {
        $worksheet[0]->write($row, $col, strip_tags($field));
        $col++;
    }
    $row++;

    $numfields = count($fields);

    foreach ($datarows as $record) {
        for($col=0; $col<$numfields; $col++) {
            if (isset($record[$col])) {
                $worksheet[0]->write($row, $col, html_entity_decode($record[$col], ENT_COMPAT, 'UTF-8'));
            }
        }
        $row++;
    }

    $workbook->close();
    die;
}

/** Download data in XLS format
 *
 * @param array $fields Array of column headings
 * @param string $datarows Array of data to populate table with
 * @param string $file Name of file for exportig
 * @return Returns the Excel file
 *
 * @deprecated since Totara 13.0
 */
function facetoface_download_xls($fields, $datarows, $file=null) {
    global $CFG;

    debugging('facetoface_download_xls() function has been deprecated, please use export_helper::download_xls()',
        DEBUG_DEVELOPER);

    require_once($CFG->libdir . '/excellib.class.php');
    $filename = clean_filename($file . '.xls');

    header("Content-Type: application/download\n");
    header("Content-Disposition: attachment; filename=$filename");
    header("Expires: 0");
    header("Cache-Control: must-revalidate,post-check=0,pre-check=0");
    header("Pragma: public");

    $workbook = new MoodleExcelWorkbook('-');
    $workbook->send($filename);

    $worksheet = array();

    $worksheet[0] = $workbook->add_worksheet('');
    $row = 0;
    $col = 0;

    foreach ($fields as $field) {
        $worksheet[0]->write($row, $col, strip_tags($field));
        $col++;
    }
    $row++;

    $numfields = count($fields);

    foreach ($datarows as $record) {
        for ($col=0; $col<$numfields; $col++) {
            $worksheet[0]->write($row, $col, html_entity_decode($record[$col], ENT_COMPAT, 'UTF-8'));
        }
        $row++;
    }

    $workbook->close();
    die;
}

/** Download data in CSV format
 *
 * @param array $fields Array of column headings
 * @param string $datarows Array of data to populate table with
 * @param string $file Name of file for exportig
 * @return Returns the CSV file
 *
 * @deprecated since Totara 13.0
 */
function facetoface_download_csv($fields, $datarows, $file=null) {
    global $CFG;

    debugging('facetoface_download_csv() function has been deprecated, please use export_helper::download_csv()',
        DEBUG_DEVELOPER);

    require_once($CFG->libdir . '/csvlib.class.php');

    $csvexport = new csv_export_writer();
    $csvexport->set_filename($file);
    $csvexport->add_data($fields);

    $numfields = count($fields);
    foreach ($datarows as $record) {
        $row = array();
        for ($j = 0; $j < $numfields; $j++) {
            $row[] = (isset($record[$j]) ? $record[$j] : '');
        }
        $csvexport->add_data($row);
    }

    $csvexport->download_file();
    die;
}

/**
 * Notify managers that a session they had reserved spaces on has been deleted.
 *
 * @param object $facetoface
 * @param object $session
 *
 * @deprecated since Totara 13.0
 */
function facetoface_notify_reserved_session_deleted($facetoface, $session) {
    global $CFG;

    debugging('facetoface_notify_reserved_session_deleted() function has been deprecated, please use notice_sender::reservation_cancelled()',
        DEBUG_DEVELOPER);

    $attendees = facetoface_get_attendees($session->id, array(\mod_facetoface\signup\state\booked::get_code()), true);
    $reservedids = array();
    foreach ($attendees as $attendee) {
        if ($attendee->bookedby) {
            if (!$attendee->id) {
                // Managers can already get booking cancellation notices - just adding reserve cancellation notices.
                $reservedids[] = $attendee->bookedby;
            }
        }
    }
    if (!$reservedids) {
        return;
    }
    $reservedids = array_unique($reservedids);

    $ccmanager = !empty($facetoface->ccmanager);
    $facetoface->ccmanager = false; // Never Cc the manager's manager (that would just be too much).

    // Notify all managers that have reserved spaces for their team.
    $params = array(
        'facetofaceid'  => $facetoface->id,
        'type'          => MDL_F2F_NOTIFICATION_AUTO,
        'conditiontype' => MDL_F2F_CONDITION_RESERVATION_CANCELLED
    );

    $includeical = empty($CFG->facetoface_disableicalcancel);
    foreach ($reservedids as $reservedid) {
        facetoface_send_notice($facetoface, $session, $reservedid, $params, $includeical ? MDL_F2F_BOTH : MDL_F2F_TEXT, MDL_F2F_CANCEL);
    }

    $facetoface->ccmanager = $ccmanager;
}

/**
 * Returns the effective cost of a session depending on the presence
 * or absence of a discount code.
 *
 * @param int $userid
 * @param int $sessionid
 * @param object $sessiondata
 * @return string
 * @deprecated since Totara 13.0
 */
function facetoface_cost($userid, $sessionid, $sessiondata) {
    global $CFG, $DB;
    debugging('facetoface_cost() function has been deprecated, please use signup::get_cost()',
        DEBUG_DEVELOPER);

    $count = $DB->count_records_sql("SELECT COUNT(*)
                               FROM {facetoface_signups} su,
                                    {facetoface_sessions} se
                              WHERE su.sessionid = ?
                                AND su.userid = ?
                                AND su.discountcode IS NOT NULL
                                AND su.sessionid = se.id", array($sessionid, $userid));
    if ($count > 0) {
        return format_string($sessiondata->discountcost);
    } else {
        // Note that this would return the normal cost if session was deleted and the join above failed.
        return format_string($sessiondata->normalcost);
    }
}

/**
 * Return the approval type of a facetoface as a human readable string
 *
 * @param int approvaltype  The $facetoface->approvaltype value
 * @param int approvalrole  The $facetoface->approvalrole value, only required for role approval
 *
 * @deprecated since Totara 13.0
 */
function facetoface_get_approvaltype_string($approvaltype, $approvalrole = null) {

    debugging('facetoface_get_approvaltype_string() function has been deprecated, please use seminar::get_approvaltype_string()',
        DEBUG_DEVELOPER);

    switch ($approvaltype) {
        case \mod_facetoface\seminar::APPROVAL_NONE:
            return get_string('approval_none', 'mod_facetoface');
        case \mod_facetoface\seminar::APPROVAL_SELF:
            return get_string('approval_self', 'mod_facetoface');
        case \mod_facetoface\seminar::APPROVAL_ROLE:
            $rolenames = role_fix_names(get_all_roles());
            return $rolenames[$approvalrole]->localname;
        case \mod_facetoface\seminar::APPROVAL_MANAGER:
            return get_string('approval_manager', 'mod_facetoface');
        case \mod_facetoface\seminar::APPROVAL_ADMIN:
            return get_string('approval_admin', 'mod_facetoface');
        default:
            print_error('error:unrecognisedapprovaltype', 'mod_facetoface');
    }
}

/**
 * Confirm that a session has free space for a user
 *
 * @param class  $session Record from the facetoface_sessions table
 * @param object $context (optional) A context object (record from context table)
 * @param int    $status (optional), default is '70' (booked)
 * @param int    $userid (optional)
 * @return bool True if user can be added to session
 *
 * @deprecated since Totara 13.0
 */
function facetoface_session_has_capacity($session, $context = false, $status = null, $userid = 0) {
    global $USER;

    debugging('facetoface_session_has_capacity() function has been deprecated, please use seminar_event::has_capacity()',
        DEBUG_DEVELOPER);

    if (empty($session)) {
        return false;
    }
    if (is_null($status)) {
        $status = \mod_facetoface\signup\state\booked::get_code();
    }
    if (!$userid) {
        $userid = $USER->id;
    }

    $signupcount = facetoface_get_num_attendees($session->id, $status);

    if ($signupcount >= $session->capacity) {
        // if session is full, check if overbooking is allowed for this user
        if (!$context || !has_capability('mod/facetoface:signupwaitlist', $context, $userid)) {
            return false;
        }
    }

    return true;
}

/**
 * Get user current status
 *
 * @param $sessionid
 * @param $userid
 * @return mixed
 *
 * @deprecated since Totara 13.0
 */
function facetoface_get_user_current_status($sessionid, $userid) {
    global $DB;

    debugging('facetoface_get_user_current_status() function has been deprecated, please use signup::get_state() instead.',
        DEBUG_DEVELOPER);

    $sql = "
        SELECT ss.*
          FROM {facetoface_signups} su
          JOIN {facetoface_signups_status} ss ON su.id = ss.signupid
         WHERE su.sessionid = ?
           AND su.userid = ?
           AND ss.superceded = 0";

    return $DB->get_record_sql($sql, array($sessionid, $userid));

}

/**
 * Sets totara_set_notification message describing bulk import results
 * @param array $results
 * @param string $type
 *
 * @deprecated since Totara 13.0
 */
function facetoface_set_bulk_result_notification($results, $type = 'bulkadd') {

    debugging('facetoface_set_bulk_result_notification() function has been deprecated, please use attendees_list_helper::set_bulk_result_notification()',
        DEBUG_DEVELOPER);

    $added          = $results[0];
    $errors         = $results[1];
    $result_message = '';

    $noticeclass = 'notifysuccess';
    // Generate messages
    if ($errors) {
        $noticeclass = 'notifyproblem';
        $result_message .= get_string($type.'attendeeserror', 'facetoface') . ' - ';

        if (count($errors) == 1 && is_string($errors[0])) {
            $result_message .= $errors[0];
        } else {
            $result_message .= get_string('xerrorsencounteredduringimport', 'facetoface', count($errors));
            $result_message .= \html_writer::link('#', get_string('viewresults', 'mod_facetoface'), ['class' => 'f2f-import-results']);
        }
    } else if ($added) {
        $result_message .= get_string($type.'attendeessuccess', 'facetoface') . ' - ';
        if ($type == 'bulkremove') {
            $result_message .= get_string('successfullyremovedxattendees', 'facetoface', count($added));
        } else {
            $result_message .= get_string('successfullyaddededitedxattendees', 'facetoface', count($added));
        }
        $result_message .= \html_writer::link('#', get_string('viewresults', 'mod_facetoface'), ['class' => 'f2f-import-results']);
    }

    if ($result_message != '') {
        totara_set_notification($result_message, null, array('class' => $noticeclass));
    }
}

/**
 * Sets activity completion state
 *
 * @param stdClass $facetoface object
 * @param int $userid User ID
 * @param int $completionstate Completion state
 * @return mixed Returns false if completion is not enabled, or void otherwise
 * @deprecated since Totara 13.0
 */
function facetoface_set_completion($facetoface, $userid, $completionstate = COMPLETION_COMPLETE) {
    debugging('facetoface_set_completion() function has been deprecated, please use seminar::set_completion()',
        DEBUG_DEVELOPER);

    $course = new stdClass();
    $course->id = $facetoface->course;
    $completion = new completion_info($course);

    // Check if completion is enabled site-wide, or for the course
    if (!$completion->is_enabled()) {
        return;
    }

    $cm = get_coursemodule_from_instance('facetoface', $facetoface->id, $facetoface->course);
    if (empty($cm) || !$completion->is_enabled($cm)) {
        return;
    }

    $completion->update_state($cm, $completionstate, $userid);
    $completion->invalidatecache($facetoface->course, $userid, true);
}

/**
 * Build user roles in conflict message, used when saving an event.
 *
 * @param stdClass[] $users_in_conflict Array of users in conflict.
 * @return string Message
 *
 * @deprecated since Totara 13.0
 */
function facetoface_build_user_roles_in_conflict_message($users_in_conflict) {

    debugging('facetoface_build_user_roles_in_conflict_message() function has been deprecated, please use mod_facetoface\form\event::get_conflict_message()',
        DEBUG_DEVELOPER);

    if (empty($users_in_conflict)) {
        return '';
    }

    foreach ($users_in_conflict as $user) {
        if (property_exists($user, "name")) {
            // Indicating that the $user was already had the attribute 'name' built.
            $users[] = $user->name;
            continue;
        }
        $users[] = fullname($user);
    }
    $details = new stdClass();
    $details->users = implode('; ', $users);
    $details->userscount = count($users_in_conflict);

    return format_text(get_string('userschedulingconflictdetected_body', 'facetoface', $details));
}

/**
 * Determine if a user is in the waitlist of a session.
 *
 * @param object $session A session object
 * @param int $userid The user ID
 * @return bool True if the user is on waitlist, false otherwise.
 *
 * @deprecated since Totara 13.0
 */
function facetoface_is_user_on_waitlist($session, $userid = null) {
    global $DB, $USER;

    debugging('facetoface_is_user_on_waitlist() function has been deprecated, please use mod_facetoface\signup\state\waitlisted',
        DEBUG_DEVELOPER);

    if ($userid === null) {
        $userid = $USER->id;
    }

    $sql = "SELECT 1
            FROM {facetoface_signups} su
            JOIN {facetoface_signups_status} ss ON su.id = ss.signupid
            WHERE su.sessionid = ?
              AND ss.superceded != 1
              AND su.userid = ?
              AND ss.statuscode = ?";

    return $DB->record_exists_sql($sql, array($session->id, $userid, \mod_facetoface\signup\state\waitlisted::get_code()));
}

/**
 * Called when displaying facetoface Task to check
 * capacity of the session.
 *
 * @param array Message data for a facetoface task
 * @return bool True if there is capacity in the session
 *
 * @deprecated since Totara 13.0
 */
function facetoface_task_check_capacity($data) {

    debugging('facetoface_task_check_capacity() function has been deprecated as unused', DEBUG_DEVELOPER);

    $session = $data['session'];
    // Get session from database in case it has been updated
    $seminarevent = new \mod_facetoface\seminar_event($session->id);
    if (!$session) {
        return false;
    }
    $facetoface = $data['facetoface'];

    if (!$cm = get_coursemodule_from_instance('facetoface', $facetoface->id, $facetoface->course)) {
        print_error('error:incorrectcoursemodule', 'facetoface');
    }
    $contextmodule = context_module::instance($cm->id);

    return ($seminarevent->has_capacity($contextmodule) || $seminarevent->get_allowoverbook());
}

/**
 * Return message describing bulk import results
 *
 * @access  public
 * @param   array       $results
 * @param   string      $type
 * @return  string
 *
 * @deprecated since Totara 13.0
 */
function facetoface_generate_bulk_result_notice($results, $type = 'bulkadd') {

    debugging('facetoface_generate_bulk_result_notice() function has been deprecated as unused', DEBUG_DEVELOPER);

    $added          = $results[0];
    $errors         = $results[1];
    $result_message = '';

    $dialogid = 'f2f-import-results';
    $noticeclass = ($added) ? 'addedattendees' : 'noaddedattendees';
    // Generate messages
    if ($errors) {
        $result_message .= '<div class="' . $noticeclass . ' notifyproblem">';
        $result_message .= get_string($type.'attendeeserror', 'facetoface') . ' - ';

        if (count($errors) == 1 && is_string($errors[0])) {
            $result_message .= $errors[0];
        } else {
            $result_message .= get_string('xerrorsencounteredduringimport', 'facetoface', count($errors));
            $result_message .= ' <a href="#" id="'.$dialogid.'">('.get_string('viewresults', 'facetoface').')</a>';
        }
        $result_message .= '</div>';
    }
    if ($added) {
        $result_message .= '<div class="' . $noticeclass . ' notifysuccess">';
        $result_message .= get_string($type.'attendeessuccess', 'facetoface') . ' - ';
        $result_message .= get_string('successfullyaddededitedxattendees', 'facetoface', count($added));
        $result_message .= ' <a href="#" id="'.$dialogid.'">('.get_string('viewresults', 'facetoface').')</a>';
        $result_message .= '</div>';
    }

    return $result_message;
}

/**
 * Return an array of all facetoface activities in the current course
 *
 * @deprecated since Totara 13.0
 */
function facetoface_get_facetoface_menu() {
    global $DB;

    debugging('facetoface_get_facetoface_menu() function has been deprecated as unused', DEBUG_DEVELOPER);

    if ($facetofaces = $DB->get_records_sql("SELECT f.id, c.shortname, f.name
                                            FROM {course} c, {facetoface} f
                                            WHERE c.id = f.course
                                            ORDER BY c.shortname, f.name")) {
        $i=1;
        foreach ($facetofaces as $facetoface) {
            $f = $facetoface->id;
            $facetofacemenu[$f] = $facetoface->shortname.' --- '.$facetoface->name;
            $i++;
        }
        return $facetofacemenu;
    } else {
        return '';
    }
}

/**
 * Confirms waitlisted users from an array as booked on a session
 * @param int    $sessionid  ID of the session to use
 * @param array  $userids    Array of user ids to confirm
 * @return string[] failures or empty array
 * @deprecated since Totara 13.0
 */
function facetoface_confirm_attendees($sessionid, $userids) {
    global $DB;

    debugging('facetoface_confirm_attendees() function has been deprecated, please use signup_helper::confirm_waitlist()',
        DEBUG_DEVELOPER);

    $errors = [];
    $seminarevent = new \mod_facetoface\seminar_event($sessionid);
    foreach ($userids as $userid) {
        $signup = \mod_facetoface\signup::create($userid, $seminarevent);
        if ($signup->get_state() instanceof \mod_facetoface\signup\state\not_set) {
            continue;
        }

        if ($signup->can_switch(\mod_facetoface\signup\state\booked::class)) {
            $signup->switch_state(\mod_facetoface\signup\state\booked::class);
            $conditions = array('sessionid' => $sessionid, 'userid' => $userid);
            $existingsignup = $DB->get_record('facetoface_signups', $conditions, '*', MUST_EXIST);
            notice_sender::confirm_booking(new signup($existingsignup->id), $existingsignup->notificationtype);
        } else {
            $failures = $signup->get_failures(\mod_facetoface\signup\state\booked::class);
            if (!empty($failures)) {
                $errors[$signup->get_userid()] = current($failures);
            }
        }
    }
    return $errors;
}

/**
 * Randomly books waitlisted users on to a session
 * @param int $sessionid  ID of the session to use
 * @deprecated since Totara 13.0
 */
function facetoface_waitlist_randomly_confirm_users($sessionid, $userids) {

    debugging('facetoface_waitlist_randomly_confirm_users() function has been deprecated, please use signup_helper::confirm_waitlist_randomly()',
        DEBUG_DEVELOPER);

    $session = facetoface_get_session($sessionid);
    $signupcount = facetoface_get_num_attendees($sessionid);

    $numtoconfirm = $session->capacity - $signupcount;

    if (count($userids) <= $session->capacity) {
        $winners = $userids;
    } else {
        $winners = array_rand(array_flip($userids), $numtoconfirm);

        if ($numtoconfirm == 1) {
            $winners = array($winners);
        }
    }

    facetoface_confirm_attendees($sessionid, $winners);

    return $winners;
}

/**
 * Cancels waitlisted users from an array on a session
 * @param int    $sessionid  ID of the session to use
 * @param array  $userids    Array of user ids to cancel
 * @deprecated since Totara 13.0
 */
function facetoface_cancel_attendees($sessionid, $userids) {

    debugging('facetoface_cancel_attendees() function has been deprecated, please use signup_helper::cancel_waitlist()',
        DEBUG_DEVELOPER);

    $seminarevent = new \mod_facetoface\seminar_event($sessionid);
    foreach ($userids as $userid) {
        $signup = \mod_facetoface\signup::create($userid, $seminarevent);
        if ($signup->get_state() instanceof \mod_facetoface\signup\state\not_set) {
            continue;
        }
        if ($signup->can_switch(\mod_facetoface\signup\state\user_cancelled::class)) {
            $signup->switch_state(\mod_facetoface\signup\state\user_cancelled::class);
        }
    }
}

/**
 * Send a notice (all session dates in one message).
 *
 * @param class $facetoface record from the facetoface table
 * @param class $session record from the facetoface_sessions table
 * @param integer $userid ID of the recipient of the email
 * @param array $params The parameters for the notification
 * @param int $icalattachmenttype The ical attachment type, or MDL_F2F_TEXT to disable ical attachments
 * @param int $icalattachmentmethod The ical method type: MDL_F2F_INVITE or MDL_F2F_CANCEL
 * @param object $fromuser User object describing who the email is from.
 * @param array $olddates array of previous dates
 * @return string Error message (or empty string if successful)
 * @deprecated since Totara 13.0
 */
function facetoface_send_notice($facetoface, $session, $userid, $params, $icalattachmenttype = MDL_F2F_TEXT, $icalattachmentmethod = MDL_F2F_INVITE, $fromuser = null, array $olddates = array()) {
    global $DB;

    debugging('facetoface_send_notice() function has been deprecated, please use \mod_facetoface\notice_sender::send_notice()',
        DEBUG_DEVELOPER);

    $notificationdisable = get_config(null, 'facetoface_notificationdisable');
    if (!empty($notificationdisable)) {
        return false;
    }

    $user = $DB->get_record('user', array('id' => $userid));
    if (!$user) {
        return 'userdoesnotexist';
    }

    // If checkbox option is not present here then it means, perversely, that it was checked on the confirmation form.
    // This matters when we arrive here via adhoc task running in cron.
    if (!isset($session->notifyuser)) {
        $session->notifyuser = true;
    }

    if (!isset($session->notifymanager)) {
        $session->notifymanager = true;
    }

    // Make it not fail if more then one notification found. Just use one.
    // Other option is to change data_object, but so far it's facetoface issue that we hope to fix soon and remove workaround
    // code from here.
    $checkrows = $DB->get_records('facetoface_notification', $params);
    if (count($checkrows) > 1) {
        $params['id'] = reset($checkrows)->id;
        debugging("Duplicate notifications found for (excluding id): " . json_encode($params), DEBUG_DEVELOPER);
    }

    // By definition, the send one email per day feature works on sessions with
    // dates. However, the current system allows sessions to be created without
    // dates and it allows people to sign up to those sessions. In this cases,
    // the sign ups still need to get email notifications; hence the checking of
    // the existence of dates before allowing the send one email per day part.
    // Note, that's not always the case, if all dates have been deleted from a
    // seminar event we still need to send the emails to cancel the dates,
    // thus need to check whether old dates have been supplied.
    $session = facetoface_notification_session_dates($session);
    if (get_config(null, 'facetoface_oneemailperday')
        && !(empty($session->sessiondates) && empty($olddates))) {
        return facetoface_send_oneperday_notice($facetoface, $session, $userid, $params, $icalattachmenttype, $icalattachmentmethod, $fromuser, $olddates);
    }

    $notice = new facetoface_notification($params);
    if (isset($facetoface->ccmanager)) {
        $notice->ccmanager = $facetoface->ccmanager;
    }
    $notice->set_facetoface($facetoface);

    $notice->set_newevent($user, $session->id, null, $fromuser);
    $icaldata = [];
    if ((int)$icalattachmenttype == MDL_F2F_BOTH && $notice->conditiontype != MDL_F2F_CONDITION_DECLINE_CONFIRMATION) {
        $notice->add_ical_attachment($user, $session, $icalattachmentmethod, null, $olddates);
        $icaldata = [
            'dates' => $session->sessiondates,
            'olddates' => $olddates,
            'method' => $icalattachmentmethod
        ];
    }

    if ($session->notifyuser) {
        $notice->send_to_user($user, $session->id, null, $icaldata);
    }
    if ($session->notifymanager) {
        $notice->send_to_manager($user, $session->id);
    }
    $notice->send_to_thirdparty($user, $session->id);
    $notice->send_to_roleapprovers_adhoc($user, $session->id);
    $notice->send_to_adminapprovers_adhoc($user, $session->id);
    $notice->delete_ical_attachment();

    return '';
}

/**
 * Send a notice (one message per session date).
 *
 * @param stdClass $facetoface record from the facetoface table
 * @param stdClass $session record from the facetoface_sessions table
 * @param integer $userid ID of the recipient of the email
 * @param array $params The parameters for the notification
 * @param int $icalattachmenttype The ical attachment type, or MDL_F2F_TEXT to disable ical attachments
 * @param int $icalattachmentmethod The ical method type: MDL_F2F_INVITE or MDL_F2F_CANCEL
 * @param object $fromuser User object describing who the email is from.
 * @param array $olddates array of previous dates
 * @return string Error message (or empty string if successful)
 * @deprecated since Totara 13.0
 */
function facetoface_send_oneperday_notice($facetoface, $session, $userid, $params, $icalattachmenttype = MDL_F2F_TEXT, $icalattachmentmethod = MDL_F2F_INVITE, $fromuser = null, array $olddates = []) {
    global $DB, $CFG;

    debugging('facetoface_send_oneperday_notice() function has been deprecated, please use \mod_facetoface\notice_sender::send_notice_oneperday()',
        DEBUG_DEVELOPER);

    $notificationdisable = get_config(null, 'facetoface_notificationdisable');
    if (!empty($notificationdisable)) {
        return false;
    }

    $user = $DB->get_record('user', array('id' => $userid));
    if (!$user) {
        return 'userdoesnotexist';
    }

    $session = facetoface_notification_session_dates($session);

    // Filtering dates.
    // "Key by" date id.
    $get_id = function($item) {
        return $item->id;
    };
    $olds = array_combine(array_map($get_id, $olddates), $olddates);

    $dates = array_filter($session->sessiondates, function($date) use (&$olds) {
        if (isset($olds[$date->id])) {
            $old = $olds[$date->id];
            unset($olds[$date->id]);
            if ($old->sessiontimezone == $date->sessiontimezone &&
                $old->timestart == $date->timestart &&
                $old->timefinish == $date->timefinish &&
                $old->roomid == $date->roomid) {
                return false;
            }
        }

        return true;
    });

    $send = function($dates, $cancel = false) use ($facetoface, $session, $icalattachmenttype, $icalattachmentmethod, $user, $params, $CFG) {
        foreach ($dates as $date) {

            if ($cancel) {
                $params['conditiontype'] = MDL_F2F_CONDITION_CANCELLATION_CONFIRMATION;
            }

            $sendical =  (int)$icalattachmenttype == MDL_F2F_BOTH &&
                (!$cancel || ($cancel && empty($CFG->facetoface_disableicalcancel)));

            $notice = new facetoface_notification($params);

            if (isset($facetoface->ccmanager)) {
                $notice->ccmanager = $facetoface->ccmanager;
            }
            $notice->set_facetoface($facetoface);
            // Send original notice for this date.
            $notice->set_newevent($user, $session->id, $date);
            if ($sendical) {
                $notice->add_ical_attachment($user, $session, $icalattachmentmethod, !$cancel ? $date : [], $cancel ? $date : []);
            }
            if ($session->notifyuser) {
                $notice->send_to_user($user, $session->id, $date);
            }
            if ($session->notifymanager) {
                $notice->send_to_manager($user, $session->id);
            }

            $notice->send_to_thirdparty($user, $session->id);
            $notice->send_to_roleapprovers_adhoc($user, $session->id);
            $notice->send_to_adminapprovers_adhoc($user, $session->id);

            $notice->delete_ical_attachment();
        }
    };

    $send($dates);
    $send($olds, true);

    return '';
}

/**
 * Send a confirmation email to the user and manager regarding the
 * cancellation
 *
 * @param \stdClass $facetoface record from the facetoface table
 * @param \stdClass $session record from the facetoface_sessions table
 * @param integer $userid ID of the recipient of the email
 * @param integer $conditiontype Optional override of the standard cancellation confirmation
 * @param bool $invite flag whether to include iCal invitation
 * @returns string Error message (or empty string if successful)
 * @deprecated Since Totara 12.0
 */
function facetoface_send_cancellation_notice($facetoface, $session, $userid, $conditiontype = MDL_F2F_CONDITION_CANCELLATION_CONFIRMATION, $invite = true) {
    global $CFG;
    debugging('facetoface_send_cancellation_notice() function has been deprecated, please use notice_sender::signup_cancellation_notice() or notice_sender::event_cancellation_notice()', DEBUG_DEVELOPER);

    $params = array(
        'facetofaceid'  => $facetoface->id,
        'type'          => MDL_F2F_NOTIFICATION_AUTO,
        'conditiontype' => $conditiontype
    );

    $includeical = empty($CFG->facetoface_disableicalcancel) && $invite;
    return facetoface_send_notice($facetoface, $session, $userid, $params, $includeical ? MDL_F2F_BOTH : MDL_F2F_TEXT, MDL_F2F_CANCEL);
}

/**
 * Send a confirmation email to the user and manager regarding the
 * cancellation
 *
 * @param class $facetoface record from the facetoface table
 * @param class $session record from the facetoface_sessions table
 * @param integer $userid ID of the recipient of the email
 * @returns string Error message (or empty string if successful)
 * @deprecated Since Totara 12.0
 */
function facetoface_send_decline_notice($facetoface, $session, $userid) {
    global $CFG;
    debugging('facetoface_send_decline_notice() function has been deprecated, please use notice_sender::decline()', DEBUG_DEVELOPER);

    $params = array(
        'facetofaceid'  => $facetoface->id,
        'type'          => MDL_F2F_NOTIFICATION_AUTO,
        'conditiontype' => MDL_F2F_CONDITION_DECLINE_CONFIRMATION
    );

    $includeical = empty($CFG->facetoface_disableicalcancel);
    return facetoface_send_notice($facetoface, $session, $userid, $params, $includeical ? MDL_F2F_BOTH : MDL_F2F_TEXT, MDL_F2F_CANCEL);
}

/**
 * Send a email to the user and manager regarding the
 * session date/time change
 *
 * @param \stdClass $facetoface record from the facetoface table
 * @param \stdClass $session record from the facetoface_sessions table
 * @param integer $userid ID of the recipient of the email
 * @param array $olddates array of previous dates
 * @param bool $invite flag whether to include iCal invitation
 * @returns string Error message (or empty string if successful)
 * @deprecated Since Totara 12.0
 */
function facetoface_send_datetime_change_notice($facetoface, $session, $userid, $olddates, $invite = true) {
    debugging('facetoface_send_datetime_change_notice() function has been deprecated, please use notice_sender::event_datetime_changed() or notice_sender::signup_datetime_changed()', DEBUG_DEVELOPER);

    $params = array(
        'facetofaceid'  => $facetoface->id,
        'type'          => MDL_F2F_NOTIFICATION_AUTO,
        'conditiontype' => MDL_F2F_CONDITION_SESSION_DATETIME_CHANGE
    );

    $invite = $invite ? MDL_F2F_BOTH : MDL_F2F_TEXT;

    return facetoface_send_notice($facetoface, $session, $userid, $params, $invite, MDL_F2F_INVITE, null, $olddates);
}

/**
 * Send a confirmation email to the user and manager
 *
 * @param class $facetoface record from the facetoface table
 * @param class $session record from the facetoface_sessions table
 * @param integer $userid ID of the recipient of the email
 * @param integer $notificationtype Type of notifications to be sent @see {{MDL_F2F_INVITE}}
 * @param boolean $iswaitlisted If the user has been waitlisted
 * @param object $fromuser User object describing who the email is from.
 * @returns string Error message (or empty string if successful)
 * @deprecated Since Totara 12.0
 */
function facetoface_send_confirmation_notice($facetoface, $session, $userid, $notificationtype, $iswaitlisted, $fromuser = null) {
    debugging('facetoface_send_confirmation_notice() function has been deprecated, please use notice_sender::confirm_booking() or notice_sender::confirm_waitlist()', DEBUG_DEVELOPER);

    $params = array(
        'facetofaceid'  => $facetoface->id,
        'type'          => MDL_F2F_NOTIFICATION_AUTO
    );

    if ($iswaitlisted) {
        $params['conditiontype'] = MDL_F2F_CONDITION_WAITLISTED_CONFIRMATION;
    } else {
        $params['conditiontype'] = MDL_F2F_CONDITION_BOOKING_CONFIRMATION;
    }

    return facetoface_send_notice($facetoface, $session, $userid, $params, $notificationtype, MDL_F2F_INVITE, $fromuser);
}

/**
 * Send a confirmation email to the trainer
 *
 * @param class $facetoface record from the facetoface table
 * @param class $session record from the facetoface_sessions table
 * @param integer $userid ID of the recipient of the email
 * @param integer $notificationtype Type of notifications to be sent @see {{MDL_F2F_INVITE}}
 * @returns string Error message (or empty string if successful)
 * @deprecated Since Totara 12.0
 */
function facetoface_send_trainer_confirmation_notice($facetoface, $session, $userid) {
    debugging('facetoface_send_trainer_confirmation_notice() function has been deprecated, please use notice_sender::trainer_confirmation()', DEBUG_DEVELOPER);

    $params = array(
        'facetofaceid'  => $facetoface->id,
        'type'          => MDL_F2F_NOTIFICATION_AUTO,
        'conditiontype' => MDL_F2F_CONDITION_TRAINER_CONFIRMATION
    );

    return facetoface_send_notice($facetoface, $session, $userid, $params, MDL_F2F_BOTH, MDL_F2F_INVITE);
}

/**
 * Send a unassignment email to the trainer
 *
 * @param class $facetoface record from the facetoface table
 * @param class $session record from the facetoface_sessions table
 * @param integer $userid ID of the recipient of the email
 * @param integer $notificationtype Type of notifications to be sent @see {{MDL_F2F_INVITE}}
 * @returns string Error message (or empty string if successful)
 * @deprecated Since Totara 12.0
 */
function facetoface_send_trainer_session_unassignment_notice($facetoface, $session, $userid) {
    debugging('facetoface_send_trainer_session_unassignment_notice() function has been deprecated, please use notice_sender::event_trainer_unassigned()', DEBUG_DEVELOPER);

    $params = array(
        'facetofaceid'  => $facetoface->id,
        'type'          => MDL_F2F_NOTIFICATION_AUTO,
        'conditiontype' => MDL_F2F_CONDITION_TRAINER_SESSION_UNASSIGNMENT
    );

    return facetoface_send_notice($facetoface, $session, $userid, $params, MDL_F2F_BOTH, MDL_F2F_CANCEL);
}

/**
 * Send booking request notice to user and their manager
 *
 * @param   object  $facetoface Facetoface instance
 * @param   object  $session    Session instance
 * @param   int     $userid     ID of user requesting booking
 * @return  string  Error string, empty on success
 * @deprecated since Totara 12.0
 */
function facetoface_send_request_notice($facetoface, $session, $userid) {
    global $DB;

    debugging('facetoface_send_request_notice() function has been deprecated, please use notice_sender::request_manager()', DEBUG_DEVELOPER);

    $params = array('userid' => $userid, 'sessionid' => $session->id);
    $jobassignmentid = $DB->get_field('facetoface_signups', 'jobassignmentid', $params);
    $managers = facetoface_get_session_managers($userid, $session->id, $jobassignmentid);
    $sent = false;

    foreach ($managers as $manager) {
        if (empty($manager->email)) {
            continue;
        }
        $sent = true;

        $params = array(
            'facetofaceid'  => $facetoface->id,
            'type'          => MDL_F2F_NOTIFICATION_AUTO,
            'conditiontype' => MDL_F2F_CONDITION_BOOKING_REQUEST_MANAGER
        );
        return facetoface_send_notice($facetoface, $session, $userid, $params);
    }
    return 'error:nomanagersemailset';
}

/**
 * Send booking request notice to user and all users with the specified sessionrole
 * @param object $facetoface    Facetoface instance
 * @param object $session       Session instance
 * @param int    $recipientid   The id of the user requesting a booking
 * @deprecated since Totara 12.0
 */
function facetoface_send_rolerequest_notice($facetoface, $session, $recipientid) {
    debugging('facetoface_send_rolerequest_notice() function has been deprecated, please use notice_sender::request_role()', DEBUG_DEVELOPER);

    $params = array(
        'facetofaceid'  => $facetoface->id,
        'type'          => MDL_F2F_NOTIFICATION_AUTO,
        'conditiontype' => MDL_F2F_CONDITION_BOOKING_REQUEST_ROLE
    );

    return facetoface_send_notice($facetoface, $session, $recipientid, $params);
}

/**
 * Send booking request notice to user, manager, all session admins.
 *
 * @param object $facetoface    Facetoface instance
 * @param object $session       Session instance
 * @param array  $admins        An array of admin userids
 * @param int    $recipientid   The id of the user requesting a booking
 * @deprecated Since Totara 12.0
 */
function facetoface_send_adminrequest_notice($facetoface, $session, $recipientid) {
    debugging('facetoface_send_adminrequest_notice() function has been deprecated, please use notice_sender::request_admin()', DEBUG_DEVELOPER);

    $params = array(
        'facetofaceid'  => $facetoface->id,
        'type'          => MDL_F2F_NOTIFICATION_AUTO,
        'conditiontype' => MDL_F2F_CONDITION_BOOKING_REQUEST_ADMIN
    );

    return facetoface_send_notice($facetoface, $session, $recipientid, $params);
}

/**
 * Send registration closure notice to user, manager, all session admins.
 *
 * @param object $facetoface    Facetoface instance
 * @param object $session       Session instance
 * @param int    $recipientid   The id of the user requesting a booking
 * @deprecated Since Totara 13.0
 */
function facetoface_send_registration_closure_notice($facetoface, $session, $recipientid) {
    global $DB, $USER;

    debugging('facetoface_send_registration_closure_notice() function has been deprecated, please use \mod_facetoface\notice_sender::registration_closure()',
        DEBUG_DEVELOPER);

    $notificationdisable = get_config(null, 'facetoface_notificationdisable');
    if (!empty($notificationdisable)) {
        return false;
    }

    $recipient = $DB->get_record('user', array('id' => $recipientid));
    if (!$recipient) {
        return 'userdoesnotexist';
    }

    $params = array(
        'facetofaceid'  => $facetoface->id,
        'type'          => MDL_F2F_NOTIFICATION_AUTO,
        'conditiontype' => MDL_F2F_CONDITION_BEFORE_REGISTRATION_ENDS
    );

    $notice = new facetoface_notification($params);
    $notice->set_newevent($recipient, $session->id, null, $USER);
    $notice->send_to_user($recipient, $session->id);
    $notice->send_to_manager($recipient, $session->id);

    return '';
}

/**
 * Send a cancellation email to the trainer
 *
 * @param class $facetoface record from the facetoface table
 * @param class $session record from the facetoface_sessions table
 * @param integer $userid ID of the recipient of the email
 * @param integer $notificationtype Type of notifications to be sent @see {{MDL_F2F_INVITE}}
 * @returns string Error message (or empty string if successful)
 * @deprecated Since Totara 12.0
 */
function facetoface_send_trainer_session_cancellation_notice($facetoface, $session, $userid) {
    debugging('facetoface_send_trainer_session_cancellation_notice() function has been deprecated, please use notice_sender::event_trainer_cancellation()', DEBUG_DEVELOPER);

    $params = array(
        'facetofaceid'  => $facetoface->id,
        'type'          => MDL_F2F_NOTIFICATION_AUTO,
        'conditiontype' => MDL_F2F_CONDITION_TRAINER_SESSION_CANCELLATION
    );

    return facetoface_send_notice($facetoface, $session, $userid, $params, MDL_F2F_BOTH, MDL_F2F_CANCEL);
}

/**
 * Send out email notifications for all sessions that are under capacity at the cut-off.
 *
 * This function has been deprecated, please use mod_facetoface\notification\notification_helper::notify_under_capacity instead
 * @deprecated since Totara 13
 */
function facetoface_notify_under_capacity() {
    global $CFG, $DB;

    debugging(
        "The function 'facetoface_notify_under_capacity' has been deprecatred, " .
        "please call to mod_facetoface\\notification\\notification_helper::notify_under_capacity instead",
        DEBUG_DEVELOPER
    );

    $conditions = array('component' => 'mod_facetoface', 'classname' => '\mod_facetoface\task\send_notifications_task');
    $lastcron = $DB->get_field('task_scheduled', 'lastruntime', $conditions);
    $roles = $CFG->facetoface_session_rolesnotify;
    $time = time();

    // If there are no recipients, don't bother.
    if (empty($roles)) {
        return;
    }

    $params = array(
        'lastcron' => $lastcron,
        'now'      => $time
    );

    $sql = "SELECT s.*, minstart FROM {facetoface_sessions} s
            INNER JOIN (
                SELECT s.id as sessid, MIN(timestart) AS minstart
                FROM {facetoface_sessions} s
                INNER JOIN {facetoface_sessions_dates} d ON s.id = d.sessionid
                GROUP BY s.id
            ) dates ON dates.sessid = s.id
            WHERE mincapacity > 0
              AND (minstart - cutoff) < :now
              AND (minstart - cutoff) >= :lastcron
              AND s.cancelledstatus = 0";

    $tocheck = $DB->get_records_sql($sql, $params);

    foreach ($tocheck as $session) {
        $booked = facetoface_get_num_attendees($session->id, \mod_facetoface\signup\state\waitlisted::get_code());
        if ($booked >= $session->mincapacity) {
            continue;
        }

        // No cutoff period means the notify bookings email checkbox was not checked.
        if (empty($session->cutoff)) {
            continue;
        }

        // We've found a session that has not reached the minimum bookings by the cut-off - time to send out emails.
        $facetoface = $DB->get_record('facetoface', array('id' => $session->facetoface));
        $cm = get_coursemodule_from_instance('facetoface', $facetoface->id, $facetoface->course, false);
        $url = new moodle_url('/mod/facetoface/view.php', array('id' => $cm->id));
        $link = $url->out(false);

        // Get the start time of the first date for this session.
        if (!$session->minstart) {
            $starttime = get_string('nostarttime', 'facetoface');
        } else {
            $starttime = userdate($session->minstart, get_string('strftimedatetime'));
        }

        $info = (object)array(
            'name' => format_string($facetoface->name),
            'starttime' => $starttime,
            'capacity' => $session->capacity,
            'mincapacity' => $session->mincapacity,
            'booked' => $booked,
            'link' => $link,
        );

        $eventdata = (object)array(
            'userfrom' => \mod_facetoface\facetoface_user::get_facetoface_user(),
            'subject' => get_string('sessionundercapacity', 'facetoface', format_string($facetoface->name)),
            'fullmessage' => get_string('sessionundercapacity_body', 'facetoface', $info),
            'msgtype' => TOTARA_MSG_TYPE_FACE2FACE,
            'msgstatus' => TOTARA_MSG_STATUS_NOTOK,
            'urgency' => TOTARA_MSG_URGENCY_NORMAL,
            'sendmail' => TOTARA_MSG_EMAIL_YES,
        );

        if (CLI_SCRIPT && !PHPUNIT_TEST) {
            mtrace("Facetoface '{$info->name}' in course {$facetoface->course} is under minimum bookings - {$info->booked}/{$info->capacity} (min capacity {$info->mincapacity}) - emailing session roles.");
        }

        // Get all the users who need to receive the under capacity warning.
        if (!$cm = get_coursemodule_from_instance('facetoface', $facetoface->id, $facetoface->course)) {
            print_error('error:incorrectcoursemodule', 'facetoface');
        }
        $modcontext = context_module::instance($cm->id);

        // Note: remove the true to limit to users with the roles within the module.
        $recipients = get_role_users(explode(',', $roles), $modcontext, true, 'u.*');

        // And send them the notification.
        foreach ($recipients as $recipient) {
            // At this point, to prevent the object $eventdata added up data, it needed to be cloned and reset after sent
            /** @var stdClass $shadowcopy */
            $shadowcopy = clone $eventdata;
            $shadowcopy->userto = $recipient;
            tm_alert_send($shadowcopy);
            unset($shadowcopy);
        }
    }
}

/**
 * Send out email notifications for all sessions where registration period has ended.
 *
 * This function has been deprecated, please use mod_facetoface\notification\notification_helper::notify_registration_ended instead
 * @deprecated Since Totara 13
 */
function facetoface_notify_registration_ended() {
    global $CFG, $DB;

    debugging(
        "The function 'facetoface_notify_registration_ended' has been deprecated, please call to " .
        "mod_facetoface\\notification\\notification_helper::notify_registration_ended instead",
        DEBUG_DEVELOPER
    );

    if (empty($CFG->facetoface_session_rolesnotify)) {
        return;
    }

    $conditions = array('component' => 'mod_facetoface', 'classname' => '\mod_facetoface\task\send_notifications_task');
    $lastcron = $DB->get_field('task_scheduled', 'lastruntime', $conditions);
    $time = time();
    $params = array(
        'lastcron' => $lastcron,
        'now'      => $time
    );

    $sql = "SELECT s.*, minstart
            FROM {facetoface_sessions} s
                INNER JOIN (
                    SELECT s.id AS sessid, MIN(timestart) AS minstart
                    FROM {facetoface_sessions} s
                    INNER JOIN {facetoface_sessions_dates} d ON s.id = d.sessionid
                    GROUP BY s.id
                ) dates ON dates.sessid = s.id
            WHERE registrationtimefinish < :now
            AND registrationtimefinish >= :lastcron
            AND registrationtimefinish != 0";

    $tocheck = $DB->get_recordset_sql($sql, $params);

    foreach ($tocheck as $session) {
        $notification = new \facetoface_notification((array)$session, false);
        $notification->send_notification_registration_expired($session);
    }
    $tocheck->close();
}

/**
 * Cancel all pending requests for a given session.
 * Primarily for use with the close_registrations task
 *
 * This function has been deprecated
 *
 * @param stdClass $session - A database record from facetoface_session
 * @deprecated since Totara 13
 */
function facetoface_cancel_pending_requests($session) {
    global $DB;

    debugging(
        "The function 'facetoface_cancel_pending_requests' has been deprecated",
        DEBUG_DEVELOPER
    );

    // Find any pending requests for the given session.
    $requestsql = "SELECT fss.*, fs.userid as recipient
                     FROM {facetoface_signups} fs
               INNER JOIN {facetoface_signups_status} fss
                       ON fss.signupid = fs.id AND fss.superceded = 0
                    WHERE fs.sessionid = :sess
                      AND (statuscode = :req OR statuscode = :adreq)";
    $requestparams = array('req' => \mod_facetoface\signup\state\requested::get_code(), 'adreq' => \mod_facetoface\signup\state\requestedadmin::get_code());

    $f2fs = array();

    $requestparams['sess'] = $session->id;
    $pendingrequests = $DB->get_records_sql($requestsql, $requestparams);

    // Loop through all the pending requests, cancel them, and send a notification to the user.
    if (!empty($pendingrequests)) {
        if (!isset($f2fs[$session->facetoface])) {
            $f2fs[$session->facetoface] = $DB->get_record('facetoface', array('id' => $session->facetoface), '*', MUST_EXIST);
        }

        $errors = [];
        foreach ($pendingrequests as $pending) {
            // Mark the request as declined so they can no longer be approved.
            $signup = new signup($pending->signupid);
            if ($signup->can_switch(\mod_facetoface\signup\state\declined::class)) {
                $signup->switch_state(\mod_facetoface\signup\state\declined::class);
            } else {
                $failures = $signup->get_failures( \mod_facetoface\signup\state\declined::class);
                $errors[$pending->recipient] = current($failures);
            }
            // Send a registration expiration message to the user (and their manager).
            facetoface_send_registration_closure_notice($f2fs[$session->facetoface], $session, $pending->recipient);
        }
    }
}


/**
 * Delete entry from the facetoface_sessions table along with all
 * related details in other tables
 *
 * This function has been deprecated, please call to \mod_facetoface\seminar_event::delete() instead
 *
 * @param object $session Record from facetoface_sessions
 * @deprecated since Totara 13
 */
function facetoface_delete_session($session) {
    global $DB;

    debugging(
        "The function facetoface_delete_session() has been deprecated, please use \\mod_facetoface\\seminar_event::delete() instead",
        DEBUG_DEVELOPER
    );

    $facetoface = $DB->get_record('facetoface', array('id' => $session->facetoface), '*', MUST_EXIST);
    $cm = get_coursemodule_from_instance('facetoface', $facetoface->id, $facetoface->course, false, MUST_EXIST);
    $context = context_module::instance($cm->id);

    // Get session status and if it is over, do not send any cancellation notifications, see below.
    $sessionover = $session->mintimestart && facetoface_has_session_started($session, time());

    // Cancel user signups (and notify users)
    $signedupusers = $DB->get_records_sql(
        "
            SELECT DISTINCT
                userid
            FROM
                {facetoface_signups} s
            LEFT JOIN
                {facetoface_signups_status} ss
             ON ss.signupid = s.id
            WHERE
                s.sessionid = ?
            AND ss.superceded = 0
            AND ss.statuscode >= ?
        ", array($session->id, \mod_facetoface\signup\state\requested::get_code()));

    $seminarevent = new \mod_facetoface\seminar_event($session->id);

    if ($signedupusers and count($signedupusers) > 0) {
        foreach ($signedupusers as $user) {
            $signup = \mod_facetoface\signup::create($user->userid, $seminarevent);
            if (\mod_facetoface\signup_helper::can_user_cancel($signup)) {
                \mod_facetoface\signup_helper::user_cancel($signup);
                if (!$sessionover) {
                    \mod_facetoface\notice_sender::event_cancellation($user->userid, $seminarevent);
                }
            }
        }
    }

    // Send cancellations for trainers assigned to the session.
    $trainers = $DB->get_records("facetoface_session_roles", array("sessionid" => $session->id));
    if ($trainers and count($trainers) > 0) {
        foreach ($trainers as $trainer) {
            if (!$sessionover) {
                \mod_facetoface\notice_sender::event_cancellation($trainer->userid, $seminarevent);
            }
        }
    }

    // Notify managers who had reservations.
    if (!$sessionover) {
        \mod_facetoface\notice_sender::reservation_cancelled($seminarevent);
    }

    $transaction = $DB->start_delegated_transaction();

    // Remove entries from the teacher calendars.
    // Deleting records before refactoring attendees/view.php page
    $select = $DB->sql_like('description', ':attendess');
    $select .= " AND modulename = 'facetoface' AND eventtype = 'facetofacesession' AND instance = :facetofaceid";
    $params = array('attendess' => "%attendees.php?s={$session->id}%", 'facetofaceid' => $facetoface->id);
    $DB->delete_records_select('event', $select, $params);
    // Remove entries from the teacher calendars.
    // Deleting records after refactoring attendees/view.php page
    $select = $DB->sql_like('description', ':attendess');
    $select .= " AND modulename = 'facetoface' AND eventtype = 'facetofacesession' AND instance = :facetofaceid";
    $params = array('attendess' => "%view.php?s={$session->id}%", 'facetofaceid' => $facetoface->id);
    $DB->delete_records_select('event', $select, $params);

    $seminarevent = new \mod_facetoface\seminar_event($session->id);
    if ($facetoface->showoncalendar == F2F_CAL_COURSE) {
        // Remove entry from course calendar
        \mod_facetoface\calendar::remove_seminar_event($seminarevent, $facetoface->course);
    } else if ($facetoface->showoncalendar == F2F_CAL_SITE) {
        // Remove entry from site-wide calendar
        \mod_facetoface\calendar::remove_seminar_event($seminarevent, SITEID);
    }

    // Delete links to assets and delete freshly orphaned custom assets because there is little chance they would be reused.
    $dateids = $DB->get_fieldset_select('facetoface_sessions_dates', 'id', "sessionid = :sessionid", array('sessionid' => $session->id));
    foreach ($dateids as $dateid) {
        $sql = "SELECT fa.id
                  FROM {facetoface_asset} fa
                  JOIN {facetoface_asset_dates} fad ON (fad.assetid = fa.id)
                 WHERE fa.custom = 1 AND sessionsdateid = :sessionsdateid";
        $customassetids = $DB->get_fieldset_sql($sql, array('sessionsdateid' => $dateid));
        $DB->delete_records('facetoface_asset_dates', array('sessionsdateid' => $dateid));
        foreach ($customassetids as $assetid) {
            if (!$DB->record_exists('facetoface_asset_dates', array('assetid' => $assetid))) {
                $asset = new \mod_facetoface\asset($assetid);
                $asset->delete();
            }
        }
    }

    // Delete links to rooms and delete freshly orphaned custom rooms because there is little chance they would be reused.
    $sql = "SELECT fr.id
              FROM {facetoface_room} fr
              JOIN {facetoface_sessions_dates} fsd ON (fsd.roomid = fr.id)
             WHERE fr.custom = 1 AND sessionid = :sessionid";
    $customroomids = $DB->get_fieldset_sql($sql, array('sessionid' => $session->id));
    $DB->set_field('facetoface_sessions_dates', 'roomid', 0, array('sessionid' => $session->id));
    foreach ($customroomids as $roomid) {
        if (!$DB->record_exists('facetoface_sessions_dates', array('roomid' => $roomid))) {
            $room = new \mod_facetoface\room($roomid);
            $room->delete();
        }
    }

    // Delete session details
    $DB->delete_records('facetoface_sessions_dates', array('sessionid' => $session->id));
    $DB->delete_records('facetoface_session_roles', array('sessionid' => $session->id));

    // Get session data to delete.
    $sessiondataids = $DB->get_fieldset_select(
        'facetoface_session_info_data',
        'id',
        "facetofacesessionid = :facetofacesessionid",
        array('facetofacesessionid' => $session->id));

    if (!empty($sessiondataids)) {
        list($sqlin, $inparams) = $DB->get_in_or_equal($sessiondataids);
        $DB->delete_records_select('facetoface_session_info_data_param', "dataid {$sqlin}", $inparams);
        $DB->delete_records_select('facetoface_session_info_data', "id {$sqlin}", $inparams);
    }

    $sessioncancelparams = array('sessionid' => $session->id);
    $sessioncancelids = $DB->get_fieldset_select(
        'facetoface_sessioncancel_info_data',
        'id',
        "facetofacesessioncancelid = :sessionid",
        $sessioncancelparams
    );
    if (!empty($sessioncancelids)) {
        list($sqlin, $inparams) = $DB->get_in_or_equal($sessioncancelids);
        $DB->delete_records_select('facetoface_sessioncancel_info_data_param', "dataid $sqlin", $inparams);
        $DB->delete_records_select('facetoface_sessioncancel_info_data', "id {$sqlin}", $inparams);
    }

    // Delete signups and related data.
    $signups = \mod_facetoface\signup_list::from_conditions(['sessionid' => (int)$session->id]);
    $signups->delete();

    // Notifications.
    $DB->delete_records('facetoface_notification_sent', array('sessionid' => $session->id));
    $DB->delete_records('facetoface_notification_hist', array('sessionid' => $session->id));

    // Delete files embedded in details text.
    $fs = get_file_storage();
    $fs->delete_area_files($context->id, 'mod_facetoface', 'session', $session->id);

    $DB->delete_records('facetoface_sessions', array('id' => $session->id));

    $transaction->allow_commit();

    return true;
}


/**
 * This function has been deprecated, please use \mod_facetoface\trainer_helper class instead.
 *
 * @param $facetoface
 * @param $session
 * @param $form
 *
 * @return bool
 * @deprecated since Totara 13.0
 */
function facetoface_update_trainers($facetoface, $session, $form) {
    global $DB;

    debugging(
            "The function facetoface_update_trainers has been deprecated, please call to " .
            \mod_facetoface\trainer_helper::class . " instead",
            DEBUG_DEVELOPER
    );

    // If we recieved bad data
    if (!is_array($form)) {
        return false;
    }

    // Load current trainers
    $current_trainers = facetoface_get_trainers($session->id);
    // To collect trainers
    $new_trainers = array();
    $old_trainers = array();

    $transaction = $DB->start_delegated_transaction();

    // Loop through form data and add any new trainers
    foreach ($form as $roleid => $trainers) {

        // Loop through trainers in this role
        foreach ($trainers as $trainer) {

            if (!$trainer) {
                continue;
            }

            // If the trainer doesn't exist already, create it
            if (!isset($current_trainers[$roleid][$trainer])) {

                $newtrainer = new stdClass();
                $newtrainer->userid = $trainer;
                $newtrainer->roleid = $roleid;
                $newtrainer->sessionid = $session->id;
                $new_trainers[] = $newtrainer;

                if (!$DB->insert_record('facetoface_session_roles', $newtrainer)) {
                    print_error('error:couldnotaddtrainer', 'facetoface');
                    $transaction->force_transaction_rollback();
                    return false;
                }
            } else {
                unset($current_trainers[$roleid][$trainer]);
            }
        }
    }

    // Loop through what is left of old trainers, and remove
    // (as they have been deselected)
    if ($current_trainers) {
        foreach ($current_trainers as $roleid => $trainers) {
            // If no trainers left
            if (empty($trainers)) {
                continue;
            }

            // Delete any remaining trainers
            foreach ($trainers as $trainer) {
                $old_trainers[] = $trainer;
                if (!$DB->delete_records('facetoface_session_roles', array('sessionid' => $session->id, 'roleid' => $roleid, 'userid' => $trainer->id))) {
                    print_error('error:couldnotdeletetrainer', 'facetoface');
                    $transaction->force_transaction_rollback();
                    return false;
                }
            }
        }
    }

    $transaction->allow_commit();

    $seminarevent = new \mod_facetoface\seminar_event($session->id);

    // Send a confirmation notice to new trainer
    foreach ($new_trainers as $i => $trainer) {
        \mod_facetoface\notice_sender::trainer_confirmation($trainer->userid, $seminarevent);
    }

    // Send an unassignment notice to old trainer
    foreach ($old_trainers as $i => $trainer) {
        \mod_facetoface\notice_sender::trainer_confirmation($trainer->id, $seminarevent);
    }

    return true;
}


/**
 * This function has been deprecated, please use \mod_facetoface\trainer_helper::get_trainer_roles() instead.
 *
 * Return array of trainer roles configured for face-to-face
 * @param $context context of the facetoface activity
 * @return  array
 *
 * @deprecated since Totara 13.0
 */
function facetoface_get_trainer_roles($context) {
    global $CFG, $DB;

    debugging(
            "The function facetoface_get_trainer_roles has been deprecated, please call to " .
            "\\mod_facetoface\\trainer_helper::get_trainer_roles() instead",
            DEBUG_DEVELOPER
    );

    // Check that roles have been selected
    if (empty($CFG->facetoface_session_roles)) {
        return false;
    }

    // Parse roles
    $cleanroles = clean_param($CFG->facetoface_session_roles, PARAM_SEQUENCE);
    list($rolesql, $params) = $DB->get_in_or_equal(explode(',', $cleanroles));

    // Load role names
    $rolenames = $DB->get_records_sql("
        SELECT
            r.id,
            r.name
        FROM
            {role} r
        WHERE
            r.id {$rolesql}
        AND r.id <> 0
    ", $params);

    // Return roles and names
    if (!$rolenames) {
        return array();
    }

    $rolenames = role_fix_names($rolenames, $context);

    return $rolenames;
}


/**
 * This function has been deprecated, please use \mod_facetoface\trainer_helper::get_trainers() instead.
 *
 * Get all trainers associated with a session, optionally
 * restricted to a certain roleid
 *
 * If a roleid is not specified, will return a multi-dimensional
 * array keyed by roleids, with an array of the chosen roles
 * for each role
 *
 * @param   integer     $sessionid
 * @param   integer     $roleid (optional)
 * @return  array
 *
 * @deprecated since Totara 13.0
 */
function facetoface_get_trainers($sessionid, $roleid = null) {
    global $CFG, $DB;

    debugging(
            "The function facetoface_get_trainers has been deprecated, " .
            "please use \\mod_facetoface\\trainer_helper::get_trainers() instead",
            DEBUG_DEVELOPER
    );

    $usernamefields = get_all_user_name_fields(true, 'u');
    $sql = "
        SELECT
            u.id,
            {$usernamefields},
            r.roleid
        FROM
            {facetoface_session_roles} r
        LEFT JOIN
            {user} u
         ON u.id = r.userid
        WHERE
            r.sessionid = ?
        ";
    $params = array($sessionid);

    if ($roleid) {
        $sql .= "AND r.roleid = ?";
        $params[] = $roleid;
    }

    $rs = $DB->get_recordset_sql($sql , $params);
    $return = array();
    foreach ($rs as $record) {
        // Create new array for this role
        if (!isset($return[$record->roleid])) {
            $return[$record->roleid] = array();
        }
        $return[$record->roleid][$record->id] = $record;
    }
    $rs->close();

    // If we are only after one roleid
    if ($roleid) {
        if (empty($return[$roleid])) {
            return false;
        }
        return $return[$roleid];
    }

    // If we are after all roles
    if (empty($return)) {
        return false;
    }

    return $return;
}


/**
 * This function has been deprecated, please use \mod_facetoface\signup::get_managers() instead.
 *
 * @param int   $userid     The user whose manager we are looking for
 * @param int   $sessionid  The session where the manager is assigned
 * @param int   $jobassignmentid The job when users are allowed to select their secondary jobs !!! "Seconardy" jobs ???
 * @return array of object   The user object (including fullname) of the user assigned as the learners managers
 *
 * @deprecated since Totara 13.0
 */
function facetoface_get_session_managers($userid, $sessionid, $jobassignmentid = null) {
    global $DB;

    debugging(
            "The function facetoface_get_session_managers has been deprecated, " .
            " please use \\mod_facetoface\\signup::get_managers()"
    );

    $managerselect = get_config(null, 'facetoface_managerselect');
    $selectjobassignmentonsignupglobal = get_config(null, 'facetoface_selectjobassignmentonsignupglobal');
    $signup = $DB->get_record('facetoface_signups', array('userid' => $userid, 'sessionid' => $sessionid));

    $managers = array();

    if ($managerselect && !empty($signup->managerid)) {
        // Check if they selected a manager for their signup.
        $manager = $DB->get_record('user', array('id' => $signup->managerid));
        $managers[] = $manager;
    } else if ($selectjobassignmentonsignupglobal && !empty($jobassignmentid)) {
        // The job assignment could not be found here, because the system admin might had deleted
        // the job assignment record, but did not update the seminar signup record here.

        // This could mean that, seminar system is not able to notify this user's manager here.
        // However, when deleting the job assignment of a user, this could indicate that this
        // user is no longer being managed by the same manager anymore. Unless, deleting job
        // assignment is an accident.
        $ja = \totara_job\job_assignment::get_with_id($jobassignmentid, false);
        if (null != $ja && $ja->managerid) {
            $managers[] = $DB->get_record('user', array('id' => $ja->managerid));
        }
    } else {
        $managerids = \totara_job\job_assignment::get_all_manager_userids($userid);
        if (!empty($managerids)) {
            list($mansql, $manparams) = $DB->get_in_or_equal($managerids, SQL_PARAMS_NAMED);
            $managers = $DB->get_records_select('user', "id $mansql", $manparams);
        }
    }

    foreach ($managers as &$manager) {
        $manager->fullname = fullname($manager);
    }

    return $managers;
}