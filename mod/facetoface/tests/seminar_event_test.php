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

use mod_facetoface\{attendance_taking_status, seminar, seminar_event, seminar_event_helper, seminar_session, signup};
use mod_facetoface\signup\state\fully_attended;

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
            !$event->is_first_started($time),
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
                seminar::EVENT_ATTENDANCE_LAST_SESSION_END,
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
                false,
                array_fill(0, 8, attendance_taking_status::CLOSED_UNTILEND)
            ],
            [
                seminar::EVENT_ATTENDANCE_FIRST_SESSION_START,
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
                true,
                [
                    attendance_taking_status::OPEN,
                    attendance_taking_status::OPEN,
                    attendance_taking_status::OPEN,
                    attendance_taking_status::NOTAVAILABLE,
                    attendance_taking_status::OPEN,
                    attendance_taking_status::OPEN,
                    attendance_taking_status::ALLSAVED,
                    attendance_taking_status::ALLSAVED,
                ]
            ],
            [
                seminar::EVENT_ATTENDANCE_FIRST_SESSION_START,
                $time,
                [
                    [
                        'start' => $time + 3600,
                        'finish' => $time + (3600 * 2)
                    ]
                ],
                false,
                array_fill(0, 8, attendance_taking_status::CLOSED_UNTILSTARTFIRST)
            ],
            [
                seminar::EVENT_ATTENDANCE_LAST_SESSION_END,
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
                true,
                [
                    attendance_taking_status::OPEN,
                    attendance_taking_status::OPEN,
                    attendance_taking_status::OPEN,
                    attendance_taking_status::NOTAVAILABLE,
                    attendance_taking_status::OPEN,
                    attendance_taking_status::OPEN,
                    attendance_taking_status::ALLSAVED,
                    attendance_taking_status::ALLSAVED,
                ]
            ],
            // this is a wait-listed event, and attendance should be opened
            [
                seminar::EVENT_ATTENDANCE_UNRESTRICTED,
                $time - (3600 * 24),
                [],
                false,
                array_fill(0, 8, attendance_taking_status::NOTAVAILABLE)
            ],
        ];
    }

    /**
     * Test suite to check whether the event has open for attendance base on: sessions time against
     * current time.
     * Also test the attendance taking status is_attendance_open() function depends on.
     *
     * @dataProvider provide_data_for_attendance
     * @param int $eventattendance
     * @param int $time
     * @param array $sessions
     * @param bool $expectedopen
     * @param int[] $expectedstate
     */
    public function test_is_attendance_open(int $eventattendance, int $time,
                                                   array $sessions, bool $expectedopen, array $expectedstates): void {
        $gen = $this->getDataGenerator();
        $course = $gen->create_course();
        $f2f = $this->getDataGenerator()->create_module('facetoface', array('course' => $course->id, 'attendancetime' => $eventattendance));
        $s = new seminar($f2f->id);

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

        $this->assertSame($expectedopen, $event->is_attendance_open($time));
        $this->assertSame($expectedstates[0], $event->get_attendance_taking_status(null, $time, false, false));
        $this->assertSame($expectedstates[1], $event->get_attendance_taking_status(null, $time, false, true));
        $this->assertSame($expectedstates[2], $event->get_attendance_taking_status(null, $time, true, false));
        $this->assertSame($expectedstates[3], $event->get_attendance_taking_status(null, $time, true, true));

        // Create a user, sign up, take attendance while bypassing all the restrictions of state transition.
        $user = $gen->create_user();
        $gen->enrol_user($user->id, $course->id);
        $signup = signup::create($user->id, $event);
        $signup->save();

        $rc = new ReflectionClass($signup);
        $method = $rc->getMethod('update_status');
        $method->setAccessible(true);
        $method->invoke($signup, new fully_attended($signup));

        $this->assertSame($expectedopen, $event->is_attendance_open($time));
        $this->assertSame($expectedstates[4], $event->get_attendance_taking_status(null, $time, false, false));
        $this->assertSame($expectedstates[5], $event->get_attendance_taking_status(null, $time, false, true));
        $this->assertSame($expectedstates[6], $event->get_attendance_taking_status(null, $time, true, false));
        $this->assertSame($expectedstates[7], $event->get_attendance_taking_status(null, $time, true, true));
    }

    /**
     * Test suite of checking the calculation of 'attendance open' when the event is cancelled.
     * Also test the attendance taking status is_attendance_open() function depends on.
     */
    public function test_is_attendance_open_for_cancelled_event(): void {
        $gen = $this->getDataGenerator();
        $course = $gen->create_course();

        $s = new seminar();
        $s->set_course($course->id);
        $s->set_attendancetime(seminar::EVENT_ATTENDANCE_UNRESTRICTED);
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
        $this->assertSame(attendance_taking_status::CANCELLED, $event->get_attendance_taking_status(null, time()));
    }

    public function test_rotate_session_dates() {
        global $DB;

        $gen = $this->getDataGenerator();
        /** @var mod_facetoface_generator $f2fgen */
        $f2fgen = $gen->get_plugin_generator('mod_facetoface');
        $course = $gen->create_course();

        $times = [
            1111111111, 2222222222, 3333333333
        ];
        $rooms = [
            $f2fgen->add_site_wide_room([])->id,
            $f2fgen->add_site_wide_room([])->id,
            $f2fgen->add_site_wide_room([])->id,
        ];
        sort($rooms);

        $f2f = $f2fgen->create_instance(['course' => $course->id, 'name' => 'Test seminar']);
        $dates = [
            (object)[
                'timestart' => $times[0],
                'timefinish' => $times[0] + 100,
                'sessiontimezone' => '99',
                'roomids' => [$rooms[0]],
                'assetids' => []
            ],
            (object)[
                'timestart' => $times[1],
                'timefinish' => $times[1] + 100,
                'sessiontimezone' => '99',
                'roomids' => [$rooms[1]],
                'assetids' => []
            ],
            (object)[
                'timestart' => $times[2],
                'timefinish' => $times[2] + 100,
                'sessiontimezone' => '99',
                'roomids' => [$rooms[2]],
                'assetids' => []
            ]
        ];
        $sessionid = $f2fgen->add_session(array('facetoface' => $f2f->id, 'sessiondates' => $dates));
        $seminarevent = new seminar_event($sessionid);
        $sessions = iterator_to_array($seminarevent->get_sessions(true), false);
        /** @var seminar_session[] $sessions */
        $newdates = [
            (object)[
                'id' => $sessions[0]->get_id(),
                'timestart' => $sessions[1]->get_timestart(),
                'timefinish' => $sessions[1]->get_timefinish(),
                'sessiontimezone' => '99',
                'roomids' => [$rooms[0]],
                'assetids' => []
            ],
            (object)[
                'id' => $sessions[1]->get_id(),
                'timestart' => $sessions[2]->get_timestart(),
                'timefinish' => $sessions[2]->get_timefinish(),
                'sessiontimezone' => '99',
                'roomids' => [$rooms[1]],
                'assetids' => []
            ],
            (object)[
                'id' => $sessions[2]->get_id(),
                'timestart' => $sessions[0]->get_timestart(),
                'timefinish' => $sessions[0]->get_timefinish(),
                'sessiontimezone' => '99',
                'roomids' => [$rooms[2]],
                'assetids' => []
            ]
        ];

        $transaction = $DB->start_delegated_transaction();
        seminar_event_helper::merge_sessions($seminarevent, $newdates);
        $transaction->allow_commit();

        $newsessions = iterator_to_array($seminarevent->get_sessions(true), false);
        usort($newsessions, function ($x, $y) {
            $session_x_rooms = \mod_facetoface\room_list::from_session($x->get_id());
            $session_y_rooms = \mod_facetoface\room_list::from_session($y->get_id());
            $x_room = $session_x_rooms->current();
            $y_room = $session_y_rooms->current();
            return $x_room->get_id() <=> $y_room->get_id();
        });
        $this->assertCount(3, $newsessions);
        $this->assertEquals($sessions[1]->get_timestart(), $newsessions[0]->get_timestart());
        $this->assertEquals($sessions[2]->get_timestart(), $newsessions[1]->get_timestart());
        $this->assertEquals($sessions[0]->get_timestart(), $newsessions[2]->get_timestart());
    }

    public function test_shift_session_dates() {
        global $DB;

        $gen = $this->getDataGenerator();
        /** @var mod_facetoface_generator $f2fgen */
        $f2fgen = $gen->get_plugin_generator('mod_facetoface');
        $course = $gen->create_course();

        $times = [
            1111111111, 2222222222, 3333333333, 4444444444
        ];
        $rooms = [
            $f2fgen->add_site_wide_room([])->id,
            $f2fgen->add_site_wide_room([])->id,
            $f2fgen->add_site_wide_room([])->id,
        ];
        sort($rooms);

        $f2f = $f2fgen->create_instance(['course' => $course->id, 'name' => 'Test seminar']);
        $dates = [
            (object)[
                'timestart' => $times[0],
                'timefinish' => $times[0] + 1000,
                'sessiontimezone' => '99',
                'roomids' => [$rooms[0]],
                'assetids' => []
            ],
            (object)[
                'timestart' => $times[1],
                'timefinish' => $times[1] + 1000,
                'sessiontimezone' => '99',
                'roomids' => [$rooms[1]],
                'assetids' => []
            ],
            (object)[
                'timestart' => $times[2],
                'timefinish' => $times[2] + 1000,
                'sessiontimezone' => '99',
                'roomids' => [$rooms[2]],
                'assetids' => []
            ]
        ];
        $sessionid = $f2fgen->add_session(array('facetoface' => $f2f->id, 'sessiondates' => $dates));
        $seminarevent = new seminar_event($sessionid);
        $sessions = iterator_to_array($seminarevent->get_sessions(true), false);
        /** @var seminar_session[] $sessions */
        $newdates = [
            (object)[
                'id' => $sessions[0]->get_id(),
                'timestart' => $times[1],
                'timefinish' => $times[1] + 100,
                'sessiontimezone' => '99',
                'roomids' => [$rooms[0]],
                'assetids' => []
            ],
            (object)[
                'id' => $sessions[1]->get_id(),
                'timestart' => $times[2],
                'timefinish' => $times[2] + 100,
                'sessiontimezone' => '99',
                'roomids' => [$rooms[1]],
                'assetids' => []
            ],
            (object)[
                'id' => $sessions[2]->get_id(),
                'timestart' => $times[3],
                'timefinish' => $times[3] + 100,
                'sessiontimezone' => '99',
                'roomids' => [$rooms[2]],
                'assetids' => []
            ]
        ];

        $transaction = $DB->start_delegated_transaction();
        seminar_event_helper::merge_sessions($seminarevent, $newdates);
        $transaction->allow_commit();

        /** @var seminar_session[] $newsessions */
        $newsessions = iterator_to_array($seminarevent->get_sessions(true), false);
        usort($newsessions, function ($x, $y) {
            $session_x_rooms = \mod_facetoface\room_list::from_session($x->get_id());
            $session_y_rooms = \mod_facetoface\room_list::from_session($y->get_id());
            $x_room = $session_x_rooms->current();
            $y_room = $session_y_rooms->current();
            return $x_room->get_id() <=> $y_room->get_id();
        });
        $this->assertCount(3, $newsessions);
        $this->assertEquals($times[1], $newsessions[0]->get_timestart());
        $this->assertEquals($times[2], $newsessions[1]->get_timestart());
        $this->assertEquals($times[3], $newsessions[2]->get_timestart());
        $session_0_rooms = \mod_facetoface\room_list::from_session($newsessions[0]->get_id());
        $session_1_rooms = \mod_facetoface\room_list::from_session($newsessions[1]->get_id());
        $session_2_rooms = \mod_facetoface\room_list::from_session($newsessions[2]->get_id());
        $this->assertEquals($rooms[0], $session_0_rooms->current()->get_id());
        $this->assertEquals($rooms[1], $session_1_rooms->current()->get_id());
        $this->assertEquals($rooms[2], $session_2_rooms->current()->get_id());
    }
}
