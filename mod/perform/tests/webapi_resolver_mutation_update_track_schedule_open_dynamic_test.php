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
 * @author Nathan Lewis <nathan.lewis@totaralearning.com>
 * @package mod_perform
 * @category test
 */

use mod_perform\entities\activity\track as track_entity;
use mod_perform\models\activity\activity;
use mod_perform\models\activity\track;
use totara_webapi\phpunit\webapi_phpunit_helper;

require_once(__DIR__ . '/generator/activity_generator_configuration.php');
require_once(__DIR__ . '/webapi_resolver_mutation_update_track_schedule.php');

/**
 * @group perform
 */
class mod_perform_webapi_resolver_mutation_update_track_schedule_open_dynamic_testcase
    extends mod_perform_webapi_resolver_mutation_update_track_schedule_base {

    private const MUTATION = 'mod_perform_update_track_schedule';

    use webapi_phpunit_helper;

    public function test_correct_track_is_updated(): void {
        global $DB;

        $args = [
            'track_schedule' => [
                'track_id' => $this->track1_id,
                'schedule_is_open' => true,
                'schedule_is_fixed' => false,
                'schedule_dynamic_count_from' => 555,
                'schedule_dynamic_unit' => 'WEEK',
                'schedule_dynamic_direction' => 'BEFORE',
                'due_date_is_enabled' => false,
                'repeating_is_enabled' => false,
            ],
        ];

        $before_tracks = $DB->get_records('perform_track', [], 'id');
        self::assertCount(4, $before_tracks);
        unset($before_tracks[$this->track1_id]->updated_at);

        $result = $this->parsed_graphql_operation(self::MUTATION, $args);
        $this->assert_webapi_operation_successful($result);

        $result = $this->get_webapi_operation_data($result);
        $result_track = $result['track'];

        // Verify the resulting graphql data.
        self::assertEquals($this->track1_id, $result_track['id']);
        self::assertTrue($result_track['schedule_is_open']);
        self::assertFalse($result_track['schedule_is_fixed']);
        self::assertNull($result_track['schedule_fixed_from']);
        self::assertNull($result_track['schedule_fixed_to']);
        self::assertEquals(555, $result_track['schedule_dynamic_count_from']);
        self::assertNull($result_track['schedule_dynamic_count_to']);
        self::assertEquals('WEEK', $result_track['schedule_dynamic_unit']);
        self::assertEquals('BEFORE', $result_track['schedule_dynamic_direction']);

        // Manually make the changes that we expect to make.
        $affected_track = $before_tracks[$this->track1_id];
        $affected_track->schedule_is_open = 1;
        $affected_track->schedule_is_fixed = 0;
        $affected_track->schedule_fixed_from = null;
        $affected_track->schedule_fixed_to = null;
        $affected_track->schedule_dynamic_count_from = 555;
        $affected_track->schedule_dynamic_count_to = null;
        $affected_track->schedule_dynamic_unit = track_entity::SCHEDULE_DYNAMIC_UNIT_WEEK;
        $affected_track->schedule_dynamic_direction = track_entity::SCHEDULE_DYNAMIC_DIRECTION_BEFORE;
        $affected_track->schedule_needs_sync = 1;
        $affected_track->due_date_is_enabled = 0;
        $affected_track->due_date_is_fixed = null;
        $affected_track->due_date_fixed = null;
        $affected_track->due_date_relative_count = null;
        $affected_track->due_date_relative_unit = null;
        $affected_track->repeating_is_enabled = 0;
        $affected_track->repeating_relative_type = null;
        $affected_track->repeating_relative_count = null;
        $affected_track->repeating_relative_unit = null;
        $affected_track->repeating_is_limited = 0;
        $affected_track->repeating_limit = null;

        $after_tracks = $DB->get_records('perform_track', [], 'id');
        unset($after_tracks[$this->track1_id]->updated_at);
        self::assertEquals($before_tracks, $after_tracks);
    }

    public function test_with_validation_errors(): void {
        // To must be after or equal to from.
        $args = [
            'track_schedule' => [
                'track_id' => $this->track1_id,
                'schedule_is_open' => true,
                'schedule_is_fixed' => false,
                'schedule_dynamic_unit' => 'WEEK',
                'schedule_dynamic_direction' => 'AFTER',
                'schedule_dynamic_count_from' => -234,
                'due_date_is_enabled' => false,
                'repeating_is_enabled' => false,
            ],
        ];

        $this->expectException(coding_exception::class);
        $this->expectExceptionMessage('Count from must be a positive integer');

        $this->resolve_graphql_mutation(
            'mod_perform_update_track_schedule',
            $args
        );
    }
}
