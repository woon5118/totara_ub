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

use mod_facetoface\event\booking_booked;
use mod_facetoface\event\signup_status_updated;
use mod_facetoface\seminar;
use mod_facetoface\seminar_event;
use mod_facetoface\seminar_session;
use mod_facetoface\signup_helper;
use mod_facetoface\signup;
use mod_facetoface\signup_status;
use mod_facetoface\signup\state\booked;
use mod_facetoface\signup\state\partially_attended;
use mod_facetoface\signup\state\fully_attended;
use mod_facetoface\signup\state\unable_to_attend;
use mod_facetoface\signup\state\no_show;

defined('MOODLE_INTERNAL') || die();

/**
 * Class mod_facetoface_signup_helper_testcase
 */
class mod_facetoface_signup_helper_testcase extends advanced_testcase {
    /**
     * set up for test_compute_final_grade
     *
     * @return \stdClass containing seminar, user1, user2, event1, event2, signup1, signup2
     */
    protected function setup_compute_final_grade() {
        $this->resetAfterTest(true);
        $this->setAdminUser();

        $that = new \stdClass();
        $gen = $this->getDataGenerator();
        $that->course = $gen->create_course();

        $f2fgen = $gen->get_plugin_generator('mod_facetoface');
        $f2f = $f2fgen->create_instance(['course' => $that->course->id]);

        $that->seminar = new seminar($f2f->id);
        $that->seminar
            ->set_sessionattendance(0)
            ->set_multisignupfully(true)
            ->set_multisignuppartly(true)
            ->set_multiplesessions(1)
            ->set_multisignupmaximum(2)
            ->set_eventgradingmethod(0)
            ->save();

        $that->user1 = $gen->create_user();
        $that->user2 = $gen->create_user();
        $gen->enrol_user($that->user1->id, $that->course->id);

        $creator = function ($timediff, $user, $status, &$evt, &$sup) use ($f2f) {
            $evt = new seminar_event();
            $evt->set_facetoface($f2f->id)->save();
            $time = time() + $timediff;
            $sess = new seminar_session();
            $sess->set_timestart($time)->set_timefinish($time + HOURSECS)->set_sessionid($evt->get_id())->save();
            $sup = signup::create($user->id, $evt);
            $sup->save();
            $sup->switch_state(booked::class);
            $sess->set_timestart($time - YEARSECS)->set_timefinish($time - YEARSECS + HOURSECS)->save();
            return signup_helper::process_attendance($evt, [ $sup->get_id() => $status ]);
        };

        $result = $creator(DAYSECS, $that->user1, partially_attended::get_code(), $that->event1, $that->signup1);
        $this->assertTrue($result);
        $result = $creator(DAYSECS * 2, $that->user1, partially_attended::get_code(), $that->event2, $that->signup2);
        $this->assertTrue($result);
        $result = signup_helper::process_attendance($that->event1, [ $that->signup1->get_id() => fully_attended::get_code() ]);
        $this->assertTrue($result);

        return $that;
    }

    /**
     * For more extensive tests,
     * @see \mod_facetoface_event_taking_attendance_testcase
     */
    public function test_compute_final_grade() {
        global $DB;
        $that = $this->setup_compute_final_grade();

        $grade = signup_helper::compute_final_grade($that->seminar, $that->user1->id);
        $this->assertDebuggingCalled();
        $this->assertSame(100., $grade);

        $grade = signup_helper::compute_final_grade($that->seminar, $that->user2->id);
        $this->assertDebuggingCalled();
        $this->assertSame(null, $grade);

        $that->seminar->set_eventgradingmethod(seminar::GRADING_METHOD_GRADELOWEST);
        $grade = signup_helper::compute_final_grade($that->seminar, $that->user1->id);
        $this->assertDebuggingCalled();
        $this->assertSame(50., $grade);

        $grade = signup_helper::compute_final_grade($that->seminar, $that->user2->id);
        $this->assertDebuggingCalled();
        $this->assertSame(null, $grade);

        $f2f = (object)[ 'id' => $that->seminar->get_id() ];
        $grade = signup_helper::compute_final_grade($f2f, $that->user1->id);
        $this->assertDebuggingCalled();
        $this->assertSame(100., $grade);

        $grade = signup_helper::compute_final_grade($f2f, $that->user2->id);
        $this->assertDebuggingCalled();
        $this->assertSame(null, $grade);

        $f2f->eventgradingmethod = seminar::GRADING_METHOD_GRADELOWEST;
        $grade = signup_helper::compute_final_grade($f2f, $that->user1->id);
        $this->assertDebuggingCalled();
        $this->assertSame(50., $grade);

        $grade = signup_helper::compute_final_grade($f2f, $that->user2->id);
        $this->assertDebuggingCalled();
        $this->assertSame(null, $grade);

        $bogusf2fid = $f2f->id + 42;
        $this->assertEquals(0, $DB->count_records('facetoface', [ 'id' => $bogusf2fid ]));

        $seminar = new seminar();
        $rc = new ReflectionClass($seminar);
        $pr = $rc->getProperty('id');
        $pr->setAccessible(true);
        $pr->setValue($seminar, $bogusf2fid); // invalidate seminar->id
        $grade = signup_helper::compute_final_grade($seminar, $that->user1->id);
        $this->assertDebuggingCalled();
        $this->assertSame(null, $grade);

        $that->seminar->set_eventgradingmethod(42); // invalidate eventgradingmethod
        try {
            signup_helper::compute_final_grade($that->seminar, $that->user1->id);
            $this->fail('Must fail when invalid eventgradingmethod is passed');
        } catch (\coding_exception $e) {
            $this->resetDebugging();
        }

        $f2f->id += 42; // invalidate f2f->id
        $this->assertEquals(0, $DB->count_records('facetoface', [ 'id' => $f2f->id ]));
        $grade = signup_helper::compute_final_grade($f2f, $that->user1->id);
        $this->assertDebuggingCalled();
        $this->assertSame(null, $grade);

        $f2f->eventgradingmethod = 42; // invalidate eventgradingmethod
        try {
            signup_helper::compute_final_grade($f2f, $that->user1->id);
            $this->fail('Must fail when invalid eventgradingmethod is passed');
        } catch (\coding_exception $e) {
            $this->resetDebugging();
        }

        try {
            signup_helper::compute_final_grade($f2f->id, $that->user1->id);
            $this->fail('Must fail when first argument is neither seminar nor stdClass');
        } catch (\coding_exception $e) {
            $this->resetDebugging();
        }
    }

    /**
     * @return array of [time_difference]
     */
    public function data_process_attendance_and_email_notification(): array {
        return [
            [-WEEKSECS],
            [-HOURSECS],
            [+DAYSECS]
        ];
    }

    /**
     * @param int $timediff
     * @dataProvider data_process_attendance_and_email_notification
     */
    public function test_process_attendance_and_email_notification(int $timediff) {
        $gen = $this->getDataGenerator();
        $course = $gen->create_course();

        /** @var mod_facetoface_generator $f2fgen */
        $f2fgen = $gen->get_plugin_generator('mod_facetoface');
        $f2f = $f2fgen->create_instance(['course' => $course->id]);

        (new seminar($f2f->id))
            ->set_attendancetime(seminar::EVENT_ATTENDANCE_UNRESTRICTED)
            ->set_eventgradingmanual(1)
            ->save();

        $user = $gen->create_user();
        $gen->enrol_user($user->id, $course->id);

        $now = time();
        $event = new seminar_event();
        $event->set_facetoface($f2f->id)->save();
        $session = new seminar_session();
        $session->set_timestart($now + YEARSECS)->set_timefinish($now + YEARSECS + HOURSECS)->set_sessionid($event->get_id())->save();
        $sinkevent = $this->redirectEvents();
        $sinkemail = $this->redirectEmails();

        $sinkevent->clear();
        $sinkemail->clear();
        $signup = (signup::create($user->id, $event))->save()->switch_state(booked::class);
        $events = $sinkevent->get_events();
        $this->assertCount(2, $events);
        $this->assertInstanceOf(signup_status_updated::class, $events[0]);
        $this->assertInstanceOf(booking_booked::class, $events[1]);
        self::dispatch_events($events);
        $this->execute_adhoc_tasks();
        $messages = $sinkemail->get_messages();
        $this->assertCount(1, $messages);

        $session->set_timestart($now + $timediff)->set_timefinish($now + $timediff + HOURSECS * 3)->set_sessionid($event->get_id())->save();

        $sinkevent->clear();
        $sinkemail->clear();
        $result = signup_helper::process_attendance($event, [$signup->get_id() => fully_attended::get_code()], [$signup->get_id() => null]);
        $events = $sinkevent->get_events();
        $this->assertTrue($result);
        $this->assertCount(1, $events);
        $this->assertInstanceOf(signup_status_updated::class, $events[0]);
        self::dispatch_events($events);
        $this->execute_adhoc_tasks();
        $messages = $sinkemail->get_messages();
        $this->assertCount(0, $messages);

        $sinkevent->clear();
        $sinkemail->clear();
        $result = signup_helper::process_attendance($event, [$signup->get_id() => booked::get_code()], [$signup->get_id() => null]);
        $events = $sinkevent->get_events();
        $this->assertTrue($result);
        $this->assertCount(2, $events);
        $this->assertInstanceOf(signup_status_updated::class, $events[0]);
        $this->assertInstanceOf(booking_booked::class, $events[1]);
        self::dispatch_events($events);
        $this->execute_adhoc_tasks();
        $messages = $sinkemail->get_messages();
        $this->assertCount(0, $messages);
    }

    /**
     * Simulate dispatching events.
     * @param \core\event\base[] $events
     */
    private static function dispatch_events(array $events) {
        foreach ($events as $event) {
            $disp = new ReflectionProperty(\core\event\base::class, 'dispatched');
            $disp->setAccessible(true);
            $disp->setValue($event, false);
            \core\event\manager::dispatch($event);
            $disp->setValue($event, true);
        }
    }

    public function test_process_attendance() {
        global $CFG;
        require_once($CFG->dirroot . '/lib/gradelib.php');

        $gen = $this->getDataGenerator();
        $course = $gen->create_course();

        $f2fgen = $gen->get_plugin_generator('mod_facetoface');
        $f2f = $f2fgen->create_instance(['course' => $course->id]);

        $seminar = (new seminar($f2f->id))
            ->set_sessionattendance(0)
            ->set_multisignupfully(true)
            ->set_multisignuppartly(true)
            ->set_multiplesessions(1)
            ->set_multisignupmaximum(2)
            ->set_eventgradingmethod(0)
            ->set_eventgradingmanual(1)
            ->save();

        $user1 = $gen->create_user();
        $user2 = $gen->create_user();
        $gen->enrol_user($user1->id, $course->id);
        $gen->enrol_user($user2->id, $course->id);

        $now = time();
        $event1 = new seminar_event();
        $event1->set_facetoface($f2f->id)->save();
        $session1 = new seminar_session();
        $session1->set_timestart($now + DAYSECS)->set_timefinish($now + DAYSECS + HOURSECS)->set_sessionid($event1->get_id())->save();

        $event2 = new seminar_event();
        $event2->set_facetoface($f2f->id)->save();
        $session2 = new seminar_session();
        $session2->set_timestart($now + DAYSECS * 2)->set_timefinish($now + DAYSECS * 2 + HOURSECS)->set_sessionid($event2->get_id())->save();

        $signup11 = (signup::create($user1->id, $event1))->save()->switch_state(booked::class);
        $signup21 = (signup::create($user2->id, $event1))->save()->switch_state(booked::class);
        $signup21->delete();

        $session1->set_timestart($now - DAYSECS * 2)->set_timefinish($now - DAYSECS * 2 + HOURSECS)->save();

        // Attendance
        $result = signup_helper::process_attendance($event1, [ $signup11->get_id() => fully_attended::get_code() ]);
        $this->assertTrue($result);
        $this->assertInstanceOf(fully_attended::class, $signup11->get_state());

        $signup12 = (signup::create($user1->id, $event2))->save()->switch_state(booked::class);
        $session2->set_timestart($now - DAYSECS)->set_timefinish($now - DAYSECS + HOURSECS)->save();

        // Wrong event: silently fails so far
        $result = signup_helper::process_attendance($event2, [ $signup11->get_id() => partially_attended::get_code() ]);
        $this->assertFalse($result);
        $this->assertInstanceOf(fully_attended::class, $signup11->get_state());

        // Attendance
        $result = signup_helper::process_attendance($event2, [ $signup12->get_id() => partially_attended::get_code() ]);
        $this->assertTrue($result);
        $this->assertInstanceOf(fully_attended::class, $signup11->get_state());
        $this->assertInstanceOf(partially_attended::class, $signup12->get_state());

        // Wrong event: silently fails so far
        $result = signup_helper::process_attendance($event1, [ $signup12->get_id() => unable_to_attend::get_code() ]);
        $this->assertFalse($result);
        $this->assertInstanceOf(fully_attended::class, $signup11->get_state());
        $this->assertInstanceOf(partially_attended::class, $signup12->get_state());

        // Ghost signup: silently fails so far
        $result = signup_helper::process_attendance($event1, [ $signup21->get_id() => fully_attended::get_code() ]);
        $this->assertFalse($result);

        // Ghost signup & wrong event: silently fails so far
        $result = signup_helper::process_attendance($event2, [ $signup21->get_id() => fully_attended::get_code() ]);
        $this->assertFalse($result);

        // Attendance & event grade
        $result = signup_helper::process_attendance($event1, [ $signup11->get_id() => no_show::get_code() ], [ $signup11->get_id() => 42]);
        $this->assertTrue($result);
        $grade = signup_status::from_current($signup11)->get_grade();
        $this->assertSame(42., grade_floatval($grade));

        // Attendance & wrong event grade: throws coding_exception
        try {
            signup_helper::process_attendance($event2, [ $signup12->get_id() => no_show::get_code() ], [ $signup11->get_id() => 42]);
            $this->fail('coding_exception expected');
        } catch (\coding_exception $ex) {
        }
        $grade = signup_status::from_current($signup11)->get_grade();
        $this->assertSame(42., grade_floatval($grade));
    }
}
