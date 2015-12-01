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
 * @author Valerii Kuznetsov <valerii.kuznetsov@totaralms.com>
 * @package mod_facetoface
 */
require_once(dirname(dirname(dirname(dirname(__FILE__)))) . '/config.php');
require_once($CFG->dirroot.'/mod/facetoface/lib.php');
require_once($CFG->dirroot.'/mod/facetoface/attendees/forms.php');

$s      = required_param('s', PARAM_INT); // facetoface session ID
$listid = required_param('listid', PARAM_ALPHANUM); // Session key to list of users to add.

list($session, $facetoface, $course, $cm, $context) = facetoface_get_env_session($s);

// Check essential permissions.
require_login($course, false, $cm);
require_capability('mod/facetoface:removeattendees', $context);

$PAGE->set_context($context);
$PAGE->set_url('/mod/facetoface/attendees/removeconfirm.php', array('s' => $s, 'listid' => $listid));
$PAGE->set_cm($cm);
$PAGE->set_pagelayout('standard');

$list = new \mod_facetoface\bulk_list($listid);

// Selected users.
$userlist = $list->get_user_ids();
if (empty($userlist)) {
    totara_set_notification(get_string('updateattendeesunsuccessful', 'facetoface'),
            new moodle_url('/mod/facetoface/attendees.php', array('s' => $s, 'backtoallsessions' => 1)));
}

$usernamefields = get_all_user_name_fields(true, 'u');
list($idsql, $params) = $DB->get_in_or_equal($userlist, SQL_PARAMS_NAMED);
$params['sessionid'] = $s;
$users = $DB->get_records_sql("
        SELECT u.id, $usernamefields, u.email, u.idnumber, u.username, count(fsid.id) as cntcfdata
        FROM {user} u
        LEFT JOIN {facetoface_signups} fs  ON (fs.userid = u.id AND fs.sessionid = :sessionid)
        LEFT JOIN {facetoface_signups_status} fss ON (fss.signupid = fs.id)
        LEFT JOIN {facetoface_signup_info_data} fsid ON (fsid.facetofacesignupid = fss.id)
        WHERE u.id {$idsql}
        GROUP BY u.id, $usernamefields, u.email, u.idnumber, u.username
        ", $params);

$mform = new removeconfirm_form(null, array('s' => $s, 'listid' => $listid));

$returnurl = new moodle_url('/mod/facetoface/attendees.php', array('s' => $s, 'backtoallsessions' => 1));
if ($mform->is_cancelled()) {
    $list->clean();
    redirect($returnurl);
}

// Get users waiting approval to add to the "already attending" list as we might want to remove them as well.
$waitingapproval = facetoface_get_requests($session->id);

if ($fromform = $mform->get_data()) {
    if (empty($fromform->submitbutton)) {
        print_error('error:unknownbuttonclicked', 'facetoface', $list->get_returnurl());
    }

    if (empty($_SESSION['f2f-bulk-results'])) {
        $_SESSION['f2f-bulk-results'] = array();
    }

    $removed  = array();
    $errors = array();
    // Original booked attendees plus those awaiting approval
    if ($session->datetimeknown) {
        $original = facetoface_get_attendees($session->id, array(MDL_F2F_STATUS_BOOKED, MDL_F2F_STATUS_NO_SHOW,
            MDL_F2F_STATUS_PARTIALLY_ATTENDED, MDL_F2F_STATUS_FULLY_ATTENDED));
    } else {
        $original = facetoface_get_attendees($session->id, array(MDL_F2F_STATUS_WAITLISTED, MDL_F2F_STATUS_BOOKED, MDL_F2F_STATUS_NO_SHOW,
            MDL_F2F_STATUS_PARTIALLY_ATTENDED, MDL_F2F_STATUS_FULLY_ATTENDED));
    }

    // Add those awaiting approval
    foreach ($waitingapproval as $waiting) {
        if (!isset($original[$waiting->id])) {
            $original[$waiting->id] = $waiting;
        }
    }

    // Removing old attendees.
    // Check if we need to remove anyone.
    $attendeestoremove = array_intersect_key($original, $users);
    if (!empty($attendeestoremove)) {
        foreach ($attendeestoremove as $attendee) {
            $result = array();
            $result['id'] = $attendee->id;
            $result['name'] = fullname($attendee);

            if (facetoface_user_cancel($session, $attendee->id, true, $cancelerr)) {
                // Notify the user of the cancellation if the session hasn't started yet
                $timenow = time();
                if ($fromform->notifyuser and !facetoface_has_session_started($session, $timenow)) {
                    $facetoface->ccmanager = $fromform->notifymanager;
                    facetoface_send_cancellation_notice($facetoface, $session, $attendee->id);
                }
                $result['result'] = get_string('removedsuccessfully', 'facetoface');
                $removed[] = $result;
            } else {
                $result['result'] = $cancelerr;
                $errors[] = $result;
            }
        }
    }

    // Log that users were edited.
    if (count($removed) > 0 || count($errors) > 0) {
        \mod_facetoface\event\attendees_updated::create_from_session($session, $context)->trigger();
    }
    $_SESSION['f2f-bulk-results'][$session->id] = array($removed, $errors);

    facetoface_set_bulk_result_notification(array($removed, $errors), 'bulkremove');

    $list->clean();
    redirect($returnurl);
}

$PAGE->set_title(format_string($facetoface->name));
$PAGE->set_heading($course->fullname);

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('removeattendeestep2', 'facetoface'));
echo facetoface_print_session($session, false, true, true, false, 'f2fline');

// Table.
$f2frenderer = $PAGE->get_renderer('mod_facetoface');
echo $f2frenderer->print_user_table($users);

$returnurl = $list->get_returnurl();
echo html_writer::link($returnurl, get_string('changeselectedusers', 'facetoface'), array('class'=>'link-as-button'));
$mform->display();

echo $OUTPUT->footer();