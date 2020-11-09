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
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Murali Nair <murali.nair@totaralearning.com>
 * @package performelement_long_text
 * @category test
 */

use core\format;
use mod_perform\formatter\response\element_response_formatter;
use mod_perform\models\activity\element_plugin;
use performelement_long_text\formatter\response_formatter;

/**
 * @coversDefaultClass \performelement_long_text\formatter\response_formatter
 *
 * @group perform
 */
class performelement_long_text_response_formatter_testcase extends advanced_testcase {
    /**
     * @covers ::format
     */
    public function test_format(): void {
        $plugin = element_plugin::load_by_plugin('long_text');
        $formatter_class = element_response_formatter::for_plugin($plugin);
        $this->assertEquals(response_formatter::class, $formatter_class);

        $context = context_system::instance();
        $answer = '<h1>This is a test heading</h1>With some <strong>capital</strong> text<script>alert(1);</script><&\'';
        $incoming = json_encode($answer);

        // When converted to RAW, we need to get back exactly what we put in.
        $formatter = new $formatter_class(format::FORMAT_RAW, $context);
        $expected = json_encode($answer);
        $this->assertEquals($expected, $formatter->format($incoming), 'wrong formatting');

        // When converted to PLAIN, the h1 and string cause capitalisation, and the h1 causes some line returns after it.
        // The script and script content is removed. The symbols come out unencoded.
        $formatter = new $formatter_class(format::FORMAT_PLAIN, $context);
        $expected = json_encode("THIS IS A TEST HEADING\n\nWith some CAPITAL text<&'");
        $this->assertEquals($expected, $formatter->format($incoming), 'wrong formatting');

        // To convert from MOODLE to HTML, the "text_to_html" div is added around the text.
        // The script and script content is removed. The symbols come out html-encoded.
        $formatter = new $formatter_class(null, $context);
        $expected = json_encode("<div class=\"text_to_html\"><h1>This is a test heading</h1>With some <strong>capital</strong> text&lt;&amp;'</div>");
        $this->assertEquals($expected, $formatter->format($incoming), 'wrong formatting');
    }

    /**
     * @covers ::format
     * @dataProvider non_json_value_provider
     * @param string|null|bool $value
     */
    public function test_non_json_value($value): void {
        $formatter = new response_formatter(format::FORMAT_PLAIN, context_system::instance());

        $this->assertEquals($value, $formatter->format($value), 'wrong formatting');
    }

    public function non_json_value_provider(): array {
        return [
            'non json encoded string' => ['<h1>This is a <strong>test</strong> answer</h1>'],
            'null' => [null],
            'false' => [false],
        ];
    }
}
