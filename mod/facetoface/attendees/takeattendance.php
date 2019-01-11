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
 * @author Francois Marier <francois@catalyst.net.nz>
 * @author Aaron Barnes <aaronb@catalyst.net.nz>
 * @author Alastair Munro <alastair.munro@totaralms.com>
 * @author Oleg Demeshev <oleg.demeshev@totaralearning.com>
 * @package mod_facetoface
 */

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->dirroot.'/mod/facetoface/lib.php');
require_once($CFG->libdir.'/totaratablelib.php');
require_once($CFG->dirroot . '/totara/core/js/lib/setup.php');

use mod_facetoface\{signup_helper, attendees_list_helper, seminar_event, seminar};
use mod_facetoface\attendance\{attendance_helper, factory};

/**
 * Load and validate base data
 */
// Face-to-face session ID
$s = optional_param('s', 0, PARAM_INT);

// Face-to-face sessiondate Id
$sd = optional_param('sd', 0, PARAM_INT);

// Take attendance
$takeattendance = optional_param('takeattendance', false, PARAM_BOOL);

// Cancel request
$cancelform = optional_param('cancelform', false, PARAM_BOOL);

// Action being performed, a proper default will be set shortly.
// Require for attendees.js
$action = optional_param('action', 'takeattendance', PARAM_ALPHA);

// Only return content
$onlycontent = optional_param('onlycontent', false, PARAM_BOOL);

// Export download.
$download = optional_param('download', '', PARAM_ALPHA);

// If there's no sessionid specified.
if (!$s) {
    attendees_list_helper::process_no_sessionid('takeattendance');
    exit;
}

list($session, $facetoface, $course, $cm, $context) = facetoface_get_env_session($s);
$seminarevent = new seminar_event($s);
$seminar = new seminar($seminarevent->get_facetoface());

require_login($course, false, $cm);

// Setup urls
$baseurl = new moodle_url(
    '/mod/facetoface/attendees/takeattendance.php',
    ['s' => $seminarevent->get_id()]
);

$PAGE->set_context($context);
$PAGE->set_url($baseurl);

[
    $allowed_actions,
    $available_actions,
    $staff,
    $admin_requests,
    $canapproveanyrequest,
    $cancellations,
    $requests,
    $attendees
] = attendees_list_helper::get_allowed_available_actions($seminar, $seminarevent, $context, $session);

$can_view_session = !empty($allowed_actions);
if (!$can_view_session) {
    // If no allowed actions so far.
    $return = new moodle_url('/mod/facetoface/view.php', array('f' => $seminar->get_id()));
    redirect($return);
    die();
}
// $allowed_actions is already set, so we can now know if the current action is allowed.
$actionallowed = in_array($action, $allowed_actions);

/***************************************************************************
 * Handle actions
 */

//Process the submitted data here
if ($formdata = data_submitted()) {
    if (!confirm_sesskey()) {
        print_error('confirmsesskeybad', 'error');
    }

    if ($cancelform) {
        if ($sd > 0) {
            $baseurl->param('sd', $sd);
        }
        redirect($baseurl);
    }

    // Take attendance.
    if ($actionallowed && $takeattendance) {
        // Check the attendance data matches the expected seminar event.
        if ($formdata->s != $seminarevent->get_id()) {
            print_error('Mismatched attendance data handed through form submission.');
        }

        // Pre-process form data.
        $check = [];
        $items = [];
        foreach ($formdata as $key => $item) {
            $keyparts = explode('_', $key);
            /**
             * Every user on the form should have an entry in the formdata called
             * "submissionid_X" => $attendance value, but they should only be updated
             * if they also have a "Check_submissionid_X" record.
             */
            if ($keyparts[0] == 'submissionid') {
                if ($item == 110) {
                    continue; // Attendance value not set.
                }

                $items[$keyparts[1]] = (int)$item;
            }
        }

        $result = false;
        if ($sd == 0) {
            $result = signup_helper::process_attendance($seminarevent, $items);
            if ($result) {
                // Trigger take attendance update event.
                $event = \mod_facetoface\event\attendance_updated::create_from_session(
                    $session,
                    $context
                );

                $event->trigger();

                totara_set_notification(
                    get_string('updateattendeessuccessful', 'facetoface'),
                    $baseurl,
                    ['class' => 'notifysuccess']
                );
            }
        } else {
            $baseurl->param('sd', $sd);
            $result = attendance_helper::process_session_attendance($items, $sd);

            if ($result) {
                totara_set_notification(
                    get_string('updateattendeessuccessful', 'mod_facetoface'),
                    $baseurl,
                    ['class' => 'notifysuccess']
                );
            }
        }

        if (!$result) {
            totara_set_notification(
                get_string('error:takeattendance', 'facetoface'),
                $baseurl,
                ['class' => 'notifyproblem']
            );
        }
    }
}

/**
 * Print page header
 */
if (!$onlycontent) {
    attendees_list_helper::process_js($action, $seminar, $seminarevent, $sd);
    \mod_facetoface\event\attendees_viewed::create_from_session($session, $context, $action)->trigger();
    $PAGE->set_cm($cm);
    $PAGE->set_heading($course->fullname);
    echo $OUTPUT->header();
}

/**
 * Print page content
 */
if (!$onlycontent && !$download) {
    echo $OUTPUT->box_start();
    echo $OUTPUT->heading(format_string($seminar->get_name()));
    if ($can_view_session) {
        /**
         * @var mod_facetoface_renderer $seminarrenderer
         */
        $seminarrenderer = $PAGE->get_renderer('mod_facetoface');
        echo $seminarrenderer->render_seminar_event($seminarevent, true, false, true);
    }
    require_once($CFG->dirroot.'/mod/facetoface/attendees/tabs.php'); // If needed include tabs
    echo $OUTPUT->container_start('f2f-attendees-table');
}

//Print list attendees in taking attendance (if user able to view)
if ($actionallowed) {
    $numattendees = facetoface_get_num_attendees($seminarevent->get_id());
    $overbooked = ($numattendees > $seminarevent->get_capacity());

    if ($numattendees == 0) {
        if ($seminar->is_approval_required()) {
            if (count($requests) == 1) {
                echo $OUTPUT->notification(get_string('nosignedupusersonerequest', 'facetoface'));
            } else {
                echo $OUTPUT->notification(get_string('nosignedupusersnumrequests', 'facetoface', count($requests)));
            }
        } else {
            echo $OUTPUT->notification(get_string('nosignedupusers', 'facetoface'));
        }
    } else {
        $attendancetracking = factory::get_attendance_tracking($seminarevent, $baseurl, $download, $context, $sd);
        $content = $attendancetracking->generate_content();
        echo $content;
    }
}

/**
 * Print page footer
 */
if (!$onlycontent) {
    echo $OUTPUT->container_end();
    echo $OUTPUT->box_end();
    echo $OUTPUT->footer($course);
}
