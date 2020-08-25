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
use container_workspace\loader\member\loader;
use container_workspace\query\member\query;
use container_workspace\query\member\sort;

class container_workspace_member_loader_testcase extends advanced_testcase {
    /**
     * @return void
     */
    public function test_load_members_that_load_admin_first(): void {
        $generator = $this->getDataGenerator();
        $user_one = $generator->create_user();

        $this->setUser($user_one);

        /** @var container_workspace_generator $workspace_generator */
        $workspace_generator = $generator->get_plugin_generator('container_workspace');
        $workspace = $workspace_generator->create_workspace();

        for ($i = 0; $i < 10; $i++) {
            $user = $generator->create_user();
            member::join_workspace($workspace, $user->id);
        }


        $query = new query($workspace->get_id());
        $result_one = loader::get_members($query);

        // 10 members plus admin.
        $this->assertEquals(11, $result_one->get_total());
        $result_one_items = $result_one->get_items()->all();

        // First item should be the user one as the user is an owner of workspace.
        /** @var member $result_one_first */
        $result_one_first = reset($result_one_items);
        $this->assertInstanceOf(member::class, $result_one_first);

        $this->assertEquals($user_one->id, $result_one_first->get_user_id());

        $query->set_sort(sort::RECENT_JOIN);
        $result_two = loader::get_members($query);

        $this->assertEquals(11, $result_two->get_total());
        $result_two_items = $result_two->get_items()->all();

        /** @var member $result_two_first */
        $result_two_first = reset($result_two_items);
        $this->assertInstanceOf(member::class, $result_two_first);

        $this->assertEquals($user_one->id, $result_two_first->get_user_id());
    }

    /**
     * @return void
     */
    public function test_load_members_does_not_include_members_from_different_workspace(): void {
        $generator = $this->getDataGenerator();
        $user_one = $generator->create_user();

        $this->setUser($user_one);

        /** @var container_workspace_generator $workspace_generator */
        $workspace_generator = $generator->get_plugin_generator('container_workspace');

        $workspace_one = $workspace_generator->create_workspace();
        $workspace_two = $workspace_generator->create_workspace();

        // Add 5 members to the first workspace and 3 to second workspace.
        for ($i = 0; $i < 5; $i++) {
            $user = $generator->create_user();
            member::join_workspace($workspace_one, $user->id);
        }

        $users_in_workspace_two = [];
        for ($i = 0; $i < 3; $i++) {
            $user = $generator->create_user();
            member::join_workspace($workspace_two, $user->id);

            $users_in_workspace_two[] = $user->id;
        }

        // Now load the members of workspace one - that it should not return any records from workspace two's members
        $query = new query($workspace_one->get_id());
        $result = loader::get_members($query);

        // 5 members plus our special user_one.
        $this->assertEquals(6, $result->get_total());
        $members = $result->get_items()->all();

        /** @var member $member */
        foreach ($members as $member) {
            if ($user_one->id == $member->get_user_id()) {
                // Skip the owner.
                continue;
            }

            $this->assertFalse(in_array($member->get_user_id(), $users_in_workspace_two));
        }
    }
}
