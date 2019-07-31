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
 * @author Tatsuhiro Kirihara <tatsuhiro.kirihara@totaralearning.com>
 * @package totara_msteams
 */

use totara_msteams\botfw\http\clients\curl_client;
use totara_msteams\botfw\http\request;

class totara_msteams_botfw_curl_client_testcase extends advanced_testcase {
    /** @var curl_client */
    private $client;

    public function setUp(): void {
        $this->client = new curl_client();
    }

    public function tearDown(): void {
        $this->client = null;
    }

    public function test_execute() {
        global $CFG;
        require_once($CFG->dirroot.'/lib/filelib.php');
        // NOTE: curl::mock_response() is a stack (LIFO)
        curl::mock_response('{"error": "Lorem ipsum dolor sit amet, consectetur adipiscing elit."}');
        curl::mock_response('Maecenas eu placerat ex, vitae consectetur dolor.');
        $request = request::get('https://example.com/api/test/');
        $response = $this->client->execute($request);
        $this->assertEquals('Maecenas eu placerat ex, vitae consectetur dolor.', $response->get_body());
        $request = request::post('https://example.com/api/test/', 'kiaora');
        $response = $this->client->execute($request);
        $this->assertEquals((object)['error' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.'], $response->get_body_as_json());
    }
}
