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
 * @author Alastair Munro <alastair.munro@totaralms.com>
 * @author Aaron Barnes <aaron.barnes@totaralms.com>
 * @author Francois Marier <francois@catalyst.net.nz>
 * @author Valerii Kuznetsov <valerii.kuznetsov@totaralms.com>
 * @package mod_facetoface
 */

defined('MOODLE_INTERNAL') || die();

use mod_facetoface\{
    seminar,
    signup,
    seminar_event,
    notice_sender,
    signup_list,
    seminar_event_list,
    reservations
};

use mod_facetoface\query\event\query;
use mod_facetoface\query\event\sortorder\future_sortorder;

require_once($CFG->libdir.'/gradelib.php');
require_once($CFG->dirroot.'/grade/lib.php');
require_once($CFG->dirroot.'/lib/adminlib.php');
require_once($CFG->dirroot . '/user/selector/lib.php');
require_once $CFG->dirroot.'/mod/facetoface/messaginglib.php';
require_once $CFG->dirroot.'/mod/facetoface/notification/lib.php';
require_once($CFG->libdir.'/completionlib.php');
require_once($CFG->dirroot . '/mod/facetoface/room/lib.php');
require_once($CFG->dirroot . '/mod/facetoface/asset/lib.php');
require_once($CFG->dirroot . '/mod/facetoface/libdeprecated.php');
require_once($CFG->dirroot . '/mod/facetoface/classes/event_time.php');

/**
 * Definitions for setting notification types
 */
/**
 * Utility definitions
 */
define('MDL_F2F_NONE',          0);
define('MDL_F2F_TEXT',          2);
define('MDL_F2F_BOTH',          3);
define('MDL_F2F_INVITE',        4);
define('MDL_F2F_CANCEL',        8);

/**
 * Definitions for use in forms
 */
define('MDL_F2F_INVITE_BOTH',        7);     // Send a copy of both 4+1+2
define('MDL_F2F_INVITE_TEXT',        6);     // Send just a plain email 4+2
define('MDL_F2F_INVITE_ICAL',        5);     // Send just a combined text/ical message 4+1
define('MDL_F2F_CANCEL_BOTH',        11);    // Send a copy of both 8+2+1
define('MDL_F2F_CANCEL_TEXT',        10);    // Send just a plan email 8+2
define('MDL_F2F_CANCEL_ICAL',        9);     // Send just a combined text/ical message 8+1

// Custom field related constants
define('CUSTOMFIELD_DELIMITER', '##SEPARATOR##');
define('CUSTOMFIELD_TYPE_TEXT',        0);
define('CUSTOMFIELD_TYPE_SELECT',      1);
define('CUSTOMFIELD_TYPE_MULTISELECT', 2);

// Custom field reserved shortnames.
define('CUSTOMFIELD_BUILDING', 'building');
define('CUSTOMFIELD_LOCATION', 'location');
define('CUSTOMFIELD_CANCELNOTE', 'cancellationnote');
define('CUSTOMFIELD_SIGNUPNOTE', 'signupnote');

define('F2F_CAL_NONE',      0);
define('F2F_CAL_COURSE',    1);
define('F2F_CAL_SITE',      2);

// Define bulk attendance options
define('MDL_F2F_SELECT_ALL', 10);
define('MDL_F2F_SELECT_NONE', 20);
define('MDL_F2F_SELECT_SET', 30);
define('MDL_F2F_SELECT_NOT_SET', 40);

// Define events displayed on course page settings
define('MDL_F2F_MAX_EVENTS_ON_COURSE', 18);
define('MDL_F2F_DEFAULT_EVENTS_ON_COURSE', 6);

global $F2F_SELECT_OPTIONS;
$F2F_SELECT_OPTIONS = array(
    MDL_F2F_SELECT_NONE    => get_string('selectnoneop', 'facetoface'),
    MDL_F2F_SELECT_ALL     => get_string('selectallop', 'facetoface'),
    MDL_F2F_SELECT_SET     => get_string('selectsetop', 'facetoface'),
    MDL_F2F_SELECT_NOT_SET => get_string('selectnotsetop', 'facetoface')
);

// Define custom field array for reserved shortnames.
global $F2F_CUSTOMFIELD_RESERVED;
$F2F_CUSTOMFIELD_RESERVED = [
    'facetofaceroom' => ['text' => CUSTOMFIELD_BUILDING, 'location' => CUSTOMFIELD_LOCATION],
    'facetofacesignup' => ['text' => CUSTOMFIELD_SIGNUPNOTE],
    'facetofacecancellation' => ['text' => CUSTOMFIELD_CANCELNOTE]
];

/**
 * Obtains the automatic completion state for this face to face activity based on any conditions
 * in face to face settings.
 *
 * @param object $course Course
 * @param object $cm Course-module
 * @param int $userid User ID
 * @param bool $type Type of comparison (or/and; can be used as return value if no conditions)
 * @return bool True if completed, false if not. (If no conditions, then return
 *   value depends on comparison type)
 */
function facetoface_get_completion_state($course, $cm, $userid, $type) {
    global $CFG, $DB;

    require_once($CFG->libdir . '/completionlib.php');

    $result = $type;

    // Get face to face.
    $sql = "SELECT f.*, cm.completion, cm.completionview
              FROM {facetoface} f
        INNER JOIN {course_modules} cm
                ON cm.instance = f.id
               AND cm.course = f.course
        INNER JOIN {modules} m
                ON m.id = cm.module
             WHERE m.name='facetoface'
               AND f.id = :fid";
    $params = array('fid' => $cm->instance);
    if (!$facetoface = $DB->get_record_sql($sql, $params)) {
        print_error('cannotfindfacetoface');
    }

    // Only check for existence of tracks and return false if completionstatusrequired.
    // This means that if only view is required we don't end up with a false state.
    if ($facetoface->completionstatusrequired) {
        $completionstatusrequired = json_decode($facetoface->completionstatusrequired, true);
        if (!empty($completionstatusrequired)) {
            list($insql, $inparams) = $DB->get_in_or_equal(array_keys($completionstatusrequired));
            // Get user's latest face to face status.
            $sql = "SELECT f2fss.id AS signupstatusid, f2fss.statuscode, f2fsd.timefinish, f2fs.archived
                FROM {facetoface_sessions} f2fses
                LEFT JOIN {facetoface_signups} f2fs ON (f2fs.sessionid = f2fses.id)
                LEFT JOIN {facetoface_signups_status} f2fss ON (f2fss.signupid = f2fs.id AND f2fss.superceded = 0)
                LEFT JOIN {facetoface_sessions_dates} f2fsd ON (f2fsd.sessionid = f2fses.id)
                WHERE f2fses.facetoface = ? AND f2fs.userid = ?
                  AND f2fss.statuscode $insql
                ORDER BY f2fsd.timefinish DESC";
            $params = array_merge(array($facetoface->id, $userid), $inparams);
            $status = $DB->get_record_sql($sql, $params, IGNORE_MULTIPLE);
            $newstate = false;
            if ($status && !$status->archived) {
                $newstate = true;
                // Tell completion_criteria_activity::review exact time of completion, otherwise it will use time of review run.
                $cm->timecompleted = $status->timefinish;
            }
            $result = completion_info::aggregate_completion_states($type, $result, $newstate);
        }
    }
    return $result;
}

/**
 * Given an object containing all the necessary data, (defined by the
 * form in mod.html) this function will create a new instance and
 * return the id number of the new instance.
 */
function facetoface_add_instance($facetoface) {
    global $DB;

    $facetoface->timemodified = time();

    if ($facetoface->id = $DB->insert_record('facetoface', $facetoface)) {
        facetoface_grade_item_update($facetoface);
    }

    //update any calendar entries
    $seminar = new \mod_facetoface\seminar($facetoface->id);
    $seminarevents = \mod_facetoface\seminar_event_list::form_seminar($seminar);
    foreach ($seminarevents as $seminarevent) {
        \mod_facetoface\calendar::update_entries($seminarevent);
    }

    list($defaultnotifications, $missingtemplates) = facetoface_get_default_notifications($facetoface->id);

    // Create default notifications for activity.
    foreach ($defaultnotifications as $notification) {
        $notification->save();
    }

    if (!empty($missingtemplates)) {
        $message = get_string('error:notificationtemplatemissing', 'facetoface') . html_writer::empty_tag('br');

        // Loop through error items and create a message to send.
        foreach ($missingtemplates as $template) {
            $missingtemplate = get_string('template'.$template, 'facetoface');
            $message .= $missingtemplate . html_writer::empty_tag('br');
        }

        totara_set_notification($message);
    }

    return $facetoface->id;
}

/**
 * Given an object containing all the necessary data, (defined by the
 * form in mod.html) this function will update an existing instance
 * with new data.
 * @param stdClass $facetoface
 * @param mod_facetoface_mod_form $mform
 */
function facetoface_update_instance($facetoface, $mform = null) {
    global $DB;

    $facetoface->id = $facetoface->instance;
    $previousapproval = $DB->get_field('facetoface', 'approvaltype', array('id' => $facetoface->id));

    if (!$DB->update_record('facetoface', $facetoface)) {
        return false;
    }

    facetoface_grade_item_update($facetoface);

        //Get time.
        $now = time();


    $seminar = new seminar($facetoface->id);

    foreach ($seminar->get_events() as $seminarevent) {
        /**
         * @var seminar_event $seminarevent
         */
        \mod_facetoface\calendar::update_entries($seminarevent);
        $helper = new \mod_facetoface\attendees_helper($seminarevent);

        // If manager changed from approval required to not
        if ($facetoface->approvaltype != $previousapproval) {
            $status = [
                signup\state\requested::get_code(),
                signup\state\requestedrole::get_code(),
                signup\state\requestedadmin::get_code()
            ];
            $pending = $helper->get_attendees_with_codes($status);
            core_collator::asort_objects_by_property($pending, 'timecreated', core_collator::SORT_NUMERIC);

            foreach ($pending as $attendee) {
                $signup = new signup($attendee->submissionid);
                $signup->set_actorid($signup->get_userid());
                $state = $signup->get_state();
                if ($state->can_switch(signup\state\booked::class, signup\state\waitlisted::class)) {
                    $signup->switch_state(signup\state\booked::class, signup\state\waitlisted::class);
                } else if (!$seminarevent->is_started()) {
                    // Requested state for "Manager approval" and "Role approval" will not change state,
                    // however it needs messages to be resent:
                    if ($facetoface->approvaltype == seminar::APPROVAL_MANAGER) {
                        notice_sender::request_manager($signup);
                    } else if ($facetoface->approvaltype == seminar::APPROVAL_ROLE) {
                        notice_sender::request_role($signup);
                    }
                }
            }
        }
    }
    return true;
}

/**
 * Given an ID of an instance of this module, this function will
 * permanently delete the instance and any data that depends on it.
 */
function facetoface_delete_instance($id) {
    global $DB;

    $seminar = new \mod_facetoface\seminar($id);
    if (!$seminar->exists()) {
        return false;
    }

    $result = true;
    $transaction = $DB->start_delegated_transaction();

    $seminar->delete();

    $transaction->allow_commit();
    return $result;
}

/**
 * Get a grade for the given user from the gradebook.
 *
 * @param integer $userid        ID of the user
 * @param integer $courseid      ID of the course
 * @param integer $facetofaceid  ID of the Face-to-face activity
 *
 * @return object String grade and the time that it was graded
 */
function facetoface_get_grade($userid, $courseid, $facetofaceid) {

    $ret = new stdClass();
    $ret->grade = 0;
    $ret->dategraded = 0;

    $grading_info = grade_get_grades($courseid, 'mod', 'facetoface', $facetofaceid, $userid);
    if (!empty($grading_info->items)) {
        $ret->grade = $grading_info->items[0]->grades[$userid]->str_grade;
        $ret->dategraded = $grading_info->items[0]->grades[$userid]->dategraded;
    }

    return $ret;
}

/**
 * Return all user fields to include in exports
 *
 * @param bool $reset If true the user fields static cache is reset
 */
function facetoface_get_userfields(bool $reset = false) {
    global $CFG, $DB;
    require_once($CFG->dirroot . '/user/lib.php');

    static $userfields = null;
    if ($userfields === null || $reset) {
        $userfields = array();

        $fieldnames = array('firstname', 'lastname', 'email', 'city',
                            'idnumber', 'institution', 'department', 'address');
        if (!empty($CFG->facetoface_export_userprofilefields)) {
            $fieldnames = array_map('trim', explode(',', $CFG->facetoface_export_userprofilefields));
        }

        $allowed_fields = user_get_default_fields();
        // Only the fields in the user table will work. Custom fields are dealt with separately.
        $allowed_fields = array_diff($allowed_fields, ['profileimageurlsmall', 'profileimageurlsmall', 'customfields', 'groups', 'roles', 'preferences', 'enrolledcourses']);
        $fieldnames = array_intersect($fieldnames, $allowed_fields);

        foreach ($fieldnames as $shortname) {
            if (get_string_manager()->string_exists($shortname, 'moodle')) {
                $userfields[$shortname] = get_string($shortname);
            } else {
                $userfields[$shortname] = $shortname;
            }
        }

        // Add custom fields.
        if (!empty($CFG->facetoface_export_customprofilefields)) {
            $customfields = array_map('trim', explode(',', $CFG->facetoface_export_customprofilefields));
            list($insql, $params) = $DB->get_in_or_equal($customfields);
            $sql = 'SELECT '.$DB->sql_concat("'customfield_'", 'f.shortname').' AS shortname, f.name
                FROM {user_info_field} f
                JOIN {user_info_category} c ON f.categoryid = c.id
                WHERE f.shortname '.$insql.'
                ORDER BY c.sortorder, f.sortorder';

            $customfields = $DB->get_records_sql_menu($sql, $params);
            if (!empty($customfields)) {
                $userfields = array_merge($userfields, $customfields);
            }
        }
    }

    return $userfields;
}

/**
 * Called when viewing course page.
 *
 * @param cm_info $coursemodule
 */
function facetoface_cm_info_view(cm_info $coursemodule) {
    global $USER, $DB, $PAGE;
    $output = '';

    if (!($facetoface = $DB->get_record('facetoface', array('id' => $coursemodule->instance)))) {
        return null;
    }
    $seminar = new seminar();
    $seminar->map_instance($facetoface);
    $coursemodule->set_name($seminar->get_name());

    $contextmodule = context_module::instance($coursemodule->id);
    if (!has_capability('mod/facetoface:view', $contextmodule)) {
        return null; // Not allowed to view this activity.
    }
    // Can view attendees.
    $viewattendees = has_capability('mod/facetoface:viewattendees', $contextmodule);
    $editevents = has_capability('mod/facetoface:editevents', $contextmodule);
    // Can see "view all sessions" link even if activity is hidden/currently unavailable.
    $iseditor = has_any_capability(array('mod/facetoface:viewattendees', 'mod/facetoface:editevents',
                                        'mod/facetoface:addattendees', 'mod/facetoface:addattendees',
                                        'mod/facetoface:takeattendance'), $contextmodule);
    // Other variables that will be required by calls further down to print_session_list_table.
    $displaytimezones = get_config(null, 'facetoface_displaysessiontimezones');
    $reserveinfo = array();

    $timenow = time();

    $strviewallsessions = get_string('viewallsessions', 'facetoface');
    $sessions_url = new moodle_url('/mod/facetoface/view.php', array('f' => $facetoface->id));
    $htmlviewallsessions = html_writer::link($sessions_url, s($strviewallsessions), array('class' => 'f2fsessionlinks f2fviewallsessions', 'title' => $strviewallsessions));

    $interest = \mod_facetoface\interest::from_seminar($seminar);
    $alreadydeclaredinterest = $interest->is_user_declared();
    $declareinterest_enable = $alreadydeclaredinterest || $interest->can_user_declare();
    $declareinterest_label = $alreadydeclaredinterest ? get_string('declareinterestwithdraw', 'facetoface') : get_string('declareinterest', 'facetoface');
    $declareinterest_url = new moodle_url('/mod/facetoface/interest.php', array('f' => $facetoface->id));
    $declareinterest_link = html_writer::link($declareinterest_url, s($declareinterest_label), array('class' => 'f2fsessionlinks f2fviewallsessions', 'title' => $declareinterest_label));

    if ($seminar->has_unarchived_signups()) {
        // Get user signed up for the instance.
        $signups = signup_list::user_active_signups_within_seminar($USER->id, $seminar->get_id())->to_array();

        if (!$seminar->get_multiplesessions()) {
            // First signup only.
            $signups = [array_shift($signups)];
        }

        $sessions = [];
        foreach ($signups as $signup) {
            $seminarevent = $signup->get_seminar_event();
            if ($seminarevent->is_over($timenow) || $seminarevent->get_cancelledstatus()) {
                // We do not want to display those events that are either cancelled or over here at course page.
                continue;
            }

            $signupstatus = $signup->get_signup_status();
            if (null === $signupstatus) {
                // At this point, if signup status does not exist, we know that the database storage is being messed up.
                debugging(
                    "No signup status found for specific signup {$signup->get_id()}",
                    DEBUG_DEVELOPER
                );

                continue;
            }

            $state = $signup->get_state();

            // Workaround to build a dummy data of signup/submission.
            $submission = $signup->to_record();

            $submission->facetoface = $seminarevent->get_facetoface();
            $submission->cancelledstatus = $seminarevent->get_cancelledstatus();
            $submission->timemodified = $seminarevent->get_timemodified();
            $submission->timecreated = $signupstatus->get_timecreated();
            $submission->timegraded = $submission->timecreated;
            $submission->statuscode = $state::get_code();
            $submission->timecancelled = 0;
            $submission->mailedconfirmation = 0;

            $session = $seminarevent->to_record();

            $session->mintimestart = $seminarevent->get_mintimestart();
            $session->maxtimefinish = $seminarevent->get_maxtimefinish();
            $session->sessiondates = $seminarevent->get_sessions()->to_records();
            $session->cntdates = count($session->sessiondates);
            $session->bookedsession = $submission;

            $sessions[$session->id] = $session;
        }

        // If the user can sign up for multiple events, we should show all upcoming events in this seminar.
        // Otherwise it doesn't make sense to do so because the user has already signedup for the instance.
        if ($seminar->get_multiplesessions()) {
            // If state restrictions are enabled and not met, only display the current signup.
            $checkremaining = true;
            $restrictions = $seminar->get_multisignup_states();
            if (!empty($restrictions)) {
                foreach ($signups as $signup) {
                    $state = $signup->get_state();
                    $code = $state::get_code();
                    if (empty($restrictions[$code])) {
                        // We have a sign-up who's current state is not matching restrictions.
                        // Display that sign-up and nothing else.
                        // $sessions[$session->id] = $session; // This should already be there (see above) just skip the next bit.
                        $checkremaining = false;
                    }
                }
            }

            $maximum = $seminar->get_multisignup_maximum();
            if ($checkremaining && (empty($maximum) || count($signups) < $maximum)) {
                $query = new query($seminar);
                $query->with_sortorder(new future_sortorder());
                $seminarevents = seminar_event_list::from_query($query);

                $numberofeventstodisplay = isset($facetoface->display) ? (int)$facetoface->display : 0;
                $index = 0;

                /** @var seminar_event $seminarevent */
                foreach ($seminarevents as $seminarevent) {
                    $id = $seminarevent->get_id();
                    if (array_key_exists($id, $sessions)) {
                        continue;
                    }

                    if ($seminarevent->is_over($timenow) || $seminarevent->get_cancelledstatus()) {
                        continue;
                    }

                    // Displaying the seminar's event base on the config ($facetoface->display) within seminar setting.
                    // Break the loop, if the number of events ($index) reaches to the number from config ($numberofeventstodisplay)
                    if ($index == $numberofeventstodisplay) {
                        break;
                    }

                    $session = $seminarevent->to_record();
                    $session->mintimestart = $seminarevent->get_mintimestart();
                    $session->maxtimefinish = $seminarevent->get_maxtimefinish();
                    $session->sessiondates = $seminarevent->get_sessions()->to_records();
                    $session->cntdates = count($session->sessiondates);

                    $sessions[$id] = $session;
                    $index++;
                }
            }
        }

        if (!empty($facetoface->managerreserve)) {
            // Include information about reservations when drawing the list of sessions.
            $reserveinfo = reservations::can_reserve_or_allocate($seminar, $sessions, $contextmodule);
        }

        /** @var \mod_facetoface_renderer $f2frenderer */
        $f2frenderer = $PAGE->get_renderer('mod_facetoface');
        $f2frenderer->setcontext($contextmodule);
        $output .= $f2frenderer->print_session_list_table(
            $sessions,
            $viewattendees,
            $editevents,
            $displaytimezones,
            $reserveinfo,
            $PAGE->url,
            true,
            false
        );

        // Add "view all sessions" row to table.
        $output .= $htmlviewallsessions;

        if ($declareinterest_enable) {
            $output .= $declareinterest_link;
        }
    } else {
        // If user does not have signed-up, then start querying the list of seminar_events, and displaying it on screen.
        $query = new query($seminar);
        $query->with_sortorder(new future_sortorder());
        $seminarevents = seminar_event_list::from_query($query);

        if (!$seminarevents->is_empty()) {
            $sessions = [];

            if ($facetoface->display > 0) {
                /** @var seminar_event $seminarevent */
                foreach ($seminarevents as $seminarevent) {
                    if ($seminarevent->is_over() || $seminarevent->get_cancelledstatus()) {
                        // We only want upcoming sessions (or those with no date set).
                        // For now, we've cut down the sessions to loop through to just those displayed.
                        continue;
                    }

                    $id = $seminarevent->get_id();

                    $session = $seminarevent->to_record();
                    $session->mintimestart = $seminarevent->get_mintimestart();
                    $session->maxtimefinish = $seminarevent->get_maxtimefinish();
                    $session->sessiondates = $seminarevent->get_sessions()->to_records();
                    $session->cntdates = count($session->sessiondates);
                    $sessions[$id] = $session;
                }

                // Limit number of sessions display. $sessions is in order of start time.
                $displaysessions = array_slice($sessions, 0, $facetoface->display, true);

                if (!empty($facetoface->managerreserve)) {
                    // Include information about reservations when drawing the list of sessions.
                    $reserveinfo = reservations::can_reserve_or_allocate($seminar, $displaysessions, $contextmodule);
                }

                /** @var mod_facetoface_renderer $f2frenderer */
                $f2frenderer = $PAGE->get_renderer('mod_facetoface');
                $f2frenderer->setcontext($contextmodule);
                $output .= $f2frenderer->print_session_list_table(
                    $displaysessions,
                    $viewattendees,
                    $editevents,
                    $displaytimezones,
                    $reserveinfo,
                    $PAGE->url,
                    true,
                    false
                );

                $output .= ($iseditor || ($coursemodule->visible && $coursemodule->available)) ? $htmlviewallsessions : $strviewallsessions;
                if (($iseditor || ($coursemodule->visible && $coursemodule->available)) && $declareinterest_enable) {
                    $output .= $declareinterest_link;
                }
            } else {
                // Show only name if session display is set to zero.
                $content = html_writer::tag('span', $htmlviewallsessions, array('class' => 'f2fsessionnotice f2factivityname'));
                $coursemodule->set_content($content, true);
                return;
            }
        } else if (has_capability('mod/facetoface:viewemptyactivities', $contextmodule)) {
            $content = html_writer::tag('span', $htmlviewallsessions, array('class' => 'f2fsessionnotice f2factivityname'));
            $coursemodule->set_content($content, true);
            return;
        } else {
            // Nothing to display to this user.
            $coursemodule->set_content('');
            return;
        }
    }

    $coursemodule->set_content($output, true);
}

/**
 * Update grades by firing grade_updated event
 *
 * @param object $facetoface null means all facetoface activities
 * @param int $userid specific user only, 0 mean all (not used here)
 * @param bool $defaultifnone If a single user is specified and $defaultifnone is true, a grade item with a default rawgrade will be inserted
 *                            the default rawgrade will be recalculated based on $facetoface->eventgradingmethod.
 */
function facetoface_update_grades($facetoface=null, $userid=0, $defaultifnone = true) {
    global $DB;

    if (($facetoface != null) && $userid && $defaultifnone) {
        $grade = new stdClass();
        $grade->userid   = $userid;
        $grade->rawgrade = \mod_facetoface\signup_helper::compute_final_grade($facetoface, $userid);
        facetoface_grade_item_update($facetoface, $grade);
    } else if ($facetoface != null) {
        facetoface_grade_item_update($facetoface);
    } else {
        $sql = "SELECT f.*, cm.idnumber as cmidnumber
                  FROM {facetoface} f
                  JOIN {course_modules} cm ON cm.instance = f.id
                  JOIN {modules} m ON m.id = cm.module
                 WHERE m.name='facetoface'";
        if ($rs = $DB->get_recordset_sql($sql)) {
            foreach ($rs as $facetoface) {
                facetoface_grade_item_update($facetoface);
            }
            $rs->close();
        }
    }
    return true;
}

/**
 * Create grade item for given Face-to-face session
 *
 * @param int facetoface  Face-to-face activity (not the session) to grade
 * @param mixed grades    grades objects or 'reset' (means reset grades in gradebook)
 * @return int 0 if ok, error code otherwise
 */
function facetoface_grade_item_update($facetoface, $grades=NULL) {
    global $CFG, $DB;

    if (!isset($facetoface->cmidnumber)) {

        $sql = "SELECT cm.idnumber as cmidnumber
                  FROM {course_modules} cm
                  JOIN {modules} m ON m.id = cm.module
                 WHERE m.name='facetoface' AND cm.instance = ?";
        $facetoface->cmidnumber = $DB->get_field_sql($sql, array($facetoface->id));
    }

    $params = array('itemname' => $facetoface->name,
                    'idnumber' => $facetoface->cmidnumber);

    $params['gradetype'] = GRADE_TYPE_VALUE;
    $params['grademin']  = 0;
    $params['gradepass'] = 100;
    $params['grademax']  = 100;

    if ($grades  === 'reset') {
        $params['reset'] = true;
        $grades = NULL;
    }

    $retcode = grade_update('mod/facetoface', $facetoface->course, 'mod', 'facetoface',
                            $facetoface->id, 0, $grades, $params);
    return ($retcode === GRADE_UPDATE_OK);
}

/**
 * A list of actions in the logs that indicate view activity for participants
 */
function facetoface_get_view_actions() {
    return array('view', 'view all');
}

/**
 * A list of actions in the logs that indicate post activity for participants
 */
function facetoface_get_post_actions() {
    return array('cancel booking', 'signup');
}

/**
 * Return a small object with summary information about what a user
 * has done with a given particular instance of this module (for user
 * activity reports.)
 *
 * $return->time = the time they did it
 * $return->info = a short text description
 *
 * Core component callback
 *
 * @param stdClass $course     DB record of course
 * @param stdClass $user       DB record of user who is being viewed.
 * @param cm_info  $mod        DB record of course module
 * @param stdClass $facetoface DB record of mod_facetoface
 *
 * @return stdClass
 */
function facetoface_user_outline($course, $user, $mod, $facetoface) {
    $seminar = new seminar();
    $seminar->map_instance($facetoface);

    $result = new stdClass;

    $grade = facetoface_get_grade($user->id, $course->id, $seminar->get_id());

    if ($grade->grade > 0) {
        $result->info = get_string('grade') . ': ' . $grade->grade;
        $result->time = $grade->dategraded;
    } else {
        $signups = signup_list::user_active_signups_within_seminar($user->id, $seminar->get_id());

        if (!$signups->is_empty()) {
            if ($seminar->get_multiplesessions() && count($signups) > 1) {
                $result->info = get_string('usersignedupmultiple', 'mod_facetoface', count($signups));
                $result->time = 0;

                /** @var signup $signup */
                foreach ($signups as $signup) {
                    $signupstatus = $signup->get_signup_status();
                    if (null === $signupstatus) {
                        // Database is messed up. Just debugging it.
                        debugging("No signup status found for signup: '{$signup->get_id()}'", DEBUG_DEVELOPER);
                        continue;
                    }

                    if ($signupstatus->get_timecreated() > $result->time) {
                        $result->time = $signupstatus->get_timecreated();
                    }
                }
            } else {
                $result->info = get_string('usersignedup', 'mod_facetoface');

                // Only display the first signup.
                $signup = $signups->get_first();
                $signupstatus = $signup->get_signup_status();

                if (null === $signupstatus) {
                    // Database is messed up. Just debugging it.
                    debugging("No signup status found for signup: '{$signup->get_id()}'", DEBUG_DEVELOPER);
                } else {
                    $result->time = $signupstatus->get_timecreated();
                }
            }
        } else {
            $result->info = get_string('usernotsignedup', 'facetoface');
        }
    }

    return $result;
}

/**
 * Print a detailed representation of what a user has done with a
 * given particular instance of this module (for user activity
 * reports).
 *
 * Core component callback
 *
 * @param stdClass $course
 * @param stdClass $user
 * @param cm_info  $mod
 * @param stdClass $facetoface
 *
 * @return bool
 */
function facetoface_user_complete($course, $user, $mod, $facetoface) {
    $grade = facetoface_get_grade($user->id, $course->id, $facetoface->id);
    
    $signups = signup_list::user_signups_within_seminar($user->id, $facetoface->id);
    if (!$signups->is_empty()) {
        echo get_string('grade').': '.$grade->grade.html_writer::empty_tag('br');

        if ($grade->dategraded > 0) {
            $timegraded = trim(userdate($grade->dategraded, get_string('strftimedatetime')));
            echo '('.format_string($timegraded).')'.html_writer::empty_tag('br');
        }

        echo html_writer::empty_tag('br');

        /** @var signup $signup */
        foreach ($signups as $signup) {
            $signupstatus = $signup->get_signup_status();
            if (null === $signupstatus) {
                // Database is messed up. Just debugging it.
                debugging("There is no signup status found for signup: {$signup->get_id()}", DEBUG_DEVELOPER);
                continue;
            }

            $timesignedup = trim(userdate($signupstatus->get_timecreated(), get_string('strftimedatetime')));
            echo get_string(
                'usersignedupon',
                'mod_facetoface',
                format_string($timesignedup) . html_writer::empty_tag('br')
            );
        }
    } else {
        echo get_string('usernotsignedup', 'facetoface');
    }

    return true;
}

/**
 * Return the values stored for all custom fields in the given session.
 *
 * @param integer $sessionid  ID of facetoface_sessions record
 * @returns array Indexed by field shortnames
 */
function facetoface_get_customfielddata($sessionid) {

    $out = [];
    $item = (object)['id' => $sessionid];
    $out['sess'] = customfield_get_data($item, 'facetoface_session', 'facetofacesession', false);

    // A session can have more than one room if there are more than one date in the session and different
    // rooms are used on different dates
    $rooms = \mod_facetoface\room_list::get_event_rooms($sessionid);
    $out['room'] = array();
    foreach ($rooms as $room) {
        /**
         * @var \mod_facetoface\room $room
         */
        $out['room'] = array_merge_recursive($out['room'], customfield_get_data($room->to_record(), 'facetoface_room', 'facetofaceroom', false));
    }

    // We want rooms values to be in 1 comma separated string
    foreach ($out['room'] as $key => $vals) {
        if (is_array($vals)) {
            $out['room'][$key] = implode(', ', $vals);
        }
    }
    return $out;
}

/**
 * Returns all other caps used in module
 * @return array
 */
function facetoface_get_extra_capabilities() {
    return array('moodle/site:viewfullnames');
}

/**
 * @param string $feature FEATURE_xx constant for requested feature
 * @return mixed True if module supports feature, null if doesn't know
 */
function facetoface_supports($feature) {
    switch($feature) {
        case FEATURE_BACKUP_MOODLE2:          return true;
        case FEATURE_GRADE_HAS_GRADE:         return true;
        case FEATURE_COMPLETION_TRACKS_VIEWS: return true;
        case FEATURE_ARCHIVE_COMPLETION:      return true;
        case FEATURE_COMPLETION_HAS_RULES:    return true;
        case FEATURE_COMPLETION_TIME_IN_TIMECOMPLETED: return true;

        default: return null;
    }
}

/**
 * Adds module specific settings to the settings block
 *
 * @param settings_navigation $settings The settings navigation object
 * @param navigation_node $facetofacenode The node to add module settings to
 */
function facetoface_extend_settings_navigation(settings_navigation $settings, navigation_node $facetofacenode) {
    global $PAGE, $DB;

    $mode = optional_param('mode', '', PARAM_ALPHA);
    $hook = optional_param('hook', 'ALL', PARAM_CLEAN);

    $context = context_module::instance($PAGE->cm->id);
    if (has_capability('moodle/course:manageactivities', $context)) {
        $facetofacenode->add(get_string('notifications', 'facetoface'), new moodle_url('/mod/facetoface/notification/index.php', array('update' => $PAGE->cm->id)), navigation_node::TYPE_SETTING);
    }

    $facetoface = $DB->get_record('facetoface', array('id' => $PAGE->cm->instance), '*', MUST_EXIST);
    if ($facetoface->declareinterest && has_capability('mod/facetoface:viewinterestreport', $context)) {
        $facetofacenode->add(get_string('declareinterestreport', 'facetoface'), new moodle_url('/mod/facetoface/reports/interests.php', array('facetofaceid' => $PAGE->cm->instance)), navigation_node::TYPE_SETTING);
    }
}

/**
 * Main calendar hook for filtering f2f events (if necessary)
 *
 * @param array $events from the events table
 * @uses $SESSION->calendarfacetofacefilter - contains an assoc array of filter fieldids and vals
 *
 * @return void
 */
function facetoface_filter_calendar_events(&$events) {
    global $SESSION;
    if (empty($SESSION->calendarfacetofacefilter)) {
        return;
    }
    $filters = $SESSION->calendarfacetofacefilter;
    foreach ($events as $eid => $event) {
        $event = new calendar_event($event);
        if ($event->modulename != 'facetoface') {
            continue;
        }

        $cfield_vals = facetoface_get_customfielddata($event->uuid);

        foreach ($filters as $type => $filter) {
            foreach ($filter as $shortname => $fval) {
                if (empty($fval) || $fval == 'all') {  // ignore empty filters
                    continue;
                }
                if (empty($cfield_vals[$type][$shortname])) {
                    // no reason comparing empty values :D
                    unset($events[$eid]);
                    break;
                }
                $filterval = core_text::strtolower($fval);
                $fielddval = core_text::strtolower($cfield_vals[$type][$shortname]);
                if (core_text::strpos($fielddval, $filterval) === false) {
                    unset($events[$eid]);
                    break;
                }
            }
        }
    }
}

/**
 * Main calendar hook for settinging f2f calendar filters
 *
 * @uses $SESSION->calendarfacetofacefilter - initialises assoc array of filter fieldids and vals
 *
 * @return void
 */
function facetoface_calendar_set_filter() {
    global $SESSION;

    $fieldsall = \mod_facetoface\calendar::get_customfield_filters();

    $SESSION->calendarfacetofacefilter = array();
    foreach ($fieldsall as $type => $fields) {
        if (!isset($SESSION->calendarfacetofacefilter[$type])) {
            $SESSION->calendarfacetofacefilter[$type] = array();
        }
        foreach ($fields as $field) {
            $fieldname = "field_{$type}_{$field->shortname}";
            $SESSION->calendarfacetofacefilter[$type][$field->shortname] = optional_param($fieldname, '', PARAM_TEXT);
        }
    }
}

/**
 * Serves the facetoface and sessions details.
 *
 * @param stdClass $course course object
 * @param cm_info $cm course module object
 * @param context $context context object
 * @param string $filearea file area
 * @param array $args extra arguments
 * @param bool $forcedownload whether or not force download
 * @param array $options additional options affecting the file serving
 * @return bool false if file not found, does not return if found - just send the file
 */
function facetoface_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options=array()) {
    global $DB;

    if ($context->contextlevel == CONTEXT_SYSTEM && ($filearea === 'room' || $filearea === 'asset')) {
        // NOTE: we do not know where is the room and asset description visible,
        //       this means we cannot do any strict access control, bad luck.
        $fs = get_file_storage();
        $relativepath = implode('/', $args);
        $fullpath = "/$context->id/mod_facetoface/$filearea/$relativepath";
        if (!$file = $fs->get_file_by_hash(sha1($fullpath))) {
            return false;
        }
        // This function will stop code.
        send_stored_file($file, 360, 0, true, $options);
    }

    $sessionid = (int)array_shift($args);
    if (!$DB->get_record('facetoface_sessions', array('id' => $sessionid, 'facetoface' => $cm->instance))) {
        return false;
    }

    $fileinstance = function() use($context, $filearea, $args, $sessionid) {
        $fs = get_file_storage();
        $relativepath = implode('/', $args);
        $fullpath = "/$context->id/mod_facetoface/$filearea/$sessionid/$relativepath";
        if (!$file = $fs->get_file_by_hash(sha1($fullpath))) {
            return false;
        }
        return $file;
    };

    if ($context->contextlevel != CONTEXT_MODULE || $filearea !== 'session') {
        return false;
    }

    // NOTE: we do not know where is the session details text displayed,
    //       this means we cannot do any strict access control, bad luck.
    $storedfile = $fileinstance();
    send_stored_file($storedfile, 360, 0, true, $options);
}

/**
 * Removes grades and resets completion
 *
 * @global object $CFG
 * @global object $DB
 * @param int $userid
 * @param int $courseid
 * @return boolean
 */
function facetoface_archive_completion($userid, $courseid, $windowopens = NULL) {
    global $DB, $CFG;

    require_once($CFG->libdir . '/completionlib.php');

    if (!isset($windowopens)) {
        $windowopens = time();
    }

    $course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);
    $completion = new completion_info($course);

    // All facetoface sessions with this course and user.
    $sql = "SELECT f.*
            FROM {facetoface} f
            WHERE f.course = :courseid
            AND EXISTS (SELECT su.id
                        FROM {facetoface_sessions} s
                        JOIN {facetoface_signups} su ON su.sessionid = s.id AND su.userid = :userid
                        WHERE s.facetoface = f.id)";
    $facetofaces = $DB->get_records_sql($sql, array('courseid' => $courseid, 'userid' => $userid));
    foreach ($facetofaces as $facetoface) {
        // Add an archive flag.
        $params = array('facetofaceid' => $facetoface->id, 'userid' => $userid, 'archived' => 1, 'archived2' => 1, 'windowopens' => $windowopens);
        $sql = "UPDATE {facetoface_signups}
                SET archived = :archived
                WHERE userid = :userid
                AND archived <> :archived2
                AND EXISTS (SELECT s.id, MAX(sd.timefinish) as maxfinishtime
                            FROM {facetoface_sessions} s
                            LEFT JOIN {facetoface_sessions_dates} sd ON s.id = sd.sessionid
                            WHERE s.id = {facetoface_signups}.sessionid
                            AND s.facetoface = :facetofaceid
                            AND sd.id IS NOT NULL
                            GROUP BY s.id
                            HAVING MAX(sd.timefinish) <= :windowopens)";
        // NOTE: Timefinish can be, at most, the date/time that the course/cert was completed. In the windowopens check, we
        // do <= rather than < because windowopens may be equal to timefinish when the cert active period is equal to the window
        // period. Luckily, window period cannot be more than the active period, so the window cannot open before timefinish.
        $DB->execute($sql, $params);

        // Reset the grades.
        facetoface_update_grades($facetoface, $userid, true);

        // Set completion to incomplete.
        // Reset viewed.
        $course_module = get_coursemodule_from_instance('facetoface', $facetoface->id, $courseid);
        $completion->set_module_viewed_reset($course_module, $userid);
        // And reset completion, in case viewed is not a required condition.
        $completion->update_state($course_module, COMPLETION_INCOMPLETE, $userid);
        $completion->invalidatecache($courseid, $userid, true);
    }
}

/**
 * Called after each config setting update.
 */
function facetoface_displaysessiontimezones_updated() {

    $seminarevents = \mod_facetoface\seminar_event_list::get_all();
    foreach ($seminarevents as $seminarevent) {
        \mod_facetoface\calendar::update_entries($seminarevent);
    }
}
