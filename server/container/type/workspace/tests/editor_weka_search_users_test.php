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
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Kian Nguyen <kian.nguyen@totaralearning.com>
 * @package container_workspace
 */
defined('MOODLE_INTERNAL') || die();

use editor_weka\hook\search_users_by_pattern;
use container_workspace\watcher\editor_weka_watcher;
use container_workspace\workspace;
use container_workspace\discussion\discussion;

class container_workspace_editor_weka_search_users_testcase extends advanced_testcase {
    /**
     * @param int $number_of_users
     * @return stdClass[]
     */
    private function create_users(int $number_of_users = 2): array {
        $generator = $this->getDataGenerator();
        $users = [];

        for ($i = 0; $i < $number_of_users; $i++) {
            $users[] = $generator->create_user([
                'firstname' => uniqid('user_'),
                'lastname' => uniqid('user_')
            ]);
        }

        return $users;
    }

    /**
     * @return container_workspace_generator
     */
    private function get_workspace_generator(): container_workspace_generator {
        $generator = $this->getDataGenerator();

        /** @var container_workspace_generator $workspace_generator */
        $workspace_generator = $generator->get_plugin_generator('container_workspace');
        return $workspace_generator;
    }

    /**
     * @return void
     */
    public function test_search_for_users_with_invalid_context(): void {
        [$user_one] = $this->create_users(1);
        $hook = search_users_by_pattern::create(
            workspace::get_type(),
            discussion::AREA,
            "",
            context_system::instance()->id,
            $user_one->id
        );

        editor_weka_watcher::on_search_users($hook);
        $this->assertDebuggingCalled("Context level is not support by container_workspace");

        self::assertFalse($hook->is_db_run());
    }

    /**
     * @return void
     */
    public function test_search_for_users_in_hidden_workspace_only(): void {
        [$user_one, $user_two, $user_three] = $this->create_users(3);
        $workspace_generator = $this->get_workspace_generator();

        $this->setUser($user_one);
        $workspace = $workspace_generator->create_hidden_workspace();

        $workspace_generator->add_member($workspace, $user_two->id, $user_one->id);
        $hook = search_users_by_pattern::create(
            workspace::get_type(),
            discussion::AREA,
            "",
            $workspace->get_context()->id,
            $user_one->id
        );

        self::assertEmpty($hook->get_users());
        self::assertFalse($hook->is_db_run());

        editor_weka_watcher::on_search_users($hook);
        self::assertTrue($hook->is_db_run());

        $users = $hook->get_users();
        self::assertNotEmpty($users);
        self::assertCount(2, $users);

        foreach ($users as $user) {
            self::assertContains($user->id, [$user_one->id, $user_two->id]);
            self::assertNotEquals($user_three->id, $user->id);
        }
    }

    /**
     * @return void
     */
    public function test_search_for_non_member_users_in_public_workspace(): void {
        [$user_one, $user_two] = $this->create_users();
        $workspace_generator = $this->get_workspace_generator();

        $this->setUser($user_one);
        $workspace = $workspace_generator->create_workspace();

        $hook = search_users_by_pattern::create(
            workspace::get_type(),
            discussion::AREA,
            $user_two->firstname,
            $workspace->get_context()->id,
            $user_one->id
        );

        editor_weka_watcher::on_search_users($hook);
        self::assertTrue($hook->is_db_run());

        $users = $hook->get_users();
        self::assertNotEmpty($users);
        self::assertCount(1, $users);

        $fetched_user = reset($users);
        self::assertEquals($user_two->id, $fetched_user->id);
    }

    /**
     * @return void
     */
    public function test_search_for_non_member_users_in_private_workspace(): void {
        [$user_one, $user_two] = $this->create_users();
        $workspace_generator = $this->get_workspace_generator();

        $this->setUser($user_one);
        $workspace = $workspace_generator->create_private_workspace();

        $hook = search_users_by_pattern::create(
            workspace::get_type(),
            discussion::AREA,
            $user_two->firstname,
            $workspace->get_context()->id,
            $user_one->id
        );

        editor_weka_watcher::on_search_users($hook);
        self::assertTrue($hook->is_db_run());

        $users = $hook->get_users();
        self::assertNotEmpty($users);
        self::assertCount(1, $users);

        $fetched_user = reset($users);
        self::assertEquals($user_two->id, $fetched_user->id);
    }
}