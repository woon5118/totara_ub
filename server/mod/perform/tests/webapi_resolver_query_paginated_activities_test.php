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
 * @author Kunle Odusan <kunle.odusan@totaralearning.com>
 * @package mod_perform
 */

use totara_core\advanced_feature;
use totara_webapi\phpunit\webapi_phpunit_helper;

/**
 * @coversDefaultClass \mod_perform\webapi\resolver\query\paginated_activities
 *
 * @group perform
 */
class mod_perform_webapi_resolver_query_paginated_activities_testcase extends advanced_testcase {
    private const QUERY = 'mod_perform_paginated_activities';

    use webapi_phpunit_helper;

    public function test_get_paginated_activities() {
        $names = ['Mid year performance', 'End year performance'];
        $this->create_test_data($names);

        $query_params = $this->get_query_options(null, 1);

        /** @var array $activities */
        $result = $this->resolve_graphql_query(self::QUERY, $query_params);
        $page_1_activities = array_column($result['items'], 'name');

        $this->assertCount(1, $page_1_activities);
        $this->assertEquals(count($names), $result['total']);
        $this->assertNotNull($result['next_cursor']);
        $this->assertEquals($names[0], $page_1_activities[0]);

        //page 2.
        $query_params = $this->get_query_options($result['next_cursor'], 5);
        $result = $this->resolve_graphql_query(self::QUERY, $query_params);
        $page_2_activities = array_column($result['items'], 'name');

        $this->assertCount(1, $page_2_activities);
        $this->assertEquals(count($names), $result['total']);
        $this->assertEmpty($result['next_cursor']);
        $this->assertEquals($names[1], $page_2_activities[0]);
    }

    public function test_user_needs_view_manage_activities_capability() {
        global $DB;

        $user1 = $this->getDataGenerator()->create_user();
        $this->setUser($user1);

        $user_role = $DB->get_record('role', ['shortname' => 'user']);
        assign_capability('mod/perform:view_manage_activities', CAP_ALLOW, $user_role->id, SYSCONTEXTID, true);

        /** @var array $activities */
        $activities = $this->resolve_graphql_query(self::QUERY, $this->get_query_options(null));

        // There are not activities but also no exceptions
        $this->assertEmpty($activities['items']);

        unassign_capability('mod/perform:view_manage_activities', $user_role->id, SYSCONTEXTID);
        $result = $this->parsed_graphql_operation(
            self::QUERY,
            [
                'query_options_input' => $this->get_query_options()['query_options']
            ]
        );
        $this->assert_webapi_operation_failed($result, '(Access the performance activities management interface)');
    }

    public function test_users_can_view_reports_or_manage_other_users_activities() {
        global $DB;

        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $this->setUser($user1);

        $names = ['Mid year performance', 'End year performance'];
        $this->create_test_data($names);

        $user_role = $DB->get_record('role', ['shortname' => 'user']);
        assign_capability('mod/perform:view_manage_activities', CAP_ALLOW, $user_role->id, SYSCONTEXTID, true);

        /** @var array $activities */
        $activities = $this->resolve_graphql_query(self::QUERY, $this->get_query_options());

        $this->assertCount(count($names), $activities['items']);
        // Users who created activities can always manage and view reporting
        foreach ($activities['items'] as $activity) {
            $this->assertTrue($activity->can_manage());
            $this->assertTrue($activity->can_view_participation_reporting());
        }

        $this->setUser($user2);

        $activities = $this->resolve_graphql_query(self::QUERY, $this->get_query_options());
        $this->assertEmpty($activities['items']);

        // Let the other user manage only
        assign_capability('mod/perform:manage_activity', CAP_ALLOW, $user_role->id, SYSCONTEXTID, true);

        $activities = $this->resolve_graphql_query(self::QUERY, $this->get_query_options());

        foreach ($activities['items'] as $activity) {
            $this->assertTrue($activity->can_manage());
            $this->assertFalse($activity->can_view_participation_reporting());
        }

        // Let the user also see view the reports
        assign_capability('mod/perform:view_participation_reporting', CAP_ALLOW, $user_role->id, SYSCONTEXTID, true);

        $activities = $this->resolve_graphql_query(self::QUERY, $this->get_query_options());

        foreach ($activities['items'] as $activity) {
            $this->assertTrue($activity->can_manage());
            $this->assertTrue($activity->can_view_participation_reporting());
        }

        // Let the user just view reports
        unassign_capability('mod/perform:manage_activity', $user_role->id, SYSCONTEXTID);

        foreach ($activities['items'] as $activity) {
            $this->assertFalse($activity->can_manage());
            $this->assertTrue($activity->can_view_participation_reporting());
        }
    }

    public function test_get_empty_activities() {
        $this->setAdminUser();

        $activities = $this->resolve_graphql_query(self::QUERY, $this->get_query_options());

        $this->assertCount(0, $activities['items']);
    }

    public function test_get_activities_non_admin() {
        $this->setGuestUser();

        $this->expectException(required_capability_exception::class);

        $this->resolve_graphql_query(self::QUERY, $this->get_query_options());
    }

    public function test_ajax_query() {
        $names = ['Mid year performance', 'End year performance'];
        $this->create_test_data($names);

        $result = $this->parsed_graphql_operation(self::QUERY,
            [
                'query_options_input' => $this->get_query_options()['query_options']
            ]
        );
        $this->assert_webapi_operation_successful($result);

        $activities = $this->get_webapi_operation_data($result);
        $this->assertCount(count($names), $activities['items'], 'wrong count');
        $this->assertEmpty($activities['next_cursor']);
        $this->assertEquals(count($names), $activities['total']);
    }

    public function test_failed_ajax_query(): void {
        $feature = 'performance_activities';
        advanced_feature::disable($feature);
        $result = $this->parsed_graphql_operation(self::QUERY,
            [
                'query_options_input' => $this->get_query_options()['query_options']
            ]
        );
        $this->assert_webapi_operation_failed($result, 'Feature performance_activities is not available.');
        advanced_feature::enable($feature);
    }

    private function get_query_options($cursor = null, $limit = 10): array {
        return [
            'query_options' => [
                'pagination' => [
                    'limit' => $limit,
                    'cursor' => $cursor,
                ],
            ]
        ];
    }

    private function create_test_data(array $activity_names) {
        $this->setAdminUser();

        /** @var mod_perform_generator $perform_generator */
        $perform_generator = $this->getDataGenerator()->get_plugin_generator('mod_perform');
        foreach ($activity_names as $name) {
            $perform_generator->create_activity_in_container(['activity_name' => $name]);
        }
    }
}
