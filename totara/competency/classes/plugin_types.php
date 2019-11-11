<?php
/*
 * This file is part of Totara Learn
 *
 * Copyright (C) 2018 onwards Totara Learning Solutions LTD
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
 * @author Riana Rossouw <riana.rossouw@totaralearning.com>
 * @package totara_competency
 */

namespace totara_competency;

/**
 * Class for competency plugin management
 */
class plugin_types {

    private const ENABLE_CONFIG_POSTFIX = '_types_enabled';

    /**
     * Return information on all installed plugins of this type
     * The plugin names are used as type key
     *
     * @param  string $plugin_type Plugin type (e.g. pathway)
     * @param  string $config_scope Config scope (e.g. totara_competency)
     * @return array containing objects with type, display name, version and enabled
     *         attributes for all installed plugins of this type
     */
    public static function get_installed_plugins(string $plugin_type, string $config_scope): array {
        $enable_setting = $plugin_type . self::ENABLE_CONFIG_POSTFIX;

        $plugin_infos = \core_plugin_manager::instance()->get_plugins_of_type($plugin_type);

        $enabled_plugins = get_config($config_scope, $enable_setting);

        // Never set before - mark all as enabled by default
        if (empty($enabled_plugins)) {
            $enabled_plugins = array_keys($plugin_infos);

            set_config($enable_setting, implode(',', $enabled_plugins), $config_scope);
            \core_plugin_manager::reset_caches();
        } else {
            $enabled_plugins = explode(',', $enabled_plugins);
        }

        $plugins = [];

        foreach ($plugin_infos as $plugin => $info) {
            $plugins[$plugin] = (object)[
                'type' => $plugin,
                'title' => $info->displayname,
                'version' => $info->versiondb,
                'enabled' => in_array($plugin, $enabled_plugins),
            ];
        }

        return $plugins;
    }

    /**
     * Get list of enabled plugins of this type
     *
     * @param  string $plugin_type Plugin type (e.g. pathway)
     * @param  string $config_scope Config scope (e.g. totara_competency)
     * @return string[] List of plugin names that are currently enabled
     */
    public static function get_enabled_plugins(string $plugin_type, string $config_scope): array {
        $enable_setting = $plugin_type . self::ENABLE_CONFIG_POSTFIX;

        $enabled_plugins = get_config($config_scope, $enable_setting);

        // Todo: I propose we ultimately do this on upgrade. And then anything not enabled is not enabled.
        //       But as it is now might be useful while in dev.
        // Never set before - mark all as enabled by default
        if ($enabled_plugins === false) {
            $all_types = self::get_installed_plugins($plugin_type, $config_scope);
            $enabled_plugins = array_keys($all_types);
        } else {
            if (empty($enabled_plugins)) {
                // An empty string would represent all plugins having been disabled.
                $enabled_plugins = [];
            } else {
                $enabled_plugins = explode(',', $enabled_plugins);
            }
        }

        // TODO: Rather return the same structure as installed_plugins
        return $enabled_plugins;
    }

    /**
     * Enable a specific plugin
     *
     * @param  string $plugin Plugin to enable
     * @param  string $plugin_type Plugin type (e.g. pathway)
     * @param  string $config_scope Config scope (e.g. totara_competency)
     * @return array Resulting array of enabled plugins
     */
    public static function enable_plugin(string $plugin, string $plugin_type, string $config_scope): array {
        $enable_setting = $plugin_type . self::ENABLE_CONFIG_POSTFIX;

        $enabled_plugins = self::get_enabled_plugins($plugin_type, $config_scope);

        if (!in_array($plugin, $enabled_plugins)) {
            $enabled_plugins[] = $plugin;
            set_config($enable_setting, implode(',', $enabled_plugins), $config_scope);
            \core_plugin_manager::reset_caches();
        }

        return $enabled_plugins;
    }

    /**
     * Disable a specific plugin
     *
     * @param string $plugin Type to disable
     * @param  string $plugin_type Plugin type (e.g. pathway)
     * @param  string $config_scope Config scope (e.g. totara_competency)
     * @return array Resulting array of enabled plugins
     */
    public static function disable_plugin(string $plugin, string $plugin_type, string $config_scope): array {
        $enable_setting = $plugin_type . self::ENABLE_CONFIG_POSTFIX;

        $enabled_plugins = self::get_enabled_plugins($plugin_type, $config_scope);

        $idx = array_search($plugin, $enabled_plugins);
        if ($idx !== false) {
            array_splice($enabled_plugins, $idx, 1);
            set_config($enable_setting, implode(',', $enabled_plugins), $config_scope);
            \core_plugin_manager::reset_caches();
        }

        return $enabled_plugins;
    }

}