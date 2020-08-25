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

use container_workspace\member\member;
use container_workspace\interactor\workspace\interactor;

class container_workspace_workspace_interactor_testcase extends advanced_testcase {
    /**
     * @return void
     */
    public function test_has_seen(): void {
        $generator = $this->getDataGenerator();

        /** @var container_workspace_generator $workspace_generator */
        $workspace_generator = $generator->get_plugin_generator('container_workspace');
        $this->setAdminUser();

        $workspace = $workspace_generator->create_workspace();
        $interactor = new interactor($workspace);

        $this->assertTrue(
            $interactor->has_seen(time() + 500),
            "The seen time is greater than the timestamp of workspace should " .
            "result in user had already seen the workspace"
        );

        $this->assertFalse(
            $interactor->has_seen(time() - 3600),
            "The seen time is less than the timestap of the workspace which it should result in " .
            "user had not seen the workspace"
        );
    }

    /**
     * @return void
     */
    public function test_check_owner(): void {
        $generator = $this->getDataGenerator();

        /** @var container_workspace_generator $workspace_generator */
        $workspace_generator = $generator->get_plugin_generator('container_workspace');

        $user_one = $generator->create_user();
        $user_two = $generator->create_user();

        $this->setUser($user_one);
        $workspace = $workspace_generator->create_workspace();

        $user_one_interactor = new interactor($workspace, $user_one->id);
        $user_two_interactor = new interactor($workspace, $user_two->id);

        $this->assertTrue($user_one_interactor->is_owner());
        $this->assertTrue($user_one_interactor->is_primary_owner());

        $this->assertFalse($user_two_interactor->is_owner());
        $this->assertFalse($user_two_interactor->is_primary_owner());
        $this->assertFalse($user_two_interactor->is_joined());
    }

    /**
     * @return void
     */
    public function test_check_join(): void {
        $generator = $this->getDataGenerator();

        $user_one = $generator->create_user();
        $user_two = $generator->create_user();

        $this->setUser($user_one);

        /** @var container_workspace_generator $workspace_generator */
        $workspace_generator = $generator->get_plugin_generator('container_workspace');
        $workspace = $workspace_generator->create_workspace();

        $user_two_interactor = new interactor($workspace, $user_two->id);
        $this->assertFalse($user_two_interactor->is_joined());

        member::join_workspace($workspace, $user_two->id);
        $this->assertTrue($user_two_interactor->is_joined());
    }

    /**
     * @return void
     */
    public function test_check_manage(): void {
        $generator = $this->getDataGenerator();

        /** @var container_workspace_generator $workspace_generator */
        $workspace_generator = $generator->get_plugin_generator('container_workspace');

        $user_one = $generator->create_user();
        $user_two = $generator->create_user();

        $this->setUser($user_one);
        $workspace = $workspace_generator->create_workspace();

        $user_one_interactor = new interactor($workspace, $user_one->id);
        $user_two_interactor = new interactor($workspace, $user_two->id);

        $this->assertTrue($user_one_interactor->can_manage());
        $this->assertFalse($user_two_interactor->can_manage());

        // Even after user two join the workspace, user two should not be able to manage the workspace.
        member::join_workspace($workspace, $user_two->id);
        $user_two_interactor->reload_workspace();

        $this->assertFalse($user_two_interactor->can_manage());
    }
}