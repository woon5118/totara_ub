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
 * @author Sam Hemelryk <sam.hemelryk@totaralearning.com>
 * @package totara_tui
 */

use totara_tui\local\scss\scss;
use totara_tui\local\scss\scss_options;
use totara_tui\local\scss\compiler;

defined('MOODLE_INTERNAL') || die();

class totara_tui_local_scss_testcase extends basic_testcase {
    /**
     * Create an instance of tui_scss for testing
     *
     * @param array $opts Options for mocking
     * @param array $tui_scss_opts Extra options to pass to new tui_scss()
     * @return scss
     */
    private function create_tui_scss($opts = []) {
        $compiler = $this->createMock(compiler::class);
        $compiler->method('compile')
            ->will(
                isset($opts['compile'])
                    ? $this->returnCallback($opts['compile'])
                    : $this->returnArgument(0)
            );

        $options = new scss_options();
        $options->set_compiler($compiler);
        $options->set_themes(['base', 'legacy', 'ventura']);

        $tui_scss = new scss($options);

        return $tui_scss;
    }

    /**
     * Test that CSS is compiled in the correct order for Totara components
     */
    public function test_import_resolution_tui() {
        global $CFG;
        $tui_scss = $this->create_tui_scss();
        $method = new ReflectionMethod($tui_scss, 'get_imports');
        $method->setAccessible(true);

        $expected = new \stdClass;
        $expected->imports = [];
        $expected->cssvars_legacy_imports = [];

        if (file_exists($CFG->srcroot . '/client/build/tui')) {
            $expected->imports[] = 'definitions_only!internal_absolute:'.$CFG->srcroot.'/client/build/theme_ventura/styles/_variables.scss';
        }
        $result = $method->invoke($tui_scss, 'tui');
        $this->assertEquals($expected, $result);

        $tui_scss->get_options()->set_legacy(true);

        if (file_exists($CFG->srcroot . '/client/build/tui')) {
            $expected->cssvars_legacy_imports = [
                'definitions_only!internal_absolute:'.$CFG->srcroot.'/client/build/theme_ventura/styles/_variables.scss',
                'output_only!internal_absolute:'.$CFG->srcroot.'/client/build/theme_ventura/styles/_variables.scss',
            ];
        }

        $result = $method->invoke($tui_scss, 'tui');
        $this->assertEquals($expected, $result);
    }

    /**
     * Test that CSS is compiled in the correct order for Totara components
     */
    public function test_import_resolution_theme_ventura() {
        global $CFG;
        $tui_scss = $this->create_tui_scss();
        $method = new ReflectionMethod($tui_scss, 'get_imports');
        $method->setAccessible(true);

        $expected = new \stdClass;
        $expected->imports = [];
        $expected->cssvars_legacy_imports = [];

        if (file_exists($CFG->srcroot . '/client/build/tui')) {
            $expected->imports[] = 'definitions_only!internal_absolute:'.$CFG->srcroot.'/client/build/theme_ventura/styles/_variables.scss';
            $expected->imports[] = 'definitions_only!internal_absolute:'.$CFG->srcroot.'/client/build/theme_ventura/styles/_variables.scss';
            $expected->imports[] = 'output_only!internal_absolute:'.$CFG->srcroot.'/client/build/theme_ventura/styles/_variables.scss';
            $expected->imports[] = 'internal_absolute:'.$CFG->srcroot.'/client/build/theme_ventura/tui_bundle.scss';
        }
        $result = $method->invoke($tui_scss, 'theme_ventura');
        $this->assertEquals($expected, $result);

        $tui_scss->get_options()->set_legacy(true);

        if (file_exists($CFG->srcroot . '/client/build/tui')) {
            $expected->cssvars_legacy_imports = [
                'definitions_only!internal_absolute:'.$CFG->srcroot.'/client/build/theme_ventura/styles/_variables.scss',
                'definitions_only!internal_absolute:'.$CFG->srcroot.'/client/build/theme_ventura/styles/_variables.scss',
                'output_only!internal_absolute:'.$CFG->srcroot.'/client/build/theme_ventura/styles/_variables.scss',
            ];
        }

        $result = $method->invoke($tui_scss, 'theme_ventura');
        $this->assertEquals($expected, $result);
    }

    /**
     * Test that CSS var substitution is performed correctly for legacy browsers in themes
     */
    public function test_theme_css_var_substitution() {
        $tui_scss = $this->create_tui_scss([
            'compile' => function ($scss) {
                return ":root { --var: 'theme'; } a::before { content: var(--var); }";
            }
        ]);

        $this->assertEquals(
            ":root { --var: 'theme'; } a::before { content: var(--var); }",
            $tui_scss->get_compiled_css('theme')
        );

        $tui_scss->get_options()->set_legacy(true);

        $this->assertEquals(
            ":root{--var: 'theme';-var--var: 'theme';} a::before { content: 'theme'; }",
            $tui_scss->get_compiled_css('theme')
        );
    }
}
