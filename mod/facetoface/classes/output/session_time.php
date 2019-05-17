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
use mod_facetoface\seminar_session;

/**
 * Provide functions to format session times.
 */
class session_time {

    /**
     * Format the session time according to specific timezone.
     *
     * @param int $start
     * @param int $end
     * @param int|string|float|DateTimeZone $timezone
     * @param bool $displaytimezones
     * @return \stdClass
     */
    public static function format(int $start, int $end, $timezone, bool $displaytimezones = null): \stdClass {
        if ($displaytimezones === null) {
            $displaytimezones = (bool)(int)get_config(null, 'facetoface_displaysessiontimezones');
        }

        if (empty($timezone) || (int)$timezone == 99 or !$displaytimezones) {
            $timezone = \core_date::get_user_timezone();
        } else {
            $timezone = \core_date::normalise_timezone($timezone);
        }

        $formattedsession = new \stdClass();
        $formattedsession->startdate = userdate($start, get_string('strftimedate', 'langconfig'), $timezone);
        $formattedsession->starttime = userdate($start, get_string('strftimetime', 'langconfig'), $timezone);
        $formattedsession->enddate = userdate($end, get_string('strftimedate', 'langconfig'), $timezone);
        $formattedsession->endtime = userdate($end, get_string('strftimetime', 'langconfig'), $timezone);
        if (!$displaytimezones) {
            $formattedsession->timezone = '';
        } else {
            $formattedsession->timezone = \core_date::get_localised_timezone($timezone);
        }
        return $formattedsession;
    }

    public static function to_string(int $start, int $end, string $timezone): string {

        $sessionobj = static::format($start, $end, $timezone);

        // No timezone to display.
        if (empty($sessionobj->timezone)) {
            if ($sessionobj->startdate == $sessionobj->enddate) {
                $timestring = get_string('sessionstartdateandtimewithouttimezone', 'mod_facetoface', $sessionobj);
            } else {
                $timestring = get_string('sessionstartfinishdateandtimewithouttimezone', 'mod_facetoface', $sessionobj);
            }
        } else {
            if ($sessionobj->startdate == $sessionobj->enddate) {
                $timestring = get_string('sessionstartdateandtime', 'mod_facetoface', $sessionobj);
            } else {
                $timestring = get_string('sessionstartfinishdateandtime', 'mod_facetoface', $sessionobj);
            }
        }
        return $timestring;
    }

    public static function signup_period($startdate, $finishdate, $timezone = 99): string {

        $returntext    = '';
        $startdatestr  = null;
        $finishdatestr = null;
        $displaytimezones = (bool)(int)get_config(null, 'facetoface_displaysessiontimezones');
        if (empty($timezone) || (int)$timezone == 99 || !$displaytimezones) {
            $targettz = \core_date::get_user_timezone();
        } else {
            $targettz = \core_date::normalise_timezone($timezone);
        }

        if ($startdate && is_numeric($startdate)) {
            $startdatestr = userdate($startdate, get_string('strftimedatetime', 'langconfig'), $targettz);
        }
        if ($finishdate && is_numeric($finishdate)) {
            $finishdatestr = userdate($finishdate, get_string('strftimedatetime', 'langconfig'), $targettz);
        }

        if ($startdatestr && $finishdatestr) {
            $returntext = get_string('signupstartend', 'mod_facetoface', ['startdate' => $startdatestr, 'enddate' => $finishdatestr]);
        } else if ($startdatestr) {
            $returntext = get_string('signupstartsonly', 'mod_facetoface', ['startdate' => $startdatestr]);
        } else if ($finishdatestr) {
            $returntext = get_string('signupendsonly', 'mod_facetoface', ['enddate' => $finishdatestr]);
        }

        if (!empty($returntext) && $displaytimezones) {
            $returntext .= ' ' . \core_date::get_localised_timezone($targettz);
        }
        return $returntext;
    }

    /**
     * Format the session time as HTML.
     *
     * @param string|integer $startdate
     * @param string|integer $finishdate
     * @param integer|string|float|DateTimeZone $timezone
     * @param boolean|null $displaytimezones
     * @return string
     */
    public static function to_html(int $timestart, int $timefinish, $timezone = 99, bool $displaytimezones = null): string {
        $sessionobj = static::format($timestart, $timefinish, $timezone, $displaytimezones);

        if ($timestart && $timefinish) {
            if ($sessionobj->startdate == $sessionobj->enddate) {
                // <startdate>, <starttime> - <finishtime> [Timezone: <timezone>]
                $html = get_string('sessionstartdateandtimewithouttimezone', 'mod_facetoface', [
                    'startdate' => self::wrap_eventtime($sessionobj->startdate),
                    'starttime' => self::wrap_eventtime($sessionobj->starttime),
                    'endtime' => self::wrap_eventtime($sessionobj->endtime),
                ]);
            } else {
                // <startdate>, <starttime> - <finishdate>, <finishtime> [Timezone: <timezone>]
                $html = get_string('sessionstartfinishdateandtimewithouttimezone', 'mod_facetoface', [
                    'startdate' => self::wrap_eventtime($sessionobj->startdate),
                    'starttime' => self::wrap_eventtime($sessionobj->starttime),
                    'enddate' => self::wrap_eventtime($sessionobj->enddate),
                    'endtime' => self::wrap_eventtime($sessionobj->endtime),
                ]);
            }
        } else if ($timestart) {
            // After <startdate>, <starttime> [Timezone: <timezone>]
            $html = get_string('signupstartdateandtimeonlywithouttimezone', 'mod_facetoface', [
                'startdate' => self::wrap_eventtime($sessionobj->startdate),
                'starttime' => self::wrap_eventtime($sessionobj->starttime),
            ]);
        } else if ($timefinish) {
            // Before <enddate>, <endtime> [Timezone: <timezone>]
            $html = get_string('signupenddateandtimeonlywithouttimezone', 'mod_facetoface', [
                'enddate' => self::wrap_eventtime($sessionobj->enddate),
                'endtime' => self::wrap_eventtime($sessionobj->endtime),
            ]);
        } else {
            return '';
        }

        if ($sessionobj->timezone) {
            $timezonestring = clean_string(get_string('timezoneformat', 'mod_facetoface', $sessionobj->timezone));
            $html .= \html_writer::tag('span', $timezonestring, ['class' => 'mod_facetoface__sessionlist__timezone']);
        }
        return $html;
    }

    /**
     * @param string $time
     * @return string
     */
    private static function wrap_eventtime(string $time): string {
        return \html_writer::tag('time', clean_string($time), ['class' => 'mod_facetoface__sessionlist__eventtime']);
    }
}
