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

namespace core\tui\scss;

/**
 * SCSS compiler.
 *
 * Provides a compiler with support for sourcemaps, import resolvers, and
 * variables_only/output_only transforms.
 */
class compiler {
    /**
     * @var scss_compiler_implementation
     */
    private $compiler;

    /**
     * @var bool
     */
    private $sourcemap_enabled = false;

    /**
     * @var array Array of strings and functions to resolve imports
     */
    private $import_paths;

    public function __construct() {
        $this->compiler = new scss_compiler_implementation();

        $this->compiler->register_transform('definitions_only', new \core\tui\scss\transforms\definitions_only());
        $this->compiler->register_transform('output_only', new \core\tui\scss\transforms\output_only());
    }

    /**
     * Get whether to generate a source map.
     *
     * @return bool
     */
    public function get_sourcemap_enabled(): bool {
        return $this->sourcemap_enabled;
    }

    /**
     * Set whether to generate a source map.
     *
     * @param bool $sourcemap_enabled
     */
    public function set_sourcemap_enabled(bool $sourcemap_enabled) {
        $this->sourcemap_enabled = $sourcemap_enabled;
    }

    /**
     * Get array to use to resolve import requests to a file path.
     *
     * @return array Array of strings (path) and functions.
     */
    public function get_import_paths(): array {
        return $this->import_paths;
    }

    /**
     * Set array to use to resolve import requests to a file path.
     *
     * @param array $import_paths Array of strings (path) and functions.
     */
    public function set_import_paths(array $import_paths) {
        $this->import_paths = $import_paths;
    }

    /**
     * Compile the provided SCSS
     *
     * @param string $scss
     * @return string
     */
    public function compile(string $scss): string {
        global $CFG;

        // Raise memory/time limits, this might take a while.
        raise_memory_limit(MEMORY_EXTRA);
        \core_php_time_limit::raise(300);

        $this->compiler->setImportPaths($this->import_paths);

        if ($this->sourcemap_enabled) {
            $this->compiler->setSourceMap(scss_compiler_implementation::SOURCE_MAP_INLINE);
            $this->compiler->setSourceMapOptions([
                'sourceMapBasepath' => $CFG->dirroot,
                'outputSourceFiles' => true,
                'excludeSourceFiles' => ['(stdin)'],
                'sourceMapApplyInline' => true,
            ]);
        } else {
            $this->compiler->setSourceMap(scss_compiler_implementation::SOURCE_MAP_NONE);
        }

        $compiled = $this->compiler->compile($scss);

        return $compiled;
    }
}
