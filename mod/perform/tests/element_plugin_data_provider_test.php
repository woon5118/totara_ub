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

/**
 * @group perform
 */
use  mod_perform\data_providers\activity\element_plugin as element_plugin_data_provider;
use  mod_perform\models\activity\element_plugin as element_plugin_model;

class mod_perform_element_plugin_data_provider_testcase extends advanced_testcase {

    public function test_fetch() {
        // All users should be able to access the list of element plugins, regardless of capabilities.
        $data_generator = $this->getDataGenerator();
        $user1 = $data_generator->create_user();
        $this->setUser($user1);

        $data_provider = new element_plugin_data_provider();
        $element_plugins = $data_provider->fetch()->get();

        foreach ($element_plugins as $element_plugin) {
            $this->assertTrue($element_plugin instanceof element_plugin_model);
        }

        $actual_plugin_names = [];
        foreach ($element_plugins as $element_plugin) {
            /** @var element_plugin_model $element_plugin */
            $actual_plugin_names[] = $element_plugin->get_plugin_name();
        }

        $expected_plugins = \core_component::get_plugin_list('performelement');

        $this->assertEqualsCanonicalizing(array_keys($expected_plugins), $actual_plugin_names);
    }
}