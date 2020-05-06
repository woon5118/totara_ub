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

use core\collection;

use mod_perform\models\activity\track;
use mod_perform\models\activity\track_status;

/**
 * @coversDefaultClass track.
 *
 * @group perform
 */
class mod_perform_track_model_testcase extends advanced_testcase {
    /**
     * @covers ::create
     * @covers ::load_by_activity
     */
    public function test_create_tracks(): void {
        $this->setAdminUser();

        /** @var mod_perform_generator $perform_generator */
        $perform_generator = $this->getDataGenerator()->get_plugin_generator('mod_perform');
        $activity = $perform_generator->create_activity_in_container(['create_track' => true]);

        // There is already a "default" track, created when the activity is
        // created.
        $existing_tracks = track::load_by_activity($activity);
        $this->assertEquals(1, $existing_tracks->count(), 'wrong existing track count');
        $default_track = $existing_tracks->first();

        $desc_base = "my test track";
        $tracks = collection::new(range(0, 1))
            ->map_to(
                function (int $i) use ($activity, $desc_base): track {
                    return track::create($activity, "$desc_base #$i");
                }
            )->all();

        $tracks_by_id = [];
        foreach ($tracks as $track) {
            $track_id = $track->id;

            $this->assertGreaterThan(0, $track_id, 'transient track');
            $this->assertStringContainsString($desc_base, $track->description, 'wrong desc');
            $this->assertEquals($activity->get_id(), $track->activity_id, 'wrong parent');
            $this->assertEquals(track_status::ACTIVE, $track->status, 'wrong track status');
            $this->assertEmpty($track->assignments->all(), 'wrong track assignments');

            $tracks_by_id[$track_id] = $track;
        }

        // Confirm the repository really has the new tracks.
        $retrieved_tracks = track::load_by_activity($activity);
        $this->assertEquals(
            count($tracks) + 1,
            $retrieved_tracks->count(),
            'wrong track retrieval count'
        );

        foreach ($retrieved_tracks as $track) {
            $track_id = $track->id;
            if ($track_id === $default_track->id) {
                // Ignore the default track.
                continue;
            }

            $expected = $tracks_by_id[$track_id] ?? null;
            $this->assertNotNull($expected, "unknown retrieved track id '$track_id'");

            $expected_values = [
                $expected->activity_id,
                $expected->status,
                []
            ];

            $actual_values = [
                $track->activity_id,
                $track->status,
                $track->assignments->all()
            ];

            $this->assertEquals($expected_values, $actual_values, 'wrong track values');
        }
    }

    /**
     * @covers ::create
     * @covers ::activate
     * @covers ::pause
     */
    public function test_track_transitions(): void {
        $this->setAdminUser();

        $active = track_status::ACTIVE;
        $paused = track_status::PAUSED;

        /** @var mod_perform_generator $perform_generator */
        $perform_generator = $this->getDataGenerator()->get_plugin_generator('mod_perform');
        $activity = $perform_generator->create_activity_in_container();
        $track = track::create($activity);

        // State active; invoke activate - ignored.
        $to_status = $track->activate()->status;
        $this->assertEquals($active, $to_status, 'wrong status');

        // Transition active -> paused
        $from_status = $track->status;
        $to_status = $track->pause()->status;
        $this->assertEquals($active, $from_status, 'wrong status');
        $this->assertEquals($paused, $to_status, 'wrong status');

        // State paused; invoke paused - ignored
        $from_status = $track->status;
        $to_status = $track->pause()->status;
        $this->assertEquals($paused, $to_status, 'wrong status');

        // Transition paused to active.
        $from_status = $track->status;
        $to_status = $track->activate()->status;
        $this->assertEquals($paused, $from_status, 'wrong status');
        $this->assertEquals($active, $to_status, 'wrong status');
    }
}
