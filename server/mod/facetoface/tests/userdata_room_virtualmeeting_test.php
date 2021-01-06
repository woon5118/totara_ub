<?php
/*
* This file is part of Totara Learn
*
* Copyright (C) 2021 onwards Totara Learning Solutions LTD
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
* @author Oleg Demeshev <oleg.demeshev@totaralearning.com>
* @package mod_facetoface
*/

use mod_facetoface\seminar_event;
use mod_facetoface\userdata\room_virtualmeeting;
use totara_userdata\userdata\item;
use totara_userdata\userdata\target_user;
use totara_core\virtualmeeting\virtual_meeting as virtualmeeting_model;

defined('MOODLE_INTERNAL') || die();

class mod_facetoface_userdata_room_virtualmeeting_testcase extends advanced_testcase {

    private $user1;
    private $user2;
    private $f2f;
    private $event1;
    private $sitewide_room;
    private $custom_room;
    private $virtual_room1;
    private $virtual_room2;
    private $session1start;
    private $virtualmeeting1;
    private $virtualmeeting2;

    public function setUp(): void {
        $this->user1 = $this->getDataGenerator()->create_user(['username' => 'bob']);
        $this->user2 = $this->getDataGenerator()->create_user(['username' => 'ann']);
        $course = $this->getDataGenerator()->create_course();

        $this->setUser($this->user1);
        $seminar_generator = $this->getDataGenerator()->get_plugin_generator('mod_facetoface');
        $this->f2f = $seminar_generator->create_instance([
            'name' => 'Test seminar',
            'course' => $course->id
        ]);
        $this->sitewide_room = $seminar_generator->add_site_wide_room(['name' => 'just a room']);
        $this->custom_room = $seminar_generator->add_custom_room(['name' => 'room with url', 'url' => 'https://example.com']);
        $this->virtual_room1 = $seminar_generator->add_virtualmeeting_room(['name' => 'vroom1']);
        $this->virtual_room2 = $seminar_generator->add_virtualmeeting_room(['name' => 'vroom2']);
        $session1start = time() + 3600;
        $session1finish = time() + 5400;
        $session2start = time() + 7200;
        $session2finish = time() + 9000;
        $this->event1 = $seminar_generator->add_session([
            'facetoface' => $this->f2f->id,
            'sessiondates' => [
                [
                    'timestart' => $session1start,
                    'timefinish' => $session1finish,
                    'sessiontimezone' => 'Pacific/Auckland',
                    'roomids' => [$this->virtual_room1->id]
                ],
                [
                    'timestart' => $session2start,
                    'timefinish' => $session2finish,
                    'sessiontimezone' => 'Pacific/Auckland',
                    'roomids' => [$this->virtual_room2->id]
                ],
            ],
        ]);
        // Create virtualmeeting instances up front
        $this->virtualmeeting1 = virtualmeeting_model::create(
            'poc_app',
            $this->user1,
            'Test seminar',
            DateTime::createFromFormat('U', $session1start),
            DateTime::createFromFormat('U', $session1finish)
        );
        $this->virtualmeeting2 = virtualmeeting_model::create(
            'poc_app',
            $this->user1,
            'Test seminar',
            DateTime::createFromFormat('U', $session2start),
            DateTime::createFromFormat('U', $session2finish)
        );
        $seminar_event = new seminar_event($this->event1);
        $sessions = $seminar_event->get_sessions();
        foreach($sessions as $session) {
            if ($session->get_timestart() == $session1start) {
                $seminar_generator->create_room_dates_virtualmeeting($this->virtual_room1->id, $session->get_id(), $this->virtualmeeting1->get_id());
            } else {
                $seminar_generator->create_room_dates_virtualmeeting($this->virtual_room2->id, $session->get_id(), $this->virtualmeeting2->get_id());
            }
        }
        // Keep track of session1
        $this->session1start = $session1start;
    }

    public function tearDown(): void {
        $this->user1 = null;
        $this->user2 = null;
        $this->f2f = null;
        $this->event1 = null;
        $this->sitewide_room = null;
        $this->custom_room = null;
        $this->virtual_room1 = null;
        $this->virtual_room2 = null;
        $this->virtualmeeting1 = null;
        $this->virtualmeeting2 = null;
        $this->session1start = null;
        parent::tearDown();
    }

    /**
     * Test count.
     */
    public function test_count() {
        $targetuser1 = new target_user($this->user1);
        $targetuser2 = new target_user($this->user2);

        // System context
        $this->assertEquals(2, room_virtualmeeting::execute_count($targetuser1, context_system::instance()));
        $this->assertEquals(0, room_virtualmeeting::execute_count($targetuser2, context_system::instance()));

        // Module context
        $coursemodule = get_coursemodule_from_instance('facetoface', $this->f2f->id);
        $modulecontext = context_module::instance($coursemodule->id);
        $this->assertEquals(2, room_virtualmeeting::execute_count($targetuser1, $modulecontext));
        $this->assertEquals(0, room_virtualmeeting::execute_count($targetuser2, $modulecontext));

        // Course context
        $coursemodule = get_coursemodule_from_instance('facetoface', $this->f2f->id);
        $coursecontext = context_course::instance($coursemodule->course);
        $this->assertEquals(2, room_virtualmeeting::execute_count($targetuser1, $coursecontext));
        $this->assertEquals(0, room_virtualmeeting::execute_count($targetuser2, $coursecontext));

        // Course category context
        $coursemodule = get_coursemodule_from_instance('facetoface', $this->f2f->id);
        $course = get_course($coursemodule->course);
        $coursecatcontext = context_coursecat::instance($course->category);
        $this->assertEquals(2, room_virtualmeeting::execute_count($targetuser1, $coursecatcontext));
        $this->assertEquals(0, room_virtualmeeting::execute_count($targetuser2, $coursecatcontext));
    }

    /**
     * Test export.
     */
    public function test_export() {
        $targetuser1 = new target_user($this->user1);
        $targetuser2 = new target_user($this->user2);

        // System content.
        $export = room_virtualmeeting::execute_export($targetuser1, context_system::instance());
        $data = $export->data;
        $this->assertCount(2, $data);

        $record = array_shift($data);
        $this->assertEquals($targetuser1->id, $record->userid);
        $this->assertEquals('vroom1', $record->name);
        $this->assertEquals('poc_app', $record->plugin);
        $this->assertNotEmpty($record->description);

        // Module context
        $coursemodule = get_coursemodule_from_instance('facetoface', $this->f2f->id);
        $modulecontext = context_module::instance($coursemodule->id);
        $export = room_virtualmeeting::execute_export($targetuser1, $modulecontext);
        $data = $export->data;
        $this->assertCount(2, $data);

        $record = array_shift($data);
        $this->assertEquals($targetuser1->id, $record->userid);
        $this->assertEquals('vroom1', $record->name);
        $this->assertEquals('poc_app', $record->plugin);
        $this->assertNotEmpty($record->description);

        // Course context
        $coursemodule = get_coursemodule_from_instance('facetoface', $this->f2f->id);
        $coursecontext = context_course::instance($coursemodule->course);
        $export = room_virtualmeeting::execute_export($targetuser1, $coursecontext);
        $data = $export->data;
        $this->assertCount(2, $data);

        $record = array_shift($data);
        $this->assertEquals($targetuser1->id, $record->userid);
        $this->assertEquals('vroom1', $record->name);
        $this->assertEquals('poc_app', $record->plugin);
        $this->assertNotEmpty($record->description);

        // Course category context
        $coursemodule = get_coursemodule_from_instance('facetoface', $this->f2f->id);
        $course = get_course($coursemodule->course);
        $coursecatcontext = context_coursecat::instance($course->category);
        $export = room_virtualmeeting::execute_export($targetuser1, $coursecatcontext);
        $data = $export->data;
        $this->assertCount(2, $data);

        $record = array_shift($data);
        $this->assertEquals($targetuser1->id, $record->userid);
        $this->assertEquals('vroom1', $record->name);
        $this->assertEquals('poc_app', $record->plugin);
        $this->assertNotEmpty($record->description);

        $export = room_virtualmeeting::execute_export($targetuser2, context_system::instance());
        $data = $export->data;
        $this->assertEmpty($data);
    }

    public function test_purge_context_system() {
        global $DB;

        $targetuser2 = new target_user($this->user2);
        $status = room_virtualmeeting::execute_purge($targetuser2, context_system::instance());

        $this->assertEquals(item::RESULT_STATUS_SUCCESS, $status);
        $this->assertCount(2, $DB->get_records('facetoface_room_virtualmeeting', ['userid' => $this->user1->id]));
        $this->assertCount(0, $DB->get_records('facetoface_room_virtualmeeting', ['userid' => $this->user2->id]));

        $targetuser1 = new target_user($this->user1);
        $status = room_virtualmeeting::execute_purge($targetuser1, context_system::instance());

        $this->assertEquals(item::RESULT_STATUS_SUCCESS, $status);
        $this->assertCount(0, $DB->get_records('facetoface_room_virtualmeeting', ['userid' => $this->user1->id]));
        $this->assertCount(0, $DB->get_records('facetoface_room_virtualmeeting', ['userid' => $this->user2->id]));
    }

    public function test_purge_context_module() {
        global $DB;

        $coursemodule = get_coursemodule_from_instance('facetoface', $this->f2f->id);
        $modulecontext = context_module::instance($coursemodule->id);

        $targetuser2 = new target_user($this->user2);
        $status = room_virtualmeeting::execute_purge($targetuser2, $modulecontext);

        $this->assertEquals(item::RESULT_STATUS_SUCCESS, $status);
        $this->assertCount(2, $DB->get_records('facetoface_room_virtualmeeting', ['userid' => $this->user1->id]));
        $this->assertCount(0, $DB->get_records('facetoface_room_virtualmeeting', ['userid' => $this->user2->id]));

        $targetuser1 = new target_user($this->user1);
        $status = room_virtualmeeting::execute_purge($targetuser1, $modulecontext);

        $this->assertEquals(item::RESULT_STATUS_SUCCESS, $status);
        $this->assertCount(0, $DB->get_records('facetoface_room_virtualmeeting', ['userid' => $this->user1->id]));
        $this->assertCount(0, $DB->get_records('facetoface_room_virtualmeeting', ['userid' => $this->user2->id]));
    }

    public function test_purge_context_course() {
        global $DB;

        $coursemodule = get_coursemodule_from_instance('facetoface', $this->f2f->id);
        $coursecontext = context_course::instance($coursemodule->course);

        $targetuser2 = new target_user($this->user2);
        $status = room_virtualmeeting::execute_purge($targetuser2, $coursecontext);

        $this->assertEquals(item::RESULT_STATUS_SUCCESS, $status);
        $this->assertCount(2, $DB->get_records('facetoface_room_virtualmeeting', ['userid' => $this->user1->id]));
        $this->assertCount(0, $DB->get_records('facetoface_room_virtualmeeting', ['userid' => $this->user2->id]));

        $targetuser1 = new target_user($this->user1);
        $status = room_virtualmeeting::execute_purge($targetuser1, $coursecontext);

        $this->assertEquals(item::RESULT_STATUS_SUCCESS, $status);
        $this->assertCount(0, $DB->get_records('facetoface_room_virtualmeeting', ['userid' => $this->user1->id]));
        $this->assertCount(0, $DB->get_records('facetoface_room_virtualmeeting', ['userid' => $this->user2->id]));
    }

    public function test_purge_context_course_category() {
        global $DB;

        $coursemodule = get_coursemodule_from_instance('facetoface', $this->f2f->id);
        $course = get_course($coursemodule->course);
        $coursecatcontext = context_coursecat::instance($course->category);

        $targetuser2 = new target_user($this->user2);
        $status = room_virtualmeeting::execute_purge($targetuser2, $coursecatcontext);

        $this->assertEquals(item::RESULT_STATUS_SUCCESS, $status);
        $this->assertCount(2, $DB->get_records('facetoface_room_virtualmeeting', ['userid' => $this->user1->id]));
        $this->assertCount(0, $DB->get_records('facetoface_room_virtualmeeting', ['userid' => $this->user2->id]));

        $targetuser1 = new target_user($this->user1);
        $status = room_virtualmeeting::execute_purge($targetuser1, $coursecatcontext);

        $this->assertEquals(item::RESULT_STATUS_SUCCESS, $status);
        $this->assertCount(0, $DB->get_records('facetoface_room_virtualmeeting', ['userid' => $this->user1->id]));
        $this->assertCount(0, $DB->get_records('facetoface_room_virtualmeeting', ['userid' => $this->user2->id]));
    }
}