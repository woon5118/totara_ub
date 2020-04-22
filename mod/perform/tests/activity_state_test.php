<?php
/*
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
 * @author Fabian Derschatta <fabian.derschatta@totaralearning.com>
 * @package mod_perform
 */

use mod_perform\event\activity_activated;
use mod_perform\state\activity\active;
use mod_perform\state\activity\draft;

/**
 * @group perform
 */
class mod_perform_activity_state_testcase extends advanced_testcase {

    public function test_activate() {
        $data_generator = $this->getDataGenerator();
        /** @var mod_perform_generator $perform_generator */
        $perform_generator = $data_generator->get_plugin_generator('mod_perform');

        $user = $data_generator->create_user();

        $this->setUser($user);

        $activity = $perform_generator->create_activity_in_container([
            'activity_name' => 'User1 One',
            'activity_status' => draft::get_code()
        ]);

        $this->assertEquals(draft::get_code(), $activity->status);
        $this->assertTrue($activity->is_draft());
        $this->assertFalse($activity->is_active());

        // TODO With TL-24784 we need to add at least one valid question and one valid assignment to make this test pass

        $activity->activate();

        $activity->refresh();

        $this->assertEquals(active::get_code(), $activity->status);
        $this->assertTrue($activity->is_active());
        $this->assertFalse($activity->is_draft());

        // Activating an already active activity should not change anything
        $activity->activate();

        $activity->refresh();

        $this->assertEquals(active::get_code(), $activity->status);
        $this->assertTrue($activity->is_active());
        $this->assertFalse($activity->is_draft());
    }

    public function test_activate_event_is_triggered() {
        $data_generator = $this->getDataGenerator();
        /** @var mod_perform_generator $perform_generator */
        $perform_generator = $data_generator->get_plugin_generator('mod_perform');

        $user = $data_generator->create_user();

        $this->setUser($user);

        $activity = $perform_generator->create_activity_in_container([
            'activity_name' => 'User1 One',
            'activity_status' => draft::get_code()
        ]);

        // TODO With TL-24784 we need to add at least one valid question and one valid assignment to make this test pass

        $sink = $this->redirectEvents();

        $activity->activate();

        $events = $sink->get_events();

        $this->assertCount(1, $events);
        $event = array_shift($events);
        $this->assertInstanceOf(activity_activated::class, $event);
        $this->assertEquals($activity->get_id(), $event->objectid);
        $this->assertEquals($activity->get_context()->id, $event->contextid);

        $sink->clear();

        // Activating an already active activity should not trigger an event again
        $activity->refresh()
            ->activate()
            ->refresh();

        $this->assertEmpty($sink->get_events());
    }

    public function test_can_activate(): void {
        $data_generator = $this->getDataGenerator();
        /** @var mod_perform_generator $perform_generator */
        $perform_generator = $data_generator->get_plugin_generator('mod_perform');

        $user1 = $data_generator->create_user();
        $user2 = $data_generator->create_user();

        $this->setUser($user1);

        // TODO With TL-24784 we need to add at least one valid question and one valid assignment to make this test pass
        //      and we need to test whether an activity can be activated if not all conditions are fulfilled

        $draft_activity = $perform_generator->create_activity_in_container([
            'activity_name' => 'User1 One',
            'activity_status' => draft::get_code()
        ]);

        $this->assertTrue($draft_activity->can_potentially_activate());
        $this->assertTrue($draft_activity->can_activate());

        $this->setUser($user2);

        // The user can't activate it because he does not have the capability
        $this->assertFalse($draft_activity->can_potentially_activate());
        // And all conditions are fulfilled
        $this->assertFalse($draft_activity->can_activate());

        $this->setUser($user1);

        $active_activity = $perform_generator->create_activity_in_container([
            'activity_name' => 'User1 One',
            'activity_status' => active::get_code()
        ]);

        $this->assertFalse($active_activity->can_potentially_activate());
        $this->assertFalse($active_activity->can_activate());
    }

}