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
 * @author Matthias Bonk <matthias.bonk@totaralearning.com>
 * @package mod_perform
 */

use mod_perform\entities\activity\activity_relationship;
use mod_perform\entities\activity\section_relationship;
use mod_perform\models\activity\activity;
use mod_perform\models\activity\section;

abstract class mod_perform_relationship_testcase extends advanced_testcase {

    protected function perform_generator() {
        return $this->getDataGenerator()->get_plugin_generator('mod_perform');
    }

    /**
     * @return object
     */
    protected function create_test_data() {
        /** @var mod_perform_generator $perform_generator */
        $perform_generator = $this->getDataGenerator()->get_plugin_generator('mod_perform');

        $data = new stdClass();

        /*
         * Set up:
         *
         * activity1
         *   - section1_1
         *       - relationship1_1_1: appraiser
         *       - relationship1_1_2: manager
         *   - section1_2
         *       - relationship1_2_1: subject
         *
         * activity2
         *   - section2_1
         *       - relationship2_1_1: subject
         *   - section2_2
         *
         * activity3
         *   (no sections, no relationships)
         */
        $data->activity1 = $perform_generator->create_activity_in_container(['activity_name' => 'Activity 1']);
        $data->activity2 = $perform_generator->create_activity_in_container(['activity_name' => 'Activity 2']);
        $data->activity3 = $perform_generator->create_activity_in_container(['activity_name' => 'Activity 3']);

        $data->section1_1 = $perform_generator->create_section($data->activity1, ['title' => 'Test section 1 1']);
        $data->section1_2 = $perform_generator->create_section($data->activity1, ['title' => 'Test section 1 2']);
        $data->section2_1 = $perform_generator->create_section($data->activity2, ['title' => 'Test section 2 1']);
        // Section without relationship.
        $data->section2_2 = $perform_generator->create_section($data->activity2, ['title' => 'Test section 2 2']);

        // Two relationships for activity 1, section 1
        $data->relationship1_1_1 = $perform_generator->create_section_relationship(
            $data->section1_1,
            ['class_name' => 'appraiser']
        );
        $data->relationship1_1_2 = $perform_generator->create_section_relationship(
            $data->section1_1,
            ['class_name' => 'manager']
        );
        // One relationship for activity 1, section 2
        $data->relationship1_2_1 = $perform_generator->create_section_relationship(
            $data->section1_2,
            ['class_name' => 'subject']
        );

        // One relationship for activity 2's first section.
        $data->relationship2_1_1 = $perform_generator->create_section_relationship(
            $data->section2_1,
            ['class_name' => 'subject']
        );

        return $data;
    }

    /**
     * @param section $section
     * @param array $expected_class_names
     */
    protected function assert_section_relationships(section $section, array $expected_class_names): void {
        $section_relationships = section_relationship::repository()->where('section_id', $section->get_id())->get();
        $actual_class_names = $section_relationships->map(function (section_relationship $section_relationship) {
            return $section_relationship->activity_relationship()->one(true)->get_attribute('class_name');
        })->all();

        $this->assertEqualsCanonicalizing($expected_class_names, $actual_class_names);
    }

    /**
     * @param activity $activity
     * @param array $expected_class_names
     */
    protected function assert_activity_relationships(activity $activity, array $expected_class_names): void {
        $actual_class_names = activity_relationship::repository()
            ->where('activity_id', $activity->get_id())
            ->get()
            ->pluck('class_name');

        $this->assertEqualsCanonicalizing($expected_class_names, $actual_class_names);
    }
}