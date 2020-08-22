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
 * @author Qingyang Liu <qingyang.liu@totaralearning.com>
 * @package totara_engage
 */
defined('MOODLE_INTERNAL') || die();

use totara_engage\access\access;
use totara_engage\share\manager as share_manager;
use core\webapi\execution_context;
use totara_webapi\graphql;

class totara_engage_unshare_testcase extends advanced_testcase {
    /**
     *  @return void
     */
    public function test_unshare(): void {
        global $DB;

        $gen = $this->getDataGenerator();

        /** @var engage_article_generator $articlegen */
        $article_gen = $gen->get_plugin_generator('engage_article');

        // Create users.
        $users = $article_gen->create_users(2);

        // Create article.
        $this->setUser($users[0]);
        $article = $article_gen->create_article();

        // Setup recipients.
        $recipients = $article_gen->create_user_recipients([$users[1]]);
        $shares = $article_gen->share_article($article, $recipients);

        /** @var \totara_engage\share\share $share */
        $share = reset($shares);

        $this->assertTrue($DB->record_exists('engage_share', ['id' => $share->get_id()]));
        $this->assertEquals(1, $DB->get_field('engage_share_recipient', 'visibility', ['id' => $share->get_recipient_id()]));

        share_manager::unshare($share->get_recipient_id(), $article);

        $this->assertTrue($DB->record_exists('engage_share', ['id' => $share->get_id()]));
        $this->assertEquals(0, $DB->get_field('engage_share_recipient', 'visibility', ['id' => $share->get_recipient_id()]));

        /** @var engage_survey_generator $articlegen */
        $survey_gen = $gen->get_plugin_generator('engage_survey');
        $this->setUser($users[0]);
        $survey = $survey_gen->create_survey();

        $recipients = $survey_gen->create_user_recipients([$users[1]]);
        $shares = $survey_gen->share_survey($survey, $recipients);
        $share = reset($shares);

        $this->assertTrue($DB->record_exists('engage_share', ['id' => $share->get_id()]));
        $this->assertEquals(1, $DB->get_field('engage_share_recipient', 'visibility', ['id' => $share->get_recipient_id()]));

        share_manager::unshare($share->get_recipient_id(), $survey);

        $this->assertTrue($DB->record_exists('engage_share', ['id' => $share->get_id()]));
        $this->assertEquals(0, $DB->get_field('engage_share_recipient', 'visibility', ['id' => $share->get_recipient_id()]));
    }

    /**
     *  @return void
     */
    public function test_unshare_via_graphql(): void {
        global $DB;

        $gen = $this->getDataGenerator();
        /** @var totara_playlist_generator $playlistgen */
        $playlist_gen = $gen->get_plugin_generator('totara_playlist');

        // Create users.
        $users = $playlist_gen->create_users(2);

        // Create playlist.
        $this->setUser($users[0]);
        $playlist = $playlist_gen->create_playlist(['access' => access::PUBLIC]);

        $recipients = $playlist_gen->create_user_recipients([$users[1]]);
        $shares = $playlist_gen->share_playlist($playlist, $recipients);

        /** @var \totara_engage\share\share $share */
        $share = reset($shares);

        $this->assertTrue($DB->record_exists('engage_share', ['id' => $share->get_id()]));
        $this->assertEquals(1, $DB->get_field('engage_share_recipient', 'visibility', ['id' => $share->get_recipient_id()]));

        $this->setUser($users[0]);
        $parameters = [
            'recipient_id' => $share->get_recipient_id(),
            'component' => 'totara_playlist',
            'item_id' => $playlist->get_id()
        ];

        // Create share via graphql.
        $ec = execution_context::create('ajax', 'totara_engage_unshare');
        $result = graphql::execute_operation($ec, $parameters);

        $this->assertEmpty($result->errors);
        $this->assertNotEmpty($result->data);

        $this->assertTrue($DB->record_exists('engage_share', ['id' => $share->get_id()]));
        $this->assertEquals(0, $DB->get_field('engage_share_recipient', 'visibility', ['id' => $share->get_recipient_id()]));
    }
}