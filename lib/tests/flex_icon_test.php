<?php
/*
 * This file is part of Totara LMS
 *
 * Copyright (C) 2015 onwards Totara Learning Solutions LTD
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
 * @copyright 2015 onwards Totara Learning Solutions LTD
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Joby Harding <joby.harding@totaralms.com>>
 * @package   core_output
 */

use core\output\flex_icon;

defined('MOODLE_INTERNAL') || die();

/**
 * Class flex_icon_testcase
 *
 * PHPUnit unit tests for \core\output\flex_icon class.
 */
class flex_icon_testcase extends advanced_testcase {

    /**
     * @var string
     */
    protected static $tmpdir;

    /**
     * Return an accessible version of a given [protected|private] method.
     *
     * @param string $methodname
     * @return ReflectionMethod
     */
    protected function get_inaccessible_method($methodname, $classname = '') {

        $classname = empty($classname) ? 'core\output\flex_icon' : $classname;
        $reflectionclass = new \ReflectionClass($classname);
        $method = $reflectionclass->getMethod($methodname);
        $method->setAccessible(true);

        return $method;

    }

    /**
     * It should return the template name for a given core identifier when there are no overrides.
     */
    public function test_get_template_output_core() {

        $expected = 'core/flex_icon';
        $actual = (new flex_icon('moodle-i/import'))->get_template();

        $this->assertEquals($expected, $actual);

    }

    /**
     * It should return template context object containing data to render a given icon.
     */
    public function test_export_for_template_output() {

        global $PAGE;

        $identifier = 'no-key';
        $icon = new flex_icon($identifier);

        $coreiconsfilepath = \core\flex_icon_helper::get_core_map_path();
        $corejson = json_decode(file_get_contents($coreiconsfilepath), true);

        $expected = array_merge(
            $corejson['map'][$identifier]['data'],
            array('iconidentifier' => $identifier, 'customdata' => array(),
        ));
        $actual = $icon->export_for_template($PAGE->get_renderer('core'));

        $this->assertEquals($expected, $actual);

    }

    /**
     * It should transform a pix path plus component into a legacy identifier.
     */
    public function test_legacy_identifier_from_pix_data_output() {

        $componentname = 'totara_core';
        $pixpath = 'path/to/icon';

        $expected = "{$componentname}-{$pixpath}";
        $actual = flex_icon::legacy_identifier_from_pix_data($pixpath, $componentname);

        $this->assertEquals($expected, $actual);

    }

    /**
     * It should transform a pix path into a moodle core legacy identifier when no component is provided.
     */
    public function test_legacy_identifier_from_pix_data_output_no_component() {

        $pixpath = 'path/to/icon';

        $expected = "core-{$pixpath}";
        $actual = flex_icon::legacy_identifier_from_pix_data($pixpath);

        $this->assertEquals($expected, $actual);

    }

    /**
     * It should remove size suffixes from core file icon paths.
     */
    public function test_legacy_identifier_from_pix_data_normalizes_file_icon_identifiers() {

        $pixpaths = array(
            'f/pdf' => 'f/pdf',
            'f/png-24' => 'f/png',
            'f/parent-32' => 'f/parent',
            'f/pdf-48' => 'f/pdf',
            'f/moodle-64' => 'f/moodle',
            'f/video-72' => 'f/video',
            'f/wav-80' => 'f/wav',
            'f/powerpoint-96' => 'f/powerpoint',
            'f/folder-open-128' => 'f/folder-open',
            'f/unknown-256' => 'f/unknown',
        );

        foreach ($pixpaths as $pixpath => $expected) {
            $actual = flex_icon::normalize_pixpath($pixpath);
            $message = "Expected '{$pixpath}' to equal '{$expected}' not {$actual}";

            $this->assertEquals($expected, $actual, $message);
        }

    }

    /**
     * It should return boolean whether a given string is a legacy identifier or not.
     */
    public function test_is_legacyidentifier_output() {

        $expectedoutcomes = array(
            'moodle-t/upload' => true,
            'block_foo-icon' => true,
            'block_foo_bar-path/to/pix' => true,
            'notlegacy' => false,
            'block_foo_bar-/path/to/pix' => false,
            'moodle-i/completion-manual-enabled' => true,
        );

        foreach ($expectedoutcomes as $identifier => $expected) {
            $actual = flex_icon::is_legacy_identifier($identifier);
            $this->assertEquals($expected, $actual);
        }

    }

    /**
     * It should return additional customdata for certain legacy identifiers.
     */
    public function test_get_customdata_by_legacyidentifier_output() {

        $expectedoutcomes = array(
            'core-i/upload' => array('classes' => 'ft-size-200'),
            'core-t/upload' => array(),
            'core-docs' => array(),
            'core-f/gif-24' => array('classes' => 'ft-size-400'),
            'core-f/impress-32' => array('classes' => 'ft-size-600'),
            'core-f/html-48' => array('classes' => 'ft-size-700'),
            'core-f/jpeg-64' => array('classes' => 'ft-size-700'),
            'core-f/moodle-72' => array('classes' => 'ft-size-700'),
            'core-f/markup-80' => array('classes' => 'ft-size-700'),
            'core-f/flash-96' => array('classes' => 'ft-size-700'),
            'core-f/oth-128' => array('classes' => 'ft-size-700'),
            'core-f/mpeg-256' => array('classes' => 'ft-size-700'),
            'core-f/audio' => array(),
        );

        foreach ($expectedoutcomes as $identifier => $expected) {
            $actual = flex_icon::get_customdata_by_legacy_identifier($identifier);
            $message  = "Expected ";
            $message .= json_encode($expected) . ' ';
            $message .= "data for '{$identifier}'' ";
            $message .= "but got " . json_encode($actual);

            $this->assertEquals($expected, $actual, $message);
        }

   }

    /**
     * It should throw an exception if identifier is not a legacy identifier.
     */
    public function test_get_customdata_by_legacyidentifier_throws() {

        $this->setExpectedException('\coding_exception');

        flex_icon::get_customdata_by_legacy_identifier('notlegacyidentifier');

    }

    /**
     * Test the convenience method outputs a pix icon string.
     *
     * This test should not strictly be in this class however as there
     * is not currently a test file for outputrenderers.php and the
     * functionality is related to flex_icons we include it.
     */
    public function test_flex_icon_convenience_method() {

        global $OUTPUT;

        $customdata = array('legacy' => array('alt' => 'Settings'));

        $expected = $OUTPUT->render(new flex_icon('cog'));
        $actual = $OUTPUT->flex_icon('cog', $customdata);

        $this->assertEquals($expected, $actual);

    }

    /**
     *
     */
    public function test_existing_icon_uses() {
        global $CFG;

        $data = \core\flex_icon_helper::build_cache_file_data(\theme_config::DEFAULT_THEME);
        $translations = $data['map'];

        file_put_contents('/tmp/data', var_export($data, true));

        $directory = new RecursiveDirectoryIterator($CFG->dirroot);
        $iterator = new RecursiveIteratorIterator($directory);
        $diresc = preg_quote($CFG->dirroot, '#');
        $regexiterator = new RegexIterator($iterator, '#^'.$diresc.'/(.+\.php)$#i', RecursiveRegexIterator::GET_MATCH);

        $flexiconregex = '#(new\s+\\\\core\\\\output\\\\flex_icon|>flex_icon)\((\'|")(.*?)\2\s*[,\)]#';

        // Test the regex to make sure it finds what we want to find.
        $fakecontenttotestregex = '<?php
        $OUTPUT->flex_icon(\'monkey-fish/t\');
        new \core\output\flex_icon(\'fish-treat/t\');';
        $count = preg_match_all($flexiconregex, $fakecontenttotestregex, $matches);
        $this->assertSame(2, $count);
        $this->assertArrayHasKey(3, $matches);
        $this->assertSame('monkey-fish/t', $matches[3][0]);
        $this->assertSame('fish-treat/t', $matches[3][1]);

        $nomaperrors = array();
        $translationmapping = array();
        foreach ($regexiterator as $file) {
            if (strpos($file[1], '/tests/') || strpos($file[1], 'vendor/')) {
                // Skip the vendor directory and the test directory.
                continue;
            }
            $content = file_get_contents($file[0]);
            $result = preg_match_all($flexiconregex, $content, $matches);
            if ($result) {
                foreach ($matches[3] as $identifier) {
                    if (!isset($translations[$identifier]) && strpos($identifier, '.') === false) {
                        $nomaperrors[] = "{$identifier} in {$file[1]}";
                        var_dump("{$identifier} in {$file[1]}");
                    }
                    if (strpos($identifier, '/') !== false && isset($translations[$identifier]['translatesto'])) {
                        $translationmapping[] = "{$identifier} should be converted to {$translations[$identifier]['translatesto']}";
                    }
                }
            }
        }
        $this->assertCount(0, $translationmapping, "The following flex_icons are using a translation and should be converted:\n * ".join("\n * ", $translationmapping)."\n");
        $this->assertCount(0, $nomaperrors, "The following flex_icons are used but not mapped:\n * ".join("\n * ", $nomaperrors)."\n");
    }

    public function test_missing_map_triggers_debug_message() {

        global $CFG, $OUTPUT;

        $this->resetAfterTest();

        $CFG->debugdeveloper = true;

        $OUTPUT->render(new flex_icon('__XXX__not__a_mapped___identifier__XXX__'));

        $this->assertDebuggingCalled();

    }

    public function test_template_helper_output() {

        global $OUTPUT;

        $name = 'cog';

        $expected = $OUTPUT->flex_icon($name);
        $actual = $OUTPUT->render_from_template('core/flex_icon_helper_test', array());

        $this->assertEquals($expected, $actual);

    }

}
