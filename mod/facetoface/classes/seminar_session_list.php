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
 * @author Tatsuhiro Kirihara <tatsuhiro.kirihara@totaralearning.com>
 * @author Oleg Demeshev <oleg.demeshev@totaralearning.com>
 * @package mod_facetoface
 */

namespace mod_facetoface;

use mod_facetoface\signup\state\{attendance_state, booked, waitlisted};

defined('MOODLE_INTERNAL') || die();


/**
 * Class seminar_session_list represents Seminar event sessions date list
 */
final class seminar_session_list implements \Iterator, \Countable {

    use traits\seminar_iterator;

    /**
     * Keep track of those session's id that are already over.
     * @var seminar_session[]
     */
    private $overs = [];

    /**
     * Keep track of those session's id that are upcoming.
     * @var seminar_session[]
     */
    private $upcoming = [];

    /**
     * Reloading the trackers of those session(s) that either are over and upcoming.
     * This is for test purpose and also ability to re-calculate the sessions
     * @return seminar_session_list
     */
    public function reload(): seminar_session_list {
        $this->overs = [];
        $this->upcoming = [];

        return $this;
    }

    /**
     * Add seminar session to item list
     * @param seminar_session $item
     */
    public function add(seminar_session $item) {
        $id = $item->get_id();
        $this->items[$id] = $item;

        $time = time();

        if ($item->is_upcoming($time)) {
            $this->upcoming[$id] = $item;
        } else if ($item->is_over($time)) {
            $this->overs[$id] = $item;
        }
    }

    /**
     * Create list of seminar sessions from seminar event
     * @param seminar_event $seminarevent
     * @return seminar_session_list
     */
    public static function from_seminar_event(seminar_event $seminarevent) : seminar_session_list {
        global $DB;
        $list = new seminar_session_list();
        $sessionrecords = $DB->get_records('facetoface_sessions_dates', ['sessionid' => $seminarevent->get_id()], 'timestart DESC');
        foreach ($sessionrecords as $sessionrecord) {
            $session = new seminar_session();
            $list->add($session->from_record($sessionrecord));
        }
        return $list;
    }

    /**
     * Count sessions that will happen in the future.
     *
     * @param int $time
     * @return int
     */
    public function count_upcoming(int $time =0): int {
        return count($this->get_upcoming($time));
    }

    /**
     * Count sessions that are over, based on given time.
     *
     * @param int $time
     * @return int
     */
    public function count_over(int $time = 0): int {
        return count($this->get_over($time));
    }

    /**
     * Get sessions that are over.
     *
     * @param int $time
     * @return seminar_session[]
     */
    public function get_over(int $time = 0): array {
        if (empty($this->overs)) {
            if (0 >= $time) {
                $time = time();
            }

            $this->overs = [];

            /** @var seminar_session $session */
            foreach ($this->items as $session) {
                if ($session->is_over($time)) {
                    $this->overs[] = $session;
                }
            }
        }

        return $this->overs;
    }

    /**
     * Get sessions that will happen in the future.
     *
     * @param int $time
     * @return seminar_session[]
     */
    public function get_upcoming(int $time = 0): array {
        if (empty($this->upcoming)) {
            if (0 >= $time) {
                $time = time();
            }

            $this->upcoming = [];

            /** @var seminar_session $session */
            foreach ($this->items as $session) {
                if ($session->is_upcoming($time)) {
                    $this->upcoming[] = $session;
                }
            }
        }

        return $this->upcoming;
    }

    /**
     * Return true if all the sessions are over.
     *
     * If there are no sessions, then it is most likely to be a waitlist event.
     *
     * @param int $time
     * @return bool
     */
    public function is_everything_over(int $time = 0): bool {
        if ($this->is_empty()) {
            // Waitlisted event.
            return false;
        }

        return $this->count_over($time) === $this->count();
    }

    /**
     * Get the session status summary string.
     *
     * If $reload is true, then it will recalculate the session data before generating the summary.
     *
     * @param bool $reload
     * @return string
     */
    public function get_summary(bool $reload = false): string {
        if ($reload) {
            $this->reload();
        }

        $time = time();
        $a = new \stdClass();

        $a->total = count($this->items);
        $a->upcoming = $this->count_upcoming($time);
        $a->over = $this->count_over($time);

        return get_string('eventsummary', 'mod_facetoface', $a);
    }

    /**
     * Return the last session of an event.
     *
     * @return seminar_session
     */
    public function get_last(): seminar_session {
        $items = $this->items;
        self::sort($items);
        return end($items);
    }

    /**
     * Return the first session of an event.
     *
     * @return seminar_session
     */
    public function get_first(): seminar_session {
        $items = $this->items;
        self::sort($items);
        $o = array_shift($items);
        return $o;
    }

    /**
     * Returning an array of dummy class holding data of seminar_session. And the session's id associated as array key.
     *
     * @return \stdClass[]
     */
    public function to_records(): array {
        $data = [];

        /** @var seminar_session $item */
        foreach ($this->items as $item) {
            $data[$item->get_id()] = $item->to_record();
        }

        return $data;
    }

    /**
     * Sort the list of items based on finish time.
     *
     * The earliest finish time will be first, latest finish time will be last.
     *
     * @param seminar_session[] $items
     */
    private static function sort(array &$items): void {
        usort(
            $items,
            function (seminar_session $a, seminar_session $b) {
                $result = $a->get_timefinish() > $b->get_timefinish();
                return $result ? 1 : -1;
            }
        );
    }

    /**
     * Given an array of date object (where it is must include $timestart and $timefinish), then it this factory method will be able to retrieve the list of session dates that
     * the passed in $userid is having and conflicting with the $dates. Each date object in $dates must have the keys specified as bellow
     * + timestart: int => time start of a new session, or old session
     * + timefinish: int => time finish of new session, or old session
     *
     * @param \stdClass[]        $dates
     * @param int                $userid
     * @param seminar_event|null $excludeseminarevent
     * @param string[]           $additionalstatuses
     *
     * @return seminar_session_list
     */
    public static function from_user_conflicts_with_dates(int $userid, array $dates, seminar_event $excludeseminarevent = null,
                                                         array $additionalstatuses = []): seminar_session_list {
        global $DB;
        $list = new static();

        if (empty($dates)) {
            // No times were given, so we can't supply sessions within any times. Just return empty list.
            return $list;
        }

        $sql = "
            SELECT d.*
            FROM {facetoface_sessions_dates} d
            INNER JOIN {facetoface_sessions} s 
            ON s.id = d.sessionid

            LEFT JOIN {facetoface_signups} su 
            ON su.sessionid = s.id AND su.userid = :userid1

            LEFT JOIN {facetoface_signups_status} ss 
            ON ss.signupid = su.id AND ss.superceded <> 1

            LEFT JOIN {facetoface_session_roles} sr
            ON sr.sessionid = s.id AND sr.userid = :userid2

            WHERE s.cancelledstatus = 0
        ";

        $params = [
            'userid1' => $userid,
            'userid2' => $userid
        ];

        // Building status sql for the signup/users.
        $additional = array_merge([booked::class, waitlisted::class], $additionalstatuses);
        $statuses = attendance_state::get_all_attendance_code_with($additional);

        [$asql, $aparams] = $DB->get_in_or_equal($statuses, SQL_PARAMS_NAMED);

        $sql .= " AND ((ss.id IS NOT NULL AND ss.statuscode {$asql}) OR sr.id IS NOT NULL)";
        $params = array_merge($params, $aparams);

        // Building times conflicting sql.
        $timesqls = [];
        $i = 0;

        foreach ($dates as $date) {
            $timesqls[] = "(d.timefinish > :timestart{$i} AND d.timestart < :timefinish{$i})";
            $params["timestart{$i}"] = $date->timestart;
            $params["timefinish{$i}"] = $date->timefinish;
            $i++;
        }

        $sql .= " AND (" . implode(" OR ", $timesqls) . ")";

        // Finally, building the excluded seminar_event sql pharse here.
        if (null !== $excludeseminarevent) {
            $sql .= " AND s.id <> :sessionid";
            $params['sessionid'] = $excludeseminarevent->get_id();
        }

        $records = $DB->get_records_sql($sql, $params);
        if (!$records) {
            return $list;
        }

        foreach ($records as $record) {
            $session = new seminar_session();
            $session->from_record($record);
            $list->add($session);
        }

        return $list;
    }

    /**
     * Given the list of sessions, this factory will try to pre-build the data for $dates, then invoke ::from_user_conflicts with_date
     *
     * @param int                  $userid
     * @param seminar_session_list $sessions
     * @param seminar_event|null   $excludeseminarevent
     * @param array                $additionalstatuses
     *
     * @return seminar_session_list
     */
    public static function from_user_conflicts_with_sessions(int $userid, seminar_session_list $sessions,
                                                             seminar_event $excludeseminarevent = null,
                                                             array $additionalstatuses = []): seminar_session_list {
        $dates = [];

        /** @var seminar_session $session */
        foreach ($sessions as $session) {
            $date = new \stdClass();
            $date->timestart = $session->get_timestart();
            $date->timefinish = $session->get_timefinish();

            $dates[] = $date;
        }

        return self::from_user_conflicts_with_dates($userid, $dates, $excludeseminarevent, $additionalstatuses);
    }
}