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

use mod_perform\entities\activity\element_response as element_response_entity;
use mod_perform\entities\activity\participant_instance as participant_instance_entity;
use mod_perform\entities\activity\section as section_entity;
use mod_perform\entities\activity\section_element as section_element_entity;
use mod_perform\models\activity\element_plugin;
use mod_perform\models\response\section_element_response;
use mod_perform\models\response\element_validation_error;
use performelement_short_text\answer_length_exceeded_error;

/**
 * @group perform
 */
class mod_perform_response_model_testcase extends advanced_testcase {

    /**
     * @dataProvider constructor_only_allows_responses_entities_related_to_others_provider
     * @param participant_instance_entity $participant_instance_entity
     * @param section_element_entity $section_element_entity
     * @param string $expected_message
     * @throws coding_exception
     */
    public function test_constructor_does_not_allow_responses_entities_not_related_to_others(
        participant_instance_entity $participant_instance_entity,
        section_element_entity $section_element_entity,
        string $expected_message
    ): void {

        $element_response_entity = new element_response_entity();
        $element_response_entity->participant_instance_id = 1;
        $element_response_entity->section_element_id = 1;

        $this->expectException(coding_exception::class);
        $this->expectExceptionMessage($expected_message);

        new section_element_response(
            $participant_instance_entity,
            $section_element_entity,
            $element_response_entity,
            new collection()
        );
    }

    public function constructor_only_allows_responses_entities_related_to_others_provider(): array {
        $matching_participant_instance = new participant_instance_entity(['id' => 1]);
        $not_matching_participant_instance = new participant_instance_entity(['id' => 2]);

        $matching_section_element = new section_element_entity(['id' => 1]);
        $not_matching_section_element = new section_element_entity(['id' => 2]);

        return [
            'Participant instance does not match element response' => [
                $not_matching_participant_instance,
                $matching_section_element,
                'participant_instance_id'
            ],
            'Section element does not match element response' => [
                $matching_participant_instance,
                $not_matching_section_element,
                'section_element_id'
            ],
        ];
    }

    /**
     * @throws coding_exception
     */
    public function test_saving_supports_elements_that_have_not_been_responded_to(): void {
        self::setAdminUser();

        $subject = self::getDataGenerator()->create_user();
        $participant = self::getDataGenerator()->create_user();

        /** @var mod_perform_generator $generator */
        $generator = self::getDataGenerator()->get_plugin_generator('mod_perform');

        $subject_instance = $generator->create_subject_instance([
            'subject_is_participating' => true,
            'subject_user_id' => $subject->id,
            'other_participant_id' => $participant->id,
            'include_questions' => true,
        ]);

        $participant_instance = $subject_instance->participant_instances->first();

        $track = $subject_instance->track;
        $activity = $track->activity;
        $sections = $activity->sections;
        /** @var section_entity $section */
        foreach ($sections as $section) {
            /** @var section_element_entity $section_element */
            $section_elements = $section->section_elements;
            foreach ($section_elements as $section_element) {
                $element_type = $section_element->element;
                if ($element_type) {
                    break 2;
                }
            }
        }
        $element = element_plugin::load_by_plugin($element_type->plugin_name);

        $element_response = new section_element_response($participant_instance,
            $section_element,
            null,
            new collection(),
            $element
        );

        $element_response->save();

        $element_response_entity = new element_response_entity($element_response->get_id());

        // Saving when a response record has not yet been created will create the record with the foreign keys
        // pulled from the participant_instance and section_element.
        self::assertEquals($participant_instance->id, $element_response_entity->participant_instance_id);
        self::assertEquals($section_element->id, $element_response_entity->section_element_id);
    }

    /**
     * @throws coding_exception
     */
    public function test_validation_success(): void {
        self::setAdminUser();

        $subject = self::getDataGenerator()->create_user();
        $participant = self::getDataGenerator()->create_user();

        /** @var mod_perform_generator $generator */
        $generator = self::getDataGenerator()->get_plugin_generator('mod_perform');

        $subject_instance = $generator->create_subject_instance([
            'subject_is_participating' => true,
            'subject_user_id' => $subject->id,
            'other_participant_id' => $participant->id,
            'include_questions' => true,
        ]);

        $participant_instance = $subject_instance->participant_instances->first();

        $track = $subject_instance->track;
        $activity = $track->activity;
        $sections = $activity->sections;
        /** @var section_entity $section */
        foreach ($sections as $section) {
            /** @var section_element_entity $section_element */
            $section_elements = $section->section_elements;
            foreach ($section_elements as $section_element) {
                $element_type = $section_element->element;
                if ($element_type) {
                    break 2;
                }
            }
        }
        $element = element_plugin::load_by_plugin($section_element->element->plugin_name);

        $element_response = new section_element_response($participant_instance,
            $section_element,
            null,
            new collection(),
            $element
        );

        $response_data = ['answer_text' => 'Hello there.'];

        $element_response->set_response_data(json_encode($response_data));

        self::assertTrue($element_response->validate_response());
    }

    /**
     * @throws coding_exception
     */
    public function test_validation_with_errors(): void {
        self::setAdminUser();

        $subject = self::getDataGenerator()->create_user();
        $participant = self::getDataGenerator()->create_user();

        /** @var mod_perform_generator $generator */
        $generator = self::getDataGenerator()->get_plugin_generator('mod_perform');

        $subject_instance = $generator->create_subject_instance([
            'subject_is_participating' => true,
            'subject_user_id' => $subject->id,
            'other_participant_id' => $participant->id,
            'include_questions' => true,
        ]);

        $participant_instance = $subject_instance->participant_instances->first();

        $track = $subject_instance->track;
        $activity = $track->activity;
        $sections = $activity->sections;
        /** @var section_entity $section */
        foreach ($sections as $section) {
            /** @var section_element_entity $section_element */
            $section_elements = $section->section_elements;
            foreach ($section_elements as $section_element) {
                $element_type = $section_element->element;
                if ($element_type) {
                    break 2;
                }
            }
        }
        $element = element_plugin::load_by_plugin($section_element->element->plugin_name);

        $element_response = new section_element_response($participant_instance,
            $section_element,
            null,
            new collection(),
            $element
        );

        // Structurally valid response, but will fail validation for being too long.
        $response_data = ['answer_text' => str_repeat('x', 1025)];

        $element_response->set_response_data(json_encode($response_data));

        self::assertFalse($element_response->validate_response());

        /** @var element_validation_error[] $validation_errors */
        $validation_errors = $element_response->get_validation_errors()->all();

        self::assertCount(1, $validation_errors);

        self::assertEquals('Question text exceeds the maximum length', $validation_errors[0]->error_message);
        self::assertEquals(answer_length_exceeded_error::LENGTH_EXCEEDED, $validation_errors[0]->error_code);
    }
}
