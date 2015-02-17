<?php
/*
 * This file is part of Totara LMS
 *
 * Copyright (C) 2015 onwards Totara Learning Solutions LTD
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
 * @author Petr Skoda <petr.skoda@totaralms.com>
 * @package totara_core
 */

defined('MOODLE_INTERNAL') || die();

class totara_core_timezones_testcase extends advanced_testcase {
    public function test_get_list_of_timezones() {
        global $DB;

        // Totara has extra parameter and it lists only timezones form PHP.

        $list = get_list_of_timezones();
        foreach ($list as $tzkey => $name) {
            if ($tzkey === 'UTC') {
                continue;
            }
            $this->assertContains('/', $tzkey);
        }
        $this->assertArrayHasKey('UTC', $list);
        $this->assertArrayHasKey('Europe/London', $list);
        $this->assertArrayHasKey('Pacific/Auckland', $list);
        $this->assertArrayNotHasKey('99', $list);

        $list = get_list_of_timezones(99);
        $this->assertArrayHasKey('UTC', $list);
        $this->assertArrayHasKey('99', $list);

        $list = get_list_of_timezones('UTC');
        $this->assertArrayHasKey('UTC', $list);
        $this->assertArrayNotHasKey('99', $list);

        $list = get_list_of_timezones('Abc/Def');
        $this->assertArrayHasKey('UTC', $list);
        $this->assertArrayNotHasKey('99', $list);
        $this->assertArrayHasKey('Abc/Def', $list);

        // Makes sure all possible zones returned by PHP in Totara have entries in Moodle timezone file.
        $list = get_list_of_timezones();
        $zones = $DB->get_records('timezone');
        $moodlezones = array();
        foreach ($zones as $zone) {
            $moodlezones[$zone->name] = true;
        }
        foreach ($list as $tzkey => $name) {
            $this->assertArrayHasKey($tzkey, $moodlezones);
        }
    }

    public function test_get_user_timezone() {
        global $DB, $CFG, $USER;

        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        // All set to something.

        date_default_timezone_set('Pacific/Auckland');
        $CFG->timezone = 'Pacific/Auckland';
        $USER->timezone = 'Europe/Prague';

        $tz = get_user_timezone();
        $this->assertSame('Europe/Prague', $tz);

        $tz = get_user_timezone(99);
        $this->assertSame('Europe/Prague', $tz);
        $tz = get_user_timezone('99');
        $this->assertSame('Europe/Prague', $tz);

        $tz = get_user_timezone('Europe/Berlin');
        $this->assertSame('Europe/Berlin', $tz);

        // User timezone not set.

        date_default_timezone_set('Pacific/Auckland');
        $CFG->timezone = 'Pacific/Auckland';
        $USER->timezone = '99';

        $tz = get_user_timezone();
        $this->assertSame('Pacific/Auckland', $tz);

        $tz = get_user_timezone(99);
        $this->assertSame('Pacific/Auckland', $tz);
        $tz = get_user_timezone('99');
        $this->assertSame('Pacific/Auckland', $tz);

        $tz = get_user_timezone('Europe/Berlin');
        $this->assertSame('Europe/Berlin', $tz);

        // Server timezone not set.

        date_default_timezone_set('Pacific/Auckland');
        $CFG->timezone = '99';
        $USER->timezone = 'Europe/Prague';

        $tz = get_user_timezone();
        $this->assertSame('Europe/Prague', $tz);

        $tz = get_user_timezone(99);
        $this->assertSame('Europe/Prague', $tz);
        $tz = get_user_timezone('99');
        $this->assertSame('Europe/Prague', $tz);

        $tz = get_user_timezone('Europe/Berlin');
        $this->assertSame('Europe/Berlin', $tz);

        // Server and user timezone not set.

        date_default_timezone_set('Pacific/Auckland');
        $CFG->timezone = '99';
        $USER->timezone = '99';

        $tz = get_user_timezone();
        $this->assertSame(99.0, $tz); // This is not nice.

        $tz = get_user_timezone(99);
        $this->assertSame(99.0, $tz); // This is not nice.
        $tz = get_user_timezone('99');
        $this->assertSame(99.0, $tz); // This is not nice.

        $tz = get_user_timezone('Europe/Berlin');
        $this->assertSame('Europe/Berlin', $tz);
    }

    public function test_get_timezone_offset() {
        $this->markTestSkipped('get_timezone_offset() is completely wrong, do not use it!!!');
    }

    public function test_get_user_timezone_offset() {
        $this->markTestSkipped('get_user_timezone_offset() is completely wrong, do not use it!!!');
    }

    public function test_make_timestamp() {
        global $CFG;

        $this->resetAfterTest();

        // There are quite a lot of problems, let's pick some less problematic zones for now.
        //$timezones = get_list_of_timezones('99');
        //$timezones = array_keys($timezones);
        $timezones = array('UTC', 'Europe/Prague', 'Europe/London', 'Australia/Perth', 'Pacific/Auckland', '99');

        $dates = array(
            array(2, 1, 0, 40, 40),
            array(4, 3, 0, 30, 22),
            array(9, 5, 0, 20, 19),
            array(11, 28, 0, 10, 45),
        );
        $years = array(1999, 2009, 2014, 2018);

        date_default_timezone_set('Pacific/Auckland');
        $CFG->timezone = 'Pacific/Auckland';
        foreach ($timezones as $tz) {
            foreach ($years as $year) {
                foreach ($dates as $date) {
                    $result = make_timestamp($year, $date[0], $date[1], $date[2], $date[3], $date[4], $tz, true);
                    $expected = new DateTime('now', new DateTimeZone(($tz == 99 ? 'Pacific/Auckland' : $tz)));
                    $expected->setDate($year, $date[0], $date[1]);
                    $expected->setTime($date[2], $date[3], $date[4]);
                    $this->assertSame($expected->getTimestamp(), $result, 'Incorrect result for data ' . $expected->format("D, d M Y H:i:s O") . ' ' . $tz);
                }
            }
        }

        date_default_timezone_set('Pacific/Auckland');
        $CFG->timezone = '99';
        foreach ($timezones as $tz) {
            foreach ($years as $year) {
                foreach ($dates as $date) {
                    $result = make_timestamp($year, $date[0], $date[1], $date[2], $date[3], $date[4], $tz, true);
                    $expected = new DateTime('now', new DateTimeZone(($tz == 99 ? 'Pacific/Auckland' : $tz)));
                    $expected->setDate($year, $date[0], $date[1]);
                    $expected->setTime($date[2], $date[3], $date[4]);
                    $this->assertSame($expected->getTimestamp(), $result, 'Incorrect result for data ' . $expected->format("D, d M Y H:i:s O") . ' ' . $tz);
                }
            }
        }
    }

    public function test_usergetdate() {
        global $CFG;

        $this->resetAfterTest();

        // There are quite a lot of problems, let's pick some less problematic zones for now.
        //$timezones = get_list_of_timezones('99');
        //$timezones = array_keys($timezones);
        $timezones = array('UTC', 'Europe/Prague', 'Europe/London', 'Australia/Perth', 'Pacific/Auckland', '99');

        $dates = array(
            array(2, 1, 0, 40, 40),
            array(4, 3, 0, 30, 22),
            array(9, 5, 0, 20, 19),
            array(11, 28, 0, 10, 45),
        );
        $years = array(1999, 2009, 2014, 2018);

        date_default_timezone_set('Pacific/Auckland');
        $CFG->timezone = 'Pacific/Auckland';
        foreach ($timezones as $tz) {
            foreach ($years as $year) {
                foreach ($dates as $date) {
                    $expected = new DateTime('now', new DateTimeZone(($tz == 99 ? 'Pacific/Auckland' : $tz)));
                    $expected->setDate($year, $date[0], $date[1]);
                    $expected->setTime($date[2], $date[3], $date[4]);
                    $result = usergetdate($expected->getTimestamp(), $tz);
                    $ex = array(
                        'seconds' => $date[4],
                        'minutes' => $date[3],
                        'hours' => $date[2],
                        'mday' => $date[1],
                        'wday' => (int)$expected->format('w'),
                        'mon' => $date[0],
                        'year' => $year,
                        'yday' => (int)$expected->format('z'),
                        'weekday' => $expected->format('l'),
                        'month' => $expected->format('F'),
                    );
                    $this->assertSame($ex, $result, 'Incorrect result for data ' . $expected->format("D, d M Y H:i:s O") . ' ' . $tz);
                }
            }
        }

        date_default_timezone_set('Pacific/Auckland');
        $CFG->timezone = '99';
        foreach ($timezones as $tz) {
            foreach ($years as $year) {
                foreach ($dates as $date) {
                    $expected = new DateTime('now', new DateTimeZone(($tz == 99 ? 'Pacific/Auckland' : $tz)));
                    $expected->setDate($year, $date[0], $date[1]);
                    $expected->setTime($date[2], $date[3], $date[4]);
                    $result = usergetdate($expected->getTimestamp(), $tz);
                    if ($tz == 99) {
                        unset($result[0]); // What the hell?
                    }
                    $ex = array(
                        'seconds' => $date[4],
                        'minutes' => $date[3],
                        'hours' => $date[2],
                        'mday' => $date[1],
                        'wday' => (int)$expected->format('w'),
                        'mon' => $date[0],
                        'year' => $year,
                        'yday' => (int)$expected->format('z'),
                        'weekday' => $expected->format('l'),
                        'month' => $expected->format('F'),
                    );
                    $this->assertSame($ex, $result, 'Incorrect result for data ' . $expected->format("D, d M Y H:i:s O") . ' ' . $tz);
                }
            }
        }
    }
}
