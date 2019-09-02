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

use totara_competency\plugintypes;

class totara_competency_plugintypes_testcase extends \advanced_testcase {

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
     */
    public function test_get_installed_plugins($plugintype, $configsetting) {
        $enabled_setting = $plugintype . '_types_enabled';

        $plugininfos = \core_plugin_manager::instance()->get_plugins_of_type($plugintype);
        $plugins = array_keys($plugininfos);

        // Initially there are no config for enabled plugins
        $configvalue = get_config($configsetting, $enabled_setting);
        $this->assertFalse($configvalue);

        // Initially all plugins are set to be enabled except learning_plan
        $installed = plugintypes::get_installed_plugins($plugintype, $configsetting);
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
    public function test_get_enable_and_disable($plugintype, $configsetting) {

        $enabled_setting = $plugintype . '_types_enabled';

        // Set up some dummy enabled type configuration
        set_config($enabled_setting, 'Type1,Type2,Type3', $configsetting);

        $enabledtypes = plugintypes::get_enabled_plugins($plugintype, $configsetting);

        $this->assertTrue(in_array('Type1', $enabledtypes));
        $this->assertTrue(in_array('Type2', $enabledtypes));
        $this->assertTrue(in_array('Type3', $enabledtypes));
        $this->assertFalse(in_array('Type4', $enabledtypes));

        $updatedtypes = plugintypes::enable_plugin('Type4', $plugintype, $configsetting);
        $this->assertTrue(in_array('Type1', $updatedtypes));
        $this->assertTrue(in_array('Type2', $updatedtypes));
        $this->assertTrue(in_array('Type3', $updatedtypes));
        $this->assertTrue(in_array('Type4', $updatedtypes));

        // Re-retrieve also
        $enabledtypes = plugintypes::get_enabled_plugins($plugintype, $configsetting);
        $this->assertTrue(in_array('Type1', $enabledtypes));
        $this->assertTrue(in_array('Type2', $enabledtypes));
        $this->assertTrue(in_array('Type3', $enabledtypes));
        $this->assertTrue(in_array('Type4', $enabledtypes));

        // Disable one-by-one until the last to check edge cases
        $updatedtypes = plugintypes::disable_plugin('Type1', $plugintype, $configsetting);
        $this->assertFalse(in_array('Type1', $updatedtypes));
        $this->assertTrue(in_array('Type2', $updatedtypes));
        $this->assertTrue(in_array('Type3', $updatedtypes));
        $this->assertTrue(in_array('Type4', $updatedtypes));

        $enabledtypes = plugintypes::get_enabled_plugins($plugintype, $configsetting);
        $this->assertFalse(in_array('Type1', $enabledtypes));
        $this->assertTrue(in_array('Type2', $enabledtypes));
        $this->assertTrue(in_array('Type3', $enabledtypes));
        $this->assertTrue(in_array('Type4', $enabledtypes));

        $updatedtypes = plugintypes::disable_plugin('Type3', $plugintype, $configsetting);
        $this->assertFalse(in_array('Type1', $updatedtypes));
        $this->assertTrue(in_array('Type2', $updatedtypes));
        $this->assertFalse(in_array('Type3', $updatedtypes));
        $this->assertTrue(in_array('Type4', $updatedtypes));

        $enabledtypes = plugintypes::get_enabled_plugins($plugintype, $configsetting);
        $this->assertFalse(in_array('Type1', $enabledtypes));
        $this->assertTrue(in_array('Type2', $enabledtypes));
        $this->assertFalse(in_array('Type3', $enabledtypes));
        $this->assertTrue(in_array('Type4', $enabledtypes));

        $updatedtypes = plugintypes::disable_plugin('Type4', $plugintype, $configsetting);
        $this->assertFalse(in_array('Type1', $updatedtypes));
        $this->assertTrue(in_array('Type2', $updatedtypes));
        $this->assertFalse(in_array('Type3', $updatedtypes));
        $this->assertFalse(in_array('Type4', $updatedtypes));

        $enabledtypes = plugintypes::get_enabled_plugins($plugintype, $configsetting);
        $this->assertFalse(in_array('Type1', $enabledtypes));
        $this->assertTrue(in_array('Type2', $enabledtypes));
        $this->assertFalse(in_array('Type3', $enabledtypes));
        $this->assertFalse(in_array('Type4', $enabledtypes));

        $updatedtypes = plugintypes::disable_plugin('Type2', $plugintype, $configsetting);
        $this->assertFalse(in_array('Type1', $updatedtypes));
        $this->assertFalse(in_array('Type2', $updatedtypes));
        $this->assertFalse(in_array('Type3', $updatedtypes));
        $this->assertFalse(in_array('Type4', $updatedtypes));

        $enabledtypes = plugintypes::get_enabled_plugins($plugintype, $configsetting);
        $this->assertFalse(in_array('Type1', $enabledtypes));
        $this->assertFalse(in_array('Type2', $enabledtypes));
        $this->assertFalse(in_array('Type3', $enabledtypes));
        $this->assertFalse(in_array('Type4', $enabledtypes));
    }
}
