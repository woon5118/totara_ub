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

        $article_generator = $gen->get_plugin_generator('engage_article');
        $survey_generator = $gen->get_plugin_generator('engage_survey');

        $article_generator->generate_random();
        $survey_generator->generate_random();

        $query = new query();
        $loader = new card_loader($query);
        $result = $loader->fetch();

        $cards = $result->get_items()->all();

        $this->assertEquals(2, $result->get_total());
        $this->assertNotEmpty($cards);
        $this->assertCount(2, $cards);
    }
}