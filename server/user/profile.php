<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Public Profile -- a user's public profile page
 *
 * - each user can currently have their own page (cloned from system and then customised)
 * - users can add any blocks they want
 * - the administrators can define a default site public profile for users who have
 *   not created their own public profile
 *
 * This script implements the user's view of the public profile, and allows editing
 * of the public profile.
 *
 * @package    core_user
 * @copyright  2010 Remote-Learner.net
 * @author     Hubert Chathi <hubert@remote-learner.net>
 * @author     Olav Jordan <olav.jordan@remote-learner.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../config.php');
require_once($CFG->dirroot . '/my/lib.php');
require_once($CFG->dirroot . '/user/profile/lib.php');
require_once($CFG->dirroot . '/user/lib.php');
require_once($CFG->libdir.'/filelib.php');

$userid = optional_param('id', 0, PARAM_INT);
$reset = optional_param('reset', null, PARAM_BOOL);

$PAGE->set_url('/user/profile.php', array('id' => $userid));

if (!empty($CFG->forceloginforprofiles)) {
    require_login();
    if (isguestuser()) {
        $PAGE->set_context(context_system::instance());
        echo $OUTPUT->header();
        echo $OUTPUT->confirm(get_string('guestcantaccessprofiles', 'error'),
                              get_login_url(),
                              $CFG->wwwroot);
        echo $OUTPUT->footer();
        die;
    }
} else if (!empty($CFG->forcelogin)) {
    require_login();
}

$userid = $userid ? $userid : $USER->id;       // Owner of the page.
$user = $DB->get_record('user', array('id' => $userid));
if (!$user || $user->deleted) {
    $PAGE->set_context(context_system::instance());
    echo $OUTPUT->header();
    if (!$user) {
        echo $OUTPUT->notification(get_string('invaliduser', 'error'));
    } else {
        echo $OUTPUT->notification(get_string('userdeleted'));
    }
    echo $OUTPUT->footer();
    die;
}

$currentuser = ($user->id == $USER->id);
$context = $usercontext = context_user::instance($userid, MUST_EXIST);

if (!user_can_view_profile($user, null)) {
    if (isguestuser()) {
        // Let them log in.
        redirect(get_login_url());
    }

    // Course managers can be browsed at site level. If not forceloginforprofiles, allow access (bug #4366).
    $struser = get_string('user');
    $PAGE->set_context(context_system::instance());
    $PAGE->set_title("$SITE->shortname: $struser");  // Do not leak the name.
    $PAGE->set_heading($struser);
    $PAGE->set_pagelayout('noblocks'); // Prevent showing default blocks.
    $PAGE->set_url('/user/profile.php', array('id' => $userid));
    $PAGE->navbar->add($struser);
    echo $OUTPUT->header();
    echo $OUTPUT->notification(get_string('usernotavailable', 'error'));
    echo $OUTPUT->footer();
    exit;
}

// Get the profile page. Should always return something unless the database is broken.
if (!$currentpage = my_get_page($userid, MY_PAGE_PUBLIC)) {
    print_error('mymoodlesetup');
}
$USER->editing = 0;
// Start setting up the page.
$PAGE->set_context($context);
$PAGE->set_pagelayout('mypublic');
$PAGE->set_pagetype('user-profile');

// Set up block editing capabilities.
if (isguestuser()) {     // Guests can never edit their profile.
    $PAGE->set_blocks_editing_capability('moodle/my:configsyspages');  // unlikely :).
} else {
    $PAGE->set_blocks_editing_capability('moodle/user:manageblocks');
}

$PAGE->blocks->add_region('content');
$PAGE->set_subpage(1);
$PAGE->set_title(fullname($user).": " . get_string('publicprofile'));
$PAGE->set_heading(fullname($user));

if (!$currentuser) {
    $PAGE->navigation->extend_for_user($user);
    if ($node = $PAGE->settingsnav->get('userviewingsettings'.$user->id)) {
        $node->forceopen = true;
    }
} else if ($node = $PAGE->settingsnav->get('dashboard', navigation_node::TYPE_CONTAINER)) {
    $node->forceopen = true;
}
if ($node = $PAGE->settingsnav->get('root')) {
    $node->forceopen = false;
}

// Toggle the editing state and switches.
if ($PAGE->user_allowed_editing()) {
    // We will allow the manager to remove custom user profile blocks if they exists.
    if ($reset !== null) {
        if (!is_null($userid)) {
            if (!$currentpage = my_reset_page($userid, MY_PAGE_PUBLIC, 'user-profile')) {
                print_error('reseterror', 'my');
            }
            redirect(new moodle_url('/user/profile.php', array('id' => $userid)));
        }
    }

    if ($currentpage->userid !== null) {
        $resetstring = get_string('resetpage', 'my');
        $reseturl = new moodle_url("$CFG->wwwroot/user/profile.php", ['reset' => 1, 'id' => $userid]);
        $resetbutton = $OUTPUT->single_button($reseturl, $resetstring);
        $PAGE->set_button($resetbutton);
    }
}

// Trigger a user profile viewed event.
profile_view($user, $usercontext);

// TODO WORK OUT WHERE THE NAV BAR IS!
echo $OUTPUT->header();

$headerinfo = array('heading' => fullname($user), 'user' => $user, 'usercontext' => $usercontext);
echo $OUTPUT->context_header($headerinfo, 2);

echo '<div class="userprofile">';

if ($user->description && !isset($hiddenfields['description'])) {
    echo '<div class="description">';
    if (!empty($CFG->profilesforenrolledusersonly) && !$currentuser &&
        !$DB->record_exists('role_assignments', array('userid' => $user->id))) {
        echo get_string('profilenotshown', 'moodle');
    } else {
        $user->description = file_rewrite_pluginfile_urls($user->description, 'pluginfile.php', $usercontext->id, 'user',
                                                          'profile', null);
        echo format_text($user->description, $user->descriptionformat);
    }
    echo '</div>';
}

echo $OUTPUT->custom_block_region('content');

echo '</div>';  // Userprofile class.

echo $OUTPUT->footer();
