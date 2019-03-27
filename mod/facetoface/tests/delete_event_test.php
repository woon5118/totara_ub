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
 * @author Kian Nguyen <kian.nguyen@totaralearning.com>
 * @package mod_facetoface
 */

defined('MOODLE_INTERNAL') || die();

use mod_facetoface\{seminar_event, signup, seminar_session, role, seminar_event_helper, room, asset, asset_helper};
use mod_facetoface\signup\state\booked;

class mod_facetoface_delete_event_testcase extends advanced_testcase {
    /**
     * @return seminar_event
     */
    private function create_seminar_event(): seminar_event {
        $gen = $this->getDataGenerator();
        $course = $gen->create_course([], ['createsections' => 1]);

        $f2fgen = $gen->get_plugin_generator('mod_facetoface');
        $f2f = $f2fgen->create_instance(['course' => $course->id]);


        $e = new seminar_event();
        $e->set_facetoface($f2f->id);
        $e->save();
        return $e;
    }

    /**
     * For the event that is in the future, when it gets to delete, the system will try to cancel the event first,
     * because it wants to send out the messages to the learners/trainers/manager that the event is no longer available.
     *
     * Then afterward, it starts deleting the record (hard deleting). This test suite is to ensure that the messages got sent out
     * to related users.
     *
     * @return void
     */
    public function test_delete_event_with_email_sendingout(): void {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        $event = $this->create_seminar_event();
        $eventid = $event->get_id();

        $gen = $this->getDataGenerator();

        $s = new seminar_session();
        $s->set_sessionid($event->get_id());
        $s->set_timestart(time() + 3600);
        $s->set_timefinish(time() + 7200);
        $s->save();

        $sink = phpunit_util::start_message_redirection();
        for ($i = 0; $i < 2; $i++) {
            $user = $gen->create_user();
            $gen->enrol_user($user->id, $event->get_seminar()->get_course());

            $signup = signup::create($user->id, $event);
            $signup->save();

            $signup->switch_state(booked::class);
        }

        // Clearing emails that sent out to users for confirmation about booking.
        $this->execute_adhoc_tasks();
        $sink->clear();

        // Start adding trainer roles to the event
        $teacher = $DB->get_record('role', ['shortname' => 'teacher']);
        for ($i = 0; $i < 2; $i++) {
            $user = $gen->create_user();
            $gen->enrol_user($user->id, $event->get_seminar()->get_course(), 'teacher');

            $role = new role();
            $role->set_roleid($teacher->id);
            $role->set_userid($user->id);
            $role->set_sessionid($event->get_id());
            $role->save();
        }

        seminar_event_helper::delete_seminarevent($event);
        $this->execute_adhoc_tasks();

        $messages = $sink->get_messages();
        $this->assertCount(4, $messages);

        $this->assertFalse($DB->record_exists('facetoface_sessions', ['id' => $eventid]));
        $this->assertFalse($DB->record_exists('facetoface_sessions_dates', ['sessionid' => $eventid]));

        $this->assertFalse($DB->record_exists('facetoface_session_roles', ['sessionid' => $eventid]));
        $this->assertFalse($DB->record_exists('facetoface_signups', ['sessionid' => $eventid]));
    }

    /**
     * For past event, the cancellation process will be skipped. Test suite to assure that no messages sent out to related users at
     * all.
     * @return void
     */
    public function test_delete_event_without_email_sendingout(): void {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        $event = $this->create_seminar_event();
        $eventid = $event->get_id();

        $s = new seminar_session();
        $s->set_timestart(time() - 7200);
        $s->set_timefinish(time() - 3600);
        $s->set_sessionid($event->get_id());
        $s->save();

        $sink = phpunit_util::start_message_redirection();
        $gen = $this->getDataGenerator();
        for ($i = 0; $i < 2; $i++) {
            $user = $gen->create_user();
            $gen->enrol_user($user->id, $event->get_seminar()->get_course());

            $signup = signup::create($user->id, $event);
            $signup->save();

            $signup->switch_state(booked::class);
        }

        // Start sending out messages to the learners here, so that the last assertion would be more reasonable.
        $this->execute_adhoc_tasks();
        $sink->clear();

        $teacher = $DB->get_record('role', ['shortname' => 'teacher']);

        for ($i = 0; $i < 2; $i++) {
            $user = $gen->create_user();
            $gen->enrol_user($user->id, $event->get_seminar()->get_course(), 'teacher');

            $role = new role();
            $role->set_sessionid($event->get_id());
            $role->set_userid($user->id);
            $role->set_roleid($teacher->id);
            $role->save();
        }

        seminar_event_helper::delete_seminarevent($event);
        $this->execute_adhoc_tasks();

        $messages = $sink->get_messages();
        $this->assertEmpty($messages);

        $this->assertFalse($DB->record_exists('facetoface_sessions', ['id' => $eventid]));
    }

    /**
     * @return void
     */
    public function test_delete_event_with_custom_room(): void {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        $room = room::create_custom_room();
        $room->save();
        $roomid = $room->get_id();

        $seminarevent = $this->create_seminar_event();
        $s = new seminar_session();
        $s->set_timestart(time() + 3600);
        $s->set_timefinish(time() + 7200);
        $s->set_sessionid($seminarevent->get_id());
        $s->set_roomid($roomid);
        $s->save();

        // Add this custom room to be used at different seminar event, so that we can check whether the room is being
        // deleted after the first event cancelled or not.
        $seminarevent2 = $this->create_seminar_event();
        $s2 = new seminar_session();
        $s2->set_timestart(time() + 7200);
        $s2->set_timefinish(time() + 7200 + 3600);
        $s2->set_sessionid($seminarevent2->get_id());
        $s2->set_roomid($roomid);
        $s2->save();

        $seminareventid = $seminarevent->get_id();
        seminar_event_helper::delete_seminarevent($seminarevent);
        $this->assertFalse($DB->record_exists('facetoface_sessions', ['id' => $seminareventid]));
        $this->assertTrue($DB->record_exists('facetoface_room', ['id' => $roomid]));

        $seminarevent2id = $seminarevent2->get_id();
        seminar_event_helper::delete_seminarevent($seminarevent2);
        $this->assertFalse($DB->record_exists('facetoface_sessions', ['id' => $seminarevent2id]));
        $this->assertFalse($DB->record_exists('facetoface_room', ['id' => $roomid]));
    }

    /**
     * @return void
     */
    public function test_delete_event_with_custom_assets(): void {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        $asset1 = asset::create_custom_asset();
        $asset1->save();
        $asset1id = $asset1->get_id();

        $asset2 = asset::create_custom_asset();
        $asset2->save();
        $asset2id = $asset2->get_id();

        $seminarevent1 = $this->create_seminar_event();
        $seminarevent2 = $this->create_seminar_event();

        $time = time();

        /** @var seminar_event $seminarevent */
        foreach ([$seminarevent1, $seminarevent2] as $seminarevent) {
            for ($i = 0; $i < 2; $i++) {
                $time += 7200;

                $s = new seminar_session();
                $s->set_timestart($time);
                $s->set_timefinish($time + 2900);
                $s->set_sessionid($seminarevent->get_id());
                $s->save();

                asset_helper::sync($s->get_id(), [$asset1id, $asset2id]);
            }
        }

        // Deleting the first seminar event does not clear the custom assets at all, because custom asset is also
        // being linked with different seminar_event.
        seminar_event_helper::delete_seminarevent($seminarevent1);
        $this->assertTrue($DB->record_exists('facetoface_asset', ['id' => $asset1id]));
        $this->assertTrue($DB->record_exists('facetoface_asset', ['id' => $asset2id]));

        seminar_event_helper::delete_seminarevent($seminarevent2);
        $this->assertFalse($DB->record_exists('facetoface_asset', ['id' => $asset2id]));
        $this->assertFalse($DB->record_exists('facetoface_asset', ['id' => $asset1id]));
    }
}