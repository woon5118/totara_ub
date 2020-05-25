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
 * @author Oleg Demeshev <oleg.demeshev@totaralearning.com>
 * @package mod_perform
 */

use mod_perform\state\activity\draft;
use mod_perform\data_providers\activity\activity;
use mod_perform\webapi\resolver\mutation\clone_activity;
use totara_core\advanced_feature;
use totara_webapi\phpunit\webapi_phpunit_helper;

/**
 * @coversDefaultClass clone_activity
 *
 * @group perform
 */
class mod_perform_webapi_resolver_mutation_clone_activity_testcase extends advanced_testcase {
    private const MUTATION = 'mod_perform_clone_activity';

    use webapi_phpunit_helper;

    public function test_clone_activity(): void {
        [$activity, $args, $context] = $this->create_activity();
        self::assertTrue($this->container_course_exists($activity->course));

        $clone_activity = clone_activity::resolve($args, $context);
        $this->assertIsObject($clone_activity['activity']);
        $data_provider = new activity();
        $activities = $data_provider->fetch()->get();
        $this->assertCount(2, $activities);
    }

    /**
     * Test the mutation through the GraphQL stack.
     */
    public function test_execute_query_successful(): void {
        [$activity, $args, ] = $this->create_activity();
        self::assertTrue($this->container_course_exists($activity->course));

        $result = $this->parsed_graphql_operation(self::MUTATION, $args);
        $this->assert_webapi_operation_successful($result);

        $result = $this->get_webapi_operation_data($result);
        $this->assertNotEmpty($result, 'result empty');

        $activity_result = $result['activity'] ?? null;
        $this->assertNotEmpty($activity_result, 'result empty');
        $this->assertEquals($activity->name . get_string('activity_name_restore_suffix', 'mod_perform'), $activity_result['name']);
        $this->assertEquals(draft::get_code(), $activity_result['state_details']['code']);
    }

    public function test_failed_ajax_query(): void {
        [$activity, $args, ] = $this->create_activity();

        $feature = 'performance_activities';
        advanced_feature::disable($feature);
        $result = $this->parsed_graphql_operation(self::MUTATION, $args);
        $this->assert_webapi_operation_failed($result, $feature);
        advanced_feature::enable($feature);

        $result = $this->parsed_graphql_operation(self::MUTATION, []);
        $this->assert_webapi_operation_failed($result, 'input');

        $args['input']['activity_id'] = 0;
        $result = $this->parsed_graphql_operation(self::MUTATION, $args);
        $this->assert_webapi_operation_failed($result, 'activity id');

        $activity_id = 999;
        $args['input']['activity_id'] = $activity_id;
        $result = $this->parsed_graphql_operation(self::MUTATION, $args);
        $this->assert_webapi_operation_failed($result, "$activity_id");

        self::setGuestUser();
        $args['input']['activity_id'] = $activity->id;
        $result = $this->parsed_graphql_operation(self::MUTATION, $args);
        $this->assert_webapi_operation_failed($result, 'not accessible');
    }

    private function container_course_exists(int $course_id): bool {
        global $DB;
        return $DB->record_exists('course', ['id' => $course_id]);
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
            'input' => [
                'activity_id' => $activity->id
            ]
        ];

        $context = $this->create_webapi_context(self::MUTATION);
        $context->set_relevant_context($activity->get_context());

        return [$activity, $args, $context];
    }
}
