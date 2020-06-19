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

use totara_webapi\phpunit\webapi_phpunit_helper;

require_once(__DIR__ . '/generator/activity_generator_configuration.php');
require_once(__DIR__ . '/webapi_resolver_mutation_update_track_schedule.php');

/**
 * @group perform
 */
class mod_perform_webapi_resolver_mutation_update_track_due_date_testcase
    extends mod_perform_webapi_resolver_mutation_update_track_schedule_base {

    use webapi_phpunit_helper;

    public function test_correct_track_is_disabled(): void {
        global $DB;

        $args = [
            'track_schedule' => [
                'track_id' => $this->track1_id,
                'schedule_is_open' => true,
                'schedule_is_fixed' => true,
                'schedule_fixed_from' => 222,
                'due_date_is_enabled' => false,
                'repeating_is_enabled' => false,
            ],
        ];

        $before_tracks = $DB->get_records('perform_track', [], 'id');
        self::assertCount(4, $before_tracks);
        unset($before_tracks[$this->track1_id]->updated_at);

        $result = $this->resolve_graphql_mutation(
            'mod_perform_update_track_schedule',
            $args
        );
        $result_track = $result['track'];

        // Verify the resulting graphql data.
        self::assertEquals($this->track1_id, $result_track->id);
        self::assertFalse($result_track->due_date_is_enabled);
        self::assertNull($result_track->due_date_is_fixed);
        self::assertNull($result_track->due_date_fixed);
        self::assertNull($result_track->due_date_relative_count);
        self::assertNull($result_track->due_date_relative_unit);

        // Manually make the changes that we expect to make.
        $affected_track = $before_tracks[$this->track1_id];
        $affected_track->schedule_is_open = 1;
        $affected_track->schedule_is_fixed = 1;
        $affected_track->schedule_fixed_from = 222;
        $affected_track->schedule_fixed_to = null;
        $affected_track->schedule_dynamic_count_from = null;
        $affected_track->schedule_dynamic_count_to = null;
        $affected_track->schedule_dynamic_unit = null;
        $affected_track->schedule_dynamic_direction = null;
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

    public function test_correct_track_is_set_to_fixed(): void {
        global $DB;

        $args = [
            'track_schedule' => [
                'track_id' => $this->track1_id,
                'schedule_is_open' => false,
                'schedule_is_fixed' => true,
                'schedule_fixed_from' => 222,
                'schedule_fixed_to' => 333,
                'due_date_is_enabled' => true,
                'due_date_is_fixed' => true,
                'due_date_fixed' => 444,
                'repeating_is_enabled' => false,
            ],
        ];

        $before_tracks = $DB->get_records('perform_track', [], 'id');
        self::assertCount(4, $before_tracks);
        unset($before_tracks[$this->track1_id]->updated_at);

        $result = $this->resolve_graphql_mutation(
            'mod_perform_update_track_schedule',
            $args
        );
        $result_track = $result['track'];

        // Verify the resulting graphql data.
        self::assertEquals($this->track1_id, $result_track->id);
        self::assertTrue($result_track->due_date_is_enabled);
        self::assertTrue($result_track->due_date_is_fixed);
        self::assertEquals(444, $result_track->due_date_fixed);
        self::assertNull($result_track->due_date_relative_count);
        self::assertNull($result_track->due_date_relative_unit);

        // Manually make the changes that we expect to make.
        $affected_track = $before_tracks[$this->track1_id];
        $affected_track->schedule_is_open = 0;
        $affected_track->schedule_is_fixed = 1;
        $affected_track->schedule_fixed_from = 222;
        $affected_track->schedule_fixed_to = 333;
        $affected_track->schedule_dynamic_count_from = null;
        $affected_track->schedule_dynamic_count_to = null;
        $affected_track->schedule_dynamic_unit = null;
        $affected_track->schedule_dynamic_direction = null;
        $affected_track->schedule_needs_sync = 1;
        $affected_track->due_date_is_enabled = 1;
        $affected_track->due_date_is_fixed = 1;
        $affected_track->due_date_fixed = 444;
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
        [, $dynamic_source_input] = $this->get_user_creation_date_dynamic_source();

        // To must be after or equal to from.
        $args = [
            'track_schedule' => [
                'track_id' => $this->track1_id,
                'schedule_is_open' => false,
                'schedule_is_fixed' => false,
                'schedule_dynamic_unit' => 'WEEK',
                'schedule_dynamic_direction' => 'AFTER',
                'schedule_dynamic_count_from' => 200,
                'schedule_dynamic_count_to' => 100,
                'schedule_dynamic_source' => $dynamic_source_input,
                'schedule_use_anniversary' => false,
                'due_date_is_enabled' => false,
                'repeating_is_enabled' => false,
            ],
        ];

        $this->expectException(coding_exception::class);
        $this->expectExceptionMessage('"count_from" must not be after "count_to" when dynamic schedule direction is "AFTER"');

        $this->resolve_graphql_mutation(
            'mod_perform_update_track_schedule',
            $args
        );
    }

}
