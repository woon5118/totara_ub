<?php
/**
 * This file is part of Totara LMS
 *
 * Copyright (C) 2017 onwards Totara Learning Solutions LTD
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
 * @author Rob tyler <rob.tyler@totaralearning.com>
 * @package admin
 */

require_once('../config.php');
require_once($CFG->dirroot . '/totara/reportbuilder/lib.php');

// Reportbuilder basic arguments
$debug = optional_param('debug', false, PARAM_BOOL); // Debug reportbuilder.
$sid = optional_param('sid', '0', PARAM_INT);
$format = optional_param('format', '', PARAM_TEXT); // Export format.
$page = optional_param('spage', 0, PARAM_INT);

// Actions on this page.
$delete = optional_param('delete', 0, PARAM_INT);
$undelete = optional_param('undelete', 0, PARAM_INT);
$confirm = optional_param('confirm', '', PARAM_ALPHANUM);   //md5 confirmation hash
$confirmuser = optional_param('confirmuser', 0, PARAM_INT);
$suspend = optional_param('suspend', 0, PARAM_INT);
$unsuspend = optional_param('unsuspend', 0, PARAM_INT);
$unlock = optional_param('unlock', 0, PARAM_INT);
$returnurl = optional_param('returnurl', '', PARAM_LOCALURL);

admin_externalpage_setup('editusers');

// If the legacy report should be used redirect the user there.
if (!empty($CFG->uselegacybrowselistofusersreport)) {
    redirect(new moodle_url("/admin/user_legacy.php"));
}

// Improve the page URL, but ensure the navigation stays the same.
\navigation_node::override_active_url($PAGE->url);
$PAGE->set_url(new moodle_url('/admin/user.php', array('spage' => $page)));

$sitecontext = context_system::instance();

// Process any actions.
if ($confirmuser || $delete || $undelete || $suspend || $unsuspend || $unlock) {

    $actions = 0;
    $action = null;
    $actioncap = 'moodle/user:update';
    $userparams = null;
    if ($confirmuser) {
        $action = 'confirmuser';
        $userparams = array('id' => $confirmuser, 'mnethostid' => $CFG->mnet_localhost_id);
        $actions++;
    }
    if ($delete) {
        $action = 'delete';
        $actioncap = 'moodle/user:delete';
        $userparams = array('id' => $delete, 'mnethostid' => $CFG->mnet_localhost_id);
        $actions++;
    }
    if ($undelete) {
        $action = 'undelete';
        $actioncap = 'totara/core:undeleteuser';
        $userparams = array('id' => $undelete, 'mnethostid' => $CFG->mnet_localhost_id);
        $actions++;
    }
    if ($suspend) {
        $action = 'suspend';
        $userparams = array('id' => $suspend, 'mnethostid' => $CFG->mnet_localhost_id, 'deleted' => 0);
        $actions++;
    }
    if ($unsuspend) {
        $action = 'unsuspend';
        $userparams = array('id' => $unsuspend, 'mnethostid' => $CFG->mnet_localhost_id, 'deleted' => 0);
        $actions++;
    }
    if ($unlock) {
        $action = 'unlock';
        $userparams = array('id' => $unlock, 'mnethostid' => $CFG->mnet_localhost_id, 'deleted' => 0);
        $actions++;
    }

    if ($actions > 1) {
        throw new coding_exception('Invalid number of actions requested.', $actions . ' actions, last was ' . $action);
    }

    // All actions must require the sesskey.
    require_sesskey();
    require_capability($actioncap, $sitecontext);

    $user = $DB->get_record('user', $userparams, '*', MUST_EXIST);
    $preg_emailhash = '/^[0-9a-f]{32}$/i';

    if ($returnurl) {
        $redirecturl = $returnurl;
    } else {
        $redirecturl = clone $PAGE->url;
    }
    $redirectmessage = '';
    $redirectdelay = null;
    $redirectmessagetype = \core\notification::INFO;

    switch ($action) {
        case 'confirmuser':

            /** @var auth_plugin_base|auth_plugin_manual $auth */
            $auth = get_auth_plugin($user->auth);

            $result = $auth->user_confirm($user->username, $user->secret);

            if ($result != AUTH_CONFIRM_OK && $result != AUTH_CONFIRM_ALREADY) {
                // It didn't work.
                $redirectmessage = get_string('usernotconfirmed', '', fullname($user, true));
                $redirectmessagetype = \core\notification::ERROR;
            }
            break;

        case 'delete':

            if (is_siteadmin($user->id)) {
                print_error('useradminodelete', 'error');
            }

            if ($confirm != md5($delete)) {
                // The deletion must be confirmed.
                echo $OUTPUT->header();
                $fullname = fullname($user, true);
                echo $OUTPUT->heading(get_string('deleteuser', 'admin'));
                echo $OUTPUT->confirm(
                    get_string('deleteusercheckfull', 'totara_core', "'$fullname'"),
                    new moodle_url(
                        '/admin/user.php',
                        array(
                            'delete' => $delete,
                            'confirm' => md5($delete),
                            'sesskey' => sesskey(),
                            'returnurl' => $returnurl
                        )
                    ),
                    $returnurl
                );
                echo $OUTPUT->footer();
                die;
            }

            if (data_submitted()) {
                if (!$user->deleted) {
                    $result = delete_user($user);
                    if (!$result) {
                        // Hmm could not delete the user, inform the current user.
                        $redirectmessage = get_string('deletednot', '', fullname($user, true));
                        $redirectmessagetype = \core\notification::ERROR;
                    }
                    // Remove stale sessions.
                    \core\session\manager::gc();
                } else {
                    // The user has already been deleted.
                    // If it was a partial deletion then we want to do a full deletion now.
                    if ($CFG->authdeleteusers !== 'partial' and !preg_match($preg_emailhash, $user->email)) {
                        // Do the real delete again - discard the username, idnumber and email.
                        $trans = $DB->start_delegated_transaction();
                        $DB->set_field('user', 'deleted', 0, array('id' => $user->id));
                        $user->deleted = 0;
                        delete_user($user);
                        $trans->allow_commit();
                    }
                }
            }
            break;

        case 'undelete':

            if (preg_match($preg_emailhash, $user->email)) {
                // ensure we're not trying to undelete a legacy-deleted (hash in email) user
                print_error('cannotundeleteuser', 'totara_core');
            }

            $fullname = fullname($user, true);

            if ($confirm != md5($undelete)) {
                echo $OUTPUT->header();
                echo $OUTPUT->heading(get_string('undeleteuser', 'totara_core'));
                echo $OUTPUT->confirm(
                    get_string('undeletecheckfull', 'totara_core', "'$fullname'"),
                    new moodle_url(
                        '/admin/user.php',
                        array(
                            'undelete' => $undelete,
                            'confirm' => md5($undelete),
                            'sesskey' => sesskey(),
                            'returnurl' => $returnurl
                    )),
                    $returnurl
                );
                echo $OUTPUT->footer();
                die;
            } else if (data_submitted() && $user->deleted) {
                if (undelete_user($user)) {
                    $redirectmessage = get_string('undeletedx', 'totara_core', $fullname);
                    $redirectmessagetype = \core\notification::SUCCESS;
                } else {
                    $redirectmessage = get_string('undeletednotx', 'totara_core', $fullname);
                    $redirectmessagetype = \core\notification::SUCCESS;
                }
            }

            break;

        case 'suspend':

            if (is_siteadmin($user->id)) {
                throw new coding_exception('The admin user cannot be suspended');
            }

            if ($USER->id != $user->id and $user->suspended != 1) {
                $user->suspended = 1;
                // Force logout.
                \core\session\manager::kill_user_sessions($user->id);
                user_update_user($user, false);

                \totara_core\event\user_suspended::create_from_user($user)->trigger();
            }

            break;
        case 'unsuspend':

            if ($user->suspended != 0) {
                $user->suspended = 0;
                user_update_user($user, false);
            }

            break;
        case 'unlock':

            login_unlock_account($user);

            break;
        default:
            throw new coding_exception('Unknown user action requested', $action);
    }

    // All actions lead to a redirect - no way around this!
    redirect($redirecturl);
}

$reportshortname = 'system_browse_users';
$reportrecord = $DB->get_record('report_builder', array('shortname' => $reportshortname));
$globalrestrictionset = rb_global_restriction_set::create_from_page_parameters($reportrecord);
$report = reportbuilder_get_embedded_report($reportshortname, null, false, 0, $globalrestrictionset);
if (!$report) {
    print_error('error:couldnotgenerateembeddedreport', 'totara_reportbuilder');
}

if ($format != '') {
    $report->export_data($format);
    exit();
}

\totara_reportbuilder\event\report_viewed::create_from_report($report)->trigger();

$report->include_js();

$PAGE->set_title($report->fullname);
$PAGE->set_button($report->edit_button());

echo $OUTPUT->header();

/** @var totara_reportbuilder_renderer $renderer */
$renderer = $PAGE->get_renderer('totara_reportbuilder');

list($reporthtml, $debughtml) = $renderer->report_html($report, $debug);
echo $debughtml;

$a = $renderer->result_count_info($report);
echo $OUTPUT->heading(get_string('userreportheading', 'totara_reportbuilder', $a));

$report->display_restrictions();

echo $renderer->print_description($report->description, $report->_id);

$report->display_search();
$report->display_sidebar_search();

if (has_capability('moodle/user:create', $sitecontext)) {
    $returnurl = (string) new moodle_url($PAGE->url, array(
        'sort' => optional_param('sort', 'name', PARAM_ALPHANUM),
        'dir' => optional_param('dir', 'ASC', PARAM_ALPHA),
        'page'=> optional_param('page', 0, PARAM_INT)
    ));

    $url = new moodle_url('/user/editadvanced.php', array('id' => -1, 'returnurl' => $returnurl));
    echo $OUTPUT->single_button($url, get_string('addnewuser'), 'get');

    echo $reporthtml;

    $url = new moodle_url('/user/editadvanced.php', array('id' => -1, 'returnurl' => $returnurl));
    echo $OUTPUT->single_button($url, get_string('addnewuser'), 'get');
} else {
    echo $reporthtml;
}

// Spreadsheet export. No need to check capability. They should see the same data as in the report.
$renderer->export_select($report, $sid);

echo $OUTPUT->footer();
