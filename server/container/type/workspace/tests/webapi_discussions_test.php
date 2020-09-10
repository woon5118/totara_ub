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

use totara_webapi\phpunit\webapi_phpunit_helper;
use container_workspace\query\discussion\sort as discussion_sort;
use container_workspace\member\member;

class container_workspace_webapi_discussions_testcase extends advanced_testcase {
    use webapi_phpunit_helper;

    /**
     * @return void
     */
    public function test_fetch_discussions_of_private_workspace_by_non_member(): void {
        $generator = $this->getDataGenerator();
        $user_one = $generator->create_user();

        $this->setUser($user_one);

        /** @var container_workspace_generator $workspace_generator */
        $workspace_generator = $generator->get_plugin_generator('container_workspace');
        $workspace = $workspace_generator->create_private_workspace();

        // Generate 6 discussions for this workspace.
        $workspace_id = $workspace->get_id();
        for ($i = 0; $i < 5; $i++) {
            $workspace_generator->create_discussion($workspace_id);
        }

        // Log in as non member user and check if user is able to see the discussions or not.
        $user_two = $generator->create_user();
        $this->setUser($user_two);

        self::expectException(coding_exception::class);
        self::expectExceptionMessage("Cannot get the list of discussions");

        $this->resolve_graphql_query(
            'container_workspace_discussions',
            [
                'workspace_id' => $workspace_id,
                'sort' => discussion_sort::get_code(discussion_sort::RECENT)
            ]
        );
    }

    /**
     * @return void
     */
    public function test_fetch_discussions_of_hidden_workspace_by_non_member(): void {
        $generator = $this->getDataGenerator();
        $user_one = $generator->create_user();

        $this->setUser($user_one);

        /** @var container_workspace_generator $workspace_generator */
        $workspace_generator = $generator->get_plugin_generator('container_workspace');
        $workspace = $workspace_generator->create_hidden_workspace();

        // Generate 2 discussions for this workspace.
        $workspace_id = $workspace->get_id();
        for ($i = 0; $i < 2; $i++) {
            $workspace_generator->create_discussion($workspace_id);
        }

        // Log in as non member user and check if user is able to see the discussions or not.
        $user_two = $generator->create_user();
        $this->setUser($user_two);

        self::expectException(coding_exception::class);
        self::expectExceptionMessage("Cannot get the list of discussions");

        $this->resolve_graphql_query(
            'container_workspace_discussions',
            [
                'workspace_id' => $workspace_id,
                'sort' => discussion_sort::get_code(discussion_sort::RECENT)
            ]
        );
    }

    /**
     * @return void
     */
    public function test_fetch_discussions_of_public_workspace_by_non_member(): void {
        $generator = $this->getDataGenerator();
        $user_one = $generator->create_user();

        $this->setUser($user_one);

        /** @var container_workspace_generator $workspace_generator */
        $workspace_generator = $generator->get_plugin_generator('container_workspace');
        $workspace = $workspace_generator->create_workspace();

        // Generate 5 discussions for this workspace.
        $workspace_id = $workspace->get_id();
        for ($i = 0; $i < 5; $i++) {
            $workspace_generator->create_discussion($workspace_id);
        }

        // Log in as non member user and check if user is able to see the discussions or not.
        $user_two = $generator->create_user();
        $this->setUser($user_two);

        $discussions = $this->resolve_graphql_query(
            'container_workspace_discussions',
            [
                'workspace_id' => $workspace_id,
                'sort' => discussion_sort::get_code(discussion_sort::RECENT)
            ]
        );

        self::assertCount(5, $discussions);
    }

    /**
     * @return void
     */
    public function test_fetch_discussions_of_private_workspace_as_member(): void {
        $generator = $this->getDataGenerator();
        $user_one = $generator->create_user();

        $this->setUser($user_one);

        /** @var container_workspace_generator $workspace_generator */
        $workspace_generator = $generator->get_plugin_generator('container_workspace');
        $workspace = $workspace_generator->create_private_workspace();

        // Generate 3 discussions for this workspace.
        $workspace_id = $workspace->get_id();
        for ($i = 0; $i < 3; $i++) {
            $workspace_generator->create_discussion($workspace_id);
        }

        // Log in as non member user and check if user is able to see the discussions or not.
        $user_two = $generator->create_user();
        $this->setUser($user_two);

        // Add user two to the workspace by user one.
        member::added_to_workspace($workspace, $user_two->id, false, $user_one->id);

        $discussions = $this->resolve_graphql_query(
            'container_workspace_discussions',
            [
                'workspace_id' => $workspace_id,
                'sort' => discussion_sort::get_code(discussion_sort::RECENT)
            ]
        );

        self::assertCount(3, $discussions);
    }

    /**
     * @return void
     */
    public function test_fetch_discussions_of_hidden_workspace_as_member(): void {
        $generator = $this->getDataGenerator();
        $user_one = $generator->create_user();

        $this->setUser($user_one);

        /** @var container_workspace_generator $workspace_generator */
        $workspace_generator = $generator->get_plugin_generator('container_workspace');
        $workspace = $workspace_generator->create_hidden_workspace();

        // Generate 4 discussions for this workspace.
        $workspace_id = $workspace->get_id();
        for ($i = 0; $i < 4; $i++) {
            $workspace_generator->create_discussion($workspace_id);
        }

        // Log in as non member user and check if user is able to see the discussions or not.
        $user_two = $generator->create_user();
        $this->setUser($user_two);

        // Add user two to the workspace by user one.
        member::added_to_workspace($workspace, $user_two->id, false, $user_one->id);

        $discussions = $this->resolve_graphql_query(
            'container_workspace_discussions',
            [
                'workspace_id' => $workspace_id,
                'sort' => discussion_sort::get_code(discussion_sort::RECENT)
            ]
        );

        self::assertCount(4, $discussions);
    }

    /**
     * @return void
     */
    public function test_fetch_discussions_of_public_workspace_as_member(): void {
        $generator = $this->getDataGenerator();
        $user_one = $generator->create_user();

        $this->setUser($user_one);

        /** @var container_workspace_generator $workspace_generator */
        $workspace_generator = $generator->get_plugin_generator('container_workspace');
        $workspace = $workspace_generator->create_workspace();

        // Generate 7 discussions for this workspace.
        $workspace_id = $workspace->get_id();
        for ($i = 0; $i < 7; $i++) {
            $workspace_generator->create_discussion($workspace_id);
        }

        // Log in as non member user and check if user is able to see the discussions or not.
        $user_two = $generator->create_user();
        $this->setUser($user_two);

        // Add user two to the workspace by user one.
        member::added_to_workspace($workspace, $user_two->id, false, $user_one->id);

        $discussions = $this->resolve_graphql_query(
            'container_workspace_discussions',
            [
                'workspace_id' => $workspace_id,
                'sort' => discussion_sort::get_code(discussion_sort::RECENT)
            ]
        );

        self::assertCount(7, $discussions);
    }
}