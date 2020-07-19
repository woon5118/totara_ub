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
 * Represents a TUI bundle file for a Totara component
 */
abstract class requirement {
    const TYPE_JS = 'js';
    const TYPE_CSS = 'css';

    /**
     * @var string Totara component this requirement is part of, e.g. 'mod_example'
     */
    protected $component;

    /**
     * @var string Name of the bundle (e.g. tui_bundle.js)
     */
    protected $name;

    /**
     * Create a new instance of requirement
     *
     * @param string $component Totara component this requirement is part of, e.g. 'mod_example'
     * @param string $name Name of the bundle (e.g. tui_bundle.js)
     */
    public function __construct(string $component, string $name) {
        $this->component = $component;
        $this->name = $name;
    }

    /**
     * Get the type of the bundle, e.g. 'js' or 'css'
     *
     * @return string
     */
    abstract public function get_type(): string;

    /**
     * Get the URL the requirement is served from
     *
     * @param array $options Extra context that may be needed to generate the URL
     *   - theme: Name of current theme
     * @return moodle_url
     */
    abstract public function get_url(array $options = null): moodle_url;

    /**
     * Get the Totara component this requirement is part of, e.g. 'mod_example'
     *
     * @return string
     */
    public function get_component(): string {
        return $this->component;
    }

    /**
     * Get the name of the bundle, e.g. 'tui_bundle.js'
     *
     * @return string
     */
    public function get_name(): string {
        return $this->name;
    }

    /**
     * Get data to serve through the API
     *
     * @param array $options Options to pass to {@see requirement::get_url()}
     * @return object
     */
    public function get_api_data(array $options = null): object {
        return (object)[
            'id' => $this->get_component() . ':' . $this->get_name(),
            'type' => $this->get_type(),
            'component' => $this->get_component(),
            'name' => $this->get_name(),
            'url' => $this->get_url($options)->out(false),
        ];
    }
}
