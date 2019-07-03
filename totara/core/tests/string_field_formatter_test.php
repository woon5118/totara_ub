<?php
/*
 * This file is part of Totara Learn
 *
 * Copyright (C) 2019 onwards Totara Learning Solutions LTD
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
 * @author Fabian Derschatta <fabian.derschatta@totaralearning.com>
 * @package totara_core
 */

use core\format;
use totara_core\formatter\field\string_field_formatter;

defined('MOODLE_INTERNAL') || die();

class totara_core_string_field_formatter_testcase extends basic_testcase {

    public function test_html_format() {
        $context = context_system::instance();
        $formatter = new string_field_formatter(format::FORMAT_HTML, context_system::instance());

        $value = '<span class="myhtml">_)(*&^%$#test</span>';

        $result = $formatter->format($value);

        $value = format_string($value, true, ['context' => $context]);

        // format_string() should have been applied
        $this->assertEquals($result, $value);
        // Tags are stripped
        $this->assertNotRegExp("/span class=/", $result);
    }

    public function test_html_format_without_stripping_tags() {
        $context = context_system::instance();
        $formatter = new string_field_formatter(format::FORMAT_HTML, context_system::instance());
        $formatter->set_strip_tags(false);

        $value = '<span class="myhtml">_)(*&^%$#test</span>';

        $result = $formatter->format($value);

        $value = format_string($value, false, ['context' => $context]);

        // format_string() should have been applied
        $this->assertEquals($result, $value);
        // Tags are still there
        $this->assertRegExp("/span class=/", $result);
    }

    public function test_html_format_with_additional_options() {
        $context = context_system::instance();
        $formatter = new string_field_formatter(format::FORMAT_HTML, context_system::instance());
        $formatter->set_strip_tags(true);
        // Escape option acts as an inverse strip_tags, so escape = true means strip_tags = false
        $formatter->set_additional_options(['escape' => true]);

        $value = '<span class="myhtml">_)(*&^%$#test</span>';

        $result = $formatter->format($value);

        $value = format_string($value, false, ['context' => $context]);

        // format_string() should have been applied
        $this->assertEquals($result, $value);
        // Tags are not stripped
        $this->assertRegExp("/span class=/", $result);
    }

    public function test_plain_format() {
        $context = context_system::instance();
        $formatter = new string_field_formatter(format::FORMAT_PLAIN, $context);

        $value = '<span class="myhtml">_)(*&^%$#test</span>';

        $result = $formatter->format($value);

        $value = format_string($value, true, ['context' => $context]);

        // We should have plain text now
        $this->assertNotEquals($result, $value);
        $this->assertEquals(html_to_text($value), $result);
    }

    public function test_raw_format() {
        $formatter = new string_field_formatter(format::FORMAT_RAW, context_system::instance());

        $value = '<span class="myhtml">test</span>';

        $result = $formatter->format($value);

        // Nothing should have changed
        $this->assertEquals($result, $value);
    }

    public function test_unknown_format() {
        $formatter = new string_field_formatter('foo', context_system::instance());

        $value = '<span class="myhtml">test</span>';

        $this->expectException(coding_exception::class);
        $this->expectExceptionMessageRegExp('/Invalid format given/');

        $formatter->format($value);
    }

    public function test_null_value() {
        $formatter = new string_field_formatter(format::FORMAT_HTML, context_system::instance());
        $value = $formatter->format(null);
        $this->assertNull($value);

        $formatter = new string_field_formatter(format::FORMAT_PLAIN, context_system::instance());
        $value = $formatter->format(null);
        $this->assertNull($value);

        $formatter = new string_field_formatter(format::FORMAT_RAW, context_system::instance());
        $value = $formatter->format(null);
        $this->assertNull($value);
    }

}
