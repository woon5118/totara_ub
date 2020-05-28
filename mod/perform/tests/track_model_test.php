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

use mod_perform\entities\activity\activity as activity_entity;
use mod_perform\models\activity\activity;
use mod_perform\models\activity\track;
use mod_perform\entities\activity\track as track_entity;
use mod_perform\models\activity\track_status;
use mod_perform\state\activity\active;
use PHPUnit\Framework\MockObject\MockObject;

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

    /**
     * @dataProvider invalid_open_dynamic_schedule_from_to_permutations_provider
     * @param int $count_from
     * @param int $unit
     * @param int $direction
     * @param string $expected_exception_message
     * @throws coding_exception
     */
    public function test_invalid_open_dynamic_schedule_from_to_permutations(
        int $count_from,
        int $unit,
        int $direction,
        string $expected_exception_message
    ): void {
        $track = track::load_by_entity($this->mock_existing_entity(track_entity::class));

        $this->expectException(coding_exception::class);
        $this->expectExceptionMessage($expected_exception_message);

        $track->set_schedule_closed_dynamic(
            $count_from,
            $unit,
            track_entity::SCHEDULE_DYNAMIC_UNIT_DAY,
            $direction
        );
    }

    /**
     * Mock an entity that will be flagged as "existing".
     *
     * Useful for when you don't actually need to hit the database, but something requires an
     * "existing" entity, such as testing a model method that doesn't directly need the database.
     *
     * @param string $class
     * @return \core\orm\entity\entity|MockObject
     */
    protected function mock_existing_entity(string $class): \core\orm\entity\entity {
        $mock = $this->getMockBuilder($class)->getMock();
        $mock->method('exists')->willReturn(true);

        return $mock;
    }

    public function invalid_open_dynamic_schedule_from_to_permutations_provider(): array {
        return [
            [
                100,
                0,
                track_entity::SCHEDULE_DYNAMIC_DIRECTION_AFTER,
                '"count_from" must not be after "count_to" when dynamic schedule direction is "AFTER"'
            ],
            [
                0,
                100,
                track_entity::SCHEDULE_DYNAMIC_DIRECTION_BEFORE,
                'count_from" must not be before "count_to" when dynamic schedule direction is "BEFORE"'
            ],
        ];
    }

    public function set_schedule_methods_data_provider() {
        return [
            ['set_schedule_open_fixed', [111]],
            ['set_schedule_closed_fixed', [111, 222]],
            ['set_schedule_closed_dynamic', [111, 222, 1, 0]],
            ['set_schedule_open_dynamic', [111, 1, 1]],
        ];
    }

    /**
     * @dataProvider set_schedule_methods_data_provider
     * @param string $method_name
     * @param array $params
     */
    public function test_schedule_sync_is_not_flagged_for_draft(string $method_name, array $params) {
        $this->setAdminUser();

        $track = $this->create_activity_track('DRAFT');

        /** @var track_entity $track_entity */
        $track_entity = track_entity::repository()->find($track->get_id());
        $this->assertEquals(0, $track_entity->schedule_needs_sync);

        // Update method should set flag.
        $track->$method_name(...$params);
        $track->update();
        $track_entity->refresh();
        $this->assertEquals(0, $track_entity->schedule_needs_sync);
    }

    /**
     * @dataProvider set_schedule_methods_data_provider
     * @param string $method_name
     * @param array $params
     */
    public function test_schedule_sync_is_flagged_for_active_activity(string $method_name, array $params) {
        $this->setAdminUser();

        $track = $this->create_activity_track('ACTIVE');

        /** @var track_entity $track_entity */
        $track_entity = track_entity::repository()->find($track->get_id());
        $this->assertEquals(0, $track_entity->schedule_needs_sync);

        // Update method should set flag.
        $track->$method_name(...$params);
        $track->update();
        $track_entity->refresh();
        $this->assertEquals(1, $track_entity->schedule_needs_sync);
    }

    /**
     * @dataProvider set_schedule_methods_data_provider
     * @param string $method_name
     * @param array $params
     */
    public function test_schedule_sync_is_not_flagged_for_update_without_actual_changes(string $method_name, array $params) {
        $this->setAdminUser();

        $track = $this->create_activity_track('ACTIVE');

        /** @var track_entity $track_entity */
        $track_entity = track_entity::repository()->find($track->get_id());

        // Update method sets flag because our test params are different from default data.
        $track->$method_name(...$params);
        $track->update();
        $track_entity->refresh();
        $this->assertEquals(1, $track_entity->schedule_needs_sync);

        // Reset flag.
        $track_entity->schedule_needs_sync = 0;
        $track_entity->update();

        // Update method doesn't set flag because the params are the same as the ones already saved.
        $track->$method_name(...$params);
        $track->update();
        $track_entity->refresh();
        $this->assertEquals(0, $track_entity->schedule_needs_sync);
    }

    public function schedule_changes_data_provider() {
        return [
            ['set_schedule_open_fixed', [111], [222]],
            ['set_schedule_closed_fixed', [111, 999], [222, 999]],
            ['set_schedule_closed_fixed', [111, 999], [111, 888]],
            ['set_schedule_closed_dynamic', [111, 999, 1, 0], [222, 999, 1, 0]],
            ['set_schedule_closed_dynamic', [111, 999, 1, 0], [111, 888, 1, 0]],
            ['set_schedule_closed_dynamic', [111, 999, 1, 0], [111, 999, 0, 0]],
            ['set_schedule_closed_dynamic', [111, 999, 1, 0], [999, 111, 1, 1]],
            ['set_schedule_open_dynamic', [111, 1, 1], [222, 1, 1]],
            ['set_schedule_open_dynamic', [111, 1, 1], [111, 0, 1]],
            ['set_schedule_open_dynamic', [111, 1, 1], [111, 1, 0]],
        ];
    }

    /**
     * @dataProvider schedule_changes_data_provider
     * @param string $method_name
     * @param array $setup_params
     * @param array $changed_params
     */
    public function test_schedule_sync_is_flagged_for_update_with_actual_changes(
        string $method_name,
        array $setup_params,
        array $changed_params
    ) {
        $this->setAdminUser();

        $track = $this->create_activity_track('ACTIVE');

        /** @var track_entity $track_entity */
        $track_entity = track_entity::repository()->find($track->get_id());

        // Update method sets flag because our test params are different from default data.
        $track->$method_name(...$setup_params);
        $track->update();
        $track_entity->refresh();
        $this->assertEquals(1, $track_entity->schedule_needs_sync);

        // Reset flag.
        $track_entity->schedule_needs_sync = 0;
        $track_entity->update();

        // Update method sets flag because the params are different from the ones already saved.
        $track->$method_name(...$changed_params);
        $track->update();
        $track_entity->refresh();
        $this->assertEquals(1, $track_entity->schedule_needs_sync);
    }


    /**
     * Create an activity with the given status and return the first created track.
     *
     * @param string $activity_status
     * @return track
     */
    private function create_activity_track(string $activity_status): track {
        $perform_generator = $this->getDataGenerator()->get_plugin_generator('mod_perform');
        /** @var activity $activity */
        $activity = $perform_generator->create_activity_in_container([
            'create_track' => true,
            'activity_status' => $activity_status
        ]);

        return track::load_by_activity($activity)->first();
    }

    public function test_update_performs_validation(): void {
        $this->setAdminUser();

        /** @var mod_perform_generator $perform_generator */
        $perform_generator = $this->getDataGenerator()->get_plugin_generator('mod_perform');
        $activity = $perform_generator->create_activity_in_container();
        $track = track::create($activity);

        $track->set_schedule_open_fixed(111);
        $track->set_due_date_fixed(222);

        $this->expectException(coding_exception::class);
        $this->expectExceptionMessage('Cannot set due date to fixed except when schedule is not open and fixed');

        $track->update();
    }

    public function test_validate_fails_due_to_invalid_due_date_type(): void {
        $this->setAdminUser();

        /** @var mod_perform_generator $perform_generator */
        $perform_generator = $this->getDataGenerator()->get_plugin_generator('mod_perform');
        $activity = $perform_generator->create_activity_in_container();
        $track = track::create($activity);

        $track->set_schedule_open_fixed(111);
        $track->set_due_date_fixed(222);

        $this->expectException(coding_exception::class);
        $this->expectExceptionMessage('Cannot set due date to fixed except when schedule is not open and fixed');

        $track->validate();
    }

    public function test_validate_fails_due_to_invalid_fixed_due_date(): void {
        $this->setAdminUser();

        /** @var mod_perform_generator $perform_generator */
        $perform_generator = $this->getDataGenerator()->get_plugin_generator('mod_perform');
        $activity = $perform_generator->create_activity_in_container();
        $track = track::create($activity);

        $track->set_schedule_closed_fixed(222, 444);
        $track->set_due_date_fixed(333);

        $this->expectException(coding_exception::class);
        $this->expectExceptionMessage('Cannot set fixed due date earlier than the schedule end date');

        $track->validate();
    }

}
