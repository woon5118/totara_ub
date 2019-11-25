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
 * @author Kian Nguyen <kian.nguyen@totaralearning.com>
 * @package core
 */
defined('MOODLE_INTERNAL') || die();

use core\json_editor\node\mention;

class core_json_editor_mention_testcase extends advanced_testcase {
    /**
     * @return void
     */
    public function test_validate_schema_with_valid_data(): void {
        $this->assertTrue(
            mention::validate_schema([
                'type' => mention::get_type(),
                'attrs' => [
                    'id' => 15,
                    'display' => 'some-text'
                ]
            ])
        );
    }

    /**
     * @return void
     */
    public function test_validate_schema_with_missing_keys(): void {
        $this->assertFalse(
            mention::validate_schema([
                'type' => mention::get_type(),
                'attrs' => [
                    'display' => 'some-text'
                ]
            ])
        );

        $this->assertFalse(
            mention::validate_schema([
                'type' => mention::get_type(),
                'attrs' => [
                    'id' => 15
                ]
            ])
        );
    }

    /**
     * @return void
     */
    public function test_validate_schema_with_extra_keys(): void {
        $this->assertFalse(
            mention::validate_schema([
                'type' => mention::get_type(),
                'attrs' => [
                    'display' => 'some-text',
                    'id' => 15,
                    'youtube' => 'dd'
                ]
            ])
        );

        $this->assertFalse(
            mention::validate_schema([
                'type' => mention::get_type(),
                'attrs' => [
                    'display' => 'some-text',
                    'id' => 15,
                ],
                'youtube' => 'dd'
            ])
        );

        $this->assertDebuggingCalledCount(2);
    }

    /**
     * @return void
     */
    public function test_clean_raw_node(): void {
        $data = [
            'type' => mention::get_type(),
            'attrs' => [
                'id' => 15,
                'display' => 'some_text'
            ]
        ];

        $cleaned = mention::clean_raw_node($data);
        $this->assertSame($data, $cleaned);
    }

    /**
     * @return void
     */
    public function test_clean_xss_node(): void {
        $data = [
            'type' => mention::get_type(),
            'attrs' => [
                'id' => '42',
                'display' => '<script type="text/javascript">alert(\'hello world\');</script>'
            ]
        ];

        $cleaned = mention::clean_raw_node($data);
        $this->assertNotSame($data, $cleaned);

        $this->assertArrayHasKey('attrs', $cleaned);
        $this->assertArrayHasKey('id', $cleaned['attrs']);
        $this->assertSame(42, $cleaned['attrs']['id']);
        $this->assertArrayHasKey('display', $cleaned['attrs']);
        $this->assertEquals("alert('hello world');", $cleaned['attrs']['display']);
    }

    /**
     * @return void
     */
    public function test_sanitizing_node(): void {
        $text = '<script>alert("hello world");</script>';
        $result = mention::sanitize_raw_node([
            'type' => mention::get_type(),
            'attrs' => [
                'id' => 42,
                'display' => $text
            ]
        ]);

        $this->assertArrayHasKey('type', $result);
        $this->assertArrayHasKey('attrs', $result);

        $this->assertSame(mention::get_type(), $result['type']);

        $this->assertIsArray($result['attrs']);
        $this->assertNotEmpty($result['attrs']);

        $this->assertArrayHasKey('id', $result['attrs']);
        $this->assertArrayHasKey('display', $result['attrs']);

        $this->assertEquals(42, $result['attrs']['id']);
        $this->assertEquals(s($text), $result['attrs']['display']);
    }
}