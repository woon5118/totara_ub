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
use totara_webapi\graphql;

class mod_perform_webapi_resolver_mutation_activate_activity_testcase extends advanced_testcase {

    private function get_execution_context(string $type = graphql::TYPE_AJAX, ?string $operation = null): execution_context {
        return execution_context::create($type, $operation);
    }

    public function test_activate_draft_activity(): void {
        $this->setAdminUser();

        $activity = $this->create_activity(draft::get_code());

        $args = ['input' => ['activity_id' => $activity->id]];

        /** @type activity $result */
        ['activity' => $result] = activate_activity::resolve($args, $this->get_execution_context());
        $this->assertEquals($activity->id, $result->id);
        $this->assertEquals(active::get_code(), $result->status);
    }

    public function test_activate_active_activity(): void {
        $this->setAdminUser();

        $activity = $this->create_activity(active::get_code());

        $args = ['input' => ['activity_id' => $activity->id]];

        /** @type activity $result */
        ['activity' => $result] = activate_activity::resolve($args, $this->get_execution_context());
        $this->assertEquals($activity->id, $result->id);
        $this->assertEquals(active::get_code(), $result->status);
    }

    public function test_activate_activity_without_capability(): void {
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();

        $this->setUser($user1);

        $activity = $this->create_activity(active::get_code());

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

        $activity = $this->create_activity(draft::get_code());

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
        $this->assertEquals('ACTIVE', $activity_result['status_name']);
    }

    /**
     * Test the mutation through the GraphQL stack.
     */
    public function test_execute_query_successful_on_active_activity() {
        $this->setAdminUser();

        $activity = $this->create_activity(active::get_code());

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
        $this->assertEquals('ACTIVE', $activity_result['status_name']);
    }

    /**
     * @param int $status a status constant coming from the activity state classes
     * @return activity
     */
    private function create_activity(int $status): activity {
        // TODO With TL-24784 we need to add at least one valid question and one valid assignment to make this test pass

        $data = [
            'activity_status' => $status
        ];

        /** @var mod_perform_generator $perform_generator */
        $perform_generator = self::getDataGenerator()->get_plugin_generator('mod_perform');
        return $perform_generator->create_activity_in_container($data);
    }

}