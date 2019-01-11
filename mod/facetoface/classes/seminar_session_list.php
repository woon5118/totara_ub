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

defined('MOODLE_INTERNAL') || die();

use stdClass;

/**
 * Class seminar_session_list represents Seminar event sessions date list
 */
final class seminar_session_list implements \Iterator {

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
        foreach ($sessionrecords as $sessionrecords) {
            $session = new seminar_session();
            $list->add($session->from_record($sessionrecords));
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
        $a = new stdClass();

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
     * Sort the list of items based on finish time.
     *
     * The earliest finish time will be first, latest finish time will be last.
     *
     * @param seminar_session[] $items
     */
    private static function sort(array &$items): void {
        usort($items, function ($a, $b) {
            return $a->get_timefinish() > $b->get_timefinish();
        });
    }
}
