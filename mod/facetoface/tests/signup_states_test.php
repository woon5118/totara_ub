<?php

/*
 * This file is part of Totara LMS
 *
 * Copyright (C) 2018 onwards Totara Learning Solutions LTD
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

use mod_facetoface\{seminar, seminar_event, seminar_session, signup, signup_status};
use mod_facetoface\signup\condition\{ condition, event_taking_attendance };
use mod_facetoface\signup\restriction\restriction;
use mod_facetoface\signup\state\{not_set, state, booked, event_cancelled};
use mod_facetoface\signup\transaction;

defined('MOODLE_INTERNAL') || die();

/**
 * A unit test to make sure that all states and relevant objects are the right classes.
 *
 * Class mod_facetoface_signup_states_testcase
 */
class mod_facetoface_signup_states_testcase extends advanced_testcase {
    protected function setUp() {
        $this->resetAfterTest(true);
        $this->setAdminUser();
    }

    /**
     * Walk through the transitions of all states and assert their conditions or restrictions.
     *
     * @param string $prop_name the property name of the transaction class
     * @param string $parent_class
     * @return void
     */
    private function assert_state_transitions(string $prop_name, string $parent_class): void {
        $user = $this->getDataGenerator()->create_user();
        $seminar = new seminar();
        $seminar->save();
        $seminarevent = new seminar_event();
        $seminarevent->set_facetoface($seminar->get_id());
        $seminarevent->save();
        $signup = signup::create($user->id, $seminarevent);

        $done = [];

        foreach (state::get_all_states() as $stateclass) {
            $state = new $stateclass($signup);

            foreach ($state->get_map() as $transition) {
                $reflect = new ReflectionClass($transition);
                $prop = $reflect->getProperty($prop_name);
                $prop->setAccessible(true);

                $classes = $prop->getValue($transition);
                foreach ($classes as $class) {
                    // do not test a class that is already tested
                    if (array_key_exists($class, $done)) {
                        continue;
                    }
                    $done[$class] = true;
                    $obj = new $class($signup);
                    $this->assertInstanceOf($parent_class, $obj);
                }
            }
        }
    }

    /**
     * @return void
     */
    public function test_all_given_conditions_are_classes_of_condition(): void {
        // test only conditions
        $this->assert_state_transitions('conditions', condition::class);
    }

    /**
     * @return void
     */
    public function test_all_given_restrictions_are_classes_of_restriction(): void {
        // test only restrictions
        $this->assert_state_transitions('restrictions', restriction::class);
    }

    /**
     * Test the transition of attendance_state classes.
     */
    public function test_all_attendance_states_and_their_transitions(): void {
        $user = $this->getDataGenerator()->create_user();
        $seminar = new seminar();
        $seminar->save();
        $seminarevent = new seminar_event();
        $seminarevent->set_facetoface($seminar->get_id());
        $seminarevent->save();
        $signup = signup::create($user->id, $seminarevent);

        $attendance_state_classes = mod_facetoface\signup\state\attendance_state::get_all_attendance_states();
        // add some states if not there
        $attendance_state_classes = array_unique(array_merge($attendance_state_classes, [ booked::class, event_cancelled::class ]));

        foreach ($attendance_state_classes as $stateclass) {
            $state = new $stateclass($signup);
            if ($state instanceof event_cancelled) {
                // nothing to test
                continue;
            }
            if ($state instanceof booked) {
                // booked state is slightly different
                // its transition must have all attendance_state classes
                $transitions = $state->get_map();
                // remove booked state
                $remains = array_diff($attendance_state_classes, [ booked::class ]);
                foreach ($transitions as $transition) {
                    $to_class = get_class($transition->get_to());
                    // remove found attendance_state
                    $remains = array_diff($remains, [ $to_class ]);
                }
                $this->assertCount(0, $remains);
            } else {
                // anything else must be attendance_state,
                // whose transition must have all attendance_state classes and booked state, except itself
                $this->assertInstanceOf(mod_facetoface\signup\state\attendance_state::class, $state);
                $transitions = $state->get_map();
                $this->assertCount(count($attendance_state_classes) - 1, $transitions);
                foreach ($transitions as $transition) {
                    $to_class = get_class($transition->get_to());
                    $this->assertContains($to_class, $attendance_state_classes);
                    $this->assertNotEquals($to_class, $stateclass);
                }
            }
        }
    }

    /**
     * Create a seminar event and add a session to the event.
     *
     * @param \mod_facetoface\seminar $seminar
     * @param integer $timediff
     * @param integer $duration
     * @return \mod_facetoface\seminar_event
     */
    private function create_event_and_session(seminar $seminar, int $timediff, int $duration) {
        $seminarevent = new seminar_event();
        $seminarevent->set_facetoface($seminar->get_id());
        $seminarevent->save();

        $timestart = time() + $timediff;
        $sessiondate = new seminar_session();
        $sessiondate->set_sessionid($seminarevent->get_id())
            ->set_timestart($timestart)
            ->set_timefinish($timestart + $duration)
            ->save();

        return $seminarevent;
    }

    /**
     * Data provider - [ human_readable_text, expect, attendancetime, session_duration, time_difference ]
     *
     * @return array
     */
    public function data_provider_name_expect_attendancetime_duration_timediff() {
        $before_attendance_start = mod_facetoface\signup\condition\event_taking_attendance::UNLOCKED_SECS_PRIOR_TO_START + 300;
        $after_attendance_start = mod_facetoface\signup\condition\event_taking_attendance::UNLOCKED_SECS_PRIOR_TO_START - 300;
        $dataset = [
            [ 'case' => 'Far past', /*                          */'expects' => [ true, true, true ], /*  */'duration' => HOURSECS, /**/'timediff' => -YEARSECS ],
            [ 'case' => 'Just ended', /*                        */'expects' => [ true, true, true ], /*  */'duration' => DAYSECS - 10, 'timediff' => -DAYSECS ],
            [ 'case' => 'Just started', /*                      */'expects' => [ false, true, true ], /* */'duration' => HOURSECS, /**/'timediff' => -1 ],
            [ 'case' => 'Starts within attendance unlock period', 'expects' => [ false, true, true ], /* */'duration' => HOURSECS, /**/'timediff' => $after_attendance_start ],
            [ 'case' => 'Starts before attendance unlock period', 'expects' => [ false, false, true ], /**/'duration' => HOURSECS, /**/'timediff' => $before_attendance_start ],
            [ 'case' => 'Far future', /*                        */'expects' => [ false, false, true ], /**/'duration' => HOURSECS, /**/'timediff' => YEARSECS ],
        ];
        $data = [];
        foreach ($dataset as $e) {
            // add seminar::ATTENDANCE_TIME_xxx
            for ($i = 0; $i <= 2; $i++) {
                $data[] = [ $e['case'], $e['expects'][$i], $i, $e['duration'], $e['timediff'] ];
            }
        }
        return $data;
    }

    /**
     * Test event_taking_attendance::pass.
     *
     * @dataProvider data_provider_name_expect_attendancetime_duration_timediff
     */
    public function test_attendance_states_when_attendance_tracking_is_enabled(string $name, bool $expect, int $attendancetime, int $duration, int $timediff) {
        // some sanity checks
        $this->assertSame(0, seminar::ATTENDANCE_TIME_END);
        $this->assertSame(1, seminar::ATTENDANCE_TIME_START);
        $this->assertSame(2, seminar::ATTENDANCE_TIME_ANY);

        $generator = $this->getDataGenerator();

        $seminar = new seminar();
        $seminar->set_sessionattendance(1)->set_attendancetime($attendancetime)->save();
        $event = $this->create_event_and_session($seminar, $timediff, $duration);
        $user = $generator->create_user();
        $signup = signup::create($user->id, $event)->save();
        $condition = new event_taking_attendance($signup);
        $this->assertSame($expect, $condition->pass(), $name . ' [' . [ 'END', 'START', 'ANY' ][$attendancetime] . ']');
    }

    /**
     * Confirm that not_set state cannot be stored in signup_status.
     * @expectedException \mod_facetoface\exception\signup_exception
     * @expectedExceptionMessage Cannot update status without state set
     */
    public function test_signup_status_not_set_cannot_be_saved() {
        $status = new signup_status();
        $status->set_statuscode(signup\state\not_set::get_code());
        $status->set_signupid(42);
        $status->save();
    }

    /**
     * Confirm signup_status cannot be stored if status was never set.
     * @expectedException \mod_facetoface\exception\signup_exception
     * @expectedExceptionMessage Cannot update status without state set
     */
    public function test_signup_status_cannot_be_saved_if_set_statuscode_is_never_called() {
        $status = new signup_status();
        $status->set_signupid(42);
        $status->save();
    }

    /**
     * Confirm that not_set state cannot be stored as signup state.
     * @expectedException \mod_facetoface\exception\signup_exception
     * @expectedExceptionMessage New booking status cannot be 'not set'
     */
    public function test_signup_cannot_be_updated_to_not_set() {
        $signup = new signup();
        $reflection = new ReflectionMethod($signup, 'update_status');
        $reflection->setAccessible(true);
        $reflection->invoke($signup, new not_set($signup));
    }
}
