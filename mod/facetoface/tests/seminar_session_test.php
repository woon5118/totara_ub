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
 * @author Kian Nguyen <kian.nguyen@totaralearning.com>
 * @package mod_facetoface
 */

defined('MOODLE_INTERNAL') || die();

use mod_facetoface\{seminar_event, seminar, seminar_session};

class mod_facetoface_seminar_session_testcase extends advanced_testcase {
    /**
     * Create seminar_event with default setting of seminar and event itself
     * @return seminar_event
     */
    private function create_event(): seminar_event {
        $gen = $this->getDataGenerator();
        $course = $gen->create_course();

        $f2f = new seminar();
        $f2f->set_course($course->id)->save();

        $event = new seminar_event();
        $event->set_facetoface($f2f->get_id());
        $event->save();

        return $event;
    }

    /**
     * Data for session_status test suite.
     * @return array
     */
    public function provide_session_status_data(): array {
        $time = time();
        return [
            [
                ['start' => $time + 3600, 'finish' => $time + (3600 * 2)],
                $time,
                ['upcoming' => true, 'over' => false]
            ],
            [
                ['start' => $time + 3600, 'finish' => $time + (3600 * 3)],
                $time + (3600 * 5),
                ['upcoming' => false, 'over' => true]
            ],
            [
                ['start' => $time + 3600, 'finish' => $time + (3600 * 3)],
                $time + (3600 * 2),
                ['upcoming' => false , 'over' => false]
            ]
        ];
    }

    /**
     * Test suite of checking the session status base on: created time check against the current-time
     *
     * @dataProvider provide_session_status_data
     * @param array $time
     * @param int $currenttime
     * @param array $result
     */
    public function test_check_session_status(array $time, int $currenttime, array $result): void {
        $this->resetAfterTest();
        $event = $this->create_event();

        $session = new seminar_session();
        $session->set_sessionid($event->get_id());
        $session->set_timestart($time['start']);
        $session->set_timefinish($time['finish']);
        $session->save();

        $this->assertEquals($result['upcoming'], $session->is_upcoming($currenttime));
        $this->assertEquals($result['over'], $session->is_over($currenttime));
    }

    /**
     * Data for checking attendance_open test suite.
     * @return array
     */
    public function provide_attendance_status_data(): array {
        $time = time();
        return [
            [
                ['start' => $time + 3600, 'finish' => $time + (3600 * 3)],
                $time,
                ['sessionattendance' => 1, 'attendancetime' => seminar::ATTENDANCE_TIME_ANY],
                true,
            ],
            [
                ['start' => $time + 3600, 'finish' => $time + (3600 * 3)],
                $time,
                ['sessionattendance' => 1, 'attendancetime' => seminar::ATTENDANCE_TIME_END],
                false
            ],
            [
                ['start' => $time + 295, 'finish' => $time + (3600 * 3)],
                $time,
                ['sessionattendance' => 1, 'attendancetime' => seminar::ATTENDANCE_TIME_START],
                true
            ],
            [
                ['start' => $time + 3600, 'finish' => $time + (3600 * 3)],
                $time + (3600 * 4),
                ['sessionattendance' => 1, 'attendancetime' => seminar::ATTENDANCE_TIME_END],
                true
            ],
            [
                ['start' => $time + 3600, 'finish' => $time + (3600 * 3)],
                $time,
                ['sessionattendance' => 1, 'attendancetime' => seminar::ATTENDANCE_TIME_END],
                false
            ],
            [
                [],
                $time,
                ['sessionattendance' => 1, 'attendancetime' => seminar::ATTENDANCE_TIME_START],
                false
            ],
            [
                [],
                $time,
                ['sessionattendance' => 1, 'attendancetime' => seminar::ATTENDANCE_TIME_ANY],
                false
            ],
            [
                [],
                $time,
                ['sessionattendance' => 1, 'attendancetime' => seminar::ATTENDANCE_TIME_END],
                false
            ],
            [
                ['start' => $time + 3600, 'finish' => $time + (3600 * 2)],
                $time,
                ['sessionattendance' => 0, 'attendancetime' => seminar::ATTENDANCE_TIME_END],
                false
            ],
            [
                ['start' => $time + 3600, 'finish' => $time + (3600 * 2)],
                $time,
                ['sessionattendance' => 0, 'attendancetime' => seminar::ATTENDANCE_TIME_END],
                false
            ],
            [
                ['start' => $time + 3600, 'finish' => $time + (3600 * 2)],
                $time,
                ['sessionattendance' => 0, 'attendancetime' => seminar::ATTENDANCE_TIME_START],
                false
            ],
            [
                ['start' => $time + 3600, 'finish' => $time + (3600 * 2)],
                $time,
                ['sessionattendance' => 0, 'attendancetime' => seminar::ATTENDANCE_TIME_ANY],
                false
            ],
        ];
    }

    /**
     * Test suite to check the logic of open attendance: sessions time against current time,
     * and also checking against seminar->settings.
     *
     * @dataProvider provide_attendance_status_data
     * @param array $time
     * @param int $currenttime
     * @param bool $result
     * @param array $seminarsetting
     */
    public function test_is_attendance_open(array $time, int $currenttime, array $seminarsetting,
                                            bool $result): void {
        $this->resetAfterTest();
        $event = $this->create_event();

        $seminar = $event->get_seminar();
        $seminar->set_attendancetime($seminarsetting['attendancetime']);
        $seminar->set_sessionattendance($seminarsetting['sessionattendance']);
        $seminar->save();

        $session = new seminar_session();
        $session->set_sessionid($event->get_id());
        if (isset($time['start'])) {
            $session->set_timestart($time['start']);
        }

        if (isset($time['finish'])) {
            $session->set_timefinish($time['finish']);
        }

        $session->save();
        $this->assertEquals($result, $session->is_attendance_open($currenttime));
    }

    /**
     * If an event is cancelled, then it should not be open for taking attendance.
     * @return void
     */
    public function test_open_attendance_when_event_is_cancelled(): void {
        $this->resetAfterTest();

        $event = $this->create_event();

        $seminar = $event->get_seminar();
        $seminar->set_sessionattendance(true);
        $seminar->set_attendancetime(seminar::ATTENDANCE_TIME_ANY);
        $seminar->save();

        $session = new seminar_session();
        $session->set_timefinish(time() + 3000);
        $session->set_timestart(time() + 300);
        $session->set_sessionid($event->get_id());
        $session->save();

        $event->set_cancelledstatus(1);
        $event->save();

        $this->assertFalse($session->is_attendance_open(time()));
    }
}