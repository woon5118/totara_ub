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

use mod_perform\dates\date_offset;
use mod_perform\dates\resolvers\dynamic\dynamic_source;
use mod_perform\entities\activity\track as track_entity;
use totara_core\advanced_feature;
use totara_webapi\phpunit\webapi_phpunit_helper;

/**
 * @group perform
 */
class mod_perform_webapi_resolver_mutation_update_track_schedule_closed_dynamic_testcase
    extends mod_perform_webapi_resolver_mutation_update_track_schedule_base {

    private const MUTATION = 'mod_perform_update_track_schedule';

    use webapi_phpunit_helper;

    public function test_correct_track_is_updated(): void {
        global $DB;

        /** @var $date_dynamic_source dynamic_source */
        [$date_dynamic_source, $dynamic_source_input] = $this->get_user_creation_date_dynamic_source();

        $from = [
            'count' => 555,
            'unit' => date_offset::UNIT_WEEK,
            'direction' => date_offset::DIRECTION_BEFORE,
        ];
        $to = [
            'count' => 444,
            'unit' => date_offset::UNIT_WEEK,
            'direction' => date_offset::DIRECTION_BEFORE,
        ];

        $args = [
            'track_schedule' => [
                'track_id' => $this->track1_id,
                'schedule_is_open' => false,
                'schedule_is_fixed' => false,
                'schedule_dynamic_from' => $from,
                'schedule_dynamic_to' => $to,
                'schedule_dynamic_source' => $dynamic_source_input,
                'schedule_use_anniversary' => true,
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
        self::assertFalse($result_track['schedule_is_open']);
        self::assertFalse($result_track['schedule_is_fixed']);
        self::assertTrue($result_track['schedule_use_anniversary']);
        self::assertNull($result_track['schedule_fixed_from']);
        self::assertNull($result_track['schedule_fixed_to']);
        self::assertEquals($from, $result_track['schedule_dynamic_from']);
        self::assertEquals($to, $result_track['schedule_dynamic_to']);
        self::assertEquals($date_dynamic_source->jsonSerialize(), $result_track['schedule_dynamic_source']);

        // Manually make the changes that we expect to make.
        $affected_track = $before_tracks[$this->track1_id];
        $affected_track->schedule_is_open = 0;
        $affected_track->schedule_is_fixed = 0;
        $affected_track->schedule_fixed_from = null;
        $affected_track->schedule_fixed_to = null;
        $affected_track->schedule_dynamic_from = json_encode($from);
        $affected_track->schedule_dynamic_to = json_encode($to);
        $affected_track->schedule_dynamic_source = json_encode($date_dynamic_source);
        $affected_track->schedule_use_anniversary = 1;
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
        [, $resolver_selection] = $this->get_user_creation_date_dynamic_source();

        // To must be after or equal to from.
        $args = [
            'track_schedule' => [
                'track_id' => $this->track1_id,
                'schedule_is_open' => false,
                'schedule_is_fixed' => false,
                'schedule_dynamic_from' => [
                    'count' => 200,
                    'unit' => date_offset::UNIT_WEEK,
                    'direction' => date_offset::DIRECTION_AFTER
                ],
                'schedule_dynamic_to' => [
                    'count' => 100,
                    'unit' => date_offset::UNIT_WEEK,
                    'direction' => date_offset::DIRECTION_AFTER
                ],
                'schedule_dynamic_source' => $resolver_selection,
                'schedule_use_anniversary' => true,
                'due_date_is_enabled' => false,
                'repeating_is_enabled' => false,
            ],
        ];

        $result = $this->parsed_graphql_operation(self::MUTATION, $args);
        $this->assert_webapi_operation_failed(
            $result,
            '"from" must not be after "to"'
        );
    }

    /**
     * @dataProvider date_resolver_validation_errors_provider
     * @param array $resolver_selection
     * @param string $expected_exception_message
     * @throws coding_exception
     * @throws required_capability_exception
     */
    public function test_with_date_resolver_validation_errors(array $resolver_selection, string $expected_exception_message): void {
        $args = [
            'track_schedule' => [
                'track_id' => $this->track1_id,
                'schedule_is_open' => false,
                'schedule_is_fixed' => false,
                'schedule_dynamic_from' => [
                    'count' => 100,
                    'unit' => date_offset::UNIT_WEEK,
                    'direction' => date_offset::DIRECTION_AFTER,
                ],
                'schedule_dynamic_to' => [
                    'count' => 200,
                    'unit' => date_offset::UNIT_WEEK,
                    'direction' => date_offset::DIRECTION_AFTER,
                ],
                'schedule_dynamic_source' => $resolver_selection,
                'schedule_use_anniversary' => true,
                'due_date_is_enabled' => false,
                'repeating_is_enabled' => false,
            ],
        ];

        $context = $this->create_webapi_context(self::MUTATION);

        $this->expectException(coding_exception::class);
        $this->expectExceptionMessage($expected_exception_message);

        $this->resolve_graphql_mutation(self::MUTATION, $args);
    }

    public function date_resolver_validation_errors_provider(): array {
        /* @var $date_dynamic_source dynamic_source */
        [$date_dynamic_source, ] = $this->get_user_creation_date_dynamic_source();

        return [
            'Invalid resolver class name' => [
                [
                    'resolver_class_name' => \DateTime::class, // Valid class but invalid subclass.
                    'option_key' => $date_dynamic_source->get_option_key(),
                ],
                'Source is not available'
            ],
            'Invalid resolver option key' => [
                [
                    'resolver_class_name' => $date_dynamic_source->get_resolver_class_name(),
                    'option_key' => 'rubbish key'
                ],
                'Source is not available'
            ],
        ];
    }


    public function test_failed_ajax_query(): void {
        self::setAdminUser();

        [, $dynamic_source_input] = $this->get_user_creation_date_dynamic_source();

        $args = [
            'track_schedule' => [
                'track_id' => $this->track1_id,
                'schedule_is_open' => false,
                'schedule_is_fixed' => false,
                'schedule_dynamic_from' => [
                    'count' => 555,
                    'unit' => date_offset::UNIT_WEEK,
                    'direction' => date_offset::DIRECTION_BEFORE,
                ],
                'schedule_dynamic_to' => [
                    'count' => 444,
                    'unit' => date_offset::UNIT_WEEK,
                    'direction' => date_offset::DIRECTION_BEFORE,
                ],
                'schedule_dynamic_source' => $dynamic_source_input,
                'schedule_use_anniversary' => true,
                'due_date_is_enabled' => false,
                'repeating_is_enabled' => false,
            ],
        ];

        // Fails when feature is disabled.
        $feature = 'performance_activities';
        advanced_feature::disable($feature);
        $result = $this->parsed_graphql_operation(self::MUTATION, $args);
        $this->assert_webapi_operation_failed($result, 'Feature performance_activities is not available.');
        advanced_feature::enable($feature);

        // Fails when arguments are missing.
        $result = $this->parsed_graphql_operation(self::MUTATION, []);
        $this->assert_webapi_operation_failed($result, 'Variable "$track_schedule" of required type "mod_perform_track_schedule_input!" was not provided.');

        // Fails when id is 0.
        $args['track_schedule']['track_id'] = 0;
        $result = $this->parsed_graphql_operation(self::MUTATION, $args);
        $this->assert_webapi_operation_failed($result, 'Invalid parameter value detected (invalid track id)');

        // Fails when id is not found.
        $track_id = 1293;
        $args['track_schedule']['track_id'] = $track_id;
        $result = $this->parsed_graphql_operation(self::MUTATION, $args);
        $this->assert_webapi_operation_failed($result, "Invalid activity");
    }
}
