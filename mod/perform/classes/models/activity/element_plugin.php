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

/**
 * Class element_plugin
 *
 * This class contains the methods related to performance activity element plugin
 * All the activity element plugin entity properties accessible via this class
 *
 * @package mod_perform\models\activity
 */
abstract class element_plugin {

    /**
     * element plugin constructor
     */
    private function __construct() {
    }

    /**
     * load by plugin name
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
     * get plugin name
     *
     * @return string
     */
    final public static function get_plugin_name(): string {
        return explode('\\', static::class)[1];
    }

    /**
     * get name
     *
     * @return string
     */
    final public static function get_name(): string {
        return get_string('name', 'performelement_' . static::get_plugin_name());
    }

    /**
     * This method return element's admin form vue component name
     * @return string
     */
    public static function get_admin_form_component(): string {
        return self::get_component_name_prefix() . 'ElementAdminForm';
    }

    /**
     * This method return element's admin display vue component name
     * @return string
     */
    public static function get_admin_display_component(): string {
        return self::get_component_name_prefix().'ElementAdminDisplay';
    }

    /**
     * This method return element's default component name prefix
     * @return string
     */
    protected static function get_component_name_prefix(): string {
        $prefix = '';
        foreach (explode('_', self::get_plugin_name()) as $name) {
            $prefix .= ucfirst($name);
        }

        return $prefix;
    }
}
