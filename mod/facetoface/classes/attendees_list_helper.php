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
* @author Oleg Demeshev <oleg.demeshev@totaralearning.com>
* @package mod_facetoface
*/

namespace mod_facetoface;

use mod_facetoface\signup\state\{booked, waitlisted};
use mod_facetoface\form\attendees_add_confirm;
use mod_facetoface\event\attendees_updated;
use \context_module;

defined('MOODLE_INTERNAL') || die();

final class attendees_list_helper {

    /**
     * Add attendees to seminar event via html form.
     *
     * @param \stdClass $data submitted users to add to seminar event:
     *      @var s seminar event id
     *      @var listid list id
     *      @var isapprovalrequired
     *      @var enablecustomfields
     *      @var ignoreconflicts
     *      @var is_notification_active
     *      @var notifyuser
     *      @var notifymanager
     *      @var ignoreapproval
     *      customfields optional
     */
    public static function add($data) {
        global $USER, $DB, $CFG;

        $seminarevent = new seminar_event($data->s);
        $seminar = $seminarevent->get_seminar();
        $list = new bulk_list($data->listid);

        if (empty($_SESSION['f2f-bulk-results'])) {
            $_SESSION['f2f-bulk-results'] = array();
        }

        $added  = array();
        $errors = array();

        $signedupstates = \mod_facetoface\signup\state\attendance_state::get_all_attendance_code_with([ booked::class ]);
        if (empty($seminarevent->get_sessions())) {
            $signedupstates[] = waitlisted::get_code();
        }
        $original = facetoface_get_attendees($seminarevent->get_id(), $signedupstates);

        // Get users waiting approval to add to the "already attending" list as we do not want to add them again.
        $waitingapproval = facetoface_get_requests($seminarevent->get_id());
        // Add those awaiting approval.
        foreach ($waitingapproval as $waiting) {
            if (!isset($original[$waiting->id])) {
                $original[$waiting->id] = $waiting;
            }
        }

        // Adding new attendees.
        $userlist = $list->get_user_ids();
        // Check if we need to add anyone.
        $users = attendees_add_confirm::get_user_list($userlist);
        $attendeestoadd = array_diff_key($users, $original);

        // Confirm that new attendess have job assignments when required.
        if (!empty($seminar->get_forceselectjobassignment())) {
            // Current page number.
            $page   = optional_param('page', 0, PARAM_INT);
            $currenturl = new \moodle_url('/mod/facetoface/attendees/list/addconfirm.php', ['s' => $seminarevent->get_id(), 'listid' => $list->get_list_id(), 'page' => $page]);
            foreach ($attendeestoadd as $attendeetoadd) {
                $userdata = $list->get_user_data($attendeetoadd->id);
                if (empty($userdata['jobassignmentid'])) {
                    totara_set_notification(get_string('error:nojobassignmentselectedlist', 'mod_facetoface'), $currenturl);
                }
            }
        }

        if (!empty($attendeestoadd)) {
            $clonefromform = serialize($data);
            $cm = $seminar->get_coursemodule();
            $context = \context_module::instance($cm->id);
            foreach ($attendeestoadd as $attendee) {
                if (!is_enrolled($context, $attendee)) {
                    $defaultlearnerrole = $DB->get_record('role', array('id' => $CFG->learnerroleid));
                    if (!enrol_try_internal_enrol($seminar->get_course(), $attendee->id, $defaultlearnerrole->id, time())) {
                        $errors[] = [
                            'idnumber' => $attendee->idnumber,
                            'name' => fullname($attendee),
                            'result' => get_string('error:enrolmentfailed', 'mod_facetoface', fullname($attendee))
                        ];
                        continue;
                    }
                }

                $signup = signup::create($attendee->id, $seminarevent);
                if (!empty($data->ignoreapproval)) {
                    $signup->set_skipapproval();
                }
                if (!empty($data->ignoreconflicts)) {
                    $signup->set_ignoreconflicts();
                }
                if (empty($data->notifyuser)) {
                    $signup->set_skipusernotification();
                }
                if (empty($data->notifymanager)) {
                    $signup->set_skipmanagernotification();
                }
                if ($attendee->id != $USER->id) {
                    $signup->set_bookedby($USER->id);
                }
                $userdata = $list->get_user_data($attendee->id);
                if (!empty($userdata['jobassignmentid'])) {
                    $signup->set_jobassignmentid($userdata['jobassignmentid']);
                }
                if (signup_helper::can_signup($signup)) {
                    signup_helper::signup($signup);
                    $added[] = [
                        'idnumber' => $attendee->idnumber,
                        'name' => fullname($attendee),
                        'result' => get_string('addedsuccessfully', 'mod_facetoface')
                    ];

                    // Store customfields.
                    $signupstatus = facetoface_get_attendee($seminarevent->get_id(), $attendee->id);
                    $customdata = $list->has_user_data() ? (object)$list->get_user_data($attendee->id) : $data;
                    $customdata->id = $signupstatus->submissionid;
                    customfield_save_data($customdata, 'facetofacesignup', 'facetoface_signup');
                    // Values of multi-select are changing after edit_save_data func.
                    $data = unserialize($clonefromform);
                } else {
                    $failures = signup_helper::get_failures($signup);
                    $errors[] = ['idnumber' => $attendee->idnumber, 'name' => fullname($attendee), 'result' => current($failures)];
                }
            }
        }

        // Log that users were edited.
        if (count($added) > 0 || count($errors) > 0) {
            attendees_updated::create_from_seminar_event($seminarevent, $context)->trigger();
        }
        $_SESSION['f2f-bulk-results'][$seminarevent->get_id()] = array($added, $errors);

        self::set_bulk_result_notification(array($added, $errors));

        $list->clean();
    }

    /**
     * Add attendees to seminar event via file.
     *
     * @param \stdClass $formdata users to add to seminar event via file
     *      @var s seminar event id
     *      @var listid list id
     *      @var requiredcfnames
     *      data via file
     */
    public static function add_file($formdata) {
        global $DB;

        $importid = optional_param('importid', '', PARAM_INT);

        $listid = $formdata->listid;
        $requiredcfnames = $formdata->requiredcfnames;
        $seminarevent = new seminar_event($formdata->s);
        $seminar = $seminarevent->get_seminar();
        $currenturl = new \moodle_url('/mod/facetoface/attendees/list/addfile.php', array('s' => $seminarevent->get_id(), 'listid' => $listid));
        $list = new bulk_list($listid, $currenturl, 'addfile');

        // Large files are likely to take their time and memory. Let PHP know
        // that we'll take longer, and that the process should be recycled soon
        // to free up memory.
        \core_php_time_limit::raise(0);
        @raise_memory_limit(MEMORY_EXTRA);

        $errors = array();
        if (!$importid) {
            $importid = \csv_import_reader::get_new_iid('uploaduserlist');
            $cir = new \csv_import_reader($importid, 'uploaduserlist');
            $delimiter = import_helper::csv_detect_delimiter($formdata);
            if (!$delimiter) {
                $errors[] = get_string('error:delimiternotfound', 'mod_facetoface');
            } else {
                $readcount = $cir->load_csv_content($formdata->content, $formdata->encoding, $delimiter);
                if (!$readcount) {
                    $errors[] = $cir->get_error();
                }
            }
            unset($content);
        } else {
            $cir = new \csv_import_reader($listid, 'uploaduserlist');
        }

        $headers = $cir->get_columns();
        if (!$headers) {
            $errors[] = get_string('error:csvcannotparse', 'mod_facetoface');
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
                        $errors[] = get_string('error:csvtoomanyidfields', 'mod_facetoface');
                        break;
                    }
                    $idfield = $header;
                    switch ($idfield) {
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
                            print_error(get_string('error:unknownuserfield', 'mod_facetoface'));
                    }
                }
            }
            if (empty($idfield)) {
                $errors[] = get_string('error:csvnoidfields', 'mod_facetoface');
            }
        }

        // Check that all required customfields are provided.
        if (empty($errors)) {
            $notfoundcf = array_diff($requiredcfnames, $headers);
            if (!empty($notfoundcf)) {
                $errors[] = get_string('error:csvnorequiredcf', 'mod_facetoface', implode('\', \'', $notfoundcf));
            }
        }

        // Convert headers to field names required for data storing.
        if (empty($errors)) {
            $fieldnames = array();
            foreach ($headers as $header) {
                $fieldnames[] = $header;
            }
        }

        // Prepare add users information.
        if (empty($errors)) {
            $inconsistentlines = array();
            $usersnotexist = array();
            $validationerrors = array();
            $iter = 0;
            while ($attempt = $cir->next()) {
                $iter++;

                $data = array_combine($fieldnames, $attempt);
                if (!$data) {
                    $inconsistentlines[] = $iter;
                    continue;
                }

                // Custom fields validate.
                $data['id'] = 0;
                list($cferrors, $data) = customfield_validation_filedata((object)$data, 'facetofacesignup', 'facetoface_signup');
                if (!empty($cferrors)) {
                    $errors = array_merge($errors, $cferrors);
                    continue;
                }

                // Check that user exists.
                $user = $DB->get_record('user', array($idfield => $data[$idfield]));
                if (!$user) {
                    $usersnotexist[] = $data[$idfield];
                    continue;
                } else {
                    $signup = signup::create($user->id, $seminarevent);
                    $signup->set_ignoreconflicts($formdata->ignoreconflicts);

                    if (!signup_helper::can_signup($signup)) {
                        $signuperrors = signup_helper::get_failures($signup);
                        if (!empty($signuperrors) && !isset($signuperrors['user_is_enrolable'])) {
                            $validationerrors[] = ['idnumber' => $user->idnumber, 'name' => fullname($user), 'result' => current($signuperrors)];
                        }
                    }
                }

                // Add job assignments info.
                if ($seminar->get_selectjobassignmentonsignup()) {
                    if (!empty($data['jobassignmentidnumber'])) {
                        try {
                            $jobassignment = \totara_job\job_assignment::get_with_idnumber($user->id, $data['jobassignmentidnumber'], true);
                            $data['jobassignmentid'] = $jobassignment->id;
                        } catch(\dml_missing_record_exception $e) {
                            $a = new \stdClass();
                            $a->user = fullname($user);
                            $a->idnumber = $data['jobassignmentidnumber'];
                            $errors[] = get_string('error:xinvalidjaidnumber', 'mod_facetoface', $a);
                        }
                    }
                }

                $addusers[$user->id] = $data;
            }
            if (!empty($inconsistentlines)) {
                $errors[] = get_string('error:csvinconsistentrows', 'mod_facetoface', implode(', ', $inconsistentlines));
            }
            if (!empty($usersnotexist)) {
                $errors[] = get_string($erridstr, 'mod_facetoface', implode(', ', $usersnotexist));
            }
            if (!empty($validationerrors)) {
                $validationerrorcount = count($validationerrors);
                $validationnotification = get_string('xerrorsencounteredduringimport', 'mod_facetoface', $validationerrorcount);
                $validationnotification .= ' '. \html_writer::link('#', get_string('viewresults', 'mod_facetoface'), array('id' => 'viewbulkresults', 'class' => 'viewbulkresults'));
                $list->set_validaton_results($validationerrors);
                $errors[] = $validationnotification;
            }
        }

        if (!empty($errors)) {
            $errors = array_unique($errors);
            foreach ($errors as $error) {
                totara_set_notification($error, null, array('class' => 'notifyproblem'));
            }
        } else {
            $list->set_all_user_data($addusers);
            redirect(new \moodle_url('/mod/facetoface/attendees/list/addconfirm.php', array('s' => $seminarevent->get_id(), 'listid' => $listid, 'ignoreconflicts' => $formdata->ignoreconflicts)));
        }
    }

    /**
     * Add attendees to seminar event via textarea input.
     *
     * @param \stdClass $data submitted users to add to seminar event via textarea input
     *      @var s seminar event id
     *      @var listid list id
     *      @var csvinput textarea input
     */
    public static function add_list($data) {
        global $DB;

        $seminarevent = new seminar_event($data->s);
        $listid = $data->listid;
        $currenturl = new \moodle_url('/mod/facetoface/attendees/list/addlist.php', array('s' => $seminarevent->get_id(), 'listid' => $listid));
        $list = new bulk_list($listid, $currenturl, 'addlist');

        // Handle data.
        $rawinput = $data->csvinput;

        // Replace commas with newlines and remove carriage returns.
        $rawinput = str_replace(array("\r\n", "\r", ","), "\n", $rawinput);

        $addusers = clean_param($rawinput, PARAM_NOTAGS);
        $addusers = explode("\n", $addusers);
        $addusers = array_map('trim', $addusers);
        $addusers = array_filter($addusers);

        // Validate list and fetch users.
        switch ($data->idfield) {
            case 'idnumber':
                $field = 'idnumber';
                $errstr = 'error:idnumbernotfound';
                break;
            case 'email':
                $field = 'email';
                $errstr = 'error:emailnotfound';
                break;
            case 'username':
                $field = 'username';
                $errstr = 'error:usernamenotfound';
                break;
            default:
                print_error(get_string('error:unknownuserfield', 'mod_facetoface'));
        }

        // Validate every user.
        $added = array();
        $notfound = array();
        $userstoadd = array();
        $validationerrors = array();

        if (!empty($addusers)) {
            list($insql, $params) = $DB->get_in_or_equal($addusers, SQL_PARAMS_NAMED);
            $availableusers = $DB->get_records_sql("SELECT * FROM {user} WHERE {$field} " . $insql, $params);
            foreach ($availableusers as $id => $user) {
                $added[] = $user->{$field};
                $userstoadd[] = $user->id;
                $signup = signup::create($user->id, $seminarevent);
                $signup->set_ignoreconflicts($data->ignoreconflicts);
                if (!signup_helper::can_signup($signup)) {
                    $signuperrors = signup_helper::get_failures($signup);
                    if (!empty($signuperrors) && !isset($signuperrors['user_is_enrolable'])) {
                        $validationerrors[] =
                            ['idnumber' => $user->idnumber, 'name' => fullname($user), 'result' => implode(',', $signuperrors)];
                    }
                }
            }
            $notfound = array_diff_key($addusers, $added);
        }

        // Check for data.
        if (empty($addusers)) {
            totara_set_notification(get_string('error:nodatasupplied', 'mod_facetoface'), null, array('class' => 'notifyproblem'));
        } else if (!empty($notfound)) {
            $notfoundlist = implode(', ', $notfound);
            totara_set_notification(get_string($errstr, 'mod_facetoface', $notfoundlist), null, array('class' => 'notifyproblem'));
        } else if (!empty($validationerrors)) {
            $validationerrorcount = count($validationerrors);
            $validationnotification = get_string('xerrorsencounteredduringimport', 'mod_facetoface', $validationerrorcount);
            $validationnotification .= ' '. \html_writer::link('#', get_string('viewresults', 'mod_facetoface'), array('id' => 'viewbulkresults', 'class' => 'viewbulkresults'));
            $list->set_validaton_results($validationerrors);
            totara_set_notification($validationnotification, null, array('class' => 'notifyproblem'));
        } else {
            $list->set_user_ids($userstoadd);
            $list->set_form_data($data);
            redirect(new \moodle_url('/mod/facetoface/attendees/list/addconfirm.php', array('s' => $seminarevent->get_id(), 'listid' => $listid, 'ignoreconflicts' => $data->ignoreconflicts)));
        }
    }

    /**
     * Remove attendees from seminar event.
     *
     * @param \stdClass $data submitted remove users confirmation form data
     *      @var s seminar event id
     *      @var listid list id
     *      @var notifyuser
     *      @var notifymanager
     *      customfields optional
     */
    public static function remove($data) {

        $listid = $data->listid;
        $seminarevent = new seminar_event($data->s);
        $list = new bulk_list($listid);

        if (empty($_SESSION['f2f-bulk-results'])) {
            $_SESSION['f2f-bulk-results'] = array();
        }

        $removed  = array();
        $errors = array();
        // Original booked attendees plus those awaiting approval
        if ($seminarevent->is_sessions()) {
            $original = facetoface_get_attendees($seminarevent->get_id(), array(MDL_F2F_STATUS_BOOKED, MDL_F2F_STATUS_NO_SHOW,
                MDL_F2F_STATUS_PARTIALLY_ATTENDED, MDL_F2F_STATUS_FULLY_ATTENDED));
        } else {
            $original = facetoface_get_attendees($seminarevent->get_id(), array(MDL_F2F_STATUS_WAITLISTED, MDL_F2F_STATUS_BOOKED, MDL_F2F_STATUS_NO_SHOW,
                MDL_F2F_STATUS_PARTIALLY_ATTENDED, MDL_F2F_STATUS_FULLY_ATTENDED));
        }

        // Get users waiting approval to add to the "already attending" list as we might want to remove them as well.
        $waitingapproval = facetoface_get_requests($seminarevent->get_id());
        // Add those awaiting approval
        foreach ($waitingapproval as $waiting) {
            if (!isset($original[$waiting->id])) {
                $original[$waiting->id] = $waiting;
            }
        }

        // Removing old attendees.
        // Check if we need to remove anyone.
        $attendeestoremove = array_intersect_key($original, $data->users);
        if (!empty($attendeestoremove)) {
            $clonefromform = serialize($data);
            foreach ($attendeestoremove as $attendee) {
                $result = array();
                $result['idnumber'] = $attendee->idnumber;
                $result['name'] = fullname($attendee);

                $signup = signup::create($attendee->id, $seminarevent);
                if (signup_helper::can_user_cancel($signup)) {
                    if (empty($data->notifyuser)) {
                        $signup->set_skipusernotification();
                    }
                    if (empty($data->notifymanager)) {
                        $signup->set_skipmanagernotification();
                    }

                    signup_helper::user_cancel($signup);
                    notice_sender::signup_cancellation($signup);

                    // Store customfields.
                    $signupstatus = facetoface_get_attendee($seminarevent->get_id(), $attendee->id);
                    $customdata = $list->has_user_data() ? (object)$list->get_user_data($attendee->id) : $data;
                    $customdata->id = $signupstatus->submissionid;
                    customfield_save_data($customdata, 'facetofacecancellation', 'facetoface_cancellation');
                    // Values of multi-select are changed after edit_save_data func.
                    $data = unserialize($clonefromform);

                    $result['result'] = get_string('removedsuccessfully', 'mod_facetoface');
                    $removed[] = $result;
                } else {
                    $result['result'] = get_string('error:cannotcancel', 'mod_facetoface');
                    $errors[] = $result;
                }
            }
        }

        // Log that users were edited.
        if (count($removed) > 0 || count($errors) > 0) {
            $session = facetoface_get_session($seminarevent->get_id());
            $cm = $seminarevent->get_seminar()->get_coursemodule();
            $context = context_module::instance($cm->id);
            \mod_facetoface\event\attendees_updated::create_from_session($session, $context)->trigger();
        }
        $_SESSION['f2f-bulk-results'][$seminarevent->get_id()] = array($removed, $errors);

        self::set_bulk_result_notification(array($removed, $errors), 'bulkremove');

        $list->clean();
    }

    /**
     * Get a list of status codes depending from booked state.
     * @param bool $allbooked
     * @return array
     *
     * @deprecated since Totara 13
     */
    public static function get_status($allbooked = false) {

        debugging('attendees_list_helper::get_status() function has been deprecated, please use attendees_helper::get_status()',
            DEBUG_DEVELOPER);

        return \mod_facetoface\attendees_helper::get_status($allbooked);
    }

    /**
     * Prepare exit as session id is missed.
     * @param string $page
     *
     * @deprecated since Totara 13
     */
    public static function process_no_sessionid(string $page = 'view') {
        debugging('attendees_list_helper::process_no_sessionid() function has been deprecated, please use attendees_helper::process_no_sessionid()',
            DEBUG_DEVELOPER);

        \mod_facetoface\attendees_helper::process_no_sessionid($page);
    }

    /**
     * @param $action
     * @param seminar $seminar
     * @param seminar_event $seminar_event
     *
     * @deprecated since Totara 13
     */
    public static function process_js($action, \mod_facetoface\seminar $seminar, \mod_facetoface\seminar_event $seminar_event) {
        debugging('attendees_list_helper::process_js() function has been deprecated, please use attendees_helper::process_js()',
            DEBUG_DEVELOPER);

        \mod_facetoface\attendees_helper::process_js($action, $seminar, $seminar_event);
    }

    /**
     * Get allowed actions are actions the user has permissions to do
     * Get available actions are actions that have a point.
     * e.g. view the cancellations page when there are no cancellations is not an "available" action,
     * but it maybe be an "allowed" action
     *
     * @param seminar $seminar
     * @param seminar_event $seminarevent
     * @param $context
     * @param $session
     * @return array
     *
     * @deprecated since Totara 13
     */
    public static function get_allowed_available_actions(\mod_facetoface\seminar $seminar,
        \mod_facetoface\seminar_event $seminarevent, $context, $session) {

        debugging('attendees_list_helper::get_allowed_available_actions() function has been deprecated, please use attendees_helper::get_allowed_available_actions()',
            DEBUG_DEVELOPER);

        return \mod_facetoface\attendees_helper::get_allowed_available_actions($seminar, $seminarevent, $context, $session);
    }

    /**
     * Sets totara_set_notification message describing bulk import results
     * @param array $results
     * @param string $type
     */
    public static function set_bulk_result_notification($results, $type = 'bulkadd') {
        $added          = $results[0];
        $errors         = $results[1];
        $result_message = '';

        $noticeclass = 'notifysuccess';
        // Generate messages
        if ($errors) {
            $noticeclass = 'notifyproblem';
            $result_message .= get_string($type.'attendeeserror', 'facetoface') . ' - ';

            if (count($errors) == 1 && is_string($errors[0])) {
                $result_message .= $errors[0];
            } else {
                $result_message .= get_string('xerrorsencounteredduringimport', 'facetoface', count($errors));
                $result_message .= \html_writer::link('#', get_string('viewresults', 'mod_facetoface'), ['class' => 'f2f-import-results']);
            }
        } else if ($added) {
            $result_message .= get_string($type.'attendeessuccess', 'facetoface') . ' - ';
            if ($type == 'bulkremove') {
                $result_message .= get_string('successfullyremovedxattendees', 'facetoface', count($added));
            } else {
                $result_message .= get_string('successfullyaddededitedxattendees', 'facetoface', count($added));
            }
            $result_message .= ' ' . \html_writer::link('#', get_string('viewresults', 'mod_facetoface'), ['class' => 'f2f-import-results']);
        }

        if ($result_message != '') {
            totara_set_notification($result_message, null, array('class' => $noticeclass));
        }
    }
}
