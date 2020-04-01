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

use core\entities\user;
use mod_perform\data_providers\activity\responses_for_participant_section;
use mod_perform\entities\activity\participant_section as participant_section_entity;
use mod_perform\models\activity\participant_section;

/**
 * @group perform
 */
class mod_perform_data_provider_responses_for_participant_section_testcase extends advanced_testcase {


    public function test_get_unanswered(): void {
        self::setAdminUser();

        $subject = self::getDataGenerator()->create_user();

        /** @var mod_perform_generator $generator */
        $generator = self::getDataGenerator()->get_plugin_generator('mod_perform');

        $generator->create_subject_instance([
            'subject_is_participating' => true,
            'subject_user_id' => $subject->id,
            'other_participant_id' => user::logged_in()->id,
            'include_questions' => true,
        ]);

        $participant_section = new participant_section(
            participant_section_entity::repository()
                ->with(['section_elements', 'participant_instance'])
                ->get()
                ->first()
        );

        $data_provider = new responses_for_participant_section($subject->id, $participant_section->id);

        $data_provider->fetch();

        $fetched_participant_section = $data_provider->get_participant_section();

        self::assert_same_participant_section($participant_section, $fetched_participant_section);

        $responses = $data_provider->get_responses();
        self::assertCount(2, $responses);

        foreach ($responses as $response) {
            self::assertNull($response->response_data);
        }
    }

    public function test_get_answered(): void {
        self::setAdminUser();

        $subject = self::getDataGenerator()->create_user();

        /** @var mod_perform_generator $generator */
        $generator = self::getDataGenerator()->get_plugin_generator('mod_perform');

        $generator->create_subject_instance([
            'subject_is_participating' => true,
            'subject_user_id' => $subject->id,
            'other_participant_id' => user::logged_in()->id,
            'include_questions' => true,
        ]);

        $participant_section = new participant_section(
            participant_section_entity::repository()
                ->with(['section_elements', 'participant_instance'])
                ->get()
                ->first()
        );

        $data_provider = new responses_for_participant_section($subject->id, $participant_section->id);

        $responses =  $data_provider->fetch()->get_responses();
        self::assertCount(2, $responses);

        // Set answers on each question.
        foreach ($responses as $response) {
            $response->set_response_data('{}');
            $response->save();
        }

        $responses =  $data_provider->fetch()->get_responses();
        self::assertCount(2, $responses);

        // Set answers on each question.
        foreach ($responses as $response) {
            self::assertEquals('{}', $response->response_data);
        }
    }

    public function test_cant_get_another_users_section_and_responses(): void {
        self::setAdminUser();

        $subject = self::getDataGenerator()->create_user();
        $another_user = self::getDataGenerator()->create_user();

        /** @var mod_perform_generator $generator */
        $generator = self::getDataGenerator()->get_plugin_generator('mod_perform');

        $generator->create_subject_instance([
            'subject_is_participating' => true,
            'subject_user_id' => $subject->id,
            'other_participant_id' => user::logged_in()->id,
            'include_questions' => true,
        ]);

        $participant_section = new participant_section(
            participant_section_entity::repository()
                ->with(['section_elements', 'participant_instance'])
                ->get()
                ->first()
        );

        $data_provider = new responses_for_participant_section($another_user->id, $participant_section->id);

        $data_provider->fetch();

        $fetched_participant_section = $data_provider->get_participant_section();

        self::assertNull($fetched_participant_section);

        $fetched_responses = $data_provider->get_responses();

        self::assertCount(0, $fetched_responses);
    }

    protected static function assert_same_participant_section(participant_section $expected, participant_section $other): void {
        self::assertEquals(
            $expected->id,
            $other->id
        );

        self::assertEquals(
            $expected->get_section()->id,
            $other->get_section()->id
        );

        self::assertEquals(
            $expected->get_participant_instance()->id,
            $other->get_participant_instance()->id
        );
    }

}