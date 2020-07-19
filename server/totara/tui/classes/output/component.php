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
 * @package totara_core
 */

namespace totara_tui\output;

/**
 * Renderable TUI component
 */
final class component implements \renderable {
    /**
     * @var string Component name
     */
    private $name;

    /**
     * @var array|null Component props
     */
    private $props = [];

    /**
     * @var bool
     */
    private static $registered = [];

    /**
     * Create a new instance of component
     *
     * @param string $name Module ID of the component to render.
     *     e.g. 'totara_core/components/Example`.
     * @param array|null $props Props to pass to the component.
     *     Props are not processed or escaped in any way, they are passed as-is
     *     to the component. Must be encodable as JSON.
     */
    public function __construct(string $name, array $props = null) {
        $this->name = $name;
        $this->props = $props;
    }

    /**
     * Get component name
     *
     * @return string
     */
    public function get_name(): string {
        return $this->name;
    }

    /**
     * Get component props
     *
     * @return array Mapping of prop keys to values
     */
    public function get_props(): ?array {
        return $this->props;
    }

    /**
     * @return bool
     */
    public function has_props(): bool {
        return ($this->props !== null);
    }

    /**
     * @throws \coding_exception if there are problems json encoding data for the component.
     * @return string
     */
    public function get_props_encoded(): string {
        if (!$this->has_props()) {
            // If you get here and want to remove this then you must work out and confirm what empty props look like for Vue.
            // It wasn't required during development and it's a bad idea to support something you don't need!
            throw new \coding_exception('Encoded props requested, but there are no props.');
        }
        $data = json_encode($this->props);
        if ($data === false && json_last_error() !== JSON_ERROR_NONE) {
            throw new \coding_exception('Invalid component data encountered while attempting to encode a component of type ' . static::class, json_last_error_msg());
        }
        return $data;
    }

    public function register(\moodle_page $page): \moodle_page {
        return self::register_component($this->get_name(), $page);
    }

    public static function register_component(string $name, \moodle_page $page): \moodle_page {
        $pos = strpos($name, '/');
        if ($pos !== false) {
            $name = substr($name, 0, $pos);
        }
        if (!isset(self::$registered[$name])) {
            if ($page->state >= \moodle_page::STATE_IN_BODY) {
                throw new \coding_exception('Unable to register component as the header has already been printed.');
            }
            self::get_framework($page)->require_vue($name);
            self::$registered[$name] = true;
        }
        return $page;
    }

    /**
     * @param \moodle_page $page
     * @throws \coding_exception
     * @return framework
     */
    private static function get_framework(\moodle_page $page): framework {
        /** @var framework $framework */
        $framework = $page->requires->framework(framework::class);
        return $framework;
    }
}
