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
use mod_facetoface\signup\condition\event_taking_attendance;
use stdClass;

defined('MOODLE_INTERNAL') || die();

/**
 * Class seminar_session represents Seminar event session dates (aka event sessions)
 */
final class seminar_session implements seminar_iterator_item {

    use traits\crud_mapper;

    /**
     * @var int {facetoface_sessions_dates}.id
     */
    private $id = 0;
    /**
     * @var int {facetoface_sessions_dates}.sessionid
     */
    private $sessionid = 0;
    /**
     * @var string {facetoface_sessions_dates}.sessiontimezone
     */
    private $sessiontimezone = "99";
    /**
     * @var int {facetoface_sessions_dates}.roomid
     */
    private $roomid = 0;
    /**
     * @var int {facetoface_sessions_dates}.timestart
     */
    private $timestart = 0;
    /**
     * @var int {facetoface_sessions_dates}.timefinish
     */
    private $timefinish = 0;
    /**
     * @var string facetoface_sessions_dates table name
     */
    const DBTABLE = 'facetoface_sessions_dates';
    /**
     * Related to seminar event here
     * @var seminar_event|null
     */
    private $seminarevent = null;

    /**
     * Session constructor.
     * @param int $id {facetoface_sessions_dates}.id If 0 - new Session will be created
     */
    public function __construct(int $id = 0) {

        $this->id = $id;
        $this->load();
    }

    /**
     * Load seminar event session dates data from DB
     *
     * @return seminar session this
     */
    public function load(): seminar_session {

        return $this->crud_load();
    }

    /**
     * Create/update {facetoface_sessions_dates}.record
     */
    public function save(): void {

        $this->crud_save();
    }

    /**
     * Map data object to class instance.
     *
     * @param \stdClass $object
     * @return seminar_session
     */
    public function from_record(\stdClass $object): seminar_session {

        return $this->map_object($object);
    }

    /**
     * Remove seminar event session dates from database
     */
    public function delete(): void {
        global $DB;

        $DB->delete_records(self::DBTABLE, ['id' => $this->id]);

        // Re-load instance with default values.
        $this->map_object((object)get_object_vars(new self()));
    }

    /**
     * Get session date id
     *
     * @return int
     */
    public function get_id(): int {
        return (int)$this->id;
    }

    /**
     * Get seminar event id (known as sessionid)
     *
     * @return int
     */
    public function get_sessionid(): int {
        return (int)$this->sessionid;
    }
    /**
     * Set seminar event id (known as sessionid)
     *
     * @param int $sessionid
     */
    public function set_sessionid(int $sessionid): seminar_session {
        $this->sessionid = $sessionid;
        return $this;
    }

    /**
     * Get timezone for this session date
     * @return int
     */
    public function get_sessiontimezone(): string {
        return (string)$this->sessiontimezone;
    }
    /**
     * Set timezone for this session date
     * @param int $sessiontimezone
     */
    public function set_sessiontimezone(string $sessiontimezone): seminar_session {
        $this->sessiontimezone = $sessiontimezone;
        return $this;
    }

    /**
     * Get room id for this session date
     *
     * @return int
     */
    public function get_roomid(): int {
        return (int)$this->roomid;
    }
    /**
     * Set room id for this session date
     *
     * @param int $roomid
     */
    public function set_roomid(int $roomid): seminar_session {
        $this->roomid = $roomid;
        return $this;
    }

    /**
     * Get start time for this session date
     *
     * @return int
     */
    public function get_timestart(): int {
        return (int)$this->timestart;
    }
    /**
     * Set start time for this session date
     *
     * @param int $timestart
     */
    public function set_timestart(int $timestart): seminar_session {
        $this->timestart = $timestart;
        return $this;
    }

    /**
     * Get end time for this session date
     *
     * @return int
     */
    public function get_timefinish(): int {
        return (int)$this->timefinish;
    }
    /**
     * Set end time for this session date
     *
     * @param int $timefinish
     */
    public function set_timefinish(int $timefinish): seminar_session {
        $this->timefinish = $timefinish;
        return $this;
    }

    /**
     * Is session date over, in the past?
     *
     * @param int $time
     * @return bool
     */
    public function is_over(int $time = 0): bool {
        if (empty($this->timefinish)) {
            return false;
        }

        if (0 >= $time) {
            $time = time();
        }

        // If the time finish is already behind the current time. Then this session is
        // obviously completed.
        return $this->timefinish < $time;
    }

    /**
     * Is session date upcoming, in the future?
     *
     * @param int $time
     * @return bool
     */
    public function is_upcoming(int $time = 0): bool {
        if (empty($this->timestart)) {
            return false;
        }

        if (0 >= $time) {
            $time = time();
        }

        // If the current time is behind the timestart of the session, then this session
        // will start in the future, actually.
        return $this->timestart > $time;
    }

    /**
     * Checking whether the session has started or not.
     *
     * @param int $time
     * @return bool
     */
    public function is_start(int $time = 0): bool {
        if (empty($this->timestart)) {
            return false;
        }

        if (0 >= $time) {
            $time = time();
        }

        // If the current time exceed the timestart, which means that the session
        // has already started
        return $this->timestart < $time;
    }

    /**
     * Returning the time description of seminar's session.
     *
     * @param string $fullformatstring name of string to use for date and time
     * @param string $timeformatstring name of string to use for just time
     * @return string
     */
    public function get_time_description(string $fullformatstring = 'strftimerecentfull', string $timeformatstring = 'strftimetime'): string {
        if (empty($this->timestart) || empty($this->timefinish)) {
            return '';
        }

        $a = new stdClass();
        $fullformat = get_string($fullformatstring, 'langconfig');
        $a->start = userdate($this->timestart, $fullformat);

        $startdate = date("Y-m-d", $this->timestart);
        $finishdate = date("Y-m-d", $this->timefinish);

        if ($startdate == $finishdate) {
            // If it is in a same date, then we display Date month Year Hour -> end hour
            $timeformat = get_string($timeformatstring, 'langconfig');
            $a->end = userdate($this->timefinish, $timeformat);
        } else {
            $a->end = date_format_string($this->timefinish, $fullformat);
        }

        return get_string('sessiontimedescription', 'mod_facetoface', $a);
    }

    /**
     * Get the seminar event that this session date belongs to
     *
     * @return seminar_event
     */
    public function get_seminar_event(): seminar_event {
        if (null == $this->seminarevent) {
            $this->seminarevent = new seminar_event($this->sessionid);
        }

        return $this->seminarevent;
    }

    /**
     * Checking whether the session is open for taking attendance or not.
     *
     * @param int $time
     * @return bool
     */
    public function is_attendance_open(int $time = 0): bool {
        $seminarevent = $this->get_seminar_event();
        $seminar = $seminarevent->get_seminar();
        if (!$seminar->get_sessionattendance() || (0 != $seminarevent->get_cancelledstatus())) {
            // Attendance tracking is not enabled, or when the seminar event is cancelled.
            return false;
        }

        if (0 >= $time) {
            $time = time();
        }

        $attendancetime = $seminar->get_attendancetime();
        switch ($attendancetime) {
            case seminar::ATTENDANCE_TIME_ANY:
                if ($seminarevent->get_sessions()->is_empty()) {
                    // Wait listed event should not open attendance for session.
                    // This seems to be a nonsense check here, as it is in a session level and it
                    // should not be checking for wait-listed event, because this made session to
                    // check for another session, inside of this session.
                    // However, there is a case where the session/event was not saved previously
                    // and developer somehow use this checking method for their logic.
                    return false;
                } else if (empty($this->timestart) || empty($this->timefinish)) {
                    // A scenario where developer accidently did not provide timestart or timefinish
                    // previously, and either saved it to the database or using the logic check
                    // straight away. Despite of attendance time is set to any time, but it needs
                    // times to be checked against any time.
                    return false;
                }

                return true;
            case seminar::ATTENDANCE_TIME_START:
                // For attendance time start, it need to allow actor to mark attendnace number of
                // minutes before the actual start time.
                $time += event_taking_attendance::UNLOCKED_SECS_PRIOR_TO_START;
                return $this->is_start($time);
            case seminar::ATTENDANCE_TIME_END:
                return $this->is_over($time);
        }

        // Last fall back here, and it should probably be an error.
        debugging(
            "Unable to find the attendance time: {$attendancetime}",
            DEBUG_DEVELOPER
        );

        return false;
    }
}
