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
 * @author Tatsuhiro Kirihara <tatsuhiro.kirihara@totaralearning.com>
 * @package mod_facetoface
 */

namespace mod_facetoface\query\event\filter;

use mod_facetoface\event_time;

defined('MOODLE_INTERNAL') || die();

/**
 * Filtering event base on the type of time, whether event is UPCOMING, IN-PROGRESS or OVER. This filter will help to build
 * the part of SQL for that checking of criteria.
 */
final class event_times_filter extends filter {
    /**
     * @var int[]
     */
    private $eventtimes;

    /**
     * @var integer
     */
    private static $index = 0;

    /**
     * event_time_filter constructor.
     *
     * @param int[] $values    The constants defined in class \mod_facetoface\event_time
     */
    public function __construct(array $values) {
        parent::__construct('event_times');
        $this->set_eventtimes($values);
    }

    /**
     * $value is one or more values defined in event_time's constants.
     * @param int[] $values
     *
     * @return event_time_filter
     */
    public function set_eventtimes(array $values): event_times_filter {
        $values = array_unique($values);
        if (count($values) === 0 || in_array(event_time::ALL, $values)) {
            $this->eventtimes = [];
        } else {
            $this->eventtimes = $values;
        }
        return $this;
    }

    /**
     * @return string
     */
    private static function new_param(): string {
        $i = ++self::$index;
        return "timenow{$i}_etf";
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

        if (!empty($this->eventtimes)) {
            $sql = '';
            foreach ($this->eventtimes as $eventtime) {
                $param1 = self::new_param();
                $params[$param1] = $time;

                if ($sql !== '') {
                    $sql .= ' OR ';
                }
                if ($eventtime === event_time::UPCOMING) {
                    // (wait-listed OR first session_date not started) AND (not cancelled)
                    $sql .= "((m.cntdates IS NULL OR :{$param1} < m.mintimestart) AND s.cancelledstatus = 0)";
                } else if ($eventtime === event_time::INPROGRESS) {
                    // (first session_date started AND last session_date not finished) AND (not cancelled)
                    $param2 = self::new_param();
                    $sql .= "((m.mintimestart <= :{$param1} AND :{$param2} < m.maxtimefinish) AND s.cancelledstatus = 0)";
                    $params[$param2] = $time;
                } else if ($eventtime === event_time::OVER) {
                    // (last session_date finished) OR (cancelled)
                    $sql .= "(m.maxtimefinish  <= :{$param1} OR s.cancelledstatus = 1)";
                }
            }
            $sql = '(' . $sql . ')';
        }

        return [$sql, $params];
    }
}
