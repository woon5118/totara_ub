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
 * @author Cody Finegan <cody.finegan@totaralearning.com>
 * @package ml_recommender
 */
defined('MOODLE_INTERNAL') || die();

use core\webapi\execution_context;
use totara_core\advanced_feature;
use totara_engage\access\access;
use totara_webapi\graphql;

/**
 * Test the endpoints for recommending playlists by playlist or user
 */
class ml_recommender_get_recommended_playlists_test extends advanced_testcase {
    /**
     * Test playlists are recommended by playlist id
     */
    public function test_recommended_playlists_graphql() {
        $generator = $this->getDataGenerator();
        /** @var totara_playlist_generator $playlist_generator */
        $playlist_generator = $generator->get_plugin_generator('totara_playlist');
        /** @var totara_topic_generator $topic_generator */
        $topic_generator = $generator->get_plugin_generator('totara_topic');

        $this->setAdminUser();
        $topic = $topic_generator->create_topic();

        $user = $generator->create_user();
        $this->setUser($user);

        /** @var ml_recommender_generator $recommendations_generator */
        $recommendations_generator = $generator->get_plugin_generator('ml_recommender');

        // This is our target playlist. We're going to ask for recommendations related to this playlist
        $target_playlist = $playlist_generator->create_playlist([
            'name' => 'Target Playlist',
            'userid' => $user->id,
            'access' => access::PUBLIC,
            'topics' => [$topic->get_id()],
        ]);
        $target_playlist2 = $playlist_generator->create_playlist([
            'name' => 'Target Playlist',
            'userid' => $user->id,
            'access' => access::PUBLIC,
            'topics' => [$topic->get_id()],
        ]);

        // Going to create a few playlists, then recommend *some* of them
        $playlists = [];
        for ($i = 1; $i <= 10; $i++) {
            $playlist = $playlist_generator->create_playlist([
                'name' => 'P' . $i,
                'userid' => $user->id,
                'access' => access::PUBLIC,
                'topics' => [$topic->get_id()],
            ]);
            $playlists[] = $playlist;

            // Recommend it if it's > 5
            if ($i > 5) {
                $recommendations_generator->create_item_recommendation(
                    $target_playlist->get_id(),
                    $playlist->get_id(),
                    $playlist::get_resource_type(),
                    null,
                    2.5
                );
            }
        }

        // Now we're going to ask for some recommended playlists
        advanced_feature::enable('ml_recommender');
        $ec = execution_context::create('ajax', 'ml_recommender_get_recommended_playlists');
        $parameters = [
            'playlist_id' => $target_playlist->get_id(),
            'cursor' => null,
        ];
        $result = graphql::execute_operation($ec, $parameters);
        $this->assertNotNull($result->data);

        $cursor = $result->data['cursor'];
        $results = $result->data['playlists'];

        $this->assertEquals(5, $cursor['total']);
        $this->assertCount(5, $results);

        // Quick check
        $expected = ['P5', 'P6', 'P7', 'P8', 'P9', 'P10'];
        foreach ($results as $result) {
            $this->assertTrue(in_array($result['name'], $expected));
        }

        // Now check for no results
        $parameters = [
            'playlist_id' => $target_playlist2->get_id(),
            'cursor' => null,
        ];
        $result = graphql::execute_operation($ec, $parameters);
        $this->assertNotNull($result->data);

        $cursor = $result->data['cursor'];
        $results = $result->data['playlists'];

        $this->assertEquals(0, $cursor['total']);
        $this->assertCount(0, $results);

        // Disable the feature
        advanced_feature::disable('ml_recommender');
        $ec = execution_context::create('ajax', 'ml_recommender_get_recommended_playlists');
        $parameters = [
            'playlist_id' => $target_playlist->get_id(),
            'cursor' => null,
        ];
        $result = graphql::execute_operation($ec, $parameters);
        $this->assertNotNull($result->data);

        $cursor = $result->data['cursor'];
        $results = $result->data['playlists'];

        $this->assertNull($cursor);
        $this->assertEmpty($results);
    }

    /**
     * Test playlists are recommended by user id
     */
    public function test_recommended_playlists_by_user_graphql() {
        $generator = $this->getDataGenerator();
        /** @var totara_playlist_generator $playlist_generator */
        $playlist_generator = $generator->get_plugin_generator('totara_playlist');
        /** @var totara_topic_generator $topic_generator */
        $topic_generator = $generator->get_plugin_generator('totara_topic');

        $this->setAdminUser();
        $topic = $topic_generator->create_topic();

        $user = $generator->create_user();
        $this->setUser($user);

        /** @var ml_recommender_generator $recommendations_generator */
        $recommendations_generator = $generator->get_plugin_generator('ml_recommender');

        // We're going to recommend for user 2
        $user2 = $generator->create_user();

        // Going to create a few playlists, then recommend *some* of them
        $playlists = [];
        for ($i = 1; $i <= 10; $i++) {
            $playlist = $playlist_generator->create_playlist([
                'name' => 'P' . $i,
                'userid' => $user->id,
                'access' => access::PUBLIC,
                'topics' => [$topic->get_id()],
            ]);
            $playlists[] = $playlist;

            // Recommend it if it's > 5
            if ($i > 5) {
                $recommendations_generator->create_user_recommendation(
                    $user2->id,
                    $playlist->get_id(),
                    $playlist::get_resource_type(),
                    null,
                    2.5
                );
            }
        }

        // Now we're going to ask for some recommended playlists
        advanced_feature::enable('ml_recommender');
        $ec = execution_context::create('ajax', 'ml_recommender_get_recommended_user_playlists');
        $parameters = [
            'user_id' => $user2->id,
            'cursor' => null,
        ];
        $result = graphql::execute_operation($ec, $parameters);
        $this->assertNotNull($result->data);

        $cursor = $result->data['cursor'];
        $results = $result->data['playlists'];

        $this->assertEquals(5, $cursor['total']);
        $this->assertCount(5, $results);

        // Quick check
        $expected = ['P5', 'P6', 'P7', 'P8', 'P9', 'P10'];
        foreach ($results as $result) {
            $this->assertTrue(in_array($result['name'], $expected));
        }

        // Now check for no results
        $parameters = [
            'user_id' => $user->id,
            'cursor' => null,
        ];
        $result = graphql::execute_operation($ec, $parameters);
        $this->assertNotNull($result->data);

        $cursor = $result->data['cursor'];
        $results = $result->data['playlists'];

        $this->assertEquals(0, $cursor['total']);
        $this->assertCount(0, $results);

        // Disable the feature
        advanced_feature::disable('ml_recommender');
        $ec = execution_context::create('ajax', 'ml_recommender_get_recommended_user_playlists');
        $parameters = [
            'user_id' => $user2->id,
            'cursor' => null,
        ];
        $result = graphql::execute_operation($ec, $parameters);
        $this->assertNotNull($result->data);

        $cursor = $result->data['cursor'];
        $results = $result->data['playlists'];

        $this->assertNull($cursor);
        $this->assertEmpty($results);
    }
}