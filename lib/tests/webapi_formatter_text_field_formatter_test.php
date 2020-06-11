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
 * @package core
 */

use core\format;
use core\webapi\formatter\field\text_field_formatter;

defined('MOODLE_INTERNAL') || die();

class core_webapi_formatter_text_field_formatter_testcase extends advanced_testcase {

    public function test_html_format() {
        $context = context_system::instance();
        $formatter = new text_field_formatter(format::FORMAT_HTML, $context);
        $formatter->set_pluginfile_url_options($context, 'component', 'filearea', 1, 'file.php');

        $value = '<span class="myhtml">_)(*&^%$#test</span>';

        $result = $formatter->format($value);

        $value = format_text($value, FORMAT_HTML, ['context' => $context]);

        // format_text() should have been applied
        $this->assertEquals($result, $value);
    }

    public function test_html_format_replace_urls() {
        global $CFG;

        $context = context_system::instance();
        $formatter = new text_field_formatter(format::FORMAT_HTML, $context);
        $formatter->set_pluginfile_url_options($context, 'component', 'filearea', 1, 'file.php');

        $value = '<span class="myhtml">@@PLUGINFILE@@/</span>';

        $result = $formatter->format($value);

        $url = "{$CFG->wwwroot}/file.php/{$context->id}/component/filearea/1/";

        // url should have been replaced
        // Tags should be there
        $this->assertRegExp("/<span class/", $result);
        $this->assertRegExp('/'.preg_quote($url, '/').'/', $result);
        $this->assertNotRegExp("/@@PLUGINFILE@@\\//", $result);

        // set additional pluginfile url rewrite options
        $formatter->set_pluginfile_url_options($context, 'component', 'filearea', 1, 'file.php', ['reverse' => true]);
        $result = $formatter->format($result);
        $this->assertDebuggingCalled('Before calling format_text(), the content must be processed with file_rewrite_pluginfile_urls()');
        $this->assertRegExp("/@@PLUGINFILE@@\\//", $result);
    }

    public function test_html_format_replace_urls_without_item_and_pluginfile() {
        global $CFG;

        $context = context_system::instance();
        $formatter = new text_field_formatter(format::FORMAT_HTML, $context);
        $formatter->set_pluginfile_url_options($context, 'component', 'filearea');

        $value = '<span class="myhtml">@@PLUGINFILE@@/</span>';

        $result = $formatter->format($value);

        $url = "{$CFG->wwwroot}/pluginfile.php/{$context->id}/component/filearea/";

        // url should have been replaced
        // Tags should be there
        $this->assertRegExp("/<span class/", $result);
        $this->assertRegExp('/'.preg_quote($url, '/').'/', $result);
        $this->assertNotRegExp("/@@PLUGINFILE@@\\//", $result);
    }

    public function test_html_format_different_text_format() {
        $context = context_system::instance();
        $formatter = new text_field_formatter(format::FORMAT_HTML, $context);
        $formatter->set_pluginfile_url_options($context, 'component', 'filearea', 1, 'file.php');
        $formatter->set_text_format(FORMAT_PLAIN);

        $value = '<span class="myhtml">@@PLUGINFILE@@/</span>';

        $result = $formatter->format($value);

        // Should be plain now, special characters are encoded
        $this->assertRegExp("/&lt;span class=&quot;/", $result);
    }

    public function test_html_format_with_additional_options() {
        $context = context_system::instance();
        $formatter = new text_field_formatter(format::FORMAT_HTML, $context);
        $formatter->set_pluginfile_url_options($context, 'component', 'filearea', 1, 'file.php');
        $formatter->set_additional_options(['overflowdiv' => true]);

        $value = '<span class="myhtml">@@PLUGINFILE@@/</span>';

        $result = $formatter->format($value);

        // We expect the string wrapped in a div as we've passed the option above
        $this->assertRegExp("/<div class=\"no\-overflow\"><span class=\"/", $result);
    }

    public function test_plain_format() {
        $context = context_system::instance();
        $formatter = new text_field_formatter(format::FORMAT_PLAIN, $context);
        $formatter->set_pluginfile_url_options($context, 'component', 'filearea', 1, 'file.php');

        $value = '<span class="myhtml">_)(*&^%$#test</span>';

        $result = $formatter->format($value);

        $value = format_text($value, FORMAT_HTML, ['context' => $context]);

        // We should have plain text now
        $this->assertNotEquals($result, $value);
        $this->assertEquals('_)(*&^%$#test', $result);
    }

    public function test_plain_format_with_long_lines() {
        $context = context_system::instance();
        $formatter = new text_field_formatter(format::FORMAT_PLAIN, $context);
        $formatter->set_pluginfile_url_options($context, 'component', 'filearea', 1, 'file.php');

        $value = '<span class="myhtml">KO WIKITORIA te Kuini o Ingarani i tana mahara atawai ki nga Rangatira me nga Hapu o Nu Tirani</span>';
        $result = $formatter->format($value);

        $value = format_text($value, FORMAT_HTML, ['context' => $context]);
        $expected = 'KO WIKITORIA te Kuini o Ingarani i tana mahara atawai ki nga Rangatira me nga Hapu o Nu Tirani';

        // We should have plain text now.
        $this->assertNotEquals($result, $value);
        // html_to_text() will have inserted linebreaks.
        $this->assertNotEquals(html_to_text($value), $result);
        $this->assertEquals($expected, $result);
    }

    public function test_plain_format_with_br() {
        $context = context_system::instance();
        $formatter = new text_field_formatter(format::FORMAT_PLAIN, $context);
        $formatter->set_pluginfile_url_options($context, 'component', 'filearea', 1, 'file.php');

        $value = '<span class="myhtml">KO WIKITORIA te Kuini o Ingarani i tana<br>mahara atawai ki nga Rangatira me nga Hapu o Nu Tirani</span>';
        $result = $formatter->format($value);

        $expected = "KO WIKITORIA te Kuini o Ingarani i tana\nmahara atawai ki nga Rangatira me nga Hapu o Nu Tirani";
        $this->assertNotEquals($result, $value);
        $this->assertEquals($expected, $result);
    }

    public function test_plain_format_with_links() {
        $context = context_system::instance();
        $formatter = new text_field_formatter(format::FORMAT_PLAIN, $context);
        $formatter->set_pluginfile_url_options($context, 'component', 'filearea', 1, 'file.php');

        $value = '<span class="myhtml">KO <a href="https://en.wikipedia.org/wiki/Queen_Victoria">WIKITORIA</a> te Kuini o Ingarani</span>';
        $result = $formatter->format($value);

        $value = format_text($value, FORMAT_HTML, ['context' => $context]);
        $expected = 'KO WIKITORIA [https://en.wikipedia.org/wiki/Queen_Victoria] te Kuini o Ingarani';

        // We should have plain text now.
        $this->assertNotEquals($result, $value);
        // html_to_text() will have made the URL a footnote.
        $this->assertNotEquals(html_to_text($value), $result);
        $this->assertEquals($expected, $result);
    }

    public function test_raw_format() {
        $context = context_system::instance();
        $formatter = new text_field_formatter(format::FORMAT_RAW, $context);

        $value = '<span class="myhtml">@@PLUGINFILE@@/</span>';

        $result = $formatter->format($value);

        // Nothing should have changed
        $this->assertEquals($result, $value);
    }

    public function test_missing_pluginfile_options() {
        $context = context_system::instance();
        $formatter = new text_field_formatter(format::FORMAT_HTML, $context);

        $value = '<span class="myhtml">test</span>';

        $this->expectException(coding_exception::class);
        $this->expectExceptionMessage('You must provide the pluginfile url options via set_pluginfile_url_options()');

        $formatter->format($value);
    }

    public function test_toggle_pluginfile_url_rewrite() {
        $context = context_system::instance();
        $formatter = new text_field_formatter(format::FORMAT_HTML, $context);
        $formatter->disabled_pluginfile_url_rewrite();

        $value = '<span class="myhtml">test</span>';

        $result = $formatter->format($value);
        $this->assertRegExp("/<span class/", $result);

        $formatter->enable_pluginfile_url_rewrite();

        $this->expectException(coding_exception::class);
        $this->expectExceptionMessage('You must provide the pluginfile url options via set_pluginfile_url_options()');

        $formatter->format($value);
    }

    public function test_unknown_format() {
        $context = context_system::instance();
        $formatter = new text_field_formatter('foo', $context);
        $formatter->set_pluginfile_url_options($context, 'component', 'filearea', 1, 'file.php');

        $value = '<span class="myhtml">test</span>';

        $this->expectException(coding_exception::class);
        $this->expectExceptionMessage('Invalid format given');

        $formatter->format($value);
    }

    public function test_null_value() {
        $formatter = new text_field_formatter(format::FORMAT_HTML, context_system::instance());
        $value = $formatter->format(null);
        $this->assertNull($value);

        $formatter = new text_field_formatter(format::FORMAT_PLAIN, context_system::instance());
        $value = $formatter->format(null);
        $this->assertNull($value);

        $formatter = new text_field_formatter(format::FORMAT_RAW, context_system::instance());
        $value = $formatter->format(null);
        $this->assertNull($value);
    }

}
