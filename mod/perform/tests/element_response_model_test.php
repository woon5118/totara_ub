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
use mod_perform\entities\activity\section_element as section_element_entity;
use mod_perform\models\activity\element_plugin;
use mod_perform\models\response\section_element_response;
use mod_perform\models\response\element_validation_error;

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

        new section_element_response($participant_instance_entity, $section_element_entity, $element_response_entity);
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
        $participant_instance = new participant_instance_entity(['id' => 100]);
        $section_element = new section_element_entity(['id' => 200]);

        $element_response = new section_element_response($participant_instance,
            $section_element,
            null,
            null,
            $this->create_element_plugin_stub()
        );

        $element_response->save();

        $element_response_entity = new element_response_entity($element_response->get_id());

        // Saving when a response record has not yet been created will create the record with the foreign keys
        // pulled from the participant_instance and section_element.
        self::assertEquals(100, $element_response_entity->participant_instance_id);
        self::assertEquals(200, $element_response_entity->section_element_id);
    }

    /**
     * @throws coding_exception
     */
    public function test_validation_success(): void {
        $participant_instance = new participant_instance_entity(['id' => 100]);
        $section_element = new section_element_entity(['id' => 200]);

        $element_response = new section_element_response($participant_instance,
            $section_element,
            null,
            null,
            $this->create_element_plugin_stub()
        );

        $response_data = ['answer_text' => 'Hello there.'];

        $element_response->set_response_data(json_encode($response_data));

        self::assertTrue($element_response->validate_response());
        self::assertEmpty($element_response->get_validation_errors()->all());
    }

    /**
     * @throws coding_exception
     */
    public function test_validation_with_errors(): void {
        $participant_instance = new participant_instance_entity(['id' => 100]);
        $section_element = new section_element_entity(['id' => 200]);

        $element_response = new section_element_response($participant_instance,
            $section_element,
            null,
            null,
            $this->create_element_plugin_with_validation_errors()
        );

        $response_data = ['answer_text' => 'Hello there.'];

        $element_response->set_response_data(json_encode($response_data));

        self::assertFalse($element_response->validate_response());

        /** @var element_validation_error[] $validation_errors */
        $validation_errors = $element_response->get_validation_errors()->all();

        self::assertNotEmpty($validation_errors, 'Expected validation errors, none were generated');

        self::assertEquals($validation_errors[0]->error_message, 'There was a problem.');
        self::assertEquals($validation_errors[0]->error_code, 1);

        self::assertEquals($validation_errors[1]->error_message, 'There was another problem.');
        self::assertEquals($validation_errors[1]->error_code, 2);
    }

    private function create_element_plugin_stub(): element_plugin {
        return new class extends element_plugin {
            public function __construct() {
            }
        };
    }

    private function create_element_plugin_with_validation_errors(): element_plugin {
        return new class extends element_plugin {
            public function __construct() {
            }

            public function validate_response(?string $encoded_response_data): collection {
                $error = new element_validation_error(1, 'There was a problem.');
                $error2 = new element_validation_error(2, 'There was another problem.');

                return new collection([$error, $error2]);
            }
        };
    }

}
