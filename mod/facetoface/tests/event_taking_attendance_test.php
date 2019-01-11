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

use mod_facetoface\{seminar_event, seminar, seminar_session, signup, signup_helper};
use mod_facetoface\signup\state\{booked, fully_attended};

class mod_facetoface_event_taking_attendance_testcase extends advanced_testcase {
    /**
     * Create an event with two sessions, where first session is going to be in the pass and the
     * second session is going to the future.
     * @return seminar_event
     */
    private function create_seminar_event(): seminar_event {
        $gen = $this->getDataGenerator();
        $course = $gen->create_course();

        $f2fgen = $gen->get_plugin_generator('mod_facetoface');
        $f2f = $f2fgen->create_instance(['course' => $course->id]);

        $s = new seminar($f2f->id);
        $s->set_sessionattendance(0);
        $s->set_attendancetime(seminar::ATTENDANCE_TIME_ANY);
        $s->save();

        $e = new seminar_event();
        $e->set_facetoface($s->get_id());
        $e->save();

        $time = time();
        $times = [
            ['start' => $time + 3600, 'finish' => $time + (3600 * 2)],
            ['start' => $time +  (3600 * 4), 'finish' => $time + (3600 * 5)],
        ];

        foreach ($times as $t) {
            $ss = new seminar_session();
            $ss->set_timefinish($t['finish']);
            $ss->set_timestart($t['start']);
            $ss->set_sessionid($e->get_id());
            $ss->save();
        }

        return $e;
    }

    /**
     * Test suite to ensure that switching between booked state to attendance_state of a user within
     * event level (which has more than 1 session) has no problem at all.
     * @return void
     */
    public function test_process_attendance_on_event_with_multiplesessions(): void {
        $this->resetAfterTest();
        $event = $this->create_seminar_event();

        $gen = $this->getDataGenerator();
        $data = [];

        for ($i = 0; $i < 5; $i++) {
            $user = $gen->create_user();
            $gen->enrol_user($user->id, $event->get_seminar()->get_course());

            $signup = signup::create($user->id, $event);
            $signup->save();
            $signup->switch_state(booked::class);

            // Start preparing the attendance test for taking attendance in event level.
            $data[$signup->get_id()] = fully_attended::get_code();
        }

        // Start marking the first session to be a history session here.
        $time = time();
        $session = $event->get_sessions()->get_first();
        $session->set_timestart($time - (3600 * 2));
        $session->set_timefinish($time - 3600);
        $session->save();

        signup_helper::process_attendance($event, $data);

        foreach ($data as $submissionid => $code) {
            $signup = new signup($submissionid);
            $state = $signup->get_state();
            $this->assertEquals($state::get_code(), $code);
        }
    }
}