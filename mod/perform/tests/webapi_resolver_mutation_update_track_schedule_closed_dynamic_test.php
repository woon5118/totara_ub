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

use mod_perform\entities\activity\track as track_entity;
use mod_perform\models\activity\activity;
use mod_perform\models\activity\track;
use totara_webapi\phpunit\webapi_phpunit_helper;
use totara_core\advanced_feature;


/**
 * @group perform
 */
class mod_perform_webapi_resolver_mutation_update_track_schedule_closed_dynamic_testcase
    extends mod_perform_webapi_resolver_mutation_update_track_schedule_base {

    private const MUTATION = 'mod_perform_update_track_schedule';

    use webapi_phpunit_helper;

    public function test_correct_track_is_updated(): void {
        global $DB;

        self::setAdminUser();

        $configuration = mod_perform_activity_generator_configuration::new();
        $configuration->set_number_of_activities(2);
        $configuration->set_number_of_tracks_per_activity(2);

        /** @var mod_perform_generator $perform_generator */
        $perform_generator = $this->getDataGenerator()->get_plugin_generator('mod_perform');
        $activities = $perform_generator->create_full_activities($configuration);

        /** @var activity $activity1 */
        $activity1 = $activities->first();
        /** @var track $track1 */
        $track1 = $activity1->get_tracks()->first();

        $args = [
            'track_schedule' => [
                'track_id' => $track1->id,
                'schedule_is_open' => false,
                'schedule_is_fixed' => false,
                'schedule_dynamic_count_from' => 555,
                'schedule_dynamic_count_to' => 444,
                'schedule_dynamic_unit' => 'MONTH',
                'schedule_dynamic_direction' => 'BEFORE',
                'due_date_is_enabled' => false,
                'repeating_is_enabled' => false,
            ],
        ];

        $before_tracks = $DB->get_records('perform_track', [], 'id');
        self::assertCount(8, $before_tracks);
        unset($before_tracks[$track1->id]->updated_at);

        $result = $this->parsed_graphql_operation(self::MUTATION, $args);
        $this->assert_webapi_operation_successful($result);

        $result = $this->get_webapi_operation_data($result);
        $result_track = $result['track'];

        // Verify the resulting graphql data.
        self::assertEquals($track1->id, $result_track['id']);
        self::assertFalse($result_track['schedule_is_open']);
        self::assertFalse($result_track['schedule_is_fixed']);
        self::assertNull($result_track['schedule_fixed_from']);
        self::assertNull($result_track['schedule_fixed_to']);
        self::assertEquals(555, $result_track['schedule_dynamic_count_from']);
        self::assertEquals(444, $result_track['schedule_dynamic_count_to']);
        self::assertEquals('MONTH', $result_track['schedule_dynamic_unit']);
        self::assertEquals('BEFORE', $result_track['schedule_dynamic_direction']);

        // Manually make the changes that we expect to make.
        $affected_track = $before_tracks[$track1->id];
        $affected_track->schedule_is_open = 0;
        $affected_track->schedule_is_fixed = 0;
        $affected_track->schedule_fixed_from = null;
        $affected_track->schedule_fixed_to = null;
        $affected_track->schedule_dynamic_count_from = 555;
        $affected_track->schedule_dynamic_count_to = 444;
        $affected_track->schedule_dynamic_unit = track_entity::SCHEDULE_DYNAMIC_UNIT_MONTH;
        $affected_track->schedule_dynamic_direction = track_entity::SCHEDULE_DYNAMIC_DIRECTION_BEFORE;
        $affected_track->schedule_needs_sync = 1;
        $affected_track->due_date_is_enabled = 0;
        $affected_track->due_date_is_fixed = null;
        $affected_track->due_date_fixed = null;
        $affected_track->due_date_relative_count = null;
        $affected_track->due_date_relative_unit = null;
        $affected_track->repeating_is_enabled = 0;

        $after_tracks = $DB->get_records('perform_track', [], 'id');
        unset($after_tracks[$track1->id]->updated_at);

        self::assertEquals($after_tracks, $before_tracks);
    }

    public function test_with_validation_errors(): void {
        // To must be after or equal to from.
        $args = [
            'track_schedule' => [
                'track_id' => $this->track1_id,
                'schedule_is_open' => false,
                'schedule_is_fixed' => false,
                'schedule_dynamic_unit' => 'MONTH',
                'schedule_dynamic_direction' => 'AFTER',
                'schedule_dynamic_count_from' => 200,
                'schedule_dynamic_count_to' => 100,
                'due_date_is_enabled' => false,
                'repeating_is_enabled' => false,
            ],
        ];

        $result = $this->parsed_graphql_operation(self::MUTATION, $args);
        $this->assert_webapi_operation_failed(
            $result,
            '"count_from" must not be after "count_to" when dynamic schedule direction is "AFTER"'
        );
    }


    public function test_failed_ajax_query(): void {
        self::setAdminUser();

        $args = [
            'track_schedule' => [
                'track_id' => $this->track1_id,
                'schedule_is_open' => false,
                'schedule_is_fixed' => false,
                'schedule_dynamic_count_from' => 555,
                'schedule_dynamic_count_to' => 444,
                'schedule_dynamic_unit' => 'MONTH',
                'schedule_dynamic_direction' => 'BEFORE',
                'due_date_is_enabled' => false,
                'repeating_is_enabled' => false,
            ],
        ];

        // Fails when feature is disabled.
        $feature = 'performance_activities';
        advanced_feature::disable($feature);
        $result = $this->parsed_graphql_operation(self::MUTATION, $args);
        $this->assert_webapi_operation_failed($result, $feature);
        advanced_feature::enable($feature);

        // Fails when arguments are missing.
        $result = $this->parsed_graphql_operation(self::MUTATION, []);
        $this->assert_webapi_operation_failed($result, 'track_schedule');

        // Fails when id is 0.
        $args['track_schedule']['track_id'] = 0;
        $result = $this->parsed_graphql_operation(self::MUTATION, $args);
        $this->assert_webapi_operation_failed($result, 'track id');

        // Fails when id is not found.
        $track_id = 1293;
        $args['track_schedule']['track_id'] = $track_id;
        $result = $this->parsed_graphql_operation(self::MUTATION, $args);
        $this->assert_webapi_operation_failed($result, "$track_id");
    }
}
