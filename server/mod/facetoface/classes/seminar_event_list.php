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
 * @author  Oleg Demeshev <oleg.demeshev@totaralearning.com>
 * @package mod_facetoface
 */

namespace mod_facetoface;

use mod_facetoface\query\event\event_query;

defined('MOODLE_INTERNAL') || die();

/**
 * Class seminar_event_list represents all events in one activity
 */
final class seminar_event_list implements \Iterator, \Countable {

    use traits\seminar_iterator;

    /**
     * Add seminar_event to list
     *
     * @param seminar_event $item
     * @return void
     */
    public function add(seminar_event $item): void {
        $this->items[$item->get_id()] = $item;
    }

    /**
     * Create list of events in seminar
     *
     * @deprecated since Totara 13
     * @param seminar $seminar
     * @return seminar_event_list
     */
    public static function form_seminar(seminar $seminar): seminar_event_list {
        debugging('seminar_event_list::form_seminar() has been renamed to seminar_event_list::from_seminar()', DEBUG_DEVELOPER);
        return seminar_event_list::from_seminar($seminar);
    }

    /**
     * Create list of events in seminar
     *
     * @since Totara 13
     * @param seminar $seminar
     * @return seminar_event_list
     */
    public static function from_seminar(seminar $seminar): seminar_event_list {
        global $DB;
        $seminarevents = $DB->get_records('facetoface_sessions', ['facetoface' => $seminar->get_id()]);
        $list = new static();
        foreach ($seminarevents as $seminarevent) {
            $item = new seminar_event();
            $list->add($item->from_record($seminarevent));
        }
        return $list;
    }

    /**
     * Get any seminar events that we need to check for waitlist entries.
     *
     * @param int $now
     * @return seminar_event_list
     */
    public static function pending_waitlist_clear(int $now = 0): seminar_event_list {
        global $DB;
        if (empty($now) || $now < 0) {
            $now = time();
        }

        // SQL that gets all events that have started, but still have at least one waitlisted user.
        $sql = 'SELECT DISTINCT fs.*, ( SELECT MIN(fsd.timestart)
                                          FROM {facetoface_sessions_dates} fsd
                                         WHERE fsd.sessionid = fs.id
                                      ) AS mintimestart
                  FROM {facetoface_sessions} fs
                  JOIN {facetoface_sessions_dates} fsd
                    ON fsd.sessionid = fs.id
                  JOIN {facetoface} f
                    ON fs.facetoface = f.id
                 WHERE f.waitlistautoclean = 1
                   AND EXISTS ( SELECT 1
                                  FROM {facetoface_signups} fss
                                  JOIN {facetoface_signups_status} fst
                                    ON fst.signupid = fss.id
                                 WHERE fss.sessionid = fs.id
                                   AND fst.statuscode = :wcode
                       )';

        $list = new static();
        $seminarevents = $DB->get_records_sql($sql, ['wcode' => \mod_facetoface\signup\state\waitlisted::get_code()]);
        foreach ($seminarevents as $seminarevent) {
            if ($seminarevent->mintimestart < $now) {
                // The event has started, pass it along to have its waitlist checked.
                unset($seminarevent->mintimestart);
                $item = new seminar_event();
                $list->add($item->from_record($seminarevent));
            }
        }

        return $list;
    }

    /**
     * Create list of all events in seminar
     *
     * @return seminar_event_list
     */
    public static function get_all(): seminar_event_list {
        global $DB;
        $seminarevents = $DB->get_records('facetoface_sessions');
        $list = new static();
        foreach ($seminarevents as $seminarevent) {
            $item = new seminar_event();
            $list->add($item->from_record($seminarevent));
        }
        return $list;
    }

    /**
     * Retrieving the list of seminar_evnet base on query builder object, as this query will give us the sql built from filters
     * and sortorder that are injected into the object before this stage.
     *
     * @param event_query $query
     * @return seminar_event_list
     */
    public static function from_query(event_query $query): seminar_event_list {
        global $DB;

        $statement = $query->get_statement();
        $records = $DB->get_records_sql($statement->get_sql(), $statement->get_parameters());

        $list = new static();
        foreach ($records as $record) {
            $seminarevent = new seminar_event();
            $seminarevent->from_record($record);
            $list->add($seminarevent);
        }

        return $list;
    }

    /**
     * Returning an array of dummy class that hold data of a session. With a event's id associated as the array key.
     *
     * @return \stdClass[]
     */
    public function to_records(): array {
        $data = [];

        /** @var seminar_event $item */
        foreach ($this->items as $item) {
            $data[$item->get_id()] = $item->to_record();
        }

        return $data;
    }
}