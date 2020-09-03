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
 * @author Qingyang Liu <qingyang.liu@totaralearning.com>
 * @package totara_playlist
 */

use totara_engage\resource\resource_factory;
use totara_playlist\exception\playlist_exception;
use totara_playlist\local\helper;
use core\webapi\execution_context;
use totara_webapi\graphql;

defined('MOODLE_INTERNAL') || die();

class totara_playlist_update_card_order_testcase extends advanced_testcase {
    /**
     * @return void
     */
    public function test_swap_card(): void {
        global $DB;

        $gen = $this->getDataGenerator();
        $this->setAdminUser();

        /** @var totara_playlist_generator $playlistgen */
        $playlistgen = $gen->get_plugin_generator('totara_playlist');
        $playlist = $playlistgen->create_playlist();

        /** @var engage_article_generator $articlegen */
        $articlegen = $gen->get_plugin_generator('engage_article');
        $article = $articlegen->create_article();
        $article1 = $articlegen->create_article();
        $article2 = $articlegen->create_article();

        $playlist->add_resource(resource_factory::create_instance_from_id($article->get_id()));
        $playlist->add_resource(resource_factory::create_instance_from_id($article1->get_id()));
        $playlist->add_resource(resource_factory::create_instance_from_id($article2->get_id()));

        $playlist->load_resources();
        foreach ($playlist->get_resources() as $resource) {
            if ((int)$resource->resourceid === $article->get_id()) {
                $this->assertEquals(1, $resource->sortorder);
            }
        }
        $this->assertEquals(
            1,
            $DB->get_field(
                'playlist_resource',
                'sortorder',
                ['playlistid' => $playlist->get_id(), 'resourceid' => $article->get_id()]
            )
        );

        // The last array index is 2.
        helper::swap_card_sort_order($playlist, $article->get_id(), 2);

        $this->assertEquals(
            3,
            $DB->get_field(
                'playlist_resource',
                'sortorder',
                ['playlistid' => $playlist->get_id(), 'resourceid' => $article->get_id()]
            )
        );

        $this->assertEquals(
            2,
            $DB->get_field(
                'playlist_resource',
                'sortorder',
                ['playlistid' => $playlist->get_id(), 'resourceid' => $article2->get_id()]
            )
        );

        $this->assertEquals(
            1,
            $DB->get_field(
                'playlist_resource',
                'sortorder',
                ['playlistid' => $playlist->get_id(), 'resourceid' => $article1->get_id()]
            )
        );

        // Test order is negative.
        $this->expectException(playlist_exception::class, get_string('error:update_order', 'totara_playlist'));
        helper::swap_card_sort_order($playlist, $article->get_id(), -1);
    }

    /**
     * @return void
     */
    public function test_swap_card_order_with_exception(): void {
        $gen = $this->getDataGenerator();
        $this->setAdminUser();

        /** @var totara_playlist_generator $playlistgen */
        $playlistgen = $gen->get_plugin_generator('totara_playlist');
        $playlist = $playlistgen->create_playlist();

        /** @var engage_article_generator $articlegen */
        $articlegen = $gen->get_plugin_generator('engage_article');
        $article = $articlegen->create_article();
        $article1 = $articlegen->create_article();
        $article2 = $articlegen->create_article();
        $article3 = $articlegen->create_article();

        $playlist->add_resource(resource_factory::create_instance_from_id($article->get_id()));
        $playlist->add_resource(resource_factory::create_instance_from_id($article1->get_id()));
        $playlist->add_resource(resource_factory::create_instance_from_id($article2->get_id()));

        // Test resource is not in the playlist
        $this->expectException(
            'coding_exception',
            "Coding error detected, it must be fixed by a programmer: Resource with {$article3->get_id()} is not in the playlist"
        );
        helper::swap_card_sort_order($playlist, $article3->get_id(), 2);
    }

    /**
     * @return void
     */
    public function test_swap_card_order_out_of_boundary(): void {
        $gen = $this->getDataGenerator();
        $this->setAdminUser();

        /** @var totara_playlist_generator $playlistgen */
        $playlistgen = $gen->get_plugin_generator('totara_playlist');
        $playlist = $playlistgen->create_playlist();

        /** @var engage_article_generator $articlegen */
        $articlegen = $gen->get_plugin_generator('engage_article');
        $article = $articlegen->create_article();
        $article1 = $articlegen->create_article();
        $article2 = $articlegen->create_article();

        $playlist->add_resource(resource_factory::create_instance_from_id($article->get_id()));
        $playlist->add_resource(resource_factory::create_instance_from_id($article1->get_id()));
        $playlist->add_resource(resource_factory::create_instance_from_id($article2->get_id()));

        // Test is out of boundary.
        $this->expectException(playlist_exception::class, get_string('error:update_order', 'totara_playlist'));
        helper::swap_card_sort_order($playlist, $article->get_id(), 5);
    }

    /**
     * @return void
     */
    public function test_swap_card_order_with_graphql(): void {
        global $DB;

        $gen = $this->getDataGenerator();
        $this->setAdminUser();

        /** @var totara_playlist_generator $playlistgen */
        $playlistgen = $gen->get_plugin_generator('totara_playlist');
        $playlist = $playlistgen->create_playlist();

        /** @var engage_article_generator $articlegen */
        $articlegen = $gen->get_plugin_generator('engage_article');
        $article = $articlegen->create_article();
        $article1 = $articlegen->create_article();
        $article2 = $articlegen->create_article();

        $playlist->add_resource(resource_factory::create_instance_from_id($article->get_id()));
        $playlist->add_resource(resource_factory::create_instance_from_id($article1->get_id()));
        $playlist->add_resource(resource_factory::create_instance_from_id($article2->get_id()));

        $this->assertEquals(
            1,
            $DB->get_field(
                'playlist_resource',
                'sortorder',
                ['resourceid' => $article->get_id(), 'playlistid' => $playlist->get_id()])
        );

        $parameters = [
            'id' => $playlist->get_id(),
            'order' => 2,
            'instanceid' => $article->get_id()
        ];

        $ec = execution_context::create('ajax', 'totara_playlist_update_card_order');
        $result = graphql::execute_operation($ec, $parameters);

        $this->assertEmpty($result->errors);
        $this->assertNotEmpty($result->data);
        $this->assertEquals(
            3,
            $DB->get_field(
                'playlist_resource',
                'sortorder',
                ['resourceid' => $article->get_id(), 'playlistid' => $playlist->get_id()])
        );

        // Check the resource is not in the playlist, exception will be fired.
        $article3 = $articlegen->create_article();
        $parameters = [
            'id' => $playlist->get_id(),
            'order' => 2,
            'instanceid' => $article3->get_id()
        ];

        $ec = execution_context::create('ajax', 'totara_playlist_update_card_order');
        $result = graphql::execute_operation($ec, $parameters);
        $this->assertNotEmpty($result->errors);


        // Check order is out of boundary
        $parameters = [
            'id' => $playlist->get_id(),
            'order' => -1 ,
            'instanceid' => $article->get_id()
        ];

        $ec = execution_context::create('ajax', 'totara_playlist_update_card_order');
        $result = graphql::execute_operation($ec, $parameters);
        $this->assertNotEmpty($result->errors);

        // Check order is out of boundary
        $parameters = [
            'id' => $playlist->get_id(),
            'order' => 3 ,
            'instanceid' => $article3->get_id()
        ];

        $ec = execution_context::create('ajax', 'totara_playlist_update_card_order');
        $result = graphql::execute_operation($ec, $parameters);
        $this->assertNotEmpty($result->errors);
        $error = current($result->errors);
        $this->assertEquals(get_string('error:update_order', 'totara_playlist'), $error->getMessage());
    }
}