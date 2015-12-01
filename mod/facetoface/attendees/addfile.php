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
 * @author Valerii Kuznetsov <valerii.kuznetsov@totaralms.com>
 * @package mod_facetoface
 */
require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/config.php');
require_once($CFG->dirroot . '/mod/facetoface/lib.php');
require_once($CFG->dirroot . '/mod/facetoface/attendees/forms.php');

$s = required_param('s', PARAM_INT);
$listid = optional_param('listid', uniqid('f2f'), PARAM_ALPHANUM);
$importid = optional_param('importid', '', PARAM_INT);
$currenturl = new moodle_url('/mod/facetoface/attendees/addfile.php', array('s' => $s, 'listid' => $listid));
$returnurl = new moodle_url('/mod/facetoface/attendees.php', array('s' => $s, 'backtoallsessions' => 1));

$addusers = array();

list($session, $facetoface, $course, $cm, $context) = facetoface_get_env_session($s);
// Check capability
require_login($course, false, $cm);
require_capability('mod/facetoface:addattendees', $context);

$PAGE->set_context($context);
$PAGE->set_url($currenturl);
$PAGE->set_cm($cm);
$PAGE->set_pagelayout('standard');

$list = new \mod_facetoface\bulk_list($listid, $currenturl, 'addfile');

$customfields = customfield_get_fields_definition('facetoface_signup');

// Get list of required customfields.
$requiredcfnames = array();
$customfieldnames = array();

foreach($customfields as $customfield) {
    if ($customfield->locked || $customfield->hidden) {
        continue;
    }
    if ($customfield->required) {
        $requiredcfnames[] = $customfield->shortname;
    }
    $customfieldnames[] = $customfield->shortname;
}

$mform = new facetoface_bulkadd_file_form(null, array(
    's' => $s,
    'listid' => $listid,
    'customfields' => $customfieldnames,
    'requiredcustomfields' => $requiredcfnames
    ));

if ($mform->is_cancelled()) {
    $list->clean();
    redirect($returnurl);
}

// Check if data submitted
if ($formdata = $mform->get_data()) {
    // Large files are likely to take their time and memory. Let PHP know
    // that we'll take longer, and that the process should be recycled soon
    // to free up memory.
    core_php_time_limit::raise(0);
    @raise_memory_limit(MEMORY_EXTRA);

    $errors = array();
    if (!$importid) {
        $importid = csv_import_reader::get_new_iid('uploaduserlist');

        $cir = new csv_import_reader($importid, 'uploaduserlist');
        $content = $mform->get_file_content('userfile');
        $readcount = $cir->load_csv_content($content, $formdata->encoding, 'comma');
        if (!$readcount) {
            $errors[] = get_string('error:nodatasupplied', 'facetoface');
        }
        unset($content);
    } else {
        $cir = new csv_import_reader($listid, 'uploaduserlist');
    }

    $headers = $cir->get_columns();
    if (!$headers) {
        $errors[] = get_string('error:csvcannotparse', 'facetoface');
    }

    $cir->init();

    // Get headers and id column.
    $idfield = '';
    $erridstr = '';
    if (empty($errors)) {
        // Validate user identification fields.
        foreach ($headers as $header) {
            if (in_array($header, array('idnumber', 'username', 'email'))) {
                if ($idfield != '') {
                    $errors[] = get_string('error:csvtoomanyidfields', 'facetoface');
                    break;
                }
                $idfield = $header;
                switch($idfield) {
                    case 'idnumber':
                        $erridstr = 'error:idnumbernotfound';
                        break;
                    case 'email':
                        $erridstr = 'error:emailnotfound';
                        break;
                    case 'username':
                        $erridstr = 'error:usernamenotfound';
                        break;
                    default:
                        print_error(get_sring('error:unknownuserfield', 'facetoface'));
                }
            }
        }
        if (empty($idfield)) {
            $errors[] = get_string('error:csvnoidfields', 'facetoface');
        }
    }

    // Check that all required customfields are provided.
    if (empty($errors)) {
        if (!empty($requiredcfnames)) {
            $notfoundcf = array_diff($requiredcfnames, $headers);
            if (!empty($notfoundcf)) {
                $errors[] = get_string('error:csvnorequiredcf', 'facetoface', implode('\', \'', $notfoundcf));
            }
        }
    }

    // Convert headers to field names required for data storing.
    $fieldnames = array();
    foreach ($headers as $header) {
        if (in_array($header, $customfieldnames)) {
            $fieldnames[] = 'customfield_' . $header;
        } else {
            $fieldnames[] = $header;
        }
    }

    // Prepare add users information.
    if (empty($errors)) {
        $inconsistentlines = array();
        $usersnotexist = array();
        $iter = 0;
        while ($signup = $cir->next()) {
            $iter++;
            $data = array_combine($fieldnames, $signup);
            if(!$data) {
                $inconsistentlines[] = $iter;
                continue;
            }
            // Check that user exists.
            $userid = $DB->get_field('user', 'id', array($idfield => $data[$idfield]));
            if (!$userid) {
                $usersnotexist[] = $data[$idfield];
                continue;
            }

            // Custom fields validate.
            $data['id'] = 0;
            $cferrors = customfield_validation((object)$data, 'facetofacesignup', 'facetoface_signup');
            if (!empty($cferrors)) {
                 $errors = array_merge($errors, $cferrors);
                 continue;
            }

            $addusers[$userid] = $data;
        }
        if (!empty($inconsistentlines)) {
            $errors[] = get_string('error:csvinconsistentrows', 'facetoface', implode(', ', $inconsistentlines));
        }
        if (!empty($usersnotexist)) {
            $errors[] = get_string($erridstr, 'facetoface', implode(', ', $usersnotexist));
        }
    }

    if (!empty($errors)) {
        foreach ($errors as $error) {
            totara_set_notification($error, null, array('class' => 'notifyproblem'));
        }
    } else {
        $list->set_user_data($addusers);
        redirect(new moodle_url('/mod/facetoface/attendees/addconfirm.php', array('s' => $s, 'listid' => $listid)));
    }
}

$PAGE->set_title(format_string($facetoface->name));
$PAGE->set_heading($course->fullname);

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('addattendeestep1', 'facetoface'));
echo facetoface_print_session($session, false, true, true, false, 'f2fline');

$mform->display();

echo $OUTPUT->footer();