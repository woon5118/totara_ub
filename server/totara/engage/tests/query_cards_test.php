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
 * @package totara_engage
 */
defined('MOODLE_INTERNAL') || die();

use totara_engage\access\access;
use totara_engage\card\card_loader;
use totara_engage\query\query;

class totara_engage_query_cards_testcase extends advanced_testcase {
    /**
     * A test to asure that our query builder is working fine. Since there can be more than
     * one builder being unioned together.
     *
     * @return void
     */
    public function test_load_cards(): void {
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        $query = new query();
        $loader = new card_loader($query);
        $result = $loader->fetch();

        $cards = $result->get_items()->all();

        $this->assertEquals(0, $result->get_total());
        $this->assertEmpty($cards);
    }

    /**
     * @return void
     */
    public function test_load_valid_cards(): void {
        $gen = $this->getDataGenerator();

        $user = $gen->create_user();
        $this->setUser($user);

        /** @var engage_article_generator $article_generator */
        $article_generator = $gen->get_plugin_generator('engage_article');

        /** @var engage_survey_generator $survey_generator */
        $survey_generator = $gen->get_plugin_generator('engage_survey');

        $article_generator->create_article();
        $survey_generator->create_survey();

        $query = new query();
        $loader = new card_loader($query);
        $result = $loader->fetch();

        $cards = $result->get_items()->all();

        $this->assertEquals(2, $result->get_total());
        $this->assertNotEmpty($cards);
        $this->assertCount(2, $cards);
    }

    /**
     * Assert that only public & shared with active user cards
     * are returned for the other user library
     */
    public function test_load_public_and_shared_cards(): void {
        $gen = $this->getDataGenerator();

        $user = $gen->create_user();
        $this->setUser($user);

        /** @var engage_article_generator $article_generator */
        $article_generator = $gen->get_plugin_generator('engage_article');
        /** @var engage_survey_generator $survey_generator */
        $survey_generator = $gen->get_plugin_generator('engage_survey');
        /** @var totara_playlist_generator $playlist_generator */
        $playlist_generator = $gen->get_plugin_generator('totara_playlist');

        $article_generator->create_article(['name' => 'Public Resource', 'access' => access::PUBLIC]);
        $article_generator->create_article(['name' => 'Private Resource', 'access' => access::PRIVATE]);
        $shared_article = $article_generator->create_article(['name' => 'Restricted Resource', 'access' => access::RESTRICTED]);
        $survey_generator->create_survey('Public Survey', ['A', 'B'], \totara_engage\answer\answer_type::MULTI_CHOICE, [
            'access' => access::PUBLIC,
        ]);
        $survey_generator->create_survey('Private Survey', ['A', 'B'], \totara_engage\answer\answer_type::MULTI_CHOICE, [
            'access' => access::PRIVATE,
        ]);
        $shared_survey = $survey_generator->create_survey('Restricted Survey', ['A', 'B'], \totara_engage\answer\answer_type::MULTI_CHOICE, [
            'access' => access::RESTRICTED,
        ]);

        $playlist_generator->create_playlist(['name' => 'Public Playlist', 'access' => access::PUBLIC]);
        $playlist_generator->create_playlist(['name' => 'Private Playlist', 'access' => access::PRIVATE]);
        $shared_playlist = $playlist_generator->create_playlist(['name' => 'Restricted Playlist', 'access' => access::RESTRICTED]);

        $user2 = $gen->create_user();

        // Share the restricted resources/surveys with $user2
        $article_recipient = $article_generator->create_user_recipients([$user2]);
        $survey_recipient = $survey_generator->create_user_recipients([$user2]);
        $playlist_recipient = $playlist_generator->create_user_recipients([$user2]);

        $article_generator->share_article($shared_article, $article_recipient);
        $survey_generator->share_survey($shared_survey, $survey_recipient);
        $playlist_generator->share_playlist($shared_playlist, $playlist_recipient);

        $this->setUser($user2);

        $query = new query();
        $query->set_area('otheruserlib');
        $query->set_userid($user->id);
        $query->set_share_recipient_id($user2->id);

        $loader = new card_loader($query);
        $result = $loader->fetch();

        $this->assertEquals(6, $result->get_total());

        $valid_cards = [
            'Public Resource',
            'Public Survey',
            'Public Playlist',
            'Restricted Survey',
            'Restricted Resource',
            'Restricted Playlist',
        ];
        $cards = $result->get_items()->all();
        $this->assertNotEmpty($cards);
        $this->assertCount(6, $cards);

        /** @var \totara_engage\card\card $card */
        foreach ($cards as $card) {
            $this->assertTrue(in_array($card->get_name(), $valid_cards), $card->get_name());
        }

        // Now test that user3 can only see the public cards (not the shared cards)
        $user3 = $gen->create_user();
        $this->setUser($user3);

        $query = new query();
        $query->set_area('otheruserlib');
        $query->set_userid($user->id);
        $query->set_share_recipient_id($user3->id);

        $loader = new card_loader($query);
        $result = $loader->fetch();

        $this->assertEquals(3, $result->get_total());

        $valid_cards = [
            'Public Resource',
            'Public Survey',
            'Public Playlist',
        ];
        $cards = $result->get_items()->all();
        $this->assertNotEmpty($cards);
        $this->assertCount(3, $cards);

        /** @var \totara_engage\card\card $card */
        foreach ($cards as $card) {
            $this->assertTrue(in_array($card->get_name(), $valid_cards), $card->get_name());
        }
    }
}