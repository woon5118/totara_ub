<?php
/**
 * This file is part of Totara Learn
 *
 * Copyright (C) 2021 onwards Totara Learning Solutions LTD
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
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 */

// Error page to be used as a custom 404 set in server configuration.

    require('../config.php');
    require_once($CFG->libdir.'/eventslib.php');

    $site = get_site();
    $PAGE->set_url('/error/');
    $PAGE->set_context(context_system::instance());
    $PAGE->set_title($site->fullname .':Error');
    $PAGE->set_heading($site->fullname .': Error 404');

    // Form submitted, do not check referer (original page unknown).
    if ($form = data_submitted()) {
        // Only deal with real users.
        if (!isloggedin()) {
            redirect($CFG->wwwroot);
        }

        // Send the message and redirect.
        $eventdata = new \core\message\message();
        $eventdata->courseid         = SITEID;
        $eventdata->component        = 'moodle';
        $eventdata->name             = 'errors';
        $eventdata->userfrom          = $USER;
        $eventdata->userto            = core_user::get_support_user();
        $eventdata->subject           = 'Error: '. $form->referer .' -> '. $form->requested;
        $eventdata->fullmessage       = $form->text;
        $eventdata->fullmessageformat = FORMAT_PLAIN;
        $eventdata->fullmessagehtml   = '';
        $eventdata->smallmessage      = '';
        message_send($eventdata);

        redirect($CFG->wwwroot, 'Message sent, thanks', 3);
        exit;
    }

    $httpreferer = get_local_referer(false);
    $requesturi  = empty($_SERVER['REQUEST_URI'])  ? '' : $_SERVER['REQUEST_URI'];

    header("HTTP/1.0 404 Not Found");
    header("Status: 404 Not Found");

    $PAGE->navbar->add('Error 404 - File not Found');
    echo $OUTPUT->header();
    echo $OUTPUT->box(get_string('pagenotexist', 'error'). '<br />'.s($requesturi), 'generalbox boxaligncenter');

    if (isloggedin()) {
?>
        <p><?php echo get_string('pleasereport', 'error'); ?>
        <p><form action="<?php echo $CFG->wwwroot ?>/error/index.php" method="post">
           <textarea rows="3" cols="50" name="text" id="text" spellcheck="true"></textarea><br />
           <input type="hidden" name="referer" value="<?php p($httpreferer) ?>">
           <input type="hidden" name="requested" value="<?php p($requesturi) ?>">
           <input type="submit" value="<?php echo get_string('sendmessage', 'error'); ?>">
           </form>
<?php
    } else {
        echo $OUTPUT->continue_button($CFG->wwwroot);
    }
    echo $OUTPUT->footer();
?>
