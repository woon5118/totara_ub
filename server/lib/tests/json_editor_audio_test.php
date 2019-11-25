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

use core\json_editor\node\audio;

class core_json_editor_audio_testcase extends advanced_testcase {
    /**
     * @return void
     */
    public function test_validate_schema_with_valid_data(): void {
        $result = audio::validate_schema([
            'type' => audio::get_type(),
            'attrs' => [
                'filename' => 'some_file.png',
                'url' => 'http://example.com',
                'mime_type' => 'audio/mp3'
            ]
        ]);

        $this->assertTrue($result);
    }

    /**
     * @return void
     */
    public function test_validate_schema_with_missing_keys(): void {
        $this->assertFalse(
            audio::validate_schema([
                'type' => audio::get_type(),
                'attrs' => [
                    'filename' => 'woops.png'
                ]
            ])
        );

        $this->assertFalse(
            audio::validate_schema([
                'type' => audio::get_type(),
                'woops' => []
            ])
        );

        $this->assertFalse(
            audio::validate_schema([
                'type' => audio::get_type(),
                'attrs' => [
                    'filename' => 'woops.png',
                    'url' => 'http://example.com',
                ]
            ])
        );

        $this->assertFalse(
            audio::validate_schema([
                'type' => audio::get_type(),
                'attrs' => [
                    'url' => 'http://example.com',
                ]
            ])
        );

        $this->assertFalse(
            audio::validate_schema([
                'type' => audio::get_type(),
                'attrs' => [
                    'filename' => 'woops.png',
                    'mime_type' => 'audio/mp3',
                ]
            ])
        );

        $this->assertFalse(
            audio::validate_schema([
                'type' => audio::get_type(),
                'attrs' => [
                    'mime_type' => 'audio/mp3',
                ]
            ])
        );

        $this->assertFalse(
            audio::validate_schema([
                'type' => audio::get_type(),
                'attrs' => [
                    'url' => 'http://example.com',
                    'mime_type' => 'audio/mp3',
                ]
            ])
        );
    }

    /**
     * @return void
     */
    public function test_validate_schema_with_extra_keys(): void {
        $this->assertFalse(
            audio::validate_schema([
                'type' => audio::get_type(),
                'attrs' => [
                    'filename' => 'Dx.png',
                    'url' => 'http://example.com',
                    'mime_type' => 'audio/mp3'
                ],
                'woops' => 'x'
            ])
        );

        $this->assertFalse(
            audio::validate_schema([
                'type' => audio::get_type(),
                'attrs' => [
                    'filename' => 'dxx.png',
                    'url' => 'http://example.com',
                    'mime_type' => 'audio/mp3',
                    'xx_dd' => 'dd__xx'
                ]
            ])
        );

        $this->assertDebuggingCalledCount(2);
    }
}