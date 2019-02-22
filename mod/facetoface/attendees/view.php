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

use \mod_facetoface\attendees_helper;
use \mod_facetoface\signup\state\booked;
use \mod_facetoface\signup\state\fully_attended;
use \mod_facetoface\signup\state\not_set;
use \mod_facetoface\signup\state\no_show;
use \mod_facetoface\signup\state\partially_attended;

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->dirroot.'/mod/facetoface/lib.php');
require_once($CFG->libdir.'/totaratablelib.php');
require_once($CFG->dirroot . '/totara/core/js/lib/setup.php');

/**
 * Load and validate base data
 */
// Face-to-face session ID
$s = optional_param('s', 0, PARAM_INT);
// Action being performed, a proper default will be set shortly.
// Require for attendees.js
$action            = optional_param('action', 'attendees', PARAM_ALPHA);
// Only return content
$onlycontent       = optional_param('onlycontent', false, PARAM_BOOL);
// Export download.
$download = optional_param('download', '', PARAM_ALPHA);
// Back to all sessions.
$backtoallsessions = optional_param('backtoallsessions', 1, PARAM_BOOL);
// Report support.
$sid = optional_param('sid', '0', PARAM_INT);
$debug = optional_param('debug', 0, PARAM_INT);

// If there's no sessionid specified.
if (!$s) {
    attendees_helper::process_no_sessionid('view');
    exit;
}

list($session, $facetoface, $course, $cm, $context) = facetoface_get_env_session($s);
$seminarevent = new \mod_facetoface\seminar_event($s);
$seminar = new \mod_facetoface\seminar($seminarevent->get_facetoface());

require_login($course, false, $cm);

// Setup urls
$baseurl = new moodle_url('/mod/facetoface/attendees/view.php', array('s' => $seminarevent->get_id()));

$PAGE->set_context($context);
$PAGE->set_url($baseurl);

list($allowed_actions, $available_actions, $staff, $admin_requests, $canapproveanyrequest, $cancellations, $requests, $attendees)
    = attendees_helper::get_allowed_available_actions($seminar, $seminarevent, $context, $session);

$can_view_session = !empty($allowed_actions);
if (!$can_view_session) {
    // If no allowed actions so far.
    $return = new moodle_url('/mod/facetoface/view.php', array('f' => $seminar->get_id()));
    redirect($return);
    die();
}
// $allowed_actions is already set, so we can now know if the current action is allowed.
$actionallowed = in_array($action, $allowed_actions);
$pagecontent = !$onlycontent && !$download;
/**
 * Handle actions
 */
$show_table = false;
if ($actionallowed) {
    // Verify global restrictions and process report early before any output is done (required for export).
    $shortname = 'facetoface_sessions';
    $attendancestatuses = \mod_facetoface\signup\state\attendance_state::get_all_attendance_code_with(
        [
            \mod_facetoface\signup\state\booked::class,
            \mod_facetoface\signup\state\not_set::class
        ]
    );
    $report = attendees_helper::load_report($shortname, $attendancestatuses);
    // We will show embedded report.
    $show_table = true;
}
/**
 * Print page header
 */
if ($pagecontent) {
    attendees_helper::process_js($action, $seminar, $seminarevent);
    \mod_facetoface\event\attendees_viewed::create_from_session($session, $context, $action)->trigger();
    $PAGE->set_cm($cm);
    $PAGE->set_heading($course->fullname);
    echo $OUTPUT->header();
}
/**
 * Print page content
 */
if ($pagecontent) {
    echo $OUTPUT->box_start();
    echo $OUTPUT->heading(format_string($seminar->get_name()));
    if ($can_view_session) {
        attendees_helper::show_customfields($seminarevent);
    }
    require_once($CFG->dirroot.'/mod/facetoface/attendees/tabs.php'); // If needed include tabs
    echo $OUTPUT->container_start('f2f-attendees-table');
}
/**
 * Print attendees (if user able to view)
 */
if ($show_table) {
    // Get list of attendees
    if ($pagecontent) {
        attendees_helper::is_overbooked($seminarevent);
        // Output the section heading.
        echo $OUTPUT->heading(get_string('attendees', 'mod_facetoface'));
    }

    $report->set_baseurl($baseurl);
    $report->display_restrictions();

    // Actions menu.
    $capability = has_any_capability(array('mod/facetoface:addattendees', 'mod/facetoface:removeattendees'), $context);
    if ($capability && $actionallowed && ($seminarevent->get_cancelledstatus() == 0)) {
        // Get list of actions
        if (in_array('addattendees', $allowed_actions)) {
            $actions['add'] = get_string('addattendees', 'facetoface');
            $actions['bulkaddfile'] = get_string('addattendeesviafileupload', 'facetoface');
            $actions['bulkaddinput'] = get_string('addattendeesviaidlist', 'facetoface');
            if (has_capability('mod/facetoface:removeattendees', $context)) {
                $actions['remove'] = get_string('removeattendees', 'facetoface');
            }
            echo $OUTPUT->container_start('actions last');
            // Action selector
            echo html_writer::label(get_string('attendeeactions', 'mod_facetoface'), 'menuf2f-actions', true, ['class' => 'sr-only']);
            echo html_writer::select($actions, 'f2f-actions', '', array('' => get_string('actions')));
            echo $OUTPUT->container_end();
        }
    }
    /** @var totara_reportbuilder_renderer $output */
    $output = $PAGE->get_renderer('totara_reportbuilder');
    // This must be done after the header and before any other use of the report.
    list($reporthtml, $debughtml) = $output->report_html($report, $debug);
    echo $debughtml;

    $report->display_search();
    $report->display_sidebar_search();

    // Print saved search buttons if appropriate.
    echo $report->display_saved_search_options();
    echo $reporthtml;

    // Session downloadable sign in sheet.
    if ($seminarevent->is_sessions() && has_capability('mod/facetoface:exportsessionsigninsheet', $context)) {
        $downloadsheetattendees = facetoface_get_attendees($seminarevent->get_id(), $attendancestatuses);
        if (!empty($downloadsheetattendees)) {
            // We need the dates, and we only want to show this option if there are one or more dates.
            $formurl = new moodle_url('/mod/facetoface/reports/signinsheet.php');
            $signinform = new \mod_facetoface\form\signin($formurl, $session);
            echo html_writer::start_div('f2fdownloadsigninsheet');
            $signinform->display();
            echo html_writer::end_div();
        }
    }

    attendees_helper::report_export_form($report, $sid);
}
// Go back.
if ($backtoallsessions) {
    $url = new moodle_url('/mod/facetoface/view.php', array('f' => $seminar->get_id()));
} else {
    $url = new moodle_url('/course/view.php', array('id' => $course->id));
}
echo html_writer::link($url, get_string('goback', 'facetoface')) . html_writer::end_tag('p');
/**
 * Print page footer
 */
if ($pagecontent) {
    echo $OUTPUT->container_end();
    echo $OUTPUT->box_end();
    echo $OUTPUT->footer($course);
}
