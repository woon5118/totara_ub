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

use mod_facetoface\signup\state\{
    booked,
    waitlisted
};

/**
 * Class calendar implements the calendar event handling interface for seminar events.
 */
final class calendar {

    /**
     * Add a link to the seminar event to the courses calendar.
     *
     * @param seminar_event $seminarevent
     * @param string   $calendartype Which calendar to add the event to (user, course, site)
     * @param int      $userid       Optional param for user calendars
     * @param string   $eventtype    Optional param for user calendar (booking/session)
     * @return bool
     */
    public static function add_seminar_event(seminar_event $seminarevent, string $calendartype = 'none', int $userid = 0, string $eventtype = 'session'): bool {
        $seminar = new seminar($seminarevent->get_facetoface());

        if (empty($seminarevent->get_mintimestart())) {
            return true; //date unkown, can't add to calendar
        }

        if ($seminar->get_showoncalendar() == 0 && $seminar->get_usercalentry() == 0) {
            return true; //facetoface calendar settings prevent calendar
        }

        // Generate link to append to the description.
        $description = '';
        $linkurl = new \moodle_url('/mod/facetoface/signup.php', array('s' => $seminarevent->get_id()));
        $linktext = get_string('signupforthissession', 'facetoface');

        if ($calendartype == 'site' && $seminar->get_showoncalendar() == F2F_CAL_SITE) {
            $courseid = SITEID;
            $description .= \html_writer::link($linkurl, $linktext);
        } else if ($calendartype == 'course' && $seminar->get_showoncalendar() == F2F_CAL_COURSE) {
            $courseid = $seminar->get_course();
            $description .= \html_writer::link($linkurl, $linktext);
        } else if ($calendartype == 'user' && $seminar->get_usercalentry()) {
            $courseid = 0;
            if ($eventtype == 'session') {
                $linkurl = new \moodle_url('/mod/facetoface/attendees/view.php', array('s' => $seminarevent->get_id()));
            }
            $description .= get_string("calendareventdescription{$eventtype}", 'facetoface', $linkurl->out());
        } else {
            return true;
        }

        // Remove all calendar events related to current session and user before adding new event to avoid duplication.
        self::remove_seminar_event($seminarevent, $courseid, $userid);

        // Ready to add standard events.
        return self::add_event_internal($seminarevent, $courseid, $userid, true, $eventtype, '', $description);
    }

    /**
     * Generate a new set of calendar events for the facilitator of one or more sessions in a seminar event.
     *
     * @param seminar_event $seminarevent
     * @param facilitator_user $facilitator
     * @return void
     */
    public static function add_facilitator_event(seminar_event $seminarevent, facilitator_user $facilitator): void {
        global $PAGE, $DB;

        // Is there a date for this seminar (should be since facilitator, but good to check)?
        if (empty($seminarevent->get_mintimestart())) {
            return;
        }

        // Remove all calendar events related to current session and user before adding new event to avoid duplication.
        self::remove_facilitator_event($seminarevent, $facilitator->get_userid());

        // Limit the sessions list to just the ones this facilitator cares about.
        $seminarevent->facilitator_sessions_only($facilitator->get_id());

        // Include description?
        $linkurl = new \moodle_url('/mod/facetoface/attendees/view.php', array('s' => $seminarevent->get_id()));
        $introduction = get_string("calendareventdescriptionfacilitato", 'facetoface', $linkurl->out());

        // Ready to add standard events.
        self::add_event_internal($seminarevent, 0, $facilitator->get_userid(), false, 'facilitator', $introduction, '');

        // load all sessions again
        $seminarevent->get_sessions(true);
    }

    /**
     * Perform the parts of add_seminar_event() and add_facilitator_event() which are similar.
     *
     * @param seminar_event $seminarevent
     * @param int $courseid
     * @param int $userid
     * @param bool $usemodule whether to include the seminar module instance as part of the event
     * @param string $eventtype
     * @param string $prepend markup that goes before the rendered description
     * @param string $postpend markup that goes after the rendered description
     * @return bool
     */
    private static function add_event_internal(seminar_event $seminarevent, int $courseid, int $userid, bool $usemodule, string $eventtype, string $prepend = '', string $postpend = ''): bool {
        global $PAGE, $DB;

        // Start description with prepend.
        $description = $prepend;

        $seminar = new \mod_facetoface\seminar($seminarevent->get_facetoface());
        if (!empty($seminar->get_intro())) {
            $description .= \html_writer::tag('p', clean_param($seminar->get_intro(), PARAM_CLEANHTML));
        }

        // Use the facetoface seminar event renderer to build the description.
        $seminarrenderer = $PAGE->get_renderer('mod_facetoface');

        // Maximum event name is 256 chars, which may be shorter than seminar name.
        $shortname = $seminar->get_shortname();
        if (empty($shortname)) {
            // Calendar-related constants
            if (!defined('CALENDAR_MAX_NAME_LENGTH')) {
                // Admins may override this in config.php if necessary.
                define('CALENDAR_MAX_NAME_LENGTH', 256);
            }
            $shortname = shorten_text($seminar->get_name(), CALENDAR_MAX_NAME_LENGTH);
        }

        // Link event to seminar activity or not?
        if ($usemodule) {
            $instance = $seminar->get_id();
            $modulename = 'facetoface';
        } else {
            $instance = 0;
            $modulename = '';
        }

        // Truncate eventtype if necessary, full event type is limited to 20 characters in DB.
        if (strlen($eventtype) > 10) {
            $eventtype = substr($eventtype, 0, 10);
        }
        $eventtype = "facetoface{$eventtype}";

        $result = true;
        $sessiondates = clone $seminarevent->get_sessions();
        foreach ($sessiondates as $date) {
            // Render each session separately as a single session.
            $session_description = $description . $seminarrenderer->render_seminar_event($seminarevent, false, true, false, 'mod_facetoface__event_details', $date->get_id());
            $session_description .= $postpend;

            $newevent = new \stdClass();
            $newevent->name = $shortname;
            $newevent->description = $session_description;
            $newevent->format = FORMAT_HTML;
            $newevent->courseid = $courseid;
            $newevent->groupid = 0;
            $newevent->userid = $userid;
            $newevent->uuid = "{$seminarevent->get_id()}";
            $newevent->instance = $instance;
            $newevent->modulename = $modulename;
            $newevent->eventtype = $eventtype;
            $newevent->timestart = $date->get_timestart();
            $newevent->timeduration = $date->get_timefinish() - $date->get_timestart();
            $newevent->visible = 1;
            $newevent->timemodified = time();

            $result = $result && $DB->insert_record('event', $newevent);
        }

        return $result;
    }

    /**
     * Update site/course and user calendar entries.
     *
     * @param seminar_event $seminarevent
     * @return bool
     */
    public static function update_entries(seminar_event $seminarevent): bool {
        global $USER;

        // Do not re-create calendars as they already removed from cancelled session.
        if ((bool)$seminarevent->get_cancelledstatus()) {
            return true;
        }

        $seminar = $seminarevent->get_seminar();
        $helper = new attendees_helper($seminarevent);

        // Remove from all calendars.
        self::delete_user_events($seminarevent, 'booking');
        self::delete_user_events($seminarevent, 'session');
        self::remove_facilitator_event($seminarevent, 0);
        self::remove_seminar_event($seminarevent, $seminar->get_course());
        self::remove_seminar_event($seminarevent, SITEID);

        // If there are internal facilitators, add to facilitators' user calendars
        $internal_facilitators = facilitator_list::from_seminarevent($seminarevent->get_id(), true);
        if ($internal_facilitators->count()) {
            foreach ($internal_facilitators as $facilitator_user) {
                self::add_facilitator_event($seminarevent, $facilitator_user);
            }
        }

        // If no other events need to be created, return here.
        if ($seminar->get_showoncalendar() == 0 && $seminar->get_usercalentry() == 0) {
            return true;
        }

        // Add to NEW calendartype.
        if ($seminar->get_usercalentry() > 0) {
            // Get ALL enrolled/booked users.
            $statuscodes = [booked::get_code(), waitlisted::get_code()];
            $users = $helper->get_attendees_with_codes($statuscodes);

            // This adds the seminar events to the current user's personal calendar, which is arbitrary and should be reconsidered.
            if (!in_array($USER->id, array_keys($users))) {
                self::add_seminar_event($seminarevent, 'user', $USER->id, 'session');
            }

            foreach ($users as $user) {
                $eventtype = $user->statuscode == \mod_facetoface\signup\state\booked::get_code() ? 'booking' : 'session';
                self::add_seminar_event($seminarevent, 'user', $user->id, $eventtype);
            }
        }

        if ($seminar->get_showoncalendar() == F2F_CAL_COURSE) {
            self::add_seminar_event($seminarevent, 'course');
        } else if ($seminar->get_showoncalendar() == F2F_CAL_SITE) {
            self::add_seminar_event($seminarevent, 'site');
        }

        return true;
    }

    /**
     * Delete all user level calendar events for a seminar event
     *
     * @param seminar_event $seminarevent Record from the facetoface_sessions table
     * @param string $eventtype Type of the event (booking or session)
     * @return array An array of user events
     */
    public static function delete_user_events(seminar_event $seminarevent, string $eventtype): array {
        global $DB;

        // Truncate eventtype if necessary
        if (strlen($eventtype) > 10) {
            $eventtype = substr($eventtype, 0, 10);
        }

        // Without uuid(sessionid) param, this function deletes all events(seminar with multiple events) except the last one,
        // meaning the last event (running the update calendar) will delete all previous events created just now.
        // Usercase: Seminar has 2 events and attendee signed to the 1st event.
        $whereclause = "modulename = ? AND
                        eventtype = ? AND
                        instance = ? AND
                        uuid = ?";

        $whereparams = array('facetoface', "facetoface{$eventtype}", $seminarevent->get_facetoface(), $seminarevent->get_id());

        if ('session' == $eventtype) {
            $likestr = "%attendees.php?s={$seminarevent->get_id()}%";
            $likeold = $DB->sql_like('description', '?');
            $whereparams[] = $likestr;

            $likestr = "%view.php?s={$seminarevent->get_id()}%";
            $likenew = $DB->sql_like('description', '?');
            $whereparams[] = $likestr;

            $whereclause .= " AND ($likeold OR $likenew)";
        }

        //users calendar
        $users = $DB->get_records_sql("SELECT DISTINCT userid FROM {event} WHERE $whereclause", $whereparams);
        if ($users && count($users) > 0) {
            // Delete the existing events
            $DB->delete_records_select('event', $whereclause, $whereparams);
        }

        return $users;
    }

    /**
     * Remove all entries in the course calendar which relate to this seminar event.
     *
     * @param seminar_event $seminarevent Record from the facetoface_sessions table
     * @param int $courseid ID of the course (courseid, SITEID, 0)
     * @param int $userid   ID of the user
     * @return bool
     */
    public static function remove_seminar_event(seminar_event $seminarevent, int $courseid = 0, int $userid = 0): bool {
        global $DB;

        $params = array($seminarevent->get_facetoface(), $userid, $courseid, $seminarevent->get_id());

        return $DB->delete_records_select('event', "modulename = 'facetoface' AND
                                                instance = ? AND
                                                userid = ? AND
                                                courseid = ? AND
                                                uuid = ?", $params);
    }

    /**
     * Remove all entries in the course calendar which relate to this facilitator event.
     *
     * @param seminar_event $seminarevent Record from the facetoface_sessions table
     * @param int $userid   ID of the user, or 0 to remove all facilitator events for this seminar event
     * @return bool
     */
    public static function remove_facilitator_event(seminar_event $seminarevent, int $userid = 0): bool {
        global $DB;

        if ($userid) {
            $select = "eventtype = 'facetofacefacilitato' AND userid = ? AND uuid = ?";
            $params = array($userid, $seminarevent->get_id());
        } else {
            $select = "eventtype = 'facetofacefacilitato' AND uuid = ?";
            $params = array($seminarevent->get_id());
        }

        return $DB->delete_records_select('event', $select, $params);
    }

    /**
     * Remove all entries in the course calendar which relate to this seminar event.
     *
     * Note: the user/course ID is nominally an integer but it is not right for the
     * code to assume its value will always > 0. This is why default values for the
     * parameters are null, NOT 0. In other words, if a caller passes in a non null
     * user ID, then the assumption is the caller wants to remove calendar entries
     * for that specific userid. It is this contract that works around a problem in
     * `calendar::remove_seminar_event` - where a course/user ID is always
     * used even if it is 0.
     *
     * @param seminar_event $seminarevent record from the facetoface_sessions table.
     * @param integer $courseid identifies the specific course whose calendar entry
     *        is to be removed. If null, it is ignored.
     * @param integer $userid identifies the specific user whose calendar entry is
     *        to be removed. If null, it is ignored.
     *
     * @return boolean true if the removal succeeded.
     */
    public static function remove_all_entries(seminar_event $seminarevent, int $courseid = null, int $userid = null): bool {
        global $DB;

        $initial = new \stdClass();
        $initial->whereClause = "modulename = 'facetoface'";
        $initial->params = array();

        $fragments = array(
            array('instance', $seminarevent->get_facetoface()),
            array('uuid',     $seminarevent->get_id()),
            array('courseid', $courseid),
            array('userid',   $userid)
        );

        $final = array_reduce($fragments,
            function (\stdClass $accumulated, array $fragment) {

                list($field, $value) = $fragment;
                if (is_null($value)) {
                    return $accumulated;
                }

                $accumulated->whereClause = sprintf('%s AND %s = ?', $accumulated->whereClause, $field);
                $accumulated->params[] = $value;

                return $accumulated;
            },

            $initial
        );

        return $DB->delete_records_select('event', $final->whereClause, $final->params);
    }

    /**
     * Get custom field filters that are currently selected in seminar settings
     *
     * @return array Array of objects if any filter is found, empty array otherwise
     */
    public static function get_customfield_filters(): array {
        global $DB;

        $sessfields = array();
        $roomfields = array();
        $allsearchfields = get_config(null, 'facetoface_calendarfilters');
        if ($allsearchfields) {
            $customfieldids = array('sess' => array(), 'room' => array());
            $allsearchfields = explode(',', $allsearchfields);

            foreach ($allsearchfields as $filterkey) {
                // Customfields are prefixed with room_ and sess_ strings
                // @see settings.php refer to facetoface_calendarfilters setting for details.
                if (strpos($filterkey, 'sess_') === 0) {
                    $customfieldids['sess'][] = explode('_', $filterkey)[1];
                }
                if (strpos($filterkey, 'room_') === 0) {
                    $customfieldids['room'][] = explode('_', $filterkey)[1];
                }
            }
            if (!empty($customfieldids['sess'])) {
                list($cfids, $cfparams) = $DB->get_in_or_equal($customfieldids['sess']);
                $sql = "SELECT * FROM {facetoface_session_info_field} WHERE id $cfids";
                $sessfields = $DB->get_records_sql($sql, $cfparams);
            }
            if (!empty($customfieldids['room'])) {
                list($cfids, $cfparams) = $DB->get_in_or_equal($customfieldids['room']);
                $sql = "SELECT * FROM {facetoface_room_info_field} WHERE id $cfids";
                $roomfields = $DB->get_records_sql($sql, $cfparams);
            }
        }

        return array('sess' => $sessfields, 'room' => $roomfields);
    }

    /**
     * Check if we need to update the calendar
     * @param seminar $new_seminar
     * @param seminar $old_seminar
     * @return bool
     */
    public static function is_update_required(seminar $new_seminar, seminar $old_seminar): bool {
        $is_changed = false;
        if ($new_seminar->get_showoncalendar() !== $old_seminar->get_showoncalendar()) {
            $is_changed = true;
        }
        if ($new_seminar->get_usercalentry() !== $old_seminar->get_usercalentry()) {
            $is_changed = true;
        }
        if (strcmp($new_seminar->get_intro(), $old_seminar->get_intro()) !== 0) {
            $is_changed = true;
        }
        if (strcmp($new_seminar->get_shortname(), $old_seminar->get_shortname()) !== 0) {
            $is_changed = true;
        }
        if (empty($new_seminar->get_shortname())) {
            // We use a full seminar name in calendar if short name is not exist
            if (strcmp($new_seminar->get_name(), $old_seminar->get_name()) !== 0) {
                $is_changed = true;
            }
        }
        return $is_changed;
    }
}