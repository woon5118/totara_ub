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
 * @author Simon Chester <simon.chester@totaralearning.com>
 * @author Sam Hemelryk <sam.hemelryk@totaralearning.com>
 * @package totara_tui
 */

use totara_tui\local\scss\scss;
use totara_tui\local\scss\scss_options;
use totara_tui\local\scss\compiler;

defined('MOODLE_INTERNAL') || die();

class core_tui_scss_testcase extends basic_testcase {
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
        $options->set_themes(['main', 'custom']);

        $tui_scss = new scss($options);

        return $tui_scss;
    }

    /**
     * Extract import values from CSS code
     *
     * @param string $str
     * @return string
     */
    private function get_imports($str) {
        preg_match_all("/@import\\s+['\"]([^'\"]*)['\"];/", $str, $matches);
        return $matches[1];
    }

    /**
     * Get path to SCSS file inside component
     *
     * @param string $comp
     * @param string $file
     * @return string
     */
    private function component_scss($comp, $file) {
        return "internal_absolute:/component/$comp/tui/build/styles/$file.scss";
    }

    /**
     * Get path to SCSS bundle file inside component
     *
     * @param string $comp
     * @return string
     */
    private function component_bundle($comp) {
        return "internal_absolute:/component/$comp/tui/build/tui_bundle.scss";
    }

    /**
     * Test that CSS is compiled in the correct order for Totara components
     */
    public function test_component_order() {
        $this->markTestSkipped('No longer possible ');
        $tui_scss = $this->create_tui_scss();

        $expected = [
            // SCSS vars first
            'definitions_only!' . $this->component_scss('totara_test', '_variables'), // component first so it can be overridden
            'definitions_only!' . $this->component_scss('theme_main', '_variables'),
            'definitions_only!' . $this->component_scss('theme_custom', '_variables'),
            // then component vars and CSS
            'output_only!'. $this->component_scss('totara_test', '_variables'),
            $this->component_bundle('totara_test'),
        ];

        $result = $tui_scss->get_compiled_css('totara_test');
        $this->assertEquals($expected, $this->get_imports($result));

        $tui_scss->get_options()->set_legacy(true);

        $result = $tui_scss->get_compiled_css('totara_test');
        $this->assertEquals($expected, $this->get_imports($result));
    }

    /**
     * Test that CSS is compiled in the correct order for themes
     */
    public function test_theme_order() {
        $this->markTestSkipped('No longer possible');
        $tui_scss = $this->create_tui_scss();

        $expected = [
            // SCSS vars first
            'definitions_only!' . $this->component_scss('theme_main', '_variables'),
            'definitions_only!' . $this->component_scss('theme_custom', '_variables'),
            // then interleaved vars and CSS
            'output_only!' . $this->component_scss('theme_main', '_variables'),
            $this->component_bundle('theme_main'),
            'output_only!' . $this->component_scss('theme_custom', '_variables'),
            $this->component_bundle('theme_custom'),
        ];

        $result = $tui_scss->get_compiled_css('theme');
        $this->assertEquals($expected, $this->get_imports($result));

        $tui_scss->get_options()->set_legacy(true);

        $result = $tui_scss->get_compiled_css('theme');
        $this->assertEquals($expected, $this->get_imports($result));
    }

    /**
     * Test that CSS var substitution is performed correctly for legacy browsers in components
     */
    public function test_component_css_var_substitution() {
        $this->markTestSkipped('No longer possible ');
        $tui_scss = $this->create_tui_scss([
            'compile' => function ($scss) {
                if (preg_match('/output_only.*theme/', $scss)) {
                    // compiling css vars for theme
                    return ":root { --var: 'theme'; }";
                }
                return ":root { --var: 'totara_test'; } a::before { content: var(--var); }";
            }
        ]);

        $this->assertEquals(
            ":root { --var: 'totara_test'; } a::before { content: var(--var); }",
            $tui_scss->get_compiled_css('totara_test')
        );

        $tui_scss->get_options()->set_legacy(true);

        $this->assertEquals(
            ":root{--var: 'totara_test';-var--var: 'totara_test';} a::before { content: 'theme'; }",
            $tui_scss->get_compiled_css('totara_test')
        );
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
