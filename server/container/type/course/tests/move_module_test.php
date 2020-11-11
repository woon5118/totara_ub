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
 * @package container_course
 */
defined('MOODLE_INTERNAL') || die();

use container_course\course;
use container_course\module\course_module;

class container_course_move_module_testcase extends advanced_testcase {
    /**
     * @return void
     */
    public function test_move_module_within_a_section(): void {
        $generator = self::getDataGenerator();
        $course_record = $generator->create_course();

        $course = course::from_record($course_record);

        $mod_f2f_one = $generator->create_module('facetoface', ['course' => $course->id]);
        $mod_f2f_two = $generator->create_module('facetoface', ['course' => $course->id]);

        $module_one = course_module::from_id($mod_f2f_one->cmid);
        $module_two = course_module::from_id($mod_f2f_two->cmid);

        $section_zero = $course->get_section(0);
        $mod_sequences = $section_zero->get_sequence();

        self::assertEquals(
            [$module_one->get_id(), $module_two->get_id()],
            $mod_sequences
        );

        $module_two->move_to_section($section_zero->get_section_number(), $module_one->get_id());
        $section_zero->reload();

        $updated_mod_sequences = $section_zero->get_sequence();
        self::assertEquals(
            [$module_two->get_id(), $module_one->get_id()],
            $updated_mod_sequences
        );
    }

    /**
     * @return void
     */
    public function test_to_not_move_module_within_a_section(): void {
        $generator = self::getDataGenerator();
        $course_record = $generator->create_course();
        $generator->create_module('facetoface', ['course' => $course_record->id]);

        $course = course::from_record($course_record);
        $section_zero = $course->get_section(0);

        $all_modules = $section_zero->get_all_modules();
        self::assertCount(1, $all_modules);

        $module_one = reset($all_modules);
        self::assertEquals($section_zero->get_id(), $module_one->get_section()->get_id());

        $result = $module_one->move_to_section(0);
        self::assertFalse($result);

        $section_zero->reload();
        $updated_all_modules = $section_zero->get_all_modules();
        self::assertCount(1, $updated_all_modules);

        $module_one->reload();
        self::assertEquals($section_zero->get_id(), $module_one->get_section()->get_id());
    }
}