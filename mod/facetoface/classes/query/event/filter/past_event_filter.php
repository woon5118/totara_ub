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

defined('MOODLE_INTERNAL') || die();

/**
 * Filter past events by time period.
 */
final class past_event_filter extends filter {
    /**
     * @var int
     */
    private $timeperiod;

    /**
     * @param integer $timeperiod   The time period in days. 0 or a negative value disables this filter.
     */
    public function __construct(int $timeperiod = 0) {
        parent::__construct('past_event');
        $this->set_value($timeperiod);
    }

    /**
     * @param integer $timeperiod   The time period in days. 0 or a negative value disables this filter.
     * @return past_event_filter
     */
    public function set_value(int $timeperiod = 0): past_event_filter {
        $this->timeperiod = $timeperiod;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function get_where_and_params(int $time): array {
        // The external use will always have the keyword 'AND' before hand, therefore, it should have something here to returned
        // for the event_time criteria.
        $sql = "(1=1)";
        $params = [];

        if ($this->timeperiod > 0) {
            $sql = '(((m.mintimestart IS NULL OR m.maxtimefinish IS NULL) OR (m.mintimestart > :timefrom_pef) OR (m.maxtimefinish > :timenow_pef)) AND s.cancelledstatus = 0)';
            $params = [
                'timefrom_pef' => $time - ($this->timeperiod * DAYSECS),
                'timenow_pef' => $time,
            ];
        }

        return [$sql, $params];
    }
}
