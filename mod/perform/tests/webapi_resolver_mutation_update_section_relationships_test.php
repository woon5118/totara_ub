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
 * @author Matthias Bonk <matthias.bonk@totaralearning.com>
 * @package mod_perform
 */

use mod_perform\entities\activity\section as section_entity;
use mod_perform\models\activity\section;
use mod_perform\webapi\resolver\mutation\update_section_relationships;
use totara_core\relationship\resolvers\subject;
use totara_job\relationship\resolvers\appraiser;
use totara_job\relationship\resolvers\manager;
use totara_core\advanced_feature;
use totara_webapi\phpunit\webapi_phpunit_helper;

require_once(__DIR__.'/relationship_testcase.php');

/**
 * @coversDefaultClass update_section_relationships.
 *
 * @group perform
 */
class mod_perform_webapi_resolver_mutation_update_section_relationships_testcase extends mod_perform_relationship_testcase {
    private const MUTATION = 'mod_perform_update_section_relationships';

    use webapi_phpunit_helper;

    public function test_update_invalid_section_id() {
        $this->setAdminUser();
        $relationship_id = $this->perform_generator()->get_relationship(subject::class)->id;
        $non_existent_section_id = 1234;
        while (section_entity::repository()->where('id', $non_existent_section_id)->exists()) {
            $non_existent_section_id ++;
        }
        $this->expectException(coding_exception::class);
        $this->expectExceptionMessage('Specified section id does not exist');

        [$args, $context] = $this->create_args($non_existent_section_id, [$relationship_id]);
        update_section_relationships::resolve($args, $context);
    }

    public function test_update_missing_capability() {
        $this->setAdminUser();
        $perform_generator = $this->perform_generator();
        $relationship_id = $this->perform_generator()->get_relationship(subject::class)->id;
        $activity1 = $perform_generator->create_activity_in_container();
        /** @var section $section1 */
        $section1 = $perform_generator->create_section($activity1);

        $user1 = $this->getDataGenerator()->create_user();
        $this->setUser($user1);

        $this->expectException(required_capability_exception::class);
        $this->expectExceptionMessage('you do not currently have permissions to do that (Manage performance activities)');

        [$args, $context] = $this->create_args($section1->id, [$relationship_id]);
        update_section_relationships::resolve($args, $context);
    }

    public function test_update_successful() {
        self::setAdminUser();
        $perform_generator = $this->perform_generator();
        $subject_id = $perform_generator->get_relationship(subject::class)->id;
        $manager_id = $perform_generator->get_relationship(manager::class)->id;
        $appraiser_id = $perform_generator->get_relationship(appraiser::class)->id;
        $activity1 = $perform_generator->create_activity_in_container(['activity_name' => 'Activity 1']);
        $activity2 = $perform_generator->create_activity_in_container(['activity_name' => 'Activity 2']);
        /** @var section $section1 */
        $section1 = $perform_generator->create_section($activity1);
        $section2 = $perform_generator->create_section($activity1);
        $this->assert_section_relationships($section1, []);
        $this->assert_section_relationships($section2, []);

        // Add three relationships to section1.
        [$args, $context] = $this->create_args($section1->id, [$subject_id, $manager_id, $appraiser_id]);
        $result = update_section_relationships::resolve($args, $context);

        /** @var section $returned_section */
        $returned_section = $result['section'];
        $this->assertEquals($section1->id, $returned_section->id);
        $this->assert_section_relationships($section1, [subject::class, manager::class, appraiser::class]);
        $this->assert_section_relationships($section2, []);
        $this->assert_activity_relationships($activity1, [subject::class, manager::class, appraiser::class]);
        $this->assert_activity_relationships($activity2, []);

        // Remove all relationships.
        [$args, $context] = $this->create_args($section1->id, []);
        update_section_relationships::resolve($args, $context);

        $this->assert_section_relationships($section1, []);
        $this->assert_section_relationships($section2, []);
        $this->assert_activity_relationships($activity1, []);
        $this->assert_activity_relationships($activity2, []);
    }

    /**
     * Test the mutation through the GraphQL stack.
     */
    public function test_ajax_query_successful() {
        $data = $this->create_test_data();
        // Section without relationships.
        $section_id = $data->activity2_section2->id;
        $appraiser_id = $this->perform_generator()->get_relationship(appraiser::class)->id;

        [$args, ] = $this->create_args($section_id, [$appraiser_id]);
        $result = $this->parsed_graphql_operation(self::MUTATION, $args);
        $this->assert_webapi_operation_successful($result);

        $result = $this->get_webapi_operation_data($result);
        $this->assertNotEmpty($result, "no result");
        $this->assertEquals($section_id, $result['section']['id']);
    }

    public function test_failed_ajax_query(): void {
        $data = $this->create_test_data();
        $section_id = $data->activity2_section2->id;
        $appraiser_id = $this->perform_generator()->get_relationship(appraiser::class)->id;
        [$args, ] = $this->create_args($section_id, [$appraiser_id]);

        $feature = 'performance_activities';
        advanced_feature::disable($feature);
        $result = $this->parsed_graphql_operation(self::MUTATION, $args);
        $this->assert_webapi_operation_failed($result, $feature);
        advanced_feature::enable($feature);

        $this->setUser();
        $result = $this->parsed_graphql_operation(self::MUTATION, $args);
        $this->assert_webapi_operation_failed($result, 'not logged in');
    }

    private function create_args(int $section_id, array $relationship_ids): array {
        $args = [
            'input' => [
                'section_id' => $section_id,
                'relationship_ids' => $relationship_ids
            ]
        ];

        $context = $this->create_webapi_context(self::MUTATION);

        return [$args, $context];
    }
}