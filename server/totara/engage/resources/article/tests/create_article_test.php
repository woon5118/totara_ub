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

use core\webapi\execution_context;
use totara_engage\timeview\time_view;
use totara_webapi\graphql;
use engage_article\totara_engage\resource\article;

class engage_article_create_article_testcase extends advanced_testcase {
    /**
     * @return void
     */
    public function test_create_article_via_graphql(): void {
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        $ec = execution_context::create('ajax', 'engage_article_create_article');
        $parameters = [
            'name' => "Hello world",
            'content' => 'Wassup bolobala',
            'format' => FORMAT_PLAIN,
            'timeview' => time_view::get_code(time_view::LESS_THAN_FIVE)
        ];

        $result = graphql::execute_operation($ec, $parameters);

        $this->assertEmpty($result->errors);
        $this->assertNotEmpty($result->data);
        $this->assertArrayHasKey('article', $result->data);

        $article = $result->data['article'];
        $this->assertArrayHasKey('resource', $article);
        $this->assertEquals($parameters['name'], $article['resource']['name']);
        $this->assertEquals($parameters['content'], $article['content']);
    }

    /**
     * @return void
     */
    public function test_create_article(): void {
        global $DB;

        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        $data = [
            'name' => "Hello world",
            'content' => 'Wassup llowjkfwoj',
            'timeview' => time_view::LESS_THAN_FIVE
        ];

        $resourcetype = article::get_resource_type();
        $resource = article::create($data);

        $sql = '
            SELECT 1 FROM "ttr_engage_article"  ea
            INNER JOIN "ttr_engage_resource" er ON er.instanceid = ea.id AND er.resourcetype = :type
            WHERE ea.id = :id
        ';

        $params = [
            'id' => $resource->get_instanceid(),
            'type' => $resourcetype
        ];


        $this->assertTrue($DB->record_exists_sql($sql, $params));
    }

    /**
     * This test is about making sure that no article is being created when the name is not
     * given properly.
     *
     * @return void
     */
    public function test_create_article_with_empty_name_via_graphql(): void {
        $generator = $this->getDataGenerator();
        $user = $generator->create_user();

        $this->setUser($user);
        $parameters = [
            'name' => '',
            'content' => 'Wassup wasabi',
            'timeview' => time_view::get_code(time_view::LESS_THAN_FIVE)
        ];

        $ec = execution_context::create('ajax', 'engage_article_create_article');
        $result = graphql::execute_operation($ec, $parameters);

        $this->assertNotEmpty($result->errors);
        $error = reset($result->errors);

        $this->assertStringContainsString(
            "Validation run for property 'name' has been failed",
            $error->getMessage()
        );
    }
}