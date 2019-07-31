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
 * @package totara_playlist
 */
defined('MOODLE_INTERNAL') || die();

use totara_playlist\playlist;
use core\webapi\execution_context;
use totara_webapi\graphql;

class totara_playlist_create_testcase extends advanced_testcase {
    /**
     * @return void
     */
    public function test_create_playlist(): void {
        global $DB;

        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        $playlist = playlist::create('Hello world');
        $this->assertTrue($DB->record_exists('playlist', ['id' => $playlist->get_id()]));

        $this->assertEquals('Hello world', $playlist->get_name());
    }

    /**
     * @return void
     */
    public function test_create_playlist_with_graphql(): void {
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        $parameters = [
            'name' => 'Hello World',
            'summary' => "This is just a summary",
            'summary_format' => FORMAT_PLAIN,
        ];

        $ec = execution_context::create('ajax', 'totara_playlist_create_playlist');
        $result = graphql::execute_operation($ec, $parameters);

        $this->assertEmpty($result->errors);
        $this->assertNotEmpty($result->data);

        $this->assertArrayHasKey('playlist', $result->data);
        $playlist = $result->data['playlist'];

        $this->assertEquals('Hello World', $playlist['name']);
        $this->assertEquals('This is just a summary', $playlist['summary']);
    }
}