<?php
/*
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
 * @author Matthias Bonk <matthias.bonk@totaralearning.com>
 * @package mod_perform
 */

use mod_perform\models\activity\activity;
use mod_perform\models\activity\activity_type;
use mod_perform\entities\activity\activity as activity_entity;
use mod_perform\state\activity\active;

/**
 * @group perform
 */
class mod_perform_activity_model_testcase extends advanced_testcase {

    public function test_can_manage() {
        $data_generator = $this->getDataGenerator();
        /** @var mod_perform_generator $perform_generator */
        $perform_generator = $data_generator->get_plugin_generator('mod_perform');

        $user1 = $data_generator->create_user();
        $user2 = $data_generator->create_user();
        $user3 = $data_generator->create_user();

        $this->setUser($user1);
        $activity_user1 = $perform_generator->create_activity_in_container(['activity_name' => 'User1 One']);

        $this->setUser($user2);
        $activity_user2 = $perform_generator->create_activity_in_container(['activity_name' => 'User2 One']);

        $this->setAdminUser();

        $this->assertTrue($activity_user1->can_manage($user1->id));
        $this->assertFalse($activity_user1->can_manage($user2->id));
        $this->assertFalse($activity_user1->can_manage($user3->id));

        $this->assertFalse($activity_user2->can_manage($user1->id));
        $this->assertTrue($activity_user2->can_manage($user2->id));
        $this->assertFalse($activity_user2->can_manage($user3->id));

        $this->setUser($user1);
        $this->assertTrue($activity_user1->can_manage());
        $this->assertFalse($activity_user2->can_manage());
    }

    public function test_update_general_info(): void {
        $original_data = new activity_entity();
        $original_data->name = 'Existing activity name';
        $original_data->description = 'Existing activity description';

        $activity_type = 'check-in';
        $activity = $this->create_activity($original_data, $activity_type);

        $this->assertEquals($activity->name, $original_data->name);
        $this->assertEquals($activity->description, $original_data->description);
        $this->assertEquals($activity->type->name, $activity_type);

        $activity->update_general_info('New name for existing activity', 'New description');

        // Assert in memory state is correct
        $this->assertEquals($activity->name, 'New name for existing activity');
        $this->assertEquals($activity->description, 'New description');

        // Assert persisted state is correct
        $from_database = activity::load_by_id($activity->id);
        $this->assertEquals($from_database->name, 'New name for existing activity');
        $this->assertEquals($from_database->description, 'New description');
    }

    public function test_update_general_info_accepts_null_description(): void {
        $original_data = new activity_entity();
        $original_data->name = 'Existing activity name';
        $original_data->description = 'Existing activity description';

        $activity_type = 'feedback';
        $activity = $this->create_activity($original_data, $activity_type);

        $this->assertEquals($activity->name, $original_data->name);
        $this->assertEquals($activity->description, $original_data->description);
        $this->assertEquals($activity->type->name, $activity_type);

        $activity->update_general_info('New name for existing activity', null);

        // Assert in memory state is correct
        $this->assertEquals($activity->name, 'New name for existing activity');
        $this->assertNull($activity->description);

        // Assert persisted state is correct
        $from_database = activity::load_by_id($activity->id);
        $this->assertEquals($from_database->name, 'New name for existing activity');
        $this->assertNull($from_database->description);
    }

    /**
     * @dataProvider update_general_should_validate_new_attributes
     * @param string $new_name
     * @param string $expected_message
     * @throws coding_exception
     */
    public function test_update_general_should_validate_new_attributes(string $new_name, string $expected_message): void {
        $original_data = new activity_entity();
        $original_data->name = 'Existing activity name';
        $original_data->description = 'Existing activity description';

        $activity = $this->create_activity($original_data);

        $this->expectException(coding_exception::class);
        $this->expectExceptionMessage($expected_message);

        $activity->update_general_info($new_name, 'New description');
    }

    public function update_general_should_validate_new_attributes(): array {
        return [
            'Name not present' => [
                '',
                'Name is required',
            ],
            'Name too long' => [
                random_string(activity::NAME_MAX_LENGTH + 1),
                'Name must be less than 255 characters',
            ],
        ];
    }

    public function test_update_general_should_fail_on_new_activity(): void {
        $original_data = new activity_entity();
        $original_data->name = 'Existing activity name';
        $original_data->description = 'Existing activity description';

        $new_entity = new class extends activity_entity {
            public $exists_now = true;

            public function exists(): bool {
                return $this->exists_now;
            }
        };

        /** @var activity $activity */
        $activity = activity::load_by_entity($new_entity);

        $this->expectException(coding_exception::class);
        $this->expectExceptionMessage('You can not update a entity that does not exist yet or was deleted.');

        $new_entity->exists_now = false;
        $activity->update_general_info('name', 'description');
    }

    /**
     * Create just the activity entity without any container.
     *
     * @param activity_entity $entity
     * @param string $type activity type
     * @return activity
     * @throws coding_exception
     */
    private function create_activity(activity_entity $entity, string $type = 'appraisal'): activity {
        $entity->type_id = activity_type::load_by_name($type)->id;
        $entity->status = active::get_code();

        /** @var activity_entity $entity */
        /** @noinspection CallableParameterUseCaseInTypeContextInspection */
        $entity = activity_entity::repository()->create_entity($entity);
        return activity::load_by_entity($entity);
    }
}