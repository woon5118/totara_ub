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

/**
 * @group perform
 */

use mod_perform\entities\activity\section as section_entity;
use mod_perform\webapi\resolver\mutation\update_section_relationships;
use core\webapi\execution_context;
use totara_webapi\graphql;
use mod_perform\models\activity\section;

require_once(__DIR__.'/relationship_testcase.php');

class webapi_resolver_mutation_update_section_relationships_testcase extends mod_perform_relationship_testcase {

    private function get_execution_context() {
        return execution_context::create('ajax', 'mod_perform_update_section_relationships');
    }

    public function test_update_invalid_section_id() {
        $this->setAdminUser();
        $non_existent_section_id = 1234;
        while (section_entity::repository()->where('id', $non_existent_section_id)->exists()) {
            $non_existent_section_id ++;
        }
        $this->expectException(coding_exception::class);
        $this->expectExceptionMessage('Specified section id does not exist');

        $args = [
            'section_id' => $non_existent_section_id,
            'names' => ['appraiser', 'manager', 'subject'],
        ];
        update_section_relationships::resolve(['input' => $args], $this->get_execution_context());
    }

    public function test_update_missing_capability() {
        $this->setAdminUser();
        $perform_generator = $this->perform_generator();
        $activity1 = $perform_generator->create_activity_in_container();
        /** @var section $section1 */
        $section1 = $perform_generator->create_section($activity1);

        $user1 = $this->getDataGenerator()->create_user();
        $this->setUser($user1);

        $this->expectException(required_capability_exception::class);
        $this->expectExceptionMessage('you do not currently have permissions to do that (Manage performance activities)');

        $args = [
            'section_id' => $section1->id,
            'names' => ['appraiser', 'manager', 'subject'],
        ];
        update_section_relationships::resolve(['input' => $args], $this->get_execution_context());
    }

    public function test_update_successful() {
        self::setAdminUser();
        $perform_generator = $this->perform_generator();
        $activity1 = $perform_generator->create_activity_in_container(['activity_name' => 'Activity 1']);
        $activity2 = $perform_generator->create_activity_in_container(['activity_name' => 'Activity 2']);
        /** @var section $section1 */
        $section1 = $perform_generator->create_section($activity1);
        $section2 = $perform_generator->create_section($activity1);
        $this->assert_section_relationships($section1, []);
        $this->assert_section_relationships($section2, []);

        // Add three relationships to section1.
        $args = [
            'section_id' => $section1->id,
            'names' => ['appraiser', 'manager', 'subject'],
        ];
        $result = update_section_relationships::resolve(['input' => $args], $this->get_execution_context());
        /** @var section $returned_section */
        $returned_section = $result['section'];
        $this->assertEquals($section1->id, $returned_section->id);
        $this->assert_section_relationships($section1, ['appraiser', 'manager', 'subject']);
        $this->assert_section_relationships($section2, []);
        $this->assert_activity_relationships($activity1, ['appraiser', 'manager', 'subject']);
        $this->assert_activity_relationships($activity2, []);

        // Remove all relationships.
        $args = [
            'section_id' => $section1->id,
            'names' => [],
        ];
        update_section_relationships::resolve(['input' => $args], $this->get_execution_context());
        $this->assert_section_relationships($section1, []);
        $this->assert_section_relationships($section2, []);
        $this->assert_activity_relationships($activity1, []);
        $this->assert_activity_relationships($activity2, []);
    }

    /**
     * Test the mutation through the GraphQL stack.
     */
    public function test_ajax_query_successful() {
        $this->setAdminUser();
        $data = $this->create_test_data();
        // Section without relationships.
        $section_id = $data->activity2_section2->id;

        $args = [
            'section_id' => $section_id,
            'names' => ["appraiser"],
        ];

        $result = graphql::execute_operation(
            $this->get_execution_context(),
            ['input' => $args]
        );
        $this->assertEquals([], $result->errors);
        $this->assertEquals($section_id, $result->data['mod_perform_update_section_relationships']['section']['id']);
    }
}