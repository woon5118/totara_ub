<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Unit tests for format_text defined in weblib.php.
 *
 * @package   core
 * @category  test
 * @copyright 2015 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

defined('MOODLE_INTERNAL') || die();


/**
 * Unit tests for format_text defined in weblib.php.
 *
 * @copyright 2015 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU Public License
 */
class core_weblib_format_text_testcase extends advanced_testcase {

    public function test_format_text_format_html() {
        $this->resetAfterTest();
        filter_set_global_state('emoticon', TEXTFILTER_ON);
        $this->assertRegExp('~^<p><img class="icon emoticon" alt="smile" ([^>]+)></p>$~',
                format_text('<p>:-)</p>', FORMAT_HTML));
    }

    public function test_format_text_format_html_no_filters() {
        $this->resetAfterTest();
        filter_set_global_state('emoticon', TEXTFILTER_ON);
        $this->assertEquals('<p>:-)</p>',
                format_text('<p>:-)</p>', FORMAT_HTML, array('filter' => false)));
    }

    public function test_format_text_format_plain() {
        // Note FORMAT_PLAIN does not filter ever, no matter we ask for filtering.
        $this->resetAfterTest();
        filter_set_global_state('emoticon', TEXTFILTER_ON);
        $this->assertEquals(':-)',
                format_text(':-)', FORMAT_PLAIN));
    }

    public function test_format_text_format_plain_no_filters() {
        $this->resetAfterTest();
        filter_set_global_state('emoticon', TEXTFILTER_ON);
        $this->assertEquals(':-)',
                format_text(':-)', FORMAT_PLAIN, array('filter' => false)));
    }

    public function test_format_text_format_markdown() {
        $this->resetAfterTest();
        filter_set_global_state('emoticon', TEXTFILTER_ON);
        $this->assertRegExp('~^<p><em><img class="icon emoticon" alt="smile" ([^>]+)></em></p>\n$~',
                format_text('*:-)*', FORMAT_MARKDOWN));
    }

    public function test_format_text_format_markdown_nofilter() {
        $this->resetAfterTest();
        filter_set_global_state('emoticon', TEXTFILTER_ON);
        $this->assertEquals("<p><em>:-)</em></p>\n",
                format_text('*:-)*', FORMAT_MARKDOWN, array('filter' => false)));
    }

    public function test_format_text_format_moodle() {
        $this->resetAfterTest();
        filter_set_global_state('emoticon', TEXTFILTER_ON);
        $this->assertRegExp('~^<div class="text_to_html"><p><img class="icon emoticon" alt="smile" ([^>]+)></p></div>$~',
                format_text('<p>:-)</p>', FORMAT_MOODLE));
    }

    public function test_format_text_format_moodle_no_filters() {
        $this->resetAfterTest();
        filter_set_global_state('emoticon', TEXTFILTER_ON);
        $this->assertEquals('<div class="text_to_html"><p>:-)</p></div>',
                format_text('<p>:-)</p>', FORMAT_MOODLE, array('filter' => false)));
    }

    public function test_format_text_overflowdiv() {
        $this->assertEquals('<div class="no-overflow"><p>:-)</p></div>',
                format_text('<p>:-)</p>', FORMAT_HTML, array('overflowdiv' => true)));
    }

    /**
     * Test adding blank target attribute to links
     *
     * @dataProvider format_text_blanktarget_testcases
     * @param string $link The link to add target="_blank" to
     * @param string $expected The expected filter value
     */
    public function test_format_text_blanktarget($link, $expected) {
        $actual = format_text($link, FORMAT_HTML, array('blanktarget' => true, 'filter' => false, 'allowxss' => true));
        $this->assertEquals($expected, $actual);
    }

    /**
     * Data provider for the test_format_text_blanktarget testcase
     *
     * @return array of testcases
     */
    public function format_text_blanktarget_testcases() {
        return [
            'Simple link' => [
                '<a href="https://www.youtube.com/watch?v=JeimE8Wz6e4">Hey, that\'s pretty good!</a>',
                '<a href="https://www.youtube.com/watch?v=JeimE8Wz6e4" target="_blank" rel="noreferrer">Hey, that\'s pretty good!</a>'
            ],
            'Link with rel' => [
                '<a href="https://www.youtube.com/watch?v=JeimE8Wz6e4" rel="nofollow">Hey, that\'s pretty good!</a>',
                '<a href="https://www.youtube.com/watch?v=JeimE8Wz6e4" rel="nofollow noreferrer" target="_blank">Hey, that\'s pretty good!</a>'
            ],
            'Link with rel noreferrer' => [
                '<a href="https://www.youtube.com/watch?v=JeimE8Wz6e4" rel="noreferrer">Hey, that\'s pretty good!</a>',
                '<a href="https://www.youtube.com/watch?v=JeimE8Wz6e4" rel="noreferrer" target="_blank">Hey, that\'s pretty good!</a>'
            ],
            'Link with target' => [
                '<a href="https://www.youtube.com/watch?v=JeimE8Wz6e4" target="_self">Hey, that\'s pretty good!</a>',
                '<a href="https://www.youtube.com/watch?v=JeimE8Wz6e4" target="_self">Hey, that\'s pretty good!</a>'
            ],
            'Link with target blank' => [
                '<a href="https://www.youtube.com/watch?v=JeimE8Wz6e4" target="_blank">Hey, that\'s pretty good!</a>',
                '<a href="https://www.youtube.com/watch?v=JeimE8Wz6e4" target="_blank" rel="noreferrer">Hey, that\'s pretty good!</a>'
            ],
            'Link with Frank\'s casket inscription' => [
                '<a href="https://en.wikipedia.org/wiki/Franks_Casket">ᚠᛁᛋᚳ᛫ᚠᛚᚩᛞᚢ᛫ᚪᚻᚩᚠᚩᚾᚠᛖᚱᚷ ᛖᚾᛒᛖᚱᛁᚷ ᚹᚪᚱᚦᚷᚪ᛬ᛋᚱᛁᚳᚷᚱᚩᚱᚾᚦᚫᚱᚻᛖᚩᚾᚷᚱᛖᚢᛏᚷᛁᛋᚹᚩᛗ ᚻ' .
                    'ᚱᚩᚾᚫᛋᛒᚪᚾ ᛗᚫᚷᛁᚠᛁᛋᚳ᛫ᚠᛚᚩᛞᚢ᛫ᚪᚻᚩᚠᚩᚾᚠᛖᚱᚷ ᛖᚾᛒᛖᚱᛁᚷ ᚹᚪᚱᚦᚷᚪ᛬ᛋᚱᛁᚳᚷᚱᚩᚱᚾᚦᚫᚱᚻᛖᚩᚾᚷᚱᛖᚢᛏᚷᛁᛋᚹᚩᛗ ᚻᚱᚩᚾᚫᛋᛒᚪᚾ ᛗᚫᚷᛁ</a>',
                '<a href="https://en.wikipedia.org/wiki/Franks_Casket" target="_blank" rel="noreferrer">' .
                    'ᚠᛁᛋᚳ᛫ᚠᛚᚩᛞᚢ᛫ᚪᚻᚩᚠᚩᚾᚠᛖᚱᚷ ᛖᚾᛒᛖᚱᛁᚷ ᚹᚪᚱᚦᚷᚪ᛬ᛋᚱᛁᚳᚷᚱᚩᚱᚾᚦᚫᚱᚻᛖᚩᚾᚷᚱᛖᚢᛏᚷᛁᛋᚹᚩᛗ ᚻᚱᚩᚾᚫᛋᛒᚪᚾ ᛗᚫᚷᛁᚠᛁᛋᚳ᛫ᚠᛚᚩᛞᚢ᛫ᚪᚻᚩᚠᚩᚾᚠᛖᚱᚷ ᛖᚾ' .
                    'ᛒᛖᚱᛁᚷ ᚹᚪᚱᚦᚷᚪ᛬ᛋᚱᛁᚳᚷᚱᚩᚱᚾᚦᚫᚱᚻᛖᚩᚾᚷᚱᛖᚢᛏᚷᛁᛋᚹᚩᛗ ᚻᚱᚩᚾᚫᛋᛒᚪᚾ ᛗᚫᚷᛁ</a>'
             ],
            'No link' => [
                'Some very boring text written with the Latin script',
                '<p>Some very boring text written with the Latin script</p>'
            ],
            'No link with Thror\'s map runes' => [
                'ᛋᛏᚫᚾᛞ ᛒᚣ ᚦᛖ ᚷᚱᛖᚣ ᛋᛏᚩᚾᛖ ᚻᚹᛁᛚᛖ ᚦᛖ ᚦᚱᚢᛋᚻ ᚾᚩᚳᛋ ᚫᚾᛞ ᚦᛖ ᛋᛖᛏᛏᛁᚾᚷ ᛋᚢᚾ ᚹᛁᚦ ᚦᛖ ᛚᚫᛋᛏ ᛚᛁᚷᚻᛏ ᚩᚠ ᛞᚢᚱᛁᚾᛋ ᛞᚫᚣ ᚹᛁᛚᛚ ᛋᚻᛁᚾᛖ ᚢᛈᚩᚾ ᚦᛖ ᚳᛖᚣᚻᚩᛚᛖ',
                '<p>ᛋᛏᚫᚾᛞ ᛒᚣ ᚦᛖ ᚷᚱᛖᚣ ᛋᛏᚩᚾᛖ ᚻᚹᛁᛚᛖ ᚦᛖ ᚦᚱᚢᛋᚻ ᚾᚩᚳᛋ ᚫᚾᛞ ᚦᛖ ᛋᛖᛏᛏᛁᚾᚷ ᛋᚢᚾ ᚹᛁᚦ ᚦᛖ ᛚᚫᛋᛏ ᛚᛁᚷᚻᛏ ᚩᚠ ᛞᚢᚱᛁᚾᛋ ᛞᚫᚣ ᚹᛁᛚᛚ ᛋᚻᛁᚾᛖ ᚢᛈᚩᚾ ᚦᛖ ᚳᛖᚣᚻᚩᛚᛖ</p>'
            ]
        ];
    }

    public function test_format_text_empty_values() {
        self::assertSame('', format_text(''));
        self::assertSame('', format_text('', FORMAT_HTML));
        self::assertSame('', format_text(null));
        self::assertSame('', format_text(null, FORMAT_HTML));
        self::assertSame('<div class="text_to_html">0</div>', format_text(0));
        self::assertSame('0', format_text(0, FORMAT_HTML));
        self::assertSame('<div class="text_to_html">0</div>', format_text('0'));
        self::assertSame('0', format_text('0', FORMAT_HTML));
    }

    public function test_format_text_wiki() {
        $text = 'I\'m the needle<a onclick="alert(1)">Hack</a>';
        $filtered = format_text($text, FORMAT_WIKI);
        self::assertContains(s($text), $filtered);
        self::assertNotContains($text, $filtered);
        self::assertNotContains('<a', $filtered);
        self::assertContains('NOTICE: Wiki-like formatting has been removed from Moodle', $filtered);
    }

    public function test_format_text_markdown() {
        $text = 'I\'m the needle<a onclick="alert(1)">Hack</a>';
        $filtered = format_text($text, FORMAT_MARKDOWN);
        $expected = '<p>I\'m the needle<a>Hack</a></p>';
        self::assertSame($expected, trim($filtered));
        self::assertNotContains($text, $filtered);
    }

    public function test_format_text_fake_format() {
        $text = 'I\'m the needle<a onclick="alert(1)">Hack</a>';
        $filtered = format_text($text, 'sam');
        $expected = '<div class="text_to_html">I\'m the needle<a>Hack</a></div>';
        self::assertSame($expected, trim($filtered));
        self::assertNotContains($text, $filtered);
    }

    public function test_format_text_option_none() {
        $text = 'I\'m the needle<a onclick="alert(1)">Hack</a>';
        $filtered = format_text($text, FORMAT_HTML, []);
        $expected = 'I\'m the needle<a>Hack</a>';
        self::assertSame($expected, trim($filtered));
        self::assertNotContains($text, $filtered);
    }

    public function test_format_text_option_overflowdiv() {
        $text = 'I\'m the needle<a onclick="alert(1)">Hack</a>';
        $filtered = format_text($text, FORMAT_HTML, ['overflowdiv' => true]);
        $expected = '<div class="no-overflow">I\'m the needle<a>Hack</a></div>';
        self::assertSame($expected, trim($filtered));
        self::assertNotContains($text, $filtered);
    }

    public function test_format_text_option_blanktarget() {
        $text = 'I\'m the needle<a onclick="alert(1)">Hack</a>';
        $filtered = format_text($text, FORMAT_HTML, ['blanktarget' => true]);
        $expected = '<p>I\'m the needle<a target="_blank" rel="noreferrer">Hack</a></p>';
        self::assertSame($expected, trim($filtered));
        self::assertNotContains($text, $filtered);
    }

    public function test_format_text_option_allowxss() {
        $text = 'I\'m the needle<a onclick="alert(1)">Hack</a>';
        $filtered = format_text($text, FORMAT_HTML, ['allowxss' => true]);
        $expected = $text;
        self::assertSame($expected, $filtered);
    }

    public function test_format_text_with_cleantext_compatible_filters() {
        global $CFG, $DB, $PAGE, $FILTERLIB_PRIVATE;

        $file =  $CFG->dirroot . '/filter/multilang/filter.php';
        if (!file_exists($file)) {
            $this->markTestSkipped('The multilang filter is not installed.');
            return;
        }
        require_once($file);

        $this->resetAfterTest();
        $context = \context_system::instance();
        $DB->execute('DELETE FROM {filter_active}');
        $DB->insert_record('filter_active', [
            'filter' => 'multilang',
            'contextid' => $context->id,
            'active' => '1',
            'softorder' => '1',
        ]);

        $FILTERLIB_PRIVATE = null;

        $filtermanager = filter_manager::instance();
        $filtermanager->setup_page_for_filters($PAGE, $context);
        self::assertTrue($filtermanager->result_is_compatible_with_text_cleaning($context));

        // Prep some data.
        $text = '<span lang="en" class="multilang">English</span><span lang="xx" class="multilang">Klingon</span>';
        $filtered = 'English';

        self::assertSame($filtered, format_text($text, FORMAT_HTML, ['context' => $context]));

        $FILTERLIB_PRIVATE = null;
    }
}
