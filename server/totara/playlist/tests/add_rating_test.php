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

use core\webapi\execution_context;
use totara_webapi\graphql;
use totara_engage\access\access;
use totara_playlist\playlist;

class totara_playlist_add_rating_test extends advanced_testcase {
    /**
     * @return void
     */
    public function test_add_rating(): void {
        global $DB;

        $gen = $this->getDataGenerator();
        /** @var totara_playlist_generator $playlistgen */
        $playlistgen = $gen->get_plugin_generator('totara_playlist');

        // Create two users.
        $users = $playlistgen->create_users(2);

        // Set viewer
        $this->setUser($users[1]);
        $playlist = $playlistgen->create_playlist([
            'access' => access::PUBLIC,
            'userid' => $users[0]->id // Owner
        ]);


        $parameters = [
            'playlistid' => $playlist->get_id(),
            'ratingarea' => 'playlist',
            'rating' => 2
        ];

        $ec = execution_context::create('ajax', 'totara_playlist_add_rating');
        $result = graphql::execute_operation($ec, $parameters);
        $this->assertEmpty($result->errors);
        $this->assertNotEmpty($result->data);

        $params = [
            'component' => $playlist::get_resource_type(),
            'area' => playlist::RATING_AREA,
            'instanceid' => $playlist->get_id()
        ];

        $sql = 'SELECT r.* FROM {engage_rating} r WHERE r.component = :component AND r.instanceid = :instanceid AND r.area = :area';
        $this->assertTrue($DB->record_exists_sql($sql, $params));
        $this->assertTrue($DB->record_exists('engage_rating', ['instanceid' => $playlist->get_id()]));
    }
}