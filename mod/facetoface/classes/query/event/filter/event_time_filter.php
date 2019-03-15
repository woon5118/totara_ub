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
 * @author Kian Nguyen <kian.nguyen@totaralearning.com>
 * @package mod_facetoface
 */

namespace mod_facetoface\query\event\filter;

use mod_facetoface\event_time;

defined('MOODLE_INTERNAL') || die();

/**
 * Filtering event base on the type of time, whether event is UPCOMING, IN-PROGRESS or OVER. This filter will help to build
 * the part of SQL for that checking of criteria.
 */
final class event_time_filter extends filter {
    /**
     * @var int
     */
    private $eventtime;

    /**
     * event_time_filter constructor.
     *
     * @param int $eventtime    The constants defined in class \mod_facetoface\event_time
     */
    public function __construct(int $eventtime = event_time::ALL) {
        parent::__construct('event_time');
        $this->eventtime = $eventtime;
    }

    /**
     * $value is one value defined in event_time's constants.
     * @param int $value
     *
     * @return event_time_filter
     */
    public function set_eventtime(int $value): event_time_filter {
        $this->eventtime = $value;
        return $this;
    }

    /**
     * @return array
     * @inheritdoc
     */
    public function get_where_and_params(): array {
        // The external use will always have the keyword 'AND' before hand, therefore, it should have something here to returned
        // for the event_time criteria.
        $sql = "(1=1)";
        $params = [];

        if ($this->eventtime !== event_time::ALL) {
            $params['timenow'] = time();

            if ($this->eventtime === event_time::UPCOMING) {
                // (wait-listed OR first session_date not started) AND (not cancelled)
                $sql = "(m.cntdates IS NULL OR :timenow < m.mintimestart) AND s.cancelledstatus = 0";
            } else if ($this->eventtime === event_time::INPROGRESS) {
                // (first session_date started AND last session_date not finished) AND (not cancelled)
                $sql = "(m.mintimestart <= :timenow AND :timenow2 < m.maxtimefinish) AND s.cancelledstatus = 0";
                $params['timenow2'] = time();
            } else if ($this->eventtime === event_time::OVER) {
                // (last session_date finished) OR (cancelled)
                $sql = "(m.maxtimefinish  <= :timenow OR s.cancelledstatus = 1)";
            }
        }

        return [$sql, $params];
    }
}