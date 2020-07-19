<?php
/**
 * This file is part of Totara LMS
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
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Kian Nguyen <kian.nguyen@totaralearning.com>
 * @package container_course
 */
defined('MOODLE_INTERNAL') || die();

use container_course\course;

class container_course_create_testcase extends advanced_testcase {
    /**
     * @return void
     */
    public function test_create_instance(): void {
        global $DB;
        $this->setAdminUser();

        $category = $DB->get_record('course_categories', [
            'id' => course::get_default_category_id()
        ]);

        $record = new \stdClass();
        $record->fullname = "Course 101";
        $record->shortname = 'c101';
        $record->category = $category->id;
        $record->summary = 'hello world';

        $course = course::create($record);
        $dbrecord = $DB->get_record('course', ['id' => $course->id]);

        $this->assertEquals(course::get_type(), $dbrecord->containertype);
    }
}