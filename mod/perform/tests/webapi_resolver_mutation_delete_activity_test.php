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
 * @author Jaron Steenson <jaron.steenson@totaralearning.com>
 * @package mod_perform
 */

use core\webapi\execution_context;
use mod_perform\models\activity\activity;
use mod_perform\entities\activity\activity as activity_entity;
use mod_perform\webapi\resolver\mutation\delete_activity;
use totara_webapi\graphql;

/**
 * Class mod_perform_webapi_resolver_mutation_delete_activity_testcase
 *
 * @group perform
 */
class mod_perform_webapi_resolver_mutation_delete_activity_testcase extends advanced_testcase {

    private function get_execution_context(string $type = graphql::TYPE_AJAX, ?string $operation = null): execution_context {
        return execution_context::create($type, $operation);
    }

    public function test_activate_delete_activity(): void {
        self::setAdminUser();

        $activity = $this->create_activity();
        self::assertTrue($this->container_course_exists($activity->course));

        $args = ['input' => ['activity_id' => $activity->id]];
        $result = delete_activity::resolve($args, $this->get_execution_context());

        $this->assertTrue($result);
        self::assertNull(activity_entity::repository()->find($activity->id));
        self::assertFalse($this->container_course_exists($activity->course));
    }

    public function test_delete_activity_without_capability(): void {
        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();

        self::setUser($user1);

        $activity = $this->create_activity();
        self::assertTrue($this->container_course_exists($activity->course));

        self::setUser($user2);

        $this->expectException(moodle_exception::class);
        $this->expectExceptionMessage('Invalid activity');

        $args = ['input' => ['activity_id' => $activity->id]];

        delete_activity::resolve($args, $this->get_execution_context());
    }

    public function test_delete_nonexisting_activity(): void {
        self::setAdminUser();

        $this->expectException(moodle_exception::class);
        $this->expectExceptionMessage('Invalid activity');

        $args = ['input' => ['activity_id' => 999]];

        delete_activity::resolve($args, $this->get_execution_context());
    }

    /**
     * Test the mutation through the GraphQL stack.
     */
    public function test_execute_query_successful(): void {
        self::setAdminUser();

        $activity = $this->create_activity();
        self::assertTrue($this->container_course_exists($activity->course));

        $args = ['activity_id' => $activity->id];

        $result = graphql::execute_operation(
            $this->get_execution_context(graphql::TYPE_AJAX, 'mod_perform_delete_activity'),
            ['input' => $args]
        );

        $this->assertTrue($result->data['mod_perform_delete_activity']);
        self::assertNull(activity_entity::repository()->find($activity->id));
        self::assertFalse($this->container_course_exists($activity->course));
    }

    /**
     * @return activity
     */
    private function create_activity(): activity {
        /** @var mod_perform_generator $perform_generator */
        $perform_generator = self::getDataGenerator()->get_plugin_generator('mod_perform');
        return $perform_generator->create_activity_in_container();
    }

    private function container_course_exists(int $course_id): bool {
        global $DB;
        return $DB->record_exists('course', ['id' => $course_id]);
    }

}
