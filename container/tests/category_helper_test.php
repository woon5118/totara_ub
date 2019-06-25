<?php
/**
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
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Kian Nguyen <kian.nguyen@totaralearning.com>
 * @package core_container
 */
defined('MOODLE_INTERNAL') || die();

use core_container\container_category_helper;

class core_container_category_helper_testcase extends advanced_testcase {
    /**
     * @return void
     */
    public function test_get_default_category(): void {
        global $DB;

        $generator = $this->getDataGenerator();
        $user = $generator->create_user();

        $category_id = container_category_helper::get_default_category_id(
            'container_course',
            true,
            null,
            $user->id
        );

        $this->assertNotEmpty($category_id);
        $this->assertTrue(
            $DB->record_exists('course_categories', ['id' => $category_id])
        );

        // The first run is where the creation happen if the record is not found.
        // However check if the api is returning the same result if there is actual record.
        $second_category_id = container_category_helper::get_default_category_id(
            'container_course',
            false,
            null,
            $user->id
        );

        $this->assertEquals($category_id, $second_category_id);
    }

    /**
     * @return void
     */
    public function test_create_category(): void {
        global $DB;

        $course_cat = container_category_helper::create_container_category(
            'container_course',
            0,
            null,
            null
        );

        $this->assertTrue(
            $DB->record_exists('course_categories', ['id' => $course_cat->id])
        );

        $this->assertSame('container_course-0', $course_cat->idnumber);
    }
}