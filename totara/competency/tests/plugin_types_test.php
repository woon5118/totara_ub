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

use totara_competency\plugin_types;

class totara_competency_plugin_types_testcase extends \advanced_testcase {

    /**
     * Data provider for test_get_all_installed_plugins.
     */
    public function data_provider_test_get_all_data() {
        return [
            [
                'plugintype' => 'criteria',
                'configsetting' => 'totara_criteria',
            ],
            [
                'plugintype' => 'pathway',
                'configsetting' => 'totara_competency',
            ],
            [
                'plugintype' => 'aggregation',
                'configsetting' => 'totara_competency',
            ],
        ];
    }

    /**
     * Test get_all_plugins
     *
     * @dataProvider data_provider_test_get_all_data
     *
     * @param $plugin_type
     * @param $config_setting
     */
    public function test_get_installed_plugins($plugin_type, $config_setting) {
        $enabled_setting = $plugin_type . '_types_enabled';

        $plugininfos = \core_plugin_manager::instance()->get_plugins_of_type($plugin_type);
        $plugins = array_keys($plugininfos);

        // Initially there are no config for enabled plugins
        $configvalue = get_config($config_setting, $enabled_setting);
        $this->assertFalse($configvalue);

        // Initially all plugins are set to be enabled except learning_plan
        $installed = plugin_types::get_installed_plugins($plugin_type, $config_setting);
        foreach ($plugins as $plugin) {
            // All plugins should be returned as a type
            // All plugins/types should be 'enabled'
            $this->assertTrue(array_key_exists($plugin, $installed));
            $this->assertTrue($installed[$plugin]->enabled);
        }
    }

    /**
     * Test get_enabled_plugins, enable and disable
     *
     * @dataProvider data_provider_test_get_all_data
     */
    public function test_get_enable_and_disable($plugin_type, $config_setting) {

        $enabled_setting = $plugin_type . '_types_enabled';

        // Set up some dummy enabled type configuration
        set_config($enabled_setting, 'Type1,Type2,Type3', $config_setting);

        $enabledtypes = plugin_types::get_enabled_plugins($plugin_type, $config_setting);

        $this->assertTrue(in_array('Type1', $enabledtypes));
        $this->assertTrue(in_array('Type2', $enabledtypes));
        $this->assertTrue(in_array('Type3', $enabledtypes));
        $this->assertFalse(in_array('Type4', $enabledtypes));

        $updatedtypes = plugin_types::enable_plugin('Type4', $plugin_type, $config_setting);
        $this->assertTrue(in_array('Type1', $updatedtypes));
        $this->assertTrue(in_array('Type2', $updatedtypes));
        $this->assertTrue(in_array('Type3', $updatedtypes));
        $this->assertTrue(in_array('Type4', $updatedtypes));

        // Re-retrieve also
        $enabledtypes = plugin_types::get_enabled_plugins($plugin_type, $config_setting);
        $this->assertTrue(in_array('Type1', $enabledtypes));
        $this->assertTrue(in_array('Type2', $enabledtypes));
        $this->assertTrue(in_array('Type3', $enabledtypes));
        $this->assertTrue(in_array('Type4', $enabledtypes));

        // Disable one-by-one until the last to check edge cases
        $updatedtypes = plugin_types::disable_plugin('Type1', $plugin_type, $config_setting);
        $this->assertFalse(in_array('Type1', $updatedtypes));
        $this->assertTrue(in_array('Type2', $updatedtypes));
        $this->assertTrue(in_array('Type3', $updatedtypes));
        $this->assertTrue(in_array('Type4', $updatedtypes));

        $enabledtypes = plugin_types::get_enabled_plugins($plugin_type, $config_setting);
        $this->assertFalse(in_array('Type1', $enabledtypes));
        $this->assertTrue(in_array('Type2', $enabledtypes));
        $this->assertTrue(in_array('Type3', $enabledtypes));
        $this->assertTrue(in_array('Type4', $enabledtypes));

        $updatedtypes = plugin_types::disable_plugin('Type3', $plugin_type, $config_setting);
        $this->assertFalse(in_array('Type1', $updatedtypes));
        $this->assertTrue(in_array('Type2', $updatedtypes));
        $this->assertFalse(in_array('Type3', $updatedtypes));
        $this->assertTrue(in_array('Type4', $updatedtypes));

        $enabledtypes = plugin_types::get_enabled_plugins($plugin_type, $config_setting);
        $this->assertFalse(in_array('Type1', $enabledtypes));
        $this->assertTrue(in_array('Type2', $enabledtypes));
        $this->assertFalse(in_array('Type3', $enabledtypes));
        $this->assertTrue(in_array('Type4', $enabledtypes));

        $updatedtypes = plugin_types::disable_plugin('Type4', $plugin_type, $config_setting);
        $this->assertFalse(in_array('Type1', $updatedtypes));
        $this->assertTrue(in_array('Type2', $updatedtypes));
        $this->assertFalse(in_array('Type3', $updatedtypes));
        $this->assertFalse(in_array('Type4', $updatedtypes));

        $enabledtypes = plugin_types::get_enabled_plugins($plugin_type, $config_setting);
        $this->assertFalse(in_array('Type1', $enabledtypes));
        $this->assertTrue(in_array('Type2', $enabledtypes));
        $this->assertFalse(in_array('Type3', $enabledtypes));
        $this->assertFalse(in_array('Type4', $enabledtypes));

        $updatedtypes = plugin_types::disable_plugin('Type2', $plugin_type, $config_setting);
        $this->assertFalse(in_array('Type1', $updatedtypes));
        $this->assertFalse(in_array('Type2', $updatedtypes));
        $this->assertFalse(in_array('Type3', $updatedtypes));
        $this->assertFalse(in_array('Type4', $updatedtypes));

        $enabledtypes = plugin_types::get_enabled_plugins($plugin_type, $config_setting);
        $this->assertFalse(in_array('Type1', $enabledtypes));
        $this->assertFalse(in_array('Type2', $enabledtypes));
        $this->assertFalse(in_array('Type3', $enabledtypes));
        $this->assertFalse(in_array('Type4', $enabledtypes));
    }
}
