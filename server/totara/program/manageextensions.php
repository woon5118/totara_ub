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
 * @author Alastair Munro <alastair.munro@totaralms.com>
 * @package totara
 * @subpackage program
 */


require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/totara/program/lib.php');

require_login();

$userid = optional_param('userid', 0, PARAM_INT);
$extensions = optional_param_array('extension', array(), PARAM_INT);
$reasons = optional_param_array('reasondecision', array(), PARAM_TEXT);

$PAGE->set_context(context_system::instance());
$PAGE->set_url(new moodle_url("/totara/program/manageextensions.php", array('userid' => $userid)));

if ((!empty($userid) && !\totara_job\job_assignment::is_managing($USER->id, $userid)) && !is_siteadmin()) {
    print_error('nopermissions', 'error', '', get_string('manageextensions', 'totara_program'));
}

// Don't show any information if Program extensions are not allowed on this site.
if (empty($CFG->enableprogramextensionrequests)) {
    print_error('error:notextensionallowed', 'totara_program');
}

$extensionsselceted = array_filter($extensions);
if (data_submitted() && confirm_sesskey() && (!empty($extensionsselceted))) {
    $result = prog_process_extensions($extensionsselceted, $reasons);
    if ($result) {
        $total = $result['total'];
        $failcount = $result['failcount'];
        $update_fail_count = $result['updatefailcount'];
        $update_extension_count = $total;
        if ($total == 0 && $update_fail_count == 0) {
            redirect('manageextensions.php');
        } else if ($update_fail_count == $update_extension_count && $update_fail_count > 0) {
            \core\notification::error(get_string('updateextensionfailall', 'totara_program'));
        } else if ($update_fail_count > 0) {
            \core\notification::error(get_string('updateextensionfailcount', 'totara_program', $update_fail_count));
        } else {
            \core\notification::success(get_string('updateextensionsuccess', 'totara_program'));
        }
        redirect('manageextensions.php?userid=' . $userid);
    }
}

$heading = get_string('manageextensions', 'totara_program');
$pagetitle = get_string('extensions', 'totara_program');

$PAGE->navbar->add($heading);
$PAGE->set_title($pagetitle);
$PAGE->set_heading($SITE->fullname);
echo $OUTPUT->header();

if (!empty($userid)) {
    $backstr = "&laquo" . get_string('backtoallextrequests', 'totara_program');
    $url = new moodle_url('/totara/program/manageextensions.php');
    $link = html_writer::link($url, $backstr);
    echo html_writer::start_tag('p') . $link . html_writer::end_tag('p');
}

echo $OUTPUT->heading($heading);
echo html_writer::start_tag('p') . get_string('extensionexpirywarning', 'totara_program') . html_writer::end_tag('p');

if (!empty($userid)) {
    if (!$user = $DB->get_record('user', array('id' => $userid))) {
        print_error(get_string('error:invaliduser', 'totara_program'));
    }
    $user_fullname = fullname($user);

    $staff_ids = $userid;
} else {
    $staff_ids = \totara_job\job_assignment::get_staff_userids($USER->id);
}

if (!empty($staff_ids)) {
    list($staff_sql, $staff_params) = $DB->get_in_or_equal($staff_ids);
    $sql = "SELECT * FROM {prog_extension}
        WHERE status = 0
        AND userid {$staff_sql}";

    $extensions = $DB->get_records_sql($sql, $staff_params);

    if ($extensions) {

        $columns[] = 'user';
        $headers[] = get_string('name');
        $columns[] = 'program';
        $headers[] = get_string('program', 'totara_program');
        $columns[] = 'currentdate';
        $headers[] = get_string('currentduedate', 'totara_program');
        $columns[] = 'extensiondate';
        $headers[] = get_string('extensiondate', 'totara_program');
        $columns[] = 'reason';
        $headers[] = get_string('reason', 'totara_program');
        $columns[] = 'reasonfordecision';
        $headers[] = get_string('reasonfordecision', 'totara_message');
        $columns[] = 'grant';
        $headers[] = get_string('grantdeny', 'totara_program');

        $table = new flexible_table('Extensions');
        $table->define_columns($columns);
        $table->define_headers($headers);
        $table->define_baseurl(new moodle_url("/totara/program/manageextensions.php"));
        $table->set_attribute('class', 'fullwidth');
        $table->setup();

        $options = array(
            PROG_EXTENSION_GRANT => get_string('grant', 'totara_program'),
            PROG_EXTENSION_DENY => get_string('deny', 'totara_program'),
        );

        $currenturl = qualified_me();
        echo html_writer::start_tag('form', array('id'=>'program-extension-update', 'action'=>$currenturl, 'method'=>'POST'));

        $programs = array();
        $extensionstoprocess = 0;
        foreach ($extensions as $extension) {
            $tablerow = array();

            // Get program record.
            if (!isset($programs[$extension->programid])) {
                $params = array('id' => $extension->programid);
                $programs[$extension->programid] = $DB->get_record('prog', $params, 'fullname, allowextensionrequests');
            }

            // Don't take into account programs that don't allow extension requests as they won't be processed.
            if (!$programs[$extension->programid] || $programs[$extension->programid]->allowextensionrequests == 0) {
                continue;
            }

            if ($prog_completion = $DB->get_record('prog_completion', array('programid' => $extension->programid, 'userid' => $extension->userid, 'coursesetid' => 0))) {
                $duedatestr = empty($prog_completion->timedue) ? get_string('duedatenotset', 'totara_program') : userdate($prog_completion->timedue, get_string('strftimedatetime', 'langconfig'), 99, false);
            }

            $prog_name = $programs[$extension->programid]->fullname;

            $user = $DB->get_record('user', array('id' => $extension->userid));
            $tablerow[] = fullname($user);
            $tablerow[] = $prog_name;
            $tablerow[] = $duedatestr;
            $tablerow[] = userdate($extension->extensiondate, get_string('strftimedatetime', 'langconfig'), 99, false);
            $tablerow[] = $extension->extensionreason;

            $pulldown_name = "extension[{$extension->id}]";
            $attributes = array();
            $attributes['disabled'] = false;
            $attributes['tabindex'] = 0;
            $attributes['class'] = 'approval';
            $attributes['id'] = null;

            $tablerow[] = html_writer::empty_tag('input', array('name' =>"reasondecision[{$extension->id}]", 'type' =>'text'));

            $pulldown_menu = html_writer::select($options, $pulldown_name, $extension->status, array(0 => 'choose'), $attributes);
            $tablerow[] = $pulldown_menu;
            $table->add_data($tablerow);
            $extensionstoprocess++;
        }

        if (!empty($userid)) {
            echo html_writer::tag('p', get_string('viewinguserextrequests', 'totara_program', $user_fullname));
        }

        $table->finish_html();
        if ($extensionstoprocess > 0) {
            echo html_writer::empty_tag('input', array('type' => 'hidden', 'id' => 'sesskey', 'name' => 'sesskey', 'value' => sesskey()));
            echo html_writer::empty_tag('br');
            echo html_writer::empty_tag('input', array('type' => 'submit', 'name' => 'submitbutton', 'value' => get_string('updateextensions', 'totara_program')));
        }
        html_writer::end_tag('form');

    } elseif (!empty($userid)) {
        echo html_writer::tag('p', get_string('nouserextensions', 'totara_program', $user_fullname));
    } else {
        echo html_writer::tag('p', get_string('noextensions', 'totara_program'));
    }
} else {
    echo html_writer::tag('p', get_string('notmanager', 'totara_program'));
}

echo $OUTPUT->footer();
