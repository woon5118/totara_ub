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

use mod_facetoface\{seminar, seminar_event, seminar_session};

class mod_facetoface_seminar_event_testcase extends advanced_testcase {
    /**
     * Provide the data for testing the seminar_event status calculation
     * @return array
     */
    public function provide_test_data(): array {
        $time = time();

        $s1 = new seminar_session();
        $s1->set_timefinish($time + 3600);
        $s1->set_timestart($time + (3600 * 3)); // 3 hours from current time

        $s2 = new seminar_session();
        $s2->set_timefinish($time + (3600 * 5)); // 5 hours from current time
        $s2->set_timestart($time + (3600 * 7)); // 7 hours from current time

        $a = [$s1, $s2];
        return [
            [$time + (3600 * 11), $a, [
                'progress' => false,
                'upcoming' => false,
                'over' => true
                ]
            ],

            [$time + (3600 * 4), $a, [
                'progress' => true,
                'upcoming' => false,
                'over' => false
                ]
            ],

            [$time, $a, [
                'progress' => false,
                'upcoming' => true,
                'over' => false
                ]
            ],
            [$time, [], [
                'progress' => false,
                'upcoming' => true, // Waitlisted-event is considered as upcoming
                'over' => false
                ]
            ]
        ];
    }

    /**
     * Test suite of calculate the status of event base on: sessions time against the current time.
     * @dataProvider provide_test_data
     * @param int $time
     * @param seminar_session[] $sessiondates
     * @param array $statuses
     * @return void
     */
    public function test_getting_status_of_event(int $time, array $sessiondates,
                                                 array $statuses): void {
        $this->resetAfterTest();
        $gen = $this->getDataGenerator();
        $course = $gen->create_course();

        $f2f = new seminar();
        $f2f->set_course($course->id);
        $f2f->save();

        $event = new seminar_event();
        $event->set_facetoface($f2f->get_id());
        $event->save();

        foreach ($sessiondates as $sd) {
            // Cloning a single object session date here, so that it won't affect the data provider.
            $o = clone $sd;
            $o->set_sessionid($event->get_id());
            $o->save();
        }

        $this->assertEquals(
            $statuses['progress'],
            $event->is_progress($time),
            "Expecting progress to be a value of {$statuses['progress']} but received differently"
        );
        $this->assertEquals(
            $statuses['upcoming'],
            !$event->is_started($time),
            "Expecting 'upcoming' to be a value of {$statuses['upcoming']} but received differently"
        );
        $this->assertEquals(
            $statuses['over'],
            $event->is_over($time),
            "Expecting 'over' to be a value of {$statuses['over']} but received differently"
        );
    }

    /**
     * Provide data for test suite of checking whether the attendance is open for event.
     * @return array
     */
    public function provide_data_for_attendance(): array {
        $time = time();
        return [
            [
                seminar::ATTENDANCE_TIME_END,
                $time,
                [
                    [
                        'start' => $time + (3600 * 3),
                        'finish' => $time + (3600 * 4)
                    ],
                    [
                        'start' => $time + (3600 * 5),
                        'finish' => $time + (3600 * 8)
                    ]
                ],
                false
            ],
            [
                seminar::ATTENDANCE_TIME_START,
                $time,
                [
                    [
                        'start' => $time + (60 * 2),
                        'finish' => $time + (3600 * 2),
                    ],
                    [
                        'start' => $time + (3600 * 5),
                        'finish' => $time + (3600 * 6)
                    ]
                ],
                true
            ],
            [
                seminar::ATTENDANCE_TIME_START,
                $time,
                [
                    [
                        'start' => $time + 3600,
                        'finish' => $time + (3600 * 2)
                    ]
                ],
                false
            ],
            [
                seminar::ATTENDANCE_TIME_END,
                $time + (3600 * 11),
                [
                    [
                        'start' => $time + 3600,
                        'finish' => $time + (3600 * 2),
                    ],
                    [
                        'start' => $time + (3600 * 4),
                        'finish' => $time + (3600 * 6)
                    ]
                ],
                true
            ],
            // this is a wait-listed event, and attendance should be opened
            [seminar::ATTENDANCE_TIME_ANY, $time - (3600 * 24), [], false],
        ];
    }

    /**
     * Test suite to check whether the event has open for attendance base on: sessions time against
     * current time.
     *
     * @dataProvider provide_data_for_attendance
     * @param int $attendancetime
     * @param int $time
     * @param array $sessions
     * @param bool $expected
     */
    public function test_is_attendance_open(int $attendancetime, int $time,
                                                   array $sessions, bool $expected): void {
        $this->resetAfterTest(true);

        $gen = $this->getDataGenerator();
        $course = $gen->create_course();

        $s = new seminar();
        $s->set_course($course->id);
        $s->set_sessionattendance(1);
        $s->set_attendancetime($attendancetime);
        $s->save();

        $event = new seminar_event();
        $event->set_facetoface($s->get_id());
        $event->save();

        foreach ($sessions as $session) {
            $d = new seminar_session();
            $d->set_sessionid($event->get_id());
            $d->set_timestart($session['start']);
            $d->set_timefinish($session['finish']);
            $d->save();
        }

        $rs = $event->is_attendance_open($time);
        $this->assertEquals($expected, $rs);
    }

    /**
     * Test suite of checking the calculation of 'attendance open' when the event is cancelled.
     * @return void
     */
    public function test_is_attendance_open_for_cancelled_event(): void {
        $this->resetAfterTest();

        $gen = $this->getDataGenerator();
        $course = $gen->create_course();

        $s = new seminar();
        $s->set_course($course->id);
        $s->set_sessionattendance(1);
        $s->set_attendancetime(seminar::ATTENDANCE_TIME_ANY);
        $s->save();

        $event = new seminar_event();
        $event->set_facetoface($s->get_id());
        $event->set_cancelledstatus(1);
        $event->save();

        $s = new seminar_session();
        $s->set_sessionid($event->get_id());
        $s->set_timestart(time() + 3600);
        $s->set_timefinish(time() + 300);
        $s->save();

        $this->assertFalse($event->is_attendance_open(time()));
    }
}