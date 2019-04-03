<?php
/*
 * This file is part of Totara Learn
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
 * @author Johannes Cilliers <johannes.cilliers@totaralearning.com>
 * @package mod_facetoface
 */

namespace mod_facetoface\output;

defined('MOODLE_INTERNAL') || die();

use core\output\template;

/**
 * Class session_time
 *
 */
class session_time extends template {

    /**
     * Format the session time according to specific timezone.
     *
     * @param int $start
     * @param int $end
     * @param string $timezone
     * @return \stdClass
     */
    public static function format(int $start, int $end, string $timezone) : \stdClass {
        $displaytimezones = get_config(null, 'facetoface_displaysessiontimezones');

        if (empty($timezone) or empty($displaytimezones)) {
            $timezone = \core_date::get_user_timezone();
        } else {
            $timezone = \core_date::get_user_timezone($timezone);
        }

        $formattedsession = new \stdClass();
        $formattedsession->startdate = userdate($start, get_string('strftimedate', 'langconfig'), $timezone);
        $formattedsession->starttime = userdate($start, get_string('strftimetime', 'langconfig'), $timezone);
        $formattedsession->enddate = userdate($end, get_string('strftimedate', 'langconfig'), $timezone);
        $formattedsession->endtime = userdate($end, get_string('strftimetime', 'langconfig'), $timezone);
        if (empty($displaytimezones)) {
            $formattedsession->timezone = '';
        } else {
            $formattedsession->timezone = \core_date::get_localised_timezone($timezone);
        }
        return $formattedsession;
    }
}