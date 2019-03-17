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
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Oleg Demeshev <oleg.demeshev@totaralearning.com>
 * @package mod_facetoface
 */

use \mod_facetoface\attendees_helper;
use \mod_facetoface\signup\state\declined;
use \mod_facetoface\signup\state\event_cancelled;
use \mod_facetoface\signup\state\user_cancelled;

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->dirroot.'/mod/facetoface/lib.php');

// Face-to-face session ID
$s = optional_param('s', 0, PARAM_INT);
// Action being performed, a proper default will be set shortly.
// Require for attendees.js
$action = optional_param('action', 'cancellations', PARAM_ALPHA);
// Back to all sessions.
$backtoallsessions = optional_param('backtoallsessions', 1, PARAM_BOOL);
$sid = optional_param('sid', '0', PARAM_INT);
$debug = optional_param('debug', 0, PARAM_INT);

// If there's no sessionid specified.
if (!$s) {
    \mod_facetoface\attendees_helper::process_no_sessionid('cancellations');
    exit;
}

list($session, $facetoface, $course, $cm, $context) = facetoface_get_env_session($s);
$seminarevent = new \mod_facetoface\seminar_event($s);
$seminar = new \mod_facetoface\seminar($seminarevent->get_facetoface());

require_login($course, false, $cm);
/**
 * Print page header
 */
// Setup urls
$baseurl = new moodle_url('/mod/facetoface/attendees/cancellations.php', ['s' => $seminarevent->get_id()]);
$PAGE->set_context($context);
$PAGE->set_url($baseurl);

list($allowed_actions, $available_actions, $staff, $admin_requests, $canapproveanyrequest, $cancellations, $requests, $attendees)
    = attendees_helper::get_allowed_available_actions($seminar, $seminarevent, $context, $session);

$can_view_session = !empty($allowed_actions);
if (!$can_view_session) {
    // If no allowed actions so far.
    $return = new moodle_url('/mod/facetoface/view.php', ['f' => $seminar->get_id()]);
    redirect($return);
}

$pagetitle = format_string($seminar->get_name());
$PAGE->set_pagelayout('standard');
$PAGE->set_title($pagetitle);
$PAGE->set_cm($cm);
$PAGE->set_heading($course->fullname);

// $allowed_actions is already set, so we can now know if the current action is allowed.
$actionallowed = in_array($action, $allowed_actions);
$show_table = false;
if ($actionallowed) {
    attendees_helper::process_js($action, $seminar, $seminarevent);
    // Verify global restrictions and process report early before any output is done (required for export).
    $shortname = 'facetoface_cancellations';
    $attendancestatuses = [
        user_cancelled::get_code(),
        event_cancelled::get_code(),
        declined::get_code()
    ];
    $report = attendees_helper::load_report($shortname, $attendancestatuses);
    // We will show embedded report.
    $show_table = true;
}

/**
 * Print page content
 */
echo $OUTPUT->header();
echo $OUTPUT->box_start();
echo $OUTPUT->heading($pagetitle);
if ($can_view_session) {
    attendees_helper::show_customfields($seminarevent);
}
require_once($CFG->dirroot.'/mod/facetoface/attendees/tabs.php'); // If needed include tabs
echo $OUTPUT->container_start('f2f-attendees-table');

/**
 * Print attendees (if user able to view)
 */
if ($show_table) {
    // Output the section heading.
    echo $OUTPUT->heading(get_string('cancellations', 'mod_facetoface'));

    $report->set_baseurl($baseurl);
    $report->display_restrictions();
    // Actions menu.
    //if (has_capability('mod/facetoface:manageattendeesnote', $context)) {

    $output = $PAGE->get_renderer('totara_reportbuilder');
    // This must be done after the header and before any other use of the report.
    list($reporthtml, $debughtml) = $output->report_html($report, $debug);
    echo $debughtml;

    $report->display_search();
    $report->display_sidebar_search();

    // Print saved search buttons if appropriate.
    echo $report->display_saved_search_options();
    echo $reporthtml;

    attendees_helper::report_export_form($report, $sid);
}
// Go back.
if ($backtoallsessions) {
    $url = new moodle_url('/mod/facetoface/view.php', array('f' => $seminar->get_id()));
} else {
    $url = new moodle_url('/course/view.php', array('id' => $course->id));
}
echo html_writer::link($url, get_string('goback', 'mod_facetoface')) . html_writer::end_tag('p');
/**
 * Print page footer
 */
echo $OUTPUT->container_end();
echo $OUTPUT->box_end();
echo $OUTPUT->footer($course);

\mod_facetoface\event\attendees_viewed::create_from_session($session, $context, $action)->trigger();