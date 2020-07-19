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

use mod_perform\models\activity\activity;
use mod_perform\models\activity\activity_type as activity_type_model;
use mod_perform\state\activity\draft;
use totara_core\advanced_feature;
use totara_webapi\phpunit\webapi_phpunit_helper;

/**
 * @group perform
 * Tests the mutation to create assignments for self or other
 */
class mod_perform_webapi_resolver_mutation_update_activity_testcase extends advanced_testcase {
    private const MUTATION = 'mod_perform_update_activity';

    use webapi_phpunit_helper;

    public function test_user_cannot_update_without_permission(): void {
        [, $args] = $this->create_activity();

        $user = self::getDataGenerator()->create_user();
        self::setUser($user);

        $this->expectException(moodle_exception::class);
        $this->expectExceptionMessage('Invalid activity');

        $this->resolve_graphql_mutation(self::MUTATION, $args);
    }

    public function test_update_success(): void {
        [, $args] = $this->create_activity();

        /** @var activity $activity */
        ['activity' => $activity] = $this->resolve_graphql_mutation(self::MUTATION, $args);

        // Return values should be updated
        $this->assert_base_update_result($args, $activity);
    }

    public function test_activity_must_belong_to_user(): void {
        $data_generator = self::getDataGenerator();

        $user1 = $data_generator->create_user();
        $user2 = $data_generator->create_user();

        [, $args] = $this->create_activity($user1);

        /** @type activity $returned_activity */
        ['activity' => $returned_activity] = $this->resolve_graphql_mutation(self::MUTATION, $args);

        $this->assert_base_update_result($args, $returned_activity);

        self::setUser($user2);
        $this->expectException(moodle_exception::class);

        $this->resolve_graphql_mutation(self::MUTATION, $args);
    }

    public function test_successful_ajax_call(): void {
        [, $args] = $this->create_activity();

        $result = $this->parsed_graphql_operation(self::MUTATION, $args);
        $this->assert_webapi_operation_successful($result);

        $result = $this->get_webapi_operation_data($result);
        $this->assertNotNull($result, 'null result');

        $result = $result['activity'];
        $this->assert_base_update_result($args, $result);

        $actual_type_display_name = $result['type']['display_name'];
        $expected_type_display_name = activity_type_model::load_by_id($args['type_id'])->get_display_name();
        $this->assertEquals($actual_type_display_name, $expected_type_display_name);
    }

    public function test_failed_ajax_query(): void {
        [, $args] = $this->create_activity();

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
        $activity = $perform_generator->create_activity_in_container(['activity_status' => draft::get_code()]);
        $new_type_id = 3;

        $args = [
            'activity_id' => $activity->id,
            'name' => "Activity-1",
            'description' => "Description of Activity 1",
            'type_id' => $new_type_id,
            'anonymous_responses' => true,
        ];

        return [$activity, $args];
    }

    /**
     * @param array $args
     * @param array|object|activity $returned_activity
     */
    private function assert_base_update_result(array $args, $returned_activity): void {
        if (is_array($returned_activity)) {
            $returned_activity = (object) $returned_activity;
            $returned_activity->type = (object) $returned_activity->type;
        }

        self::assertEquals($returned_activity->id, $args['activity_id']);
        self::assertEquals($returned_activity->name, $args['name']);
        self::assertEquals($returned_activity->description, $args['description']);
        self::assertEquals($returned_activity->type->id, $args['type_id']);
        self::assertTrue($returned_activity->anonymous_responses);
    }

}
