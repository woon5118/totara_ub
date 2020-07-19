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

namespace totara_tui\local\scss;

use core\cssvars;
use totara_tui\local\scss\compiler;
use totara_tui\output\framework;

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
     * @var cssvars CSS vars polyfill
     */
    private $cssvars;

    public function __construct() {
        $this->compiler = new compiler();
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
}
