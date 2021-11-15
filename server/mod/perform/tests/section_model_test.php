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

use mod_perform\constants;
use mod_perform\models\activity\section_element;
use mod_perform\models\activity\section;
use mod_perform\state\activity\active;
use mod_perform\state\activity\draft;

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

        $appraiser_relationship = $perform_generator->get_core_relationship(constants::RELATIONSHIP_APPRAISER);
        $manager_relationship = $perform_generator->get_core_relationship(constants::RELATIONSHIP_MANAGER);
        $subject_relationship = $perform_generator->get_core_relationship(constants::RELATIONSHIP_SUBJECT);

        // Add three relationships to section1.
        $returned_section = $section1->update_relationships(
            [
                [
                    'core_relationship_id' => $appraiser_relationship->id,
                    'can_view' => true,
                    'can_answer' => true,
                ],
                [
                    'core_relationship_id' => $manager_relationship->id,
                    'can_view' => true,
                    'can_answer' => true,
                ],
                [
                    'core_relationship_id' => $subject_relationship->id,
                    'can_view' => true,
                    'can_answer' => true,
                ],
            ]
        );
        $this->assertEquals($section1, $returned_section);
        $this->assert_section_relationships($section1, [constants::RELATIONSHIP_APPRAISER, constants::RELATIONSHIP_MANAGER, constants::RELATIONSHIP_SUBJECT]);
        $this->assert_section_relationships($section2, []);

        // Remove one relationship.
        $section1->update_relationships(
            [
                [
                    'core_relationship_id' => $appraiser_relationship->id,
                    'can_view' => true,
                    'can_answer' => true,
                ],
                [
                    'core_relationship_id' => $manager_relationship->id,
                    'can_view' => true,
                    'can_answer' => true,
                ]
            ]
        );
        $this->assert_section_relationships($section1, [constants::RELATIONSHIP_APPRAISER, constants::RELATIONSHIP_MANAGER]);
        $this->assert_section_relationships($section2, []);

        // Add to section2.
        $section2->update_relationships(
            [
                [
                    'core_relationship_id' => $manager_relationship->id,
                    'can_view' => true,
                    'can_answer' => true,
                ],
                [
                    'core_relationship_id' => $subject_relationship->id,
                    'can_view' => true,
                    'can_answer' => true,
                ]
            ]
        );
        $this->assert_section_relationships($section1, [constants::RELATIONSHIP_APPRAISER, constants::RELATIONSHIP_MANAGER]);
        $this->assert_section_relationships($section2, [constants::RELATIONSHIP_MANAGER, constants::RELATIONSHIP_SUBJECT]);

        // Remove all from section1.
        $section1->update_relationships([]);
        $this->assert_section_relationships($section1, []);
        $this->assert_section_relationships($section2, [constants::RELATIONSHIP_MANAGER, constants::RELATIONSHIP_SUBJECT]);

        // Remove all from section2.
        $section2->update_relationships([]);
        $this->assert_section_relationships($section1, []);
        $this->assert_section_relationships($section2, []);

        // Invalid relation ids are not accepted.
        $invalid = 31443;

        $this->expectException(coding_exception::class);
        $this->expectExceptionMessage("Invalid relationship id: $invalid");
        $section1->update_relationships(
            [
                [
                    'core_relationship_id' => $invalid,
                    'can_view' => true,
                    'can_answer' => true,
                ]
            ]
        );
    }

    public function test_get_section_element_stats() {
        self::setAdminUser();
        $perform_generator = $this->perform_generator();
        $activity = $perform_generator->create_activity_in_container();
        $section1 = $perform_generator->create_section($activity);
        $section2 = $perform_generator->create_section($activity);

        $element1 = $perform_generator->create_element(['title' => 'element one', 'is_required' => true]);
        $element2 = $perform_generator->create_element(['title' => 'element two', 'is_required' => true]);
        $element3 = $perform_generator->create_element(['title' => 'element three']);
        $element4 = $perform_generator->create_element(['title' => 'element three', 'plugin_name' => 'static_content']);

        section_element::create($section1, $element1, 1);
        section_element::create($section1, $element2, 2);
        section_element::create($section1, $element3, 3);
        section_element::create($section1, $element4, 4);

        //check element counts after create
        $result = $section1->get_section_elements_summary();
        $expected = (object)[
            'required_question_count' => 2,
            'optional_question_count' => 1,
            'other_element_count' => 1
        ];
        $this->assertEquals($expected, $result);

        // Check element counts after update
        $perform_generator->update_element($element1, ['is_required' => false]);
        $perform_generator->update_element($element2, ['is_required' => false]);
        // Refresh model
        $section1 = section::load_by_id($section1->id);

        $result = $section1->get_section_elements_summary();
        $expected = (object)[
            'required_question_count' => 0,
            'optional_question_count' => 3,
            'other_element_count' => 1
        ];
        $this->assertEquals($expected, $result);


        //check other section element counts
        $result = $section2->get_section_elements_summary();
        $expected = (object)[
            'required_question_count' => 0,
            'optional_question_count' => 0,
            'other_element_count' => 0
        ];
        $this->assertEquals($expected, $result);
    }

    public function test_delete_section_success() {
        global $DB;

        $data = $this->create_test_data();
        $this->add_participant_section($data);
        $activity = $data->activity1;
        $section = $data->activity1_section1;

        $this->assertCount(3, $activity->sections);
        $this->assertCount(2, $section->section_relationships);
        $this->assertCount(1, $section->participant_sections);
        $section->delete();
        $activity->refresh(true);

        $this->assertCount(2, $activity->sections);
        $section_relationships = $DB->get_records('perform_section_relationship', ['section_id' => $section->id]);
        $this->assertCount(0, $section_relationships);
        $participant_sections = $DB->get_records('perform_participant_section', ['section_id' => $section->id]);
        $this->assertCount(0, $participant_sections);
    }

    public function test_fail_to_check_deletion_requirement_if_activity_is_active() {
        $data = $this->create_test_data();
        $activity = $data->activity1;
        $this->assertEquals(active::get_code(), $activity->status);

        $this->expectException(coding_exception::class);
        $this->expectExceptionMessage('section can not be deleted for active performance activity');
        $section = $data->activity1_section1;
        $section->check_deletion_requirements();
    }

    public function test_fail_to_check_deletion_requirement_if_no_enough_sections() {
        self::setAdminUser();
        $perform_generator = $this->perform_generator();
        $activity = $perform_generator->create_activity_in_container(
            ['activity_name' => 'Activity 1', 'activity_status' => draft::get_code(), 'create_section' => false]
        );
        $activity_section1 = $perform_generator->create_section($activity, ['title' => 'Activity 1 section 1']);
        $activity_section2 = $perform_generator->create_section($activity, ['title' => 'Activity 1 section 2']);
        $this->assertEquals(draft::get_code(), $activity->status);

        $activity_section1->delete();
        $activity->refresh(true);
        $this->assertCount(1, $activity->sections);

        $this->expectException(coding_exception::class);
        $this->expectExceptionMessage('activity does not have enough sections, section can not be deleted');
        $activity_section2->check_deletion_requirements();
    }

    /**
     * @param $data
     * @throws coding_exception
     */
    private function add_participant_section($data): void {
        $perform_generator = $this->perform_generator();
        $user1 = self::getDataGenerator()->create_user();

        $subject_instance = $perform_generator->create_subject_instance(
            [
                'activity_id'       => $data->activity1->id,
                'subject_user_id'   => $user1->id,
                'include_questions' => false,
            ]
        );
        $data->activity1_section1_relationship1 = $perform_generator->create_section_relationship(
            $data->activity1_section1,
            ['relationship' => constants::RELATIONSHIP_APPRAISER]
        );
        $participant_instance = $perform_generator->create_participant_instance(
            $user1, $subject_instance->id, $data->activity1_section1_relationship1->id
        );
        $data->activity1_section_1_participant1 = $perform_generator->create_participant_section(
            $data->activity1, $participant_instance, false, $data->activity1_section1
        );
    }

    public function test_add_section_element_fail_if_section_is_deleted() {
        [$section, $section_element, $element] = $this->create_section_element();

        $this->expectException(coding_exception::class);
        $this->expectExceptionMessage('Section has been deleted, can not add section element');

        $section->delete();
        $section->add_element($element);
    }

    public function test_move_section_element_fail_if_section_is_deleted() {
        [$section, $section_element] = $this->create_section_element();

        $this->expectException(coding_exception::class);
        $this->expectExceptionMessage('Section has been deleted, can not move section elements');

        $section->delete();
        $section->move_section_elements([$section_element]);
    }

    public function test_remove_section_element_fail_if_section_is_deleted() {
        [$section, $section_element] = $this->create_section_element();

        $this->expectException(coding_exception::class);
        $this->expectExceptionMessage('Section has been deleted, can not remove section elements');

        $section->delete();
        $section->remove_section_elements([$section_element]);
    }

    public function test_update_relationship_fail_if_section_is_deleted() {
        [$section] = $this->create_section_element();

        $this->expectException(coding_exception::class);
        $this->expectExceptionMessage('Section has been deleted, can not update relationships');

        $section->delete();
        $section->update_relationships([]);
    }

    public function test_sync_updated_at_with_created_at() {
        [$section] = $this->create_section_element();
        $this->waitForSecond();

        // make an update operation to change the update time
        $section->update_title("new title");
        $this->assertNotEquals($section->created_at, $section->updated_at);

        $section->sync_updated_at_with_created_at();
        $updated_section = section::load_by_id($section->id);

        $this->assertEquals($updated_section->created_at, $updated_section->updated_at);
    }

    public function test_get_highest_sort_order() {
        self::setAdminUser();
        $perform_generator = $this->perform_generator();
        $activity = $perform_generator->create_activity_in_container(
            ['activity_name' => 'Activity 1', 'activity_status' => draft::get_code(), 'create_section' => false]
        );
        $activity_section1 = $perform_generator->create_section($activity, ['title' => 'Activity 1 section 1']);
        $activity_section2 = $perform_generator->create_section($activity, ['title' => 'Activity 1 section 2']);
        $element = $perform_generator->create_element(['title' => 'Question one']);
        $section_element = $perform_generator->create_section_element($activity_section1, $element);

        $this->assertEquals(1, $activity_section1->get_highest_sort_order());
        $this->assertEquals(0, $activity_section2->get_highest_sort_order());
    }

    public function test_update_title(): void {
        $this->setAdminUser();
        $activity = $this->perform_generator()->create_activity_in_container();
        $section = section::create($activity, 'section name');

        $new_section_name = 'Updated section name';
        $section->update_title($new_section_name);
        $this->assertSame($new_section_name, $section->title);

        $new_section_name = $this->get_string_with_length(1025);
        $this->expectException('coding_exception');
        $this->expectExceptionMessage('Section title text exceeds the maximum length');
        $section->update_title($new_section_name);
    }

    public function test_create_section_with_lengthy_title(): void {
        $this->setAdminUser();
        $activity = $this->perform_generator()->create_activity_in_container();
        $lengthy_section_name = $this->get_string_with_length(1025);

        $this->expectException('coding_exception');
        $this->expectExceptionMessage('Section title text exceeds the maximum length');
        section::create($activity, $lengthy_section_name);
    }

    /**
     * @param int $length
     * @return string
     */
    private function get_string_with_length(int $length): string {
        $string = '';
        while (strlen($string) < $length) {
            $string .= 'x';
        }
        return $string;
    }

    /**
     * @return array
     */
    private function create_section_element(): array {
        $data = $this->create_test_data();
        $perform_generator = $this->perform_generator();
        $element = $perform_generator->create_element(['title' => 'Question one']);
        $section = $data->activity1_section1;
        $section_element = $perform_generator->create_section_element($section, $element);
        return [$section, $section_element, $element];
    }
}