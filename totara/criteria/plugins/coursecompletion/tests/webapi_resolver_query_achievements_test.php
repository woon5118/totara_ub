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
 * @author Fabian Derschatta <fabian.derschatta@totaralearning.com>
 * @package criteria_coursecompletion
 * @subpackage test
 */

use core\webapi\execution_context;
use criteria_coursecompletion\coursecompletion;
use criteria_coursecompletion\webapi\resolver\query\achievements;
use totara_program\task\completions_task;

defined('MOODLE_INTERNAL') || die();


/**
 * Tests the query to fetch data for a coursecompletion criteria
 */
class criteria_coursecompletion_webapi_resolver_query_achievements_testcase extends advanced_testcase {

    private function get_execution_context(string $type = 'dev', ?string $operation = null) {
        return execution_context::create($type, $operation);
    }

    public function test_non_existing_instance() {
        $this->setAdminUser();

        $args = ['instance_id' => 999, 'user_id' => 999];

        $result = achievements::resolve($args, $this->get_execution_context());
        $this->assertNull($result);
    }

    public function test_existing_instance_with_non_existing_courses() {
        $this->setAdminUser();

        $data = $this->create_data();

        $aggregation_method = 0;

        $criteria = new coursecompletion();
        $criteria->set_aggregation_method($aggregation_method)
            ->set_item_ids([987, $data->course2->id, 897])
            ->save();

        $args = ['instance_id' => $criteria->get_id(), 'user_id' => 999];

        $result = achievements::resolve($args, $this->get_execution_context());
        $this->assertIsArray($result);
        $this->assertEquals($aggregation_method, $result['aggregation']);
        $items = $result['items'] ?? [];
        // Only one course really exists the rest should not be there
        $this->assertCount(1, $items);
        $this->assert_course_is_visible($data->course2->fullname, $result);
    }


    public function test_courses_without_user_being_enrolled() {
        $this->setAdminUser();

        $data = $this->create_data();

        $aggregation_method = 0;

        $criteria = new coursecompletion();
        $criteria->set_aggregation_method($aggregation_method)
            ->set_item_ids([$data->course1->id, $data->course2->id, $data->course3->id])
            ->save();

        $args = ['instance_id' => $criteria->get_id(), 'user_id' => 999];

        $result = achievements::resolve($args, $this->get_execution_context());
        $this->assertIsArray($result);
        $this->assertEquals($aggregation_method, $result['aggregation']);
        $items = $result['items'] ?? [];
        // Make sure we get the correct amount
        $this->assertCount(3, $items);

        // Make sure the data structure matches our expectation
        foreach ($items as $item) {
            $this->assertValidKeys(['progress' => null, 'course' => null], $item);
            $this->assertEquals(0, $item['progress']);
            $course =  $item['course'];
            $this->assertValidKeys(['name' => null, 'summary' => null, 'url' => null], $course);
            $this->assertNotEmpty($course['summary']);
            $this->assertStringStartsWith('http', $course['url']);
        }
        $this->assert_course_is_visible($data->course1->fullname, $result);
        $this->assert_course_is_visible($data->course2->fullname, $result);
        $this->assert_course_is_visible($data->course3->fullname, $result);
    }

    public function test_aggregation_methods() {
        $this->setAdminUser();

        $data = $this->create_data();

        $criteria = (new coursecompletion())
            ->set_item_ids([$data->course1->id, $data->course2->id, $data->course3->id])
            ->save();

        for ($method = 0; $method < 4; $method++) {
            $criteria->set_aggregation_method($method)
                ->save();

            $args = ['instance_id' => $criteria->get_id(), 'user_id' => 999];

            $result = achievements::resolve($args, $this->get_execution_context());
            $this->assertEquals($method, $result['aggregation']);
        }
    }

    public function test_course_progress_is_returned_correctly() {
        $this->setAdminUser();

        $data = $this->create_data();

        $user = $this->getDataGenerator()->create_user();
        $control_user = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($user->id, $data->course2->id);
        $this->getDataGenerator()->enrol_user($control_user->id, $data->course2->id);

        $completion = new completion_completion(['userid' => $user->id, 'course' => $data->course2->id]);
        $completion->status = COMPLETION_STATUS_COMPLETE;
        $completion->mark_complete(time() - 10000);

        // Make sure all our completions are up to date.
        $task3 = new completions_task();
        $task3->execute();

        $criteria = (new coursecompletion())
            ->set_item_ids([$data->course1->id, $data->course2->id, $data->course3->id])
            ->save();

        $args = ['instance_id' => $criteria->get_id(), 'user_id' => $user->id];

        $result = achievements::resolve($args, $this->get_execution_context());
        // Course 2 should come out as completed
        $this->assert_course_has_progress($data->course1->fullname, 0, $result);
        $this->assert_course_has_progress($data->course2->fullname, 100, $result);
        $this->assert_course_has_progress($data->course3->fullname, 0, $result);

        // Control user should not have completed the course
        $args = ['instance_id' => $criteria->get_id(), 'user_id' => $control_user->id];

        $result = achievements::resolve($args, $this->get_execution_context());
        $this->assert_course_has_progress($data->course1->fullname, 0, $result);
        $this->assert_course_has_progress($data->course2->fullname, 0, $result);
        $this->assert_course_has_progress($data->course3->fullname, 0, $result);
    }

    public function test_hidden_course_is_not_returned() {
        global $DB;

        $data = $this->create_data();

        // Hide course 2
        $data->course2->visible = 0;
        $DB->update_record('course', $data->course2);

        $user = $this->getDataGenerator()->create_user();

        $criteria = (new coursecompletion())
            ->set_item_ids([$data->course1->id, $data->course2->id, $data->course3->id])
            ->save();

        $this->setAdminUser();

        $args = ['instance_id' => $criteria->get_id(), 'user_id' => $user->id];

        $result = achievements::resolve($args, $this->get_execution_context());
        // The user should only see course 1 and 3
        $this->assert_course_is_visible($data->course1->fullname, $result);
        $this->assert_course_is_visible($data->course2->fullname, $result);
        $this->assert_course_is_visible($data->course3->fullname, $result);

        // Now query as a normal user for whom the course 2 should not be visible
        $this->setUser($user);

        $args = ['instance_id' => $criteria->get_id(), 'user_id' => $user->id];

        $result = achievements::resolve($args, $this->get_execution_context());
        // The user should only see course 1 and 3
        $this->assert_course_is_visible($data->course1->fullname, $result);
        $this->assert_course_is_not_visible($data->course2->fullname, $result);
        $this->assert_course_is_visible($data->course3->fullname, $result);
    }

    /**
     * Assert that the given course has the expected progress in the result
     *
     * @param string $course_name
     * @param int $progress
     * @param array $result
     */
    private function assert_course_has_progress(string $course_name, int $progress, array $result) {
        $checked = false;
        foreach ($result['items'] as $item) {
            if ($item['course']['name'] == $course_name) {
                $this->assertEquals($progress, $item['progress'], 'Progress: Course does not have expected progress');
                $checked = true;
            }
        }
        $this->assertTrue($checked, 'Progres: Course not found!');
    }

    /**
     * Assert that the given course is visible in the result
     *
     * @param string $course_name
     * @param array $result
     */
    private function assert_course_is_visible(string $course_name, array $result) {
        $visible_courses = [];
        foreach ($result['items'] as $item) {
            if (!empty($item['course'])) {
                $visible_courses[] = $item['course']['name'];
            }
        }
        $this->assertContains($course_name, $visible_courses);
    }

    /**
     * Assert that the given course is NOT visible in the result
     *
     * @param string $course_name
     * @param array $result
     */
    private function assert_course_is_not_visible(string $course_name, array $result) {
        $visible_courses = [];
        foreach ($result['items'] as $item) {
            if (!empty($item['course'])) {
                $visible_courses[] = $item['course']['name'];
            }
        }
        $this->assertNotContains($course_name, $visible_courses);
    }

    protected function create_data() {
        $data = new class() {
            public $fw1;
            public $comp1;
            public $course1, $course2, $course3;
        };

        /** @var totara_hierarchy_generator $hierarchy_generator */
        $hierarchy_generator =  $this->getDataGenerator()->get_plugin_generator('totara_hierarchy');
        $data->fw1 = $hierarchy_generator->create_comp_frame([]);
        $data->comp1 = $hierarchy_generator->create_comp([
            'shortname' => 'c-chef',
            'fullname' => 'Chef proficiency',
            'description' => 'Bossing around',
            'idnumber' => 'cook-chef-c',
            'frameworkid' => $data->fw1->id
        ]);

        // Enable completion before creating modules, otherwise the completion data is not written in DB.
        set_config('enablecompletion', 1);

        $data->course1 = $this->getDataGenerator()->create_course(['enablecompletion' => true]);
        $this->getDataGenerator()->create_module('forum', array('course' => $data->course1->id));
        $this->getDataGenerator()->create_module('forum', array('course' => $data->course1->id));

        $data->course2 = $this->getDataGenerator()->create_course(['enablecompletion' => true]);
        $this->getDataGenerator()->create_module('forum', array('course' => $data->course2->id));
        $this->getDataGenerator()->create_module('forum', array('course' => $data->course2->id));

        $data->course3 = $this->getDataGenerator()->create_course(['enablecompletion' => true]);
        $this->getDataGenerator()->create_module('forum', array('course' => $data->course3->id));
        $this->getDataGenerator()->create_module('forum', array('course' => $data->course3->id));

        return $data;
    }

}