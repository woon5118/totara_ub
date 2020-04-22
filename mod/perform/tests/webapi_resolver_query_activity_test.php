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
use mod_perform\models\activity\activity;
use mod_perform\webapi\resolver\query\activity as activity_resolver;
use totara_core\relationship\resolvers\subject;
use totara_job\relationship\resolvers\appraiser;
use totara_job\relationship\resolvers\manager;
use totara_webapi\graphql;

require_once(__DIR__.'/relationship_testcase.php');

class mod_perform_webapi_resolver_query_activity_testcase extends mod_perform_relationship_testcase {

    public function test_get_activity(): void {
        self::setAdminUser();
        $created_activity = $this->create_activity();
        $id = $created_activity->id;

        $returned_activity = activity_resolver::resolve(['activity_id' => $id], $this->get_execution_context());

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

        self::setUser($user1);
        $created_activity = $this->create_activity();
        $id = $created_activity->id;

        // Returns the activity for the user that created it
        $returned_activity = activity_resolver::resolve(['activity_id' => $id], $this->get_execution_context());
        $this->assertEquals($id, $returned_activity->id);
        $this->assertEquals($created_activity->name, $returned_activity->name);

        self::setUser($user2);
        $this->expectException(moodle_exception::class);
        activity_resolver::resolve(['activity_id' => $id], $this->get_execution_context());
    }

    public function test_get_activity_non_admin(): void {
        $this->expectException(moodle_exception::class);
        self::setGuestUser();

        $created_activity = $this->create_activity();
        $id = $created_activity->id;

        activity_resolver::resolve(['activity_id' => $id], $this->get_execution_context());
    }

    /**
     * Test the query through the GraphQL stack.
     */
    public function test_ajax_query_successful() {
        self::setAdminUser();
        $data = $this->create_test_data();
        $appraiser_id = $this->perform_generator()->get_relationship(appraiser::class);
        $manager_id = $this->perform_generator()->get_relationship(manager::class);
        $subject_id = $this->perform_generator()->get_relationship(subject::class);

        $id = $data->activity1->id;
        $args = [
            'activity_id' => $id,
        ];

        $result = graphql::execute_operation(
            $this->get_execution_context('ajax', 'mod_perform_activity'),
            $args
        );
        $this->assertEquals([], $result->errors);

        // Assert result structure.
        $result = $result->data['mod_perform_activity'];
        $this->assertEquals($data->activity1->id, $result['id']);
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