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
 * @author David Curry <david.curry@totaralearning.com>
 * @author Valerii Kuznetsov <valerii.kuznetsov@totaralms.com>
 * @author Oleg Demeshev <oleg.demeshev@totaralearning.com>
 * @package mod_facetoface
 */

require(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/mod/facetoface/lib.php');

use mod_facetoface\signup_helper;
use mod_facetoface\signup;
use mod_facetoface\seminar;

$s = required_param('s', PARAM_INT); // {facetoface_sessions}.id

$seminarevent = new \mod_facetoface\seminar_event($s);
$seminar = $seminarevent->get_seminar();
$course = $DB->get_record('course', ['id' => $seminar->get_course()], '*', MUST_EXIST);
$cm = $seminar->get_coursemodule();
$context = context_module::instance($cm->id);

$signup = signup::create($USER->id, $seminarevent);

if (isguestuser()) {
    redirect(new moodle_url('/course/view.php', ['id' => $course->id]),
        get_string('error:cannotsignuptoeventasguest', 'mod_facetoface'));
}

if (!empty($seminarevent->get_cancelledstatus())) {
    redirect(new moodle_url('/course/view.php', ['id' => $course->id]),
        get_string('error:cannotsignupforacancelledevent', 'mod_facetoface'));
}

if ($CFG->enableavailability) {
    if (!get_fast_modinfo($cm->course)->get_cm($cm->id)->available) {
        redirect(new moodle_url('/course/view.php', ['id' => $course->id]));
        die;
    }
}

require_login();

$returnurl = new moodle_url('/enrol/index.php', ['id' => $course->id]);

// This is not strictly required for signup (more correctly it is checked in actor_has_role), but leaving it for early
// indication of the issue.
$trainerroles = facetoface_get_trainer_roles(context_course::instance($course->id));
$trainers     = facetoface_get_trainers($seminarevent->get_id());
if ($seminar->get_approvaltype() == seminar::APPROVAL_ROLE) {
    if (!$trainerroles || !$trainers) {
        totara_set_notification(get_string('error:missingrequiredrole', 'mod_facetoface'), $returnurl);
    }
}

$PAGE->set_cm($cm);
$PAGE->set_url('/enrol/totara_facetoface/signup.php', ['s' => $s]);
$PAGE->set_title(format_string($seminar->get_name()));
$PAGE->set_heading($course->fullname);

$params = ['signup' => $signup, 'backtoallsessions' => false];
$mform = new \mod_facetoface\form\signup(null, $params, 'post', '', ['name' => 'signupform']);

local_js([TOTARA_JS_DIALOG, TOTARA_JS_TREEVIEW]);

$PAGE->requires->strings_for_js(['selectmanager'], 'mod_facetoface');
$jsmodule = [
    'name' => 'facetoface_managerselect',
    'fullpath' => '/mod/facetoface/js/manager.js',
    'requires' => ['json']
];
$selected_manager = dialog_display_currently_selected(get_string('currentmanager', 'mod_facetoface'), 'manager');
$args = [
    'userid' => $USER->id,
    'fid' => $seminar->get_id(),
    'manager' => $selected_manager,
    'sesskey' => sesskey()
];
$PAGE->requires->js_init_call('M.facetoface_managerselect.init', $args, false, $jsmodule);

if ($mform->is_cancelled()) {
    redirect($returnurl);
}
if ($fromform = $mform->get_data()) {
    if (empty($fromform->submitbutton)) {
        print_error('error:unknownbuttonclicked', 'mod_facetoface', $returnurl);
    }

    if (!is_enrolled($context, $USER)) {
        // Check for and attempt to enrol via the totara_facetoface enrolment plugin.
        $enrolments = enrol_get_plugins(true);
        $instances = enrol_get_instances($course->id, true);
        foreach ($instances as $instance) {
            if ($instance->enrol === 'totara_facetoface') {
                $data = clone($fromform);
                $data->sid = [$seminarevent->get_id()];
                $enrolments[$instance->enrol]->enrol_totara_facetoface($instance, $data, $course, $returnurl);
                // We expect enrol module to take all required sign up action and redirect, so it should never return.
                debugging("Seminar direct enrolment should never return to signup page");
                exit();
            }
        }
    }
}

echo $OUTPUT->header();
echo $OUTPUT->box_start();

// Choose header depending on resulting state: waitlist or booked.
$heading = get_string('signupfor', 'mod_facetoface', $seminar->get_name());
$currentstate = $signup->get_state();
if(!$currentstate->can_switch(signup\state\booked::class) &&
    $currentstate->can_switch(signup\state\waitlisted::class)) {
    $heading = get_string('waitlistfor', 'mod_facetoface', $seminar->get_name());
}
echo $OUTPUT->heading($heading);

/**
 * @var mod_facetoface_renderer $seminarrenderer
 */
$seminarrenderer = $PAGE->get_renderer('mod_facetoface');
$signedup = !$signup->get_state()->is_not_happening();
$viewattendees = has_capability('mod/facetoface:viewattendees', $context);
echo $seminarrenderer->render_seminar_event($seminarevent, $viewattendees, false, $signedup);

if (signup_helper::can_signup($signup)) {
    $mform->display();
} else if ($currentstate instanceof signup\state\not_set
    || $currentstate instanceof signup\state\user_cancelled
    || $currentstate instanceof signup\state\declined
) {
    // Display message only if user is not signed up:
    echo $seminarrenderer->render_signup_failures(signup_helper::get_failures($signup));
}

echo html_writer::empty_tag('br') . html_writer::link($returnurl, get_string('goback', 'mod_facetoface'), ['title' => get_string('goback', 'mod_facetoface')]);

echo $OUTPUT->box_end();
echo $OUTPUT->footer($course);
