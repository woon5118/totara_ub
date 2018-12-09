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
 * @author Alastair Munro <alastair.munro@totaralms.com>
 * @author Oleg Demeshev <oleg.demeshev@totaralearning.com>
 * @package mod_facetoface
 */

use \mod_facetoface\signup\state\waitlisted;
use \mod_facetoface\signup\state\booked;

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->dirroot.'/mod/facetoface/lib.php');
require_once($CFG->libdir.'/totaratablelib.php');
require_once($CFG->dirroot . '/totara/core/js/lib/setup.php');

/**
 * Load and validate base data
 */
// Face-to-face session ID
$s = optional_param('s', 0, PARAM_INT);
// Action being performed, a proper default will be set shortly.
// Require for attendees.js
$action = optional_param('action', 'waitlist', PARAM_ALPHA);
// Back to all sessions.
$backtoallsessions = optional_param('backtoallsessions', 1, PARAM_BOOL);

// If there's no sessionid specified.
if (!$s) {
    \mod_facetoface\attendees_list_helper::process_no_sessionid('waitlist');
    exit;
}

list($session, $facetoface, $course, $cm, $context) = facetoface_get_env_session($s);
$seminarevent = new \mod_facetoface\seminar_event($s);
$seminar = new \mod_facetoface\seminar($seminarevent->get_facetoface());

require_login($course, false, $cm);

// Setup urls
$baseurl = new moodle_url('/mod/facetoface/attendees/waitlist.php', array('s' => $seminarevent->get_id()));

$PAGE->set_context($context);
$PAGE->set_url($baseurl);

list($allowed_actions, $available_actions, $staff, $admin_requests, $canapproveanyrequest, $cancellations, $requests, $attendees)
    = \mod_facetoface\attendees_list_helper::get_allowed_available_actions($seminar, $seminarevent, $context, $session);
$includeattendeesnote = (has_any_capability(array('mod/facetoface:viewattendeesnote', 'mod/facetoface:manageattendeesnote'), $context));

$can_view_session = !empty($allowed_actions);
if (!$can_view_session) {
    // If no allowed actions so far.
    $return = new moodle_url('/mod/facetoface/view.php', array('f' => $seminar->get_id()));
    redirect($return);
    die();
}
// $allowed_actions is already set, so we can now know if the current action is allowed.
$actionallowed = in_array($action, $allowed_actions);

/***************************************************************************
 * Handle actions
 */
$show_table = false;
$heading_message = '';
$params = array('sessionid' => $s);
$cols = array();
$actions = array();

if ($actionallowed) {
    $heading = get_string('wait-list', 'facetoface');

    $params['status'] = \mod_facetoface\signup\state\waitlisted::get_code();
    $cols = array(
        array('user', 'namelink'),
        array('user', 'email'),
    );

    $lotteryenabled = get_config(null, 'facetoface_lotteryenabled');

    $actions['confirmattendees'] = get_string('confirmattendees', 'facetoface');
    $actions['cancelattendees'] = get_string('cancelattendees', 'facetoface');
    if ($lotteryenabled) {
        $actions['playlottery'] = get_string('playlottery', 'facetoface');
    }

    $show_table = true;
}

/**
 * Print page header
 */
\mod_facetoface\attendees_list_helper::process_js($action, $seminar, $seminarevent);
\mod_facetoface\event\attendees_viewed::create_from_session($session, $context, $action)->trigger();
$PAGE->set_cm($cm);
$PAGE->set_heading($course->fullname);
echo $OUTPUT->header();

/**
 * Print page content
 */
echo $OUTPUT->box_start();
echo $OUTPUT->heading(format_string($seminar->get_name()));
if ($can_view_session) {
    /**
     * @var mod_facetoface_renderer $seminarrenderer
     */
    $seminarrenderer = $PAGE->get_renderer('mod_facetoface');
    echo $seminarrenderer->render_seminar_event($seminarevent, true, false, true);
    // Print customfields.
    $customfields = customfield_get_data($session, 'facetoface_sessioncancel', 'facetofacesessioncancel');
    if (!empty($customfields)) {
        $output = html_writer::start_tag('dl', array('class' => 'f2f'));
        foreach ($customfields as $cftitle => $cfvalue) {
            $output .= html_writer::tag('dt', str_replace(' ', '&nbsp;', $cftitle));
            $output .= html_writer::tag('dd', $cfvalue);
        }
        $output .= html_writer::end_tag('dl');
        echo $output;
    }
}
require_once($CFG->dirroot.'/mod/facetoface/attendees/tabs.php'); // If needed include tabs
echo $OUTPUT->container_start('f2f-attendees-table');

/**
 * Print attendees (if user able to view)
 */
$pix = new pix_icon('t/edit', get_string('edit'));
if ($show_table) {
    // Get list of attendees
    $rows = facetoface_get_attendees($seminarevent->get_id(), array(\mod_facetoface\signup\state\waitlisted::get_code()));
    $numattendees = facetoface_get_num_attendees($seminarevent->get_id());
    $overbooked = ($numattendees > $seminarevent->get_capacity());

    //output the section heading
    echo $OUTPUT->heading($heading);

    // Actions menu.
    if (has_any_capability(array('mod/facetoface:addattendees', 'mod/facetoface:removeattendees'), $context)) {
        if ($actions) {
            echo $OUTPUT->container_start('actions last');
            // Action selector
            echo html_writer::label(get_string('attendeeactions', 'mod_facetoface'), 'menuf2f-actions', true, array('class' => 'sr-only'));
            echo html_writer::select($actions, 'f2f-actions', '', array('' => get_string('actions')));
            echo $OUTPUT->help_icon('f2f-waitlist-actions', 'mod_facetoface');
            echo $OUTPUT->container_end();
        }
    }

    if (empty($rows)) {
        if ($seminar->is_approval_required()) {
            if (count($requests) == 1) {
                echo $OUTPUT->notification(get_string('nosignedupusersonerequest', 'facetoface'));
            } else {
                echo $OUTPUT->notification(get_string('nosignedupusersnumrequests', 'facetoface', count($requests)));
            }
        } else {
            echo $OUTPUT->notification(get_string('nosignedupusers', 'facetoface'));
        }
    } else {
        echo html_writer::tag('div', '', array('class' => 'hide', 'id' => 'noticeupdate'));

        $table = new totara_table('facetoface-attendees');
        $actionurl = clone($baseurl);
        $actionurl->params(['sesskey' => sesskey(), 'action' => $action]);
        $table->define_baseurl($actionurl);
        $table->set_attribute('class', 'generalbox mod-facetoface-attendees '.$action);

        $headers = array();
        $columns = array();

        $headers[] = get_string('name');
        $columns[] = 'name';
        $headers[] = get_string('timesignedup', 'facetoface');
        $columns[] = 'timesignedup';

        $hidecost = get_config(null, 'facetoface_hidecost');
        $hidediscount = get_config(NULL, 'facetoface_hidediscount');
        $selectjobassignmentonsignupglobal = get_config(null, 'facetoface_selectjobassignmentonsignupglobal');

        $showjobassignments = (!empty($selectjobassignmentonsignupglobal) && $seminar->get_selectjobassignmentonsignup() != 0);
        if ($showjobassignments) {
            $headers[] = get_string('selectedjobassignment', 'mod_facetoface');
            $columns[] = 'jobassignment';
        }

        // Additional approval columns for the attendees tab.
        if ($seminar->get_approvaltype() == \mod_facetoface\seminar::APPROVAL_ROLE) {
            $rolenames = role_fix_names(get_all_roles());
            $headers[] = get_string('approverrolename', 'mod_facetoface');
            $columns[] = 'approverrolename';
        }

        if ($seminar->get_approvaltype() > \mod_facetoface\seminar::APPROVAL_SELF) {
            // Display approval columns for anything except none and self approval.
            $headers[] = get_string('approvername', 'mod_facetoface');
            $columns[] = 'approvername';
            $headers[] = get_string('approvaltime', 'mod_facetoface');
            $columns[] = 'approvaltime';
        }

        if (!$hidecost) {
            $headers[] = get_string('cost', 'facetoface');
            $columns[] = 'cost';
            if (!$hidediscount) {
                $headers[] = get_string('discountcode', 'facetoface');
                $columns[] = 'discountcode';
            }
        }

        $headers[] = get_string('attendance', 'facetoface');
        $columns[] = 'attendance';

        if ($includeattendeesnote) {
            $headers[] = get_string('attendeenote', 'facetoface');
            $columns[] = 'usernote';
        }

        $headers[] = html_writer::tag('a', get_string('all'), array('href' => '#', 'class' => 'selectall'))
                . '/'
                . html_writer::tag('a', get_string('none'), array('href' => '#', 'class' => 'selectnone'));
        $columns[] = 'actions';

        $table->define_columns($columns);
        $table->define_headers($headers);
        $table->setup();

        $cancancelreservations = has_capability('mod/facetoface:reserveother', $context);
        $canchangesignedupjobassignment = has_capability('mod/facetoface:changesignedupjobassignment', $context);

        foreach ($rows as $attendee) {
            $data = array();
            // Add the name of the manager who made the booking after the user's name.
            $managername = null;
            if (!empty($attendee->bookedby)) {
                $managerurl = new moodle_url('/user/view.php', array('id' => $attendee->bookedby));
                $manager = (object)array('firstname' => $attendee->bookedbyfirstname, 'lastname' => $attendee->bookedbylastname);
                $managername = fullname($manager);
                $managername = html_writer::link($managerurl, $managername);
            }
            if ($attendee->id) {
                $attendeename = fullname($attendee);
                $attendeeurl = new moodle_url('/user/view.php', array('id' => $attendee->id, 'course' => $course->id));
                $attendeename = html_writer::link($attendeeurl, $attendeename);

                if ($managername) {
                    $strinfo = (object)array('attendeename' => $attendeename, 'managername' => $managername);
                    $attendeename = get_string('namewithmanager', 'mod_facetoface', $strinfo);
                }
                $data[] = $attendeename;
            } else {
                // Reserved space - display 'Reserved' + the name of the person who booked it.
                $cancelicon = '';
                if ($attendee->bookedby) {
                    if ($cancancelreservations) {
                        $params = array(
                            's' => $seminarevent->get_id(),
                            'managerid' => $attendee->bookedby,
                            'backtosession' => $action,
                            'cancelreservation' => 1,
                            'sesskey' => sesskey(),
                        );
                        $cancelurl = new moodle_url('/mod/facetoface/reservations/reserve.php', $params);
                        $cancelicon = $OUTPUT->pix_icon('t/delete', get_string('cancelreservation', 'mod_facetoface'));
                        $cancelicon = ' '.html_writer::link($cancelurl, $cancelicon);
                    }
                }
                if ($managername) {
                    $reserved = get_string('reservedby', 'mod_facetoface', $managername);
                } else {
                    $reserved = get_string('reserved', 'mod_facetoface');
                }
                $data[] = $reserved.$cancelicon;
            }

            $data[] = userdate($attendee->timesignedup, get_string('strftimedatetime'));

            if ($showjobassignments) {
                if (!empty($attendee->jobassignmentid)) {
                    $jobassignment = \totara_job\job_assignment::get_with_id($attendee->jobassignmentid);
                    $label = position::job_position_label($jobassignment);
                } else {
                    $label = '';
                }

                $url = new moodle_url('/mod/facetoface/attendees/ajax/job_assignment.php', array('s' => $seminarevent->get_id(), 'id' => $attendee->id));
                $icon = $OUTPUT->action_icon($url, $pix, null, array('class' => 'action-icon attendee-edit-job-assignment pull-right'));
                $jobassign = html_writer::span($label, 'jobassign'.$attendee->id, array('id' => 'jobassign'.$attendee->id));

                if ($canchangesignedupjobassignment) {
                    $data[] = $icon . $jobassign;
                } else {
                    $data[] = $jobassign;
                }
            }

            // To get the right approver & approval time we will need to get the approved status record.
            $sql = 'SELECT fss.id, fss.signupid, fs.userid, fss.createdby, fss.timecreated
                      FROM {facetoface_signups} fs
                      JOIN {facetoface_signups_status} fss
                        ON fss.signupid = fs.id
                     WHERE fs.id = :sid
                       AND fs.userid = :uid
                       AND fss.statuscode IN (' . waitlisted::get_code() . ', ' . booked::get_code() . ')
                       AND fss.createdby != fs.userid
                  ORDER BY fss.timecreated DESC';
            $params = array('sid' => $attendee->submissionid, 'uid' => $attendee->id);

            $apprecords = $DB->get_records_sql($sql, $params);
            $apprecord = array_shift($apprecords);

            // Additional approval columns for the attendees tab.
            if ($seminar->get_approvaltype() == \mod_facetoface\seminar::APPROVAL_ROLE) {
                $data[] = $rolenames[$seminar->get_approvalrole()]->localname;
            }

            if ($seminar->get_approvaltype() > \mod_facetoface\seminar::APPROVAL_SELF) {
                // It is possible for a seminar to start from a "no approval
                // needed" type to become a "manager approved" seminar even
                // after people have signed up. When this occurs, learners
                // will not be picked up by the SQL statement above - simply
                // because no approval record need to be created when they
                // were waitlisted or booked. Hence the check here.
                $approver = isset($apprecord->createdby) ? fullname($DB->get_record('user', array('id' => $apprecord->createdby))) : '';
                $approval_time = isset($apprecord->timecreated) ? userdate($apprecord->timecreated) : '';

                $data[] = $approver;
                $data[] = $approval_time;
            }

            if (!$hidecost) {
                $this_signup = mod_facetoface\signup::create($attendee->id, $seminarevent);
                $data[] = $this_signup->get_cost();
                if (!$hidediscount) {
                    $data[] = $attendee->discountcode;
                }
            }

            $state = \mod_facetoface\signup\state\state::from_code($attendee->statuscode);
            $data[] = str_replace(' ', '&nbsp;', $state::get_string());
            $icon = '';
            if (has_capability('mod/facetoface:manageattendeesnote', $context)) {
                $url = new moodle_url('/mod/facetoface/attendees/ajax/signup_notes.php', array('s' => $seminarevent->get_id(), 'userid' => $attendee->id, 'sesskey' => sesskey()));
                $showpix = new pix_icon('/t/preview', get_string('showattendeesnote', 'facetoface'));
                $icon = $OUTPUT->action_icon($url, $showpix, null, array('class' => 'action-icon attendee-add-note pull-right'));
            }
            if ($includeattendeesnote) {
                // Get signup note.
                $signupstatus = new stdClass();
                $signupstatus->id = $attendee->submissionid;
                $signupnote = customfield_get_data($signupstatus, 'facetoface_signup', 'facetofacesignup', false);
                // Currently it is possible to delete signupnote custom field easly so we must check if cf is exists.
                $signupnotetext = isset($signupnote['signupnote']) ? $signupnote['signupnote'] : '';
                $data[] = $icon . html_writer::span($signupnotetext, 'note' . $attendee->id, array('id' => 'usernote' . $attendee->id));
            }

            $d = html_writer::empty_tag('input', array('type' => 'checkbox', 'value' => $attendee->id, 'name' => 'userid'));
            $data[] = $d;
            $table->add_data($data);
        }
        $table->finish_html();
    }
}

// Go back.
if ($backtoallsessions) {
    $url = new moodle_url('/mod/facetoface/view.php', array('f' => $seminar->get_id()));
} else {
    $url = new moodle_url('/course/view.php', array('id' => $course->id));
}
echo html_writer::link($url, get_string('goback', 'facetoface')) . html_writer::end_tag('p');

/**
 * Print page footer
 */
echo $OUTPUT->container_end();
echo $OUTPUT->box_end();
echo $OUTPUT->footer($course);
