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
use mod_perform\webapi\resolver\mutation\update_section_elements;
use totara_webapi\phpunit\webapi_phpunit_helper;

/**
 * @coversDefaultClass update_section_elements.
 *
 * @group perform
 * Tests the mutation to add, update and delete section elements
 */
class mod_perform_webapi_resolver_mutation_update_section_elements_testcase extends advanced_testcase {
    private const MUTATION = 'mod_perform_update_section_elements';

    use webapi_phpunit_helper;

    public function test_create_new_section_elements(): void {
        global $DB;

        self::setAdminUser();

        /** @var mod_perform_generator $perform_generator */
        $perform_generator = self::getDataGenerator()->get_plugin_generator('mod_perform');

        $activity = $perform_generator->create_activity_in_container();
        $section = $perform_generator->create_section($activity);

        $args = [
            'input' => [
                'section_id' => $section->id,
                'create_new' => [
                    [
                        'plugin_name' => 'short_text',
                        'title' => 'Test title 1',
                        'data' => 'aaa',
                        'is_required' => true,
                        'sort_order' => 2,
                    ],
                    [
                        'plugin_name' => 'short_text',
                        'title' => 'Test title 2',
                        'data' => 'bbb',
                        'is_required' => true,
                        'sort_order' => 1,
                    ],
                ],
            ]
        ];

        $result = $this->resolve_graphql_mutation(self::MUTATION, $args);

        // Check that the changes were made.
        $section_element_records = $DB->get_records('perform_section_element', ['section_id' => $section->id]);
        $this->assertCount(2, $section_element_records);

        $section_elements = [];
        foreach ($section_element_records as $section_element_record) {
            $section_elements[$section_element_record->sort_order] = $section_element_record;
        }

        $element1 = $DB->get_record('perform_element', ['id' => $section_elements[1]->element_id]);
        $this->assertEquals('Test title 2', $element1->title);
        $this->assertEquals('bbb', $element1->data);
        $this->assertEquals(1, $element1->is_required);

        $element2 = $DB->get_record('perform_element', ['id' => $section_elements[2]->element_id]);
        $this->assertEquals('Test title 1', $element2->title);
        $this->assertEquals('aaa', $element2->data);
        $this->assertEquals(1, $element2->is_required);

        $this->assertEquals($section->id, $section_elements[1]->section_id);
        $this->assertEquals($element1->id, $section_elements[1]->element_id);
        $this->assertEquals(1, $section_elements[1]->sort_order);

        $this->assertEquals($section->id, $section_elements[2]->section_id);
        $this->assertEquals($element2->id, $section_elements[2]->element_id);
        $this->assertEquals(2, $section_elements[2]->sort_order);

        // Check that the result is correct (good enough).
        /** @var section $result_section */
        $result_section = $result['section'];
        $this->assertEquals($section->id, $result_section->id);
        $this->assertCount(2, $result_section->get_section_elements());
    }

    public function test_create_linked_section_elements(): void {
        global $DB;

        self::setAdminUser();

        /** @var mod_perform_generator $perform_generator */
        $perform_generator = self::getDataGenerator()->get_plugin_generator('mod_perform');

        $activity = $perform_generator->create_activity_in_container();
        $section = $perform_generator->create_section($activity);
        $element1 = $perform_generator->create_element([
            'title' => 'Test title 1',
            'data' => 'aaa',
            'identifier' => 111,
        ]);
        $element2 = $perform_generator->create_element([
            'title' => 'Test title 2',
            'data' => 'bbb',
            'identifier' => 222,
        ]);

        $args = [
            'input' => [
                'section_id' => $section->id,
                'create_link' => [
                    [
                        'element_id' => $element1->id,
                        'sort_order' => 2,
                    ],
                    [
                        'element_id' => $element2->id,
                        'sort_order' => 1,
                    ],
                ],
            ]
        ];

        $result = $this->resolve_graphql_mutation(self::MUTATION, $args);

        // Check that the changes were made.
        $section_element_records = $DB->get_records('perform_section_element', ['section_id' => $section->id]);
        $this->assertCount(2, $section_element_records);

        $section_elements = [];
        foreach ($section_element_records as $section_element_record) {
            $section_elements[$section_element_record->sort_order] = $section_element_record;
        }

        $element1 = $DB->get_record('perform_element', ['id' => $section_elements[1]->element_id]);
        $this->assertEquals('Test title 2', $element1->title);
        $this->assertEquals('bbb', $element1->data);

        $element2 = $DB->get_record('perform_element', ['id' => $section_elements[2]->element_id]);
        $this->assertEquals('Test title 1', $element2->title);
        $this->assertEquals('aaa', $element2->data);

        $this->assertEquals($section->id, $section_elements[1]->section_id);
        $this->assertEquals($element1->id, $section_elements[1]->element_id);
        $this->assertEquals(1, $section_elements[1]->sort_order);

        $this->assertEquals($section->id, $section_elements[2]->section_id);
        $this->assertEquals($element2->id, $section_elements[2]->element_id);
        $this->assertEquals(2, $section_elements[2]->sort_order);

        // Check that the result is correct (good enough).
        /** @var section $result_section */
        $result_section = $result['section'];
        $this->assertEquals($section->id, $result_section->id);
        $this->assertCount(2, $result_section->get_section_elements());
    }

    public function test_update_elements(): void {
        global $DB;

        self::setAdminUser();

        /** @var mod_perform_generator $perform_generator */
        $perform_generator = self::getDataGenerator()->get_plugin_generator('mod_perform');

        $activity = $perform_generator->create_activity_in_container();
        $section = $perform_generator->create_section($activity);
        $element1 = $perform_generator->create_element([
            'title' => 'Test title 1',
            'data' => 'aaa',
            'identifier' => 111,
        ]);
        $element2 = $perform_generator->create_element([
            'title' => 'Test title 2',
            'data' => 'bbb',
            'identifier' => 222,
        ]);

        $args = [
            'input' => [
                'section_id' => $section->id,
                'create_link' => [
                    [
                        'element_id' => $element1->id,
                        'sort_order' => 2,
                    ],
                    [
                        'element_id' => $element2->id,
                        'sort_order' => 1,
                    ],
                ],
            ]
        ];
        // Section sort order 1 => element 2 (title 2), section sort order 2 => element 1 (title 1).

        $result = $this->resolve_graphql_mutation(self::MUTATION, $args);

        $args = [
            'input' => [
                'section_id' => $section->id,
                'update' => [
                    [
                        'element_id' => $element1->id,
                        'title' => 'Test title 3',
                        'data' => 'ccc',
                    ],
                    [
                        'element_id' => $element2->id,
                        'title' => 'Test title 4',
                        'data' => 'ddd',
                    ],
                ],
            ]
        ];
        // Section sort order 1 => element 2 (title 4), section sort order 2 => element 1 (title 3).

        $result = $this->resolve_graphql_mutation(self::MUTATION, $args);

        // Check that the changes were made.
        $section_element_records = $DB->get_records('perform_section_element', ['section_id' => $section->id]);
        $this->assertCount(2, $section_element_records);

        $section_elements = [];
        foreach ($section_element_records as $section_element_record) {
            $section_elements[$section_element_record->sort_order] = $section_element_record;
        }

        $element1 = $DB->get_record('perform_element', ['id' => $section_elements[1]->element_id]);
        $this->assertEquals('Test title 4', $element1->title);
        $this->assertEquals('ddd', $element1->data);

        $element2 = $DB->get_record('perform_element', ['id' => $section_elements[2]->element_id]);
        $this->assertEquals('Test title 3', $element2->title);
        $this->assertEquals('ccc', $element2->data);

        $this->assertEquals($section->id, $section_elements[1]->section_id);
        $this->assertEquals($element1->id, $section_elements[1]->element_id);
        $this->assertEquals(1, $section_elements[1]->sort_order);

        $this->assertEquals($section->id, $section_elements[2]->section_id);
        $this->assertEquals($element2->id, $section_elements[2]->element_id);
        $this->assertEquals(2, $section_elements[2]->sort_order);

        // Check that the result is correct (good enough).
        /** @var section $result_section */
        $result_section = $result['section'];
        $this->assertEquals($section->id, $result_section->id);
        $this->assertCount(2, $result_section->get_section_elements());
    }

    public function test_delete_section_elements(): void {
        global $DB;

        self::setAdminUser();

        /** @var mod_perform_generator $perform_generator */
        $perform_generator = self::getDataGenerator()->get_plugin_generator('mod_perform');

        $activity = $perform_generator->create_activity_in_container();
        $section = $perform_generator->create_section($activity);

        $args = [
            'input' => [
                'section_id' => $section->id,
                'create_new' => [
                    [
                        'plugin_name' => 'short_text',
                        'title' => 'Test title 1',
                        'data' => 'aaa',
                        'sort_order' => 3,
                    ],
                    [
                        'plugin_name' => 'short_text',
                        'title' => 'Test title 2',
                        'data' => 'bbb',
                        'sort_order' => 1,
                    ],
                    [
                        'plugin_name' => 'short_text',
                        'title' => 'Test title 3',
                        'data' => 'ccc',
                        'sort_order' => 4,
                    ],
                    [
                        'plugin_name' => 'short_text',
                        'title' => 'Test title 4',
                        'data' => 'ddd',
                        'sort_order' => 2,
                    ],
                ],
            ]
        ];

        $result = $this->resolve_graphql_mutation(self::MUTATION, $args);

        $section_elements = $section->get_section_elements()->all(true);

        $args = [
            'input' => [
                'section_id' => $section->id,
                'delete' => [
                    [
                        'section_element_id' => $section_elements[3]->id,
                    ],
                    [
                        'section_element_id' => $section_elements[1]->id,
                    ],
                ],
                [
                    'section_element_id' => $section_elements[2]->id,
                    'sort_order' => 2,
                ],
                [
                    'section_element_id' => $section_elements[4]->id,
                    'sort_order' => 1,
                ],
            ]
        ];

        $result = $this->resolve_graphql_mutation(self::MUTATION, $args);

        // Check that the changes were made.
        $section_element_records = $DB->get_records('perform_section_element', ['section_id' => $section->id]);
        $this->assertCount(2, $section_element_records);

        $section_elements = [];
        foreach ($section_element_records as $section_element_record) {
            $section_elements[$section_element_record->sort_order] = $section_element_record;
        }

        $element1 = $DB->get_record('perform_element', ['id' => $section_elements[1]->element_id]);
        $this->assertEquals('Test title 4', $element1->title);
        $this->assertEquals('ddd', $element1->data);

        $element2 = $DB->get_record('perform_element', ['id' => $section_elements[2]->element_id]);
        $this->assertEquals('Test title 3', $element2->title);
        $this->assertEquals('ccc', $element2->data);

        $this->assertEquals($section->id, $section_elements[1]->section_id);
        $this->assertEquals($element1->id, $section_elements[1]->element_id);
        $this->assertEquals(1, $section_elements[1]->sort_order);

        $this->assertEquals($section->id, $section_elements[2]->section_id);
        $this->assertEquals($element2->id, $section_elements[2]->element_id);
        $this->assertEquals(2, $section_elements[2]->sort_order);

        // Check that the result is correct (good enough).
        /** @var section $result_section */
        $result_section = $result['section'];
        $this->assertEquals($section->id, $result_section->id);
        $this->assertCount(2, $result_section->get_section_elements());
    }

    public function test_move_section_elements(): void {
        global $DB;

        self::setAdminUser();

        /** @var mod_perform_generator $perform_generator */
        $perform_generator = self::getDataGenerator()->get_plugin_generator('mod_perform');

        $activity = $perform_generator->create_activity_in_container();
        $section = $perform_generator->create_section($activity);

        $args = [
            'input' => [
                'section_id' => $section->id,
                'create_new' => [
                    [
                        'plugin_name' => 'short_text',
                        'title' => 'Test title 1',
                        'data' => 'aaa',
                        'sort_order' => 2,
                    ],
                    [
                        'plugin_name' => 'short_text',
                        'title' => 'Test title 2',
                        'data' => 'bbb',
                        'sort_order' => 1,
                    ],
                ],
            ]
        ];

        $result = $this->resolve_graphql_mutation(self::MUTATION, $args);

        $section_elements = $section->get_section_elements()->all(true);

        $args = [
            'input' => [
                'section_id' => $section->id,
                'create_new' => [
                    [
                        'plugin_name' => 'short_text',
                        'title' => 'Test title 3',
                        'data' => 'ccc',
                        'sort_order' => 2,
                    ],
                    [
                        'plugin_name' => 'short_text',
                        'title' => 'Test title 4',
                        'data' => 'ddd',
                        'sort_order' => 4,
                    ],
                ],
                'move' => [
                    [
                        'section_element_id' => $section_elements[1]->id,
                        'sort_order' => 3,
                    ],
                    [
                        'section_element_id' => $section_elements[2]->id,
                        'sort_order' => 1,
                    ],
                ],
            ]
        ];

        $result = $this->resolve_graphql_mutation(self::MUTATION, $args);

        // Check that the changes were made.
        $section_element_records = $DB->get_records('perform_section_element', ['section_id' => $section->id]);
        $this->assertCount(4, $section_element_records);

        $section_elements = [];
        foreach ($section_element_records as $section_element_record) {
            $section_elements[$section_element_record->sort_order] = $section_element_record;
        }

        $element1 = $DB->get_record('perform_element', ['id' => $section_elements[1]->element_id]);
        $this->assertEquals('Test title 1', $element1->title);
        $this->assertEquals('aaa', $element1->data);

        $element2 = $DB->get_record('perform_element', ['id' => $section_elements[2]->element_id]);
        $this->assertEquals('Test title 3', $element2->title);
        $this->assertEquals('ccc', $element2->data);

        $element3 = $DB->get_record('perform_element', ['id' => $section_elements[3]->element_id]);
        $this->assertEquals('Test title 2', $element3->title);
        $this->assertEquals('bbb', $element3->data);

        $element4 = $DB->get_record('perform_element', ['id' => $section_elements[4]->element_id]);
        $this->assertEquals('Test title 4', $element4->title);
        $this->assertEquals('ddd', $element4->data);

        $this->assertEquals($section->id, $section_elements[1]->section_id);
        $this->assertEquals($element1->id, $section_elements[1]->element_id);
        $this->assertEquals(1, $section_elements[1]->sort_order);

        $this->assertEquals($section->id, $section_elements[2]->section_id);
        $this->assertEquals($element2->id, $section_elements[2]->element_id);
        $this->assertEquals(2, $section_elements[2]->sort_order);

        $this->assertEquals($section->id, $section_elements[3]->section_id);
        $this->assertEquals($element3->id, $section_elements[3]->element_id);
        $this->assertEquals(3, $section_elements[3]->sort_order);

        $this->assertEquals($section->id, $section_elements[4]->section_id);
        $this->assertEquals($element4->id, $section_elements[4]->element_id);
        $this->assertEquals(4, $section_elements[4]->sort_order);

        // Check that the result is correct (good enough).
        /** @var section $result_section */
        $result_section = $result['section'];
        $this->assertEquals($section->id, $result_section->id);
        $this->assertCount(4, $result_section->get_section_elements());
    }

    /**
     * Test the mutation through the GraphQL stack.
     */
    public function test_combination(): void {
        global $DB;

        self::setAdminUser();

        /** @var mod_perform_generator $perform_generator */
        $perform_generator = self::getDataGenerator()->get_plugin_generator('mod_perform');

        $activity = $perform_generator->create_activity_in_container();
        $section = $perform_generator->create_section($activity);

        $args = [
            'input' => [
                'section_id' => $section->id,
                'create_new' => [
                    [
                        'plugin_name' => 'short_text',
                        'title' => 'Test title 1',
                        'data' => 'aaa',
                        'is_required' => true,
                        'sort_order' => 1,
                    ],
                    [
                        'plugin_name' => 'short_text',
                        'title' => 'Test title 2',
                        'data' => 'bbb',
                        'is_required' => true,
                        'sort_order' => 2,
                    ],
                    [
                        'plugin_name' => 'short_text',
                        'title' => 'Test title 3',
                        'data' => 'ccc',
                        'is_required' => true,
                        'sort_order' => 3,
                    ],
                ],
            ]
        ];

        $result = $this->resolve_graphql_mutation(self::MUTATION, $args);

        $section_elements = $section->get_section_elements()->all(true);

        $element4 = $perform_generator->create_element([
            'title' => 'Test title 5',
            'data' => 'eee',
            'is_required' => true,
            'identifier' => 555,
        ]);

        $args = [
            'input' => [
                'section_id' => $section->id,
                'create_new' => [
                    [
                        'plugin_name' => 'short_text',
                        'title' => 'Test title 4',
                        'data' => 'ddd',
                        'is_required' => true,
                        'sort_order' => 1,
                    ],
                ],
                'create_link' => [
                    [
                        'element_id' => $element4->id,
                        'sort_order' => 2,
                    ],
                ],
                'update' => [
                    [
                        'element_id' => $element4->id,
                        'title' => 'Test title 6',
                        'is_required' => true,
                        'data' => 'fff',
                    ],
                ],
                'delete' => [
                    [
                        'section_element_id' => $section_elements[1]->id,
                    ],
                ],
                'move' => [
                    [
                        'section_element_id' => $section_elements[2]->id,
                        'sort_order' => 4,
                    ],
                    [
                        'section_element_id' => $section_elements[3]->id,
                        'sort_order' => 3,
                    ],
                ],
            ]
        ];

        $result = $this->parsed_graphql_operation(self::MUTATION, $args);
        $this->assert_webapi_operation_successful($result);

        $result_data = $this->get_webapi_operation_data($result);
        $this->assertNotEmpty($result_data, "no result");

        // Check that the changes were made.
        $section_element_records = $DB->get_records('perform_section_element', ['section_id' => $section->id]);
        $this->assertCount(4, $section_element_records);

        $section_elements = [];
        foreach ($section_element_records as $section_element_record) {
            $section_elements[$section_element_record->sort_order] = $section_element_record;
        }

        $element1 = $DB->get_record('perform_element', ['id' => $section_elements[1]->element_id]);
        $this->assertEquals('Test title 4', $element1->title);
        $this->assertEquals('ddd', $element1->data);

        $element2 = $DB->get_record('perform_element', ['id' => $section_elements[2]->element_id]);
        $this->assertEquals('Test title 6', $element2->title);
        $this->assertEquals('fff', $element2->data);

        $element3 = $DB->get_record('perform_element', ['id' => $section_elements[3]->element_id]);
        $this->assertEquals('Test title 3', $element3->title);
        $this->assertEquals('ccc', $element3->data);

        $element4 = $DB->get_record('perform_element', ['id' => $section_elements[4]->element_id]);
        $this->assertEquals('Test title 2', $element4->title);
        $this->assertEquals('bbb', $element4->data);

        $this->assertEquals($section->id, $section_elements[1]->section_id);
        $this->assertEquals($element1->id, $section_elements[1]->element_id);
        $this->assertEquals(1, $section_elements[1]->sort_order);

        $this->assertEquals($section->id, $section_elements[2]->section_id);
        $this->assertEquals($element2->id, $section_elements[2]->element_id);
        $this->assertEquals(2, $section_elements[2]->sort_order);

        $this->assertEquals($section->id, $section_elements[3]->section_id);
        $this->assertEquals($element3->id, $section_elements[3]->element_id);
        $this->assertEquals(3, $section_elements[3]->sort_order);

        $this->assertEquals($section->id, $section_elements[4]->section_id);
        $this->assertEquals($element4->id, $section_elements[4]->element_id);
        $this->assertEquals(4, $section_elements[4]->sort_order);

        // Check that the result is correct (good enough).
        $this->assertEquals($section->id, $result_data['section']['id']);
        $this->assertCount(4, $result_data['section']['section_elements']);
    }

}
