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
 * @author Simon Chester <simon.chester@totaralearning.com>
 * @package core
 */

use core\tui\scss\scss;
use core\tui\scss\scss_options;
use core\tui\scss\compiler;

defined('MOODLE_INTERNAL') || die();

class core_tui_scss_testcase extends basic_testcase {
    /**
     * Create an instance of tui_scss for testing
     *
     * @param array $opts Options for mocking
     * @param array $tui_scss_opts Extra options to pass to new tui_scss()
     * @return \core\tui\scss\scss
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
        $options->set_get_component_directory(function ($comp) {
            return "/component/{$comp}";
        });
        $options->set_choose_build_file(function ($file) {
            return $file;
        });
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
