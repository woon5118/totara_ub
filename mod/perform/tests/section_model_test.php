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
 * @author Matthias Bonk <matthias.bonk@totaralearning.com>
 * @package mod_perform
 */

use mod_perform\entities\activity\section as section_entity;
use mod_perform\models\activity\section_element;
use mod_perform\models\activity\section;
use totara_core\relationship\resolvers\subject;
use totara_job\relationship\resolvers\appraiser;
use totara_job\relationship\resolvers\manager;

require_once(__DIR__.'/relationship_testcase.php');

/**
 * @group perform
 */
class mod_perform_section_model_testcase extends mod_perform_relationship_testcase {

    public function test_create() {
        $this->setAdminUser();
        $activity = $this->perform_generator()->create_activity_in_container();

        $section2 = section::create($activity, 'section name two');
        $this->assertSame('section name two', $section2->title);
    }

    public function test_sort_order() {
        $this->setAdminUser();
        $activity = $this->perform_generator()->create_activity_in_container();

        $section1 = $activity->sections->first();
        $this->assertEquals(1, $section1->sort_order);

        $section2 = section::create($activity, 'section name two');
        $this->assertEquals(2, $section2->sort_order);

        // Add another section to check whether the sort order is correct
        $section3 = section::create($activity, 'section name three');
        $this->assertEquals(3, $section3->sort_order);

        // Let's add one section in between
        $section4 = section::create($activity, 'section name four', 2);
        $this->assertEquals(2, $section4->sort_order);

        $section1_reloaded = section::load_by_id($section1->id);
        $this->assertEquals(1, $section1_reloaded->sort_order);
        $section2_reloaded = section::load_by_id($section2->id);
        $this->assertEquals(3, $section2_reloaded->sort_order);
        $section3_reloaded = section::load_by_id($section3->id);
        $this->assertEquals(4, $section3_reloaded->sort_order);

        // Let's add one section at the beginning
        $section5 = section::create($activity, 'section name five', 1);
        $this->assertEquals(1, $section5->sort_order);

        $section1_reloaded = section::load_by_id($section1->id);
        $this->assertEquals(2, $section1_reloaded->sort_order);
        $section4_reloaded = section::load_by_id($section4->id);
        $this->assertEquals(3, $section4_reloaded->sort_order);
        $section2_reloaded = section::load_by_id($section2->id);
        $this->assertEquals(4, $section2_reloaded->sort_order);
        $section3_reloaded = section::load_by_id($section3->id);
        $this->assertEquals(5, $section3_reloaded->sort_order);

        // Let's add one section at the end
        $section6 = section::create($activity, 'section name six', 6);
        $this->assertEquals(6, $section6->sort_order);

        $section5_reloaded = section::load_by_id($section5->id);
        $this->assertEquals(1, $section5_reloaded->sort_order);
        $section1_reloaded = section::load_by_id($section1->id);
        $this->assertEquals(2, $section1_reloaded->sort_order);
        $section4_reloaded = section::load_by_id($section4->id);
        $this->assertEquals(3, $section4_reloaded->sort_order);
        $section2_reloaded = section::load_by_id($section2->id);
        $this->assertEquals(4, $section2_reloaded->sort_order);
        $section3_reloaded = section::load_by_id($section3->id);
        $this->assertEquals(5, $section3_reloaded->sort_order);

        // Let's add one section with a much higher sort order than the current max
        $section7 = section::create($activity, 'section name seven', 666);
        // And we still should get the next higher one
        $this->assertEquals(7, $section7->sort_order);

        // Delete a section and make sure the sort_order got recalculated
        $section4_reloaded->delete();

        $section5_reloaded = section::load_by_id($section5->id);
        $this->assertEquals(1, $section5_reloaded->sort_order);
        $section1_reloaded = section::load_by_id($section1->id);
        $this->assertEquals(2, $section1_reloaded->sort_order);
        $section2_reloaded = section::load_by_id($section2->id);
        $this->assertEquals(3, $section2_reloaded->sort_order);
        $section3_reloaded = section::load_by_id($section3->id);
        $this->assertEquals(4, $section3_reloaded->sort_order);
        $section6_reloaded = section::load_by_id($section6->id);
        $this->assertEquals(5, $section6_reloaded->sort_order);
        $section7_reloaded = section::load_by_id($section7->id);
        $this->assertEquals(6, $section7_reloaded->sort_order);
    }

    public function test_get_display_title() {
        $this->setAdminUser();
        $placeholder_string = get_string('untitled_section', 'mod_perform');
        $activity = $this->perform_generator()->create_activity_in_container(['create_section' => false]);

        $section1 = section::create($activity, 'Test Section');
        $section2 = section::create($activity, '   ');
        $section3 = section::create($activity);

        $this->assertEquals('Test Section', $section1->title);
        $this->assertEquals('Test Section', $section1->display_title);
        $this->assertEquals('   ', $section2->title);
        $this->assertEquals($placeholder_string, $section2->display_title);
        $this->assertEquals('', $section3->title);
        $this->assertEquals($placeholder_string, $section3->display_title);
    }

    public function test_update_relationships() {
        self::setAdminUser();
        $perform_generator = $this->perform_generator();
        $activity1 = $perform_generator->create_activity_in_container(['activity_name' => 'Activity 1']);
        $activity2 = $perform_generator->create_activity_in_container(['activity_name' => 'Activity 2']);
        $section1 = $perform_generator->create_section($activity1);
        $section2 = $perform_generator->create_section($activity1);
        $this->assert_section_relationships($section1, []);
        $this->assert_section_relationships($section2, []);

        $appraiser_relationship = $perform_generator->get_core_relationship(appraiser::class);
        $manager_relationship = $perform_generator->get_core_relationship(manager::class);
        $subject_relationship = $perform_generator->get_core_relationship(subject::class);

        // Add three relationships to section1.
        $returned_section = $section1->update_relationships(
            [
                [
                    'core_relationship_id' => $appraiser_relationship->id,
                    'can_view' => true,
                ],
                [
                    'core_relationship_id' => $manager_relationship->id,
                    'can_view' => true,
                ],
                [
                    'core_relationship_id' => $subject_relationship->id,
                    'can_view' => true,
                ],
            ]
        );
        $this->assertEquals($section1, $returned_section);
        $this->assert_section_relationships($section1, [appraiser::class, manager::class, subject::class]);
        $this->assert_section_relationships($section2, []);

        // Remove one relationship.
        $section1->update_relationships(
            [
                [
                    'core_relationship_id' => $appraiser_relationship->id,
                    'can_view' => true,
                ],
                [
                    'core_relationship_id' => $manager_relationship->id,
                    'can_view' => true,
                ]
            ]
        );
        $this->assert_section_relationships($section1, [appraiser::class, manager::class]);
        $this->assert_section_relationships($section2, []);

        // Add to section2.
        $section2->update_relationships(
            [
                [
                    'core_relationship_id' => $manager_relationship->id,
                    'can_view' => true,
                ],
                [
                    'core_relationship_id' => $subject_relationship->id,
                    'can_view' => true,
                ]
            ]
        );
        $this->assert_section_relationships($section1, [appraiser::class, manager::class]);
        $this->assert_section_relationships($section2, [manager::class, subject::class]);

        // Remove all from section1.
        $section1->update_relationships([]);
        $this->assert_section_relationships($section1, []);
        $this->assert_section_relationships($section2, [manager::class, subject::class]);

        // Remove all from section2.
        $section2->update_relationships([]);
        $this->assert_section_relationships($section1, []);
        $this->assert_section_relationships($section2, []);
    }

    public function test_get_section_element_stats() {
        self::setAdminUser();
        $perform_generator = $this->perform_generator();
        $activity = $perform_generator->create_activity_in_container();
        $section1 = $perform_generator->create_section($activity);
        $section2 = $perform_generator->create_section($activity);

        $element1 = $perform_generator->create_element(['title'=>'element one', 'is_required'=>true]);
        $element2 = $perform_generator->create_element(['title'=>'element two', 'is_required'=>true]);
        $element3 = $perform_generator->create_element(['title'=>'element three']);

        section_element::create($section1, $element1, 1);
        section_element::create($section1, $element2, 2);
        section_element::create($section1, $element3, 3);

        //check element counts after create
        $result= $section1->get_section_elements_summary();
        $expected = (object)[
            'required_question_count' => 2,
            'optional_question_count' => 1,
            'other_element_count' => 0
        ];
        $this->assertEquals($expected, $result);

        //check element counts after update
        $perform_generator->update_element($element1, ['is_required'=>false]);
        $perform_generator->update_element($element2, ['is_required'=>false]);

        $result= $section1->get_section_elements_summary();
        $expected = (object)[
            'required_question_count' => 0,
            'optional_question_count' => 3,
            'other_element_count' => 0
        ];
        $this->assertEquals($expected, $result);

        //check other section element counts
        $result= $section2->get_section_elements_summary();
        $expected = (object)[
            'required_question_count' => 0,
            'optional_question_count' => 0,
            'other_element_count' => 0
        ];
        $this->assertEquals($expected, $result);
    }
}