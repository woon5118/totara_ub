<?php
/*
 * This file is part of Totara Learn
 *
 * Copyright (C) 2019 onwards Totara Learning Solutions LTD
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
 * @author Brendan Cox <brendan.cox@totaralearning.com>
 * @author Riana Rossouw <riana.rossouw@totaralearning.com>
 * @package totara_criteria
 */

use hierarchy_competency\event\evidence_deleted;
use totara_competency\event\linked_courses_updated;
use totara_competency\linked_courses;

class totara_competency_linked_courses_testcase extends advanced_testcase {

    public function test_get_linked_courses_none() {

        $this->getDataGenerator()->create_course();

        /** @var totara_hierarchy_generator $hierarchy_generator */
        $hierarchy_generator = $this->getDataGenerator()->get_plugin_generator('totara_hierarchy');
        $compfw = $hierarchy_generator->create_comp_frame([]);
        $comp = $hierarchy_generator->create_comp(['frameworkid' => $compfw->id]);

        $linked_courses = linked_courses::get_linked_courses($comp->id);
        $this->assertEmpty($linked_courses);
    }

    public function test_set_linked_courses_none() {

        $this->getDataGenerator()->create_course();

        /** @var totara_hierarchy_generator $hierarchy_generator */
        $hierarchy_generator = $this->getDataGenerator()->get_plugin_generator('totara_hierarchy');
        $compfw = $hierarchy_generator->create_comp_frame([]);
        $comp = $hierarchy_generator->create_comp(['frameworkid' => $compfw->id]);

        linked_courses::set_linked_courses($comp->id, []);

        $linked_courses = linked_courses::get_linked_courses($comp->id);
        $this->assertEmpty($linked_courses);
    }

    public function test_get_and_set_linked_courses_some() {
        global $CFG;
        require_once($CFG->dirroot . '/totara/plan/lib.php');

        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();
        $course3 = $this->getDataGenerator()->create_course();

        /** @var totara_hierarchy_generator $hierarchy_generator */
        $hierarchy_generator = $this->getDataGenerator()->get_plugin_generator('totara_hierarchy');
        $compfw = $hierarchy_generator->create_comp_frame([]);
        $comp1 = $hierarchy_generator->create_comp(['frameworkid' => $compfw->id]);
        $comp2 = $hierarchy_generator->create_comp(['frameworkid' => $compfw->id]);

        linked_courses::set_linked_courses(
            $comp1->id,
            [
                ['id' => $course1->id, 'linktype' => linked_courses::LINKTYPE_MANDATORY],
                ['id' => $course2->id, 'linktype' => linked_courses::LINKTYPE_MANDATORY]
            ]
        );

        linked_courses::set_linked_courses(
            $comp2->id,
            [
                ['id' => $course3->id, 'linktype' => linked_courses::LINKTYPE_OPTIONAL],
            ]
        );

        $linked_courses1 = linked_courses::get_linked_courses($comp1->id);
        $this->assertCount(2, $linked_courses1);
        foreach ($linked_courses1 as $course) {
            $this->assertContains($course->id, [$course1->id, $course2->id]);
            $this->assertEquals(linked_courses::LINKTYPE_MANDATORY, $course->linktype);
        }

        $linked_courses2 = linked_courses::get_linked_courses($comp2->id);
        $this->assertCount(1, $linked_courses2);
        $course = array_pop($linked_courses2);
        $this->assertEquals($course->id, $course3->id);
        $this->assertEquals(linked_courses::LINKTYPE_OPTIONAL, $course->linktype);

        linked_courses::set_linked_courses(
            $comp1->id,
            [
                ['id' => $course1->id, 'linktype' => linked_courses::LINKTYPE_MANDATORY],
                ['id' => $course2->id, 'linktype' => linked_courses::LINKTYPE_OPTIONAL]
            ]
        );

        $linked_courses1 = linked_courses::get_linked_courses($comp1->id);
        $this->assertCount(2, $linked_courses1);
        foreach ($linked_courses1 as $course) {
            switch ($course->id) {
                case $course1->id:
                    $this->assertEquals(linked_courses::LINKTYPE_MANDATORY, $course->linktype);
                    break;
                case $course2->id:
                    $this->assertEquals(linked_courses::LINKTYPE_OPTIONAL, $course->linktype);
                    break;
                default:
                    $this->fail('Unexpected course included in linked courses');
            }
        }

        $linked_courses2 = linked_courses::get_linked_courses($comp2->id);
        $this->assertCount(1, $linked_courses2);
        $course = array_pop($linked_courses2);
        $this->assertEquals($course->id, $course3->id);
        $this->assertEquals(linked_courses::LINKTYPE_OPTIONAL, $course->linktype);

        linked_courses::set_linked_courses($comp1->id, []);

        $linked_courses1 = linked_courses::get_linked_courses($comp1->id);
        $this->assertCount(0, $linked_courses1);

        $linked_courses2 = linked_courses::get_linked_courses($comp2->id);
        $this->assertCount(1, $linked_courses2);
        $course = array_pop($linked_courses2);
        $this->assertEquals($course->id, $course3->id);
        $this->assertEquals(linked_courses::LINKTYPE_OPTIONAL, $course->linktype);
    }

    public function test_get_and_set_linked_courses_event_is_fired() {
        global $CFG;
        require_once($CFG->dirroot . '/totara/plan/lib.php');

        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();

        /** @var totara_hierarchy_generator $hierarchy_generator */
        $hierarchy_generator = $this->getDataGenerator()->get_plugin_generator('totara_hierarchy');
        $compfw = $hierarchy_generator->create_comp_frame([]);
        $comp1 = $hierarchy_generator->create_comp(['frameworkid' => $compfw->id]);

        $sink = $this->redirectEvents();

        linked_courses::set_linked_courses(
            $comp1->id,
            [
                ['id' => $course1->id, 'linktype' => linked_courses::LINKTYPE_MANDATORY],
                ['id' => $course2->id, 'linktype' => linked_courses::LINKTYPE_MANDATORY]
            ]
        );

        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = array_shift($events);
        $this->assertInstanceOf(linked_courses_updated::class, $event);
        $this->assertEquals($comp1->id, $event->get_data()['objectid']);
    }

    /**
     * @param array $courses_to_update
     * @dataProvider courses_data_provider
     */
    public function test_set_linked_courses_updates_linked_course_count_of_competency(array $courses_to_update): void {
        /** @var totara_hierarchy_generator $hierarchy_generator */
        $hierarchy_generator = $this->getDataGenerator()->get_plugin_generator('totara_hierarchy');
        $compfw = $hierarchy_generator->create_comp_frame([]);
        $comp1 = $hierarchy_generator->create_comp(['frameworkid' => $compfw->id]);

        $count_before = $this->get_linked_course_count($comp1->id);
        $this->assertEquals(0, $count_before);

        $courses = array_map(static function (object $course) {
            return ['id' => $course->id, 'linktype' => linked_courses::LINKTYPE_MANDATORY];
        }, $courses_to_update);

        linked_courses::set_linked_courses($comp1->id, $courses);

        $count_after = $this->get_linked_course_count($comp1->id);
        $this->assertCount($count_after, $courses_to_update);
    }

    public function courses_data_provider(): array {
        $existing_course_1 = self::getDataGenerator()->create_course();
        $existing_course_2 = self::getDataGenerator()->create_course();

        return [
            'Update all' => [[$existing_course_1, $existing_course_2]],
            'Remove all' => [[]],
        ];
    }

    public function test_remove_course(): void {
        [$course_id, [$competency1, $competency2]] = $this->link_course_to_multiple_competencies();
        [$other_course_id, [$other_competency1, $other_competency2]] = $this->link_course_to_multiple_competencies();

        // Precondition check, check count is originally correct.
        $before_removal1 = Linked_courses::get_linked_courses($competency1->id);
        $this->assertCount(1, $before_removal1);

        $before_removal2 = Linked_courses::get_linked_courses($competency2->id);
        $this->assertCount(1, $before_removal2);

        linked_courses::remove_course($course_id);

        $after_removal1 = Linked_courses::get_linked_courses($competency1->id);
        $this->assertCount(0, $after_removal1);

        $after_removal2 = Linked_courses::get_linked_courses($competency2->id);
        $this->assertCount(0, $after_removal2);

        // Check the other/unrelated courses and competencies are not touched by the removal of the course
        $other_before_removal1 = Linked_courses::get_linked_courses($other_competency1->id);
        $this->assertCount(1, $other_before_removal1);

        $other_before_removal2 = Linked_courses::get_linked_courses($other_competency2->id);
        $this->assertCount(1, $other_before_removal2);
    }

    public function test_remove_course_updates_competency_count(): void {
        [$course_id, [$competency1, $competency2]] = $this->link_course_to_multiple_competencies();

        $count_before1 = $this->get_linked_course_count($competency1->id);
        $this->assertEquals(1, $count_before1);

        $count_before2 = $this->get_linked_course_count($competency2->id);
        $this->assertEquals(1, $count_before2);

        linked_courses::remove_course($course_id);

        $count_after1 = $this->get_linked_course_count($competency1->id);
        $this->assertEquals(0, $count_after1);

        $count_after2 = $this->get_linked_course_count($competency2->id);
        $this->assertEquals(0, $count_after2);
    }

    public function test_remove_course_fires_events(): void {
        [$course_id, [$competency1, $competency2]] = $this->link_course_to_multiple_competencies();
        $competency_course_ids = array_keys($this->get_competency_criteria_by_course($course_id));

        $sink = $this->redirectEvents();
        linked_courses::remove_course($course_id);

        $events = $sink->get_events();

        $this->assertCount(4, $events);

        // Check new courses updated events are fired
        $courses_updated_events = array_filter($events, static function (core\event\base $event) {
            return $event instanceof linked_courses_updated;
        });

        $this->assertEqualsCanonicalizing([$competency1->id, $competency2->id], array_column($courses_updated_events, 'objectid'), 'The events should contain the competency ids');

        // Check legacy evidence deleted events are fired (for backwards compatibility
        $evidence_deleted_events = array_filter($events, static function (core\event\base $event) {
            return $event instanceof evidence_deleted;
        });

        $this->assertEqualsCanonicalizing($competency_course_ids, array_column($evidence_deleted_events, 'objectid'), 'The events should contain the competency_criteria ids');
    }

    private function link_course_to_multiple_competencies(): array {
        $course = self::getDataGenerator()->create_course();

        /** @var totara_hierarchy_generator $hierarchy_generator */
        $hierarchy_generator = self::getDataGenerator()->get_plugin_generator('totara_hierarchy');

        $compfw1 = $hierarchy_generator->create_comp_frame([]);
        $competency1 = $hierarchy_generator->create_comp(['frameworkid' => $compfw1->id]);

        $compfw2 = $hierarchy_generator->create_comp_frame([]);
        $competency2 = $hierarchy_generator->create_comp(['frameworkid' => $compfw2->id]);

        linked_courses::set_linked_courses($competency1->id, [['id' => $course->id, 'linktype' => linked_courses::LINKTYPE_MANDATORY]]);
        linked_courses::set_linked_courses($competency2->id, [['id' => $course->id, 'linktype' => linked_courses::LINKTYPE_MANDATORY]]);

        return [$course->id, [$competency1, $competency2]];
    }

    private function get_linked_course_count(int $competency_id): int {
        global $DB;

        return $DB->count_records('comp_criteria', ['itemtype' => 'coursecompletion', 'competencyid' => $competency_id]);
    }

    private function get_competency_criteria_by_course(int $course_id): array {
        global $DB;

        return $DB->get_records('comp_criteria', ['itemtype' => 'coursecompletion', 'iteminstance' => $course_id]);
    }

}
