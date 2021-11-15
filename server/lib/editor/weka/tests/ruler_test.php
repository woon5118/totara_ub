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
 * @author Qingyang Liu <qingyang.liu@totaralearning.com>
 * @package editor_weka
 */
defined('MOODLE_INTERNAL') || die();

use core\json_editor\json_editor;

class editor_weka_ruler_testcase extends advanced_testcase {
    /**
     * @return void
     */
    public function test_ruler_tohtml(): void {
        $json = json_encode(
            [
                'type' => 'doc',
                'content' => [
                    [
                        'type' => 'ruler',
                    ]
                ]
            ]
        );

        $editor = json_editor::default();
        $html = $editor->to_html($json);
        $this->assertStringContainsString("<hr />", $html);

        $text = $editor->to_text($json);
        $this->assertStringContainsString('---', $text);
    }
}