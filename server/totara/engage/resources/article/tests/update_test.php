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
 * @package engage_article
 */
defined('MOODLE_INTERNAL') || die();

use engage_article\totara_engage\resource\article;
use totara_engage\access\access;
use totara_engage\exception\resource_exception;
use core\webapi\execution_context;
use totara_engage\timeview\time_view;
use totara_webapi\graphql;

class engage_article_update_testcase extends advanced_testcase {
    /**
     * @return void
     */
    public function test_update_article(): void {
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        $data = [
            'name' => 'hello world',
            'content' => 'bolobala',
            'timeview' => time_view::LESS_THAN_FIVE,
            'access' => access::PRIVATE
        ];

        /** @var article $resource */
        $resource = article::create($data);

        $this->assertEquals($data['name'], $resource->get_name());
        $this->assertEquals($data['content'], $resource->get_content());

        $result = $resource->can_update($user->id);
        
        $data['name'] = "Bolobala xxoxo";
        $data['access'] = access::PUBLIC;
        $resource->update($data);

        $this->assertTrue($result);
        $this->assertEquals($data['name'], $resource->get_name());
        $this->assertEquals($data['content'], $resource->get_content());
        $this->assertEquals($data['access'], $resource->get_access());
    }

    /**
     * @return void
     */
    public function test_update_article_without_access(): void {
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        $data = [
            'name' => 'hello world',
            'content' => 'Article content',
        ];

        /** @var article $resource */
        $resource = article::create($data);

        $this->assertEquals($data['name'], $resource->get_name());
        $this->assertEquals($data['content'], $resource->get_content());

        $data['name'] = "hello new world";
        $data['content'] = "New article content";
        $resource->update($data, $user->id);

        $this->assertEquals($data['name'], $resource->get_name());
        $this->assertEquals($data['content'], $resource->get_content());
    }

    /**
     * @return void
     */
    public function test_update_article_without_permissions(): void {
        $gen = $this->getDataGenerator();

        $user = $gen->create_user();
        $this->setUser($user);

        $data = [
            'name' => 'hello world',
            'content' => 'bolobala',
            'timeview' => time_view::LESS_THAN_FIVE
        ];

        /** @var article $resource */
        $resource = article::create($data);

        $user2 = $gen->create_user();
        $this->setUser($user2);

        $exception = null;
        try {
            $data['name'] = ";pkdowq dddwew";
            $resource->update($data, $user2->id);
        } catch (resource_exception $e) {
            $exception = $e;
        }

        $this->assertNotNull($exception);
        $this->assertEquals(get_string('error:update', 'engage_article'), $exception->getMessage());
        $this->assertNotEquals($data['name'], $resource->get_name());
    }

    /**
     * @return void
     */
    public function test_update_article_via_graphql(): void {
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        $article = article::create(
            [
                'name' => "Hello world",
                'content' => "Abcde eee",
                'timeview' => time_view::LESS_THAN_FIVE
            ]
        );

        $args = [
            'resourceid' => $article->get_id(),
            'name' => "Bolobala",
            'access' => 'PUBLIC',
            'format' => FORMAT_PLAIN
        ];

        $ec = execution_context::create('ajax', 'engage_article_update_article');
        $result = graphql::execute_operation($ec, $args);

        $this->assertEmpty($result->errors);
        $this->assertNotEmpty($result->data);

        $this->assertArrayHasKey('article', $result->data);
        $article = $result->data['article'];

        $this->assertEquals('Bolobala', $article['resource']['name']);
        $this->assertEquals('PUBLIC', $article['resource']['access']);
    }
}