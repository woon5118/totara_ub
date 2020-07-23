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
* along with this program.  If not, see <http://www.gnu.org/licenses/>.
*
* @author Oleg Demeshev <oleg.demeshev@totaralearning.com>
* @package mod_perform
*/

use core\entities\user;
use mod_perform\data_providers\response\participant_section_with_responses;
use mod_perform\entities\activity\activity as activity_entity;
use mod_perform\entities\activity\participant_section as participant_section_entity;
use mod_perform\models\activity\activity;
use mod_perform\models\response\participant_section;
use mod_perform\models\response\responder_group;
use mod_perform\models\response\section_element_response;
use mod_perform\userdata\export_user_responses;
use totara_userdata\userdata\target_user;

class mod_perform_userdata_export_user_responses_testcase  extends advanced_testcase {

    public function test_count(): void {
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

        $data_provider = new participant_section_with_responses($subject->id, $participant_section->id);

        $responses = $data_provider->fetch()->get()->get_section_element_responses();
        // Set answers on each question.
        foreach ($responses->all(false) as $question_number => $response) {
            $response->set_response_data($question_number);
            $response->save();
        }
        $responses = $data_provider->fetch()->get()->get_section_element_responses();

        $targetuser1 = new target_user($subject);
        /** @var activity_entity $activity */
        $perform = activity_entity::repository()
            ->where('name', 'test performance activity')
            ->one(true);
        $activity = activity::load_by_entity($perform);
        $coursemodule = get_coursemodule_from_instance('perform', $activity->get_id());

        // System context
        $this->assertEquals($responses->count(), export_user_responses::execute_count($targetuser1, context_system::instance()));
        // Module context
        $modulecontext = context_module::instance($coursemodule->id);
        $this->assertEquals($responses->count(), export_user_responses::execute_count($targetuser1, $modulecontext));
        // Course context
        $coursecontext = context_course::instance($coursemodule->course);
        $this->assertEquals($responses->count(), export_user_responses::execute_count($targetuser1, $coursecontext));
        // Course category context
        $course = get_course($coursemodule->course);
        $coursecatcontext = context_coursecat::instance($course->category);
        $this->assertEquals($responses->count(), export_user_responses::execute_count($targetuser1, $coursecatcontext));
    }

    public function test_export() {
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

        $data_provider = new participant_section_with_responses($subject->id, $participant_section->id);

        $responses = $data_provider->fetch()->get()->get_section_element_responses();
        // Set answers on each question.
        foreach ($responses->all(false) as $question_number => $response) {
            $response->set_response_data($question_number);
            $response->save();
        }
        $responses = $data_provider->fetch()->get()->get_section_element_responses();

        $targetuser1 = new target_user($subject);
        $export = export_user_responses::execute_export($targetuser1, context_system::instance());
        $data = $export->data;

        $this->assertCount(2, $data);
        $i = 0;
        foreach ($responses->all(false) as $question_number => $response) {
            $this->assertEquals($data[$i]['response_data'], $response->response_data);
            $this->assertEquals($data[$i]['element_response_id'], $response->get_id());
            $i++;
        }
    }

    public function test_export_others_responses(): void {
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

        $data_provider = new participant_section_with_responses($subject->id, $participant_section->id);

        $responses =  $data_provider->fetch()->get()->get_section_element_responses();

        // Set the manager's response on each question.
        foreach ($responses->all(false) as $question_number => $response) {
            $other_responder_groups = $response->get_other_responder_groups();
            /** @var responder_group $manager_response_group */
            $manager_response_group = $other_responder_groups->first();
            $manager_responses =  $manager_response_group->get_responses();
            /** @var section_element_response $manager_response */
            $manager_response = $manager_responses->first();
            $manager_response->set_response_data($question_number);
            $manager_response->save();
        }

        $manager = $manager_response->get_participant_instance()->get_participant();
        $responses =  $data_provider->fetch()->get()->get_section_element_responses();
        self::assertCount(2, $responses);

        $user = \core_user::get_user($manager->id);
        $targetuser1 = new target_user($user);
        $export = export_user_responses::execute_export($targetuser1, context_system::instance());
        $data = $export->data;
        $this->assertCount(2, $data);

        $i = 0;
        foreach ($responses->all(false) as $question_number => $response) {
            $other_responder_groups = $response->get_other_responder_groups();
            /** @var responder_group $manager_response_group */
            $manager_response_group = $other_responder_groups->first();
            $manager_responses =  $manager_response_group->get_responses();
            /** @var section_element_response $manager_response */
            $manager_response = $manager_responses->first();
            $this->assertEquals($data[$i]['response_data'], $manager_response->response_data);
            $this->assertEquals($data[$i]['element_response_id'], $manager_response->get_id());
            $i++;
        }
    }
}