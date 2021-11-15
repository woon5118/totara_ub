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

use mod_perform\entity\activity\participant_instance;
use mod_perform\entity\activity\participant_section;
use mod_perform\entity\activity\subject_instance;
use mod_perform\state\participant_instance\open as participant_instance_open;
use mod_perform\state\participant_instance\closed as participant_instance_closed;
use mod_perform\state\participant_instance\not_started as participant_instance_not_started;
use mod_perform\state\participant_instance\not_submitted as participant_instance_not_submitted;
use mod_perform\state\participant_section\open as participant_section_open;
use mod_perform\state\participant_section\closed as participant_section_closed;
use mod_perform\state\participant_section\not_started as participant_section_not_started;
use mod_perform\state\participant_section\not_submitted as participant_section_not_submitted;
use mod_perform\state\subject_instance\open as subject_instance_open;
use mod_perform\state\subject_instance\closed as subject_instance_closed;
use mod_perform\state\subject_instance\not_started as subject_instance_not_started;
use mod_perform\state\subject_instance\not_submitted as subject_instance_not_submitted;
use mod_perform\webapi\resolver\mutation\create_track;
use totara_webapi\phpunit\webapi_phpunit_helper;

require_once(__DIR__ . '/generator/activity_generator_configuration.php');

/**
 * @coversDefaultClass \mod_perform\webapi\resolver\mutation\create_track
 *
 * @group perform
 */
class mod_perform_webapi_mutation_manually_change_subject_instance_testcase extends advanced_testcase {
    private const MUTATION = 'mod_perform_manually_change_subject_instance';

    use webapi_phpunit_helper;

    public function test_close_and_open(): void {
        $this->setAdminUser();

        $configuration = mod_perform_activity_generator_configuration::new()
            ->set_number_of_activities(1)
            ->set_number_of_sections_per_activity(1)
            ->set_relationships_per_section(['subject'])
            ->set_number_of_users_per_user_group_type(1)
            ->set_number_of_elements_per_section(0);

        /** @var mod_perform_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_perform');
        $generator->create_full_activities($configuration);

        // Everything starts out open.
        /** @var subject_instance $subject_instance */
        $subject_instance = subject_instance::repository()->get()->first();
        $this->assertEquals(subject_instance_not_started::get_code(), $subject_instance->progress);
        $this->assertEquals(subject_instance_open::get_code(), $subject_instance->availability);
        /** @var participant_instance $participant_instance */
        $participant_instance = participant_instance::repository()->get()->first();
        $this->assertEquals(participant_instance_not_started::get_code(), $participant_instance->progress);
        $this->assertEquals(participant_instance_open::get_code(), $participant_instance->availability);
        /** @var participant_section $participant_section */
        $participant_section = participant_section::repository()->get()->first();
        $this->assertEquals(participant_section_not_started::get_code(), $participant_section->progress);
        $this->assertEquals(participant_section_open::get_code(), $participant_section->availability);

        // Set to closed.
        $args = [
            'input' => [
                'subject_instance_id' => $subject_instance->id,
                'availability' => 'CLOSED',
            ],
        ];
        $result = $this->parsed_graphql_operation(self::MUTATION, $args);
        $this->assert_webapi_operation_successful($result);

        // Verify the changes have been applied.
        $subject_instance = subject_instance::repository()->get()->first();
        $this->assertEquals(subject_instance_not_submitted::get_code(), $subject_instance->progress);
        $this->assertEquals(subject_instance_closed::get_code(), $subject_instance->availability);
        $participant_instance = participant_instance::repository()->get()->first();
        $this->assertEquals(participant_instance_not_submitted::get_code(), $participant_instance->progress);
        $this->assertEquals(participant_instance_closed::get_code(), $participant_instance->availability);
        $participant_section = participant_section::repository()->get()->first();
        $this->assertEquals(participant_section_not_submitted::get_code(), $participant_section->progress);
        $this->assertEquals(participant_section_closed::get_code(), $participant_section->availability);

        // Set to open.
        $args = [
            'input' => [
                'subject_instance_id' => $subject_instance->id,
                'availability' => 'OPEN',
            ],
        ];
        $result = $this->parsed_graphql_operation(self::MUTATION, $args);
        $this->assert_webapi_operation_successful($result);

        // Verify the changes have been applied.
        $subject_instance = subject_instance::repository()->get()->first();
        $this->assertEquals(subject_instance_not_started::get_code(), $subject_instance->progress);
        $this->assertEquals(subject_instance_open::get_code(), $subject_instance->availability);
        $participant_instance = participant_instance::repository()->get()->first();
        $this->assertEquals(participant_instance_not_started::get_code(), $participant_instance->progress);
        $this->assertEquals(participant_instance_open::get_code(), $participant_instance->availability);
        $participant_section = participant_section::repository()->get()->first();
        $this->assertEquals(participant_section_not_started::get_code(), $participant_section->progress);
        $this->assertEquals(participant_section_open::get_code(), $participant_section->availability);
    }

}
