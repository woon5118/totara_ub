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
use totara_webapi\graphql;

/**
 * @group perform
 */
class mod_perform_webapi_resolver_mutation_update_track_schedule_closed_fixed_testcase
    extends mod_perform_webapi_resolver_mutation_update_track_schedule_testcase {

    use webapi_phpunit_helper;

    public function test_user_cannot_update_without_permission(): void {
        self::setAdminUser();

        /** @var mod_perform_generator $perform_generator */
        $perform_generator = $this->getDataGenerator()->get_plugin_generator('mod_perform');
        $activities = $perform_generator->create_full_activities();

        /** @var activity $activity1 */
        $activity1 = $activities->first();
        /** @var track $track1 */
        $track1 = $activity1->get_tracks()->first();

        $args = [
            'track_schedule' => [
                'track_id' => $track1->id,
                'is_open' => false,
                'is_fixed' => true,
                'fixed_from' => 123,
                'fixed_to' => 234,
            ],
        ];

        $user = self::getDataGenerator()->create_user();
        self::setUser($user);

        $this->expectException(required_capability_exception::class);
        $this->expectExceptionMessage('Manage performance activities');

        $this->resolve_graphql_mutation('mod_perform_update_track_schedule', $args);
    }

    public function test_correct_track_is_updated(): void {
        global $DB;

        $args = [
            'track_schedule' => [
                'track_id' => $this->track1_id,
                'is_open' => false,
                'is_fixed' => true,
                'fixed_from' => 123,
                'fixed_to' => 234,
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
        $result_errors = $result['validation_errors'];

        // Verify the resulting graphql data.
        self::assertEmpty($result_errors);
        self::assertEquals($this->track1_id, $result_track->id);
        self::assertFalse($result_track->schedule_is_open);
        self::assertTrue($result_track->schedule_is_fixed);

        // Manually make the changes that we expect to make.
        $affected_track = $before_tracks[$this->track1_id];
        $affected_track->schedule_is_open = 0;
        $affected_track->schedule_is_fixed = 1;
        $affected_track->schedule_fixed_from = 123;
        $affected_track->schedule_fixed_to = 234;

        $after_tracks = $DB->get_records('perform_track', [], 'id');
        unset($after_tracks[$this->track1_id]->updated_at);
        self::assertEquals($after_tracks, $before_tracks);
    }

    public function test_with_validation_errors(): void {
        global $DB;

        $before_tracks = $DB->get_records('perform_track', [], 'id');

        // To must be after or equal to from.
        $args = [
            'track_schedule' => [
                'track_id' => $this->track1_id,
                'is_open' => false,
                'is_fixed' => true,
                'fixed_from' => 234,
                'fixed_to' => 123,
            ],
        ];

        $result = $this->resolve_graphql_mutation(
            'mod_perform_update_track_schedule',
            $args
        );
        $result_track = $result['track'];
        $result_errors = $result['validation_errors'];

        // Verify resulting graphql data.
        self::assertNotEmpty($result_errors);
        self::assertEquals('schedule_fixed_closed_order_validation', $result_errors[0]->error_code);
        self::assertEquals(-1, $result_track->schedule_is_open);
        self::assertEquals(-1, $result_track->schedule_is_fixed);

        $after_tracks = $DB->get_records('perform_track', [], 'id');
        self::assertEquals($after_tracks, $before_tracks);
    }

}