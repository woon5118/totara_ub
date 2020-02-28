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

use mod_perform\models\activity\activity;
use mod_perform\models\activity\track;
use mod_perform\models\activity\track_status;

use mod_perform\webapi\resolver\mutation\create_track;

use totara_webapi\graphql;

/**
 * @coversDefaultClass create_track.
 *
 * @group perform
 */
class mod_perform_webapi_mutation_create_track_testcase extends advanced_testcase {
    /**
     * @covers ::resolve
     */
    public function test_create_track(): void {
        $activity = $this->setup_env();
        $activity_id = $activity->get_id();

        // Note: a default track is created upon activity creation.
        $tracks = track::load_by_activity($activity);
        $this->assertEquals(1, $tracks->count(), 'wrong existing track count');

        $desc = 'my activity track description';
        $args = [
            'details' => [
                'activity_id' => $activity_id,
                'description' => $desc
            ]
        ];
        $context = $this->get_webapi_context();

        $track = create_track::resolve($args, $context);
        $this->assertNotNull($track, 'track creation failed');
        $this->assertEquals($activity_id, $track->activity_id, 'wrong track parent');
        $this->assertEquals($desc, $track->description, 'wrong track parent');
        $this->assertEquals(track_status::ACTIVE, $track->status, 'wrong track status');
        $this->assertEmpty($track->assignments->all(), 'wrong track assignments');

        $tracks = track::load_by_activity($activity);
        $this->assertEquals(2, $tracks->count(), 'track does not exist for activity');
    }

    /**
     * @covers ::resolve
     */
    public function test_successful_ajax_call(): void {
        $activity = $this->setup_env();
        $activity_id = $activity->get_id();

        // Note: a default track is created upon activity creation.
        $tracks = track::load_by_activity($activity);
        $this->assertEquals(1, $tracks->count(), 'wrong existing track count');

        $args = [
            'details' => ['activity_id' => $activity_id]
        ];
        $context = $this->get_webapi_context();

        $track = $this->exec_graphql($context, $args);
        $this->assertNotEmpty($track, 'track creation failed');
        $this->assertEmpty($track['description'], 'wrong track parent');
        $this->assertEquals(track_status::ACTIVE, $track['status'], 'wrong track status');
        $this->assertEmpty($track['assignments'], 'wrong track assignments');

        $tracks = track::load_by_activity($activity);
        $this->assertEquals(2, $tracks->count(), 'track does not exist for activity');
    }

    /**
     * @covers ::resolve
     */
    public function test_failed_ajax_call(): void {
        $activity = $this->setup_env();

        // Invalid user.
        self::setGuestUser();
        $args = [
            'details' => ['activity_id' => $activity->get_id()]
        ];
        $context = $this->get_webapi_context();

        $actual = $this->exec_graphql($context, $args);
        $this->assertIsString($actual, 'wrong type');
        $this->assertStringContainsString('not accessible', $actual, 'wrong error');

        // No input.
        $actual = $this->exec_graphql($context, []);
        $this->assertIsString($actual, 'wrong type');
        $this->assertStringContainsString('details', $actual, 'wrong error');

        // Input with activity id set to 0.
        $args = [
            'details' => ['activity_id' => 0]
        ];
        $actual = $this->exec_graphql($context, $args);
        $this->assertIsString($actual, 'wrong type');
        $this->assertStringContainsString('activity id', $actual, 'wrong error');

        // Input with unknown activity id.
        $activity_id = 1293;
        $args = [
            'details' => ['activity_id' => $activity_id]
        ];
        $actual = $this->exec_graphql($context, $args);
        $this->assertIsString($actual, 'wrong type');
        $this->assertStringContainsString("$activity_id", $actual, 'wrong error');
    }

    /**
     * Generates test data.
     *
     * @return int the created activity.
     */
    private function setup_env(): activity {
        $this->setAdminUser();

        return $this->getDataGenerator()->get_plugin_generator('mod_perform')
            ->create_activity_in_container();
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
        return execution_context::create('ajax', 'mod_perform_create_track');
    }
}
