<?php
/*
 * This file is part of Totara Learn
 *
 * Copyright (C) 2020 onwards Totara Learning Solutions LTD
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
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Nathan Lewis <nathan.lewis@totaralearning.com>
 * @package mod_perform
 */

namespace mod_perform\models\activity;

use core\collection;
use mod_perform\models\response\element_validation_error;

/**
 * Class element_plugin
 *
 * Base class for defining a type of element, including its specific behaviour.
 *
 * @package mod_perform\models\activity
 */
abstract class element_plugin {

    /**
     * Element plugin constructor
     */
    private function __construct() {
    }

    /**
     * Load by plugin name
     *
     * @param string $plugin_name
     *
     * @return static
     */
    final public static function load_by_plugin(string $plugin_name) {
        $plugin_class = "performelement_{$plugin_name}\\{$plugin_name}";
        if (!is_subclass_of($plugin_class, self::class)) {
            throw new \coding_exception('Tried to load an unknown element plugin');
        }
        return new $plugin_class();
    }

    /**
     * Get plugin name, used as a key
     *
     * @return string
     */
    final public function get_plugin_name(): string {
        return explode('\\', static::class)[1];
    }

    /**
     * Get name
     *
     * @return string
     */
    final public function get_name(): string {
        return get_string('name', 'performelement_' . $this->get_plugin_name());
    }

    /**
     * This method return element's admin form vue component name
     *
     * @return string
     */
    public function get_admin_form_component(): string {
        return $this->get_component_path('AdminForm');
    }

    /**
     * This method return element's admin display vue component name
     *
     * @return string
     */
    public function get_admin_display_component(): string {
        return $this->get_component_path('AdminDisplay');
    }

    /**
     * This method return element's user form vue component name
     * @return string
     */
    public function get_participant_form_component(): string {
        return $this->get_component_path('ParticipantForm');
    }

    /**
     * This method return element's user form vue component name
     * @return string
     */
    public function get_participant_response_component(): string {
        return $this->get_component_path('ParticipantResponse');
    }

    /**
     * Calculate the full path to a tui component related to this element plugin.
     *
     * @param string $suffix
     * @return string
     */
    protected function get_component_path(string $suffix): string {
        return 'performelement_' .
            $this->get_plugin_name() .
            '/components/' .
            $this->get_component_name_prefix() .
            'Element' .
            $suffix;
    }

    /**
     * This method return element's default component name prefix
     *
     * @return string
     */
    protected function get_component_name_prefix(): string {
        $prefix = '';
        foreach (explode('_', self::get_plugin_name()) as $name) {
            $prefix .= ucfirst($name);
        }

        return $prefix;
    }

}
