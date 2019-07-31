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
 * test the endpoints for recommending articles by article or user
 */
class ml_recommender_get_recommended_articles_test extends advanced_testcase {
    /**
     * Test articles are recommended by article id
     */
    public function test_recommended_articles_graphql() {
        $generator = $this->getDataGenerator();
        /** @var engage_article_generator $article_generator */
        $article_generator = $generator->get_plugin_generator('engage_article');
        /** @var totara_topic_generator $topic_generator */
        $topic_generator = $generator->get_plugin_generator('totara_topic');

        $this->setAdminUser();
        $topic = $topic_generator->create_topic();

        $user = $generator->create_user();
        $this->setUser($user);

        /** @var ml_recommender_generator $recommendations_generator */
        $recommendations_generator = $generator->get_plugin_generator('ml_recommender');

        // This is our target article. We're going to ask for recommendations related to this article
        $target_article = $article_generator->create_article([
            'name' => 'Target Article',
            'userid' => $user->id,
            'access' => access::PUBLIC,
            'topics' => [$topic->get_id()],
        ]);
        $target_article2 = $article_generator->create_article([
            'name' => 'Target Article',
            'userid' => $user->id,
            'access' => access::PUBLIC,
            'topics' => [$topic->get_id()],
        ]);

        // Going to create a few articles, then recommend *some* of them
        $articles = [];
        for ($i = 1; $i <= 10; $i++) {
            $article = $article_generator->create_article([
                'name' => 'A' . $i,
                'userid' => $user->id,
                'access' => access::PUBLIC,
                'topics' => [$topic->get_id()],
            ]);
            $articles[] = $article;

            // Recommend it if it's > 5
            if ($i > 5) {
                $recommendations_generator->create_item_recommendation(
                    $target_article->get_id(),
                    $article->get_id(),
                    $article::get_resource_type(),
                    null,
                    2.5
                );
            }
        }

        // Now we're going to ask for some recommended articles
        advanced_feature::enable('ml_recommender');
        $ec = execution_context::create('ajax', 'ml_recommender_get_recommended_articles');
        $parameters = [
            'article_id' => $target_article->get_id(),
            'cursor' => null,
        ];
        $result = graphql::execute_operation($ec, $parameters);
        $this->assertNotNull($result->data);

        $cursor = $result->data['cursor'];
        $results = $result->data['articles'];

        $this->assertEquals(5, $cursor['total']);
        $this->assertCount(5, $results);

        // Quick check
        $expected = ['A5', 'A6', 'A7', 'A8', 'A9', 'A10'];
        foreach ($results as $result) {
            $this->assertTrue(in_array($result['name'], $expected));
        }

        // Now check for no results
        $parameters = [
            'article_id' => $target_article2->get_id(),
            'cursor' => null,
        ];
        $result = graphql::execute_operation($ec, $parameters);
        $this->assertNotNull($result->data);

        $cursor = $result->data['cursor'];
        $results = $result->data['articles'];

        $this->assertEquals(0, $cursor['total']);
        $this->assertCount(0, $results);

        // Disable the feature & make sure we get no results back
        advanced_feature::disable('ml_recommender');
        $ec = execution_context::create('ajax', 'ml_recommender_get_recommended_articles');
        $parameters = [
            'article_id' => $target_article->get_id(),
            'cursor' => null,
        ];
        $result = graphql::execute_operation($ec, $parameters);
        $this->assertNotNull($result->data);

        $cursor = $result->data['cursor'];
        $results = $result->data['articles'];

        $this->assertNull($cursor);
        $this->assertEmpty($results);
    }

    /**
     * Test articles are recommended by user id
     */
    public function test_recommended_articles_by_user_graphql() {
        $generator = $this->getDataGenerator();
        /** @var engage_article_generator $article_generator */
        $article_generator = $generator->get_plugin_generator('engage_article');
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

        // Going to create a few articles, then recommend *some* of them
        $articles = [];
        for ($i = 1; $i <= 10; $i++) {
            $article = $article_generator->create_article([
                'name' => 'A' . $i,
                'userid' => $user->id,
                'access' => access::PUBLIC,
                'topics' => [$topic->get_id()],
            ]);
            $articles[] = $article;

            // Recommend it if it's > 5
            if ($i > 5) {
                $recommendations_generator->create_user_recommendation(
                    $user2->id,
                    $article->get_id(),
                    $article::get_resource_type(),
                    null,
                    2.5
                );
            }
        }

        // Now we're going to ask for some recommended articles
        advanced_feature::enable('ml_recommender');
        $ec = execution_context::create('ajax', 'ml_recommender_get_recommended_user_articles');
        $parameters = [
            'user_id' => $user2->id,
            'cursor' => null,
        ];
        $result = graphql::execute_operation($ec, $parameters);
        $this->assertNotNull($result->data);

        $cursor = $result->data['cursor'];
        $results = $result->data['articles'];

        $this->assertEquals(5, $cursor['total']);
        $this->assertCount(5, $results);

        // Quick check
        $expected = ['A5', 'A6', 'A7', 'A8', 'A9', 'A10'];
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
        $results = $result->data['articles'];

        $this->assertEquals(0, $cursor['total']);
        $this->assertCount(0, $results);

        // Disable the feature & check there are no results
        advanced_feature::disable('ml_recommender');
        $ec = execution_context::create('ajax', 'ml_recommender_get_recommended_user_articles');
        $parameters = [
            'user_id' => $user2->id,
            'cursor' => null,
        ];
        $result = graphql::execute_operation($ec, $parameters);
        $this->assertNotNull($result->data);

        $cursor = $result->data['cursor'];
        $results = $result->data['articles'];

        $this->assertNull($cursor);
        $this->assertEmpty($results);
    }
}