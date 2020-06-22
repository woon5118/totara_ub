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

use core\webapi\execution_context;

use mod_perform\models\activity\track;
use mod_perform\models\activity\track_assignment as track_assignment_model;
use mod_perform\models\activity\track_status;

use mod_perform\webapi\resolver\query\default_track;
use mod_perform\webapi\resolver\type\track_assignment;
use mod_perform\webapi\resolver\type\user_grouping;

use totara_core\advanced_feature;

use totara_webapi\phpunit\webapi_phpunit_helper;

/**
 * @coversDefaultClass track.
 *
 * @group perform
 */
class mod_perform_webapi_query_default_track_settings_testcase extends advanced_testcase {
    private const QUERY = 'mod_perform_default_track_settings';

    use webapi_phpunit_helper;

    /**
     * @covers ::resolve
     */
    public function test_find(): void {
        [$groups_by_id, $args, $context] = $this->setup_env();
        $track = default_track::resolve($args, $context);
        $this->assertEquals(track_status::ACTIVE, $track->status, 'wrong track status');

        $actual_assignments = $track->assignments;
        $this->assertEquals(
            count($groups_by_id),
            $actual_assignments->count(),
            'wrong count'
        );

        foreach ($actual_assignments as $assignment) {
            $group = $assignment->group;
            $group_id = $group->get_id();
            $this->assertArrayHasKey($group_id, $groups_by_id, 'unknown track');

            $expected = $groups_by_id[$group_id];
            $this->assertEquals($track->id, $assignment->track_id, 'wrong track id');
            $this->assertEquals($expected->type, $assignment->type, 'wrong type');
            $this->assertEquals($expected->group->get_type(), $group->get_type(), 'wrong group');
        }
    }

    /**
     * @covers ::resolve
     */
    public function test_successful_ajax_call(): void {
        [$groups_by_id, $args, $context] = $this->setup_env();

        $raw_result = $this->execute_graphql_operation(self::QUERY, $args);

        self::assertCount(0, $raw_result->errors);
        self::assertGreaterThan(0, count($raw_result->data['mod_perform_available_dynamic_date_sources']));

        $track = $raw_result->data['mod_perform_default_track'];
        $this->assertEquals(track_status::ACTIVE, $track['status'], 'wrong track status');

        $actual_assignments = $track['assignments'];
        $this->assertCount(count($groups_by_id), $actual_assignments, 'wrong count');

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
        [, $args, ] = $this->setup_env();

        $feature = 'performance_activities';
        advanced_feature::disable($feature);
        $raw_result = $this->execute_graphql_operation(self::QUERY, $args);
        self::assertCount(1, $raw_result->errors);
        self::assertEquals('Feature performance_activities is not available.', $raw_result->errors[0]->message);
        advanced_feature::enable($feature);


        $raw_result = $this->execute_graphql_operation(self::QUERY, []);
        self::assertCount(1, $raw_result->errors);
        self::assertEquals('Variable "$activity_id" of required type "param_integer!" was not provided.', $raw_result->errors[0]->message);


        $raw_result = $this->execute_graphql_operation(self::QUERY, ['activity_id' => 0]);
        self::assertCount(1, $raw_result->errors);
        self::assertEquals('Invalid parameter value detected (invalid activity id)', $raw_result->errors[0]->message);


        $id = 1293;
        $raw_result = $this->execute_graphql_operation(self::QUERY, ['activity_id' => $id]);
        self::assertCount(1, $raw_result->errors);
        self::assertEquals('Invalid activity', $raw_result->errors[0]->message);


        self::setGuestUser();
        $raw_result = $this->execute_graphql_operation(self::QUERY, $args);
        self::assertCount(1, $raw_result->errors);
        self::assertEquals('Course or activity not accessible. (Not enrolled)', $raw_result->errors[0]->message);
    }

    /**
     * Generates test data.
     *
     * @return array (default track assignments by group id, graphql query
     *         arguments, graphql execution context) tuple.
     */
    private function setup_env(): array {
        $this->setAdminUser();

        $generator = $this->getDataGenerator()->get_plugin_generator('mod_perform');
        $activity = $generator->create_activity_in_container(['create_track' => true]);
        $default_track = track::load_by_activity($activity)->first();

        $groups_by_id = $generator
            ->create_track_assignments($default_track, 1, 0, 0, 0)
            ->assignments
            ->reduce(
                function (array $mapping, track_assignment_model $assignment): array {
                    $mapping[$assignment->group->get_id()] = $assignment;
                    return $mapping;
                },
                []
            );

        $args = ['activity_id' => $activity->get_id()];

        $context = $this->create_webapi_context(self::QUERY);
        $context->set_relevant_context($activity->get_context());

        return [$groups_by_id, $args, $context];
    }

    /**
     * Given the input assignment, returns data the graphql call is supposed to
     * return.
     *
     * @param track_assignment_model $track_assignment source assignment.
     * @param execution_context $context graphql execution context.
     *
     * @return array the expected graphql data values.
     */
    private function graphql_return(
        track_assignment_model $track_assignment,
        execution_context $context
    ): array {
        $track_resolve = function (string $field) use ($track_assignment, $context) {
            return track_assignment::resolve($field, $track_assignment, [], $context);
        };

        $group = $track_assignment->group;
        $grouping_resolve = function (string $field) use ($group, $context) {
            return user_grouping::resolve($field, $group, [], $context);
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
