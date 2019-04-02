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

/**
 * Load and validate base data
 */
// Face-to-face session ID
$s = optional_param('s', 0, PARAM_INT);
// Action being performed, a proper default will be set shortly.
// Require for attendees.js
$action = optional_param('action', 'messageusers', PARAM_ALPHA);
// Back to all sessions.
$backtoallsessions = optional_param('backtoallsessions', 1, PARAM_BOOL);

// If there's no sessionid specified.
if (!$s) {
    \mod_facetoface\attendees_helper::process_no_sessionid('messageusers');
    exit;
}

$seminarevent = new \mod_facetoface\seminar_event($s);
$seminar = $seminarevent->get_seminar();
$course = $DB->get_record('course', array('id' => $seminar->get_course()));
$cm = $seminar->get_coursemodule();
$context = context_module::instance($cm->id);

$session = new stdClass();
$session->id = $seminarevent->get_id();

require_login($course, false, $cm);
/**
 * Print page header
 */
// Setup urls
$baseurl = new moodle_url('/mod/facetoface/attendees/messageusers.php', array('s' => $seminarevent->get_id()));
$PAGE->set_context($context);
$PAGE->set_url($baseurl);

list($allowed_actions, $available_actions, $staff, $admin_requests, $canapproveanyrequest, $cancellations, $requests, $attendees)
    = \mod_facetoface\attendees_helper::get_allowed_available_actions($seminar, $seminarevent, $context);

if (!in_array($action, $allowed_actions)) {
    // If no allowed actions so far.
    $return = new moodle_url('/mod/facetoface/view.php', array('f' => $seminar->get_id()));
    redirect($return, get_string('error:capabilitysendmessages', 'mod_facetoface'),  null, \core\notification::ERROR);
}

/**
 * Handle submitted data
 * Send messages
 */
$mform = new \mod_facetoface\form\attendees_message($baseurl, ['s' => $s, 'seminarevent' => $seminarevent, 'context' => $context]);
$returnurl = new moodle_url('/mod/facetoface/attendees/view.php', array('s' => $seminarevent->get_id()));
// Check form validates
if ($mform->is_cancelled()) {
    redirect($returnurl);
} else if ($mform->is_submitted()) {
    if (!confirm_sesskey()) {
        redirect($baseurl, get_string('confirmsesskeybad', 'error'), null, \core\notification::ERROR);
    }
    $mform->send_message();
}

$pagetitle = format_string($seminar->get_name());
$PAGE->set_pagelayout('standard');
$PAGE->set_title($pagetitle);
$PAGE->set_cm($cm);
$PAGE->set_heading($course->fullname);
\mod_facetoface\messaging::process_js($action, $seminar, $seminarevent);

/**
 * Print page content
 */
echo $OUTPUT->header();
echo $OUTPUT->box_start();
echo $OUTPUT->heading($pagetitle);
/** @var mod_facetoface_renderer $seminarrenderer */
$seminarrenderer = $PAGE->get_renderer('mod_facetoface');
echo $seminarrenderer->render_seminar_event($seminarevent, true, false, true);

require_once($CFG->dirroot.'/mod/facetoface/attendees/tabs.php'); // If needed include tabs
echo $OUTPUT->container_start('f2f-attendees-table');

$mform->display();

// Go back.
if ($backtoallsessions) {
    $url = new moodle_url('/mod/facetoface/view.php', array('f' => $seminar->get_id()));
} else {
    $url = new moodle_url('/course/view.php', array('id' => $course->id));
}
echo html_writer::tag('p', html_writer::link($url, get_string('goback', 'facetoface')));

/**
 * Print page footer
 */
echo $OUTPUT->container_end();
echo $OUTPUT->box_end();
echo $OUTPUT->footer($course);

\mod_facetoface\event\message_users_viewed::create_from_session($session, $context, $action)->trigger();