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

use mod_perform\webapi\resolver\query\activity;
use totara_core\relationship\resolvers\subject;
use totara_job\relationship\resolvers\appraiser;
use totara_job\relationship\resolvers\manager;

use totara_core\advanced_feature;

use totara_webapi\phpunit\webapi_phpunit_helper;

require_once(__DIR__.'/relationship_testcase.php');

/**
 * @coversDefaultClass activity.
 *
 * @group perform
 */
class mod_perform_webapi_resolver_query_activity_testcase extends mod_perform_relationship_testcase {
    private const QUERY = 'mod_perform_activity';

    use webapi_phpunit_helper;

    public function test_get_activity(): void {
        $created_activity = $this->create_test_data()->activity1;
        $id = $created_activity->id;
        $args = ['activity_id' => $id];

        $context = $this->create_webapi_context(self::QUERY);
        $context->set_relevant_context($created_activity->get_context());
        $returned_activity = activity::resolve($args, $context);

        $this->assertEquals($id, $returned_activity->id);
        $this->assertEquals($created_activity->name, $returned_activity->name);

        $expected_type = $created_activity->type;
        $actual_type = $returned_activity->type;
        $this->assertEquals($expected_type->name, $actual_type->name, "wrong type name");
        $this->assertEquals($expected_type->display_name, $actual_type->display_name, "wrong type display");
    }

    public function test_activity_must_belong_to_user(): void {
        $data_generator = self::getDataGenerator();

        $user1 = $data_generator->create_user();
        $user2 = $data_generator->create_user();

        $created_activity = $this->create_test_data($user1)->activity2;
        $id = $created_activity->id;
        $args = ['activity_id' => $id];

        $context = $this->create_webapi_context(self::QUERY);
        $context->set_relevant_context($created_activity->get_context());

        // Returns the activity for the user that created it
        $returned_activity = activity::resolve($args, $context);
        $this->assertEquals($id, $returned_activity->id);
        $this->assertEquals($created_activity->name, $returned_activity->name);

        self::setUser($user2);
        $this->expectException(moodle_exception::class);
        activity::resolve($args, $context);
    }

    public function test_get_activity_non_admin(): void {
        $created_activity = $this->create_test_data()->activity3;
        $args = ['activity_id' => $created_activity->id];

        $context = $this->create_webapi_context(self::QUERY);
        $context->set_relevant_context($created_activity->get_context());

        $this->expectException(moodle_exception::class);
        self::setGuestUser();
        activity::resolve($args, $context);
    }

    /**
     * Test the query through the GraphQL stack.
     */
    public function test_ajax_query_successful() {
        $data = $this->create_test_data();
        $appraiser_id = $this->perform_generator()->get_relationship(appraiser::class);
        $manager_id = $this->perform_generator()->get_relationship(manager::class);
        $subject_id = $this->perform_generator()->get_relationship(subject::class);

        $id = $data->activity1->id;
        $args = ['activity_id' => $id];

        $result = $this->parsed_graphql_operation(self::QUERY, $args);
        $this->assert_webapi_operation_successful($result);

        $result = $this->get_webapi_operation_data($result);
        $this->assertEquals($id, $result['id']);
        $this->assertEquals('Activity 1', $result['name']);
        $this->assertEquals('test description', $result['description']);

        $expected_type = $data->activity1->type;
        $actual_type = $result['type'];
        $this->assertEquals(
            $expected_type->display_name,
            $actual_type['display_name'],
            "wrong type display"
        );

        $section1 = array_filter($result['sections'], function ($section) use ($data) {
            return (int)$section['id'] === $data->activity1_section1->id;
        });
        $section1_result = reset($section1);
        $this->assertEquals('Activity 1 section 1',  $section1_result['title']);
        $section1_expected_relationships = [
            [
                'id' => $data->activity1_section1_relationship1->id,
                'can_view' => true,
                'can_answer' => true,
                'relationship' => [
                    'id' => $appraiser_id->get_id(),
                    'name' => $appraiser_id->get_name(),
                ],
            ],
            [
                'id' => $data->activity1_section1_relationship2->id,
                'can_view' => true,
                'can_answer' => true,
                'relationship' => [
                    'id' => $manager_id->get_id(),
                    'name' => $manager_id->get_name(),
                ],
            ],
        ];
        // Order of results doesn't matter.
        usort($section1_result['section_relationships'], static function ($a, $b) {
            return $a['id'] - $b['id'];
        });
        $this->assertEquals($section1_expected_relationships, $section1_result['section_relationships']);

        $section2 = array_filter($result['sections'], function ($section) use ($data) {
            return (int)$section['id'] === $data->activity1_section2->id;
        });
        $section2_result = reset($section2);
        $this->assertEquals('Activity 1 section 2',  $section2_result['title']);
        $section2_expected_relationships = [
            'id' => $data->activity1_section2_relationship1->id,
            'can_view' => true,
            'can_answer' => true,
            'relationship' => [
                'id' => $subject_id->get_id(),
                'name' => $subject_id->get_name(),
            ],
        ];
        $this->assertCount(1, $section2_result['section_relationships']);
        $this->assertEquals($section2_expected_relationships, reset($section2_result['section_relationships']));
    }

    /**
     * @covers ::resolve
     */
    public function test_failed_ajax_query(): void {
        $activity = $this->create_test_data()->activity1;
        $args = ['activity_id' => $activity->id];

        $feature = 'performance_activities';
        advanced_feature::disable($feature);
        $result = $this->parsed_graphql_operation(self::QUERY, $args);
        $this->assert_webapi_operation_failed($result, $feature);
        advanced_feature::enable($feature);

        $result = $this->parsed_graphql_operation(self::QUERY, []);
        $this->assert_webapi_operation_failed($result, 'activity_id');

        $result = $this->parsed_graphql_operation(self::QUERY, ['activity_id' => 0]);
        $this->assert_webapi_operation_failed($result, 'activity id');

        $id = 1293;
        $result = $this->parsed_graphql_operation(self::QUERY, ['activity_id' => $id]);
        $this->assert_webapi_operation_failed($result, "$id");
    }
}