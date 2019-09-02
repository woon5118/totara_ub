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

use criteria_linkedcourses\task\update_linked_course_items_adhoc;
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

        $this->verify_adhoc_task($comp->id);
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
                ['id' => $course1->id, 'linktype' => PLAN_LINKTYPE_MANDATORY],
                ['id' => $course2->id, 'linktype' => PLAN_LINKTYPE_MANDATORY]
            ]
        );

        linked_courses::set_linked_courses(
            $comp2->id,
            [
                ['id' => $course3->id, 'linktype' => PLAN_LINKTYPE_OPTIONAL],
            ]
        );

        $linked_courses1 = linked_courses::get_linked_courses($comp1->id);
        $this->assertCount(2, $linked_courses1);
        foreach ($linked_courses1 as $course) {
            $this->assertContains($course->id, [$course1->id, $course2->id]);
            $this->assertEquals(PLAN_LINKTYPE_MANDATORY, $course->linktype);
        }

        $linked_courses2 = linked_courses::get_linked_courses($comp2->id);
        $this->assertCount(1, $linked_courses2);
        $course = array_pop($linked_courses2);
        $this->assertEquals($course->id, $course3->id);
        $this->assertEquals(PLAN_LINKTYPE_OPTIONAL, $course->linktype);

        linked_courses::set_linked_courses(
            $comp1->id,
            [
                ['id' => $course1->id, 'linktype' => PLAN_LINKTYPE_MANDATORY],
                ['id' => $course2->id, 'linktype' => PLAN_LINKTYPE_OPTIONAL]
            ]
        );

        $linked_courses1 = linked_courses::get_linked_courses($comp1->id);
        $this->assertCount(2, $linked_courses1);
        foreach ($linked_courses1 as $course) {
            switch ($course->id) {
                case $course1->id:
                    $this->assertEquals(PLAN_LINKTYPE_MANDATORY, $course->linktype);
                    break;
                case $course2->id:
                    $this->assertEquals(PLAN_LINKTYPE_OPTIONAL, $course->linktype);
                    break;
                default:
                    $this->fail('Unexpected course included in linked courses');
            }
        }

        $linked_courses2 = linked_courses::get_linked_courses($comp2->id);
        $this->assertCount(1, $linked_courses2);
        $course = array_pop($linked_courses2);
        $this->assertEquals($course->id, $course3->id);
        $this->assertEquals(PLAN_LINKTYPE_OPTIONAL, $course->linktype);

        linked_courses::set_linked_courses($comp1->id, []);

        $linked_courses1 = linked_courses::get_linked_courses($comp1->id);
        $this->assertCount(0, $linked_courses1);

        $linked_courses2 = linked_courses::get_linked_courses($comp2->id);
        $this->assertCount(1, $linked_courses2);
        $course = array_pop($linked_courses2);
        $this->assertEquals($course->id, $course3->id);
        $this->assertEquals(PLAN_LINKTYPE_OPTIONAL, $course->linktype);
    }

    /**
     * Verify adhoc task has been created
     *
     * @param int $comp_id
     */
    private function verify_adhoc_task(int $comp_id) {
        global $DB;

        $tasks = $DB->get_records('task_adhoc', ['classname' => '\\'.update_linked_course_items_adhoc::class]);
        $this->assertCount(1, $tasks);
        $task = reset($tasks);
        $task_data = json_decode($task->customdata, true);
        $this->assertArrayHasKey('competency_id', $task_data);
        $this->assertEquals($task_data['competency_id'], $comp_id);
    }
}