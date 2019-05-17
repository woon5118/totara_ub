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

use mod_facetoface\signup\state\declined;
use mod_facetoface\signup\state\user_cancelled;
use mod_facetoface\signup\state\event_cancelled;

defined('MOODLE_INTERNAL') || die();

/**
 * Filter by booking status.
 */
final class booking_filter extends filter {
    const ALL = 0;
    const OPEN = 1;
    const BOOKED = 2;

    /**
     * @var int
     */
    private $value;

    /**
     * @var int
     */
    private $userid;

    /**
     * @param int $value    ALL, OPEN or BOOKED
     */
    public function __construct(int $value = 0) {
        parent::__construct('booking');
        $this->set_value($value);
    }

    /**
     * @param int $value    ALL, OPEN or BOOKED
     * @return booking_filter
     */
    public function set_value(int $value): booking_filter {
        $this->value = $value;
        return $this;
    }

    /**
     * @param integer $userid
     * @return booking_filter
     */
    public function set_userid(int $userid): booking_filter {
        $this->userid = $userid;
        return $this;
    }

    /**
     * @return array
     * @inheritdoc
     */
    public function get_where_and_params(int $time): array {
        // The external use will always have the keyword 'AND' before hand, therefore, it should have something here to returned
        // for the event_time criteria.
        $sql = "(1=1)";
        $params = [];

        if ($this->value == self::OPEN) {
            $sql = '(
                (m.cntdates IS NULL OR :timenow1_bkf < m.mintimestart)
                AND (s.cancelledstatus = 0)
                AND (s.registrationtimestart = 0 OR s.registrationtimestart <= :timenow2_bkf)
                AND (s.registrationtimefinish = 0 OR s.registrationtimefinish >= :timenow3_bkf)
                AND (
                    SELECT COUNT(su.id)
                      FROM {facetoface_signups} su
                      JOIN {facetoface_signups_status} sus ON sus.signupid = su.id
                     WHERE su.archived = 0
                       AND sus.superceded = 0
                       AND sus.statuscode != :stat_decl_bkf
                       AND sus.statuscode != :stat_ucan_bkf
                       AND sus.statuscode != :stat_ecan_bkf
                       AND su.sessionid = s.id
                    ) < s.capacity
                )';
            $params = [
                'timenow1_bkf' => $time,
                'timenow2_bkf' => $time,
                'timenow3_bkf' => $time,
                'stat_decl_bkf' => declined::get_code(),
                'stat_ucan_bkf' => user_cancelled::get_code(),
                'stat_ecan_bkf' => event_cancelled::get_code(),
            ];
        } else if ($this->value == self::BOOKED) {
            global $USER;
            $sql = '(s.cancelledstatus = 0) AND EXISTS (
                SELECT su.sessionid
                  FROM {facetoface_signups} su
                  JOIN {facetoface_signups_status} sus ON sus.signupid = su.id
                 WHERE su.archived = 0
                   AND su.userid = :uid_bkf
                   AND sus.superceded = 0
                   AND sus.statuscode != :stat_decl_bkf
                   AND sus.statuscode != :stat_ucan_bkf
                   AND sus.statuscode != :stat_ecan_bkf
                   AND su.sessionid = s.id
                )';
            $params = [
                'uid_bkf' => $this->userid ?: $USER->id,
                'stat_decl_bkf' => declined::get_code(),
                'stat_ucan_bkf' => user_cancelled::get_code(),
                'stat_ecan_bkf' => event_cancelled::get_code(),
            ];
        }

        return [$sql, $params];
    }
}
