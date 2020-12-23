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

use totara_core\http\clients\curl_client;
use totara_core\http\request;

class totara_core_http_curl_client_testcase extends advanced_testcase {
    /** @var curl_client */
    private $client;

    public function setUp(): void {
        $this->client = new curl_client();
    }

    public function tearDown(): void {
        $this->client = null;
        // TODO: TL-28914 will provide a real solution
        global $CFG;
        require_once($CFG->dirroot.'/lib/filelib.php');
        $prop = new ReflectionProperty(curl::class, 'mockresponses');
        $prop->setAccessible(true);
        $prop->setValue(null, []);
    }

    public function test_execute() {
        global $CFG;
        require_once($CFG->dirroot.'/lib/filelib.php');
        // NOTE: curl::mock_response() is a stack (LIFO)'
        curl::mock_response('kia ora koutou katoa'); // patch
        curl::mock_response('<p>New File</p>'); // put
        curl::mock_response('OK'); // head
        curl::mock_response('hooray!'); // delete
        curl::mock_response('{"error": "Lorem ipsum dolor sit amet, consectetur adipiscing elit."}'); // post
        curl::mock_response('Maecenas eu placerat ex, vitae consectetur dolor.'); // get
        $request = request::get('https://example.com/api/test/');
        $response = $this->client->execute($request);
        $this->assertEquals('Maecenas eu placerat ex, vitae consectetur dolor.', $response->get_body());
        $request = request::post('https://example.com/api/test/people', 'kiaora');
        $response = $this->client->execute($request);
        $this->assertEquals((object)['error' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.'], $response->get_body_as_json());
        $request = request::delete('https://example.com/api/test/people/me', ['Authorization' => 'bear gR0wl3']);
        $response = $this->client->execute($request);
        $this->assertEquals('hooray!', $response->get_body());
        $request = request::head('https://example.com/api/test/people/me');
        $response = $this->client->execute($request);
        $this->assertEquals('OK', $response->get_body());
        // FIXME: TL-28914 support the PUT method
        $request = request::put('https://example.com/api/test/docs', 'New File');
        try {
            $response = $this->client->execute($request);
            $this->fail('coding_exception expected');
            $this->assertEquals('<p>New File</p>', $response->get_body());
        } catch (coding_exception $ex) {
            $this->assertStringContainsString("Unsupported method: 'PUT'", $ex->getMessage());
        }
        // FIXME: TL-28914 support the PATCH method
        $request = request::patch('https://example.com/api/test/people/me', ['age' => 100]);
        try {
            $response = $this->client->execute($request);
            $this->fail('coding_exception expected');
            $this->assertEquals('kia ora koutou katoa', $response->get_body());
        } catch (coding_exception $ex) {
            $this->assertStringContainsString("Unsupported method: 'PATCH'", $ex->getMessage());
        }
    }
}
