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

use mod_perform\models\activity\section;
use mod_perform\models\activity\activity;
use mod_perform\entities\activity\participant_section as participant_section_entity;
use mod_perform\webapi\resolver\query\participant_section;
use totara_core\advanced_feature;
use totara_webapi\phpunit\webapi_phpunit_helper;

/**
 * @coversDefaultClass participant_section
 *
 * @group perform
 */
class mod_perform_webapi_resolver_query_participant_section_testcase extends advanced_testcase {
    private const QUERY = 'mod_perform_participant_section';

    use webapi_phpunit_helper;

    public function test_get_participant_section_by_participant_instance_id(): void {
        [$participant_sections, $section_element, $static_section_element] = $this->create_test_data();

        foreach ($participant_sections as $participant_section) {
            self::setUser($participant_section->participant_instance->participant_user->id);

            $args = ['participant_instance_id' => $participant_section->participant_instance->id];

            $this->assert_query_success($args, $participant_section, $section_element, $static_section_element);
        }
    }

    public function test_get_participant_section_by_participant_section_id(): void {
        [$participant_sections, $section_element, $static_section_element] = $this->create_test_data();

        foreach ($participant_sections as $participant_section) {
            self::setUser($participant_section->participant_instance->participant_user->id);

            $args = ['participant_section_id' => $participant_section->id];

            $this->assert_query_success($args, $participant_section, $section_element, $static_section_element);
        }
    }

    public function test_get_participant_section_by_participant_section_id_and_participant_instance_id(): void {
        [$participant_sections, $section_element, $static_section_element] = $this->create_test_data();

        foreach ($participant_sections as $participant_section) {
            self::setUser($participant_section->participant_instance->participant_user->id);

            $args = [
                'participant_instance_id' => $participant_section->participant_instance->id,
                'participant_section_id'  => $participant_section->id,
            ];

            $this->assert_query_success($args, $participant_section, $section_element, $static_section_element);
        }
    }

    public function test_failed_ajax_query(): void {
        [$participant_sections,] = $this->create_test_data();
        $participant_instance = $participant_sections->first()->participant_instance;
        $args = ['participant_instance_id' => $participant_instance->id];

        $feature = 'performance_activities';
        advanced_feature::disable($feature);
        $result = $this->parsed_graphql_operation(self::QUERY, $args);
        $this->assert_webapi_operation_failed($result, 'Feature performance_activities is not available.');
        advanced_feature::enable($feature);

        // fail if does not provide any parameter
        $result = $this->parsed_graphql_operation(self::QUERY, []);
        $this->assert_webapi_operation_failed(
            $result, 'At least one parameter is required, either participant_instance_id or participant_section_id'
        );

        // fail if participant_instance_id is invalid
        $result = $this->parsed_graphql_operation(self::QUERY, ['participant_instance_id' => 0]);
        $this->assert_webapi_operation_failed(
            $result, 'At least one parameter is required, either participant_instance_id or participant_section_id'
        );

        // fail if participant_section_id is invalid
        $result = $this->parsed_graphql_operation(self::QUERY, ['participant_section_id' => 0]);
        $this->assert_webapi_operation_failed(
            $result, 'At least one parameter is required, either participant_instance_id or participant_section_id'
        );

        // fail if not participant section related to the participant_instance_id
        $result = $this->parsed_graphql_operation(self::QUERY, ['participant_instance_id' => 1293]);
        $this->assert_webapi_operation_failed($result, "No participant section");

        // If the section does not exist we return an empty result
        $result = $this->parsed_graphql_operation(self::QUERY, ['participant_section_id' => 1293]);
        $this->assert_webapi_operation_successful($result);
        [$data, ] = $result;
        $this->assertNull($data);

        $this->setUser();
        $result = $this->parsed_graphql_operation(self::QUERY, $args);
        $this->assert_webapi_operation_failed($result, 'not logged in');
    }

    private function create_test_data(): array {
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

        $static_element = $perform_generator->create_element(['plugin_name' => 'static_content']);
        $static_section_element = $perform_generator->create_section_element($section, $static_element);

        $participant_sections = participant_section_entity::repository()
            ->order_by('id', 'desc')
            ->get();

        return [$participant_sections, $section_element, $static_section_element];
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
                    'is_required' => false,
                    'is_respondable' => true,
                ],
            'sort_order' => 1,
            'response_data' => null,
            'validation_errors' => [],
            'other_responder_groups' => [],
            'visible_to' => [],
        ];
    }

    private function create_static_section_element_response(int $section_element_id): array {
        return [
            'section_element_id' => $section_element_id,
            'element' =>
                [
                    'element_plugin' =>
                        [
                            'participant_form_component' =>
                                'performelement_static_content/components/StaticContentElementParticipant',
                            'participant_response_component' =>
                                'performelement_static_content/components/StaticContentElementParticipant',
                        ],
                    'title' => 'test element title',
                    'data' => null,
                    'is_required' => false,
                    'is_respondable' => false,
                ],
            'sort_order' => 2,
            'response_data' => null,
            'validation_errors' => [],
            'other_responder_groups' => [],
            'visible_to' => [],
        ];
    }

    /**
     * Check participant section query success
     *
     * @param array $args
     * @param $participant_section
     * @param $section_element
     * @param $static_section_element
     */
    private function assert_query_success(array $args, $participant_section, $section_element, $static_section_element): void {
        $result = $this->parsed_graphql_operation(self::QUERY, $args);
        $this->assert_webapi_operation_successful($result);

        $result = $this->get_webapi_operation_data($result);
        $this->assertEquals($participant_section->id, $result['id']);
        $this->assertSame($participant_section->section->title, $result['section']['display_title']);
        $this->assertSame('IN_PROGRESS', $result['progress_status']);


        $this->assertCount(1, $result['answerable_participant_instances']);
        $this->assertSame('Subject', $result['answerable_participant_instances'][0]['core_relationship']['name']);

        $section_element_responses = $result['section_element_responses'];

        $this->assertCount(
            2,
            $section_element_responses,
            'Expected one section element'
        );

        $this->assertContains(
            $this->create_section_element_response($section_element->id),
            $section_element_responses
        );

        $this->assertContains(
            $this->create_static_section_element_response($static_section_element->id),
            $section_element_responses
        );
    }
}