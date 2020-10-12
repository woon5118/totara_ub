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
 * @package totara_comment
 */
defined('MOODLE_INTERNAL') || die();

use totara_webapi\phpunit\webapi_phpunit_helper;
use totara_comment\webapi\resolver\query\editor_weka;
use totara_comment\comment;

class totara_comment_webapi_editor_weka_testcase extends advanced_testcase {
    use webapi_phpunit_helper;

    /**
     * @param int $number_of_user
     * @return array
     */
    private function create_users(int $number_of_user = 1): array {
        $generator = $this->getDataGenerator();
        $users = [];

        for ($i = 0; $i < $number_of_user; $i++) {
            $users[] = $generator->create_user();
        }

        return $users;
    }

    /**
     * @return totara_comment_generator
     */
    private function get_comment_generator(): totara_comment_generator {
        $generator = $this->getDataGenerator();

        /** @var totara_comment_generator $comment_generator */
        $comment_generator = $generator->get_plugin_generator('totara_comment');
        return $comment_generator;
    }

    /**
     * @return void
     */
    public function test_fetch_editor_weka_with_deprecated_comment_id(): void {
        [$user_one] = $this->create_users();
        $this->setUser($user_one);

        $context = context_user::instance($user_one->id);
        $comment_generator = $this->get_comment_generator();

        $comment_generator->add_context_for_default_resolver($context);

        // Fetch for editor weka with comment's id pass in.
        $query_name = $this->get_graphql_name(editor_weka::class);
        $result = $this->resolve_graphql_query(
            $query_name,
            [
                'component' => 'totara_comment',
                'area' => 'comment_view',
                'comment_area' => comment::COMMENT_AREA,
                'id' => 42,
                'instance_id' => 42
            ]
        );

        $this->assertDebuggingCalled(
            "The argument 'id' has been deprecated, please do not use it"
        );

        self::assertInstanceOf(weka_texteditor::class, $result);
    }

    /**
     * @return void
     */
    public function test_fetch_editor_weka_with_invalid_comment_area(): void {
        [$user_one] = $this->create_users();
        $this->setUser($user_one);

        $comment_generator = $this->get_comment_generator();

        $context = context_user::instance($user_one->id);
        $comment_generator->add_context_for_default_resolver($context);

        $query_name = $this->get_graphql_name(editor_weka::class);

        $this->expectException(coding_exception::class);
        $this->expectExceptionMessage("Invalid comment area: hello_world");

        $this->resolve_graphql_query(
            $query_name,
            [
                'component' => 'totara_comment',
                'area' => 'comment_view',
                'comment_area' => 'hello_world',
                'instance_id' => 42
            ]
        );
    }

    /**
     * @return void
     */
    public function test_fetch_editor_weka_for_comment_area(): void {
        [$user_one] = $this->create_users();
        $this->setUser($user_one);

        $comment_generator = $this->get_comment_generator();

        $context = context_user::instance($user_one->id);
        $comment_generator->add_context_for_default_resolver($context);

        $query_name = $this->get_graphql_name(editor_weka::class);

        /** @var weka_texteditor $editor */
        $editor = $this->resolve_graphql_query(
            $query_name,
            [
                'component' => 'totara_comment',
                'area' => 'commnet_view',
                'comment_area' => comment::COMMENT_AREA,
                'instance_id' => 42
            ]
        );

        self::assertInstanceOf(weka_texteditor::class, $editor);
        self::assertEquals($context->id, $editor->get_contextid());
    }

    /**
     * @return void
     */
    public function test_fetch_editor_weka_for_reply_area(): void {
        [$user_one] = $this->create_users();
        $this->setUser($user_one);

        $comment_generator = $this->get_comment_generator();

        $context = context_user::instance($user_one->id);
        $comment_generator->add_context_for_default_resolver($context);

        $query_name = $this->get_graphql_name(editor_weka::class);

        /** @var weka_texteditor $editor */
        $editor = $this->resolve_graphql_query(
            $query_name,
            [
                'component' => 'totara_comment',
                'area' => 'commnet_view',
                'comment_area' => comment::REPLY_AREA,
                'instance_id' => 42
            ]
        );

        self::assertInstanceOf(weka_texteditor::class, $editor);
        self::assertEquals($context->id, $editor->get_contextid());
    }
}