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

use mod_facetoface\{seminar_event, seminar, seminar_session, signup, session_status};
use mod_facetoface\signup\state\{booked, fully_attended, not_set, partially_attended};
use mod_facetoface\attendance\attendance_helper;

class mod_facetoface_attendance_helper_testcase extends advanced_testcase {
    /**
     * Create the seminar event with seminar setting as follow:
     * + sessionattendance => 1
     * + attendancetime => ANY
     * @return seminar_event
     */
    private function get_seminar_event(): seminar_event {
        $gen = $this->getDataGenerator();
        $course = $gen->create_course();

        $f2fgen = $gen->get_plugin_generator('mod_facetoface');
        $f2f = $f2fgen->create_instance(['course' => $course->id]);

        $s = new seminar($f2f->id);
        $s->set_sessionattendance(1);
        $s->set_attendancetime(seminar::ATTENDANCE_TIME_ANY);
        $s->save();

        $event = new seminar_event();
        $event->set_facetoface($s->get_id());
        $event->save();

        return $event;
    }

    /**
     * Create number of users.
     * @param int $numberofusers
     * @return array
     */
    private function create_users(int $numberofusers = 5): array {
        $gen = $this->getDataGenerator();
        $a = [];
        for ($i = 0; $i < $numberofusers; $i++) {
            $a[] = $gen->create_user();
        }

        return $a;
    }

    /**
     * Create 2 sessions in future for an event.
     * @param seminar_event $event
     * @return void
     */
    private function create_sessions(seminar_event $event): void {
        $time = time();
        $s1 = new seminar_session();
        $s1->set_timestart($time + (3600 * 3));
        $s1->set_timefinish($time + (3600 * 4));
        $s1->set_sessionid($event->get_id());
        $s1->save();

        $s2 = new seminar_session();
        $s2->set_timestart($time + (3600 * 5));
        $s2->set_timefinish($time + (3600 * 6));
        $s2->set_sessionid($event->get_id());
        $s2->save();
    }

    /**
     * A unit test to check on UNION ALL sql, as normal UNION would taking distinct record(s) rather
     * than all the record(s) that might be the same.
     *
     * @return void
     */
    public function test_get_calculated_status(): void {
        $this->resetAfterTest();

        $event = $this->get_seminar_event();
        $this->create_sessions($event);
        $first = $event->get_sessions()->get_first();

        $users = $this->create_users();

        // This is the expected data for assertion. For each signup, it should have a total of
        // 2 statuses, because there are 2 session dates within an event here. And the status is
        // not_set by default, and only one signup has set one attendance status of session.
        $data = [];
        $gen = $this->getDataGenerator();

        foreach ($users as $user) {
            $gen->enrol_user($user->id, $event->get_seminar()->get_course());

            $signup = signup::create($user->id, $event);
            $signup->save();
            $signup->switch_state(booked::class);

            // Total of 2 sessions, and these are at not_set state, for each single signup.
            $data[$signup->get_userid()] = 2;

            $sessionstatus = session_status::from_signup($signup, $first->get_id());
            $sessionstatus->save();
        }

        // Edit the last user here to have the fully_attended attendance state. so that we can
        // start testing whether the calculation is correct or not.
        $lastuser = end($users);
        $sessionstatus = session_status::from_signup(
            signup::create($lastuser->id, $event),
            $first->get_id()
        );

        $sessionstatus->set_attendance_status(fully_attended::class);
        $sessionstatus->save();
        $data[$lastuser->id] = 1;


        $helper = new attendance_helper();
        $rs = $helper->get_calculated_session_attendance_status($event->get_id());

        foreach ($data as $userid => $total) {
            if (!isset($rs[$userid])) {
                $this->fail("No user id '{$userid}' was found in \$rs");
            }

            $stats = $rs[$userid];
            $this->assertEquals($total, $stats[not_set::get_code()]);
        }
    }

    /**
     * Test suite of checking whether the field `createdby` and `timecreated` are populated corectly for table
     * `facetoface_signups_dates_status`. And also it does check the functionality of processing attendance state should
     * skip those records that does not change at all, when saving to storage.
     * @return void
     */
    public function test_taking_session_attendance(): void {
        global $USER, $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        $gen = $this->getDataGenerator();
        $event = $this->get_seminar_event();
        $this->create_sessions($event);

        $users = $this->create_users(5);
        $attendance = [];

        foreach ($users as $user) {
            $gen->enrol_user($user->id, $event->get_seminar()->get_course());
            $signup = signup::create($user->id, $event);
            $signup->save();
            $signup->switch_state(booked::class);

            $attendance[$signup->get_id()] = fully_attended::get_code();
        }

        $sessions = $event->get_sessions();
        foreach ($sessions as $session) {
            attendance_helper::process_session_attendance($attendance, $session->get_id());
        }

        // Test asserting the data are populated correctly here for field createdby and timecreated.
        $records = $DB->get_records('facetoface_signups_dates_status');
        $original = count($records);
        foreach ($records as $record) {
            $this->assertEquals($USER->id, $record->createdby);
            $this->assertNotEmpty($record->timecreated);
            $this->assertEquals(fully_attended::get_code(), $record->attendancecode);
        }

        // Now start updating a few attendance status here to see if the process_session_attendance does skip those
        // duplicated records here.
        $x = 0;
        foreach ($attendance as $submissionid => $code) {
            $attendance[$submissionid] = partially_attended::get_code();
            $x++;
            if ($x == 2) {
                break;
            }
        }

        attendance_helper::process_session_attendance($attendance, $sessions->get_first()->get_id());

        // As if the attendance state is the same for the signup (without any update), then it should not insert a new
        // row and supersede the old one. However, the different statuses will be able to allow the function to do so.
        // Therefore, we are expecting $x number of rows to be inserted into the database
        $records = $DB->get_records('facetoface_signups_dates_status');
        $this->assertCount(($original + $x), $records);

        // Expecting $x number of records that has been superseded.
        $records = $DB->get_records('facetoface_signups_dates_status', ['superceded' => 1]);
        $this->assertCount($x, $records);
    }
}