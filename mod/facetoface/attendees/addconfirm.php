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
require_capability('mod/facetoface:addattendees', $context);

$PAGE->set_context($context);
$PAGE->set_url('/mod/facetoface/attendees/addconfirm.php', array('s' => $s, 'listid' => $listid));
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
$users = $DB->get_records_sql("SELECT id, $usernamefields, email, idnumber, username FROM {user} u WHERE id " . $idsql, $params);

$enableattendeenote = $session->availablesignupnote;
$showcustomfields = $enableattendeenote && !$list->has_user_data();

$approvalreqd = facetoface_approval_required($facetoface);
$mform = new addconfirm_form(null, array('s' => $s, 'listid' => $listid, 'approvalreqd' => $approvalreqd,
        'enablecustomfields' => $showcustomfields));

$returnurl = new moodle_url('/mod/facetoface/attendees.php', array('s' => $s, 'backtoallsessions' => 1));
if ($mform->is_cancelled()) {
    $list->clean();
    redirect($returnurl);
}

// Get users waiting approval to add to the "already attending" list as we do not want to add them again.
$waitingapproval = facetoface_get_requests($session->id);

if ($fromform = $mform->get_data()) {
    if (empty($fromform->submitbutton)) {
        print_error('error:unknownbuttonclicked', 'facetoface', $returnurl);
    }

    if (empty($_SESSION['f2f-bulk-results'])) {
        $_SESSION['f2f-bulk-results'] = array();
    }

    $added  = array();
    $errors = array();
    // Original booked attendees plus those awaiting approval
    if ($session->datetimeknown) {
        $original = facetoface_get_attendees($session->id, array(MDL_F2F_STATUS_BOOKED, MDL_F2F_STATUS_NO_SHOW,
            MDL_F2F_STATUS_PARTIALLY_ATTENDED, MDL_F2F_STATUS_FULLY_ATTENDED));
    } else {
        $original = facetoface_get_attendees($session->id, array(MDL_F2F_STATUS_WAITLISTED, MDL_F2F_STATUS_BOOKED, MDL_F2F_STATUS_NO_SHOW,
            MDL_F2F_STATUS_PARTIALLY_ATTENDED, MDL_F2F_STATUS_FULLY_ATTENDED));
    }

    $approvalrequired = !empty($fromform->ignoreapproval) ? APPROVAL_NONE  : $facetoface->approvaltype;

    // Add those awaiting approval
    foreach ($waitingapproval as $waiting) {
        if (!isset($original[$waiting->id])) {
            $original[$waiting->id] = $waiting;
        }
    }

    // Adding new attendees.
    // Check if we need to add anyone.
    $attendeestoadd = array_diff_key($users, $original);
    if (!empty($attendeestoadd)) {
        // Prepare params
        $params = array();
        $params['suppressemail'] = !$fromform->notifyuser;
        // If we selected ignore approval then change the status.
        $params['approvalreqd'] = $approvalrequired;
        // If approval is required then we need to send a request to their manager.
        if ($approvalrequired) {
            $params['ccmanager'] = 1;
        } else {
            $params['ccmanager'] = $fromform->notifymanager;
        }

        foreach ($attendeestoadd as $attendee) {
            $result = facetoface_user_import($course, $facetoface, $session, $attendee->id, $params);
            if ($result['result'] !== true) {
                $errors[] = $result;
                continue;
            } else {
                $result['result'] = get_string('addedsuccessfully', 'facetoface');
                $added[] = $result;
            }

            // Store customfields.
            if ($enableattendeenote) {
                $signupstatus = facetoface_get_attendee($session->id, $attendee->id);
                $customdata = $list->has_user_data() ? (object)$list->get_user_data($attendee->id) : $fromform;
                $customdata->id = $signupstatus->statusid;
                customfield_save_data($customdata, 'facetofacesignup', 'facetoface_signup');
            }
        }
    }

    // Log that users were edited.
    if (count($added) > 0 || count($errors) > 0) {
        \mod_facetoface\event\attendees_updated::create_from_session($session, $context)->trigger();
    }
    $_SESSION['f2f-bulk-results'][$session->id] = array($added, $errors);

    facetoface_set_bulk_result_notification(array($added, $errors));
    $numattendees = facetoface_get_num_attendees($session->id);
    $overbooked = ($numattendees > $session->capacity);
    if ($overbooked) {
        $overbookedmessage = get_string('capacityoverbookedlong', 'facetoface', array('current' => $numattendees, 'maximum' => $session->capacity));
        totara_set_notification($overbookedmessage, null, array('class' => 'notifynotice'));
    }

    $list->clean();
    redirect(new moodle_url('/mod/facetoface/attendees.php', array('s' => $s, 'backtoallsessions' => 1)));
}

$PAGE->set_title(format_string($facetoface->name));
$PAGE->set_heading($course->fullname);

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('addattendeestep2', 'facetoface'));
echo facetoface_print_session($session, false, true, true, false, 'f2fline');

// Table.
$f2frenderer = $PAGE->get_renderer('mod_facetoface');
echo $f2frenderer->print_user_table($users);

$returnurl = $list->get_returnurl();
echo html_writer::link($returnurl, get_string('changeselectedusers', 'facetoface'), array('class'=>'link-as-button'));
$mform->display();

echo $OUTPUT->footer();
