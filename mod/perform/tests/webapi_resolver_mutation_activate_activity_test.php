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
 * @author Fabian Derschatta <fabian.derschatta@totaralearning.com>
 * @package mod_perform
 * @group perform
 */

use core\webapi\execution_context;
use mod_perform\models\activity\activity as activity;
use mod_perform\state\activity\active;
use mod_perform\state\activity\draft;
use mod_perform\webapi\resolver\mutation\activate_activity;
use totara_job\relationship\resolvers\manager;
use totara_webapi\graphql;

class mod_perform_webapi_resolver_mutation_activate_activity_testcase extends advanced_testcase {

    private function get_execution_context(string $type = graphql::TYPE_AJAX, ?string $operation = null): execution_context {
        return execution_context::create($type, $operation);
    }

    public function test_activate_draft_activity(): void {
        $this->setAdminUser();

        $activity = $this->create_valid_activity();

        $args = ['input' => ['activity_id' => $activity->id]];

        /** @var activity $result */
        ['activity' => $result] = activate_activity::resolve($args, $this->get_execution_context());
        $this->assertEquals($activity->id, $result->id);
        $this->assertEquals(active::get_code(), $result->status);
    }

    public function test_activate_draft_activity_which_does_not_satisfy_conditions(): void {
        $this->setAdminUser();

        $activity = $this->create_activity();

        $args = ['input' => ['activity_id' => $activity->id]];

        $this->expectException(moodle_exception::class);
        $this->expectExceptionMessage('Cannot activate this activity due to invalid state or conditions are not satisfied.');

        activate_activity::resolve($args, $this->get_execution_context());
    }

    public function test_activate_active_activity(): void {
        $this->setAdminUser();

        $activity = $this->create_valid_activity(active::get_code());

        $args = ['input' => ['activity_id' => $activity->id]];

        /** @var activity $result */
        ['activity' => $result] = activate_activity::resolve($args, $this->get_execution_context());
        $this->assertEquals($activity->id, $result->id);
        $this->assertEquals(active::get_code(), $result->status);
    }

    public function test_activate_activity_without_capability(): void {
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();

        $this->setUser($user1);

        $activity = $this->create_valid_activity();

        $this->setUser($user2);

        $this->expectException(moodle_exception::class);
        $this->expectExceptionMessage('Invalid activity');

        $args = ['input' => ['activity_id' => $activity->id]];

        activate_activity::resolve($args, $this->get_execution_context());
    }

    public function test_activate_nonexisting_activity(): void {
        $this->setAdminUser();

        $this->expectException(moodle_exception::class);
        $this->expectExceptionMessage('Invalid activity');

        $args = ['input' => ['activity_id' => 999]];

        activate_activity::resolve($args, $this->get_execution_context());
    }

    /**
     * Test the mutation through the GraphQL stack.
     */
    public function test_execute_query_successful_on_draft_activity() {
        $this->setAdminUser();

        $activity = $this->create_valid_activity();

        $args = ['activity_id' => $activity->id];

        $result = graphql::execute_operation(
            $this->get_execution_context(graphql::TYPE_AJAX, 'mod_perform_activate_activity'),
            ['input' => $args]
        );

        $this->assertEquals([], $result->errors);
        $this->assertArrayHasKey('mod_perform_activate_activity', $result->data);
        $result = $result->data['mod_perform_activate_activity'];
        $this->assertArrayHasKey('activity', $result);
        $activity_result = $result['activity'];
        $this->assertEquals($activity->id, $activity_result['id']);
        $this->assertEquals($activity->name, $activity_result['name']);
        $this->assertEquals(active::get_code(), $activity_result['state_details']['code']);
    }

    /**
     * Test the mutation through the GraphQL stack.
     */
    public function test_execute_query_successful_on_active_activity() {
        $this->setAdminUser();

        $activity = $this->create_valid_activity(active::get_code());

        $args = ['activity_id' => $activity->id];

        $result = graphql::execute_operation(
            $this->get_execution_context(graphql::TYPE_AJAX, 'mod_perform_activate_activity'),
            ['input' => $args]
        );

        $this->assertEquals([], $result->errors);
        $this->assertArrayHasKey('mod_perform_activate_activity', $result->data);
        $result = $result->data['mod_perform_activate_activity'];
        $this->assertArrayHasKey('activity', $result);
        $activity_result = $result['activity'];
        $this->assertEquals($activity->id, $activity_result['id']);
        $this->assertEquals($activity->name, $activity_result['name']);
        $this->assertEquals(active::get_code(), $activity_result['state_details']['code']);
    }

    /**
     * Create a basic activity without any sections or questions in it
     *
     * @param int|null $status defaults to draft
     * @return activity
     */
    protected function create_activity(int $status = null): activity {
        $data_generator = $this->getDataGenerator();
        /** @var mod_perform_generator $perform_generator */
        $perform_generator = $data_generator->get_plugin_generator('mod_perform');

        return $perform_generator->create_activity_in_container([
            'activity_name' => 'User1 One',
            'activity_status' => $status ?? draft::get_code()
        ]);
    }

    /**
     * Creates an activity with one section, one question and one relationship
     *
     * @param int|null $status defaults to draft
     * @return activity
     */
    protected function create_valid_activity(int $status = null): activity {
        $data_generator = $this->getDataGenerator();
        /** @var mod_perform_generator $perform_generator */
        $perform_generator = $data_generator->get_plugin_generator('mod_perform');

        $activity = $this->create_activity($status);

        $section = $perform_generator->create_section($activity, ['title' => 'Test section 1']);

        $perform_generator->create_section_relationship(
            $section,
            ['class_name' => manager::class]
        );

        $element = $perform_generator->create_element(['title' => 'Question one']);
        $perform_generator->create_section_element($section, $element);

        $perform_generator->create_single_activity_track_and_assignment($activity);

        return $activity;
    }

}