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
use mod_perform\webapi\resolver\query\activity as activity_resolver;
use \mod_perform\models\activity\activity;
use totara_webapi\graphql;

class webapi_resolver_query_activity_test extends advanced_testcase {

    public function test_get_activity(): void {
        self::setAdminUser();
        $created_activity = $this->create_activity();
        $id = $created_activity->get_id();

        $returned_activity = activity_resolver::resolve(['activity_id' => $id], $this->get_execution_context());

        $this->assertEquals($id, $returned_activity->get_id());
        $this->assertEquals($created_activity->name, $returned_activity->name);
    }

    public function test_activity_must_belong_to_user(): void {
        $data_generator = self::getDataGenerator();

        $user1 = $data_generator->create_user();
        $user2 = $data_generator->create_user();

        self::setUser($user1);
        $created_activity = $this->create_activity();
        $id = $created_activity->get_id();

        // Returns the activity for the user that created it
        $returned_activity = activity_resolver::resolve(['activity_id' => $id], $this->get_execution_context());
        $this->assertEquals($id, $returned_activity->get_id());
        $this->assertEquals($created_activity->name, $returned_activity->name);

        self::setUser($user2);
        $this->expectException(moodle_exception::class);
        activity_resolver::resolve(['activity_id' => $id], $this->get_execution_context());
    }

    public function test_get_activity_non_admin(): void {
        $this->expectException(moodle_exception::class);
        self::setGuestUser();

        $created_activity = $this->create_activity();
        $id = $created_activity->get_id();

        activity_resolver::resolve(['activity_id' => $id], $this->get_execution_context());
    }

    private function create_activity(): activity {
        /** @var mod_perform_generator $perform_generator */
        $perform_generator = self::getDataGenerator()->get_plugin_generator('mod_perform');

        return $perform_generator->create_activity_in_container(['activity_name' => 'Mid year performance']);
    }

    /**
     * Helper to get execution context
     *
     * @param string $type
     * @param string|null $operation
     * @return execution_context
     */
    private function get_execution_context(string $type = 'dev', ?string $operation = null): execution_context {
        return execution_context::create($type, $operation);
    }
}