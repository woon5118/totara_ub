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
use mod_perform\entities\activity\section_relationship as section_relationship_entity;
use mod_perform\models\activity\activity_relationship as activity_relationship_model;
use mod_perform\models\activity\activity;
use mod_perform\models\activity\section;
use mod_perform\models\activity\section_relationship as section_relationship_model;

abstract class mod_perform_relationship_testcase extends advanced_testcase {

    protected function perform_generator(): mod_perform_generator {
        /** @var mod_perform_generator $perform_generator */
        $perform_generator = $this->getDataGenerator()->get_plugin_generator('mod_perform');
        return $perform_generator;
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

        $data->activity1_section1 = $perform_generator->create_section($data->activity1, ['title' => 'Activity 1 section 1']);
        $data->activity1_section2 = $perform_generator->create_section($data->activity1, ['title' => 'Activity 1 section 2']);
        $data->activity2_section1 = $perform_generator->create_section($data->activity2, ['title' => 'Activity 2 section 1']);
        // Section without relationship.
        $data->activity2_section2 = $perform_generator->create_section($data->activity2, ['title' => 'Activity 2 section 2']);

        $data->activity1_relationship1 = activity_relationship_model::create_with_class_name($data->activity1, 'appraiser');
        $data->activity1_relationship2 = activity_relationship_model::create_with_class_name($data->activity1, 'manager');
        $data->activity1_relationship3 = activity_relationship_model::create_with_class_name($data->activity1, 'subject');
        $data->activity2_relationship1 = activity_relationship_model::create_with_class_name($data->activity2, 'subject');

        // Two relationships for activity 1, section 1
        $data->activity1_section1_relationship1 = section_relationship_model::create(
            $data->activity1_section1,
            $data->activity1_relationship1
        );
        $data->activity1_section1_relationship2 = section_relationship_model::create(
            $data->activity1_section1,
            $data->activity1_relationship2
        );

        // One relationship for activity 1, section 2
        $data->activity1_section2_relationship1 = section_relationship_model::create(
            $data->activity1_section2,
            $data->activity1_relationship3
        );

        // One relationship for activity 2's first section.
        $data->activity2_section1_relationship1 = section_relationship_model::create(
            $data->activity2_section1,
            $data->activity2_relationship1
        );

        return $data;
    }

    /**
     * @param section $section
     * @param array $expected_class_names
     */
    protected function assert_section_relationships(section $section, array $expected_class_names): void {
        $section_relationships = section_relationship_entity::repository()->where('section_id', $section->id)->get();
        $actual_class_names = $section_relationships->map(function (section_relationship_entity $section_relationship) {
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
            ->where('activity_id', $activity->id)
            ->get()
            ->pluck('class_name');

        $this->assertEqualsCanonicalizing($expected_class_names, $actual_class_names);
    }
}