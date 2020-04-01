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

defined('MOODLE_INTERNAL') || die();

use core\webapi\execution_context;

use mod_perform\models\activity\track;
use mod_perform\models\activity\track_assignment as track_assignment_model;
use mod_perform\models\activity\track_status;

use mod_perform\webapi\resolver\query\default_track;
use mod_perform\webapi\resolver\type\track_assignment;
use mod_perform\webapi\resolver\type\user_grouping;

use totara_webapi\graphql;

/**
 * @coversDefaultClass track.
 *
 * @group perform
 */
class mod_perform_webapi_query_default_track_testcase extends advanced_testcase {
    /**
     * @covers ::resolve
     */
    public function test_find(): void {
        [$activity_id, $groups_by_id] = $this->setup_env();

        $context = $this->get_webapi_context();
        $args = ['activity_id' => $activity_id];
        $track = default_track::resolve($args, $context);
        $this->assertEquals(track_status::ACTIVE, $track->status, 'wrong track status');

        $actual_assignments = $track->assignments;
        $this->assertEquals(
            count($groups_by_id),
            $actual_assignments->count(),
            'wrong count'
        );

        foreach ($actual_assignments->all() as $assignment) {
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
        [$activity_id, $groups_by_id] = $this->setup_env();

        $context = $this->get_webapi_context();
        $args = ['activity_id' => $activity_id];
        $track = $this->exec_graphql($context, $args);
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
        [$activity_id, ] = $this->setup_env(1);

        // Invalid user.
        self::setGuestUser();
        $context = $this->get_webapi_context();
        $actual = $this->exec_graphql($context, ['activity_id' => $activity_id]);
        $this->assertIsString($actual, 'wrong type');
        $this->assertStringContainsString('not accessible', $actual, 'wrong error');

        // No input.
        $actual = $this->exec_graphql($context, []);
        $this->assertIsString($actual, 'wrong type');
        $this->assertStringContainsString('activity_id', $actual, 'wrong error');

        // Input with activity id set to 0.
        $args = ['activity_id' => 0];
        $actual = $this->exec_graphql($context, $args);
        $this->assertIsString($actual, 'wrong type');
        $this->assertStringContainsString('activity id', $actual, 'wrong error');

        // Input with unknown activity id.
        $activity_id = 1293;
        $args = ['activity_id' => $activity_id];
        $actual = $this->exec_graphql($context, $args);
        $this->assertIsString($actual, 'wrong type');
        $this->assertStringContainsString("$activity_id", $actual, 'wrong error');
    }

    /**
     * Generates test data.
     *
     * @return array (generated activity id, default track assignments by group
     *         id) tuple.
     */
    private function setup_env(): array {
        $this->setAdminUser();

        $generator = $this->getDataGenerator()->get_plugin_generator('mod_perform');
        $activity = $generator->create_activity_in_container();
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

        return [$activity->get_id(), $groups_by_id];
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

    /**
     * Executes the test query via AJAX.
     *
     * @param execution_context $context graphql execution context.
     * @param array $args ajax arguments if any.
     *
     * @return array|string either the retrieved items or the error string for
     *         failures.
     */
    private function exec_graphql(execution_context $context, array $args=[]) {
        $result = graphql::execute_operation($context, $args)->toArray(true);

        $op = $context->get_operationname();
        $errors = $result['errors'] ?? null;
        if ($errors) {
            $error = $errors[0];
            $msg = $error['debugMessage'] ?? $error['message'];

            return sprintf(
                "invocation of %s://%s failed: %s",
                $context->get_type(),
                $op,
                $msg
            );
        }

        return $result['data'][$op];
    }

    /**
     * Creates an graphql execution context.
     *
     * @return execution_context the context.
     */
    private function get_webapi_context(): execution_context {
        return execution_context::create('ajax', 'mod_perform_default_track');
    }
}
