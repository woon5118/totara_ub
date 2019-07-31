<?php
/**
 * This file is part of Totara Learn
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
 * @package totara_comment
 */
defined('MOODLE_INTERNAL') || die();

use totara_comment\comment_helper;

class totara_comment_add_testcase extends advanced_testcase {
    /**
     * @return void
     */
    public function test_add_comment(): void {
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        $comment = comment_helper::create_comment(
            'totara_comment',
            'comment_view',
            42,
            'Hello world'
        );

        $this->assertStringContainsString('Hello world', $comment->get_content());
        $this->assertEquals('totara_comment', $comment->get_component());
        $this->assertEquals('comment_view', $comment->get_area());
        $this->assertEquals(42, $comment->get_instanceid());
    }

    /**
     * @return void
     */
    public function test_add_reply(): void {
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        $comment = comment_helper::create_comment(
            'totara_comment',
            'comment_view',
            42,
            'hello world'
        );

        $reply = comment_helper::create_reply($comment->get_id(), 'this is reply');

        $this->assertEquals($reply->get_parent_id(), $comment->get_id());
        $this->assertEquals('this is reply', $reply->get_content());
        $this->assertEquals(42, $reply->get_instanceid());
        $this->assertTrue($reply->is_reply());
        $this->assertEquals(FORMAT_MOODLE, $reply->get_format());
    }
}