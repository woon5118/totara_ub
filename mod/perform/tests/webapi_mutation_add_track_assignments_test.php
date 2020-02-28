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
use mod_perform\models\activity\track_assignment_type;

use mod_perform\webapi\resolver\mutation\add_track_assignments;

use mod_perform\user_groups\grouping;

use totara_webapi\graphql;

/**
 * @coversDefaultClass add_track_assignments.
 *
 * @group perform
 */
class mod_perform_webapi_mutation_add_track_assignments_testcase extends advanced_testcase {
    /**
     * @covers ::resolve
     */
    public function test_add_track_assignments(): void {
        [$track, $cohort, $org, $pos, $user] = $this->setup_env();
        $this->assertEmpty($track->assignments->all(), 'track has assignments');

        $assignment_type = track_assignment_type::ADMIN;
        $args = [
            'assignments' => [
                'track_id' => $track->id,
                'type' => $assignment_type,
                'groups' => [$cohort, $org, $pos, $user]
            ]
        ];

        $context = $this->get_webapi_context();
        $updated_track = add_track_assignments::resolve($args, $context);
        $this->assertNotNull($updated_track, 'track assignment failed');
        $this->assertInstanceOf(track::class, $updated_track, 'wrong return type');
        $this->assertEquals($track->id, $updated_track->id, 'wrong track returned');
        $this->assertEquals($track->status, $updated_track->status, 'wrong track status');

        $expected = [];
        foreach ($args['assignments']['groups'] as $group) {
            $expected[$group['id']] = $group['type'];
        }

        $updated_assignments = $updated_track->assignments;
        $this->assertEquals(
            count($expected),
            $updated_assignments->count(),
            'track has no assignments'
        );

        foreach ($updated_assignments->all() as $assignment) {
            $this->assertEquals($track->id, $assignment->track_id, 'wrong track id');
            $this->assertEquals($assignment_type, $assignment->type, 'wrong track type');

            $group = $assignment->group;
            $group_id = $group->get_id();
            $expected_group_type = $expected[$group_id] ?? null;

            $this->assertNotNull($expected_group_type, "unknown group id '$group_id'");
            $this->assertEquals($expected_group_type, $group->get_type(), 'wrong group type');
        }
    }

    /**
     * @covers ::resolve
     */
    public function test_successful_ajax_call(): void {
        [$track, $cohort, $org, $pos, $user] = $this->setup_env();
        $this->assertEmpty($track->assignments->all(), 'track has assignments');

        $assignment_type = track_assignment_type::ADMIN;
        $args = [
            'assignments' => [
                'track_id' => $track->id,
                'type' => $assignment_type,
                'groups' => [$cohort, $org, $pos, $user]
            ]
        ];

        $context = $this->get_webapi_context();
        $updated_track = $this->exec_graphql($context, $args);
        $this->assertNotNull($updated_track, 'track creation failed');
        $this->assertEquals($track->id, $updated_track['id'], 'wrong track returned');
        $this->assertEquals($track->status, $updated_track['status'], 'wrong track status');

        $expected = [];
        foreach ($args['assignments']['groups'] as $group) {
            $expected[$group['id']] = $group['type'];
        }

        $updated_assignments = $updated_track['assignments'];
        $this->assertCount(count($expected), $updated_assignments, 'track has no assignments');

        foreach ($updated_assignments as $assignment) {
            $this->assertEquals($assignment_type, $assignment['type'], 'wrong track type');

            $group = $assignment['group'];
            $group_id = $group['id'];
            $expected_group_type = $expected[$group_id] ?? null;

            $this->assertNotNull($expected_group_type, "unknown group id '$group_id'");
            $this->assertEquals($expected_group_type, $group['type'], 'wrong group type');
        }
    }

    /**
     * @covers ::resolve
     */
    public function test_failed_ajax_call(): void {
        [$track, $cohort, $org, $pos, $user] = $this->setup_env();
        $assignment_type = track_assignment_type::ADMIN;

        // Invalid user.
        self::setGuestUser();
        $args = [
            'assignments' => [
                'track_id' => $track->id,
                'type' => $assignment_type,
                'groups' => [$cohort, $org, $pos, $user]
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
                'type' => $assignment_type,
                'groups' => [$cohort, $org, $pos, $user]
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
                'type' => $assignment_type,
                'groups' => [$cohort, $org, $pos, $user]
            ]
        ];
        $actual = $this->exec_graphql($context, $args);
        $this->assertIsString($actual, 'wrong type');
        $this->assertStringContainsString("$track_id", $actual, 'wrong error');
    }

    /**
     * Generates test data.
     *
     * @return array [generated track, [cohort id, cohort type], [org id, org
     *         type], [pos id, pos type], [user id, user type]] tuple.
     */
    private function setup_env(): array {
        $this->setAdminUser();
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_perform');
        $activity = $generator->create_activity_in_container();
        $track = $generator->create_activity_tracks($activity, 1)->first();

        $cohort = $this->create_grouping(grouping::COHORT);
        $org = $this->create_grouping(grouping::ORG);
        $pos = $this->create_grouping(grouping::POS);
        $user = $this->create_grouping(grouping::USER);

        return [$track, $cohort, $org, $pos, $user];
    }

    /**
     * Generates a test grouping.
     *
     * @param string $type grouping type. One of the grouping enums.
     *
     * @return array (id, type) tuple.
     */
    private function create_grouping(?string $type=null): array {
        $generator = $this->getDataGenerator();
        $hierarchies = $generator->get_plugin_generator('totara_hierarchy');

        $grouping = null;
        switch ($type) {
            case grouping::COHORT:
                $cohort = $generator->create_cohort([
                    'name' => 'My testing cohort'
                ])->id;

                $grouping = grouping::cohort($cohort);
                break;

            case grouping::ORG:
                $org = $hierarchies->create_org([
                    'frameworkid' => $hierarchies->create_org_frame([])->id,
                    'shortname' => 'My short org name',
                    'fullname' => 'My really long org name'
                ])->id;

                $grouping = grouping::org($org);
                break;

            case grouping::POS:
                $pos = $hierarchies->create_pos([
                    'frameworkid' => $hierarchies->create_pos_frame([])->id,
                    'shortname' => 'My short pos name',
                    'fullname' => 'My really long pos name'
                ])->id;

                $grouping = grouping::pos($pos);
                break;

            default:
                $user = $generator->create_user([
                    'firstname' => 'Tester',
                    'middlename' => 'Number',
                    'lastname' => 'Two'
                ])->id;

                $grouping = grouping::user($user);
        }

        return ['id' => $grouping->get_id(), 'type' => $grouping->get_type()];
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
        return execution_context::create('ajax', 'mod_perform_add_track_assignments');
    }
}
