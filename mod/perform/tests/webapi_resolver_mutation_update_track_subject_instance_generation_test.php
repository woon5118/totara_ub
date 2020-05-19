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
class mod_perform_webapi_resolver_mutation_update_track_subject_instance_generation_testcase
    extends mod_perform_webapi_resolver_mutation_update_track_schedule_base {

    use webapi_phpunit_helper;

    public function test_when_subject_instance_generation_control_is_enabled(): void {
        global $DB;

        set_config('totara_job_allowmultiplejobs', 1);

        $args = [
            'track_schedule' => [
                'track_id' => $this->track1_id,
                'subject_instance_generation' => 'ONE_PER_JOB',
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
        self::assertEquals('ONE_PER_JOB', $result_track->subject_instance_generation);

        // Manually make the changes that we expect to make.
        $affected_track = $before_tracks[$this->track1_id];
        $affected_track->subject_instance_generation = track_entity::SUBJECT_INSTANCE_GENERATION_ONE_PER_JOB;
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
        $after_tracks = $DB->get_records('perform_track', [], 'id');
        unset($after_tracks[$this->track1_id]->updated_at);

        self::assertEquals($before_tracks, $after_tracks);
    }

    public function test_when_subject_instance_generation_control_is_disabled(): void {
        global $DB;

        // Multiple jobs is set off in test base class.

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
        self::assertEquals(null, $result_track->subject_instance_generation); // Ignores actual value.

        // Manually make the changes that we expect to make.
        $affected_track = $before_tracks[$this->track1_id];
        $affected_track->subject_instance_generation = -1; // Unchanged.
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

}
