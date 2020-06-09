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
 */

use mod_perform\entities\activity\participant_instance;
use mod_perform\state\participant_instance\not_started;

/**
 * @group perform
 */
class mod_perform_participant_instance_repository_testcase extends advanced_testcase {

    public function test_user_can_not_view_other_user_details_with_no_link_between_users(): void {
        self::setAdminUser();

        /** @var mod_perform_generator $generator */
        $generator = self::getDataGenerator()->get_plugin_generator('mod_perform');

        $main_user = self::getDataGenerator()->create_user();
        $other_user = self::getDataGenerator()->create_user();

        $generator->create_subject_instance([
            'subject_is_participating' => true,
            'subject_user_id' => $main_user->id,
            'other_participant_id' => null,
            'include_questions' => false,
        ]);

        $generator->create_subject_instance([
            'subject_is_participating' => true,
            'subject_user_id' => $other_user->id,
            'other_participant_id' => null,
            'include_questions' => false,
        ]);

        $can_view = participant_instance::repository()->user_can_view_other_users_profile($main_user->id, $other_user->id);

        self::assertFalse($can_view);
    }

    public function test_user_can_view_other_users_profile_when_they_share_a_subject_instance(): void {
        self::setAdminUser();

        /** @var mod_perform_generator $generator */
        $generator = self::getDataGenerator()->get_plugin_generator('mod_perform');

        $subject_user = self::getDataGenerator()->create_user();
        $main_user = self::getDataGenerator()->create_user();
        $other_user = self::getDataGenerator()->create_user();

        $subject_instance = $generator->create_subject_instance([
            'subject_is_participating' => false,
            'subject_user_id' => $subject_user->id,
            'other_participant_id' => $other_user->id,
            'include_questions' => false,
        ]);

        $other_participant_instance = new participant_instance();
        $other_participant_instance->core_relationship_id = 0; // stubbed
        $other_participant_instance->participant_id = $main_user->id;
        $other_participant_instance->subject_instance_id = $subject_instance->id;
        $other_participant_instance->progress = not_started::get_code();
        $other_participant_instance->save();

        $can_view = participant_instance::repository()->user_can_view_other_users_profile($main_user->id, $other_user->id);

        self::assertTrue($can_view);
    }

    public function test_user_can_view_other_users_profile_when_other_is_subject(): void {
        self::setAdminUser();

        /** @var mod_perform_generator $generator */
        $generator = self::getDataGenerator()->get_plugin_generator('mod_perform');

        $main_user = self::getDataGenerator()->create_user();
        $other_user = self::getDataGenerator()->create_user();

        $generator->create_subject_instance([
            'subject_is_participating' => false,
            'subject_user_id' => $other_user->id,
            'other_participant_id' => $main_user->id,
            'include_questions' => false,
        ]);

        $can_view = participant_instance::repository()->user_can_view_other_users_profile($main_user->id, $other_user->id);

        self::assertTrue($can_view);
    }

    public function test_subject_can_not_view_participant_details(): void {
        self::setAdminUser();

        /** @var mod_perform_generator $generator */
        $generator = self::getDataGenerator()->get_plugin_generator('mod_perform');

        $main_user = self::getDataGenerator()->create_user();
        $other_user = self::getDataGenerator()->create_user();

        $generator->create_subject_instance([
            'subject_is_participating' => false,
            'subject_user_id' => $main_user->id,
            'other_participant_id' => $other_user->id,
            'include_questions' => false,
        ]);

        $can_view = participant_instance::repository()->user_can_view_other_users_profile($main_user->id, $other_user->id);

        self::assertFalse($can_view);
    }

}
