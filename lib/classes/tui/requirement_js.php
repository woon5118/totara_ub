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

namespace core\tui;

use moodle_url;

/**
 * Represents a JS bundle
 */
class requirement_js extends requirement {
    /**
     * @var string Path to bundle
     */
    protected $path;

    /**
     * Create a new instance of requirement_js
     *
     * @param string $component Totara component this requirement is part of, e.g. 'mod_example'
     * @param string $name Name of the bundle (e.g. tui_bundle.js)
     * @param string $path Path to bundle
     */
    public function __construct(string $component, string $name, string $path) {
        parent::__construct($component, $name);
        $this->path = $path;
    }

    /**
     * {@inheritdoc}
     */
    public function get_type(): string {
        return self::TYPE_JS;
    }

    /**
     * Get the path to the JS file
     *
     * @return string
     */
    public function get_file_path(): string {
        return $this->path;
    }

    /**
     * {@inheritdoc}
     */
    public function get_url($options = null): moodle_url {
        global $PAGE;
        return $PAGE->requires->get_js_url($this->path);
    }
}
