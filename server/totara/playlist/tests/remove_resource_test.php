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
defined('MOODLE_INTERNAL') || die();

use totara_engage\access\access;
use totara_engage\resource\resource_factory;
use totara_playlist\playlist;
use core\webapi\execution_context;
use totara_webapi\graphql;

class totara_playlist_remove_resource_testcase extends advanced_testcase {
    /**
     * @return void
     */
    public function test_remove_resource(): void {
        global $DB;

        $gen = $this->getDataGenerator();
        $user_one = $gen->create_user();
        $user_two = $gen->create_user();
        $this->setUser($user_one);
        /** @var totara_playlist_generator $playlistgen */
        $playlistgen = $gen->get_plugin_generator('totara_playlist');
        $playlist = $playlistgen->create_playlist();

        $this->setUser($user_two);
        /** @var engage_article_generator $articlegen */
        $articlegen = $gen->get_plugin_generator('engage_article');
        $article1 = $articlegen->create_article(['access' => access::PUBLIC]);
        $article2 = $articlegen->create_article(['access' => access::PUBLIC]);
        $article3 = $articlegen->create_article(['access' => access::PUBLIC]);

        $playlist->add_resource(resource_factory::create_instance_from_id($article1->get_id()), $user_one->id);
        $playlist->add_resource(resource_factory::create_instance_from_id($article2->get_id()), $user_one->id);
        $playlist->add_resource(resource_factory::create_instance_from_id($article3->get_id()), $user_one->id);

        $this->assertEquals(3, $DB->count_records('playlist_resource', ['playlistid' => $playlist->get_id()]));

        // Remove resource.
        $playlist->remove_resource(resource_factory::create_instance_from_id($article1->get_id()), $user_one->id);

        $this->assertEquals(2, $DB->count_records('playlist_resource', ['playlistid' => $playlist->get_id()]));
        $this->assertFalse($DB->record_exists('playlist_resource', ['resourceid' => $article1->get_id()]));
    }

    /**
     * @return void
     */
    public function test_remove_resource_via_graphql(): void {
        global $DB;

        $gen = $this->getDataGenerator();
        $user_one = $gen->create_user();
        $user_two = $gen->create_user();
        $this->setUser($user_one);
        /** @var totara_playlist_generator $playlistgen */
        $playlistgen = $gen->get_plugin_generator('totara_playlist');
        $playlist = $playlistgen->create_playlist();

        $this->setUser($user_two);
        /** @var engage_article_generator $articlegen */
        $articlegen = $gen->get_plugin_generator('engage_article');
        $article1 = $articlegen->create_article(['access' => access::PUBLIC]);
        $article2 = $articlegen->create_article(['access' => access::PUBLIC]);
        $article3 = $articlegen->create_article(['access' => access::PUBLIC]);

        $this->setUser($user_one);
        $playlist->add_resource(resource_factory::create_instance_from_id($article1->get_id()));
        $playlist->add_resource(resource_factory::create_instance_from_id($article2->get_id()));
        $playlist->add_resource(resource_factory::create_instance_from_id($article3->get_id()));

        $this->assertEquals(3, $DB->count_records('playlist_resource', ['playlistid' => $playlist->get_id()]));

        $ec = execution_context::create('ajax', 'totara_playlist_remove_resource');
        $result = graphql::execute_operation($ec, [
            'id' => $playlist->get_id(),
            'instanceid' => $article2->get_id()
        ]);

        $this->assertEmpty($result->errors);
        $this->assertNotEmpty($result->data);

        $this->assertEquals(2, $DB->count_records('playlist_resource', ['playlistid' => $playlist->get_id()]));
        $this->assertFalse($DB->record_exists('playlist_resource', ['resourceid' => $article2->get_id()]));
    }
}