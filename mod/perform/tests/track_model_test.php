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
use core\orm\entity\entity;
use core\orm\query\builder;
use mod_perform\dates\date_offset;
use mod_perform\dates\resolvers\dynamic\dynamic_source;
use mod_perform\dates\resolvers\dynamic\user_creation_date;
use mod_perform\dates\resolvers\dynamic\user_custom_field;
use mod_perform\entities\activity\track as track_entity;
use mod_perform\models\activity\activity;
use mod_perform\models\activity\track;
use mod_perform\models\activity\track_status;
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
     * @param int $count_to
     * @param string $direction
     * @param dynamic_source $dynamic_source
     * @param string $expected_exception_message
     * @throws coding_exception
     */
    public function test_invalid_open_dynamic_schedule_from_to_permutations(
        int $count_from,
        int $count_to,
        string $direction,
        dynamic_source $dynamic_source,
        string $expected_exception_message
    ): void {
        $track = track::load_by_entity($this->mock_existing_entity(track_entity::class));

        $this->expectException(coding_exception::class);
        $this->expectExceptionMessage($expected_exception_message);

        $from = new date_offset(
            $count_from,
            date_offset::UNIT_DAY,
            $direction
        );
        $to = new date_offset(
            $count_to,
            date_offset::UNIT_DAY,
            $direction
        );
        $track->set_schedule_closed_dynamic(
            $from,
            $to,
            $dynamic_source
        );
    }

    /**
     * Mock an entity that will be flagged as "existing".
     *
     * Useful for when you don't actually need to hit the database, but something requires an
     * "existing" entity, such as testing a model method that doesn't directly need the database.
     *
     * @param string $class
     * @return entity | MockObject
     */
    protected function mock_existing_entity(string $class): entity {
        $mock = $this->getMockBuilder($class)->setMethods(['exists'])->getMock();
        $mock->method('exists')->willReturn(true);

        return $mock;
    }

    public function invalid_open_dynamic_schedule_from_to_permutations_provider(): array {
        $available_dynamic_source = (new user_creation_date())->get_options()->first();
        $unavailable_dynamic_source = new dynamic_source(null, 'default', 'Birthday');

        return [
            'From after to' => [
                100,
                0,
                date_offset::DIRECTION_AFTER,
                $available_dynamic_source,
                '"from" must not be after "to"'
            ],
            'To before from' => [
                0,
                100,
                date_offset::DIRECTION_BEFORE,
                $available_dynamic_source,
                'from" must not be after "to"'
            ],
            'Unavailable date resolver option' => [
                100,
                0,
                date_offset::DIRECTION_BEFORE,
                $unavailable_dynamic_source,
                'Dynamic source must be available'
            ],
        ];
    }

    public function set_schedule_methods_data_provider(): array {
        $dynamic_source = dynamic_source::all_available()->first();

        return [
            ['set_schedule_open_fixed', [111]],
            ['set_schedule_closed_fixed', [111, 222]],
            [
                'set_schedule_closed_dynamic',
                [
                    new date_offset(111, date_offset::UNIT_WEEK, date_offset::DIRECTION_AFTER),
                    new date_offset(222, date_offset::UNIT_WEEK, date_offset::DIRECTION_AFTER),
                    $dynamic_source
                ]
            ],
            [
                'set_schedule_open_dynamic',
                [
                    new date_offset(111, date_offset::UNIT_WEEK, date_offset::DIRECTION_BEFORE),
                    $dynamic_source
                ]
            ],
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

    public function schedule_changes_data_provider(): array {
        function get_custom_field_source(string $option_key) {
            builder::get_db()->insert_record(
                'user_info_field',
                (object)['shortname' => $option_key, 'name' => 'time-custom', 'categoryid' => 1, 'datatype' => 'datetime']
            );

            return (new user_custom_field())->get_options()->find(
                function (dynamic_source $source) use ($option_key) {
                    return $source->get_option_key() === $option_key;
                }
            );
        }

        $user_creation_date_source = (new user_creation_date())->get_options()->first();

        return [
            [
                'set_schedule_open_fixed',
                [111],
                [222]
            ],
            [
                'set_schedule_closed_fixed',
                [111, 999],
                [222, 999]
            ],
            [
                'set_schedule_closed_fixed',
                [111, 999],
                [111, 888]
            ],
            [
                'set_schedule_closed_dynamic',
                [
                    new date_offset(111, date_offset::UNIT_WEEK, date_offset::DIRECTION_AFTER),
                    new date_offset(999, date_offset::UNIT_WEEK, date_offset::DIRECTION_AFTER),
                    $user_creation_date_source
                ],
                [
                    new date_offset(222, date_offset::UNIT_WEEK, date_offset::DIRECTION_AFTER),
                    new date_offset(999, date_offset::UNIT_WEEK, date_offset::DIRECTION_AFTER),
                    $user_creation_date_source
                ]
            ],
            [
                'set_schedule_closed_dynamic',
                [
                    new date_offset(111, date_offset::UNIT_WEEK, date_offset::DIRECTION_AFTER),
                    new date_offset(999, date_offset::UNIT_WEEK, date_offset::DIRECTION_AFTER),
                    $user_creation_date_source
                ],
                [
                    new date_offset(111, date_offset::UNIT_WEEK, date_offset::DIRECTION_AFTER),
                    new date_offset(888, date_offset::UNIT_WEEK, date_offset::DIRECTION_AFTER),
                    $user_creation_date_source
                ]
            ],
            [
                'set_schedule_closed_dynamic',
                [
                    new date_offset(111, date_offset::UNIT_WEEK, date_offset::DIRECTION_AFTER),
                    new date_offset(999, date_offset::UNIT_WEEK, date_offset::DIRECTION_AFTER),
                    $user_creation_date_source
                ],
                [
                    new date_offset(111, date_offset::UNIT_DAY, date_offset::DIRECTION_AFTER),
                    new date_offset(999, date_offset::UNIT_DAY, date_offset::DIRECTION_AFTER),
                    $user_creation_date_source
                ]
            ],
            [
                'set_schedule_closed_dynamic',
                [
                    new date_offset(111, date_offset::UNIT_WEEK, date_offset::DIRECTION_AFTER),
                    new date_offset(999, date_offset::UNIT_WEEK, date_offset::DIRECTION_AFTER),
                    $user_creation_date_source
                ],
                [
                    new date_offset(999, date_offset::UNIT_WEEK, date_offset::DIRECTION_BEFORE),
                    new date_offset(111, date_offset::UNIT_WEEK, date_offset::DIRECTION_BEFORE),
                    $user_creation_date_source
                ]
            ],
            [
                'set_schedule_open_dynamic',
                [
                    new date_offset(111, date_offset::UNIT_WEEK, date_offset::DIRECTION_BEFORE),
                    $user_creation_date_source
                ],
                [
                    new date_offset(222, date_offset::UNIT_WEEK, date_offset::DIRECTION_BEFORE),
                    $user_creation_date_source
                ]
            ],
            [
                'set_schedule_open_dynamic',
                [
                    new date_offset(111, date_offset::UNIT_WEEK, date_offset::DIRECTION_BEFORE),
                    $user_creation_date_source
                ],
                [
                    new date_offset(111, date_offset::UNIT_DAY, date_offset::DIRECTION_BEFORE),
                    $user_creation_date_source
                ]
            ],
            [
                'set_schedule_open_dynamic',
                [
                    new date_offset(111, date_offset::UNIT_WEEK, date_offset::DIRECTION_BEFORE),
                    $user_creation_date_source
                ],
                [
                    new date_offset(111, date_offset::UNIT_WEEK, date_offset::DIRECTION_AFTER),
                    $user_creation_date_source
                ]
            ],
            'open dynamic - dynamic source resolver change' => [
                'set_schedule_open_dynamic',
                [
                    new date_offset(111, date_offset::UNIT_WEEK, date_offset::DIRECTION_BEFORE),
                    $user_creation_date_source
                ],
                [
                    new date_offset(111, date_offset::UNIT_WEEK, date_offset::DIRECTION_BEFORE),
                    get_custom_field_source('time-custom')
                ]
            ],
            'open dynamic - dynamic source option change' => [
                'set_schedule_open_dynamic',
                [
                    new date_offset(111, date_offset::UNIT_WEEK, date_offset::DIRECTION_BEFORE),
                    get_custom_field_source('time-custom1')
                ],
                [
                    new date_offset(111, date_offset::UNIT_WEEK, date_offset::DIRECTION_BEFORE),
                    get_custom_field_source('time-custom2')
                ]
            ],
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
        /** @var mod_perform_generator $perform_generator */
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

    public function test_get_resolver_option_when_set(): void {
        /** @var track_entity | MockObject $entity */
        $entity = $this->mock_existing_entity(track_entity::class);

        /** @var dynamic_source $option */
        $option = (new user_creation_date())->get_options()->first();

        $entity->schedule_dynamic_source = $option;

        $track = new track($entity);

        $selected_option = $track->schedule_dynamic_source;

        self::assertInstanceOf(dynamic_source::class, $selected_option);

        self::assertEqualsCanonicalizing([
            'resolver_class_name' => user_creation_date::class,
            'option_key' => user_creation_date::DEFAULT_KEY,
            'display_name' => 'User creation date',
            'is_available' => true,
        ], $selected_option->jsonSerialize());
    }

    public function test_get_resolver_option_when_key_is_no_longer_available(): void {
        /** @var track_entity | MockObject $entity */
        $entity = $this->mock_existing_entity(track_entity::class);

        /** @var dynamic_source $option */
        $option = (new user_creation_date())->get_options()->first();

        $data = $option->jsonSerialize();
        $data['option_key'] = 'non-existing';

        $entity->schedule_dynamic_source = $data;

        $track = new track($entity);

        $selected_option = $track->schedule_dynamic_source;

        self::assertInstanceOf(dynamic_source::class, $selected_option);

        self::assertEquals([
            'resolver_class_name' => user_creation_date::class,
            'option_key' => 'non-existing',
            'display_name' => 'User creation date',
            'is_available' => false,
        ], $selected_option->jsonSerialize());
    }

    public function test_get_resolver_option_when_not_set(): void {
        /** @var track_entity | MockObject $entity */
        $entity = $this->mock_existing_entity(track_entity::class);

        $entity->schedule_dynamic_source = null;

        $track = new track($entity);

        self::assertNull($track->schedule_dynamic_source);
    }

}
