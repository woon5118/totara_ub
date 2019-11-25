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

use core\json_editor\node\text;

class core_json_editor_text_testcase extends advanced_testcase {
    /**
     * @return void
     */
    public function test_validate_schema_with_valid_data(): void {
        $this->assertTrue(
            text::validate_schema([
                'type' => text::get_type(),
                'text' => 'something else'
            ])
        );

        $this->assertTrue(
            text::validate_schema([
                'type' => text::get_type(),
                'text' => 'something else',
                'marks' => [
                    [
                        'type' => 'strong'
                    ]
                ]
            ])
        );
    }

    /**
     * @return void
     */
    public function test_validate_schema_with_invalid_marks(): void {
        $this->assertFalse(
            text::validate_schema([
                'type' => text::get_type(),
                'text' => 'woho',
                'marks' => 'ddews',
            ])
        );

        $this->assertFalse(
            text::validate_schema([
                'type' => text::get_type(),
                'text' => 'ddd',
                'marks' => [1, 2, 3 , 4]
            ])
        );
    }

    /**
     * @return void
     */
    public function test_validate_schema_with_extra_keys(): void {
        $this->assertFalse(
            text::validate_schema([
                'type' => text::get_type(),
                'text' => 'whooh',
                'ddeiijiokwo' => 'ddqw',
            ])
        );

        $this->assertFalse(
            text::validate_schema([
                'type' => text::get_type(),
                'text' => 'dddeww',
                'marks' => [
                    [
                        'type' => 'strong',
                        'dame' => ''
                    ]
                ]
            ])
        );

        $this->assertDebuggingCalledCount(2);
    }

    /**
     * @return void
     */
    public function test_clean_raw_node(): void {
        $data = [
            'type' => text::get_type(),
            'text' => 'Something special',
            'marks' => [
                ['type' => 'strong']
            ]
        ];

        $cleaned = text::clean_raw_node($data);
        $this->assertSame($data, $cleaned);
    }

    /**
     * @return void
     */
    public function test_clean_raw_node_with_invalid_marks_structure(): void {
        $data = [
            'type' => text::get_type(),
            'text' => 'DUDUDU',
            'marks' => [
                'type' => 'strong'
            ]
        ];

        $cleaned = text::clean_raw_node($data);
        $this->assertNotSame($data, $cleaned);

        $this->assertArrayHasKey('marks', $cleaned);
        $this->assertEmpty($cleaned['marks']);

        $this->assertDebuggingCalled();
    }

    /**
     * @return void
     */
    public function test_clean_xss_node(): void {
        $data = [
            'type' => text::get_type(),
            'text' => '<script>alert(\'hello world\');</script>',
        ];

        $cleaned = text::clean_raw_node($data);
        $this->assertArrayHasKey('type', $cleaned);
        $this->assertArrayHasKey('text', $cleaned);

        $this->assertSame('alert(\'hello world\');', $cleaned['text']);
        $this->assertNotEquals($data, $cleaned);
    }

    /**
     * @return void
     */
    public function test_sanitize_node(): void {
        $result = text::sanitize_raw_node([
            'type' => text::get_type(),
            'text' => '<script>alert("hello world");</script>'
        ]);

        $this->assertArrayHasKey('type', $result);
        $this->assertArrayHasKey('text', $result);

        $this->assertSame(text::get_type(), $result['type']);
        $this->assertEquals(
            s('<script>alert("hello world");</script>'),
            $result['text']
        );
    }
}