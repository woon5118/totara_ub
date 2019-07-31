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

use totara_comment\comment;
use totara_comment\loader\comment_loader;
use core\webapi\execution_context;
use totara_webapi\graphql;
use totara_comment\resolver_factory;
use totara_comment\pagination\cursor;

class totara_comment_get_comments_testcase extends advanced_testcase {
    /**
     * @param int $instanceid
     * @param string $component
     * @param string $area
     * @param int $total
     * @return void
     */
    private function create_comments(int $instanceid, string $component, string $area, int $total = 20): void {
        $gen = $this->getDataGenerator();
        $user = $gen->create_user();

        $this->setUser($user);

        for ($i = 0; $i < $total; $i++) {
            comment::create(
                $instanceid,
                uniqid('random_'),
                $area,
                $component,
                FORMAT_MOODLE,
                $user->id
            );
        }
    }

    /**
     * @return void
     */
    public function test_get_comments(): void {
        $total = 20;
        $this->create_comments(42, 'totara_comment', 'xx_xx', $total);

        $cursor = new cursor();
        $cursor->set_limit(comment::ITEMS_PER_PAGE);

        $comments = comment_loader::get_paginator(42, 'totara_comment', 'xx_xx', $cursor)->get_items()->all();
        $this->assertCount(comment::ITEMS_PER_PAGE, $comments);

        $cursor->set_page(2);
        $comments = comment_loader::get_paginator(42, 'totara_comment', 'xx_xx', $cursor);
        $this->assertCount(($total - comment::ITEMS_PER_PAGE), $comments);

        /** @var comment $comment */
        foreach ($comments as $comment) {
            $this->assertEquals('totara_comment', $comment->get_component());
            $this->assertEquals('xx_xx', $comment->get_area());
            $this->assertEquals(42, $comment->get_instanceid());
        }
    }

    /**
     * @return void
     */
    public function test_get_comments_paginator(): void {
        $total = 50;

        $resolver = resolver_factory::create_resolver('totara_comment');
        $this->create_comments(15, 'totara_comment', 'xx_xx', $total);

        $cursor = new cursor();
        $paginator = comment_loader::get_paginator(15, 'totara_comment', 'xx_xx', $cursor);
        $comments = $paginator->get_items()->all();

        /** @var comment $comment */
        foreach ($comments as $comment) {
            $this->assertEquals(0, $comment->get_total_replies());
        }

        $this->assertEquals(50, $paginator->get_total());
        $this->assertEquals(comment::ITEMS_PER_PAGE, $paginator->get_current_cursor()->get_limit());
    }

    /**
     * @return void
     */
    public function test_get_comments_via_graphql(): void {
        $this->create_comments(42, 'totara_comment', 'xx_xx');
        $resolver = resolver_factory::create_resolver('totara_comment');

        $variables = [
            'instanceid' => 42,
            'component' => 'totara_comment',
            'area' => "xx_xx"
        ];

        $ec = execution_context::create('ajax', 'totara_comment_get_comments');
        $result = graphql::execute_operation($ec, $variables);

        $this->assertEmpty($result->errors);
        $this->assertNotEmpty($result->data);

        $this->assertArrayHasKey('comments', $result->data);

        $comments = $result->data['comments'];
        $this->assertCount(comment::ITEMS_PER_PAGE, $comments);

        foreach ($comments as $comment) {
            $this->assertArrayHasKey('id', $comment);
            $this->assertArrayHasKey('user', $comment);
            $this->assertArrayHasKey('content', $comment);
            $this->assertArrayHasKey('timedescription', $comment);
        }
    }
}