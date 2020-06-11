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
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Chris Snyder <chris.snyder@totaralearning.com>
 * @package mod_perform
 */

use container_perform\create_exception;
use mod_perform\models\activity\activity as activity_model;
use mod_perform\models\activity\notification as notification_model;
use mod_perform\webapi\resolver\mutation\create_notification;
use totara_core\advanced_feature;
use totara_webapi\phpunit\webapi_phpunit_helper;

/**
 * @coversDefaultClass create_notification
 *
 * @group perform
 */
class mod_perform_webapi_resolver_mutation_create_notification_testcase extends advanced_testcase {
    private const MUTATION = 'mod_perform_create_notification';

    use webapi_phpunit_helper;

    private function create_test_data(): activity_model {
        self::setAdminUser();

        $data_generator = self::getDataGenerator();
        /** @var mod_perform_generator $perform_generator */
        $perform_generator = $data_generator->get_plugin_generator('mod_perform');

        /** @var activity_model $activity */
        return $perform_generator->create_activity_in_container([]);
    }

    public function test_create_active_notification(): void {
        $activity = $this->create_test_data();
        $args = [
            'input' => [
                'activity_id' => $activity->id,
                'class_key' => "instance_created",
                'active' => true
            ]
        ];

        /** @type notification_model $result */
        $result = $this->resolve_graphql_mutation(self::MUTATION, $args);

        $notification = $result->notification;

        $this->assertNotEmpty($notification->id);
        $this->assertSame(get_string('notification_instance_created', 'mod_perform'), $notification->name);
        $this->assertTrue($notification->active);
    }

    public function test_create_inactive_notification(): void {
        $activity = $this->create_test_data();
        $args = [
            'input' => [
                'activity_id' => $activity->id,
                'class_key' => "instance_created",
                'active' => false
            ]
        ];

        /** @type notification_model $result */
        $result = $this->resolve_graphql_mutation(self::MUTATION, $args);

        $notification = $result->notification;

        $this->assertNotEmpty($notification->id);
        $this->assertSame(get_string('notification_instance_created', 'mod_perform'), $notification->name);
        $this->assertFalse($notification->active);
    }

    public function test_create_notification_for_non_admin_user(): void {
        $activity = $this->create_test_data();
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);
        $args = [
            'input' => [
                'activity_id' => $activity->id,
                'class_key' => "instance_created",
                'active' => true
            ]
        ];

        $this->expectException(moodle_exception::class);
        $this->expectExceptionMessage("Invalid activity");
        $result = $this->resolve_graphql_mutation(self::MUTATION, $args);
    }

    public function test_create_notification_with_missing_input_property(): void {
        $activity = $this->create_test_data();
        $args = [
            'activity_id' => $activity->id,
            'class_key' => "instance_created",
            'active' => true
        ];

        $this->expectException(invalid_parameter_exception::class);
        // Note: the middleware catches this before it ever gets to the resolver, because it can't find the activity_id.
        $this->expectExceptionMessage('Invalid parameter value detected (invalid activity id)');
        $result = $this->resolve_graphql_mutation(self::MUTATION, $args);
    }

    public function test_create_notification_with_missing_activity_id(): void {
        $this->setAdminUser();
        $args = [
            'input' => [
                'class_key' => "instance_created",
                'active' => true
            ]
        ];

        $this->expectException(invalid_parameter_exception::class);
        $this->expectExceptionMessage('Invalid parameter value detected (invalid activity id)');
        $result = $this->resolve_graphql_mutation(self::MUTATION, $args);
    }

    public function test_create_notification_with_invalid_activity_id(): void {
        $this->setAdminUser();
        $args = [
            'input' => [
                'activity_id' => 1138,
                'class_key' => "instance_created",
                'active' => true
            ]
        ];

        $this->expectException(moodle_exception::class);
        $this->expectExceptionMessage('Invalid activity');
        $result = $this->resolve_graphql_mutation(self::MUTATION, $args);
    }

    public function test_create_notification_with_missing_class_key(): void {
        $activity = $this->create_test_data();
        $args = [
            'input' => [
                'activity_id' => $activity->id,
                'active' => true
            ]
        ];

        $this->expectException(invalid_parameter_exception::class);
        $this->expectExceptionMessage('Invalid parameter value detected (class_key not set as part of input)');
        $result = $this->resolve_graphql_mutation(self::MUTATION, $args);
    }

    public function test_create_notification_with_invalid_class_key(): void {
        $activity = $this->create_test_data();
        $args = [
            'input' => [
                'activity_id' => $activity->id,
                'class_key' => "unimplemented_class",
                'active' => true
            ]
        ];

        $this->expectException(invalid_parameter_exception::class);
        $this->expectExceptionMessage('Invalid parameter value detected (unimplemented notification class_key)');
        $result = $this->resolve_graphql_mutation(self::MUTATION, $args);
    }

    public function test_create_activity_with_missing_active(): void {
        $activity = $this->create_test_data();
        $args = [
            'input' => [
                'activity_id' => $activity->id,
                'class_key' => "instance_created",
            ]
        ];

        /** @type notification_model $result */
        $result = $this->resolve_graphql_mutation(self::MUTATION, $args);

        $notification = $result->notification;

        $this->assertNotEmpty($notification->id);
        $this->assertSame(get_string('notification_instance_created', 'mod_perform'), $notification->name);
        $this->assertFalse($notification->active);
    }

    /**
     * @covers ::resolve
     */
    public function test_successful_ajax_call(): void {
        $activity = $this->create_test_data();
        $args = [
            'input' => [
                'activity_id' => $activity->id,
                'class_key' => "instance_created",
                'active' => true
            ]
        ];

        $result = $this->parsed_graphql_operation(self::MUTATION, $args);
        $this->assert_webapi_operation_successful($result);

        $result = $this->get_webapi_operation_data($result);
        $this->assertNotEmpty($result, 'notification creation failed');

        $notification = $result['notification'];
        $this->assertNotEmpty($notification['id']);
        $this->assertSame(get_string('notification_instance_created', 'mod_perform'), $notification['name']);
        $this->assertSame($args['input']['active'], $notification['active']);

        // Check your work.
        $actual_notification = notification_model::load_by_id($notification['id']);
        $this->assertEquals($actual_notification->id, $notification['id']);
        $this->assertSame($actual_notification->name, $notification['name']);
    }

    /**
     * @covers ::resolve
     */
    public function test_failed_ajax_call(): void {
        $activity = $this->create_test_data();
        $args = [
            'input' => [
                'activity_id' => $activity->id,
                'class_key' => "instance_created",
                'active' => true
            ]
        ];

        $feature = 'performance_activities';
        advanced_feature::disable($feature);
        $result = $this->parsed_graphql_operation(self::MUTATION, $args);
        $this->assert_webapi_operation_failed($result, 'Feature performance_activities is not available.');
        advanced_feature::enable($feature);

        self::setGuestUser();
        $result = $this->parsed_graphql_operation(self::MUTATION, $args);
        $this->assert_webapi_operation_failed($result, 'Invalid activity');
    }
}