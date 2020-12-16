<?php
/**
 * This file is part of Totara Learn
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
 * @author Kian Nguyen <kian.nguyen@totaralearning.com>
 * @package container_workspace
 */
defined('MOODLE_INTERNAL') || die();

use container_workspace\member\member;
use core\entity\user_enrolment;

class container_workspace_add_member_testcase extends advanced_testcase {
    /**
     * @return void
     */
    public function test_add_member_to_public_workspace(): void {
        $generator = $this->getDataGenerator();

        $user_one = $generator->create_user();
        $user_two = $generator->create_user();

        /** @var container_workspace_generator $workspace_generator */
        $workspace_generator = $generator->get_plugin_generator('container_workspace');

        // Login as first user and start creating the workspace.
        $this->setUser($user_one);
        $workspace = $workspace_generator->create_workspace();

        $member = member::added_to_workspace($workspace, $user_two->id);
        $this->assertEquals($user_two->id, $member->get_user_id());
    }

    /**
     * @return void
     */
    public function test_add_member_to_private_workspace(): void {
        $generator = $this->getDataGenerator();

        $user_one = $generator->create_user();
        $user_two = $generator->create_user();

        /** @var container_workspace_generator $workspace_generator */
        $workspace_generator = $generator->get_plugin_generator('container_workspace');

        // Login as first user and start creating the workspace.
        $this->setUser($user_one);
        $workspace = $workspace_generator->create_private_workspace();

        $member = member::added_to_workspace($workspace, $user_two->id);
        $this->assertEquals($user_two->id, $member->get_user_id());
    }

    /**
     * @return void
     */
    public function test_add_member_to_hidden_workspace(): void {
        $generator = $this->getDataGenerator();

        $user_one = $generator->create_user();
        $user_two = $generator->create_user();

        /** @var container_workspace_generator $workspace_generator */
        $workspace_generator = $generator->get_plugin_generator('container_workspace');

        // Login as first user and start creating the workspace.
        $this->setUser($user_one);
        $workspace = $workspace_generator->create_hidden_workspace();

        $member = member::added_to_workspace($workspace, $user_two->id);
        $this->assertEquals($user_two->id, $member->get_user_id());
    }

    /**
     * @return void
     */
    public function test_add_member_do_not_trigger_adhoc_tasks(): void {
        global $DB;
        $generator = $this->getDataGenerator();

        $user_one = $generator->create_user();
        $user_two = $generator->create_user();

        /** @var container_workspace_generator $workspace_generator */
        $workspace_generator = $generator->get_plugin_generator('container_workspace');
        $this->setUser($user_one);

        $workspace = $workspace_generator->create_workspace();

        // Make sure that we clear out the adhoc tasks first.
        $this->execute_adhoc_tasks();

        $message_sink = phpunit_util::start_message_redirection();
        $member = member::added_to_workspace($workspace, $user_two->id, false);

        $this->execute_adhoc_tasks();
        $messages = $message_sink->get_messages();

        $this->assertEmpty($messages);
        $this->assertNotEmpty($member->get_id());
        $this->assertTrue(
            $DB->record_exists(user_enrolment::TABLE, ['id' => $member->get_id()])
        );
    }

    /**
     * @return void
     */
    public function test_add_members_in_bulk(): void {
        $generator = $this->getDataGenerator();

        $user_one = $generator->create_user();
        $user_two = $generator->create_user();
        $user_three = $generator->create_user();
        $user_four = $generator->create_user();
        $user_five = $generator->create_user();

        /** @var container_workspace_generator $workspace_generator */
        $workspace_generator = $generator->get_plugin_generator('container_workspace');
        $this->setUser($user_one);

        $workspace1 = $workspace_generator->create_workspace();
        $workspace2 = $workspace_generator->create_workspace();

        // Make sure that we clear out the adhoc tasks first.
        $this->execute_adhoc_tasks();

        $users_to_add = [$user_two->id, $user_three->id, $user_four->id, $user_five->id];

        $message_sink = phpunit_util::start_message_redirection();
        $members = member::added_to_workspace_in_bulk($workspace1, $users_to_add, false);

        $this->assertEquals(count($users_to_add), count($members));

        $this->execute_adhoc_tasks();

        $this->assertEmpty($message_sink->get_messages());

        $members = member::added_to_workspace_in_bulk($workspace1, $users_to_add, true);

        $this->assertEquals(count($users_to_add), count($members));

        $this->execute_adhoc_tasks();

        $messages = $message_sink->get_messages();
        $this->assertCount(count($users_to_add), $messages);
    }

    /**
     * @return void
     */
    public function test_add_member_to_hidden_workspace_with_exception(): void {
        $generator = $this->getDataGenerator();

        $user_one = $generator->create_user();
        $user_two = $generator->create_user();
        $user_three = $generator->create_user();

        /** @var container_workspace_generator $workspace_generator */
        $workspace_generator = $generator->get_plugin_generator('container_workspace');

        $this->setUser($user_one);
        $workspace = $workspace_generator->create_hidden_workspace();

        $this->setUser($user_two);
        $this->expectException("moodle_exception");
        $this->expectExceptionMessage("Cannot manual add user to workspace");
        member::added_to_workspace($workspace, $user_three->id);
    }

    /**
     * @return void
     */
    public function test_add_member_to_private_workspace_with_exception(): void {
        $generator = $this->getDataGenerator();

        $user_one = $generator->create_user();
        $user_two = $generator->create_user();
        $user_three = $generator->create_user();

        /** @var container_workspace_generator $workspace_generator */
        $workspace_generator = $generator->get_plugin_generator('container_workspace');

        $this->setUser($user_one);
        $workspace = $workspace_generator->create_private_workspace();

        $this->setUser($user_two);
        $this->expectException("moodle_exception");
        $this->expectExceptionMessage("Cannot manual add user to workspace");
        member::added_to_workspace($workspace, $user_three->id);
    }
}