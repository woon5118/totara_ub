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
 * @author Oleg Demeshev <oleg.demeshev@totaralearning.com>
 * @package mod_facetoface
 */

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->dirroot . '/mod/facetoface/lib.php');

define('MAX_USERS_PER_PAGE', 1000);

$s      = required_param('s', PARAM_INT); // facetoface session ID
$add    = optional_param('add', false, PARAM_BOOL);
$remove = optional_param('remove', false, PARAM_BOOL);

list($session, $facetoface, $course, $cm, $context) = facetoface_get_env_session($s);
$seminarevent = new \mod_facetoface\seminar_event($s);

// Check essential permissions.
require_login($course, false, $cm);
require_capability('mod/facetoface:viewattendees', $context);

// Recipients.
$recipient_helper = new \mod_facetoface\recipients_list_helper();
$recipient_helper->set_recipients();

// Handle the POST actions sent to the page.
if ($data = data_submitted()) {
    // Add.
    if ($add and has_capability('mod/facetoface:addrecipients', $context)) {
        $recipient_helper->add_recipients($data);
    }
    // Remove.
    if ($remove and has_capability('mod/facetoface:removerecipients', $context)) {
        $recipient_helper->remove_recipients($data);
    }
}

// Set/Prepare the list of currently selected recipients for template.
$recipient_helper->set_existing_recipients();
// Set/Prepare all available attendees for template.
$recipient_helper->set_potential_recipients($seminarevent);
// Set recipients value for the form.
$recipients = implode(',', $recipient_helper->get_recipients());
// Set form url.
$url = new moodle_url('/mod/facetoface/attendees/editrecipients.php');
// Prints a form to add/remove users from the recipients list.
include($CFG->dirroot . '/mod/facetoface/editrecipients.html');
