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
 * @author Murali Nair <murali.nair@totaralearning.com>
 * @package mod_perform
 * @category test
 */

use mod_perform\models\activity\track as track_model;
use mod_perform\models\activity\track_assignment as track_assignment_model;
use mod_perform\models\activity\track_status;
use mod_perform\webapi\resolver\query\track;
use totara_core\advanced_feature;
use totara_webapi\phpunit\webapi_phpunit_helper;

/**
 * @coversDefaultClass \mod_perform\webapi\resolver\query\track
 *
 * @group perform
 */
class mod_perform_webapi_query_track_testcase extends advanced_testcase {
    private const QUERY = 'mod_perform_track';

    use webapi_phpunit_helper;

    /**
     * @covers ::resolve
     */
    public function test_find(): void {
        [$track_id, $groups_by_id, $args, ] = $this->setup_env();
        $track = $this->resolve_graphql_query('mod_perform_track', $args);

        $this->assertEquals($track_id, $track->id, 'wrong track id');
        $this->assertEquals(track_status::ACTIVE, $track->status, 'wrong track status');

        $actual_assignments = $track->assignments;
        $this->assertEquals(
            count($groups_by_id),
            $actual_assignments->count(),
            'wrong retrieve count'
        );

        foreach ($actual_assignments->all() as $assignment) {
            $group = $assignment->group;
            $group_id = $group->get_id();
            $this->assertArrayHasKey($group_id, $groups_by_id, 'unknown track');

            $expected = $groups_by_id[$group_id];
            $this->assertEquals($track_id, $assignment->track_id, 'wrong track id');
            $this->assertEquals($expected->type, $assignment->type, 'wrong type');
            $this->assertEquals($expected->group->get_type(), $group->get_type(), 'wrong group');
        }
    }

    /**
     * @covers ::resolve
     */
    public function test_successful_ajax_call(): void {
        [$track_id, $groups_by_id, $args, $context] = $this->setup_env();

        $result = $this->parsed_graphql_operation(self::QUERY, $args);
        $this->assert_webapi_operation_successful($result);

        $track = $this->get_webapi_operation_data($result);
        $this->assertEquals($track_id, $track['id'], 'wrong track id');
        $this->assertEquals(track_status::ACTIVE, $track['status'], 'wrong track status');

        $actual_assignments = $track['assignments'];
        $this->assertCount(count($groups_by_id), $actual_assignments, 'wrong retrieve count');

        foreach ($actual_assignments as $assignment) {
            $group_id = $assignment['group']['id'] ?? null;
            $this->assertNotNull($group_id, 'no retrieved group id');
            $this->assertArrayHasKey($group_id, $groups_by_id, 'unknown assignment');

            $expected = $this->graphql_return($groups_by_id[$group_id], $context);
            $this->assertEquals($expected, $assignment, 'wrong graphql return');
        }
    }

    /**
     * @covers ::resolve
     */
    public function test_failed_ajax_query(): void {
        [, , $args, ] = $this->setup_env();

        $feature = 'performance_activities';
        advanced_feature::disable($feature);
        $result = $this->parsed_graphql_operation(self::QUERY, $args);
        $this->assert_webapi_operation_failed($result, 'Feature performance_activities is not available.');
        advanced_feature::enable($feature);

        $result = $this->parsed_graphql_operation(self::QUERY, []);
        $this->assert_webapi_operation_failed($result, 'Variable "$track_id" of required type "param_integer!" was not provided.');

        $result = $this->parsed_graphql_operation(self::QUERY, ['track_id' => 0]);
        $this->assert_webapi_operation_failed($result, 'Invalid parameter value detected (invalid track id)');

        $id = 1293;
        $result = $this->parsed_graphql_operation(self::QUERY, ['track_id' => $id]);
        $this->assert_webapi_operation_failed($result, "Invalid activity");

        self::setGuestUser();
        $result = $this->parsed_graphql_operation(self::QUERY, $args);
        $this->assert_webapi_operation_failed($result, 'Invalid activity');
    }

    /**
     * Generates test data.
     *
     * @return array (track, the generated assignments by group id, graphql query
     *         arguments, context) tuple.
     */
    private function setup_env(): array {
        $this->setAdminUser();

        $generator = $this->getDataGenerator()->get_plugin_generator('mod_perform');
        $activity = $generator->create_activity_in_container();
        $track = track_model::create($activity);

        $groups_by_id = $generator
            ->create_track_assignments($track)
            ->assignments
            ->reduce(
                function (array $mapping, track_assignment_model $assignment): array {
                    $mapping[$assignment->group->get_id()] = $assignment;
                    return $mapping;
                },
                []
            );

        $track_id = $track->id;
        $args = ['track_id' => $track_id];

        return [$track_id, $groups_by_id, $args, $track->activity->get_context()];
    }

    /**
     * Given the input assignment, returns data the graphql call is supposed to
     * return.
     *
     * @param track_assignment_model $track_assignment source assignment.
     * @param context $context context.
     *
     * @return array the expected graphql data values.
     */
    private function graphql_return(
        track_assignment_model $track_assignment,
        context $context
    ): array {
        $track_resolve = function (string $field) use ($track_assignment, $context) {
            return $this->resolve_graphql_type('mod_perform_track_assignment', $field, $track_assignment, [], $context);
        };

        $group = $track_assignment->group;
        $grouping_resolve = function (string $field) use ($group, $context) {
            return $this->resolve_graphql_type('mod_perform_user_grouping', $field, $group, [], $context);
        };

        return [
            'type' => $track_resolve('type'),
            'group' => [
                'id' => $grouping_resolve('id'),
                'type' => $grouping_resolve('type'),
                'type_label' => $grouping_resolve('type_label'),
                'name' => $grouping_resolve('name')
            ]
        ];
    }
}
