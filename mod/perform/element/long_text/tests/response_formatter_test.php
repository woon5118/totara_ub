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
 * @coversDefaultClass response_formatter.
 *
 * @group perform
 */
class performelement_long_text_response_formatter_testcase extends advanced_testcase {
    /**
     * @covers ::format
     */
    public function test_format() {
        $plugin = element_plugin::load_by_plugin('long_text');
        $formatter_class = element_response_formatter::for_plugin($plugin);
        $this->assertEquals(response_formatter::class, $formatter_class);

        $context = context_system::instance();
        $answer = '<h1>This is a <strong>test</strong> answer</h1>';
        $incoming = json_encode(['answer_text' => $answer]);

        $formatter = new $formatter_class(format::FORMAT_RAW, $context);
        $expected = json_encode(['answer_text' => $answer]);
        $this->assertEquals($expected, $formatter->format($incoming), 'wrong formatting');

        $formatter = new $formatter_class(format::FORMAT_PLAIN, $context);
        $expected = json_encode(['answer_text' => $answer]);
        $this->assertEquals($expected, $formatter->format($incoming), 'wrong formatting');

        // TODO this needs to change when the element is updated to use an editor.
        $formatter = new $formatter_class(null, $context);
        $expected = json_encode(['answer_text' => '&lt;h1&gt;This is a &lt;strong&gt;test&lt;/strong&gt; answer&lt;/h1&gt;']);
        $this->assertEquals($expected, $formatter->format($incoming), 'wrong formatting');
    }

    /**
     * @covers ::format
     */
    public function test_non_json_value() {
        $answer = '<h1>This is a <strong>test</strong> answer</h1>';

        $formatter = new response_formatter(format::FORMAT_PLAIN, context_system::instance());
        $this->assertEquals($answer, $formatter->format($answer), 'wrong formatting');
    }
}
