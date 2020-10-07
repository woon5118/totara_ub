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

use core\json_editor\node\paragraph;
use container_workspace\discussion\discussion_helper;
use container_workspace\query\discussion\query as discussion_query;
use container_workspace\loader\discussion\loader as discussion_loader;
use container_workspace\discussion\discussion;
use container_workspace\member\member;
use totara_comment\comment_helper;
use container_workspace\workspace;
use container_workspace\query\discussion\sort as discussion_sort;

class container_workspace_discussion_loader_testcase extends advanced_testcase {
    /**
     * @return void
     */
    public function test_finding_discussions_with_like(): void {
        $generator = $this->getDataGenerator();
        $user_one = $generator->create_user();

        $this->setUser($user_one);

        /** @var container_workspace_generator $workspace_generator */
        $workspace_generator = $generator->get_plugin_generator('container_workspace');
        $workspace = $workspace_generator->create_workspace();

        // Login as second users and adding discussions to the workspace.
        $user_two = $generator->create_user();
        $this->setUser($user_two);

        member::join_workspace($workspace, $user_two->id);

        // Create a first discussion that has the search term.
        $special_discussion = discussion_helper::create_discussion(
            $workspace,
            json_encode([
                'type' => 'doc',
                'content' => [
                    paragraph::create_json_node_from_text("This is the special text")
                ]
            ]),
            null,
            FORMAT_JSON_EDITOR
        );

        // Now create the several not-a-like discussions.
        for ($i = 0; $i < 5; $i++) {
            discussion_helper::create_discussion(
                $workspace,
                json_encode([
                    'type' => 'doc',
                    'content' => [paragraph::create_json_node_from_text(uniqid())]
                ]),
                null,
                FORMAT_JSON_EDITOR
            );
        }

        $query = new discussion_query($workspace->get_id());
        $query->set_search_term("this is");

        $paginator = discussion_loader::get_discussions($query);
        $this->assertEquals(1, $paginator->get_total());

        /** @var discussion[] $discussions */
        $discussions = $paginator->get_items()->all();
        $this->assertCount(1, $discussions);

        $discussion = reset($discussions);
        $this->assertEquals($special_discussion->get_id(), $discussion->get_id());
        $this->assertEquals($special_discussion->get_content(), $discussion->get_content());
    }

    /**
     * @return void
     */
    public function test_finding_discussion_with_like_from_comment(): void {
        $generator = $this->getDataGenerator();
        $user_one = $generator->create_user();

        $this->setUser($user_one);

        /** @var container_workspace_generator $workspace_generator */
        $workspace_generator = $generator->get_plugin_generator('container_workspace');
        $workspace = $workspace_generator->create_workspace();

        // Log in as user two and check if you are able to search for the discussions base on the comment.
        $user_two = $generator->create_user();
        $this->setUser($user_two);

        member::join_workspace($workspace, $user_two->id);
        $special_discussion = discussion_helper::create_discussion(
            $workspace,
            json_encode([
                'type' => 'doc',
                'content' => [paragraph::create_json_node_from_text(uniqid())]
            ]),
            null,
            FORMAT_JSON_EDITOR
        );

        for ($i = 0; $i < 5; $i++) {
            discussion_helper::create_discussion(
                $workspace,
                json_encode([
                    'type' => 'doc',
                    'content' => [paragraph::create_json_node_from_text(uniqid())]
                ]),
                null,
                FORMAT_JSON_EDITOR
            );
        }

        // Now create a several comments for the special discussion.
        $workspace_type = workspace::get_type();
        comment_helper::create_comment(
            $workspace_type,
            discussion::AREA,
            $special_discussion->get_id(),
            json_encode([
                'type' => 'doc',
                'content' => [paragraph::create_json_node_from_text('Parent is THE discussion')]
            ]),
            FORMAT_JSON_EDITOR
        );

        for ($i = 0; $i < 5; $i++) {
            comment_helper::create_comment(
                $workspace_type,
                discussion::AREA,
                $special_discussion->get_id(),
                json_encode([
                    'type' => 'doc',
                    'content' => [paragraph::create_json_node_from_text(uniqid())]
                ]),
                FORMAT_JSON_EDITOR
            );
        }

        $query = new discussion_query($workspace->get_id());
        $query->set_search_term('the discussion');

        $paginator = discussion_loader::get_discussions($query);
        $this->assertEquals(1, $paginator->get_total());

        /** @var discussion[] $discussions */
        $discussions = $paginator->get_items()->all();
        $this->assertCount(1, $discussions);

        $discussion = reset($discussions);

        $this->assertEquals($special_discussion->get_id(), $discussion->get_id());
        $this->assertEquals($special_discussion->get_id(), $discussion->get_id());
    }

    /**
     * @return void
     */
    public function test_fetching_discussions_with_recent_update_sort_order(): void {
        $generator = $this->getDataGenerator();
        $user_one = $generator->create_user();

        $this->setUser($user_one);

        /** @var container_workspace_generator $workspace_generator */
        $workspace_generator = $generator->get_plugin_generator('container_workspace');
        $workspace = $workspace_generator->create_workspace();

        // Create two discussions, and fetch with the recently updated before adding comment to one and another.
        $workspace_id = $workspace->get_id();
        $discussion_one = $workspace_generator->create_discussion($workspace_id);

        // Just to make sure that race condition is not happening, for the discussion two.
        $this->waitForSecond();
        $discussion_two = $workspace_generator->create_discussion($workspace_id);

        // Load the discussion base on the recently updated which the second discussion should be there.
        $query = new discussion_query($workspace_id);
        $query->set_sort(discussion_sort::RECENT);

        $before_result = discussion_loader::get_discussions($query);
        self::assertEquals(2, $before_result->get_total());

        $before_result_discussions = $before_result->get_items()->all();
        self::assertCount(2, $before_result_discussions);

        // The discussion two will be at the top.
        $first_before_result_discussion = reset($before_result_discussions);
        self::assertEquals($discussion_two->get_id(), $first_before_result_discussion->get_id());

        // The discusison one will be at the bottom.
        $second_before_result_discussion = end($before_result_discussions);
        self::assertEquals($discussion_one->get_id(), $second_before_result_discussion->get_id());

        // Add a comment to discussion one - which it will move the discussion one to the top
        /** @var totara_comment_generator $comment_generator */
        $comment_generator = $generator->get_plugin_generator('totara_comment');
        $comment_generator->create_comment(
            $discussion_one->get_id(),
            workspace::get_type(),
            discussion::AREA
        );

        $after_result = discussion_loader::get_discussions($query);
        self::assertEquals(2, $after_result->get_total());

        $after_result_discussions = $after_result->get_items()->all();
        self::assertCount(2, $after_result_discussions);

        // Discussion one should be at the top.
        $first_after_result_discussion = reset($after_result_discussions);
        self::assertEquals($discussion_one->get_id(), $first_after_result_discussion->get_id());

        // Discussion two should be at the bottom - since discussion one was added with the comment.
        $second_after_result_discussion = end($after_result_discussions);
        self::assertEquals($discussion_two->get_id(), $second_after_result_discussion->get_id());
    }
}