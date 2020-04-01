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
use mod_perform\models\activity\section;
use mod_perform\models\activity\activity;
use mod_perform\entities\activity\participant_section as participant_section_entity;
use totara_webapi\graphql;

class mod_perform_webapi_resolver_query_participant_section_testcase extends advanced_testcase {

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

    public function test_get_participant_section(): void {
        $this->setAdminUser();

        $data_generator = $this->getDataGenerator();
        /** @var mod_perform_generator $perform_generator */
        $perform_generator = $data_generator->get_plugin_generator('mod_perform');

        /** @var activity $activity */
        $activity = $perform_generator->create_full_activities()->first();
        /** @var section $section */
        $section = $activity->sections->first();

        $element = $perform_generator->create_element();
        $section_element = $perform_generator->create_section_element($section, $element);

        /** @var participant_section_entity[] $participant_sections_entities */
        $participant_sections_entities = participant_section_entity::repository()->get();

        foreach ($participant_sections_entities as $participant_section_entity) {
            $subject_instance = $participant_section_entity->participant_instance->subject_instance;

            $this->setUser($subject_instance->subject_user_id);

            $args = ['subject_instance_id' => $subject_instance->id];

            $result = graphql::execute_operation(
                $this->get_execution_context('ajax', 'mod_perform_participant_section'),
                $args
            )->toArray(true)['data']['mod_perform_participant_section'];

            $this->assertEquals($participant_section_entity->id, $result['id']);
            $this->assertSame($participant_section_entity->section->title, $result['section']['title']);

            $result_section_elements = $result['section']['section_elements'];

            $this->assertCount(1, $result_section_elements, 'Expected one section element');
            $this->assertEquals($section_element->id, $result_section_elements[0]['id']);
            $this->assertEquals($section_element->sort_order, $result_section_elements[0]['sort_order']);
            $this->assertSame($section_element->element->title, $result_section_elements[0]['element']['title']);
        }
    }

}