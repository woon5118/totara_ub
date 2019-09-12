<?php
/*
 * This file is part of Totara LMS
 *
 * Copyright (C) 2019 onwards Totara Learning Solutions LTD
 *
 * This course is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This course is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this course.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author David Curry <david.curry@totaralearning.com>
 * @package core_course
 */

defined('MOODLE_INTERNAL') || die();

use \core\webapi\resolver\query;

/**
 * Tests the totara current learning query resolver
 */
class totara_core_webapi_resolver_query_course_testcase extends advanced_testcase {

    private function get_execution_context(string $type = 'dev', ?string $operation = null) {
        return \core\webapi\execution_context::create($type, $operation);
    }

    /**
     * Create some courses and assign some users for testing.
     * @return []
     */
    private function create_faux_courses(array $users = []) {
        $users = [];
        $users[] = $this->getDataGenerator()->create_user();
        $users[] = $this->getDataGenerator()->create_user();
        $users[] = $this->getDataGenerator()->create_user();

        $courses = [];
        $courses[] = $this->getDataGenerator()->create_course(['shortname' => 'c1', 'fullname' => 'course1', 'summary' => 'first course']);
        $courses[] = $this->getDataGenerator()->create_course(['shortname' => 'c2', 'fullname' => 'course2', 'summary' => 'second course']);
        $courses[] = $this->getDataGenerator()->create_course(['shortname' => 'c3', 'fullname' => 'course3', 'summary' => 'third course', 'visible' => 0]);

        $this->getDataGenerator()->enrol_user($users[0]->id, $courses[0]->id, 'student', 'manual');
        $this->getDataGenerator()->enrol_user($users[1]->id, $courses[0]->id, 'student', 'manual');
        $this->getDataGenerator()->enrol_user($users[1]->id, $courses[1]->id, 'student', 'manual');

        return [$users, $courses];
    }

    /**
     * Test the results of the query when the current user is not logged in.
     */
    public function test_resolve_no_login() {
        list($users, $courses) = $this->create_faux_courses();
        try {
            query\course::resolve(['courseid' => $courses[0]->id], $this->get_execution_context());
            $this->fail('Expected a moodle_exception: cannot view current learnings');
        } catch (\moodle_exception $ex) {
            $this->assertSame('Course or activity not accessible. (You are not logged in)', $ex->getMessage());
        }
    }

    /**
     * Test the results of the query when the current user is logged in as the guest user.
     */
    public function test_resolve_guest_user() {
        list($users, $courses) = $this->create_faux_courses();
        $this->setGuestUser();

        // Guests can view courses (shouldn't have completion data though so...)
        $result = query\course::resolve(['courseid' => $courses[0]->id], $this->get_execution_context());
        $this->assertEquals($courses[0]->id, $result->id);
        $this->assertEquals($courses[0]->fullname, $result->fullname);
        $this->assertEquals($courses[0]->shortname, $result->shortname);

        // Guests should not be able to see hidden courses however.
        try {
            $result = query\course::resolve(['courseid' => $courses[2]->id], $this->get_execution_context());
            $this->fail('Expected a moodle_exception: cannot view course');
        } catch (\moodle_exception $ex) {
            $this->assertSame('Coding error detected, it must be fixed by a programmer: Current user can not access this course.', $ex->getMessage());
        }
    }

    /**
     * Test the results of the query when the current user is the site administrator.
     */
    public function test_resolve_admin_user() {
        list($users, $courses) = $this->create_faux_courses();
        $this->setAdminUser();

        // Admins should be able to see courses, again without completion data.
        $result = query\course::resolve(['courseid' => $courses[0]->id], $this->get_execution_context());
        $this->assertEquals($courses[0]->id, $result->id);
        $this->assertEquals($courses[0]->fullname, $result->fullname);
        $this->assertEquals($courses[0]->shortname, $result->shortname);

        // They should also be able to see hidden courses.
        $result = query\course::resolve(['courseid' => $courses[2]->id], $this->get_execution_context());
        $this->assertEquals($courses[2]->id, $result->id);
        $this->assertEquals($courses[2]->fullname, $result->fullname);
        $this->assertEquals($courses[2]->shortname, $result->shortname);
    }

    /**
     * Test the results of the query match expectations for a course learning item.
     */
    public function test_resolve_course() {
        list($users, $courses) = $this->create_faux_courses();

        // Check visibility for user 0.
        $this->setUser($users[0]);

        // User 0 should be able to see the assigned course[0].
        try {
            $item = query\course::resolve(['courseid' => $courses[0]->id], $this->get_execution_context());

            // Do some checks on the item to make sure it's what we are expecting.
            $this->assertEquals($courses[0]->id, $item->id);
            $this->assertEquals($courses[0]->fullname, $item->fullname);
            $this->assertEquals($courses[0]->shortname, $item->shortname);
            $this->assertEquals($courses[0]->summary, $item->summary);
        } catch (\moodle_exception $ex) {
            $this->fail($ex->getMessage());
        }

        // User 0 should be able to see the unassigned course[1].
        try {
            $item = query\course::resolve(['courseid' => $courses[1]->id], $this->get_execution_context());

            // Do some checks on the item to make sure it's what we are expecting.
            $this->assertEquals($courses[1]->id, $item->id);
            $this->assertEquals($courses[1]->fullname, $item->fullname);
            $this->assertEquals($courses[1]->shortname, $item->shortname);
            $this->assertEquals($courses[1]->summary, $item->summary);
        } catch (\moodle_exception $ex) {
            $this->fail($ex->getMessage());
        }

        // User 0 should not be able to see the hidden course[2].
        try {
            $items = query\course::resolve(['courseid' => $courses[2]->id], $this->get_execution_context());
            $this->fail('Expected a moodle_exception: cannot view course');
        } catch (\moodle_exception $ex) {
            $this->assertSame('Coding error detected, it must be fixed by a programmer: Current user can not access this course.', $ex->getMessage());
        }
    }

    /**
     * Test the results of the AJAX query through the GraphQL stack.
     */
    public function test_ajax_query() {
        list($users, $courses) = $this->create_faux_courses();

        $this->setUser($users[0]);
        $result = \totara_webapi\graphql::execute_operation(
            \core\webapi\execution_context::create('ajax', 'core_course_course'),
            ['courseid' => $courses[0]->id]
        );
        $data = $result->toArray()['data'];

        $category = coursecat::get($courses[0]->category);
        $expected = [
            'core_course' => [
                'id' => "{$courses[0]->id}",
                'idnumber' => "{$courses[0]->idnumber}",
                'fullname' => "{$courses[0]->fullname}",
                'shortname' => "{$courses[0]->shortname}",
                'summary' => "{$courses[0]->summary}",
                'summaryformat' => (int) $courses[0]->summaryformat,
                'category' => [
                    'id' => $category->id,
                    'name' => $category->name
                ]
            ]
        ];
        $this->assertSame($expected, $data);
    }
}
