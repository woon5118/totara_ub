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

use core\collection;
use mod_perform\entities\activity\participant_section as participant_section_entity;
use mod_perform\models\activity\participant_instance;
use mod_perform\models\response\participant_section;

/**
 * @group perform
 */
class mod_perform_participant_section_model_testcase extends advanced_testcase {

    public function test_get_participant_section_multiple_answerable_participant_instances(): void {
        self::setAdminUser();

        $data_generator = self::getDataGenerator();

        /** @var mod_perform_generator $perform_generator */
        $perform_generator = $data_generator->get_plugin_generator('mod_perform');

        $subject_user = $data_generator->create_user();
        $manager_appraiser_user = $data_generator->create_user();

        [
            $subject_section,
            $manager_section,
            $appraiser_section
        ] = $perform_generator->create_section_with_combined_manager_appraiser($subject_user, $manager_appraiser_user);

        $subject_answerable_participants = (new participant_section($subject_section))->get_answerable_participant_instances();
        $manager_answerable_participants = (new participant_section($manager_section))->get_answerable_participant_instances();
        $appraiser_answerable_participants = (new participant_section($appraiser_section))->get_answerable_participant_instances();

        self::assertCount(1, $subject_answerable_participants);
        self::assertSame($subject_user->id, $subject_answerable_participants->first()->participant_id);

        self::assertCount(2, $manager_answerable_participants);
        self::assertCount(2, $appraiser_answerable_participants);

        self::assertEquals(
            $manager_answerable_participants,
            $appraiser_answerable_participants,
            'Both manager and appraiser should have the same answerable participants'
        );

        self::assertNotEquals(
            $manager_answerable_participants->first()->id,
            $manager_answerable_participants->last()->id,
            'Should be two distinct participant instance records for the manager answerable participants'
        );

        self::assertNotEquals(
            $appraiser_answerable_participants->first()->id,
            $appraiser_answerable_participants->last()->id,
            'Should be two distinct participant instance records for the appraiser answerable participants'
        );

        self::assertNotEquals(
            $manager_answerable_participants->first()->id,
            $manager_answerable_participants->last()->id,
            'Should be two distinct participant instance records for the manager answerable participants'
        );

        $all = $appraiser_answerable_participants->all();
        array_push($all, ...$manager_answerable_participants);

        foreach ($all as $answerable_participant) {
            self::assertEquals($manager_appraiser_user->id, $answerable_participant->participant_id);
        }
    }

}
