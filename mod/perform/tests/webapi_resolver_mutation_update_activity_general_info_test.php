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
 * @author Jaron Steenson <jaron.steenson@totaralearning.com>
 * @package mod_perform
 */

use core\webapi\execution_context;
use mod_perform\webapi\resolver\mutation\update_activity_general_info;
use mod_perform\models\activity\activity;

defined('MOODLE_INTERNAL') || die();

/**
 * @group perform
 * Tests the mutation to create assignments for self or other
 */
class webapi_resolver_mutation_update_activity_general_info_test extends advanced_testcase {

    public function test_user_cannot_update_without_permission(): void {
        self::setAdminUser();

        $activity = $this->create_activity();
        $args = $this->to_args_payload($activity);

        $user = self::getDataGenerator()->create_user();
        self::setUser($user);

        $this->expectException(required_capability_exception::class);
        $this->expectExceptionMessage('Manage performance activities');

        update_activity_general_info::resolve($args, $this->get_execution_context());
    }

    public function test_update_success(): void {
        self::setAdminUser();

        $activity = $this->create_activity();
        $args = $this->to_args_payload($activity);

        ['activity' => $activity] = update_activity_general_info::resolve($args, $this->get_execution_context());


        // Return values should be updated
        self::assertEquals($activity->id, $args['activity_id']);
        self::assertEquals($activity->name, $args['name']);
        self::assertEquals($activity->description, $args['description']);
    }

    public function test_activity_must_belong_to_user(): void {
        $data_generator = self::getDataGenerator();

        $user1 = $data_generator->create_user();
        $user2 = $data_generator->create_user();

        self::setUser($user1);
        $created_activity = $this->create_activity();
        $args = $this->to_args_payload($created_activity);

        /** @type activity $returned_activity */
        ['activity' => $returned_activity] = update_activity_general_info::resolve($args, $this->get_execution_context());

        $this->assertEquals($created_activity->get_id(), $returned_activity->get_id());
        $this->assertEquals($created_activity->name, $returned_activity->name);

        self::setUser($user2);
        $this->expectException(moodle_exception::class);
        update_activity_general_info::resolve($args, $this->get_execution_context());
    }

    private function get_execution_context(string $type = 'dev', ?string $operation = null): execution_context {
        return execution_context::create($type, $operation);
    }

    private function create_activity(): activity {
        /** @var mod_perform_generator $perform_generator */
        $perform_generator = self::getDataGenerator()->get_plugin_generator('mod_perform');
        return $perform_generator->create_activity_in_container();
    }

    private function to_args_payload(activity $activity): array {
        return [
            'activity_id' => $activity->get_id(),
            'name' => $activity->name,
            'description' => $activity->description,
        ];
    }

}
