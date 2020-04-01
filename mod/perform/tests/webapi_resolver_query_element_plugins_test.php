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
 * @author Samantha Jayasinghe <samantha.jayasinghe@totaralearning.com>
 * @package mod_perform
 */

/**
 * @group perform
 */
use core\webapi\execution_context;
use mod_perform\webapi\resolver\query\element_plugins;

class mod_perform_webapi_resolver_query_element_plugins_testcase extends advanced_testcase {

    /**
     * Helper to get execution context
     *
     * @param string $type
     * @param string|null $operation
     * @return execution_context
     */
    private function get_execution_context(string $type = 'dev', ?string $operation = null) {
        return execution_context::create($type, $operation);
    }

    public function test_get_element_plugins() {
        $this->setAdminUser();

        $element_plugins = element_plugins::resolve([], $this->get_execution_context());
        foreach ($element_plugins as $element_plugin) {
            $this->assertInstanceOf('mod_perform\models\activity\element_plugin', $element_plugin);
            $this->assertNotEmpty($element_plugin->get_plugin_name());
            $this->assertNotEmpty($element_plugin->get_name());
            $this->assertNotEmpty($element_plugin->get_admin_form_component());
            $this->assertNotEmpty($element_plugin->get_admin_display_component());
            $this->assertNotEmpty($element_plugin->get_participant_form_component());
        }
    }
}