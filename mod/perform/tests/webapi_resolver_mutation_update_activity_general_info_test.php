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
 * @author Murali Nair <murali.nair@totaralearning.com>
 * @package mod_perform
 */

use mod_perform\webapi\resolver\mutation\update_activity_general_info;

use totara_core\advanced_feature;

use totara_webapi\phpunit\webapi_phpunit_helper;

/**
 * @group perform
 * Tests the mutation to create assignments for self or other
 */
class mod_perform_webapi_resolver_mutation_update_activity_general_info_testcase extends advanced_testcase {
    private const MUTATION = 'mod_perform_update_activity_general_info';

    use webapi_phpunit_helper;

    public function test_user_cannot_update_without_permission(): void {
        [, $args, $context] = $this->create_activity();

        $user = self::getDataGenerator()->create_user();
        self::setUser($user);

        $this->expectException(required_capability_exception::class);
        $this->expectExceptionMessage('Manage performance activities');

        update_activity_general_info::resolve($args, $context);
    }

    public function test_update_success(): void {
        [$activity, $args, $context] = $this->create_activity();
        $expected_type = $activity->type;

        ['activity' => $activity] = update_activity_general_info::resolve($args, $context);

        // Return values should be updated
        self::assertEquals($activity->id, $args['activity_id']);
        self::assertEquals($activity->name, $args['name']);
        self::assertEquals($activity->description, $args['description']);

        $actual_type = $activity->type;
        $this->assertEquals($expected_type->name, $actual_type->name, "wrong type name");
        $this->assertEquals($expected_type->display_name, $actual_type->display_name, "wrong type display");
    }

    public function test_activity_must_belong_to_user(): void {
        $data_generator = self::getDataGenerator();

        $user1 = $data_generator->create_user();
        $user2 = $data_generator->create_user();

        [$created_activity, $args, $context] = $this->create_activity($user1);

        /** @type activity $returned_activity */
        ['activity' => $returned_activity] = update_activity_general_info::resolve($args, $context);

        $this->assertEquals($created_activity->id, $returned_activity->id);
        $this->assertEquals($created_activity->name, $returned_activity->name);

        self::setUser($user2);
        $this->expectException(moodle_exception::class);
        update_activity_general_info::resolve($args, $context);
    }

    public function test_successful_ajax_call(): void {
        [$activity, $args, ] = $this->create_activity();

        $result = $this->parsed_graphql_operation(self::MUTATION, $args);
        $this->assert_webapi_operation_successful($result);

        $result = $this->get_webapi_operation_data($result);
        $this->assertNotNull($result, 'null result');

        $result = $result['activity'];
        $this->assertEquals($activity->id, $result['id']);
        $this->assertEquals($activity->name, $result['name']);

        $type = $result['type'];
        $this->assertEquals($activity->type->display_name, $type['display_name']);
    }

    public function test_failed_ajax_query(): void {
        [, $args, ] = $this->create_activity();

        $feature = 'performance_activities';
        advanced_feature::disable($feature);
        $result = $this->parsed_graphql_operation(self::MUTATION, $args);
        $this->assert_webapi_operation_failed($result, 'Feature performance_activities is not available.');
        advanced_feature::enable($feature);

        $result = $this->parsed_graphql_operation(self::MUTATION, []);
        $this->assert_webapi_operation_failed($result, 'activity_id');
    }

    private function create_activity(?stdClass $as_user = null): array {
        if ($as_user) {
            self::setUser($as_user);
        } else {
            self::setAdminUser();
        }

        /** @var mod_perform_generator $perform_generator */
        $perform_generator = self::getDataGenerator()->get_plugin_generator('mod_perform');
        $activity = $perform_generator->create_activity_in_container();

        $args = [
            'activity_id' => $activity->id,
            'name' => $activity->name,
            'description' => $activity->description,
        ];

        $context = $this->create_webapi_context(self::MUTATION);
        $context->set_relevant_context($activity->get_context());

        return [$activity, $args, $context];
    }
}
