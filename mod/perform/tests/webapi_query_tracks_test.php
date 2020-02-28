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

use mod_perform\models\activity\track as track_model;

use mod_perform\webapi\resolver\query\tracks;
use mod_perform\webapi\resolver\type\track;

use totara_webapi\graphql;

/**
 * @coversDefaultClass tracks.
 *
 * @group perform
 */
class mod_perform_webapi_query_tracks_testcase extends advanced_testcase {
    /**
     * @covers ::resolve
     */
    public function test_find(): void {
        [$activity_id, $tracks_by_id] = $this->setup_env();

        $context = $this->get_webapi_context();
        $args = ['activity_id' => $activity_id];
        $actual_tracks = tracks::resolve($args, $context);

        // Need to account for default track; hence the +1.
        $this->assertCount(count($tracks_by_id) + 1, $actual_tracks, 'wrong retrieve count');

        $checked_tracks = 0;
        foreach ($actual_tracks as $track) {
            $expected = $tracks_by_id[$track->id] ?? null;
            if (!$expected) {
                continue;
            }

            $this->assertEquals($activity_id, $expected->activity_id, 'wrong activity');
            $this->assertEquals($expected->status, $track->status, 'wrong status');
            $this->assertEmpty($track->assignments->all(), 'wrong assignments');

            $checked_tracks++;
        }

        $this->assertEquals(count($tracks_by_id), $checked_tracks, 'wrong check count');
    }


    /**
     * @covers ::resolve
     */
    public function test_successful_ajax_call(): void {
        [$activity_id, $tracks_by_id] = $this->setup_env();

        $context = $this->get_webapi_context();
        $args = ['activity_id' => $activity_id];
        $actual_tracks = $this->exec_graphql($context, $args);

        // Need to account for default track; hence the +1.
        $this->assertCount(count($tracks_by_id) + 1, $actual_tracks, 'wrong retrieve count');

        $checked_tracks = 0;
        foreach ($actual_tracks as $actual) {
            $track_id = $actual['id'] ?? null;
            $this->assertNotNull($track_id, 'no retrieved track id');

            $track = $tracks_by_id[$track_id] ?? null;
            if ($track) {
                $expected = $this->graphql_return($track, $context);
                $this->assertEquals($expected, $actual, 'wrong graphql return');

                $checked_tracks++;
            }
        }

        $this->assertEquals(count($tracks_by_id), $checked_tracks, 'wrong check count');
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
     * @param int $no_of_tracks no of tracks to generate.
     *
     * @return array (activity id, the generated tracks by ids] tuple.
     */
    private function setup_env(int $no_of_tracks=10): array {
        $this->setAdminUser();
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_perform');
        $activity = $generator->create_activity_in_container();

        $tracks_by_id = $generator
            ->create_activity_tracks($activity, $no_of_tracks)
            ->reduce(
                function (array $map, track_model $track): array {
                    $map[$track->id] = $track;
                    return $map;
                },
                []
            );

        return [$activity->get_id(), $tracks_by_id];
    }

    /**
     * Given the input track, returns data the graphql call is supposed to
     * return.
     *
     * @param track_model $track source track.
     * @param execution_context $context graphql execution context.
     *
     * @return array the expected graphql data values.
     */
    private function graphql_return(track_model $track, execution_context $context): array {
        $resolve = function (string $field) use ($track, $context) {
            return track::resolve($field, $track, [], $context);
        };

        return [
            'id' => $resolve('id'),
            'description' => $resolve('description'),
            'status' => $resolve('status'),
            'assignments' => $resolve('assignments')
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

        return array_values($result['data'][$op]);
    }

    /**
     * Creates an graphql execution context.
     *
     * @return execution_context the context.
     */
    private function get_webapi_context(): execution_context {
        return execution_context::create('ajax', 'mod_perform_tracks');
    }
}
