<?php

use mod_facetoface\seminar;
use mod_facetoface\signup;
use mod_facetoface\seminar_event;
use mod_facetoface\seminar_session;
use mod_facetoface\signup_helper;
use mod_facetoface\signup\state\requestedrole;
use mod_facetoface\signup\state\requested;
use mod_facetoface\signup\state\booked;
use mod_facetoface\signup\state\fully_attended;
use mod_facetoface\signup\state\partially_attended;
use mod_facetoface\signup\state\no_show;
use mod_facetoface\signup\state\unable_to_attend;

/*
 * This file is part of Totara LMS
 *
 * Copyright (C) 2016 onwards Totara Learning Solutions LTD
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
 * @author  Valerii Kuznetsov <valerii.kuznetsov@totaralearning.com>
 * @package mod_facetoface
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot.'/mod/facetoface/db/upgradelib.php');

/**
 * Test facetoface upgradelib related functions
 */
class mod_facetoface_upgradelib_testcase extends advanced_testcase {
    /**
     * Test facetoface_upgradelib_managerprefix_clarification()
     */
    public function test_facetoface_upgradelib_managerprefix_clarification() {
        global $DB;
        $this->resetAfterTest();

        // Prepate data.
        $tpl_cancellation = $DB->get_record('facetoface_notification_tpl', array('reference' => 'cancellation'));
        $tpl_cancellation->managerprefix = text_to_html(get_string('setting:defaultcancellationinstrmngrdefault', 'facetoface') . "test");
        $DB->update_record('facetoface_notification_tpl', $tpl_cancellation);

        $tpl_reminder = $DB->get_record('facetoface_notification_tpl', array('reference' => 'reminder'));
        $tpl_reminder->managerprefix = text_to_html(get_string('setting:defaultreminderinstrmngrdefault', 'facetoface'));
        $DB->update_record('facetoface_notification_tpl', $tpl_reminder);

        $tpl_request = new stdClass();
        $tpl_request->status = 1;
        $tpl_request->title =  "Test title 3";
        $tpl_request->type =  4;
        $tpl_request->courseid =  1;
        $tpl_request->facetofaceid =  1;
        $tpl_request->courseid =  1;
        $tpl_request->templateid =  1;
        $tpl_request->body = text_to_html(get_string('setting:defaultrequestmessagedefault_v9', 'facetoface'));
        $tpl_request->managerprefix = text_to_html(get_string('setting:defaultrequestinstrmngrdefault', 'facetoface'));
        $DB->insert_record('facetoface_notification', $tpl_request);

        $tpl_rolerequest = new stdClass();
        $tpl_rolerequest->status = 1;
        $tpl_rolerequest->title =  "Test title 4";
        $tpl_rolerequest->type =  4;
        $tpl_rolerequest->courseid =  1;
        $tpl_rolerequest->facetofaceid =  1;
        $tpl_rolerequest->courseid =  1;
        $tpl_rolerequest->templateid =  1;
        $tpl_rolerequest->body = text_to_html(get_string('setting:defaultrolerequestmessagedefault_v9', 'facetoface'));
        $tpl_rolerequest->managerprefix = text_to_html("test".get_string('setting:defaultrolerequestinstrmngrdefault', 'facetoface'));
        $DB->insert_record('facetoface_notification', $tpl_rolerequest);

        // Do upgrade.
        facetoface_upgradelib_managerprefix_clarification();

        // Check that changed strings are not updated.
        $cancellation = $DB->get_field('facetoface_notification_tpl', 'managerprefix', array('reference' => 'cancellation'));
        $cancellationexp = text_to_html(get_string('setting:defaultcancellationinstrmngrdefault', 'facetoface') . "test");
        $this->assertEquals($cancellationexp, $cancellation);

        $rolerequest = $DB->get_field_select('facetoface_notification', 'managerprefix',
            $DB->sql_compare_text('title') . ' = :title', array('title' => 'Test title 4'));
        $rolerequestexp = text_to_html("test" . get_string('setting:defaultrolerequestinstrmngrdefault', 'facetoface'));
        $this->assertEquals($rolerequestexp, $rolerequest);

        // Check that not changed string are updated.
        $reminder = $DB->get_field('facetoface_notification_tpl', 'managerprefix', array('reference' => 'reminder'));
        $reminderexp = text_to_html(get_string('setting:defaultreminderinstrmngrdefault_v92', 'facetoface'));
        $this->assertEquals($reminderexp, $reminder);

        $request = $DB->get_field_select('facetoface_notification', 'managerprefix',
            $DB->sql_compare_text('title') . ' = :title', array('title' => 'Test title 3'));
        $requestexp = text_to_html(get_string('setting:defaultrequestinstrmngrdefault_v92', 'facetoface'));
        $this->assertEquals($requestexp, $request);
    }


    private function verifySessionDate($event, $timestart, $timeduration, $visible) {
        $this->assertEquals($timestart, $event->timestart);
        $this->assertEquals($timeduration, $event->timeduration);
        $this->assertEquals($visible, $event->visible);
    }

    public function test_facetoface_upgradelib_calendar_events_for_sessiondates() {
        global $DB;
        $this->resetAfterTest();

        // Setup the data
        $facetofacegenerator = $this->getDataGenerator()->get_plugin_generator('mod_facetoface');
        $course = $this->getDataGenerator()->create_course();
        $facetoface = $this->getDataGenerator()->create_module('facetoface', array('course' => $course->id));
        $context = context_module::instance($facetoface->cmid);

        $this->setAdminUser();

        $now = time();
        $sessiondates = array();
        for ($i = 0; $i < 3; $i++) {
            $sessiondates[$i] = new stdClass();
            $sessiondates[$i]->timestart = $now + $i * WEEKSECS;
            $sessiondates[$i]->timefinish = $sessiondates[$i]->timestart + 3 * HOURSECS;
            $sessiondates[$i]->sessiontimezone = '99';
            $sessiondates[$i]->assetids = array();
        }
        $sid = $facetofacegenerator->add_session(array('facetoface' => $facetoface->id, 'sessiondates' => $sessiondates));

        // We still need to add the calendar entries.
        $seminarevent = new \mod_facetoface\seminar_event($sid);
        \mod_facetoface\calendar::update_entries($seminarevent);

        $events = $DB->get_records('event', array('modulename' => 'facetoface', 'eventtype' => 'facetofacesession', 'courseid' => $course->id),
            'timestart');

        $this->assertEquals(3, count($events));
        for ($i = 0; $i < 3; $i++) {
            $event = array_shift($events);
            $this->verifySessionDate($event, $sessiondates[$i]->timestart, 3 * HOURSECS, 1);
        }

        // The database is now in the correct state that
        // First test that facetoface_upgradelib_calendar_events_for_sessiondates
        // doesn't braeak this

        facetoface_upgradelib_calendar_events_for_sessiondates();

        $events = $DB->get_records('event', array('modulename' => 'facetoface', 'eventtype' => 'facetofacesession', 'courseid' => $course->id),
            'timestart');

        $ids = array();
        $this->assertEquals(3, count($events));
        for ($i = 0; $i < 3; $i++) {
            $event = array_shift($events);
            $this->verifySessionDate($event, $sessiondates[$i]->timestart, 3 * HOURSECS, 1);

            if ($i < 2) {
                $ids[] = $event->id;
            }
        }

        // Now remove all but one of the events to simulate the state prior to this patch
        $sql = 'DELETE FROM {event} WHERE id in (' . implode(',', $ids) . ')';
        $DB->execute($sql);

        // Verify
        $events = $DB->get_records('event', array('modulename' => 'facetoface', 'eventtype' => 'facetofacesession', 'courseid' => $course->id),
            'timestart');

        $this->assertEquals(1, count($events));
        $event = array_shift($events);
        $this->verifySessionDate($event, $sessiondates[2]->timestart, 3 * HOURSECS, 1);

        // Now verify that facetoface_upgradelib_calendar_events_for_sessiondates restores the events

        facetoface_upgradelib_calendar_events_for_sessiondates();

        $events = $DB->get_records('event', array('modulename' => 'facetoface', 'eventtype' => 'facetofacesession', 'courseid' => $course->id),
            'timestart');

        $this->assertEquals(3, count($events));
        for ($i = 0; $i < 3; $i++) {
            $event = array_shift($events);
            $this->verifySessionDate($event, $sessiondates[$i]->timestart, 3 * HOURSECS, 1);
        }
    }

    public function test_facetoface_upgradelib_requestedrole_state_for_role_approval() {
        global $DB;
        $this->resetAfterTest();

        $gen = $this->getDataGenerator();
        $course = $gen->create_course([], ['createsections' => true]);

        /** @var mod_facetoface_generator $f2fgen */
        $f2fgen = $gen->get_plugin_generator('mod_facetoface');
        $f2f = $f2fgen->create_instance(['course' => $course->id, 'approvaltype' => seminar::APPROVAL_ROLE]);

        $seminarevent = new seminar_event();
        $seminarevent->set_facetoface($f2f->id)->save();

        $time = time();
        $seminarsession = new seminar_session();
        $seminarsession->set_sessionid($seminarevent->get_id())
            ->set_timestart($time + HOURSECS)
            ->set_timefinish($time + HOURSECS * 2)
            ->save();

        $seminar = new seminar($f2f->id);

        $roleapprover1 = $gen->create_user();
        $trainerrole = $DB->get_record('role', array('shortname' => 'teacher'));
        $approvalrole = $trainerrole->id;
        $DB->set_field('facetoface', 'approvalrole', $approvalrole, ['id' => $seminar->get_id()]);
        $DB->insert_record('facetoface_session_roles', (object)['sessionid' => $seminarevent->get_id(), 'roleid' => $approvalrole, 'userid' => $roleapprover1->id]);

        $student = $gen->create_user();
        $gen->enrol_user($student->id, $seminar->get_course());

        $signup = new signup();
        $signup->set_userid($student->id)->set_sessionid($seminarevent->get_id());
        $signup->save();

        signup_helper::signup($signup);

        if ($signup->get_state() instanceof requestedrole) {
            // simulate old behaviour where role approval goes to requested state
            $DB->execute('UPDATE {facetoface_signups_status} SET statuscode = :rq WHERE signupid = :sid', [ 'sid' => $signup->get_id(), 'rq' => requested::get_code() ]);
            $signup->load();
        }

        $this->assertInstanceOf(requested::class, $signup->get_state());
        facetoface_upgradelib_requestedrole_state_for_role_approval();
        $signup->load();
        $this->assertInstanceOf(requestedrole::class, $signup->get_state());
    }

    /**
     * Convert string grade to float grade if necessary.
     *
     * @param mixed $grade
     * @return float|null
     */
    private function fixup_grade($grade) : ?float {
        if (is_null($grade)) {
            return null;
        }
        if (is_float($grade)) {
            return $grade;
        }
        if (is_string($grade)) {
            if ($grade === '') {
                return null;
            } else if (is_numeric($grade)) {
                return (float)$grade;
            }
        }
        $this->fail("'{$grade}' is neither numeric string, float nor null");
    }

    /**
     * @return array
     */
    public function data_fake_signups_status() {
        return [
            [ requested::get_code(), null, null ],
            [ booked::get_code(), null, null ],
            [ fully_attended::get_code(), fully_attended::get_grade(), fully_attended::get_grade() ],
            [ partially_attended::get_code(), partially_attended::get_grade(), partially_attended::get_grade() ],
            [ unable_to_attend::get_code(), unable_to_attend::get_grade(), unable_to_attend::get_grade() ],
            [ no_show::get_code(), no_show::get_grade(), no_show::get_grade() ],
            [ 0, null, null ],
            [ 999, 0, 0 ],
        ];
    }

    /**
     * @param int $statuscode
     * @param float|null $expectedgrade_first
     * @param float|null $expectedgrade_last
     * @dataProvider data_fake_signups_status
     */
    public function test_facetoface_upgradelib_fixup_seminar_grades(int $statuscode, ?float $expectedgrade_first, ?float $expectedgrade_last) {
        global $DB;
        /** @var \moodle_database $DB */
        $this->resetAfterTest();

        // simulate buggy situation by manually inserting database records
        // note that the code doesn't set correct foreign keys and timecreated
        $first = $DB->insert_record('facetoface_signups_status', [
            'statuscode' => $statuscode,
            'superceded' => 1,
            'grade' => 0.000,
            'signupid' => 99999,
            'createdby' => 77777,
            'timecreated' => 88888,
        ]);
        $last = $DB->insert_record('facetoface_signups_status', [
            'statuscode' => $statuscode,
            'superceded' => 0,
            'grade' => 0.000,
            'signupid' => 99999,
            'createdby' => 77777,
            'timecreated' => 88888,
        ]);

        facetoface_upgradelib_fixup_seminar_grades();
        $first_grade = $this->fixup_grade($DB->get_field('facetoface_signups_status', 'grade', [ 'id' => $first ], MUST_EXIST));
        $last_grade = $this->fixup_grade($DB->get_field('facetoface_signups_status', 'grade', [ 'id' => $last ], MUST_EXIST));

        $this->assertSame($expectedgrade_first, $first_grade);
        $this->assertSame($expectedgrade_last, $last_grade);
    }

    /**
     * @return array
     */
    public function data_fake_signups_status_with_grade() {
        $any = 42;
        return [
            [ requested::get_code(), $any, null, null ],
            [ booked::get_code(), $any, null, null ],
            [ fully_attended::get_code(), $any, fully_attended::get_grade(), $any ],
            [ partially_attended::get_code(), $any, partially_attended::get_grade(), $any ],
            [ unable_to_attend::get_code(), $any, unable_to_attend::get_grade(), $any ],
            [ no_show::get_code(), $any, no_show::get_grade(), $any ],
            [ 0, $any, null, null ],
            [ 999, $any, 0, $any ],
        ];
    }

    /**
     * @param int $statuscode
     * @param float|null $initgrade_last
     * @param float|null $expectedgrade_first
     * @param float|null $expectedgrade_last
     * @dataProvider data_fake_signups_status_with_grade
     */
    public function test_facetoface_upgradelib_fixup_seminar_grades_with_grade(int $statuscode, ?float $initgrade_last, ?float $expectedgrade_first, ?float $expectedgrade_last) {
        global $DB;
        /** @var \moodle_database $DB */
        $this->resetAfterTest();

        // simulate buggy situation by manually inserting database records
        // note that the code doesn't set correct foreign keys and timecreated
        $first = $DB->insert_record('facetoface_signups_status', [
            'statuscode' => $statuscode,
            'superceded' => 1,
            'grade' => 0.000,
            'signupid' => 99999,
            'createdby' => 77777,
            'timecreated' => 88888,
        ]);
        $last = $DB->insert_record('facetoface_signups_status', [
            'statuscode' => $statuscode,
            'superceded' => 0,
            'grade' => $initgrade_last,
            'signupid' => 99999,
            'createdby' => 77777,
            'timecreated' => 88888,
        ]);

        facetoface_upgradelib_fixup_seminar_grades();
        $first_grade = $this->fixup_grade($DB->get_field('facetoface_signups_status', 'grade', [ 'id' => $first ], MUST_EXIST));
        $last_grade = $this->fixup_grade($DB->get_field('facetoface_signups_status', 'grade', [ 'id' => $last ], MUST_EXIST));

        $this->assertSame($expectedgrade_first, $first_grade);
        $this->assertSame($expectedgrade_last, $last_grade);
    }

    /**
     * @return array of [ , attendancetime ]
     */
    public function data_obsolete_attendance_values() {
        return [
            [ 0, seminar::ATTENDANCE_TIME_END, seminar::SESSION_ATTENDANCE_DISABLED ],
            [ 0, seminar::ATTENDANCE_TIME_START, seminar::SESSION_ATTENDANCE_DISABLED ],
            [ 0, seminar::ATTENDANCE_TIME_ANY, seminar::SESSION_ATTENDANCE_DISABLED ],
            [ 0, 42, seminar::SESSION_ATTENDANCE_DISABLED ],
            [ 1, seminar::ATTENDANCE_TIME_END, seminar::SESSION_ATTENDANCE_END ],
            [ 1, seminar::ATTENDANCE_TIME_START, seminar::SESSION_ATTENDANCE_START ],
            [ 1, seminar::ATTENDANCE_TIME_ANY, seminar::SESSION_ATTENDANCE_UNRESTRICTED ],
            [ 1, 42, seminar::SESSION_ATTENDANCE_DISABLED ],
            // The shim takes effect only if sessionattendance is 0 or 1.
            [ 42, seminar::ATTENDANCE_TIME_END, 42 ],
            [ 42, seminar::ATTENDANCE_TIME_START, 42 ],
            [ 42, seminar::ATTENDANCE_TIME_ANY, 42 ],
            [ 42, 42, 42 ],
        ];
    }

    /**
     * @param int $sessionattendance
     * @param int $attendancetime
     * @dataProvider data_obsolete_attendance_values
     */
    public function test_facetoface_upgradelib_fixup_seminar_sessionattendance(int $sessionattendance, int $attendancetime, int $newsessionattendance) {
        $gen = $this->getDataGenerator();
        $course = $gen->create_course();

        /** @var mod_facetoface_generator $f2fgen */
        $f2fgen = $gen->get_plugin_generator('mod_facetoface');
        $this->assertDebuggingNotCalled();
        $f2f = $f2fgen->create_instance(['course' => $course->id, 'sessionattendance' => $sessionattendance, 'attendancetime' => $attendancetime]);
        $this->resetDebugging(); // Just swallow gracious debugging messages here

        set_config('sessionattendance', $sessionattendance, 'facetoface');
        set_config('attendancetime', $attendancetime, 'facetoface');

        facetoface_upgradelib_fixup_seminar_sessionattendance();

        $seminar = new seminar($f2f->id);
        $this->assertSame($newsessionattendance, $seminar->get_sessionattendance());

        $globalsessionattendance = get_config('facetoface', 'sessionattendance');
        $globalattendancetime = get_config('facetoface', 'attendancetime');
        $this->assertNotFalse($globalsessionattendance);
        $this->assertNotFalse($globalattendancetime);
        $this->assertEquals($newsessionattendance, $globalsessionattendance);
        $this->assertEquals($attendancetime, $globalattendancetime);
    }

    public function test_facetoface_upgradelib_delete_orphaned_events() {
        global $DB;

        $gen = $this->getDataGenerator();
        $course = $gen->create_course();

        /** @var mod_facetoface_generator $f2fgen */
        $f2fgen = $gen->get_plugin_generator('mod_facetoface');
        $this->assertDebuggingNotCalled();
        $f2f = $f2fgen->create_instance(['course' => $course->id]);
        $this->resetDebugging(); // Just swallow gracious debugging messages here

        $this->setAdminUser();

        $now = time();

        // Add two seminar events
        for ($s = 1; $s < 3; $s++) {
            $sessiondates = array();
            for ($i = 0; $i < 3; $i++) {
                $sessiondates[$i] = new stdClass();
                $sessiondates[$i]->timestart = $now + $i * WEEKSECS;
                $sessiondates[$i]->timefinish = $sessiondates[$i]->timestart + 3 * HOURSECS;
                $sessiondates[$i]->sessiontimezone = '99';
                $sessiondates[$i]->assetids = array();
            }
            ${'sid' . $s} = $f2fgen->add_session(array('facetoface' => $f2f->id, 'sessiondates' => $sessiondates));

            // We still need to add the calendar entries.
            ${'seminarevent' . $s} = new \mod_facetoface\seminar_event(${'sid' . $s});
            \mod_facetoface\calendar::update_entries(${'seminarevent' . $s});
        }

        $events = $DB->get_records('event', array('modulename' => 'facetoface'),'timestart');

        // Each seminar event adds 6 dates to the calendar (three course, three user)
        $this->assertEquals(12, count($events));

        // Hack-delete the second seminar event
        $DB->delete_records('facetoface_sessions', ['id' => $sid2]);

        // Run the upgrade query to delete the orphaned calendar events
        facetoface_upgradelib_delete_orphaned_events();

        // Make sure only the orphaned 6 events were deleted
        $events = $DB->get_records('event', array('modulename' => 'facetoface'),'timestart');
        $this->assertEquals(6, count($events));
    }

    /**
     * @return array
     */
    public function data_upgradelib_add_new_template(): array {
        $longtext1 = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean tempor sed metus quis porta. Sed volutpat arcu eget nibh ultricies ultricies. Sed ac ligula enim. Ut posuere scelerisque lacus. Aliquam cursus leo dui, sit amet viverra velit lobortis non. Sed quis ullamcorper leo.';
        $longtext2 = "\u{1D576}\u{1D58E}\u{1D586}\u{1D57A}\u{1D597}\u{1D586}\u{1D576}\u{1D594}\u{1D59A}\u{1D599}\u{1D594}\u{1D59A}\u{1D576}\u{1D586}\u{1D599}\u{1D594}\u{1D586}\u{1D4D0}\u{1D4F8}\u{1D4FD}\u{1D4EE}\u{1D4EA}\u{1D4FB}\u{1D4F8}\u{1D4EA}\u{1D55F}\u{1D556}\u{1D568}\u{1D56B}\u{1D556}\u{1D552}\u{1D55D}\u{1D552}\u{1D55F}\u{1D555}";
        return [
            ['New template title', "Lorem ipsum\r\ndolor sit amet", 'New template title', "<div class=\"text_to_html\">Lorem ipsum<br />\r\ndolor sit amet</div>"],
            [$longtext1, 'Lorem ipsum', substr($longtext1, 0, 255), '<div class="text_to_html">Lorem ipsum</div>'],
            [$longtext2, 'Lorem ipsum', $longtext2, '<div class="text_to_html">Lorem ipsum</div>'],
        ];
    }

    /**
     * @param string $reference
     * @param string $title
     * @param string $body
     * @param string $expected_title
     * @param string $expected_body
     * @dataProvider data_upgradelib_add_new_template
     */
    public function test_facetoface_upgradelib_add_new_template(string $title, string $body, string $expected_title, string $expected_body) {
        global $DB, $CFG;
        /** @var moodle_database $DB */
        require_once($CFG->dirroot.'/mod/facetoface/lib.php');

        $conditiontype = 1 << 30;
        if ($DB->record_exists('facetoface_notification', ['conditiontype' => $conditiontype])) {
            $this->fail('Change the conditiontype to a value that has not been taken!!');
        }

        $gen = $this->getDataGenerator();
        $f2fgen = $gen->get_plugin_generator('mod_facetoface');
        /** @var mod_facetoface_generator $f2fgen */
        $course = $gen->create_course();
        $f2f = $f2fgen->create_instance(['course' => $course->id]);
        facetoface_upgradelib_add_new_template('kiaorakoutoukatoa', $title, $body, $conditiontype);

        $tpl = $DB->get_record('facetoface_notification_tpl', ['reference' => 'kiaorakoutoukatoa']);
        $this->assertNotEmpty($tpl, 'DB record does not exist');
        $this->assertEquals(1, $tpl->status);
        $this->assertEquals(0, $tpl->ccmanager);
        $this->assertEquals($expected_title, $tpl->title);
        $this->assertEquals($expected_body, $tpl->body);

        $notifs = $DB->get_records('facetoface_notification', ['facetofaceid' => $f2f->id, 'templateid' => $tpl->id]);
        $this->assertNotEmpty($notifs, 'DB record does not exist');
        $this->assertCount(1, $notifs);
        $notif = reset($notifs);
        $this->assertEquals(MDL_F2F_NOTIFICATION_AUTO, $notif->type);
        $this->assertEquals($conditiontype, $notif->conditiontype);
        $this->assertEquals($course->id, $notif->courseid);
        $this->assertEquals($expected_title, $notif->title);
        $this->assertEquals($expected_body, $notif->body);
    }
}
