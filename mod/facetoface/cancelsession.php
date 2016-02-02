<?php
/*
 * This file is part of Totara LMS
 *
 * Copyright (C) 2016 onwards Totara Learning Solutions LTD
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
 * @author Keelin Devenney <keelin@learningpool.com>
 * @package mod_facetoface
 */

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once ('lib.php');
require_once ('cancelsession_form.php');

$s = required_param('s', PARAM_INT); // facetoface session ID
$confirm = optional_param('confirm', false, PARAM_BOOL);
$backtoallsessions = optional_param('backtoallsessions', 0, PARAM_INT);

if (!$session = facetoface_get_session($s)) {
    print_error('error:incorrectcoursemodulesession', 'facetoface');
}
if (!$session->allowcancellations) {
    print_error('error:cancellationsnotallowed', 'facetoface');
}
if (!$facetoface = $DB->get_record('facetoface', array('id' => $session->facetoface))) {
    print_error('error:incorrectfacetofaceid', 'facetoface');
}
if (!$course = $DB->get_record('course', array('id' => $facetoface->course))) {
    print_error('error:coursemisconfigured', 'facetoface');
}
if (!$cm = get_coursemodule_from_instance("facetoface", $facetoface->id, $course->id)) {
    print_error('error:incorrectcoursemoduleid', 'facetoface');
}

$context = context_module::instance($cm->id);

if (!has_capability('mod/facetoface:configurecancellation', $context)) {
    print_error('error:couldnotcancelsession', 'facetoface');
}

require_login($course, false, $cm);
require_capability('mod/facetoface:view', $context);

$PAGE->set_url('/mod/facetoface/cancelsession.php', array('s' => $s, 'backtoallsessions' => $backtoallsessions, 'confirm' => $confirm));
$PAGE->set_title($facetoface->name);
$PAGE->set_heading($course->fullname);

$returnurl = new moodle_url($CFG->wwwroot.'/course/view.php', array('id' => $course->id));

if ($backtoallsessions) {
    $returnurl = new moodle_url($CFG->wwwroot.'/mod/facetoface/view.php', array('f' => $backtoallsessions));
}

customfield_load_data($session, 'facetofacecancellation', 'facetoface_sessioncancel');

$mform = new mod_facetoface_cancelsession_form(null, compact('s', 'backtoallsessions', 'session', 'userisinwaitlist'));
if ($mform->is_cancelled()) {
    redirect($returnurl);
}

if ($fromform = $mform->get_data()) { // Form submitted.

    if (empty($fromform->submitbutton)) {
        print_error('error:unknownbuttonclicked', 'facetoface', $returnurl);
    }

    $forcecancel = false;
    $timenow = time();
    $bookedsession = facetoface_get_user_submissions($facetoface->id, $USER->id, MDL_F2F_STATUS_WAITLISTED, MDL_F2F_STATUS_WAITLISTED, $session->id);
    if (!empty($bookedsession) && facetoface_has_session_started($session, $timenow)) {
        $forcecancel = true;
    }

    $errorstr = '';
    $fromform->id = $s;

    customfield_save_data($fromform, 'facetofacecancellation', 'facetoface_sessioncancel');

    $message = get_string('bookingsessioncancelled', 'facetoface');

    if (facetoface_cancel_session($session)) {
        \mod_facetoface\event\session_cancelled::create_from_session($session, $context)->trigger();
    } else {
        print_error('error:couldnotcancelsession', 'facetoface', $returnurl);
    }

    totara_set_notification($message, $returnurl, array('class' => 'notifysuccess'));

    redirect($returnurl);
}

echo $OUTPUT->header();
echo $OUTPUT->box_start();
echo $OUTPUT->heading(get_string('cancelingsession', 'facetoface', $facetoface->name));

echo facetoface_print_session($session, true);

$mform->display();

echo $OUTPUT->box_end();
echo $OUTPUT->footer($course);