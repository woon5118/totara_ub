<?php
/**
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
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Kian Nguyen <kian.nguyen@totaralearning.com>
 * @package editor_weka
 */
namespace editor_weka\config;

/**
 * Config class for editor.
 */
final class factory {
    /**
     * @var array
     */
    private $configuration;

    /**
     * editor_config constructor.
     */
    public function __construct() {
        $this->configuration = [];
    }

    /**
     * @return void
     */
    public function load(): void {
        if (!empty($this->configuration)) {
            return;
        }

        $istest = (defined('PHPUNIT_TEST') && PHPUNIT_TEST);
        $cache = \cache::make('editor_weka', 'editorconfig');

        if (!$istest) {
            // Cache should only be done for the non unit-testing environment.
            $configuration = $cache->get('configuration');

            if ($configuration && is_array($configuration)) {
                $this->configuration = $configuration;
                return;
            }
        }

        $types = \core_component::get_plugin_types();

        foreach ($types as $type => $directory) {
            $pluginfiles = \core_component::get_plugin_list_with_file($type, 'db/editor_weka.php');
            if (empty($pluginfiles)) {
                continue;
            }

            foreach ($pluginfiles as $pluginname => $file) {
                $component = "{$type}_{$pluginname}";

                if (isset($this->configuration[$component])) {
                    debugging(
                        "The component '{$component}' had already been loaded with the configuration",
                        DEBUG_DEVELOPER
                    );

                    continue;
                }

                $editor = [];
                require($file);

                if (!empty($editor)) {
                    $this->configuration[$component] = $editor;
                }
            }
        }

        if (!$istest && !empty($this->configuration)) {
            // Cache the configs if not test.
            $cache->set('configuration', $this->configuration);
        }
    }

    /**
     * @param string $component
     * @param string $area
     *
     * @return config_item|null
     */
    public function get_configuration(string $component, string $area): ?config_item {
        $this->load();

        if (!isset($this->configuration[$component])) {
            // No configuration found for component
            return null;
        }

        $config = $this->configuration[$component];
        if (!isset($config[$area])) {
            return null;
        }

        $data = $config[$area];

        if (array_key_exists('area', $data)) {
            debugging(
                "Please do not set the key 'area' for the editor config, as it will be reset",
                DEBUG_DEVELOPER
            );
        }

        if (array_key_exists('component', $data)) {
            debugging(
                "Please do not set the key 'component' for the editor config, as it will be reset",
                DEBUG_DEVELOPER
            );
        }

        $data['component'] = $component;
        $data['area'] = $area;

        return config_item::from_array($data);
    }
}