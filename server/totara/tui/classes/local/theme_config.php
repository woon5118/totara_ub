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

namespace totara_tui\local;

use core_minify;
use totara_tui\local\scss\scss;
use totara_tui\local\scss\scss_options;

/**
 * Overridden theme config for use when mediating JS and CSS for Tui.
 * This is local, and should only be used by mediation scripts belonging to totara_tui.
 */
final class theme_config extends \theme_config {

    /**
     * Get CSS content for type and subtype. Called by styles.php.
     *
     * @param string $type
     * @return string
     */
    public function get_css_content_by($type) {
        $csscontent = $this->get_tui_css_content($type);
        $csscontent = $this->post_process($csscontent);
        $csscontent = core_minify::css($csscontent);
        return $csscontent;
    }

    /**
     * Get the compiled TUI CSS content for the provided Totara component
     *
     * @param string $component
     * @return string Compiled CSS
     */
    private function get_tui_css_content(string $component): string {
        $scss_options = new scss_options();
        $scss_options->set_themes($this->get_tui_theme_chain());
        $scss_options->set_legacy($this->legacybrowser);
        $scss_options->set_sourcemap_enabled(false);

        $tui_scss = new scss($scss_options);
        return $tui_scss->get_compiled_css($component);
    }

    /**
     * Get theme chain (e.g. ['base', 'roots', 'basis']) for TUI CSS.
     *
     * Themes are only included if they have `$THEME->tui = true` in config.php.
     *
     * @return string[]
     */
    private function get_tui_theme_chain(): array {
        $themes = [];

        // Find out wanted parent sheets.
        $excludes = $this->resolve_excludes('parents_exclude_sheets');
        if ($excludes !== true) {
            // Base first, the immediate parent last.
            foreach (array_reverse($this->parent_configs) as $parent_config) {
                $parent = $parent_config->name;
                if (!empty($excludes[$parent]) and $excludes[$parent] === true) {
                    continue;
                }
                $themes[] = $parent;
            }
        }

        $themes[] = $this->name;

        return $themes;
    }
}