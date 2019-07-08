<?php
/*
 * This file is part of Totara LMS
 *
 * Copyright (C) 2019 onwards Totara Learning Solutions LTD
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
 * @package mod_facetoface
 */

namespace mod_facetoface\query\event\filter;

use mod_facetoface\signup\state\attendance_state;
use mod_facetoface\signup\state\booked;
use mod_facetoface\seminar;
use mod_facetoface\signup\condition\event_taking_attendance;

defined('MOODLE_INTERNAL') || die();

/**
 * Filter by attendance status.
 */
final class advanced_filter extends filter {
    const ALL = 0;
    const ATTENDANCE_OPEN = 1;
    const ATTENDANCE_SAVED = 2;

    /**
     * @var int
     */
    private $value;

    /**
     * @param int $value    ALL, OPEN or SAVED
     */
    public function __construct(int $value = 0) {
        parent::__construct('attendance');
        $this->set_value($value);
    }

    /**
     * @param int $value    ALL, OPEN or SAVED
     * @return advanced_filter
     */
    public function set_value(int $value): advanced_filter {
        $this->value = $value;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function get_where_and_params(int $time): array {
        global $DB;
        /** @var \moodle_database $DB */

        // The external use will always have the keyword 'AND' before hand, therefore, it should have something here to returned
        // for the event_time criteria.
        $sql = "(1=1)";
        $params = [];

        if ($this->value != self::ATTENDANCE_OPEN && $this->value != self::ATTENDANCE_SAVED) {
            return [ $sql, $params ];
        }

        $statuses = attendance_state::get_all_attendance_code_with([booked::class]);
        [$statsql, $params] = $DB->get_in_or_equal($statuses, SQL_PARAMS_NAMED, 'af_su');
        $sql = "EXISTS (
            SELECT su.id
            FROM {facetoface_signups} su
            JOIN {facetoface_signups_status} ss ON su.id = ss.signupid
            JOIN {user} u ON u.id = su.userid
            WHERE su.sessionid = s.id
            AND su.archived = 0
            AND ss.superceded = 0
            AND ss.statuscode {$statsql}
            AND u.deleted = 0
        )";

        $sql_at_any = 'f2f.attendancetime = :at_any_af AND EXISTS (
            SELECT sd.id
            FROM {facetoface_sessions_dates} sd
            WHERE sd.sessionid = s.id
        )';
        $sql_at_fstart = 'f2f.attendancetime = :at_fstart_af AND m.mintimestart <= :timeopen1_af';
        $sql_at_lend = 'f2f.attendancetime = :at_lend_af AND m.mintimefinish < :timenow_af';
        $sql_at_lstart = 'f2f.attendancetime = :at_lstart_af AND m.maxtimestart <= :timeopen2_af';

        $sql .= " AND EXISTS (
            SELECT f2f.id
            FROM {facetoface} f2f
            WHERE f2f.id = s.facetoface
            AND f2f.sessionattendance != 0
            AND s.cancelledstatus = 0
            AND (({$sql_at_any}) OR ({$sql_at_fstart}) OR ({$sql_at_lend}) OR ({$sql_at_lstart}))
        )";
        $params['at_any_af'] = seminar::EVENT_ATTENDANCE_UNRESTRICTED;
        $params['at_fstart_af'] = seminar::EVENT_ATTENDANCE_FIRST_SESSION_START;
        $params['at_lend_af'] = seminar::EVENT_ATTENDANCE_LAST_SESSION_END;
        $params['at_lstart_af'] = seminar::EVENT_ATTENDANCE_LAST_SESSION_START;
        $params['timeopen1_af'] = $time + event_taking_attendance::UNLOCKED_SECS_PRIOR_TO_START;
        $params['timeopen2_af'] = $time + event_taking_attendance::UNLOCKED_SECS_PRIOR_TO_START;
        $params['timenow_af'] = $time;

        if ($this->value == self::ATTENDANCE_OPEN) {
            $existence = 'EXISTS';
        } else if ($this->value == self::ATTENDANCE_SAVED) {
            $existence = 'NOT EXISTS';
        } else {
            error_log("Unknown attendance filter value: {$this->value}");
            return ['('.$sql.')', $params];
        }
        $sql .= " AND $existence (
            SELECT su.id
            FROM {facetoface_signups} su
            LEFT JOIN {facetoface_signups_dates_status} sds ON sds.signupid = su.id
            WHERE su.sessionid = s.id
            AND su.archived = 0
            AND (sds.attendancecode IS NULL OR (sds.attendancecode = 0 OR sds.attendancecode = :bookingcode_af))
            AND (COALESCE(sds.superceded, 0) = 0)
        )";
        $params['bookingcode_af'] = booked::get_code();

        $sql = '('.$sql.')';

        return [$sql, $params];
    }
}
