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

use core\json_editor\node\link_block;

class core_json_editor_link_block_testcase extends advanced_testcase {
    /**
     * @return void
     */
    public function test_validate_schema_with_valid_data(): void {
        $this->assertTrue(
            link_block::validate_schema([
                'type' => link_block::get_type(),
                'attrs' => [
                    'url' => 'http://example.com',
                    'title' => 'some title',
                    'description' => null,
                    'image' => null
                ]
            ])
        );

        $this->assertTrue(
            link_block::validate_schema([
                'type' => link_block::get_type(),
                'attrs' => [
                    'url' => 'http://example.com',
                ]
            ])
        );
    }

    /**
     * @return void
     */
    public function test_validate_schema_with_missing_keys(): void {
        $this->assertFalse(
            link_block::validate_schema([
                'type' => link_block::get_type(),
                'attrs' => []
            ])
        );

        $this->assertFalse(
            link_block::validate_schema([
                'type' => link_block::get_type()
            ])
        );
    }

    /**
     * @return void
     */
    public function test_clean_raw_node(): void {
        $data = [
            'type' => link_block::get_type(),
            'attrs' => [
                'url' => 'http://example.com'
            ],
        ];

        $cleaned = link_block::clean_raw_node($data);
        $this->assertSame($cleaned, $data);
    }

    /**
     * @return void
     */
    public function test_clean_raw_node_with_xss(): void {
        $data = [
            'type' => link_block::get_type(),
            'attrs' => [
                'url' => 'http://example.com',
                'title' => "<script type='text/javascript'>alert('hello world');</script>",
                'description' => "<script type='text/javascript'>const x = 15;</script>"
            ]
        ];

        $cleaned = link_block::clean_raw_node($data);
        $this->assertNotSame($data, $cleaned);

        $this->assertArrayHasKey('attrs', $cleaned);
        $this->assertArrayHasKey('url', $cleaned['attrs']);

        $this->assertSame($data['attrs']['url'], $cleaned['attrs']['url']);

        $this->assertArrayHasKey('title', $cleaned['attrs']);
        $this->assertNotSame($data['attrs']['title'], $cleaned['attrs']['title']);
        $this->assertEquals("alert('hello world');", $cleaned['attrs']['title']);

        $this->assertArrayHasKey('description', $cleaned['attrs']);
        $this->assertNotSame($data['attrs']['description'], $cleaned['attrs']['description']);
        $this->assertEquals("const x = 15;", $cleaned['attrs']['description']);
    }
}