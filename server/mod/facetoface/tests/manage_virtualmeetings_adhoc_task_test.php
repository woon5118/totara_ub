<?php
/**
 * This file is part of Totara Learn
 *
 * Copyright (C) 2020 onwards Totara Learning Solutions LTD
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
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Chris Snyder <chris.snyder@totaralearning.com>
 * @package mod_facetoface
 */

use totara_core\virtualmeeting\virtual_meeting as virtualmeeting_model;
use mod_facetoface\seminar_event;

defined('MOODLE_INTERNAL') || die();

/**
 * Test the manage_virtualmeetings_adhoc_task task.
 * @coversDefaultClass mod_facetoface\task\manage_virtualmeetings_adhoc_task
 */
class manage_virtualmeetings_adhoc_task_testcase extends advanced_testcase {

    private $user1;
    private $user2;
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
        $f2f = $seminar_generator->create_instance([
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
            'facetoface' => $f2f->id,
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
                $seminar_generator->create_room_dates_virtualmeeting($this->virtual_room1, $session->get_id(), $this->virtualmeeting1->get_id());
            } else {
                $seminar_generator->create_room_dates_virtualmeeting($this->virtual_room2, $session->get_id(), $this->virtualmeeting2->get_id());
            }
        }
        // Keep track of session1
        $this->session1start = $session1start;
    }

    public function tearDown(): void {
        $this->user1 = null;
        $this->user2 = null;
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
     * @covers ::execute
     */
    public function test_execute_no_changes() {
        // call execute() with no changes
        // ensure nothing happens
        $url1 = $this->virtualmeeting1->get_join_url();
        $url2 = $this->virtualmeeting2->get_join_url();
        $task = \mod_facetoface\task\manage_virtualmeetings_adhoc_task::create_from_seminar_event_id($this->event1);
        $task->execute();
        $this->nothing_happened($url1, $url2);
    }

    private function nothing_happened($url1, $url2) {
        global $DB;
        // There should be two each of virtualmeetings, room_virtualmeetings, and room_dates_virtualmeetings.
        $virtualmeetings = $DB->get_records('virtualmeeting');
        $this->assertCount(2, $virtualmeetings);
        $room_virtualmeetings = $DB->get_records('facetoface_room_virtualmeeting');
        $this->assertCount(2, $room_virtualmeetings);
        $room_date_virtualmeetings = $DB->get_records('facetoface_room_dates_virtualmeeting');
        $this->assertCount(2, $room_date_virtualmeetings);
        $vm1 = \totara_core\virtualmeeting\virtual_meeting::load_by_id($this->virtualmeeting1->get_id());
        $vm2 = \totara_core\virtualmeeting\virtual_meeting::load_by_id($this->virtualmeeting2->get_id());
        $this->assertEquals($url1, $vm1->get_join_url());
        $this->assertEquals($url2, $vm2->get_join_url());
    }

    /**
     * @covers ::execute
     */
    public function test_execute_changed_resources_but_unchanged_rooms() {
        // add assets/facilitators to session #1
        // call execute()
        // ensure nothing happens
        global $DB;

        $url1 = $this->virtualmeeting1->get_join_url();
        $url2 = $this->virtualmeeting2->get_join_url();

        $seminar_generator = $this->getDataGenerator()->get_plugin_generator('mod_facetoface');
        $asset = $seminar_generator->add_custom_asset(['name' => 'an asset']);
        $facilitator = $seminar_generator->add_custom_facilitator(['name' => 'a facilitator']);
        $seminar_event = new seminar_event($this->event1);
        $sessions = $seminar_event->get_sessions();
        foreach($sessions as $session) {
            $DB->insert_record('facetoface_asset_dates', ['assetid' => $asset->id, 'sessionsdateid' => $session->get_id()]);
            $DB->insert_record('facetoface_facilitator_dates', ['facilitatorid' => $facilitator->id, 'sessionsdateid' => $session->get_id()]);
        }

        $task = \mod_facetoface\task\manage_virtualmeetings_adhoc_task::create_from_seminar_event_id($this->event1);
        $task->execute();
        $this->nothing_happened($url1, $url2);
    }

    /**
     * @covers ::execute
     */
    public function test_execute_add_a_different_room() {
        // add / remove a room from session1
        // call execute()
        // ensure nothing happens
        global $DB;

        $url1 = $this->virtualmeeting1->get_join_url();
        $url2 = $this->virtualmeeting2->get_join_url();

        $seminar_event = new seminar_event($this->event1);
        $sessions = $seminar_event->get_sessions();
        foreach($sessions as $session) {
            $DB->insert_record('facetoface_room_dates', ['roomid' => $this->custom_room->id, 'sessionsdateid' => $session->get_id()]);
        }

        $task = \mod_facetoface\task\manage_virtualmeetings_adhoc_task::create_from_seminar_event_id($this->event1);
        $task->execute();
        $this->nothing_happened($url1, $url2);
    }

    /**
     * @covers ::execute
     */
    public function test_execute_add_virtual_room_to_session() {
        // add vroom2 to session #1
        // call execute()
        // ensure virtualmeeting record is created for vroom2 on session #1
        global $DB;

        $url1 = $this->virtualmeeting1->get_join_url();
        $url2 = $this->virtualmeeting2->get_join_url();

        $seminar_event = new seminar_event($this->event1);
        $sessions = $seminar_event->get_sessions();
        foreach($sessions as $session) {
            if ($session->get_timestart() == $this->session1start) {
                $DB->insert_record('facetoface_room_dates', ['roomid' => $this->virtual_room2->id, 'sessionsdateid' => $session->get_id()]);
            }
        }

        $task = \mod_facetoface\task\manage_virtualmeetings_adhoc_task::create_from_seminar_event_id($this->event1);
        $task->execute();

        $virtualmeetings = $DB->get_records('virtualmeeting');
        $this->assertCount(3, $virtualmeetings);
        $room_virtualmeetings = $DB->get_records('facetoface_room_virtualmeeting');
        $this->assertCount(2, $room_virtualmeetings);
        $room_date_virtualmeetings = $DB->get_records('facetoface_room_dates_virtualmeeting');
        $this->assertCount(3, $room_date_virtualmeetings);
        $vm1 = \totara_core\virtualmeeting\virtual_meeting::load_by_id($this->virtualmeeting1->get_id());
        $vm2 = \totara_core\virtualmeeting\virtual_meeting::load_by_id($this->virtualmeeting2->get_id());
        $this->assertEquals($url1, $vm1->get_join_url());
        $this->assertEquals($url2, $vm2->get_join_url());
    }

    /**
     * @covers ::execute
     */
    public function test_execute_update_time_of_session() {
        // change date/time of session #1
        // call execute()
        // ensure virtualmeeting record is updated for vroom1 on session #1
        global $DB;

        $url1 = $this->virtualmeeting1->get_join_url();
        $url2 = $this->virtualmeeting2->get_join_url();

        $seminar_event = new seminar_event($this->event1);
        $sessions = $seminar_event->get_sessions();
        foreach($sessions as $session) {
            if ($session->get_timestart() == $this->session1start) {
                $timefinish = $session->get_timefinish();
                $session->set_timefinish($timefinish - 600);
                $session->save();
            }
        }

        $task = \mod_facetoface\task\manage_virtualmeetings_adhoc_task::create_from_seminar_event_id($this->event1);
        $task->execute();

        $virtualmeetings = $DB->get_records('virtualmeeting');
        $this->assertCount(2, $virtualmeetings);
        $room_virtualmeetings = $DB->get_records('facetoface_room_virtualmeeting');
        $this->assertCount(2, $room_virtualmeetings);
        $room_date_virtualmeetings = $DB->get_records('facetoface_room_dates_virtualmeeting');
        $this->assertCount(2, $room_date_virtualmeetings);
        $vm1 = \totara_core\virtualmeeting\virtual_meeting::load_by_id($this->virtualmeeting1->get_id());
        $vm2 = \totara_core\virtualmeeting\virtual_meeting::load_by_id($this->virtualmeeting2->get_id());
        $this->assertNotEquals($url1, $vm1->get_join_url());
        $this->assertEquals($url2, $vm2->get_join_url());
    }

    /**
     * @covers ::execute
     */
    public function test_execute_deleted_room() {
        // delete vroom1 from session #1
        // call execute()
        // ensure virtualmeeting record is deleted for vroom1 on session #1
        global $DB;

        // TODO: Test against TL-29046
        $this->markTestSkipped();
        $DB->delete_records('facetoface_room_dates', ['roomid' => $this->virtual_room1->id]);
    }

    /**
     * @covers ::execute
     */
    public function test_execute_deleted_session() {
        // delete session #1
        // call execute()
        // ensure virtualmeeting record is deleted for vroom1 on session #1

        // TODO: Test against TL-29046
        $this->markTestSkipped();
    }

    /**
     * @covers ::execute
     */
    public function test_execute_deleted_event() {
        // delete session #1
        // call execute()
        // ensure virtualmeeting record is deleted for vroom1 on session #1 AND session #2

        // TODO: Test against TL-29046
        $this->markTestSkipped();
    }

    /**
     * @covers ::execute
     */
    public function test_execute_unavailable_plugin() {
        // call execute()
        // ensure adhoc task fails with 'plugin is not configured'
        set_config('virtualmeeting_poc_app_enabled', '0', 'totara_core');
        $sink = $this->redirectMessages();

        $task = \mod_facetoface\task\manage_virtualmeetings_adhoc_task::create_from_seminar_event_id($this->event1);
        $task->execute();

        $this->execute_adhoc_tasks();
        $messages = $sink->get_messages();
        $this->assertEquals('Virtual meeting creation failure', $messages[0]->subject);
        $this->assertEquals($this->user1->id, $messages[0]->useridto);
    }
}
