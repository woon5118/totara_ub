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
 * @author Jaron Steenson <jaron.steenson@totaralearning.com>
 * @package mod_perform
 * @category test
 */

use mod_perform\entities\activity\subject_instance as subject_instance_entity;
use mod_perform\models\activity\subject_instance;

require_once(__DIR__ . '/state_testcase.php');

/**
 * @group perform
 */
class mod_perform_subject_instance_model_testcase extends advanced_testcase {

    /**
     * @param int $extra_instance_count
     * @dataProvider get_instance_count_provider
     */
    public function test_get_instance_count(int $extra_instance_count): void {
        /** @var mod_perform_generator $perform_generator */
        $perform_generator = self::getDataGenerator()->get_plugin_generator('mod_perform');

        $config = mod_perform_activity_generator_configuration::new()
            ->set_number_of_activities(1)
            ->set_number_of_tracks_per_activity(1)
            ->set_number_of_users_per_user_group_type(1);

        $perform_generator->create_full_activities($config)->first();

        /** @var subject_instance_entity $subject_instance_entity */
        $subject_instance_entity = subject_instance_entity::repository()->order_by('id')->first();

        $i = 0;
        $now = time();
        while ($extra_instance_count > $i) {
            $extra_subject_instance = new subject_instance_entity();
            $extra_subject_instance->track_user_assignment_id = $subject_instance_entity->track_user_assignment_id;
            $extra_subject_instance->subject_user_id = $subject_instance_entity->subject_user_id;
            $extra_subject_instance->created_at = $now + ($i + 1); // Force a decent gap between created at times.
            $extra_subject_instance->save();

            $i++;
        }

        $last_instance_entity = $extra_subject_instance ?? $subject_instance_entity;

        $first_instance_count = (new subject_instance($subject_instance_entity))->get_instance_count();
        $last_instance_count = (new subject_instance($last_instance_entity))->get_instance_count();

        self::assertEquals(1, $first_instance_count);
        self::assertEquals($extra_instance_count + 1, $last_instance_count);
    }

    public function get_instance_count_provider(): array {
        return [
            'Single' => [0],
            'Double' => [1],
            'Triple' => [2],
        ];
    }

}
