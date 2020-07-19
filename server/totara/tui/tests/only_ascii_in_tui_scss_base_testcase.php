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

abstract class core_only_ascii_in_tui_scss_base_testcase extends basic_testcase {
    /**
     * Check TUI SCSS does not contain any non-ascii characters
     *
     * @param string $component Component name, e.g. theme_roots
     */
    protected function check_tui_scss_for_non_ascii(string $component) {
        $tui_dir = $this->unix_path(core_component::get_component_directory($component)) . '/tui';

        if (!file_exists($tui_dir)) {
            return;
        }

        // ignore certain directories
        $blacklist = [
            $tui_dir . '/build/'
        ];

        // create a filtered iterator for iterating over each matching file
        $directory = new \RecursiveDirectoryIterator($tui_dir);
        $filter = new \RecursiveCallbackFilterIterator($directory, function ($current, $key, $iterator) use ($blacklist) {
            // skip hidden files and directories.
            if ($current->getFilename()[0] === '.') {
                return false;
            }
            if ($current->isDir()) {
                // skip blacklisted directories
                foreach ($blacklist as $entry) {
                    if (strpos($this->unix_path($current->getPathname() . '/'), $entry) === 0) {
                        return false;
                    }
                }
                return true;
            } else {
                return (bool)preg_match('/\.scss$|\.vue$/', $current->getFilename());
            }
        });
        $iterator = new \RecursiveIteratorIterator($filter);

        // check each file
        foreach ($iterator as $file) {
            $content = file_get_contents($file->getPathname());

            $min_offset = 0;
            $max_offset = strlen($content);

            // for vue files, only check the content of the <style> tag
            if (preg_match('/\.vue$/', $file->getFilename())) {
                if (preg_match('/<style[^>]*>/', $content, $matches, PREG_OFFSET_CAPTURE)) {
                    $min_offset = $matches[0][1];
                    if (preg_match('/<\/style[^>]*>/', $content, $matches, PREG_OFFSET_CAPTURE)) {
                        $max_offset = $matches[0][1];
                    }
                }
            }

            // search for non-ascii characters in the file
            if (preg_match('/[^\x00-\x7F]/', $content, $matches, PREG_OFFSET_CAPTURE, $min_offset)) {
                if ($matches[0][1] <= $max_offset) {
                    $position = $this->index_to_position($content, $matches[0][1]);
                    $this->fail(
                        "Non-ascii character in {$file->getPathname()} at line {$position->line}, column {$position->column}"
                    );
                    return;
                }
            }
        }
    }

    /**
     * Update path to use / as the directory separator
     *
     * @param string $path
     * @return string
     */
    private function unix_path(string $path): string {
        return str_replace('\\', '/', $path);
    }

    /**
     * Given text and an index into that text, convert the index to a { line, column } object.
     *
     * @param string $text
     * @param int $index
     *
     * @return object Object with line and column properties, both starting at 1.
     */
    private function index_to_position(string $text, int $index): object {
        $last_newline = 0;
        $next_newline = strpos($text, "\n");
        $newlines = 0;

        // search for the last newline before index
        while ($next_newline < $index) {
            if ($next_newline === false) {
                break;
            }
            $last_newline = $next_newline;
            $newlines++;
            $next_newline = strpos($text, "\n", $last_newline + 1);
        }

        return (object)[
            'line' => $newlines + 1,
            'column' => $index - $last_newline
        ];
    }
}
