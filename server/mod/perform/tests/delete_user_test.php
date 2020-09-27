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
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Fabian Derschatta <fabian.derschatta@totaralearning.com>
 * @package mod_perform
 */

use mod_perform\constants;
use mod_perform\entities\activity\participant_instance;
use mod_perform\entities\activity\subject_instance;
use mod_perform\models\activity\participant_source;
use mod_perform\state\participant_instance\closed as participant_instance_availability_closed;
use mod_perform\state\participant_instance\open as participant_instance_availability_open;
use mod_perform\state\participant_section\closed as participant_section_availability_closed;
use mod_perform\state\participant_section\open as participant_section_availability_open;
use mod_perform\state\subject_instance\closed as subject_instance_availability_closed;
use mod_perform\state\subject_instance\open as subject_instance_availability_open;

class mod_perform_delete_user_testcase extends advanced_testcase {

    public function test_deleting_user_closes_subject_instances() {
        /** @var mod_perform_generator $perform_generator */
        $perform_generator = $this->getDataGenerator()->get_plugin_generator('mod_perform');

        $configuration = mod_perform_activity_generator_configuration::new()
            ->set_number_of_activities(3)
            ->set_number_of_users_per_user_group_type(3)
            ->set_number_of_tracks_per_activity(1)
            ->set_number_of_sections_per_activity(3)
            ->set_cohort_assignments_per_activity(1)
            ->set_number_of_elements_per_section(4)
            ->set_relationships_per_section(
                [
                    constants::RELATIONSHIP_SUBJECT,
                    constants::RELATIONSHIP_MANAGER,
                    constants::RELATIONSHIP_APPRAISER
                ]
            )
            ->enable_manager_for_each_subject_user()
            ->enable_appraiser_for_each_subject_user();

        $perform_generator->create_full_activities($configuration);

        /** @var subject_instance $subject_instance */
        $subject_instance = subject_instance::repository()
            ->order_by('id')
            ->first();

        $this->assertEquals(subject_instance_availability_open::get_code(), $subject_instance->availability);

        foreach ($subject_instance->participant_instances()->get() as $participant_instance) {
            $this->assertEquals(participant_instance_availability_open::get_code(), $participant_instance->availability);
            foreach ($participant_instance->participant_sections()->get() as $participant_section) {
                $this->assertEquals(participant_section_availability_open::get_code(), $participant_instance->availability);
            }
        }

        // Get another control instance
        $subject_instance2 = subject_instance::repository()
            ->where('subject_user_id', '<>', $subject_instance->subject_user_id)
            ->order_by('id')
            ->first();

        $this->assertEquals(subject_instance_availability_open::get_code(), $subject_instance2->availability);

        // DELETE the user. This should close all their subject instances
        delete_user($subject_instance->subject_user->get_record());

        $subject_instance->refresh();

        $this->assertEquals(subject_instance_availability_closed::get_code(), $subject_instance->availability);

        foreach ($subject_instance->participant_instances()->get() as $participant_instance) {
            $this->assertEquals(participant_instance_availability_closed::get_code(), $participant_instance->availability);
            foreach ($participant_instance->participant_sections()->get() as $participant_section) {
                $this->assertEquals(participant_section_availability_closed::get_code(), $participant_instance->availability);
            }
        }

        // The other instance should not be affected
        $subject_instance2->refresh();

        $this->assertEquals(subject_instance_availability_open::get_code(), $subject_instance2->availability);
    }

    public function test_deleting_user_closes_participant_instances() {
        /** @var mod_perform_generator $perform_generator */
        $perform_generator = $this->getDataGenerator()->get_plugin_generator('mod_perform');

        $configuration = mod_perform_activity_generator_configuration::new()
            ->set_number_of_activities(3)
            ->set_number_of_users_per_user_group_type(3)
            ->set_number_of_tracks_per_activity(1)
            ->set_number_of_sections_per_activity(3)
            ->set_cohort_assignments_per_activity(1)
            ->set_number_of_elements_per_section(4)
            ->set_relationships_per_section(
                [
                    constants::RELATIONSHIP_SUBJECT,
                    constants::RELATIONSHIP_MANAGER,
                    constants::RELATIONSHIP_APPRAISER
                ]
            )
            ->enable_manager_for_each_subject_user()
            ->enable_appraiser_for_each_subject_user();

        $perform_generator->create_full_activities($configuration);

        /** @var subject_instance $subject_instance */
        $subject_instance = subject_instance::repository()
            ->order_by('id')
            ->first();

        /** @var participant_instance $participant_instance */
        $participant_instance = $subject_instance->participant_instances()
            ->where('participant_id', '<>', $subject_instance->subject_user_id)
            ->where('participant_source', participant_source::INTERNAL)
            ->order_by('id')
            ->first();

        $this->assertEquals(participant_instance_availability_open::get_code(), $participant_instance->availability);

        foreach ($participant_instance->participant_sections()->get() as $participant_section) {
            $this->assertEquals(participant_section_availability_open::get_code(), $participant_instance->availability);
        }

        // Get another control instance
        /** @var participant_instance $participant_instance2 */
        $participant_instance2 = $subject_instance->participant_instances()
            ->where('participant_id', '<>', $subject_instance->subject_user_id)
            ->where('participant_id', '<>', $participant_instance->participant_id)
            ->where('participant_source', participant_source::INTERNAL)
            ->order_by('id')
            ->first();

        $this->assertEquals(participant_instance_availability_open::get_code(), $participant_instance2->availability);

        // DELETE the user. This should close all their participant instances
        delete_user($participant_instance->participant_user->get_record());

        $participant_instances = participant_instance::repository()
            ->where('participant_id', $participant_instance->participant_id)
            ->where('participant_source', participant_source::INTERNAL)
            ->get();

        foreach ($participant_instances as $participant_instance) {
            $this->assertEquals(participant_instance_availability_closed::get_code(), $participant_instance->availability);
        }

        // The other instance should not be affected
        $participant_instance2->refresh();

        $this->assertEquals(participant_instance_availability_open::get_code(), $participant_instance2->availability);
    }

}