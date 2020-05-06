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

use core\webapi\execution_context;
use totara_webapi\graphql;
use mod_perform\entities\activity\track as track_entity;
use mod_perform\models\activity\activity;
use mod_perform\models\activity\track;

/**
 * @group perform
 */
class mod_perform_webapi_resolver_mutation_update_track_schedule_closed_fixed_testcase extends advanced_testcase {

    public function test_correct_track_is_updated(): void {
        global $DB;

        self::setAdminUser();

        $configuration = mod_perform_activity_generator_configuration::new();
        $configuration->set_number_of_activities(2);
        $configuration->set_number_of_tracks_per_activity(2);

        /** @var mod_perform_generator $perform_generator */
        $perform_generator = $this->getDataGenerator()->get_plugin_generator('mod_perform');
        $activities = $perform_generator->create_full_activities($configuration);

        // Before we test, set them all to open fixed, so we can see the effect of changing to closed fixed.
        $DB->set_field('perform_track', 'schedule_type', track_entity::SCHEDULE_TYPE_OPEN_FIXED);

        /** @var activity $activity1 */
        $activity1 = $activities->first();
        /** @var track $track1 */
        $track1 = $activity1->get_tracks()->first();

        $args = [
            'track_schedule' => [
                'track_id' => $track1->id,
            ],
        ];

        $before_tracks = $DB->get_records('perform_track', [], 'id');
        self::assertCount(4, $before_tracks);
        unset($before_tracks[$track1->id]->updated_at);

        $update_result = graphql::execute_operation(
            $this->get_execution_context('ajax', 'mod_perform_update_track_schedule_closed_fixed'),
            $args
        )->toArray(true)['data']['mod_perform_update_track_schedule_closed_fixed']['track'];

        // Verify the resulting graphql data.
        self::assertEquals($track1->id, $update_result['id']);
        self::assertEquals(track_entity::SCHEDULE_TYPE_CLOSED_FIXED, $update_result['schedule_type']);

        // Manually make the changes that we expect to make.
        $affected_track = $before_tracks[$track1->id];
        $affected_track->schedule_type = track_entity::SCHEDULE_TYPE_CLOSED_FIXED;

        $after_tracks = $DB->get_records('perform_track', [], 'id');
        unset($after_tracks[$track1->id]->updated_at);

        self::assertEquals($after_tracks, $before_tracks);
    }

    public function test_with_validation_errors(): void {
        // None currently, but we will have when additional fields are added.
    }

    /**
     * Helper to get execution context
     *
     * @param string $type
     * @param string|null $operation
     * @return execution_context
     */
    private function get_execution_context(string $type = 'dev', ?string $operation = null): execution_context {
        return execution_context::create($type, $operation);
    }

}