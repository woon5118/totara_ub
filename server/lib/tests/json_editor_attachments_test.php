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

use core\json_editor\node\attachments;
use core\json_editor\node\attachment;

class core_json_editor_attachments_testcase extends advanced_testcase {
    /**
     * @return void
     */
    public function test_validate_schema_with_valid_data(): void {
        $result = attachments::validate_schema([
            'type' => attachments::get_type(),
            'content' => [
                [
                    'type' => attachment::get_type(),
                    'attrs' => [
                        'filename' => 'file_name.png',
                        'url' => 'http://example.com',
                        'size' => 1920
                    ]
                ]
            ],
        ]);

        $this->assertTrue($result);
    }

    /**
     * @return void
     */
    public function test_validate_schema_with_missing_key(): void {
        $result = attachments::validate_schema([
            'type' => attachments::get_type(),
            'missing_key' => 'missing_key'
        ]);

        $this->assertFalse($result);
    }

    /**
     * @return void
     */
    public function test_validate_schema_with_extra_keys(): void {
        $result = attachments::validate_schema([
            'type' => attachments::get_type(),
            'content' => [],
            'extra_key' => ['extra_keys']
        ]);

        $this->assertDebuggingCalled();
        $this->assertFalse($result);
    }
}
