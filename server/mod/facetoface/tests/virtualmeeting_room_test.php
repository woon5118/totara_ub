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

use mod_facetoface\room_virtualmeeting;
use mod_facetoface\room_dates_virtualmeeting;
use mod_facetoface\room_virtualmeeting_list;

defined('MOODLE_INTERNAL') || die();

/**
 * Test vitualmeeting rooms classes
 * @group virtualmeeting
 */
class mod_facetoface_virtualmeeting_room_testcase extends advanced_testcase {

    public function test_room_virtualmeeting() {
        global $DB;

        $user1 = $this->getDataGenerator()->create_user(['username' => 'alice']);
        $this->setUser($user1);
        /** @var mod_facetoface_generator */
        $seminar_generator = $this->getDataGenerator()->get_plugin_generator('mod_facetoface');
        $virtual_room = $seminar_generator->add_virtualmeeting_room(['name' => 'virtual']);

        // Test with a created virtualmeeting room
        $room_virtualmeeting = room_virtualmeeting::from_roomid($virtual_room->id);
        $this->assertNotEquals(0, $room_virtualmeeting->get_id());
        $this->assertEquals($virtual_room->id, $room_virtualmeeting->get_roomid());
        $this->assertEquals('poc_app', $room_virtualmeeting->get_plugin());
        $this->assertEquals($user1->id, $room_virtualmeeting->get_userid());
        unset($room_virtualmeeting);

        // Test with a new room (no virtualmeeting record yet)
        $custom_room = $seminar_generator->add_custom_room(['name' => 'physical']);
        $room_virtualmeeting = room_virtualmeeting::from_roomid($custom_room->id);
        $this->assertEquals(0, $room_virtualmeeting->get_id());
        $this->assertEquals(0, $room_virtualmeeting->get_roomid());
        $this->assertEquals('', $room_virtualmeeting->get_plugin());
        $this->assertEquals(0, $room_virtualmeeting->get_userid());

        // Set it up and save it
        $room_virtualmeeting->set_roomid($custom_room->id);
        $room_virtualmeeting->set_plugin('poc_app');
        $room_virtualmeeting->set_userid($user1->id);
        $room_virtualmeeting->save();
        $this->assertNotEquals(0, $room_virtualmeeting->get_id());
        $this->assertEquals($custom_room->id, $room_virtualmeeting->get_roomid());
        $this->assertEquals('poc_app', $room_virtualmeeting->get_plugin());
        $this->assertEquals($user1->id, $room_virtualmeeting->get_userid());
        unset($room_virtualmeeting);

        // Load it again
        $room_virtualmeeting = room_virtualmeeting::from_roomid($custom_room->id);
        $this->assertNotEquals(0, $room_virtualmeeting->get_id());
        $this->assertEquals($custom_room->id, $room_virtualmeeting->get_roomid());
        $this->assertEquals('poc_app', $room_virtualmeeting->get_plugin());
        $this->assertEquals($user1->id, $room_virtualmeeting->get_userid());
        unset($room_virtualmeeting);

        $room_virtualmeetings = $DB->get_records('facetoface_room_virtualmeeting');
        $this->assertCount(2, $room_virtualmeetings);

        // Delete by roomid
        room_virtualmeeting::delete_by_roomid($custom_room->id);
        $room_virtualmeetings = $DB->get_records('facetoface_room_virtualmeeting');
        $this->assertCount(1, $room_virtualmeetings);
        $room_virtualmeeting = room_virtualmeeting::from_roomid($custom_room->id);
        $this->assertEquals(0, $room_virtualmeeting->get_id());
        unset($room_virtualmeeting);

        // Delete method
        $room_virtualmeeting = room_virtualmeeting::from_roomid($virtual_room->id);
        $this->assertNotEquals(0, $room_virtualmeeting->get_id());
        $room_virtualmeeting->delete();
        $room_virtualmeetings = $DB->get_records('facetoface_room_virtualmeeting');
        $this->assertCount(0, $room_virtualmeetings);
    }

    public function test_room_dates_virtualmeeting() {
        global $DB;

        // Test with fake data
        $room_dates_virtualmeeting = new room_dates_virtualmeeting();
        $this->assertEquals(0, $room_dates_virtualmeeting->get_id());
        $this->assertEquals(0, $room_dates_virtualmeeting->get_roomdateid());
        $this->assertEquals(0, $room_dates_virtualmeeting->get_virtualmeetingid());

        // Set it up and save it
        $room_dates_virtualmeeting->set_roomdateid(42);
        $room_dates_virtualmeeting->set_virtualmeetingid(64);
        $room_dates_virtualmeeting->save();
        $this->assertNotEquals(0, $room_dates_virtualmeeting->get_id());
        $this->assertEquals(42, $room_dates_virtualmeeting->get_roomdateid());
        $this->assertEquals(64, $room_dates_virtualmeeting->get_virtualmeetingid());
        $rd_id = $room_dates_virtualmeeting->get_id();
        unset($room_dates_virtualmeeting);

        // Load it again
        $room_dates_virtualmeeting = new room_dates_virtualmeeting($rd_id);
        $this->assertEquals($rd_id, $room_dates_virtualmeeting->get_id());
        $this->assertEquals(42, $room_dates_virtualmeeting->get_roomdateid());
        $this->assertEquals(64, $room_dates_virtualmeeting->get_virtualmeetingid());
        unset($room_virtualmeeting);

        // Add another one
        $room_dates_virtualmeeting = new room_dates_virtualmeeting();
        $room_dates_virtualmeeting->set_roomdateid(48);
        $room_dates_virtualmeeting->set_virtualmeetingid(68);
        $room_dates_virtualmeeting->save();
        unset($room_virtualmeeting);

        $room_dates_virtualmeetings = $DB->get_records('facetoface_room_dates_virtualmeeting');
        $this->assertCount(2, $room_dates_virtualmeetings);

        // Delete by roomdateid
        room_dates_virtualmeeting::delete_by_roomdateid(42);
        $room_dates_virtualmeetings = $DB->get_records('facetoface_room_dates_virtualmeeting');
        $this->assertCount(1, $room_dates_virtualmeetings);

        // Delete by virtualmeetingid
        room_dates_virtualmeeting::delete_by_virtualmeetingid(68);
        $room_dates_virtualmeetings = $DB->get_records('facetoface_room_dates_virtualmeeting');
        $this->assertCount(0, $room_dates_virtualmeetings);

        // Delete method
        $room_dates_virtualmeeting = new room_dates_virtualmeeting();
        $room_dates_virtualmeeting->set_roomdateid(52);
        $room_dates_virtualmeeting->set_virtualmeetingid(72);
        $room_dates_virtualmeeting->save();
        $this->assertNotEquals(0, $room_dates_virtualmeeting->get_id());
        $room_dates_virtualmeetings = $DB->get_records('facetoface_room_dates_virtualmeeting');
        $this->assertCount(1, $room_dates_virtualmeetings);
        $room_dates_virtualmeeting->delete();
        $room_dates_virtualmeetings = $DB->get_records('facetoface_room_dates_virtualmeeting');
        $this->assertCount(0, $room_dates_virtualmeetings);
    }


    public function test_room_virtualmeeting_list() {
        global $DB;

        $user1 = $this->getDataGenerator()->create_user(['username' => 'alice']);
        $this->setUser($user1);
        /** @var mod_facetoface_generator */
        $seminar_generator = $this->getDataGenerator()->get_plugin_generator('mod_facetoface');
        $virtual_room1 = $seminar_generator->add_virtualmeeting_room(['name' => 'virtual one']);
        $virtual_room2 = $seminar_generator->add_virtualmeeting_room(['name' => 'virtual two']);
        $custom_room = $seminar_generator->add_custom_room(['name' => 'custom']);
        $sitewide_room = $seminar_generator->add_site_wide_room(['name' => 'sitewide']);

        $virtualmeeting_list = room_virtualmeeting_list::from_roomids([$sitewide_room->id, $virtual_room1->id, $virtual_room2->id, $custom_room->id]);
        $this->assertCount(2, $virtualmeeting_list);
        foreach($virtualmeeting_list as $virtualmeeting_room) {
            /** @var room_virtualmeeting $virtualmeeting_room */
            if ($virtual_room1->id == $virtualmeeting_room->get_roomid()) {
                $this->assertEquals($virtual_room1->id, $virtualmeeting_room->get_roomid());
                $this->assertEquals('poc_app', $virtualmeeting_room->get_plugin());
                $this->assertEquals($user1->id, $virtualmeeting_room->get_userid());
            } else {
                $this->assertNotEquals(0, $virtualmeeting_room->get_id());
                $this->assertEquals($virtual_room2->id, $virtualmeeting_room->get_roomid());
                $this->assertEquals('poc_app', $virtualmeeting_room->get_plugin());
                $this->assertEquals($user1->id, $virtualmeeting_room->get_userid());
            }
        }
    }

    /**
     * Test to make sure user cannot update an another user's virtual meeting room
     */
    public function test_can_manage(){

        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $user3 = $this->getDataGenerator()->create_user(['deleted' => 1]);
        $this->setUser($user2);
        /** @var mod_facetoface_generator */
        $seminar_generator = $this->getDataGenerator()->get_plugin_generator('mod_facetoface');
        $customroom = $seminar_generator->add_virtualmeeting_room(['name' => 'virtual', 'url' => 'link', 'usercreated' => $user2->id], ['userid' => $user2->id]);
        $customroom2 = $seminar_generator->add_custom_room(['name' => 'casual', 'usercreated' => $user2->id]);
        $room = new \mod_facetoface\room($customroom->id);
        $room2 = new \mod_facetoface\room($customroom2->id);

        // Anyone can create virtualmeeting
        $this->setAdminUser();
        $can_manage = (new room_virtualmeeting())->can_manage($user1->id);
        $this->assertTrue($can_manage);
        // Unless they are deleted
        $can_manage = (new room_virtualmeeting())->can_manage($user3->id);
        $this->assertFalse($can_manage);
        // Or they do not exist
        $can_manage = (new room_virtualmeeting())->can_manage(-42);
        $this->assertFalse($can_manage);

        // Non-creator cannot update virtualmeeting
        $this->setUser($user1);
        $can_manage = room_virtualmeeting::get_virtual_meeting($room)->can_manage();
        $this->assertFalse($can_manage);

        // And can update casual room
        $this->setUser($user2);
        $can_manage = room_virtualmeeting::from_roomid($customroom2->id)->can_manage($user1->id);
        $this->assertTrue($can_manage);

        // Creator can update virtualmeeting
        $can_manage = room_virtualmeeting::get_virtual_meeting($room)->can_manage();
        $this->assertTrue($can_manage);

        // And can update casual room
        $this->setUser($user1);
        $can_manage = room_virtualmeeting::from_roomid($customroom2->id)->can_manage($user2->id);
        $this->assertTrue($can_manage);

        // Deleted user cannot update virtualmeeting
        $this->setAdminUser();
        $can_manage = room_virtualmeeting::get_virtual_meeting($room)->can_manage($user3->id);
        $this->assertFalse($can_manage);
        $can_manage = room_virtualmeeting::from_roomid($customroom2->id)->can_manage($user3->id);
        $this->assertFalse($can_manage);

        // Non-existent user cannot update virtualmeeting
        $can_manage = room_virtualmeeting::get_virtual_meeting($room)->can_manage(0);
        $this->assertFalse($can_manage);
        $can_manage = room_virtualmeeting::from_roomid($customroom2->id)->can_manage(-42);
        $this->assertFalse($can_manage);
    }

    public function test_is_virtual_meeting() {
        $this->assertFalse(room_virtualmeeting::is_virtual_meeting('@none'), '@none');
        $this->assertFalse(room_virtualmeeting::is_virtual_meeting('@internal'), '@internal');
        $this->assertTrue(room_virtualmeeting::is_virtual_meeting(''), '(empty)');
        $this->assertTrue(room_virtualmeeting::is_virtual_meeting('poc_app'), 'poc_app');
        $this->assertTrue(room_virtualmeeting::is_virtual_meeting('he_who_must_not_be_named'), 'he_who_must_not_be_named');
    }
}
