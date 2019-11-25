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

use core\json_editor\node\bullet_list;
use core\json_editor\node\list_item;
use core\json_editor\node\paragraph;
use core\json_editor\node\text;

class core_json_editor_bullet_list_testcase extends advanced_testcase {
    /**
     * @return void
     */
    public function test_validate_schema_with_valid_data(): void {
        $this->assertTrue(
            bullet_list::validate_schema([
                'type' => bullet_list::get_type(),
                'content' => [
                    [
                        'type' => list_item::get_type(),
                        'content' => [
                            [
                                'type' => paragraph::get_type(),
                                'content' => [
                                    [
                                        'type' => text::get_type(),
                                        'text' => 'Hello world'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ])
        );
    }

    /**
     * @return void
     */
    public function test_validate_schema_without_key(): void {
        $this->assertFalse(
            bullet_list::validate_schema([
                'type' => bullet_list::get_type(),
                'missing_content' => []
            ])
        );
    }

    /**
     * @return void
     */
    public function test_validate_schema_with_invalid_content(): void {
        $this->assertFalse(
            bullet_list::validate_schema([
                'type' => bullet_list::get_type(),
                'content' => [
                    [
                        'type' => paragraph::get_type(),
                        'content' => []
                    ]
                ]
            ])
        );
    }

    /**
     * @return void
     */
    public function test_validate_schema_with_extra_keys(): void {
        $this->assertFalse(
            bullet_list::validate_schema([
                'type' => bullet_list::get_type(),
                'content' => [],
                'fine' => 'wops'
            ])
        );

        $this->assertDebuggingCalled();
    }

    /**
     * @return void
     */
    public function test_sanitize_raw_node(): void {
        $texts = [];
        for ($i = 0; $i < 5; $i++) {
            $texts[] = "<script>alert('Hello there {$i}');</script>";
        }

        $result = bullet_list::sanitize_raw_node(
            bullet_list::create_raw_node_from_texts($texts)
        );

        $this->assertArrayHasKey('type', $result);
        $this->assertArrayHasKey('content', $result);

        $this->assertEquals(bullet_list::get_type(), $result['type']);
        $this->assertIsArray($result['content']);
        $this->assertCount(5, $result['content']);

        $list_item_nodes = $result['content'];
        // Grep all the text fields.
        $out_texts = [];

        foreach ($list_item_nodes as $list_item_node) {
            $this->assertArrayHasKey('type', $list_item_node);
            $this->assertArrayHasKey('content', $list_item_node);

            $this->assertEquals(list_item::get_type(), $list_item_node['type']);
            $this->assertIsArray($list_item_node['content']);
            $this->assertCount(1, $list_item_node['content']);

            $paragraph = reset($list_item_node['content']);
            $this->assertArrayHasKey('type', $paragraph);
            $this->assertArrayHasKey('content', $paragraph);
            $this->assertCount(1, $paragraph['content']);

            $text = reset($paragraph['content']);
            $this->assertArrayHasKey('type', $text);
            $this->assertArrayHasKey('text', $text);

            $this->assertEquals(text::get_type(), $text['type']);
            $out_texts[] = $text['text'];
        }

        foreach ($texts as $text) {
            $this->assertTrue(in_array(s($text), $out_texts));
        }
    }
}