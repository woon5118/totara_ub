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

use container_perform\create_exception;
use mod_perform\models\activity\activity_type;
use mod_perform\webapi\resolver\mutation\create_activity;
use totara_core\advanced_feature;
use totara_webapi\phpunit\webapi_phpunit_helper;

/**
 * @coversDefaultClass create_activity.
 *
 * @group perform
 */
class mod_perform_webapi_resolver_mutation_create_activity_testcase extends advanced_testcase {
    private const MUTATION = 'mod_perform_create_activity';

    use webapi_phpunit_helper;

    public function test_create_activity(): void {
        $this->setAdminUser();
        $expected_type = activity_type::load_by_name('check-in');
        $args = [
            'name' => "Mid year performance review",
            'description' => "Test Description",
            'type' => $expected_type->id
        ];
        $context = $this->create_webapi_context(self::MUTATION);

        /** @type activity $result */
        ['activity' => $result] = create_activity::resolve($args, $context);
        $this->assertSame('Mid year performance review', $result->name);
        $this->assertSame('Test Description', $result->description);

        $actual_type = $result->type;
        $this->assertEquals($expected_type->name, $actual_type->name, "wrong type");
        $this->assertEquals($expected_type->display_name, $actual_type->display_name, "wrong display name");
    }

    public function test_create_activity_for_non_admin_user(): void {
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);
        $args = [
            'name' => 'Mid year performance review',
            'description' => 'Test Description',
            'type' => activity_type::load_by_name('appraisal')->id
        ];
        $context = $this->create_webapi_context(self::MUTATION);

        $this->expectException(create_exception::class);
        $this->expectExceptionMessage('You do not have the permission to create a performance activity');
        create_activity::resolve($args, $context);
    }

    public function test_create_activity_with_empty_name(): void {
        $this->setAdminUser();
        $args = [
            'name' => '',
            'description' => 'Test Description',
            'type' => activity_type::load_by_name('feedback')->id
        ];
        $context = $this->create_webapi_context(self::MUTATION);

        $this->expectException(create_exception::class);
        $this->expectExceptionMessage('You are not allowed to create an activity with an empty name');
        create_activity::resolve($args, $context);
    }

    public function test_create_activity_with_empty_description(): void {
        $this->setAdminUser();
        $type_id = activity_type::load_by_name('feedback')->id;
        $args = [
            'name' => 'Mid year performance review',
            'description' => "",
            'type' => $type_id
        ];
        $context = $this->create_webapi_context(self::MUTATION);

        ['activity' => $result] = create_activity::resolve($args, $context);
        $this->assertSame('Mid year performance review', $result->name);
        $this->assertSame('', $result->description);

        $args = [
            'name' => 'Mid year performance review',
            'description' => null,
            'type' => $type_id
        ];

        ['activity' => $result] = create_activity::resolve($args, $context);
        $this->assertSame('Mid year performance review', $result->name);
        $this->assertNull($result->description);
    }

    public function test_create_activity_with_empty_type(): void {
        $this->setAdminUser();
        $args = [
            'name' => 'Mid year performance review'
        ];
        $context = $this->create_webapi_context(self::MUTATION);

        $this->expectException(create_exception::class);
        $this->expectExceptionMessageRegExp("/type/");
        create_activity::resolve($args, $context);
    }

    public function test_create_activity_with_invalid_type(): void {
        $this->setAdminUser();
        $args = [
            'name' => 'Mid year performance review',
            'type' => 12334
        ];
        $context = $this->create_webapi_context(self::MUTATION);

        $this->expectException(create_exception::class);
        $this->expectExceptionMessageRegExp("/type id/");
        create_activity::resolve($args, $context);
    }

    /**
     * @covers ::resolve
     */
    public function test_successful_ajax_call(): void {
        $this->setAdminUser();

        $expected_type = activity_type::load_by_name('check-in');
        $args = [
            'name' => "Mid year performance review",
            'description' => "Test Description",
            'type' => $expected_type->id
        ];

        $result = $this->parsed_graphql_operation(self::MUTATION, $args);
        $this->assert_webapi_operation_successful($result);

        $result = $this->get_webapi_operation_data($result);
        $this->assertNotEmpty($result, 'activity creation failed');

        $activity = $result['activity'];
        $this->assertSame($args['name'], $activity['name']);
        $this->assertSame($args['description'], $activity['description']);

        $actual_type = $activity['type'];
        $this->assertEquals($expected_type->display_name, $actual_type['display_name'], "wrong type");
    }

    /**
     * @covers ::resolve
     */
    public function test_failed_ajax_call(): void {
        $this->setAdminUser();

        $args = [
            'name' => "Mid year performance review",
            'description' => "Test Description",
            'type' => activity_type::load_by_name('check-in')->id
        ];

        $feature = 'performance_activities';
        advanced_feature::disable($feature);
        $result = $this->parsed_graphql_operation(self::MUTATION, $args);
        $this->assert_webapi_operation_failed($result, $feature);
        advanced_feature::enable($feature);

        self::setGuestUser();
        $result = $this->parsed_graphql_operation(self::MUTATION, $args);
        $this->assert_webapi_operation_failed($result, 'permission');
    }
}