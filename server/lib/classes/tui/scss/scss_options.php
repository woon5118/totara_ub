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

use core\cssvars;
use core\tui\scss\compiler;

/**
 * Options for {@see scss}
 */
class scss_options {
    /**
     * @var string[] Active theme chain
     */
    private $themes = [];

    /**
     * @var bool Legacy browser mode
     */
    private $legacy = false;

    /**
     * @var bool Generate sourcemaps
     */
    private $sourcemap = false;

    /**
     * @var compiler SCSS compiler
     */
    private $compiler;

    /**
     * @var callable core_component::get_component_directory()
     */
    private $get_component_directory;

    /**
     * @var cssvars CSS vars polyfill
     */
    private $cssvars;

    /**
     * @var callable core_output_choose_build_file()
     */
    private $choose_build_file;

    public function __construct() {
        $this->compiler = new compiler();
        $this->get_component_directory = [\core_component::class, 'get_component_directory'];
        $this->choose_build_file = 'core_output_choose_build_file';
        $this->cssvars = new cssvars();
    }

    /**
     * Get the list of themes to use during compilation.
     *
     * @return string[] List of theme names (not including theme_ prefix).
     */
    public function get_themes(): array {
        return $this->themes;
    }

    /**
     * Set the list of themes to use during compilation.
     *
     * @param string[] $themes List of theme names (not including theme_ prefix).
     */
    public function set_themes(array $themes) {
        $this->themes = $themes;
    }

    /**
     * Get whether to compile in legacy mode (IE 11 compatibility).
     *
     * @return bool
     */
    public function get_legacy(): bool {
        return $this->legacy;
    }

    /**
     * Set whether to compile in legacy mode (IE 11 compatibility).
     *
     * @param bool $legacy
     */
    public function set_legacy(bool $legacy) {
        $this->legacy = $legacy;
    }

    /**
     * Set whether to generate a source map.
     *
     * @return bool
     */
    public function get_sourcemap_enabled(): bool {
        return $this->sourcemap;
    }

    /**
     * Set whether to generate a source map.
     *
     * @param bool $sourcemap
     */
    public function set_sourcemap_enabled(bool $sourcemap) {
        $this->sourcemap = $sourcemap;
    }

    /**
     * Get SCSS compiler instance.
     *
     * @return compiler
     */
    public function get_compiler(): compiler {
        return $this->compiler;
    }

    /**
     * Set SCSS compiler instance.
     *
     * @param compiler $compiler
     */
    public function set_compiler(compiler $compiler) {
        $this->compiler = $compiler;
    }

    /**
     * Get core_component::get_component_directory() equivalent.
     *
     * @return callable
     */
    public function get_get_component_directory(): callable {
        return $this->get_component_directory;
    }

    /**
     * Set core_component::get_component_directory() equivalent.
     *
     * @param callable $get_component_directory
     */
    public function set_get_component_directory(callable $get_component_directory) {
        $this->get_component_directory = $get_component_directory;
    }

    /**
     * Get cssvars instance.
     *
     * @return \core\cssvars
     */
    public function get_cssvars(): cssvars {
        return $this->cssvars;
    }

    /**
     * Set cssvars instance.
     *
     * @param \core\cssvars $cssvars
     */
    public function set_cssvars(cssvars $cssvars) {
        $this->cssvars = $cssvars;
    }

    /**
     * Get core_output_choose_build_file equivalent.
     *
     * @return callable
     */
    public function get_choose_build_file(): callable {
        return $this->choose_build_file;
    }

    /**
     * Set core_output_choose_build_file equivalent.
     *
     * @param callable $choose_build_file
     */
    public function set_choose_build_file(callable $choose_build_file) {
        $this->choose_build_file = $choose_build_file;
    }
}
