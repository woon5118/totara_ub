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

use mod_perform\models\activity\track;
use mod_perform\models\activity\track_status;

use mod_perform\webapi\resolver\mutation\create_track;

use totara_core\advanced_feature;

use totara_webapi\phpunit\webapi_phpunit_helper;

/**
 * @coversDefaultClass create_track.
 *
 * @group perform
 */
class mod_perform_webapi_mutation_create_track_testcase extends advanced_testcase {
    private const MUTATION = 'mod_perform_create_track';

    use webapi_phpunit_helper;

    /**
     * @covers ::resolve
     */
    public function test_create_track(): void {
        $desc = 'my activity track description';
        [$activity, $args, $context] = $this->setup_env(['description' => $desc]);

        $tracks = track::load_by_activity($activity);
        $this->assertEquals(0, $tracks->count(), 'wrong existing track count');

        $track = create_track::resolve($args, $context);
        $this->assertNotNull($track, 'track creation failed');
        $this->assertEquals($activity->id, $track->activity_id, 'wrong track parent');
        $this->assertEquals($desc, $track->description, 'wrong track parent');
        $this->assertEquals(track_status::ACTIVE, $track->status, 'wrong track status');
        $this->assertEmpty($track->assignments->all(), 'wrong track assignments');

        $tracks = track::load_by_activity($activity);
        $this->assertEquals(1, $tracks->count(), 'track does not exist for activity');
    }

    /**
     * @covers ::resolve
     */
    public function test_successful_ajax_call(): void {
        [$activity, $args, ] = $this->setup_env();

        $tracks = track::load_by_activity($activity);
        $this->assertEquals(0, $tracks->count(), 'wrong existing track count');

        $result = $this->parsed_graphql_operation(self::MUTATION, $args);
        $this->assert_webapi_operation_successful($result);

        $track = $this->get_webapi_operation_data($result);
        $this->assertNotEmpty($track, 'track creation failed');
        $this->assertEmpty($track['description'], 'wrong track parent');
        $this->assertEquals(track_status::ACTIVE, $track['status'], 'wrong track status');
        $this->assertEmpty($track['assignments'], 'wrong track assignments');

        $tracks = track::load_by_activity($activity);
        $this->assertEquals(1, $tracks->count(), 'track does not exist for activity');
    }

    /**
     * @covers ::resolve
     */
    public function test_failed_ajax_call(): void {
        [$activity, $args, ] = $this->setup_env();

        $feature = 'performance_activities';
        advanced_feature::disable($feature);
        $result = $this->parsed_graphql_operation(self::MUTATION, $args);
        $this->assert_webapi_operation_failed($result, $feature);
        advanced_feature::enable($feature);

        $result = $this->parsed_graphql_operation(self::MUTATION, []);
        $this->assert_webapi_operation_failed($result, 'details');

        $args['details']['activity_id'] = 0;
        $result = $this->parsed_graphql_operation(self::MUTATION, $args);
        $this->assert_webapi_operation_failed($result, 'activity id');

        $activity_id = 1293;
        $args['details']['activity_id'] = $activity_id;
        $result = $this->parsed_graphql_operation(self::MUTATION, $args);
        $this->assert_webapi_operation_failed($result, "$activity_id");

        self::setGuestUser();
        $args['details']['activity_id'] = $activity->id;
        $result = $this->parsed_graphql_operation(self::MUTATION, $args);
        $this->assert_webapi_operation_failed($result, 'accessible');
    }

    /**
     * Generates test data.
     *
     * @param array $activity_details details other than the activity id to add
     *        to the generated graphql arguments.
     *
     * @return array an (activity, graphql arguments, graphql context) tuple.
     */
    private function setup_env(array $activity_details = []): array {
        $this->setAdminUser();

        /** @var mod_perform_generator $perform_generator */
        $perform_generator = $this->getDataGenerator()->get_plugin_generator('mod_perform');
        $activity = $perform_generator->create_activity_in_container();

        $activity_details['activity_id'] = $activity->id;
        $args = ['details' => $activity_details];

        $context = $this->create_webapi_context(self::MUTATION);
        $context->set_relevant_context($activity->get_context());

        return [$activity, $args, $context];
    }
}
