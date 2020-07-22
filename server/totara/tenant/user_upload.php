<?php
/*
 * This file is part of Totara Learn
 *
 * Copyright (C) 2020 onwards Totara Learning Solutions LTD
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
 * @author Alastair Munro <alastair.munro@totaralearning.com>
 * @package totara_tenant
 */

use totara_tenant\local\util;

require(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/admin/tool/uploaduser/locallib.php');

$tenantid = required_param('tenantid', PARAM_INT);

$tenant = core\record\tenant::fetch($tenantid);
$context = context_tenant::instance($tenant->id);

$PAGE->set_url('/totara/tenant/user_upload.php', ['tenantid' => $tenantid]);
$PAGE->set_context($context);

require_login();
require_capability('totara/tenant:view', $context);
require_capability('totara/tenant:userupload', $context);
if (empty($CFG->tenantsenabled)) {
    redirect(new moodle_url('/'));
}

// This may take a while and use lots of memory.
raise_memory_limit(MEMORY_HUGE);
core_php_time_limit::raise(120);

$returnurl = new \moodle_url('/totara/tenant/participants.php', ['id' => $tenantid]);

$formdata = new \stdClass();
$formdata->tenantid = $tenant->id;
$formdata->createpasswordifneeded = 1;
$formdata->forcepasswordchange = 1;
$formdata->encoding = 'UTF-8';

// Set defaults for fields
$formdata->default_maildisplay = \core_user::get_property_default('maildisplay');
$formdata->default_mailformat = \core_user::get_property_default('mailformat');
$formdata->default_maildigest = \core_user::get_property_default('maildigest');
$formdata->default_autosubscribe = \core_user::get_property_default('autosubscribe');
if (empty($CFG->country)) {
    $formdata->default_country = $USER->country;
} else {
    $formdata->default_country = \core_user::get_property_default('country');
}
$formdata->default_timezone = $USER->timezone;
$formdata->default_lang = $USER->lang;

$form = new totara_tenant\form\user_upload($formdata);

if ($form->is_cancelled()) {
    redirect($returnurl);
}

if ($formdata = $form->get_data()) {

    $file = reset($form->get_files()->tenant_user_upload);
    $content = $file->get_content();
    $requirepasswords = !(bool)$formdata->createpasswordifneeded;
    $resetpasswords = (bool)$formdata->forcepasswordchange;

    $defaults = [
        'idnumber' => '',
        'middlename' => '',
        'alternatename' => '',
        'firstnamephonetic' => '',
        'lastnamephonetic' => '',
    ];
    foreach ((array)$formdata as $name => $value) {
        if (strpos($name, 'default_') === 0) {
            unset($formdata->$name);
            $name = substr($name, 8);
            $defaults[$name] = $value;
            continue;
        }
    }

    list('delimitername' => $delimitername, 'error' => $csverror) = util::validate_users_csv_structure($content, $formdata->encoding, $requirepasswords);
    if ($csverror !== null) {
        throw new coding_exception('CSV file structure should have been validated during form submission!');
    }

    $iid = \csv_import_reader::get_new_iid('uploaduser');
    $cir = new \csv_import_reader($iid, 'uploaduser');

    $readcount = $cir->load_csv_content($content, $formdata->encoding, $delimitername);
    $filecolumns = $cir->get_columns();

    $existingusercount = 0;
    $userserrors = 0;
    $usersnewcount = 0;

    $stryes = get_string('yes');
    $strno = get_string('no');

    $results = [];

    echo $OUTPUT->header();
    echo $OUTPUT->heading(get_string('uploadusersresult', 'tool_uploaduser'));

    $linenum = 1;
    $result = $cir->init();
    while ($line = $cir->next()) {
        $linenum++;

        $user = [];
        foreach ($filecolumns as $key => $column) {
            if (isset($line[$key])) {
                $user[$column] = $line[$key];
            } else {
                $user[$column] = '';
            }
        }
        $user = array_map('trim', $user);

        if (util::user_username_exists($user['username'])) {
            $existingusercount++;
            $errors = [get_string('usernameexists')];
        } else {
            $errors = util::validate_users_csv_row($user, $requirepasswords);
            if ($errors) {
                $userserrors++;
            }
        }

        // Apply defaults for missing columns.
        $user = (object)array_merge($defaults, $user);

        if (!$errors) {
            // Set required fields that cannot come from upload
            $user->tenantid = $tenantid;
            $user->auth = 'manual';
            $user->mnethostid = $CFG->mnet_localhost_id;
            $user->confirmed = 1;
            $user->deleted = 0;

            if (empty($user->password)) {
                $createpassword = true;
            } else {
                $createpassword = false;
            }

            $trans = $DB->start_delegated_transaction();

            try {
                $user->id = user_create_user($user, !$createpassword, false);
                $newuser = $DB->get_record('user', ['id' => $user->id], '*', MUST_EXIST);

                // Save custom profile fields data.
                $user = uu_pre_process_custom_profile_data($user);
                profile_save_data($user);

                // Trigger create event after all fields are stored.
                core\event\user_created::create_from_userid($newuser->id)->trigger();

                if ($newuser->suspended) {
                    $createpassword = false;
                }
                if ($createpassword) {
                    set_user_preference('create_password', 1, $newuser);
                } else if ($resetpasswords) {
                    set_user_preference('auth_forcepasswordchange', 1, $newuser);
                }

                $trans->allow_commit();
                $trans = null;
                $usersnewcount++;

                $fullname = fullname($newuser);
                $url = user_get_profile_url($newuser->id);
                if ($url) {
                    $fullname = html_writer::link($url, $fullname);
                }
                $resultrow = new stdClass();
                $resultrow->line = $linenum;
                $resultrow->fullname = $fullname;
                $resultrow->username = s($newuser->username);
                $resultrow->email = s($newuser->email);
                $resultrow->idnumber = s($newuser->idnumber);
                $resultrow->suspended = ($newuser->suspended ? $stryes : $strno);
                $resultrow->errors = '';

                $results[] = $resultrow;
                continue;
            } catch (Throwable $ex) {
                if ($trans) {
                    $trans->rollback(null);
                }
                $errors[] = get_string('error') . ': ' . clean_text($ex->getMessage());
            }
        }

        $resultrow = new stdClass();
        $resultrow->line = $linenum;
        $resultrow->fullname = fullname($user);
        $resultrow->username = s($user->username);
        $resultrow->email = s($user->email);
        $resultrow->idnumber = s($user->idnumber);
        $resultrow->suspended = '';
        $resultrow->errors = implode('<br />', $errors);

        $results[] = $resultrow;
    }

    // Delete the uploaded file so that they cannot resubmit the form.
    foreach ($form->get_files()->tenant_user_upload as $file) {
        $file->delete();
    }

    $table = totara_tenant\output\upload_results_table::create($results);

    echo $OUTPUT->render_from_template($table->get_template(), $table->export_for_template($OUTPUT));

    $cir->close();
    $cir->cleanup(true);

    echo $OUTPUT->box_start('boxwidthnarrow boxaligncenter generalbox', 'uploadresults');
    echo '<p>';

    echo get_string('useruploadadded', 'totara_tenant') . ': ' . $usersnewcount . '</br />';
    if ($existingusercount) {
        echo get_string('useruploadexistingskipped', 'totara_tenant') . ': ' . $existingusercount.'<br />';
    }
    if ($userserrors) {
        echo get_string('useruploaderrors', 'totara_tenant') .': ' . $userserrors . '<br />';
    }
    echo $OUTPUT->box_end();
    echo $OUTPUT->continue_button($returnurl);
    echo $OUTPUT->footer();
    die;
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('uploadusers', 'totara_tenant'));
echo $form->render();

if ($form->is_reloaded()) {
    // The table does not render well when embedded into forms,
    // we also do not want to generate preview unnecessarily for performance reasons.
    echo $form->render_preview();
}

echo $OUTPUT->footer();
