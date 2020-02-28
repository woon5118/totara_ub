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
use mod_perform\models\activity\track_assignment;
use mod_perform\models\activity\track_assignment_type;

use mod_perform\webapi\resolver\mutation\remove_track_assignments;

use totara_webapi\graphql;

/**
 * @coversDefaultClass remove_track_assignments.
 *
 * @group perform
 */
class mod_perform_webapi_mutation_remove_track_assignments_testcase extends advanced_testcase {
    /**
     * @covers ::resolve
     */
    public function test_remove_track_assignments(): void {
        [$track, $groups] = $this->setup_env();
        $assignments = $track->assignments;
        $this->assertNotEmpty($assignments->all(), 'track has no assignments');

        $args = [
            'assignments' => [
                'track_id' => $track->id,
                'type' => track_assignment_type::ADMIN,
                'groups' => $groups
            ]
        ];

        $context = $this->get_webapi_context();
        $updated_track = remove_track_assignments::resolve($args, $context);
        $this->assertNotNull($updated_track, 'track assignment failed');
        $this->assertInstanceOf(track::class, $updated_track, 'wrong return type');
        $this->assertEquals($track->id, $updated_track->id, 'wrong track returned');
        $this->assertEquals($track->status, $updated_track->status, 'wrong track status');
        $this->assertEquals(0, $updated_track->assignments->count(), 'assignments not removed');
    }

    /**
     * @covers ::resolve
     */
    public function test_successful_ajax_call(): void {
        [$track, $groups] = $this->setup_env();
        $assignments = $track->assignments;
        $this->assertNotEmpty($assignments->all(), 'track has no assignments');

        $args = [
            'assignments' => [
                'track_id' => $track->id,
                'type' => track_assignment_type::ADMIN,
                'groups' => $groups
            ]
        ];

        $context = $this->get_webapi_context();
        $updated_track = $this->exec_graphql($context, $args);
        $this->assertNotNull($updated_track, 'track creation failed');
        $this->assertEquals($track->id, $updated_track['id'], 'wrong track returned');
        $this->assertEquals($track->status, $updated_track['status'], 'wrong track status');
        $this->assertEmpty($updated_track['assignments'], 'assignments not removed');
    }

    /**
     * @covers ::resolve
     */
    public function test_failed_ajax_call(): void {
        [$track, $groups] = $this->setup_env();

        // Invalid user.
        self::setGuestUser();

        $args = [
            'assignments' => [
                'track_id' => $track->id,
                'type' => track_assignment_type::ADMIN,
                'groups' => $groups
            ]
        ];

        $context = $this->get_webapi_context();
        $actual = $this->exec_graphql($context, $args);
        $this->assertIsString($actual, 'wrong type');
        $this->assertStringContainsString('not accessible', $actual, 'wrong error');

        // No input.
        $actual = $this->exec_graphql($context, []);
        $this->assertIsString($actual, 'wrong type');
        $this->assertStringContainsString('assignments', $actual, 'wrong error');

        // Input with track id set to 0.
        $args = [
            'assignments' => [
                'track_id' => 0,
                'type' => track_assignment_type::ADMIN,
                'groups' => $groups
            ]
        ];
        $actual = $this->exec_graphql($context, $args);
        $this->assertIsString($actual, 'wrong type');
        $this->assertStringContainsString('track id', $actual, 'wrong error');

        // Input with unknown track id.
        $track_id = 1293;
        $args = [
            'assignments' => [
                'track_id' => $track_id,
                'type' => track_assignment_type::ADMIN,
                'groups' => $groups
            ]
        ];
        $actual = $this->exec_graphql($context, $args);
        $this->assertIsString($actual, 'wrong type');
        $this->assertStringContainsString("$track_id", $actual, 'wrong error');
    }

    /**
     * Generates test data.
     *
     * @return array a (generated track, groupings) tuple.
     */
    private function setup_env(): array {
        $this->setAdminUser();
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_perform');
        $activity = $generator->create_activity_in_container();

        $track = $generator->create_activity_tracks($activity, 1)->first();
        $updated_track = $generator->create_track_assignments($track, 2, 2, 2, 2);

        $groups = $track->assignments->reduce(
            function (array $groups, track_assignment $assignment): array {
                $group = $assignment->group;
                $groups[] = [
                    'id' => $group->get_id(),
                    'type' => $group->get_type()
                ];

                return $groups;
            },
            []
        );

        return [$updated_track, $groups];
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
        return execution_context::create('ajax', 'mod_perform_remove_track_assignments');
    }
}
