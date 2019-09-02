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
class plugintypes {

    private const ENABLE_CONFIG_POSTFIX = '_types_enabled';

    /**
     * Return information on all installed plugins of this type
     * The plugin names are used as type key
     *
     * @param  string $plugintype Plugin type (e.g. pathway)
     * @param  string $configscope Config scope (e.g. totara_competency)
     * @return array containing objects with type, display name, version and enabled
     *         attributes for all installed plugins of this type
     */
    public static function get_installed_plugins(string $plugintype, string $configscope): array {
        $enable_setting = $plugintype . self::ENABLE_CONFIG_POSTFIX;

        $plugininfos = \core_plugin_manager::instance()->get_plugins_of_type($plugintype);

        $enabledplugins = get_config($configscope, $enable_setting);

        // Never set before - mark all as enabled by default
        if (empty($enabledplugins)) {
            $enabledplugins = array_keys($plugininfos);

            set_config($enable_setting, implode(',', $enabledplugins), $configscope);
            \core_plugin_manager::reset_caches();
        } else {
            $enabledplugins = explode(',', $enabledplugins);
        }

        $plugins = [];

        foreach ($plugininfos as $plugin => $info) {
            $plugins[$plugin] = (object)[
                'type' => $plugin,
                'title' => $info->displayname,
                'version' => $info->versiondb,
                'enabled' => in_array($plugin, $enabledplugins),
            ];
        }

        return $plugins;
    }

    /**
     * Get list of enabled plugins of this type
     *
     * @param  string $plugintype Plugin type (e.g. pathway)
     * @param  string $configscope Config scope (e.g. totara_competency)
     * @return [string] List of plugin names that are currently enabled
     */
    public static function get_enabled_plugins(string $plugintype, string $configscope): array {
        $enable_setting = $plugintype . self::ENABLE_CONFIG_POSTFIX;

        $enabledplugins = get_config($configscope, $enable_setting);

        // Todo: I propose we ultimately do this on upgrade. And then anything not enabled is not enabled. But as it is now might be useful while in dev.
        // Never set before - mark all as enabled by default
        if ($enabledplugins === false) {
            $alltypes = self::get_installed_plugins($plugintype, $configscope);
            $enabledplugins = array_keys($alltypes);
        } else {
            if (empty($enabledplugins)) {
                // An empty string would represent all plugins having been disabled.
                $enabledplugins = [];
            } else {
                $enabledplugins = explode(',', $enabledplugins);
            }
        }

// TODO: Rather return the same structure as installed_plugins
        return $enabledplugins;
    }

    /**
     * Enable a specific plugin
     *
     * @param  string $plugin Plugin to enable
     * @param  string $plugintype Plugin type (e.g. pathway)
     * @param  string $configscope Config scope (e.g. totara_competency)
     * @return array Resulting array of enabled plugins
     */
    public static function enable_plugin(string $plugin, string $plugintype, string $configscope): array {
        $enable_setting = $plugintype . self::ENABLE_CONFIG_POSTFIX;

        $enabledplugins = self::get_enabled_plugins($plugintype, $configscope);

        if (!in_array($plugin, $enabledplugins)) {
            $enabledplugins[] = $plugin;
            set_config($enable_setting, implode(',', $enabledplugins), $configscope);
            \core_plugin_manager::reset_caches();
        }

        return $enabledplugins;
    }

    /**
     * Disable a specific plugin
     *
     * @param string $plugin Type to disable
     * @param  string $plugintype Plugin type (e.g. pathway)
     * @param  string $configscope Config scope (e.g. totara_competency)
     * @return array Resulting array of enabled plugins
     */
    public static function disable_plugin(string $plugin, string $plugintype, string $configscope): array {
        $enable_setting = $plugintype . self::ENABLE_CONFIG_POSTFIX;

        $enabledplugins = self::get_enabled_plugins($plugintype, $configscope);

        $idx = array_search($plugin, $enabledplugins);
        if ($idx !== false) {
            array_splice($enabledplugins, $idx, 1);
            set_config($enable_setting, implode(',', $enabledplugins), $configscope);
            \core_plugin_manager::reset_caches();
        }

        return $enabledplugins;
    }

    /**
     * Export enabled plugins' types and desplaynames for use in select elements in templates
     *
     * @param  string $plugintype Plugin type (e.g. pathway)
     * @param  string $configscope Config scope (e.g. totara_competency)
     * @return array of plugindata. For each plugin, plugindata contains 'value' and 'text'
     */
    public static function d_export_enabled_for_select_template(string $plugintype, string $configscope): array {
        $enabled_plugins = array_filter(
            self::get_installed_plugins($plugintype, $configscope),
            function ($plugin) {
                return $plugin->enabled;
            }
        );

        $results = [];
        foreach ($enabled_plugins as $plugin) {
            $results[] = ['value' => $plugin->type, 'text' => $plugin->displayname];
        }

        return $results;
    }
}