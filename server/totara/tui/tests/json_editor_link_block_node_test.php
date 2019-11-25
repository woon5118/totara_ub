<?php
/**
 * This file is part of Totara Core
 *
 * Copyright (C) 2020 onwards Totara Learning Solutions LTD
 *
 * MIT License
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
 * @author Kian Nguyen <kian.nguyen@totaralearning.com>
 * @package totara_tui
 */
defined('MOODLE_INTERNAL') || die();

use core\json_editor\node\link_block;
use totara_tui\json_editor\output_node\link_block as link_block_output;

class totara_tui_json_editor_link_block_node_testcase extends advanced_testcase {
    /**
     * @return void
     */
    public function test_output_component(): void {
        $raw_node = link_block::create_raw_node('This is title');
        $node = link_block::from_node($raw_node);

        $attributes = htmlspecialchars(
            json_encode([
                'attrs' => [
                    'url' => 'http://example.com',
                    'title' => 'This is title',
                    'image' => '',
                    'description' => null,
                ]
            ])
        );

        $expected = /** @lang text */
            "<div class=\"tui-rendered__block\">" .
            "<span data-tui-component=\"tui/components/json_editor/nodes/LinkBlock\" " .
            "data-tui-props=\"{$attributes}\"></span></div>";

        $output = new link_block_output($node);
        $this->assertSame($expected, $output->render_tui_component_content());
    }
}