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
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Nathan Lewis <nathan.lewis@totaralearning.com>
 * @package mod_perform
 */

/**
 * @group perform
 */

use core\webapi\execution_context;
use mod_perform\entities\activity\subject_instance;
use mod_perform\models\activity\section;
use mod_perform\models\activity\activity;
use mod_perform\entities\activity\participant_section as participant_section_entity;
use mod_perform\webapi\resolver\query\participant_section;
use totara_webapi\graphql;

class mod_perform_webapi_resolver_query_participant_section_testcase extends advanced_testcase {

    public function test_get_participant_section(): void {
        self::setAdminUser();

        $data_generator = self::getDataGenerator();
        /** @var mod_perform_generator $perform_generator */
        $perform_generator = $data_generator->get_plugin_generator('mod_perform');

        /** @var activity $activity */
        $activity = $perform_generator->create_full_activities()->first();
        /** @var section $section */
        $section = $activity->sections->first();

        $element = $perform_generator->create_element();
        $section_element = $perform_generator->create_section_element($section, $element);

        /** @var participant_section_entity[] $participant_sections */
        $participant_sections = participant_section_entity::repository()->order_by('id', 'desc')->get();

        foreach ($participant_sections as $participant_section) {
            self::setUser($participant_section->participant_instance->participant_user->id);

            $args = ['participant_instance_id' => $participant_section->participant_instance->id];

            $result = graphql::execute_operation(
                $this->get_execution_context('ajax', 'mod_perform_participant_section'),
                $args
            )->toArray(true)['data']['mod_perform_participant_section'];

            $this->assertEquals($participant_section->id, $result['id']);
            $this->assertSame($participant_section->section->title, $result['section']['title']);
            $this->assertSame('IN_PROGRESS', $result['progress_status']);


            $this->assertCount(1, $result['answerable_participant_instances']);
            $this->assertSame('Subject', $result['answerable_participant_instances'][0]['relationship_name']);

            $section_element_responses = $result['section_element_responses'];

            $this->assertCount(
                1,
                $section_element_responses,
                'Expected one section element'
            );

            $this->assertEquals(
                $this->create_section_element_response($section_element->id),
                $section_element_responses[0]
            );
        }
    }

    private function create_section_element_response(int $section_element_id): array {
        return [
            'section_element_id' => $section_element_id,
            'element' =>
                [
                    'element_plugin' =>
                        [
                            'participant_form_component' =>
                                'performelement_short_text/components/ShortTextElementParticipantForm',
                            'participant_response_component' =>
                                'performelement_short_text/components/ShortTextElementParticipantResponse',
                        ],
                    'title' => 'test element title',
                    'data' => null,
                ],
            'sort_order' => 1,
            'response_data' => null,
            'validation_errors' => [],
            'other_responder_groups' => [],
            'visible_to' => [],
        ];
    }


    /**
     * Helper to get execution context
     *
     * @param string $type
     * @param string|null $operation
     * @return execution_context
     */
    private function get_execution_context(string $type = 'dev', ?string $operation = null): execution_context {
        return execution_context::create($type, $operation);
    }

}