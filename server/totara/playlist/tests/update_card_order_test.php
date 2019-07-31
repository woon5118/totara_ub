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
use totara_engage\access\access;
use totara_engage\resource\resource_factory;
use totara_playlist\local\helper;
use totara_playlist\playlist;
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

        helper::swap_card_sort_order($playlist, $article->get_id(), 3);

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
    }
}