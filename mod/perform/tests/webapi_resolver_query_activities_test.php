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
use mod_perform\webapi\resolver\query\activities;
use totara_webapi\graphql;
use totara_webapi\phpunit\webapi_phpunit_helper;

class mod_perform_webapi_resolver_query_activities_testcase extends advanced_testcase {

    use webapi_phpunit_helper;

    /**
     * Helper to get execution context
     *
     * @param string $type
     * @param string|null $operation
     * @return execution_context
     */
    private function get_execution_context(string $type = 'dev', ?string $operation = null) {
        return execution_context::create($type, $operation);
    }

    public function test_get_activities() {
        $this->setAdminUser();
        $this->create_test_data();

        /** @var mod_perform\models\activity\activity[] $activities */
        $activities = activities::resolve([], $this->get_execution_context());

        $this->assertCount(2, $activities);
        $this->assertEqualsCanonicalizing(
            ['Mid year performance', 'End year performance'],
            [$activities[0]->name, $activities[1]->name]
        );
    }

    public function test_user_needs_view_manage_activities_capability() {
        global $DB;

        $user1 = $this->getDataGenerator()->create_user();
        $this->setUser($user1);
        // $this->create_test_data();

        $user_role = $DB->get_record('role', ['shortname' => 'user']);
        assign_capability('mod/perform:view_manage_activities', CAP_ALLOW, $user_role->id, SYSCONTEXTID, true);

        /** @var mod_perform\models\activity\activity[] $activities */
        $activities = activities::resolve([], $this->get_execution_context());

        // There are not activities but also no exceptions
        $this->assertEmpty($activities);

        unassign_capability('mod/perform:view_manage_activities', $user_role->id, SYSCONTEXTID);

        $this->expectException(required_capability_exception::class);
        $this->expectExceptionMessage('(Access the performance activities management interface)');

        /** @var mod_perform\models\activity\activity[] $activities */
        $this->resolve_graphql_query('mod_perform_activities');
    }

    public function test_users_can_view_reports_or_manage_other_users_activities() {
        global $DB;

        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $this->setUser($user1);
        $this->create_test_data();

        $user_role = $DB->get_record('role', ['shortname' => 'user']);
        assign_capability('mod/perform:view_manage_activities', CAP_ALLOW, $user_role->id, SYSCONTEXTID, true);

        /** @var mod_perform\models\activity\activity[] $activities */
        $activities = $this->resolve_graphql_query('mod_perform_activities');

        $this->assertCount(2, $activities);
        // Users who created activities can always manage and view reporting
        foreach ($activities as $activity) {
            $this->assertTrue($activity->can_manage());
            $this->assertTrue($activity->can_view_participation_reporting());
        }

        $this->setUser($user2);

        $activities = $this->resolve_graphql_query('mod_perform_activities');
        $this->assertEmpty($activities);

        // Let the other user manage only
        assign_capability('mod/perform:manage_activity', CAP_ALLOW, $user_role->id, SYSCONTEXTID, true);

        $activities = $this->resolve_graphql_query('mod_perform_activities');

        foreach ($activities as $activity) {
            $this->assertTrue($activity->can_manage());
            $this->assertFalse($activity->can_view_participation_reporting());
        }

        // Let the user also see view the reports
        assign_capability('mod/perform:view_participation_reporting', CAP_ALLOW, $user_role->id, SYSCONTEXTID, true);

        $activities = $this->resolve_graphql_query('mod_perform_activities');

        foreach ($activities as $activity) {
            $this->assertTrue($activity->can_manage());
            $this->assertTrue($activity->can_view_participation_reporting());
        }

        // Let the user just view reports
        unassign_capability('mod/perform:manage_activity', $user_role->id, SYSCONTEXTID);

        foreach ($activities as $activity) {
            $this->assertFalse($activity->can_manage());
            $this->assertTrue($activity->can_view_participation_reporting());
        }
    }

    public function test_get_empty_activities() {
        $this->setAdminUser();

        $activities = activities::resolve([], $this->get_execution_context());

        $this->assertCount(0, $activities);
    }

    /**
     * @expectedException moodle_exception
     */
    public function test_get_activities_non_admin() {
        $this->setGuestUser();

        activities::resolve([], $this->get_execution_context());
    }

    public function test_ajax_query() {
        $this->setAdminUser();
        $this->create_test_data();
        $result = graphql::execute_operation(
            execution_context::create('ajax', 'mod_perform_activities'),
            []
        );
        $this->assertCount(2, $result->data['mod_perform_activities']);
    }

    private function create_test_data() {
        /** @var mod_perform_generator $perform_generator */
        $perform_generator = $this->getDataGenerator()->get_plugin_generator('mod_perform');

        $perform_generator->create_activity_in_container(['activity_name' => 'Mid year performance']);
        $perform_generator->create_activity_in_container(['activity_name' => 'End year performance']);
    }
}