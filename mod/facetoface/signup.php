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
 * @author David Curry <david.curry@totaralearning.com>
 * @author Valerii Kuznetsov <valerii.kuznetsov@totaralms.com>
 * @author Oleg Demeshev <oleg.demeshev@totaralearning.com>
 * @package mod_facetoface
 */

require(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/mod/facetoface/lib.php');

use mod_facetoface\{signup_helper, signup, seminar, trainer_helper};

$s = required_param('s', PARAM_INT); // {facetoface_sessions}.id
$backtoallsessions = optional_param('backtoallsessions', 0, PARAM_BOOL);

$seminarevent = new \mod_facetoface\seminar_event($s);
$seminar = $seminarevent->get_seminar();
$course = $DB->get_record('course', ['id' => $seminar->get_course()], '*', MUST_EXIST);
$cm = $seminar->get_coursemodule();
$context = context_module::instance($cm->id);

$returnurl = new moodle_url('/course/view.php', ['id' => $course->id]);
if (isguestuser()) {
    redirect(
        $returnurl,
        get_string('error:cannotsignuptoeventasguest', 'mod_facetoface'),
        null,
        \core\notification::ERROR
    );
}

if (!empty($seminarevent->get_cancelledstatus())) {
    redirect(
        $returnurl,
        get_string('error:cannotsignupforacancelledevent', 'mod_facetoface'),
        null,
        \core\notification::ERROR
    );
}

if ($CFG->enableavailability) {
    if (!get_fast_modinfo($cm->course)->get_cm($cm->id)->available) {
        redirect(
            $returnurl,
            get_string('notavailablecourse', 'moodle', $seminar->get_name()),
            null,
            \core\notification::ERROR
        );
    }
}

if ($backtoallsessions) {
    $returnurl = new moodle_url('/mod/facetoface/view.php', ['f' => $seminar->get_id()]);
}
// This is not strictly required for signup (more correctly it is checked in actor_has_role), but leaving it for early
// indication of the issue.
$helper = new trainer_helper($seminarevent);
$trainerroles = trainer_helper::get_trainer_roles(context_course::instance($course->id));
$trainers     = $helper->get_trainers();
if ($seminar->get_approvaltype() == seminar::APPROVAL_ROLE) {
    if (empty($trainerroles) || empty($trainers)) {
        redirect(
            $returnurl,
            get_string('error:missingrequiredrole', 'mod_facetoface'),
            null,
            \core\notification::ERROR
        );
    }
}

require_login($course, false, $cm);
require_capability('mod/facetoface:view', $context);

$signup = signup::create($USER->id, $seminarevent);
// Choose header depending on resulting state: waitlist or booked.
$currentstate = $signup->get_state();
$heading = get_string('signupfor', 'mod_facetoface', $seminar->get_name());
if ($currentstate instanceof signup\state\booked ||
    $currentstate instanceof signup\state\requested ||
    $currentstate instanceof signup\state\waitlisted) {
    $heading = $seminar->get_name();
}
if (!$currentstate->can_switch(signup\state\booked::class) &&
    $currentstate->can_switch(signup\state\waitlisted::class)) {
    $heading = get_string('waitlistfor', 'mod_facetoface', $seminar->get_name());
}

$baseurlparam = [
    's' => $seminarevent->get_id(),
    'backtoallsessions' => $backtoallsessions
];
$PAGE->set_context($context);
$PAGE->set_cm($cm);
$PAGE->set_url(new moodle_url('/mod/facetoface/signup.php', $baseurlparam));
$PAGE->set_title($heading);

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

$params = [
    'signup' => $signup,
    'backtoallsessions' => $backtoallsessions,
];
$mform = new \mod_facetoface\form\signup(null, $params, 'post', '', ['name' => 'signupform']);
if ($mform->is_cancelled()) {
    redirect($returnurl);
}
if ($fromform = $mform->get_data()) {
    if (empty($fromform->submitbutton)) {
        redirect(
            $returnurl,
            get_string('error:unknownbuttonclicked', 'mod_facetoface'),
            null,
            \core\notification::ERROR
        );
    }

    $signup->set_notificationtype($fromform->notificationtype);
    $signup->set_discountcode($fromform->discountcode);

    $managerselect = get_config(null, 'facetoface_managerselect');
    if ($managerselect && isset($fromform->managerid)) {
        $signup->set_managerid($fromform->managerid);
    }

    $f2fselectedjobassignmentelemid = 'selectedjobassignment_' . $seminar->get_id();
    if (property_exists($fromform, $f2fselectedjobassignmentelemid)) {
        $signup->set_jobassignmentid($fromform->$f2fselectedjobassignmentelemid);
    }

    if (signup_helper::can_signup($signup)) {
        signup_helper::signup($signup);

        // Custom fields.
        $fromform->id = $signup->get_id();
        customfield_save_data($fromform, 'facetofacesignup', 'facetoface_signup');

        // Notification.
        $state = $signup->get_state();
        $message = $state->get_message();
        $notificationtype = \core\notification::INFO;

        // There may be lots of factors that will prevent confirmation message to appear at user mailbox
        // but in most cases this will be true:
        if ($state instanceof signup\state\booked) {
            $notificationtype = \core\notification::SUCCESS;
            if (!$signup->get_skipusernotification() && $fromform->notificationtype != MDL_F2F_NONE) {
                $message .= html_writer::empty_tag('br') . html_writer::empty_tag('br') .
                    get_string('confirmationsent', 'mod_facetoface');
            }
        }
    } else {
        // Note - We can't use the renderer_signup_failures() function here, but this is the same.
        $failures = signup_helper::get_failures($signup);
        reset($failures);
        $message = current($failures);
        $notificationtype = \core\notification::ERROR;
    }

    redirect($returnurl, $message, null, $notificationtype);
}

echo $OUTPUT->header();
echo $OUTPUT->heading($heading);

/**
 * @var mod_facetoface_renderer $seminarrenderer
 */
$seminarrenderer = $PAGE->get_renderer('mod_facetoface');
$seminarrenderer->setcontext($context);
$signedup = !$signup->get_state()->is_not_happening();
$viewattendees = has_capability('mod/facetoface:viewattendees', $context);
echo $seminarrenderer->render_seminar_event($seminarevent, $viewattendees, false, $signedup);

// Cancellation links
if ($currentstate->can_switch(signup\state\user_cancelled::class)) {
    $canceltext = get_string('cancelbooking', 'mod_facetoface');
    if ($currentstate instanceof signup\state\waitlisted) {
        $canceltext = get_string('cancelwaitlist', 'mod_facetoface');
    }
    $cancelurl = new moodle_url('/mod/facetoface/cancelsignup.php', $baseurlparam);
    echo html_writer::link($cancelurl, $canceltext, ['title' => $canceltext]);
    echo ' &ndash; ';
}

if ($viewattendees) {
    $viewurl = new moodle_url('/mod/facetoface/attendees/view.php', $baseurlparam);
    $seeattendees = get_string('seeattendees', 'mod_facetoface');
    echo html_writer::link($viewurl, $seeattendees, ['title' => $seeattendees]);
}

if (signup_helper::can_signup($signup)) {
    $mform->display();
} else if ($currentstate instanceof signup\state\not_set
    || $currentstate instanceof signup\state\user_cancelled
    || $currentstate instanceof signup\state\declined
    ) {
    // Display message only if user is not signed up:
    echo $seminarrenderer->render_signup_failures(signup_helper::get_failures($signup));
}
echo $seminarrenderer->render_action_bar_on_tabpage($returnurl);
echo $OUTPUT->footer();
