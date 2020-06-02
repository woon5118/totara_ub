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

    public function test_get_participant_section(): void {
        [$participant_sections, $section_element] = $this->create_test_data();

        foreach ($participant_sections as $participant_section) {
            self::setUser($participant_section->participant_instance->participant_user->id);

            $args = ['participant_instance_id' => $participant_section->participant_instance->id];

            $result = $this->parsed_graphql_operation(self::QUERY, $args);
            $this->assert_webapi_operation_successful($result);

            $result = $this->get_webapi_operation_data($result);
            $this->assertEquals($participant_section->id, $result['id']);
            $this->assertSame($participant_section->section->title, $result['section']['title']);
            $this->assertSame('IN_PROGRESS', $result['progress_status']);


            $this->assertCount(1, $result['answerable_participant_instances']);
            $this->assertSame('Subject', $result['answerable_participant_instances'][0]['core_relationship']['name']);

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

    public function test_failed_ajax_query(): void {
        [$participants, ] = $this->create_test_data();
        $participant_instance = $participants->first()->participant_instance;
        $args = ['participant_instance_id' => $participant_instance->id];

        $feature = 'performance_activities';
        advanced_feature::disable($feature);
        $result = $this->parsed_graphql_operation(self::QUERY, $args);
        $this->assert_webapi_operation_failed($result, 'Feature performance_activities is not available.');
        advanced_feature::enable($feature);

        $result = $this->parsed_graphql_operation(self::QUERY, []);
        $this->assert_webapi_operation_failed($result, 'participant_instance_id');

        $result = $this->parsed_graphql_operation(self::QUERY, ['participant_instance_id' => 0]);
        $this->assert_webapi_operation_failed($result, 'participant instance id');

        $result = $this->parsed_graphql_operation(self::QUERY, ['participant_instance_id' => 1293]);
        $this->assert_webapi_operation_failed($result, "No participant section");

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

        $participant_section = participant_section_entity::repository()
            ->order_by('id', 'desc')
            ->get();

        return [$participant_section, $section_element];
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
}