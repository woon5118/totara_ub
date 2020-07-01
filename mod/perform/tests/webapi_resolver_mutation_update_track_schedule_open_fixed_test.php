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


require_once(__DIR__ . '/generator/activity_generator_configuration.php');
require_once(__DIR__ . '/webapi_resolver_mutation_update_track_schedule.php');

use totara_webapi\phpunit\webapi_phpunit_helper;
use totara_core\advanced_feature;
use mod_perform\entities\activity\track as track_entity;

/**
 * @group perform
 */
class mod_perform_webapi_resolver_mutation_update_track_schedule_open_fixed_testcase
    extends mod_perform_webapi_resolver_mutation_update_track_schedule_base {

    private const MUTATION = 'mod_perform_update_track_schedule';

    use webapi_phpunit_helper;

    public function test_correct_track_is_updated(): void {
        global $DB;

        $args = [
            'track_schedule' => [
                'track_id' => $this->track1_id,
                'schedule_is_open' => true,
                'schedule_is_fixed' => true,
                'schedule_fixed_from' => [
                    'iso' => '1991-12-04',
                    'timezone' => 'Pacific/Auckland',
                ],
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
        self::assertTrue($result_track['schedule_is_fixed']);
        self::assertEquals([
            'iso' => '1991-12-04T00:00:00',
            'timezone' => 'Pacific/Auckland',
        ], $result_track['schedule_fixed_from']);
        self::assertNull($result_track['schedule_fixed_to']);
        self::assertNull($result_track['schedule_dynamic_from']);
        self::assertNull($result_track['schedule_dynamic_to']);

        // Manually make the changes that we expect to make.
        /** @var track_entity $affected_track */
        $affected_track = $before_tracks[$this->track1_id];
        $affected_track->schedule_is_open = 1;
        $affected_track->schedule_is_fixed = 1;
        $affected_track->schedule_fixed_from = $this->get_timestamp_from_date('1991-12-04', 'Pacific/Auckland');
        $affected_track->schedule_fixed_to = null;
        $affected_track->schedule_fixed_timezone = 'Pacific/Auckland';
        $affected_track->schedule_dynamic_from = null;
        $affected_track->schedule_dynamic_to = null;
        $affected_track->schedule_needs_sync = 1;
        $affected_track->due_date_is_enabled = 0;
        $affected_track->due_date_is_fixed = null;
        $affected_track->due_date_fixed = null;
        $affected_track->due_date_offset = null;
        $affected_track->repeating_is_enabled = 0;
        $affected_track->repeating_type = null;
        $affected_track->repeating_offset = null;
        $affected_track->repeating_is_limited = 0;
        $affected_track->repeating_limit = null;

        $after_tracks = $DB->get_records('perform_track', [], 'id');
        unset($after_tracks[$this->track1_id]->updated_at);
        self::assertEquals($before_tracks, $after_tracks);
    }

    public function test_with_validation_errors(): void {
        // None currently, but we will have when additional fields are added.
    }

    public function test_failed_ajax_call(): void {
        $args = [
            'track_schedule' => [
                'track_id' => $this->track1_id,
                'schedule_is_open' => true,
                'schedule_is_fixed' => true,
                'schedule_fixed_from' => [
                    'iso' => '1991-12-04',
                    'timezone' => 'Pacific/Auckland',
                ],
                'due_date_is_enabled' => false,
                'repeating_is_enabled' => false,
            ],
        ];

        $result = $this->parsed_graphql_operation(self::MUTATION, $args);
        $this->assert_webapi_operation_successful($result);

        $feature = 'performance_activities';
        advanced_feature::disable($feature);
        $result = $this->parsed_graphql_operation(self::MUTATION, $args);
        $this->assert_webapi_operation_failed($result, 'Feature performance_activities is not available.');
        advanced_feature::enable($feature);

        $result = $this->parsed_graphql_operation(self::MUTATION, []);
        $this->assert_webapi_operation_failed($result, 'Variable "$track_schedule" of required type "mod_perform_track_schedule_input!" was not provided.');

        $args['track_schedule']['track_id'] = 0;
        $result = $this->parsed_graphql_operation(self::MUTATION, $args);
        $this->assert_webapi_operation_failed($result, 'Invalid parameter value detected (invalid track id)');

        $track_id = 1293;
        $args['track_schedule']['track_id'] = $track_id;
        $result = $this->parsed_graphql_operation(self::MUTATION, $args);
        $this->assert_webapi_operation_failed($result, "Invalid activity");
    }
}
