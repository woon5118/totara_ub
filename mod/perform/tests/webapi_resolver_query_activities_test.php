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
use mod_perform\webapi\resolver\query\activities;
use totara_webapi\graphql;

class mod_perform_webapi_resolver_query_activities_testcase extends advanced_testcase {

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

    public function test_get_activities() {
        $this->setAdminUser();
        $this->create_test_data();
        $activities = activities::resolve([], $this->get_execution_context());
        $this->assertCount(2, $activities);
        $this->assertEqualsCanonicalizing(
            ['Mid year performance', 'End year performance'],
            [$activities[0]->get_entity()->name, $activities[1]->get_entity()->name]
        );
    }

    public function test_get_empty_activities() {
        $this->setAdminUser();
        $activities = activities::resolve([], $this->get_execution_context());
        $this->assertCount(0, $activities);
    }

    /**
     * @expectedException moodle_exception
     */
    public function test_get_activities_non_admin() {
        global $USER;
        $this->setUser($USER);
        $activities = activities::resolve([], $this->get_execution_context());
    }

    public function test_ajax_query() {
        $this->setAdminUser();
        $this->create_test_data();
        $result = graphql::execute_operation(
            execution_context::create('ajax', 'mod_perform_activities'),
            []
        );
        $this->assertCount(2, $result->data['mod_perform_activities']);
    }

    private function create_test_data() {
        $perform_generator = $this->getDataGenerator()->get_plugin_generator('mod_perform');
        $perform_generator->create_activity(['name' => 'Mid year performance']);
        $perform_generator->create_activity(['name' => 'End year performance']);
    }
}