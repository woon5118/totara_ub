<?php

use core\entity\user;
use mod_perform\data_providers\response\participant_section;
use mod_perform\entity\activity\participant_section as participant_section_entity;

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
 * @author Kunle Odusan <kunle.odusan@totaralearning.com>
 */

use mod_perform\entity\activity\participant_section_repository;
use mod_perform\models\activity\participant_source;

/**
 * @covers \mod_perform\entity\activity\participant_section_repository
 * @group perform
*/
class mod_perform_participant_section_repository_test extends advanced_testcase {

    public function test_cant_get_another_users_section_and_responses(): void {
        $this->setAdminUser();

        $subject = $this->getDataGenerator()->create_user();
        $another_user = $this->getDataGenerator()->create_user();

        /** @var mod_perform_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_perform');

        $generator->create_subject_instance([
            'subject_is_participating' => true,
            'subject_user_id' => $subject->id,
            'other_participant_id' => user::logged_in()->id,
            'include_questions' => true,
        ]);


        $participant_section = participant_section_entity::repository()
                ->with(['section_elements', 'participant_instance'])
                ->get()
                ->first();
        $fetched_participant_section = (new participant_section($another_user->id, participant_source::INTERNAL))->find_by_section_id($participant_section->id);

        $this->assertNull($fetched_participant_section);
    }
}