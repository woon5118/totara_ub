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
 * @package modules
 * @subpackage facetoface
 */

use core\output\flex_icon;

use mod_facetoface\asset_list;
use mod_facetoface\attendance_taking_status;
use mod_facetoface\attendees_helper;
use mod_facetoface\event_time;
use mod_facetoface\render_event_info_option;
use mod_facetoface\reservations;
use mod_facetoface\room;
use mod_facetoface\seminar;
use mod_facetoface\seminar_event;
use mod_facetoface\seminar_event_helper;
use mod_facetoface\seminar_event_list;
use mod_facetoface\seminar_session;
use mod_facetoface\session_data;
use mod_facetoface\session_date_data;
use mod_facetoface\signup;
use mod_facetoface\signup_helper;
use mod_facetoface\trainer_helper;

use mod_facetoface\dashboard\filter_list;
use mod_facetoface\dashboard\render_session_option;
use mod_facetoface\dashboard\filters\event_time_filter;
use mod_facetoface\dashboard\filters\room_filter;

use mod_facetoface\output\actions_in_dashboard;
use mod_facetoface\output\event_page_navigation;
use mod_facetoface\output\seminarevent_actionbar;
use mod_facetoface\output\seminarevent_detail;
use mod_facetoface\output\seminarevent_detail_section;
use mod_facetoface\output\seminarevent_information;
use mod_facetoface\output\session_time;

use mod_facetoface\query\event\filter\event_times_filter as query_event_times_filter;
use mod_facetoface\query\event\sortorder\future_sortorder;
use mod_facetoface\query\event\sortorder\past_sortorder;

use mod_facetoface\signup\state\attendance_state;
use mod_facetoface\signup\state\booked;
use mod_facetoface\signup\state\declined;
use mod_facetoface\signup\state\not_set;
use mod_facetoface\signup\state\user_cancelled;
use mod_facetoface\signup\state\waitlisted;

defined('MOODLE_INTERNAL') || die();

/**
 * mod_facetoface renderer class.
 */
class mod_facetoface_renderer extends plugin_renderer_base {

    /**
     * Query parameter names.
     */
    /** @deprecated Totara 13 */
    const PARAM_FILTER_F2FID = 'f';
    /** @deprecated Totara 13 */
    const PARAM_FILTER_ROOMID = 'roomid';
    /** @deprecated Totara 13 */
    const PARAM_FILTER_EVENTTIME = 'eventtime';

    /**
     * Default sign-up link
     */
    const DEFAULT_SIGNUP_LINK = '/mod/facetoface/eventinfo.php';

    /**
     * This link will be changed if mod_facetoface\hook\alternative_signup_link hook is installed.
     * @var string
     */
    protected $signuplink = self::DEFAULT_SIGNUP_LINK;

    /** @var context|null */
    protected $context = null;

    /**
     * Outputs a table showing a list of sessions along with other information.
     *
     * Note some aspects of the API used here that may not be obvious:
     *
     * It's assumed that these sessions are all from the same facetoface activity.
     *
     * *** If a session has been booked by the user ***
     * If a session has been booked, then that one session should have a bookedsession property
     * containing that information. An appropriate object for this property would be the
     * first result from facetoface_get_user_submissions, obtained via array_shift.
     *
     * If 'multiple signups' is not allowed for the facetoface activity, then if a session has been booked, it should
     * be added to the bookedsession property of each session. It'll display the right data for each session
     * by checking for whether the id of booked session matches that of the session row being processed.
     *
     * @param (session_data|stdClass)[] $sessions - array of session objects.
     * @param bool $viewattendees - true if the current user has this capability ('mod/facetoface:viewattendees').
     * @param bool $editevents - true if the current user has this capability ('mod/facetoface:editevents').
     * @param bool $displaytimezones - true if the timezones should be displayed.
     * @param array $reserveinfo - if managereserve if set to true for the facetoface, use facetoface_can_reserve_or_allocate
     * to fill out this array.
     * @param string|null $currenturl - generally this would be $PAGE->url.
     * @param bool $minimal - setting this to true will hide customfields, session status, attendance tracking and registration dates
     * in a tooltip when hovering over the signup link rather than in a column.
     * @param bool $returntoallsessions Returns the user to view all sessions after they signup/cancel.
     * @param bool|int $sessionattendance - one of seminar::SESSION_ATTENDANCE_xxx
     * @param int $eventattendance - one of seminar::EVENT_ATTENDANCE_xxx
     * @param bool $viewsignupperiod - true to display the "Sign-up period" table column
     * @param bool $viewactions - true to display the "Actions" table column
     * @return string containing html for this table.
     * @throws coding_exception
     */
    public function print_session_list_table(array $sessions, bool $viewattendees, bool $editevents, bool $displaytimezones, array $reserveinfo = array(),
                                             string $currenturl = null, bool $minimal = false, bool $returntoallsessions = true,
                                             $sessionattendance = seminar::SESSION_ATTENDANCE_DISABLED,
                                             int $eventattendance = seminar::EVENT_ATTENDANCE_LAST_SESSION_END,
                                             bool $viewsignupperiod = true, bool $viewactions = true): string {
        $output = '';

        if (empty($sessions)) {
            // If there's no sessions, just return an empty string.
            return $output;
        }

        $sessionattendance = seminar::fix_up_session_attendance_time_with($eventattendance, $sessionattendance);
        $attendancetracking = $sessionattendance != seminar::SESSION_ATTENDANCE_DISABLED;
        if ($this->context === null || !has_capability('mod/facetoface:takeattendance', $this->context)) {
            // overwrite $attendancetracking if a user does not have permission to take attendance
            $attendancetracking = false;
        }

        $tableheader = array();

        // If we want the minimal table, no customfield columns are shown.
        if (!$minimal) {
            $customfields = customfield_get_fields_definition('facetoface_session', array('hidden' => 0));
            foreach ($customfields as $customfield) {
                if (!empty($customfield->showinsummary)) {
                    $tableheader[] = format_string($customfield->fullname);
                }
            }
        }

        if ($viewattendees) {
            $tableheader[] = get_string('capacity', 'mod_facetoface');
        } else {
            $tableheader[] = get_string('seatsavailable', 'mod_facetoface');
        }
        $tableheader[] = get_string('sessionslist:eventstatus', 'mod_facetoface');

        // If we want the minimal table, the registration dates are shown in a tooltip instead of a column.
        if (!$minimal && $viewsignupperiod) {
            $tableheader[] = get_string('signupperiodheader', 'mod_facetoface');
        }
        if (!empty($displaytimezones)) {
            $tableheader[] = get_string('sessionslist:timeandtimezone', 'mod_facetoface');
        } else {
            $tableheader[] = get_string('sessionslist:time', 'mod_facetoface');
        }

        $tableheader[] = get_string('rooms', 'mod_facetoface');
        $tableheader[] = get_string('facilitators', 'mod_facetoface');

        if (!$minimal) {
            $tableheader[] = get_string('sessionslist:sessionstatus', 'mod_facetoface');
            if ($attendancetracking) {
                $tableheader[] = get_string('sessionslist:attendancetracking', 'mod_facetoface');
            }
        }

        if ($viewactions) {
            $tableheader[] = get_string('sessionslist:actions', 'mod_facetoface');
        }

        $table = new html_table();
        $table->summary = get_string('sessionslist', 'mod_facetoface');
        $table->attributes['class'] = 'mod_facetoface__sessionlist__table';
        $table->head = array_map(
            function($value) {
                return s($value);
            },
            $tableheader
        );
        $table->data = array();

        $includedeleted = has_capability('totara/core:seedeletedusers', $this->getcontext());

        foreach ($sessions as $session) {
            $seminarevent = (new seminar_event())->from_record_with_dates($session, false);
            $isbookedsession = (!empty($session->bookedsession) && ($session->id == $session->bookedsession->sessionid));
            $signupcount = attendees_helper::count_signups($seminarevent, $includedeleted);

            if (!$seminarevent->is_sessions()) {
                // An event without session dates, is a wait-listed event
                $sessionrow = array();

                if (!$minimal) {
                    $sessionrow = array_merge($sessionrow, $this->session_customfield_table_cells($session, $customfields));
                }

                // Capacity
                $sessionrow[] = $this->session_capacity_table_cell($seminarevent, $viewattendees, $signupcount);

                // Event status
                $sessionrow[] = $this->event_status_table_cell($session, $signupcount, 0, $eventattendance);

                if (!$minimal && $viewsignupperiod) {
                    // Sign-up period
                    $sessionrow[] = $this->session_resgistrationperiod_table_cell($seminarevent, 0, $displaytimezones);
                }

                // Session times (empty cell)
                $sessionrow[] = '';

                // Rooms (empty cell)
                $sessionrow[] = '';

                // Facilitators (empty cell)
                $sessionrow[] = '';

                if (!$minimal) {
                    // Session status
                    $sessionrow[] = $this->session_status_table_cell($session, null, 0);

                    if ($attendancetracking) {
                        // Attendance tracking (empty cell)
                        $sessionrow[] = '';
                    }
                }

                if ($viewactions) {
                    // Actions
                    $sessionrow[] = $this->session_options_table_cell($seminarevent, $viewattendees, $editevents);
                }

                $row = new html_table_row($sessionrow);

                // Set the CSS class for the row.
                $row->attributes['class'] .= ' mod_facetoface__sessionlist__table__sessionrow ' . $this->session_list_row_status_classes($seminarevent, $isbookedsession, $signupcount);

                // Add row to table.
                $table->data[] = $row;
            } else {
                // If there are session dates, we create one row per session date, but some will be
                // given a rowspan value as they apply to the whole session rather than just the session date.

                // Make a copy of sessiondates to prevent seminar_event::get_xxx() from trashing it
                $sessiondates = clone $seminarevent->get_sessions();
                $datescount = $sessiondates->count();
                $firstsessiondate = true;

                /** @var \mod_facetoface\seminar_session $date */
                foreach ($sessiondates as $date) {
                    $sessionrow = [];
                    if ($firstsessiondate && !$minimal) {
                        $sessionrow = array_merge($sessionrow, $this->session_customfield_table_cells($session, $customfields, $datescount));
                    }

                    if ($firstsessiondate) {
                        // Capacity
                        $sessionrow[] = $this->session_capacity_table_cell($seminarevent, $viewattendees, $signupcount, $datescount);
                        // Event status
                        $sessionrow[] = $this->event_status_table_cell($session, $signupcount, $datescount, $eventattendance);
                        if (!$minimal && $viewsignupperiod) {
                            // Sign-up period
                            $sessionrow[] = $this->session_resgistrationperiod_table_cell($seminarevent, $datescount, $displaytimezones);
                        }
                    }

                    // Session times
                    $sessionrow[] = session_time::to_html($date->get_timestart(), $date->get_timefinish(), $date->get_sessiontimezone(), $displaytimezones);

                    // Rooms.
                    $time = time();
                    $rooms = \mod_facetoface\room_list::from_session($date->get_id());
                    if (!$rooms->is_empty()) {
                        $roomoutput = html_writer::start_tag('ul', ['class' => 'roomlist']);
                        /** @var \mod_facetoface\room $room */
                        foreach ($rooms as $room) {
                            $roomstring = $this->get_room_details_html($room, $currenturl);
                            $roomoutput .= html_writer::tag('li', $roomstring, ['class' => 'room']);
                        }
                        $roomoutput .= html_writer::end_tag('ul');
                        $cell = new html_table_cell($roomoutput);
                        $cell->attributes['class'] = 'mod_facetoface__sessionlist__room';
                        $sessionrow[] = $cell;
                    } else {
                        $sessionrow[] = ''; // (empty)
                    }

                    // Facilitators.
                    $facilitators = \mod_facetoface\facilitator_list::from_session($date->get_id());
                    if (!$facilitators->is_empty()) {
                        $facilitatoroutput = html_writer::start_tag('ul', ['class' => 'facilitatorlist']);
                        /** @var \mod_facetoface\facilitator_user $facilitator */
                        foreach ($facilitators as $facilitator) {
                            $facilitatorstring = $this->get_facilitator_details($facilitator, $currenturl);
                            $facilitatoroutput .= html_writer::tag('li', $facilitatorstring, ['class' => 'facilitator']);
                        }
                        $facilitatoroutput .= html_writer::end_tag('ul');
                        $cell = new html_table_cell($facilitatoroutput);
                        $cell->attributes['class'] = 'mod_facetoface__sessionlist__facilitator';
                        $sessionrow[] = $cell;
                    } else {
                        $sessionrow[] = ''; // (empty)
                    }

                    if (!$minimal) {
                        $daterecord = $date->to_record();
                        // Session status
                        $sessionrow[] = $this->session_status_table_cell($session, $daterecord, $time);

                        if ($attendancetracking) {
                            // Attendance tracking
                            $sessionrow[] = $this->attendance_tracking_table_cell(
                                $session,
                                $daterecord,
                                $sessionattendance
                            );
                        }
                    }

                    if ($firstsessiondate) {
                        if ($viewactions) {
                            // Actions
                            $sessionrow[] = $this->session_options_table_cell($seminarevent, $viewattendees, $editevents);
                        }
                    }

                    $row = new html_table_row($sessionrow);

                    // Set the CSS class for the row.
                    $row->attributes['class'] .= ' mod_facetoface__sessionlist__table__sessionrow ' . $this->session_list_row_status_classes($seminarevent, $isbookedsession, $signupcount);
                    if ($firstsessiondate) {
                        $row->attributes['class'] .= ' firstsession';
                    }

                    // $firsessiondate should only be true on the iteration of this foreach loop.
                    $firstsessiondate = false;

                    // Add row to table.
                    $table->data[] = $row;
                }
            }
        }

        if (empty($table->data)) {
            // There were sessions when we checked at the beginning, but they've been eliminated
            // for one reason or another, so just return an empty string.
            return '';
        }

        $output .= $this->render($table);

        return $output;
    }

    /**
     * Print the list of a sessions
     *
     * @param seminar $seminar
     * @param int $roomid room id
     * @param int $eventtime one of constants defined in '\mod_facetoface\event_time'
     * @return string
     * @deprecated Totara 13
     */
    public function print_session_list(seminar $seminar, int $roomid, int $eventtime = event_time::ALL): string {
        debugging('mod_facetoface_renderer::print_session_list() is deprecated. Please use mod_facetoface_renderer::render_session_list() instead.');

        // Create a dummy filter_list on the fly.
        $filters = new filter_list(function (string $parname, $default, string $type) use ($roomid, $eventtime) {
            if ($parname === self::PARAM_FILTER_ROOMID) {
                return $roomid;
            } else if ($parname === self::PARAM_FILTER_EVENTTIME) {
                return $eventtime;
            }
            return $default;
        });

        // Create a dummy render_session_option on the fly.
        $option = (new render_session_option())
            ->set_displayactions(true)
            ->set_displayreservation(true)
            ->set_displaysignupperiod(true)
            ->set_displaytimezones(null)
            ->set_eventascendingorder(false)
            ->set_sessionascendingorder(false)
            ->set_eventtimes([]);

        // Pass through render_session_list().
        return $this->render_session_list($seminar, $filters, $option);
    }

    /**
     * Print the list of a sessions
     *
     * @param seminar               $seminar    seminar instance
     * @param filter_list           $filters    filter_list instance to filter out session events
     * @param render_session_option $option     render_session_option instance for extra rendering options
     * @param integer|null          $userid     user ID or null to use the current user
     * @return string
     */
    public function render_session_list(seminar $seminar, filter_list $filters, render_session_option $option, int $userid = null): string {
        global $USER, $OUTPUT, $PAGE;

        if (empty($userid)) {
            $userid = $USER->id;
        }

        $output = '';

        $context = $this->getcontext();
        $query = $filters->to_query($seminar, $context, $userid);
        $query->with_filter(new query_event_times_filter($option->get_eventtimes()));
        if ($option->get_eventascendingorder()) {
            $query->with_sortorder(new past_sortorder());
        } else {
            $query->with_sortorder(new future_sortorder());
        }
        $seminarevents = seminar_event_list::from_query($query);

        $viewattendees = has_capability('mod/facetoface:viewattendees', $context);
        $editevents = has_capability('mod/facetoface:editevents', $context);

        $sessionarray = [];
        if (!$seminarevents->is_empty()) {
            $time = time();
            /** @var seminar_event $seminarevent */
            foreach ($seminarevents as $seminarevent) {
                $signup = signup::create($userid, $seminarevent);
                $sessiondata = seminar_event_helper::get_sessiondata($seminarevent, $signup, $option->get_sessionascendingorder(), $time);
                $sessionarray[] = $sessiondata;
            }
        }

        if (empty($sessionarray)) {
            $output .= html_writer::tag('p',
                $OUTPUT->flex_icon('info') . get_string('noresults', 'mod_facetoface'),
                [ 'class' => 'mod_facetoface__sessionlist--empty' ]
            );
        } else {
            // We need to attach reservation information here on top of the upcoming session list table if managerreserve is set.
            $reserveinfo = [];
            if ($option->get_displayreservation()) {
                if (!empty($seminar->get_managerreserve())) {
                    // Include information about reservations when drawing the list of sessions.
                    $reserveinfo = \mod_facetoface\reservations::can_reserve_or_allocate($seminar, $sessionarray, $this->context);
                    $output .= html_writer::tag('p', get_string('lastreservation', 'mod_facetoface', $seminar->get_properties()));
                }
            }

            $sessionlist = $this->print_session_list_table(
                $sessionarray,
                $viewattendees,
                $editevents,
                $option->get_displaytimezones(),
                $reserveinfo,
                $PAGE->url,
                false,
                true,
                $seminar->get_sessionattendance(),
                $seminar->get_attendancetime(),
                $option->get_displaysignupperiod(),
                ($viewattendees || $editevents || $option->is_upcoming()) && $option->get_displayactions()
            );

            $class = 'mod_facetoface__sessionlist';
            /** Both upcomingsessionlist and previoussessionlist CSS classes should be considered @deprecated as of Totara 13. */
            if ($option->is_upcoming()) {
                $class .= ' upcomingsessionlist';
            } else {
                $class .= ' previoussessionlist';
            }
            $output .= html_writer::div($sessionlist, $class);
        }

        return $output;
    }

    /**
     * Return CSS classes for the seminar event.
     *
     * @param seminar_event $seminarevent
     * @param boolean $isbookedsession
     * @param integer $signupcount
     * @return string
     */
    protected function session_list_row_status_classes(seminar_event $seminarevent, bool $isbookedsession, int $signupcount): string {
        $classes = [];
        if (!$seminarevent->is_sessions()) {
            $classes[] = 'waitlisted';
        }
        $time = 0;
        if ($seminarevent->is_first_started($time)) {
            $classes[] = 'started';
        }
        if ($seminarevent->is_over($time)) {
            $classes[] = 'over';
        }
        if ($seminarevent->get_cancelledstatus()) {
            $classes[] = 'cancelled';
        }
        if ($isbookedsession) {
            $classes[] = 'userbooked';
        }
        $sessionfull = ($signupcount >= $seminarevent->get_capacity());
        if ($sessionfull && !$seminarevent->get_allowoverbook()) {
            $classes[] = 'fullybooked';
        }
        if (!signup_helper::is_signup_open($seminarevent, $signupcount)) {
            $classes[] = 'closed';
        }
        return implode(' ', $classes);
    }

    /**
     * Add a table cells for each customfield value associated with a session.
     *
     * @param stdClass $session
     * @param array $customfields - as returned by facetoface_get_session_customfields().
     * @param int $datescount - this determines the rowspan. Count the number of session dates to get this figure.
     * @return array of table cells to be merged with an array for the rest of the cells.
     */
    private function session_customfield_table_cells(\stdClass $session, array $customfields, int $datescount = 0): array {

        $customfieldsdata = customfield_get_data($session, 'facetoface_session', 'facetofacesession', false);
        $sessionrow = array();

        foreach ($customfields as $customfield) {
            if (empty($customfield->showinsummary)) {
                continue;
            }
            if (array_key_exists($customfield->shortname, $customfieldsdata)) {
                $cell = new html_table_cell($customfieldsdata[$customfield->shortname]);
                if ($datescount > 1) {
                    $cell->rowspan = $datescount;
                }
                $sessionrow[] = $cell;
            } else {
                $cell = new html_table_cell('&nbsp;');
                if ($datescount > 1) {
                    $cell->rowspan = $datescount;
                }
                $sessionrow[] = $cell;
            }
        }

        return $sessionrow;
    }

    /**
     * Create a table cell containing a sessions capacity or seats remaining.
     *
     * If the user has viewattendees permissions, this will show capacity.
     * If not, then this will show seats remanining.
     *
     * @param seminar_event $seminarevent - An event, not session date
     * @param bool $viewattendees - true if they do have permissions.
     * @param int $signupcount - number currently signed up to this session.
     * @param int $datescount - this determines the rowspan. Count the number of session dates to get this figure.
     * @return html_table_cell
     * @throws coding_exception
     */
    private function session_capacity_table_cell(seminar_event $seminarevent, bool $viewattendees, int $signupcount, int $datescount = 0): \html_table_cell {
        $context = $this->context;
        if (null == $context) {
            // Using $PAGE->context if the internal $context had not been set.
            $context = $this->page->context;
        }

        $includedeleted = has_capability('totara/core:seedeletedusers', $context);
        $helper = new attendees_helper($seminarevent);

        if ($viewattendees) {
            if ($seminarevent->is_sessions()) {
                // All attendance statuses with booked and waitlisted status. If the event is not a wailisted event,
                // then we need to get all the attendees that have code from waitlisted to the fully_attendeed. So that
                // we can start figuring out the number of how many user is in waitlisted.
                $statuscodes = \mod_facetoface\signup\state\attendance_state::get_all_attendance_code_with(
                    [
                        \mod_facetoface\signup\state\booked::class,
                        \mod_facetoface\signup\state\waitlisted::class,
                    ]
                );


                $currenthtml = \html_writer::span($signupcount, 'mod_facetoface__capacity__current');
                $maximumhtml = \html_writer::span($seminarevent->get_capacity(), 'mod_facetoface__capacity__maximum');
                $a = array('current' => $currenthtml, 'maximum' => $maximumhtml);
                $stats = get_string('capacitycurrentofmaximum', 'mod_facetoface', $a);
                if ($signupcount > $seminarevent->get_capacity()) {
                    $stats .= get_string('capacityoverbooked', 'mod_facetoface');
                }

                $waitlisted = $helper->count_attendees_with_codes($statuscodes, $includedeleted);
                $waitlisted -= $signupcount;

                if ($waitlisted > 0) {
                    $stats .= get_string('status_capacity_waitlisted', 'mod_facetoface', $waitlisted);
                }
            } else {
                // Since within the event that has no sesison date, and user that are in wait-list could be moved to
                // attendees, and it caused the number of wait-listed user being calculated and rendered wrong.
                // If there is any user that confirm as booked, then it should display the number of booked user within current
                $currentbookeduser = $helper->count_attendees_with_codes([booked::get_code()], $includedeleted);
                $currenthtml = \html_writer::span($currentbookeduser, 'mod_facetoface__capacity__current');
                $maximumhtml = \html_writer::span($seminarevent->get_capacity(), 'mod_facetoface__capacity__maximum');
                $a = array('current' => $currenthtml, 'maximum' => $maximumhtml);
                $stats = get_string('capacitycurrentofmaximum', 'mod_facetoface', $a);
                if ($currentbookeduser > $seminarevent->get_capacity()) {
                    $stats .= get_string('capacityoverbooked', 'mod_facetoface');
                }
                if ($signupcount > 0) {
                    $stats .= get_string('status_capacity_waitlisted', 'mod_facetoface', $signupcount);
                }
            }
        } else {
            $stats = max(0, $seminarevent->get_capacity() - $signupcount);
        }

        $sessioncell = new html_table_cell($stats);
        if ($datescount > 1) {
            $sessioncell->rowspan = $datescount;
        }

        return $sessioncell;
    }

    /**
     * Create a table cell containing the link to a session attendance tracking page.
     *
     * @param session_data $session
     * @param session_date_data $date
     * @param int $attendancetime one of seminar::SESSION_ATTENDANCE_xxx
     * @return \mod_facetoface\output\attendance_tracking_table_cell
     * @throws coding_exception
     */
    protected function attendance_tracking_table_cell(\stdClass $session, \stdClass $date, int $attendancetime): \mod_facetoface\output\attendance_tracking_table_cell {
        $cell = new \mod_facetoface\output\attendance_tracking_table_cell();

        if (!empty($session->cancelledstatus)) {
            return $cell->set_state('cancelled');
        }

        $seminarevent = (new seminar_event())->from_record_with_dates($session, false);
        $helper = new attendees_helper($seminarevent);
        if (!$helper->count_attendees()) {
            return $cell
                ->set_text_l10n('attendancetracking:noattendees')
                ->set_state('none');
        }

        $seminasession = (new seminar_session())->from_record($date, false);
        $status = $seminasession->get_attendance_taking_status($attendancetime);
        switch ($status) {
            case attendance_taking_status::CLOSED_UNTILEND:
                return $cell
                    ->set_text_l10n('attendancetracking:openatend')
                    ->set_state('locked');

            case attendance_taking_status::CLOSED_UNTILSTART:
                return $cell
                    ->set_text_l10n('attendancetracking:openatstart')
                    ->set_state('locked');

            case attendance_taking_status::OPEN:
                // no break

            case attendance_taking_status::ALLSAVED:
                $url = new \moodle_url('/mod/facetoface/attendees/takeattendance.php', ['s' => $session->id, 'sd' => $date->id]);

                if ($status == attendance_taking_status::ALLSAVED) {
                    return $cell
                        ->set_link_l10n($url, 'attendancetracking:saved')
                        ->set_icon('check-circle-success')
                        ->set_state('saved');
                } else {
                    return $cell
                        ->set_link_l10n($url, 'attendancetracking:open')
                        ->set_state('open');
                }
        }

        throw new coding_exception("Invalid attendance taking status: {$status}");
    }

    /**
     * Create a table cell containing the status of a session.
     *
     * @param \stdClass $session
     * @param \stdClass|null $date
     * @param integer $timenow current time
     * @return html_table_cell
     */
    protected function session_status_table_cell(\stdClass $session, ?\stdClass $date, int $timenow): \html_table_cell {

        $status = \mod_facetoface\seminar_session_helper::get_status($session, $date, $timenow);
        if ($status === false) {
            return new html_table_cell();
        }
        return new html_table_cell($status);
    }

    /**
     * Create a table cell containing the status of an event.
     *
     * Examples would include 'In progress' or 'Booking open'.
     *
     * @param \stdClass $session
     * @param int $signupcount - number currently signed up to this session.
     * @param int $datescount - this determines the rowspan. Count the number of session dates to get this figure.
     * @param int|null $eventattendance - One of seminar::EVENT_ATTENDANCE_xxx or null to load from the seminar setting.
     * @return html_table_cell
     * @throws coding_exception
     */
    protected function event_status_table_cell(\stdClass $session, int $signupcount, int $datescount = 0, int $eventattendance = null): html_table_cell {
        [ $event_status, $booking_status, $user_status ] = seminar_event_helper::event_status($session, $signupcount, false);
        $statuses = \html_writer::tag('li', clean_string($event_status), [ 'class' => 'mod_facetoface__sessionlist__event-status__event']);
        if ($booking_status !== '') {
            $statuses .= \html_writer::tag('li', clean_string($booking_status), [ 'class' => 'mod_facetoface__sessionlist__event-status__booking']);
        }
        if ($user_status !== '') {
            $user_status = get_string('eventstatususerbooking', 'mod_facetoface', $user_status);
            $statuses .= \html_writer::tag('li', clean_string($user_status), [ 'class' => 'mod_facetoface__sessionlist__event-status__user']);
        }

        if ($this->context !== null && has_capability('mod/facetoface:takeattendance', $this->context)) {
            $attendance_status = $this->event_status_attendance_taking_html($session, $eventattendance);
            $statuses .= \html_writer::tag('li', $attendance_status, [ 'class' => 'mod_facetoface__sessionlist__event-status__attendance']);
        }
        $html = \html_writer::tag('ul', $statuses, [ 'class' => 'mod_facetoface__sessionlist__event-status' ]);
        $sessioncell = new html_table_cell($html);
        if ($datescount > 1) {
            $sessioncell->rowspan = $datescount;
        }
        return $sessioncell;
    }

    /**
     * Create a table cell containing the link to an event attendance tracking page.
     *
     * @param \stdClass $session
     * @param integer|null $eventattendance One of seminar::EVENT_ATTENDANCE_xxx, or null to load the seminar setting
     * @return string of HTML
     */
    public function event_status_attendance_taking_html(\stdClass $session, ?int $eventattendance): string {
        $seminarevent = (new seminar_event())->from_record_with_dates($session, false);
        $status = $seminarevent->get_attendance_taking_status($eventattendance, 0, true, true);
        // Put the event attendance link only when it is open.
        if ($status != attendance_taking_status::OPEN && $status != attendance_taking_status::ALLSAVED) {
            return '';
        }

        $url = new \moodle_url('/mod/facetoface/attendees/takeattendance.php', ['s' => $session->id]);
        if ($status == attendance_taking_status::ALLSAVED) {
            $html = $this->flex_icon('check-circle-success') . get_string('eventattendancetracking:saved', 'mod_facetoface');
            $state = 'saved';
        } else {
            $html = get_string('eventattendancetracking:open', 'mod_facetoface');
            $state = 'open';
        }
        return \html_writer::link($url, $html, ['class' => "mod_facetoface__sessionlist__attendance--{$state}__link"]);
    }

    /**
     * Creates a table cell containing the registration period, if any, for a session.
     *
     * @param seminar_event $seminarevent
     * @param int $datescount - determines the number for the rowspan.
     * @param boolean|null $displaytimezones true/false or null to use facetoface_displaysessiontimezones config
     * @return html_table_cell
     * @throws coding_exception
     */
    private function session_resgistrationperiod_table_cell(seminar_event $seminarevent, int $datescount = 0, bool $displaytimezones = null): \html_table_cell {
        // Signup Start Dates/times.
        $registrationstring = \mod_facetoface\output\session_time::to_html($seminarevent->get_registrationtimestart(), $seminarevent->get_registrationtimefinish(), 99, $displaytimezones);

        $sessioncell = new html_table_cell($registrationstring);
        if ($datescount > 1) {
            $sessioncell->rowspan = $datescount;
        }

        return $sessioncell;
    }

    /**
     * Creates a table cell for the options available for a session.
     *
     * @param seminar_event $seminarevent
     * @param bool $viewattendees - true if the user has this permission.
     * @param bool $editevents - true if the user has this permission.
     * @return html_table_cell
     * @throws coding_exception
     */
    private function session_options_table_cell(seminar_event $seminarevent, bool $viewattendees, bool $editevents): \html_table_cell {

        $timenow = time();

        // NOTE: This is not a nice hack, we can only guess where to return because there is no argument above.
        $bas = 0;
        if ($this->page->has_set_url() && $this->page->url->compare(new moodle_url('/mod/facetoface/view.php'), URL_MATCH_BASE)) {
            $bas = 1;
        }
        $params = ['s' => $seminarevent->get_id(), 'backtoallsessions' => $bas];

        $builder = actions_in_dashboard::builder();

        $signuplink = $this->get_signup_link($seminarevent);
        if ($signuplink !== '' && $signuplink != self::DEFAULT_SIGNUP_LINK) {
            // Use an alternate sign-up link if it is provided by other components such as the totara_facetoface enrolment plugin.
            $builder->set_view_event_button(get_string('gotosession', 'mod_facetoface'), new \moodle_url($signuplink, $params));
        } else if (signup_helper::is_user_actionable($seminarevent)) {
            $builder->set_view_event_button(get_string('gotosession', 'mod_facetoface'), new \moodle_url('/mod/facetoface/eventinfo.php', $params));
        }

        // Can edit sessions.
        if ($editevents) {
            if (!$seminarevent->get_cancelledstatus()) {
                $builder->add_action_icon(new flex_icon('edit', ['alt' => get_string('editsession', 'mod_facetoface')]), new \moodle_url('/mod/facetoface/events/edit.php', $params));
                if (!$seminarevent->is_first_started($timenow)) {
                    $builder->add_action_icon(new flex_icon('ban', ['alt' => get_string('cancelsession', 'mod_facetoface')]), new \moodle_url('/mod/facetoface/events/cancel.php', $params));
                }
            }
            $builder->add_action_icon(new flex_icon('duplicate', ['alt' => get_string('copysession', 'mod_facetoface')]), new \moodle_url('/mod/facetoface/events/edit.php', $params + ['c' => 1]));
            $builder->add_action_icon(new flex_icon('delete', ['alt' => get_string('deletesession', 'mod_facetoface')]), new \moodle_url('/mod/facetoface/events/delete.php', $params));
        }

        // Can view attendees.
        if ($viewattendees) {
            // NOTE: the s() added in TL-20729 has been removed due to the conversion to mustache template
            $builder->add_action_link(get_string('attendees', 'mod_facetoface'), new \moodle_url('/mod/facetoface/attendees/view.php', $params));
        }

        $options = $this->render($builder->build());
        $datescount = $seminarevent->get_sessions()->count();
        $sessioncell = new html_table_cell($options);
        if ($datescount > 1) {
            $sessioncell->rowspan = $datescount;
        }

        return $sessioncell;
    }

    /**
     * Main calendar hook function for rendering the f2f filter controls
     *
     * @return string html
     */
    public function calendar_filter_controls(): string {
        global $SESSION;

        // Custom fields.
        $fieldsall = \mod_facetoface\calendar::get_customfield_filters();
        $output = '';
        foreach ($fieldsall as $type => $fields) {
            foreach ($fields as $f) {
                $currentval = '';
                if (!empty($SESSION->calendarfacetofacefilter[$type][$f->shortname])) {
                    $currentval = $SESSION->calendarfacetofacefilter[$type][$f->shortname];
                }
                $output .= $this->custom_field_chooser($type, $f, $currentval);
            }
        }
        return $output;
    }

    /**
     * Generates a custom field select for a f2f custom field
     *
     * @param string $type Custom field set ("room", "sess", etc)
     * @param stdClass $field
     * @param string|null $currentvalue
     *
     * @return string html
     */
    public function custom_field_chooser(string $type, \stdClass $field, ?string $currentvalue): string {
        // Same $fieldname  must be used in lib.php facetoface_calendar_set_filter().
        $fieldname = "field_{$type}_{$field->shortname}";
        $stringsource = '';
        switch ($type) {
            case 'sess':
                $stringsource = 'customfieldsession';
                break;
            case 'room':
                $stringsource = 'customfieldroom';
                break;
            default:
                $stringsource = 'customfieldother';
        }

        $value = empty($currentvalue) ? '' : $currentvalue;
        $values = array();
        switch ($field->datatype) {
            case 'multiselect':
                $param1 = json_decode($field->param1, true);
                foreach ($param1 as $option) {
                    $values[] = $option['option'];
                }
                break;
            case 'menu':
                $values = explode("\n", $field->param1);
                break;
            case 'checkbox':
                $values = array(0 => get_string('no'), 1 => get_string('yes'));
                break;
            case 'datetime':
                $label = html_writer::empty_tag('input', array('type' => 'text', 'size' => 10, 'name' => $fieldname, 'value' => $value, 'id' => 'id_' . $fieldname));
                build_datepicker_js('#id_' . $fieldname);
                return html_writer::tag('label', get_string($stringsource, 'facetoface', $field->fullname) . ':', array('for' => 'id_' . $fieldname)) . $label;
                break;
            case 'location':
            case 'textarea':
            case 'text':
                $label = html_writer::empty_tag('input', array('type' => 'text', 'size' => 15, 'name' => $fieldname, 'value' => $value, 'id' => 'id_' . $fieldname));
                return html_writer::tag('label', get_string($stringsource, 'facetoface', $field->fullname) . ':', array('for' => 'id_' . $fieldname)) . $label;
                break;
            default:
                return false;
        }

        // Build up dropdown list of values.
        $options = array();
        if (!empty($values)) {
            foreach ($values as $value) {
                $v = clean_param(trim($value), PARAM_TEXT);
                if (!empty($v)) {
                    $options[s($v)] = format_string($v);
                }
            }
        }

        $nothing = get_string('all');
        $nothingvalue = 'all';

        $currentvalue = empty($currentvalue) ? $nothingvalue : $currentvalue;

        $dropdown = html_writer::select($options, $fieldname, $currentvalue, array($nothingvalue => $nothing));

        return html_writer::tag('label', get_string($stringsource, 'facetoface', $field->fullname) . ':', array('for' => 'id_customfields')) . $dropdown;

    }

    /**
     * @param context|null $context
     * @return void
     */
    public function setcontext(?context $context): void {
        $this->context = $context;
    }

    /**
     * @return context
     */
    public function getcontext(): \context {
        $context = $this->context;
        if (null == $context) {
            // Probably context is not being set here yet, therefore, we should use the one provided by $PAGE
            $context = $this->page->context;
        }
        return $context;
    }

    /**
     * Generate the multiselect inputs + add/remove buttons to control allocating / deallocating users
     * for this session
     *
     * @param stdClass $team containing the lists of users who can be allocated / deallocated
     * @param stdClass $session
     * @param array $reserveinfo details of the number of allocations allowed / left
     * @return string HTML fragment to be output
     */
    public function session_user_selector(\stdClass $team, \stdClass $session, array $reserveinfo): string {
        $output = html_writer::start_tag('div', array('class' => 'row-fluid user-multiselect'));

        // Current allocations.
        $output .= html_writer::start_tag('div', array('class' => 'span5'));
        $info = (object)array(
            'allocated' => $reserveinfo['allocated'][$session->id],
            'max' => $reserveinfo['maxallocate'][$session->id],
        );
        $heading = get_string('currentallocations', 'mod_facetoface', $info);
        $output .= html_writer::tag('label', $heading, array('for' => 'deallocation'));
        $selected = optional_param_array('deallocation', array(), PARAM_INT);

        $opts = '';
        $opts .= html_writer::start_tag('optgroup', array('label' => get_string('thissession', 'mod_facetoface')));
        if (empty($team->current)) {
            $opts .= html_writer::tag('option', get_string('none'), array('value' => null, 'disabled' => 'disabled'));
        } else {
            foreach ($team->current as $user) {
                $name = fullname($user);
                $attr = array('value' => $user->id);
                if (in_array($user->id, $selected)) {
                    $attr['selected'] = 'selected';
                }
                if (!empty($user->cannotremove)) {
                    $attr['disabled'] = 'disabled';
                    $name .= ' (' . get_string($user->cannotremove, 'mod_facetoface') . ')';
                }
                $opts .= html_writer::tag('option', $name, $attr) . "\n";
            }
        }
        $opts .= html_writer::end_tag('optgroup');
        if (!empty($team->othersession)) {
            $opts .= html_writer::start_tag('optgroup', array('label' => get_string('othersession', 'mod_facetoface')));
            foreach ($team->othersession as $user) {
                $name = fullname($user);
                $attr = array('value' => $user->id, 'disabled' => 'disabled');
                if (!empty($user->cannotremove)) {
                    $name .= ' (' . get_string($user->cannotremove, 'mod_facetoface') . ')';
                }
                $opts .= html_writer::tag('option', $name, $attr) . "\n";
            }
        }
        $output .= html_writer::tag('select', $opts, array('name' => 'deallocation[]', 'multiple' => 'multiple',
            'id' => 'deallocation', 'size' => 20));
        $output .= html_writer::end_tag('div');

        // Buttons.
        $output .= html_writer::start_tag('div', array('class' => 'span2 controls'));
        $addlabel = $this->output->larrow() . ' ' . get_string('add');
        $output .= html_writer::empty_tag('input', array('name' => 'add', 'id' => 'add', 'type' => 'submit',
            'value' => $addlabel, 'title' => get_string('add')));
        $removelabel = get_string('remove') . ' ' . $this->output->rarrow();
        $output .= html_writer::empty_tag('input', array('name' => 'remove', 'id' => 'remove', 'type' => 'submit',
            'value' => $removelabel, 'title' => get_string('remove')));
        $output .= html_writer::end_tag('div');

        // Potential allocations.
        $output .= html_writer::start_tag('div', array('class' => 'span5'));
        $output .= html_writer::tag('label', get_string('potentialallocations', 'mod_facetoface',
            $reserveinfo['allocate'][$session->id]),
            array('for' => 'allocation'));

        $selected = optional_param_array('allocation', array(), PARAM_INT);
        $optspotential = array();
        foreach ($team->potential as $potential) {
            $optspotential[$potential->id] = fullname($potential);
        }
        $attr = array('multiple' => 'multiple', 'id' => 'allocation', 'size' => 20);
        if ($reserveinfo['allocate'][$session->id] == 0) {
            $attr['disabled'] = 'disabled';
        }
        $output .= html_writer::select($optspotential, 'allocation[]', $selected, null, $attr);
        $output .= html_writer::end_tag('div');
        $output .= html_writer::end_tag('div');

        return $output;
    }

    /**
     * Render strings as an unordered list HTML i.e. <ul> and <li>.
     * - if the list is empty the output will be '<div class="...emptyclass">emptytext</div>'
     * - if the list is not empty the output will be '<ul class="...outerclass"><li class="...innerclass">...</li>...</ul>'
     *
     * @param string[] $listitems
     * @param string $emptytext
     * @param string $class
     * @param string $outerclass
     * @param string $innerclass
     * @param string $emptyclass
     * @return string
     */
    public static function render_unordered_list(array $listitems, string $emptytext, string $class, string $outerclass, string $innerclass, string $emptyclass = 'empty'): string {
        if (empty($listitems)) {
            return \html_writer::tag('div', $emptytext, ['class' => "{$class}__{$outerclass}--{$emptyclass}"]);
        }
        $listhtml = '';
        foreach ($listitems as $listitem) {
            $listhtml .= \html_writer::tag('li', $listitem, ['class' => "{$class}__{$innerclass}"]);
        }
        return \html_writer::tag('ul', $listhtml, ['class' => "{$class}__{$outerclass}"]);
    }

    /**
     * Format the dates for the given session, when listing the other bookings made by a given manager
     * in a particular face to face instance.
     *
     * @param seminar_event $seminarevent
     * @param boolean|null $displaytimezones
     * @return string
     */
    protected function format_booking_dates(seminar_event $seminarevent, bool $displaytimezones = null): string {
        $formatteddates = [];
        /** @var seminar_session $date */
        $sessionlist = clone $seminarevent->get_sessions();
        foreach ($sessionlist->sort('timestart') as $date) {
            $formatteddates[] = session_time::to_html($date->get_timestart(), $date->get_timefinish(), $date->get_sessiontimezone(), $displaytimezones);
        }
        return static::render_unordered_list(
            $formatteddates,
            get_string('wait-listed', 'mod_facetoface'),
            'mod_facetoface__booking',
            'dates',
            'date',
            'waitlisted'
        );
    }

    /**
     * Output the given list of reservations/allocations that this manager has made
     * in other sessions in this facetoface.
     *
     * @param stdClass[] $bookings
     * @param stdClass $manager
     * @return string HTML fragment to output the list
     */
    public function other_reservations(array $bookings, \stdClass $manager): string {
        global $USER;

        if (!$bookings) {
            return '';
        }

        // Gather the session data together.
        $sessions = array();
        foreach ($bookings as $booking) {
            if (!isset($sessions[$booking->sessionid])) {
                $seminarevent = new \mod_facetoface\seminar_event($booking->sessionid);
                $sessions[$booking->sessionid] = (object)array(
                    'reservations' => 0,
                    'bookings' => array(),
                    'dates' => $this->format_booking_dates($seminarevent),
                );
            }
            if ($booking->userid) {
                $sessions[$booking->sessionid]->bookings[$booking->userid] = fullname($booking);
            } else {
                $sessions[$booking->sessionid]->reservations++;
            }
        }

        // Output the details as a table.
        if ($manager->id == $USER->id) {
            $bookingstr = get_string('yourbookings', 'facetoface');
        } else {
            $bookingstr = get_string('managerbookings', 'facetoface', fullname($manager));
        }
        $table = new html_table();
        $table->head = array(
            get_string('sessiondatetime', 'facetoface'),
            $bookingstr,
        );
        $table->attributes = array('class' => 'generaltable managerbookings');

        foreach ($sessions as $session) {
            $details = array();
            if ($session->reservations) {
                $details[] = get_string('reservations', 'mod_facetoface', $session->reservations);
            }
            $details += $session->bookings;
            $detailshtml = static::render_unordered_list(
                $details,
                '',
                'mod_facetoface__booking',
                'users',
                'user',
                ''
            );
            $row = new html_table_row(array($session->dates, $detailshtml));
            $table->data[] = $row;
        }

        $heading = $this->output->heading(get_string('existingbookings', 'mod_facetoface'), 3);

        return $heading . html_writer::table($table);
    }

    /**
     * Manage customfield tabs displayed in customfield/index.php
     *
     * @param string $currenttab
     * @return string tabs
     */
    public function customfield_management_tabs(string $currenttab = 'facetofacesession'): string {
        $tabs = array();
        $row = array();
        $activated = array();
        $inactive = array();

        $row[] = new tabobject('facetofacesession', new moodle_url('/mod/facetoface/customfields.php', array('prefix' => 'facetofacesession')),
            get_string('sessioncustomfieldtab', 'facetoface'));
        $row[] = new tabobject('facetofaceasset', new moodle_url('/mod/facetoface/customfields.php', array('prefix' => 'facetofaceasset')),
            get_string('assetcustomfieldtab', 'facetoface'));
        $row[] = new tabobject('facetofacefacilitator', new moodle_url('/mod/facetoface/customfields.php', array('prefix' => 'facetofacefacilitator')),
            get_string('facilitatorcustomfieldtab', 'mod_facetoface'));
        $row[] = new tabobject('facetofaceroom', new moodle_url('/mod/facetoface/customfields.php', array('prefix' => 'facetofaceroom')),
            get_string('roomcustomfieldtab', 'facetoface'));
        $row[] = new tabobject('facetofacesignup', new moodle_url('/mod/facetoface/customfields.php', array('prefix' => 'facetofacesignup')),
            get_string('signupcustomfieldtab', 'facetoface'));
        $row[] = new tabobject('facetofacecancellation', new moodle_url('/mod/facetoface/customfields.php', array('prefix' => 'facetofacecancellation')),
            get_string('cancellationcustomfieldtab', 'facetoface'));
        $row[] = new tabobject('facetofacesessioncancel', new moodle_url('/mod/facetoface/customfields.php', array('prefix' => 'facetofacesessioncancel')),
            get_string('sessioncancellationcustomfieldtab', 'facetoface'));

        $tabs[] = $row;
        $activated[] = $currenttab;

        return print_tabs($tabs, $currenttab, $inactive, $activated, true);
    }

    /**
     * Manage report tabs
     *
     * @param string $currenttab
     * @return string tabs
     */
    public function reports_management_tabs(string $currenttab = 'facetofaceeventreport'): string {

        $tabs = array();
        $row = array();
        $activated = array();
        $inactive = array();

        $row[] = new tabobject('facetofaceeventreport', new moodle_url('/mod/facetoface/reports/events.php'),
            get_string('eventsview', 'mod_facetoface'));

        $row[] = new tabobject('facetofacesessionreport', new moodle_url('/mod/facetoface/reports/sessions.php'),
            get_string('sessionsview', 'mod_facetoface'));

        $tabs[] = $row;
        $activated[] = $currenttab;

        return print_tabs($tabs, $currenttab, $inactive, $activated, true);
    }

    /**
     * Show a list of all reservations for a session and allow them to be removed.
     *
     * @param stdClass[] $reservations Data about all the reservations
     * @param boolean $backtoallsessions
     * @param boolean $backtoeventinfo
     * @return string
     */
    public function print_reservation_management_table(array $reservations, bool $backtoallsessions = true, bool $backtoeventinfo = false): string {

        $out = '';

        if (count($reservations) > 0) {
            $table = new html_table();
            $table->head = array(
                get_string('managername', 'mod_facetoface'),
                get_string('spacesreserved', 'mod_facetoface'),
                get_string('actions'));

            $table->attributes = array('class' => 'generaltable managereservations fullwidth');

            $strdelete = get_string('delete');

            foreach ($reservations as $reservation) {
                $managername = fullname($reservation);

                $managerlink = html_writer::link(new moodle_url('/user/profile.php',
                    array('id' => $reservation->bookedby)), $managername);

                $deleteurl = new moodle_url('/mod/facetoface/reservations/delete.php', ['s' => $reservation->sessionid,
                    'backtoallsessions' => (int)$backtoallsessions,
                    'backtoeventinfo' => (int)$backtoeventinfo,
                    'managerid' => $reservation->bookedby, 'sesskey' => sesskey()]);
                $buttons = $this->action_icon($deleteurl, new pix_icon('t/delete', $strdelete));

                $row = new html_table_row(array($managerlink, $reservation->reservedspaces, $buttons));
                $table->data[] = $row;
            }

            $out .= html_writer::table($table);

        } else {
            $out .= html_writer::tag('p', get_string('noreservationsforsession', 'mod_facetoface'));
        }

        return $out;
    }

    /**
     * Render table of users used in add attendees list
     * @param array $users
     * @param \mod_facetoface\bulk_list $list User list Needed only with job assignments
     * @param int $sessionid Needed only with job assignments
     * @param int $jaselector Job assignements selection: 0 - no, 1 - optional, 2 - required
     * @return string
     */
    public function print_userlist_table(array $users, \mod_facetoface\bulk_list $list = null, int $sessionid = 0, int $jaselector = 0): string {
        global $OUTPUT, $PAGE;

        $out = '';
        $showcfdatawarning = false;
        if (count($users) > 0) {

            $showemail = in_array('email', get_extra_user_fields($PAGE->context));
            $showidnumber = in_array('idnumber', get_extra_user_fields($PAGE->context));

            $table = new html_table();
            $table->head = [get_string('name')];
            if ($showemail) {
                $table->head[] = get_string('email');
            }
            if ($showidnumber) {
                $table->head[] = get_string('idnumber');
            }
            if ($jaselector) {
                $jacolumnheader = get_string('jobassignment', 'facetoface');
                if ($jaselector == 2) {
                    // Taken from lib/formslib.php.
                    $jacolumnheader .= $OUTPUT->flex_icon('required', array(
                        'classes' => 'form-required',
                        'alt' => get_string('requiredelement', 'form'),
                        'title' => get_string('requiredelement', 'form')
                    ));
                }
                $table->head[] = $jacolumnheader;
            }

            if (isset(current($users)->cntcfdata)) {
                $table->head[] = get_string('signupdata', 'facetoface');
            }

            $table->attributes = array('class' => 'generaltable userstoadd fullwidth');

            foreach ($users as $user) {
                $row = [fullname($user)];
                if ($showemail) {
                    $row[] = clean_string($user->email);
                }
                if ($showidnumber) {
                    $row[] = s($user->idnumber);
                }
                if ($jaselector) {
                    $janame = '';
                    // Get previously stored jobassignmentid from user list. @see attendess/select_job_assignment.php.
                    $userdata = $list->get_user_data($user->id);
                    if (!empty($userdata['jobassignmentid'])) {
                        $jobassignment = \totara_job\job_assignment::get_with_id($userdata['jobassignmentid']);
                        $janame = $jobassignment->fullname;
                    }

                    $url = new moodle_url('/mod/facetoface/attendees/ajax/select_job_assignment.php',
                            array('id' => $user->id, 's' => $sessionid, 'listid' => $list->get_list_id()));

                    $icon = $OUTPUT->action_icon($url, new pix_icon('t/edit', get_string('edit')), null,
                        array('class' => 'action-icon attendee-edit-job-assignment pull-right'));
                    $jobassign = html_writer::span($janame, 'jobassign' . $user->id, array('id' => 'jobassign' . $user->id));
                    $row[] = $icon . $jobassign;
                }

                if (isset($user->cntcfdata)) {
                    if ($user->cntcfdata) {
                        $showcfdatawarning = true;
                        $row[] = html_writer::tag('strong', get_string('yes'));
                    } else {
                        $row[] = get_string('no');
                    }
                }
                $row = new html_table_row($row);
                $table->data[] = $row;
            }

            if ($showcfdatawarning) {
                $out .= $OUTPUT->notification(get_string('removecfdatawarning', 'facetoface'), 'notifymessage');
            }
            $out .= $OUTPUT->render($table);
        }
        return $out;
    }

    /**
     * Displays the dismiss action icon the mismatched approval types notice.
     *
     * @param int $f2fid The id of the facetoface currently being viewed
     * @return void
     */
    public function selfapproval_notice(int $f2fid): void {
        global $OUTPUT;

        $approvalcount = \mod_facetoface\approver::count_selfapproval($f2fid);
        if ($approvalcount > 1) {
            $attributes = array('class' => 'smallicon dismissicon');
            $dismissstr = get_string('dismiss', 'mod_facetoface');
            $dismissurl = new \moodle_url('/mod/facetoface/approver/dismiss.php', array('fid' => $f2fid, 'sesskey' => sesskey()));
            $icon = $this->output->action_icon($dismissurl, new pix_icon('/t/delete', $dismissstr), null, $attributes);
            $message = get_string('warning:mixedapprovaltypes', 'mod_facetoface') . $icon;
            echo $OUTPUT->notification($message, 'notifynotice');
        }
    }

    /**
     * Render details of the room
     * @param room $room
     * @return string
     */
    public function render_room_details(room $room): string {
        global $DB;

        $output = array();

        $output[] = html_writer::start_tag('dl', array('class' => 'f2f roomdetails dl-horizontal'));

        // Room name.
        $output[] = html_writer::tag('dt', get_string('roomname', 'facetoface'));
        $output[] = html_writer::tag('dd', $room->get_name());

        $options = array('prefix' => 'facetofaceroom', 'extended' => true);
        // Converts to item required by Custom Fields.
        $cf_item = (object)['id' => $room->get_id()];
        $fields = customfield_get_data($cf_item, 'facetoface_room', 'facetofaceroom', true, $options);
        if (!empty($fields)) {

            foreach ($fields as $field => $value) {

                $output[] = html_writer::tag('dt', $field);
                $output[] = html_writer::tag('dd', $value);
            }
        }

        // Capacity.
        $output[] = html_writer::tag('dt', get_string('capacity', 'facetoface'));
        $output[] = html_writer::tag('dd', $room->get_capacity());

        // Allow scheduling conflicts.
        $output[] = html_writer::tag('dt', get_string('allowroomconflicts', 'facetoface'));
        $output[] = html_writer::tag('dd', $room->get_allowconflicts() ? get_string('yes') : get_string('no'));

        // Description.
        if (!empty($room->get_description())) {
            $output[] = html_writer::tag('dt', get_string('roomdescription', 'facetoface'));
            $descriptionhtml = file_rewrite_pluginfile_urls(
                $room->get_description(),
                'pluginfile.php',
                \context_system::instance()->id,
                'mod_facetoface',
                'room',
                $room->get_id()
            );
            $descriptionhtml = format_text($descriptionhtml, FORMAT_HTML);
            $output[] = html_writer::tag('dd', $descriptionhtml);
        }

        // Created.
        $created = new stdClass();
        $created->user = get_string('unknownuser');
        if (!empty($room->get_usercreated())) {
            $created->user = html_writer::link(
                new moodle_url('/user/view.php', array('id' => $room->get_usercreated())),
                fullname($DB->get_record('user', array('id' => $room->get_usercreated())))
            );
        }
        $created->time = userdate($room->get_timecreated());
        $output[] = html_writer::tag('dt', get_string('created', 'mod_facetoface'));
        $output[] = html_writer::tag('dd', get_string('timestampbyuser', 'mod_facetoface', $created));

        // Modified.
        if (!empty($room->get_timemodified())) {
            $modified = new stdClass();
            $modified->user = get_string('unknownuser');
            if (!empty($room->get_usermodified())) {
                $modified->user = html_writer::link(
                    new moodle_url('/user/view.php', array('id' => $room->get_usermodified())),
                    fullname($DB->get_record('user', array('id' => $room->get_usermodified())))
                );
            }
            $modified->time = userdate($room->get_timemodified());

            $output[] = html_writer::tag('dt', get_string('modified'));
            $output[] = html_writer::tag('dd', get_string('timestampbyuser', 'mod_facetoface', $modified));
        }

        $output[] = html_writer::end_tag('dl');

        $output = implode('', $output);

        return $output;
    }

    /**
     * Gets the HTML output for room details.
     *
     * @param \mod_facetoface\room $room - The room instance to get details for
     * @param string|null $backurl
     * @return string containing room details with relevant html tags.
     */
    public function get_room_details_html(\mod_facetoface\room $room, string $backurl = null): string {
        global $CFG;
        $url = new moodle_url('/mod/facetoface/reports/rooms.php', array(
            'roomid' => $room->get_id(),
        ));

        $roomhtml = '';
        // Display room custom fields?
        if (!empty($CFG->facetoface_roomidentifier)) {
            $customfields = $room->get_customfield_array();
            $cfs = [];
            // Always display building if present.
            if (!empty($customfields[CUSTOMFIELD_BUILDING])) {
                $cfs[] = s($customfields[CUSTOMFIELD_BUILDING]);
            }
            // Also display location if configured to, and present.
            if ($CFG->facetoface_roomidentifier == \mod_facetoface\room::ROOM_IDENTIFIER_LOCATION && !empty($customfields[CUSTOMFIELD_LOCATION])) {
                $cfs[] = s(str_replace('<br />', ', ', $customfields[CUSTOMFIELD_LOCATION]));
            }
            if (!empty($cfs)) {
                $cf_glue = '<br>';
                $cf_class = 'mod_facetoface__sessionlist__roomdetails';
                $roomhtml .= html_writer::tag('span', implode($cf_glue, $cfs), ['class' => $cf_class]);
            }
        }

        return $this->render_details_popup_html($room->get_name(), 'room', $url, $backurl, $roomhtml);
    }

    /**
     * Gets the HTML output for facilitator details.
     * @param \mod_facetoface\facilitator_user $facilitator - The facilitator instance to get details for
     * @param string|null $backurl
     * @return string containing facilitator details with relevant html tags.
     */
    public function get_facilitator_details_html(\mod_facetoface\facilitator_user $facilitator, string $backurl = null): string {
        $url = new moodle_url('/mod/facetoface/reports/facilitators.php', [
            'facilitatorid' => $facilitator->get_id(),
        ]);
        return $this->render_details_popup_html($facilitator->get_name(), 'facilitator', $url, $backurl, $facilitator->get_fullname_link());
    }

    /**
     * Gets the HTML output for asset details.
     *
     * @param \mod_facetoface\asset $asset - The asset instance to get details for
     * @param string|null $backurl
     * @return string containing asset details with relevant html tags.
     */
    public function get_asset_details_html(\mod_facetoface\asset $asset, string $backurl = null): string {
        $url = new moodle_url('/mod/facetoface/reports/assets.php', array(
            'assetid' => $asset->get_id(),
        ));
        return $this->render_details_popup_html($asset->get_name(), 'asset', $url, $backurl);
    }

    /**
     * Gets the HTML output of a details link.
     *
     * @param string $name - The name of the asset without escaping.
     * @param string $class - The CSS class of the link.
     * @param moodle_url $popupurl
     * @param string|null $backurl
     * @param string $extrahtml HTML appended to the link
     * @return string containing asset details with relevant html tags.
     */
    public function render_details_popup_html(string $name, string $class, moodle_url $popupurl, string $backurl = null, string $extrahtml = ''): string {
        $popupurl = clone($popupurl);
        $popupurl->param('popup', 1);
        if (!empty($backurl)) {
            $popupurl->param('b', $backurl);
        }
        $action = new popup_action('click', $popupurl, 'popup', array('width' => 800, 'height' => 600));
        $link = $this->output->action_link($popupurl, s($name), $action);
        return html_writer::span($link . $extrahtml, 'mod_facetoface__' . $class . '__detail');
    }

    /**
     * Render asset meta data
     *
     * @param \mod_facetoface\asset $asset
     * @return string
     */
    public function render_asset_details(\mod_facetoface\asset $asset): string {
        global $DB;

        $output = [];

        $output[] = html_writer::start_tag('dl', array('class' => 'f2f roomdetails'));

        // Asset name.
        $output[] = html_writer::tag('dt', get_string('assetname', 'facetoface'));
        $output[] = html_writer::tag('dd', $asset->get_name());

        $options = array('prefix' => 'facetofaceasset', 'extended' => true);
        $cfdata = (object)[
            'id' => $asset->get_id(),
            'fullname' => $asset->get_name(),
            'custom' => $asset->get_custom(),
        ];
        $fields = customfield_get_data($cfdata, 'facetoface_asset', 'facetofaceasset', true, $options);
        if (!empty($fields)) {
            foreach ($fields as $field => $value) {

                $output[] = html_writer::tag('dt', $field);
                $output[] = html_writer::tag('dd', $value);
            }
        }

        // Allow scheduling conflicts.
        $output[] = html_writer::tag('dt', get_string('allowassetconflicts', 'facetoface'));
        $output[] = html_writer::tag('dd', $asset->get_allowconflicts() ? get_string('yes') : get_string('no'));

        // Description.
        if (!empty($asset->get_description())) {
            $output[] = html_writer::tag('dt', get_string('assetdescription', 'facetoface'));
            $descriptionhtml = file_rewrite_pluginfile_urls(
                $asset->get_description(),
                'pluginfile.php',
                \context_system::instance()->id,
                'mod_facetoface',
                'asset',
                $asset->get_id()
            );
            $descriptionhtml = format_text($descriptionhtml, FORMAT_HTML);
            $output[] = html_writer::tag('dd', $descriptionhtml);
        }

        // Created.
        $created = new stdClass();
        $created->user = get_string('unknownuser');
        $usercreated = $asset->get_usercreated();
        if (!empty($usercreated)) {
            $created->user = html_writer::link(
                new moodle_url('/user/view.php', array('id' => $usercreated)),
                fullname($DB->get_record('user', array('id' => $usercreated)))
            );
        }
        $created->time = userdate($asset->get_timecreated());
        $output[] = html_writer::tag('dt', get_string('created', 'mod_facetoface'));
        $output[] = html_writer::tag('dd', get_string('timestampbyuser', 'mod_facetoface', $created));

        // Modified.
        $timemodified = $asset->get_timemodified();
        if (!empty($timemodified)) {
            $modified = new stdClass();
            $modified->user = get_string('unknownuser');
            $usermodified = $asset->get_usermodified();
            if (!empty($usermodified)) {
                $modified->user = html_writer::link(
                    new moodle_url('/user/view.php', array('id' => $usermodified)),
                    fullname($DB->get_record('user', array('id' => $usermodified)))
                );
            }
            $modified->time = userdate($timemodified);

            $output[] = html_writer::tag('dt', get_string('modified'));
            $output[] = html_writer::tag('dd', get_string('timestampbyuser', 'mod_facetoface', $modified));
        }

        $output[] = html_writer::end_tag('dl');

        $output = implode('', $output);

        return $output;
    }

    /**
     * Gets the HTML output for facilitator details.
     * @param \mod_facetoface\facilitator $facilitator - The facilitator instance to get details for
     * @param string|null $backurl
     * @return string containing facilitator details with relevant html tags.
     */
    public function get_facilitator_details(\mod_facetoface\facilitator_user $facilitator, string $backurl = null): string {
        $url = new moodle_url('/mod/facetoface/reports/facilitators.php', array(
            'facilitatorid' => $facilitator->get_id(),
            'b' => $backurl
        ));

        $popupurl = clone($url);
        $popupurl->param('popup', 1);
        $action = new popup_action('click', $popupurl, 'popup', array('width' => 800, 'height' => 600));
        $link = $this->output->action_link($url, s($facilitator->get_name()), $action, array('class' => 'mod_facetoface__sessionlist__facilitator__link'));
        return html_writer::span($link . $facilitator->get_fullname_link(), 'mod_facetoface__sessionlist__facilitatordetails');
    }

    /**
     * Output for a removable approver in the facetoface mod_form.
     *
     * @param stdClass  $user           The user object for the approver being displayed
     * @param boolean   $activity       Whether the approver is activity level or site level
     * @return string                   The html output for the approver
     */
    public function display_approver(\stdClass $user, bool $activity = false) : string {

        $uniqueid = "facetoface_approver_{$user->id}";
        if ($activity) {
            $classname = 'activity_approver';
            $delete = $this->action_icon('', new pix_icon('/t/delete', get_string('remove')), null,
                array('class' => 'activity_approver_del', 'id' => $user->id));
            $content = get_string('approval_activityapprover', 'mod_facetoface', fullname($user)) . ' ' . $delete;
        } else {
            $classname = 'system_approver';
            $content = get_string('approval_siteapprover', 'mod_facetoface', fullname($user));
        }

        return html_writer::tag('div', $content, array('id' => $uniqueid, 'class' => $classname));
    }

    /**

     * Declare or withdraw interest html output button.
     *
     * @param \mod_facetoface\seminar $seminar
     */
    public function declare_interest(\mod_facetoface\seminar $seminar) : void {
        global $OUTPUT;

        $interest = \mod_facetoface\interest::from_seminar($seminar);
        if ($interest->is_user_declared() || $interest->can_user_declare()) {
            if ($interest->is_user_declared()) {
                $strbutton = get_string('declareinterestwithdraw', 'mod_facetoface');
            } else {
                $strbutton = get_string('declareinterest', 'mod_facetoface');
            }
            $url = new moodle_url('/mod/facetoface/interest.php', array('f' => $seminar->get_id()));
            echo $OUTPUT->single_button($url, $strbutton, 'get');
        }
    }

    /**
     * Action bar html output for seminar tab pages.
     *
     * @param string|\moodle_url $goback_url the URL for the 'View all events' button (a.k.a 'Go back' button)
     * @return string
     */
    public function render_action_bar_on_tabpage($goback_url): string {
        $actionbar = \mod_facetoface\output\seminarevent_actionbar::builder()
            ->set_align('near')
            ->set_class('attendance')
            ->add_commandlink(
                'goback',
                $goback_url,
                get_string('viewallsessions', 'mod_facetoface')
            );

        return $this->render($actionbar->build());
    }

    /**
     * Action bar html output for reservation pages.
     *
     * @param \moodle_url|null $goback_url the URL for the 'Go back' button
     * @param \moodle_url|null $viewall_url the URL for the 'View all events' button
     * @return string
     */
    public function render_action_bar_on_reservation_page(\moodle_url $goback_url = null, \moodle_url $viewall_url = null): string {
        $actionbar = seminarevent_actionbar::builder()
            ->set_align('near')
            ->set_class('reservation');

        if ($goback_url !== null) {
            $actionbar->add_commandlink('goback', $goback_url, get_string('goback', 'mod_facetoface'));
        }
        if ($viewall_url) {
            $actionbar->add_commandlink('viewallsessions', $viewall_url, get_string('viewallsessions', 'mod_facetoface'));
        }

        return $this->render($actionbar->build());
    }

    /**
     * Action bar html output.
     *
     * @param \mod_facetoface\seminar $seminar
     * @deprecated Totara 13.0
     * @return void
     */
    public function print_action_bar(\mod_facetoface\seminar $seminar) : void {
        debugging('The method ' . __METHOD__ . '() has been deprecated. Please use mod_facetoface_renderer::render_action_bar() instead.', DEBUG_DEVELOPER);
        echo $this->render_action_bar($seminar);
    }

    /**
     * Action bar html output.
     *
     * @param \mod_facetoface\seminar $seminar
     * @return string
     */
    public function render_action_bar(\mod_facetoface\seminar $seminar) : string {
        $id = html_writer::random_id('id-');

        $editevents = has_capability('mod/facetoface:editevents', $this->getcontext());

        if ($editevents) {
            $actionbar = \mod_facetoface\output\seminarevent_actionbar::builder($id)
                ->set_align('far')
                ->set_class('dashboard')
                ->add_commandlink(
                    'addevent',
                    new moodle_url(
                        'events/add.php',
                        array('f' => $seminar->get_id(), 'backtoallsessions' => 1)
                    ),
                    get_string('addsession', 'mod_facetoface')
                );

            return $this->render($actionbar->build());
        }

        return '';
    }

    /**
     * Edit event button action html output.
     * @param \mod_facetoface\seminar_event $seminarevent
     * @return string
     */
    public function render_editevent_button(seminar_event $seminarevent): string {

        $editevents = has_capability('mod/facetoface:editevents', $this->getcontext());

        if (!$editevents || !$seminarevent->exists()) {
            return '';
        }

        $url = new moodle_url('/mod/facetoface/events/edit.php', ['s' => $seminarevent->get_id(), 'backtoevent' => 1]);
        $attrs = ['role' => 'button', 'class' => 'btn btn-default'];
        $link = html_writer::link($url, get_string('editsession', 'mod_facetoface'), $attrs);
        $output = html_writer::div(
            $link,
            'mod_facetoface__action mod_facetoface__action-editevent mod_facetoface__action--far'
        );
        return $output;
    }

    /**
     * Filter bar html output.
     *
     * @param \mod_facetoface\seminar $seminar
     * @param integer $roomid
     * @param integer $eventtime one of \mod_facetoface\event_time::xxx
     * @deprecated Totara 13
     * @return void
     */
    public function print_filter_bar(\mod_facetoface\seminar $seminar, int &$roomid, int &$eventtime) : void {
        debugging('The method ' . __METHOD__ . '() has been deprecated. Please use mod_facetoface_renderer::render_filter_bar() instead.', DEBUG_DEVELOPER);

        $id = html_writer::random_id('id-');

        $roomselect = $this->get_filter_by_room($seminar, $roomid);
        $eventtimeselect = $this->get_filter_by_event_time($seminar, $eventtime);

        $filterbar = \mod_facetoface\output\seminarevent_filterbar::builder($id)
            ->add_param(self::PARAM_FILTER_F2FID, $seminar->get_id())
            ->add_param(self::PARAM_FILTER_ROOMID, $roomid)
            ->add_param(self::PARAM_FILTER_EVENTTIME, $eventtime);

        $roomdisabled = count($roomselect) <= 2;
        $filterbar
            ->add_filter(self::PARAM_FILTER_ROOMID, $roomselect, 'room', get_string('filterbyroom', 'facetoface'), $roomdisabled)
            ->add_filter(self::PARAM_FILTER_EVENTTIME, $eventtimeselect, 'eventtime', get_string('filterbyeventtime', 'facetoface'));

        echo $this->render($filterbar->build());
    }

    /**
     * Filter bar html output.
     *
     * @param seminar $seminar
     * @param filter_list $filters
     * @param integer|stdClass|null $user
     * @return string
     */
    public function render_filter_bar(\mod_facetoface\seminar $seminar, filter_list $filters, $user = null) : string {
        $id = html_writer::random_id('id-');
        $filterbar = $filters->to_filterbar_builder($seminar, $id, $this->getcontext(), $user)
            ->set_icon(new \core\output\flex_icon('filter'));

        // Show "Reset" link if any filters are set
        if (!$filters->are_default()) {
            $filterbar->add_link('Reset', '?f=' . $seminar->get_id());
        }

        return $this->render($filterbar->build());
    }

    /**
     * Get an array for room filter.
     *
     * @param \mod_facetoface\seminar $seminar
     * @param integer $roomid
     * @return array consisting of [ id => name ]
     * @deprecated Totara 13
     */
    protected function get_filter_by_room(seminar $seminar, int &$roomid) : array {
        debugging('The method ' . __METHOD__ . '() has been deprecated. Please use mod_facetoface\filter_list instead.', DEBUG_DEVELOPER);

        $rooms = (new room_filter())->get_options($seminar);
        if (!array_key_exists($roomid, $rooms)) {
            $roomid = 0;
        }
        return $rooms;
    }

    /**
     * Get an array for the event time filter.
     *
     * @param \mod_facetoface\seminar $seminar
     * @param integer $eventtime one of \mod_facetoface\event_time::xxx
     * @return array consisting of [ event_time::xxx => labeltext ]
     * @deprecated Totara 13
     */
    protected function get_filter_by_event_time(seminar $seminar, int &$eventtime) : array {
        debugging('The method ' . __METHOD__ . '() has been deprecated. Please use mod_facetoface\filter_list instead.', DEBUG_DEVELOPER);

        $times = (new event_time_filter())->get_options($seminar);
        if (!array_key_exists($eventtime, $times)) {
            $eventtime = array_key_first($times);
        }
        return $times;
    }

    /**
     * Filter by room html output select input.
     *
     * @param \mod_facetoface\seminar $seminar
     * @param int $roomid
     * @return int
     * @deprecated since Totara 13
     */
    public function filter_by_room(\mod_facetoface\seminar $seminar, int $roomid): int {
        global $OUTPUT, $PAGE;

        debugging('The method ' . __METHOD__ . '() has been deprecated. Please use mod_facetoface_renderer::render_filter_bar() instead.', DEBUG_DEVELOPER);

        $rooms = \mod_facetoface\room_list::get_seminar_rooms($seminar->get_id());
        if ($rooms->count() > 1) {
            $roomselect = array(0 => get_string('allrooms', 'facetoface'));
            // Here used to be some fancy code that deal with missing room names,
            // that magic cannot be done easily any more, allow selection of named rooms only here.
            foreach ($rooms as $room) {
                $roomname = format_string($room->get_name());
                if ($roomname === '') {
                    continue;
                }
                $roomselect[$room->get_id()] = $roomname;
            }

            if (!isset($roomselect[$roomid])) {
                $roomid = 0;
            }

            if (count($roomselect) > 2) {
                echo $OUTPUT->single_select($PAGE->url, 'roomid', $roomselect, $roomid, null, null, array('label' => get_string('filterbyroom', 'facetoface')));
            }
        } else {
            $roomid = 0;
        }

        return $roomid;
    }

    /**
     * Attendees export html form.
     *
     * @param \mod_facetoface\seminar $seminar
     */
    public function attendees_export_form(\mod_facetoface\seminar $seminar): void {
        global $OUTPUT;

        if (has_capability('mod/facetoface:viewattendees', $this->context)) {
            echo \html_writer::start_tag('form', array('action' => 'export.php', 'method' => 'post'));
            echo \html_writer::start_tag('div') . \html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'f', 'value' => $seminar->get_id()));
            echo $OUTPUT->help_icon('exportattendance', 'mod_facetoface', true) . '&nbsp;';

            $formats = [
                '0' => get_string('format', 'mod_facetoface'),
                'excel' => get_string('excelformat', 'facetoface'),
                'ods' => get_string('odsformat', 'facetoface')
            ];
            echo \html_writer::select($formats, 'download', '0', '', ['aria-label' => get_string('exportformat', 'totara_core')]);

            echo \html_writer::empty_tag('input', array('type' => 'submit', 'value' => get_string('exporttofile', 'facetoface')));
            echo \html_writer::end_tag('div') . \html_writer::end_tag('form');
        }
    }

    /**
     * Print the details of a session
     *
     * @param \mod_facetoface\seminar_event $seminarevent  Record from facetoface_sessions
     * @param boolean $showcapacity   Show the capacity (true) or only the seats available (false)
     * @param boolean $calendaroutput Whether the output should be formatted for a calendar event
     * @param boolean $hidesignup     Hide any messages relating to signing up
     * @param string  $class          Custom css class for dl
     * @return string html markup
     */
    public function render_seminar_event(
        \mod_facetoface\seminar_event $seminarevent,
        bool $showcapacity,
        bool $calendaroutput = false,
        bool $hidesignup = false,
        string $class = 'mod_facetoface__event_details'
    ): string {
        global $USER;

        if ($class != 'mod_facetoface__event_details') {
            debugging('The $class argument has been deprecated and always \'mod_facetoface__event_details\'. Please do not use it anymore.');
        }

        $option = (new render_event_info_option())
            ->set_displaycapacity($showcapacity)
            ->set_calendaroutput($calendaroutput)
            ->set_displaysignupinfo(!$hidesignup)
            ->set_displayassets(false)
            ->set_displayrooms(false)
            ->set_displayassetsinsessions(true);

        $signup = signup::create($USER->id, $seminarevent);

        $data = ['nosidebar' => true, 'details' => $this->get_seminar_event_details_data($signup, $option)];
        return $this->render(new seminarevent_information($data));
    }

    /**
     * Render seminar event information.
     *
     * @param signup $signup
     * @param render_event_info_option $option
     * @param boolean $cancelsignup Set true to activate the "cancel sign-up" sidebar
     * @return string
     */
    public function render_seminar_event_information(signup $signup, render_event_info_option $option, bool $cancelsignup = false): string {
        $seminarevent = $signup->get_seminar_event();
        $seminar = $seminarevent->get_seminar();
        $cm = $seminar->get_coursemodule();

        $data = ['class' => 'eventinfo'];

        $data['navigation'] = event_page_navigation::create(
            $seminarevent,
            get_string('eventinfo:movetotop', 'mod_facetoface'), [
                [
                    'icon' => new flex_icon('arrow-left'),
                    'label' => get_string('eventinfo:allevents', 'mod_facetoface'),
                    'link' => $option->get_backurl()
                ],
                [
                    'label' => get_string('eventinfo:eventinfo', 'mod_facetoface'),
                    'link' => '#f2f-eventinfo',
                ],
                [
                    'label' => get_string('eventinfo:sessions', 'mod_facetoface'),
                    'link' => '#f2f-sessions',
                ],
            ]
        )->get_template_data();

        $currentstate = $signup->get_state();
        if ($seminarevent->get_cancelledstatus() || $seminarevent->is_over()) {
            // No sidebars for past or cancelled events.
            $data['sidebars'] = [];
            $data['nosidebar'] = true;
        } else if (signup_helper::is_booked($signup) || $currentstate->can_switch(user_cancelled::class)) {
            $data['sidebars'] = $this->get_sidebars_cancellation($signup, $option, $cancelsignup);
        } else if (signup_helper::can_signup($signup)) {
            $data['sidebars'] = [ $this->get_sidebar_signup($signup, $option) ];
        } else if ($currentstate instanceof not_set || $currentstate instanceof user_cancelled || $currentstate instanceof declined) {
            $data['sidebars'] = [ $this->get_sidebar_signup_failure($signup) ];
        } else {
            $data['sidebars'] = [];
            $data['nosidebar'] = true;
        }

        $manageractions = $this->manager_action_links($seminarevent, $option);
        if (!empty($manageractions)) {
            $data['management'] = ['actions' => $manageractions];
        }

        $data['heading'] = $option->get_heading();
        if ($seminar->get_intro() !== '') {
            $intro = \format_module_intro('facetoface', $seminar->get_properties(), $cm->id);
            $data['intro'] = $intro;
        }

        $data['details'] = $this->get_seminar_event_details_data($signup, $option);

        $builder = seminarevent_actionbar::builder()
            ->set_align('near')
            ->set_class('eventinfofooter')
            ->add_commandlink('goback', $option->get_backurl(), get_string('viewallsessions', 'mod_facetoface'));
        $data['footeraction'] = $builder->build()->get_template_data();

        return $this->render(new seminarevent_information($data));
    }

    /**
     * Get the list of manager actions.
     *
     * @param seminar_event $seminarevent
     * @param render_event_info_option $option
     * @return string[]
     */
    protected function manager_action_links(seminar_event $seminarevent, render_event_info_option $option): array {
        $class = 'mod_facetoface__eventinfo__content__management';
        $params = [
            's' => $seminarevent->get_id(),
            'backtoallsessions' => (int)$option->get_backtoallsessions(),
            'backtoeventinfo' => (int)$option->get_backtoeventinfo()
        ];
        $sessiondata = \mod_facetoface\seminar_event_helper::get_sessiondata($seminarevent, null);
        $actionlinks = $this->create_reserve_links($sessiondata, null, null, $params, $class);
        $viewattendees = has_capability('mod/facetoface:viewattendees', $this->getcontext());
        if ($viewattendees) {
            unset($params['backtoeventinfo']); // Attendees page doesn't understand the backtoeventinfo parameter
            $viewurl = new moodle_url('/mod/facetoface/attendees/view.php', $params);
            $seeattendees = get_string('eventinfo:attendees', 'mod_facetoface');
            $actionlinks[] = html_writer::link($viewurl, $seeattendees, ['class' => $class . '__viewattendees']);
        }
        return $actionlinks;
    }

    /**
     * Create an array of reserve links.
     *
     * @param session_data $session - session and sessiondates
     * @param int|null $signupcount - number currently signed up to this session.
     * @param array|null $reserveinfo - if managereserve is set to true for the seminar, use reservations::can_reserve_or_allocate
     * to fill out this array.
     * @param array|null $params - URL query parameters passed to reserve links
     * @param string $class - the base CSS class of reserve links
     * @return array of html links with optional text
     * @throws coding_exception
     */
    protected function create_reserve_links(session_data $session, int $signupcount = null, array $reserveinfo = null, array $params = null, string $class = 'mod_facetoface__reserve'): array {
        $seminarevent = $session->to_seminar_event();
        if ($seminarevent->get_cancelledstatus()) {
            return [];
        }

        $currentime = time();
        if ($seminarevent->is_first_started($currentime) || $seminarevent->is_over($currentime)) {
            return [];
        }

        if ($signupcount === null) {
            $includedeleted = has_capability('totara/core:seedeletedusers', $this->getcontext());
            $signupcount = attendees_helper::count_signups($seminarevent, $includedeleted);
        }
        if ($reserveinfo === null) {
            $seminar = $seminarevent->get_seminar();
            if (!empty($seminar->get_managerreserve())) {
                $reserveinfo = reservations::can_reserve_or_allocate($seminar, [$session], $this->getcontext());
            } else {
                $reserveinfo = array();
            }
        }
        if ($params === null) {
            $params = [];
        }

        // Clean up $params
        $params['s'] = $seminarevent->get_id();
        unset($params['id']);
        unset($params['f']);
        if (!isset($params['backtoallsessions']) && !isset($params['backtoeventinfo'])) {
            $params['backtoallsessions'] = 1;
        }

        /**
         * Helper function to render a reserve link.
         *
         * @param string $baseurl
         * @param string $text
         * @param string $class
         * @param integer $capacity
         * @param integer $maxcapacity Set non-zero to display capacity info next to the link
         * @return string
         */
        $create_reserve_link = function (string $baseurl, string $text, string $class, int $capacity = 0, int $maxcapacity = 0) use ($params): string {
            $linkurl = new moodle_url($baseurl, $params);
            $link = html_writer::link($linkurl, $text, ['class' => $class]);
            if ($maxcapacity > 0) {
                // Add capacity info at the end of the link.
                $link = get_string('reservelinkformat', 'mod_facetoface', [
                    'link' => $link,
                    'capacity' => $capacity,
                    'maxcapacity' => $maxcapacity
                ]);
            }
            return $link;
        };

        $reservelinks = [];

        // Output links to reserve/allocate spaces.
        if (!empty($reserveinfo)) {
            // TODO: find out why the logic is different from reservations/allocate.php et al
            $sessreserveinfo = $reserveinfo;
            if (!$seminarevent->get_allowoverbook()) {
                $sessreserveinfo = \mod_facetoface\reservations::limit_info_to_capacity_left($seminarevent, $sessreserveinfo,
                    max(0, $seminarevent->get_capacity() - $signupcount));
            }
            $sessreserveinfo = \mod_facetoface\reservations::limit_info_by_session_date($seminarevent, $sessreserveinfo);
            if (!empty($sessreserveinfo['allocate']) && $sessreserveinfo['maxallocate'][$seminarevent->get_id()] > 0) {
                // Able to allocate and not used all allocations for other sessions.
                $reservelinks[] = $create_reserve_link(
                    '/mod/facetoface/reservations/allocate.php',
                    get_string('allocate', 'mod_facetoface'),
                    $class . '__allocate',
                    $sessreserveinfo['allocated'][$seminarevent->get_id()],
                    $sessreserveinfo['maxallocate'][$seminarevent->get_id()]
                );
            }
            if (!empty($sessreserveinfo['reserve']) && $sessreserveinfo['maxreserve'][$seminarevent->get_id()] > 0) {
                if (empty($sessreserveinfo['reservepastdeadline'])) {
                    $reservelinks[] = $create_reserve_link(
                        '/mod/facetoface/reservations/reserve.php',
                        get_string('reserve', 'mod_facetoface'),
                        $class . '__reserve',
                        $sessreserveinfo['reserved'][$seminarevent->get_id()],
                        $sessreserveinfo['maxreserve'][$seminarevent->get_id()]
                    );
                }
            } else if (!empty($sessreserveinfo['reserveother']) && empty($sessreserveinfo['reservepastdeadline'])) {
                $reservelinks[] = $create_reserve_link(
                    '/mod/facetoface/reservations/reserve.php',
                    get_string('reserveother', 'mod_facetoface'),
                    $class . '__reserve'
                );
            }
            if (has_capability('mod/facetoface:managereservations', $this->context)) {
                $reservelinks[] = $create_reserve_link(
                    '/mod/facetoface/reservations/manage.php',
                    get_string('managereservations', 'mod_facetoface'),
                    $class . '__managereservations'
                );
            }
        }

        return $reservelinks;
    }

    /**
     * Render the details of a session event
     *
     * @param signup $signup
     * @param render_event_info_option $option
     * @param integer|null $userid
     * @return array of template data of seminarevent_detail
     */
    protected function get_seminar_event_details_data(signup $signup, render_event_info_option $option): array {
        $sessiondata = seminar_event_helper::get_sessiondata($signup->get_seminar_event(), $signup);
        $details = [
            $this->get_seminar_event_details_eventinfo($sessiondata, $signup, $option),
            $this->get_seminar_event_details_sessions($sessiondata, $signup, $option),
            $this->get_seminar_event_details_rooms($sessiondata, $signup, $option),
            $this->get_seminar_event_details_assets($sessiondata, $signup, $option),
        ];

        return array_map(
            function (seminarevent_detail $e) {
                return $e->get_template_data();
            },
            array_filter(
                $details,
                function (?seminarevent_detail $e) {
                    return $e !== null;
                }
            )
        );
    }

    /**
     * Render event summary in seminar event details.
     *
     * @param session_data $session
     * @param signup $signup
     * @param render_event_info_option $option
     * @return seminarevent_detail|null
     */
    protected function get_seminar_event_details_eventinfo(session_data $session, signup $signup, render_event_info_option $option): ?seminarevent_detail {
        global $USER;

        $builder = seminarevent_detail::builder('eventinfo')
            ->set_title(get_string('eventinfo:eventinfo', 'mod_facetoface'))
            ->set_id('f2f-eventinfo')
            ->set_collapsible(true, html_writer::random_id('f2fsection'));

        if ($option->get_displayediteventlink() && has_capability('mod/facetoface:editevents', $this->getcontext())) {
            $builder->set_actionbar(
                seminarevent_actionbar::builder()
                ->set_align('far')
                ->set_class('eventinfo')
                ->add_commandlink(
                    'editevent',
                    new moodle_url('/mod/facetoface/events/edit.php',
                    ['s' => $session->id]),
                    new flex_icon('edit', ['classes' => 'ft-size-400', 'alt' => get_string('editsession', 'mod_facetoface')])
                )
                ->build()
            );
        }

        $section = seminarevent_detail_section::builder();

        $seminarevent = (new seminar_event())->from_record_with_dates($session, false);
        $seminar = $seminarevent->get_seminar();
        $cm = $seminar->get_coursemodule();

        $includedeleted = has_capability('totara/core:seedeletedusers', $this->getcontext());
        $signupcount = attendees_helper::count_signups($seminarevent, $includedeleted);

        // Print customfields.
        $tblprefix = (bool)$seminarevent->get_cancelledstatus() ? 'facetoface_sessioncancel' : 'facetoface_session';
        $prefix    = (bool)$seminarevent->get_cancelledstatus() ? 'facetofacesessioncancel'  : 'facetofacesession';

        $customfields = customfield_get_data(
            $seminarevent->to_record(),
            $tblprefix,
            $prefix,
            true,
            ['extended' => true]
        );
        if (!empty($customfields)) {
            foreach ($customfields as $cftitle => $cfvalue) {
                if (!empty($cfvalue)) {
                    $section->add_detail_unsafe($cftitle, $cfvalue);
                }
            }
        }

        if ($option->get_displaycapacity()) {
            $labeltext = get_string('eventinfo:details:bookedcapacity', 'mod_facetoface');
            $currenthtml = \html_writer::span($signupcount, 'mod_facetoface__capacity__current');
            $maximumhtml = \html_writer::span($seminarevent->get_capacity(), 'mod_facetoface__capacity__maximum');
            $a = array('current' => $currenthtml, 'maximum' => $maximumhtml);
            $stats = get_string('capacitycurrentofmaximum', 'mod_facetoface', $a);
            if ($seminarevent->get_allowoverbook()) {
                $section->add_detail_unsafe($labeltext, get_string('capacityallowoverbook', 'mod_facetoface', $stats));
            } else {
                $section->add_detail_unsafe($labeltext, $stats);
            }
        } else if (!$option->get_calendaroutput()) {
            $placesleft = $seminarevent->get_capacity() - $signupcount;
            $section->add_detail(get_string('seatsavailable', 'mod_facetoface'), (string)max(0, $placesleft));
        }

        // Display Sign-up period.
        $signupperiod = \mod_facetoface\output\session_time::signup_period(
            $seminarevent->get_registrationtimestart(),
            $seminarevent->get_registrationtimefinish()
        );
        if ($signupperiod) {
            $section->add_detail_unsafe(get_string('registrationdetails', 'mod_facetoface'), $signupperiod);
        }

        // Display booking status.
        $bookingstatus = \mod_facetoface\seminar_event_helper::booking_status($session, $signupcount);
        $section->add_detail(get_string('eventbookingstatus', 'mod_facetoface'), $bookingstatus);

        // Display job assignments.
        if (get_config(null, 'facetoface_selectjobassignmentonsignupglobal') &&
            ($seminar->get_selectjobassignmentonsignup() || $seminar->get_forceselectjobassignment())) {
            if ($signup->has_jobassignment()) {
                $jobassignment = \totara_job\job_assignment::get_with_id(
                    $signup->get_jobassignmentid(),
                    false
                );
                if (null == $jobassignment) {
                    // If the job assignment does not exist, we should let the user know that
                    // the job assignment might have been deleted by the site admin, and the
                    // reference is not updated yet
                    $fullname = get_string("missingjobassignment", "mod_facetoface");
                } else {
                    $fullname = clean_string($jobassignment->fullname);
                }
                if (!empty($fullname)) {
                    $section->add_detail_unsafe(get_string('jobassignment', 'mod_facetoface'), $fullname);
                }
            }
        }

        // Display managers.
        if (!in_array($seminar->get_approvaltype(), [seminar::APPROVAL_NONE, seminar::APPROVAL_SELF])) {
            $approver = $seminar->get_approvaltype_string();
            if (!empty($approver)) {
                $section->add_detail(get_string('approvalrequiredby', 'mod_facetoface'), $approver);
            }

            $managernames = [];
            $managers = signup_helper::find_managers_from_signup($signup);
            foreach ($managers as $manager) {
                $manager_url = user_get_profile_url($manager);
                if ($manager_url) {
                    $managernames[] = html_writer::link($manager_url, $manager->fullname);
                } else {
                    $managernames[] = $manager->fullname;
                }
            }
            if (!empty($managernames)) {
                $section->add_detail_unsafe(get_string('managername', 'mod_facetoface'), implode(', ', $managernames));
            }
        }

        // Display trainers.
        $trainerhelper = new trainer_helper($seminarevent);
        $trainerroles = trainer_helper::get_trainer_roles(context_course::instance($seminar->get_course()));
        $trainers = $trainerhelper->get_trainers();
        foreach ((array)$trainerroles as $role => $rolename) {
            if (empty($trainers[$role])) {
                continue;
            }
            $trainer_names = [];
            $rolename = $rolename->localname;
            foreach ($trainers[$role] as $trainer) {
                $trainer_url = new moodle_url('/user/view.php', ['id' => $trainer->id]);
                $trainer_names[] = html_writer::link($trainer_url, fullname($trainer));
            }
            if (!empty($trainer_names)) {
                $section->add_detail_unsafe($rolename, implode(', ', $trainer_names));
            }
        }

        if (!get_config(null, 'facetoface_hidecost') && !empty($seminarevent->get_normalcost())) {
            $section->add_detail(get_string('normalcost', 'mod_facetoface'), format_string($seminarevent->get_normalcost()));

            if (!get_config(null, 'facetoface_hidediscount') && !empty($seminarevent->get_discountcost())) {
                $section->add_detail(get_string('discountcost', 'mod_facetoface'), format_string($seminarevent->get_discountcost()));
            }
        }

        if (!empty($seminarevent->get_details())) {
            $details = file_rewrite_pluginfile_urls(
                $seminarevent->get_details(),
                'pluginfile.php',
                context_module::instance($cm->id)->id,
                'mod_facetoface',
                'session',
                $seminarevent->get_id()
            );
            $details = format_text($details, FORMAT_HTML);
            $section->add_detail_unsafe(get_string('details', 'mod_facetoface'), $details, 'image');
        }

        return $builder->add_section($section)->build();
    }

    /**
     * Render event session information in seminar event details.
     *
     * @param session_data $session
     * @param signup $signup
     * @param render_event_info_option $option
     * @return seminarevent_detail|null
     */
    protected function get_seminar_event_details_sessions(session_data $session, signup $signup, render_event_info_option $option): ?seminarevent_detail {
        $class = 'mod_facetoface__event_details';
        $seminarevent = (new seminar_event())->from_record_with_dates($session, false);
        $builder = seminarevent_detail::builder('sessions')
            ->set_title(get_string('eventinfo:sessions', 'mod_facetoface'))
            ->set_id('f2f-sessions')
            ->set_collapsible(true, html_writer::random_id('f2fsection'));

        if ($seminarevent->get_mintimestart()) {
            $rooms = \mod_facetoface\room_list::get_event_rooms($seminarevent->get_id());
            /** @var \mod_facetoface\seminar_session $date */
            foreach ($seminarevent->get_sessions() as $date) {
                $section = seminarevent_detail_section::builder('session');

                // Session status.
                $status = \mod_facetoface\seminar_session_helper::get_status($session, $date->to_record());
                if ($status !== false) {
                    $section->add_detail(get_string('eventsessionstatus', 'mod_facetoface'), $status);
                }

                // Session dates.
                $sessiontime = \mod_facetoface\output\session_time::to_html(
                    $date->get_timestart(),
                    $date->get_timefinish(),
                    $date->get_sessiontimezone()
                );
                $section->add_detail_unsafe(get_string('eventinfo:details:sessiondate', 'mod_facetoface'), $sessiontime);
                $section->add_detail(get_string('duration', 'mod_facetoface'), format_time((int)$date->get_timestart() - (int)$date->get_timefinish()));

                $rooms = \mod_facetoface\room_list::from_session($date->get_id());
                if (!$rooms->is_empty()) {
                    $roomoutputs = [];
                    // Display room information
                    $backurl = $option->get_backurl();
                    foreach ($rooms as $room) {
                        $roomoutputs[] = $this->get_room_details_html($room, $backurl);
                    }
                    $roomoutput = $this->render_unordered_list($roomoutputs, '', $class, 'rooms', 'room');
                    $section->add_detail_unsafe(get_string('rooms', 'mod_facetoface'), $roomoutput);
                }

                $facilitators = \mod_facetoface\facilitator_list::from_session($date->get_id());
                if (!$facilitators->is_empty()) {
                    $facilitatoroutput = [];
                    $backurl = $option->get_backurl();
                    foreach ($facilitators as $facilitator) {
                        $facilitatoroutput[] = $this->get_facilitator_details_html($facilitator, $backurl);
                    }
                    $facilitatoroutput = $this->render_unordered_list($facilitatoroutput, '', $class, 'facilitators', 'facilitator');
                    $section->add_detail_unsafe(get_string('facilitators', 'mod_facetoface'), $facilitatoroutput);
                }

                if ($option->get_displayassetsinsessions()) {
                    $assets = \mod_facetoface\asset_list::from_session($date->get_id());
                } else {
                    $assets = new \mod_facetoface\asset_list();
                }
                if (!$assets->is_empty()) {
                    $assetoutputs = [];
                    // Display asset information
                    $backurl = $option->get_backurl();
                    foreach ($assets as $asset) {
                        $assetoutputs[] = $this->get_asset_details_html($asset, $backurl);
                    }
                    $assetoutput = $this->render_unordered_list($assetoutputs, '', $class, 'assets', 'asset');
                    $section->add_detail_unsafe(get_string('assets', 'mod_facetoface'), $assetoutput);
                }
                $builder->add_section($section);
            }
        } else {
            $builder->add_section(
                seminarevent_detail_section::builder()
                    ->add_detail_unsafe(
                        get_string('sessiondate', 'mod_facetoface'),
                        html_writer::tag('em', get_string('wait-listed', 'facetoface'))
                    )
            );
        }

        $output = $builder->build();

        return $output;
    }

    /**
     * Render rooms in seminar event details.
     *
     * @param session_data $session
     * @param signup $signup
     * @param render_event_info_option $option
     * @return seminarevent_detail|null
     */
    protected function get_seminar_event_details_rooms(session_data $session, signup $signup, render_event_info_option $option): ?seminarevent_detail {
        if (!$option->get_displayrooms()) {
            return null;
        }

        // TODO: TL-21511
        return null;
    }

    /**
     * Render assets in seminar event details.
     *
     * @param session_data $session
     * @param signup $signup
     * @param render_event_info_option $option
     * @return seminarevent_detail|null
     */
    protected function get_seminar_event_details_assets(session_data $session, signup $signup, render_event_info_option $option): ?seminarevent_detail {
        if (!$option->get_displayassets()) {
            return null;
        }

        // TODO: TL-21511
        return null;
    }

    /**
     * Get the template data of the signup failures information sidebar
     * Only most relevant failures will be displayed
     *
     * @param signup $signup
     * @return array the template data of the sidebar
     */
    protected function get_sidebar_signup_failure(signup $signup): array {
        $failures = signup_helper::get_failures($signup);
        // Display first failure as the most relevant
        $failure = clean_string(reset($failures));
        $heading = html_writer::tag('h3', get_string('signupunavailable', 'mod_facetoface'));
        return ['class' => 'signup-failure', 'content' => $heading . $failure];
    }

    /**
     * Get the template data of the sign-up sidebar
     *
     * @param signup $signup
     * @param render_event_info_option $option
     * @return array the template data of the sidebar
     */
    protected function get_sidebar_signup(signup $signup, render_event_info_option $option): array {
        $params = [
            'signup' => $signup,
            'backtoallsessions' => $option->get_backtoallsessions(),
        ];
        $attrs = [
            'name' => 'signupform'
        ];
        $signuplink = $this->get_signup_link($signup->get_seminar_event());
        if ($signuplink === '') {
            return [];
        }
        $mform = new \mod_facetoface\form\signup(new moodle_url($signuplink), $params, 'post', '', $attrs);
        // Add server-side validation results.
        if (!$mform->is_cancelled() && $mform->is_submitted()) {
            $mform->is_validated();
        }
        return ['class' => 'signup', 'content' => $mform->render()];
    }

    /**
     * Get the array of template data of
     * - the booked sidebar containing the link to cancellation
     * - the cancellation form sidebar
     *
     * @param signup $signup
     * @param render_event_info_option $option
     * @param boolean $cancelsignup Set true to activate the "cancel sign-up" sidebar
     * @return array of the template data of the sidebar
     */
    protected function get_sidebars_cancellation(signup $signup, render_event_info_option $option, bool $cancelsignup): array {
        $idpart = 'f2f-signup-cancel-';
        $checkid = html_writer::random_id($idpart);
        $bookedid = html_writer::random_id($idpart);
        $confirmid = html_writer::random_id($idpart);

        $sidebars = [];

        // Cancellation
        $currentstate = $signup->get_state();
        $allowcancellation = $currentstate->can_switch(user_cancelled::class);
        if ($currentstate instanceof waitlisted) {
            $canceltext = get_string('cancelwaitlist', 'mod_facetoface');
        } else {
            $canceltext = get_string('cancelbooking', 'mod_facetoface');
        }
        $class = 'mod_facetoface__eventinfo__cancellation';
        $heading = html_writer::tag('h3', signup_helper::get_user_booking_status($currentstate, false));
        if ($allowcancellation) {
            $link = html_writer::tag('label', $canceltext, ['title' => $canceltext, 'for' => $checkid, 'role' => 'link', 'class' => $class . '__link']);
            $toggle = ['id' => $checkid, 'class' => 'cancellation', 'on' => $cancelsignup, 'idon' => $bookedid, 'idoff' => $confirmid];
        } else {
            $link = '';
            $toggle = [];
        }
        $sidebars[] = ['class' => 'cancellation', 'content' => $heading . $link, 'id' => $bookedid, 'toggle' => $toggle];

        if ($allowcancellation) {
            // Confirm
            $cancellation_note = (object)['id' => $signup->get_id()];
            customfield_load_data($cancellation_note, 'facetofacecancellation', 'facetoface_cancellation');

            $params = [
                's' => $signup->get_sessionid(),
                'backtoallsessions' => $option->get_backtoallsessions(),
                'cancellation_note' => $cancellation_note,
                'userisinwaitlist' => $currentstate instanceof waitlisted,
            ];
            $url = new \moodle_url('/mod/facetoface/eventinfo.php');
            $mform = new \mod_facetoface\form\cancelsignup($url, $params);
            // Add server-side validation results.
            if (!$mform->is_cancelled() && $mform->is_submitted()) {
                $mform->is_validated();
            }
            $closebutton = ['id' => $checkid, 'text' => '&times;', 'alttext' => get_string('eventinfo:cancelcancel', 'mod_facetoface'), 'class' => 'cancellation-cancellation'];
            $sidebars[] = ['class' => 'cancellation-confirm', 'content' => $mform->render(), 'id' => $confirmid, 'togglebutton' => $closebutton];
        }

        return $sidebars;
    }

    /**
     * Render signup state tranisitons failures
     * Only most relevant failures will be displayed
     * @param array $failures string[string] where value - failure text and key failure code
     * @return string
     */
    public function render_signup_failures(array $failures) : string {
        // Display first failure as the most relevant
        reset($failures);
        $failure = current($failures);
        return $this->output->notification($failure, 'info');
    }

    /**
     * Displays a bulk actions selector
     * @return string
     */
    public function display_bulk_actions_picker(): string {
        global $OUTPUT;

        $status_options = \mod_facetoface\attendees_helper::get_status();
        unset($status_options[\mod_facetoface\signup\state\not_set::get_code()]);
        $out = $OUTPUT->container_start('facetoface-bulk-actions-picker');
        $select = html_writer::select($status_options, 'bulkattendanceop', '',
            array('' => get_string('bulkactions', 'facetoface')), array('class' => 'bulkactions'));
        $label = get_string('mark_selected_as', 'facetoface');
        $error = get_string('selectoptionbefore', 'facetoface');
        $hidenlabel = html_writer::tag('span', $error, array('id' => 'selectoptionbefore', 'class' => 'hide error'));
        $out .= $label;
        $out .= $select;
        $out .= $hidenlabel;
        $out .= $OUTPUT->container_end();

        return $out;
    }

    /**
     * Get signup link URL.
     * This link is processed through mod_facetoface\\hook\\alternative_signup_link to allow additional plugins to set different
     * page for users to sign up.
     *
     * @param seminar_event $seminarevent
     * @param string $link
     * @return string
     */
    public function get_signup_link(seminar_event $seminarevent): string {
        // look for hooks unless set_signup_link() is called
        if ($this->signuplink === self::DEFAULT_SIGNUP_LINK) {
            // Start hook code
            $signupurl = $this->signuplink;
            $hook = new \mod_facetoface\hook\alternative_signup_link($seminarevent, $signupurl);
            $hook->execute();
            // End hook code
            return $hook->signuplink;
        }
        return $this->signuplink;
    }

    /**
     * Set singup link when enrolment method is enabled and called.
     *
     * @param string $link
     * @deprecated since Totara 13.0
     */
    public function set_signup_link(string $link): void {
        debugging('mod_facetoface_renderer::set_signup_link() is deprecated. Please use mod_facetoface\\hook\\alternative_signup_link instead.', DEBUG_DEVELOPER);
        $this->signuplink = $link;
    }

    /**
     * Render table of users used in attendance list
     * @param \mod_facetoface\bulk_list $list User list Needed only with job assignments
     * @param int $courseid
     */
    public function print_attendance_upload_table(\mod_facetoface\bulk_list $list, int $courseid): void {
        global $OUTPUT;

        $status = attendance_state::get_all_attendance_csv();

        $userlist = $list->get_user_ids();
        $users = \mod_facetoface\attendees_list_helper::get_user_list($userlist);

        $table = new html_table();
        $table->head = [
            get_string('name'),
            get_string('eventattendanceheader', 'mod_facetoface'),
        ];
        // Really bad hack to know if optional column exists or not.
        // if you can think something better, let me know.
        if (isset($list->get_user_data(current($users)->id)['eventgrade'])) {
            $table->head[] = get_string('eventgradeheader', 'mod_facetoface');
        }
        $table->head[] = '';
        $table->attributes = ['class' => 'generaltable userstoupload fullwidth'];
        $context = \context_course::instance($courseid);
        if (count($users) > 0) {
            foreach ($users as $user) {
                $error = '';
                $row = [];
                $userurl = new \moodle_url('/user/view.php', ['id' => $user->id]);
                if ($courseid != SITEID && is_enrolled($context, $user->id)) {
                    $userurl->param('course', $courseid);
                }
                // User full name column.
                $row[] = \html_writer::link($userurl, fullname($user));

                $data = $list->get_user_data($user->id);

                // Event attendance column
                $state = $data['eventattendance'];
                if (!in_array($state, array_keys($status))) {
                    $row[] = $error = get_string('error:invalidvalue', 'mod_facetoface');
                } else {
                    $row[] = $status[$state]::get_string();
                }
                // Event grade column.
                if (isset($data['eventgrade'])) {
                    $value = trim($data['eventgrade']);
                    if (is_numeric($value) && $value >= 0 && $value <= 100) {
                        $row[] = \mod_facetoface\grade_helper::format($value, $courseid);
                    } else {
                        $row[] = $error = get_string('error:invalidvalue', 'mod_facetoface');
                    }
                }
                // Result icon column.
                if (!empty($error)) {
                    $row[] = $OUTPUT->flex_icon('warning', ['classes' => 'ft-size-100', 'alt' => $error]);
                } else {
                    $row[] = $OUTPUT->flex_icon('check', ['classes' => 'ft-size-100 ft-state-success', 'alt' => get_string('success')]);
                }

                $table->data[] = new html_table_row($row);
            }
        }

        $data = $list->get_validation_results();
        if (!empty($data)) {
            foreach ($data as $entry) {
                $row = [];
                foreach ($entry as $key => $value) {
                    $row[] = s($value);
                }
                reset($row);
                $row[array_key_first($row)] = current($row) .
                    \html_writer::span(get_string('error:usernotfound', 'mod_facetoface'), 'usernotfound');
                $row[] = $OUTPUT->flex_icon('warning', [
                    'classes' => 'ft-size-100',
                    'alt' => get_string('userdoesnotexist', 'totara_core')
                ]);
                $table->data[] = new html_table_row($row);
            }
        }
        $total = count($users) + count($data);
        echo \html_writer::tag('h3', get_string('uploadattendancereview', 'mod_facetoface', $total));
        echo $OUTPUT->render($table);
    }
}
