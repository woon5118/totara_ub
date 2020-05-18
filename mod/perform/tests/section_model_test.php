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

use mod_perform\entities\activity\section as section_entity;
use mod_perform\models\activity\section;
use totara_core\relationship\resolvers\subject;
use totara_job\relationship\resolvers\appraiser;
use totara_job\relationship\resolvers\manager;

require_once(__DIR__.'/relationship_testcase.php');

/**
 * @group perform
 */
class mod_perform_section_model_testcase extends mod_perform_relationship_testcase {

    public function test_create() {
        $this->setAdminUser();
        $activity = $this->perform_generator()->create_activity_in_container();

        $section = section::create($activity, 'section name one');
        $this->assertSame('section name one', $section->title);
    }

    public function test_get_title() {
        $this->setAdminUser();
        $activity = $this->perform_generator()->create_activity_in_container();

        $section1 = section::create($activity, 'Test Section');
        $section2 = section::create($activity, '   ');
        $section3 = section::create($activity);

        $this->assertEquals('Test Section', $section1->title);
        $this->assertEquals('Section 2', $section2->title);
        $this->assertEquals('Section 3', $section3->title);

        section_entity::repository()->where('id', $section1->id)->delete();

        $this->assertEquals('Section 1', $section2->title);
        $this->assertEquals('Section 2', $section3->title);

        $section4 = section::create($activity, '');
        $section5 = section::create($activity, 0);
        section_entity::repository()->where('id', $section3->id)->delete();

        $this->assertEquals('Section 1', $section2->title);
        $this->assertEquals('Section 2', $section4->title);
        $this->assertEquals('0', $section5->title);
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

        $appraiser_id = $perform_generator->get_relationship(appraiser::class)->id;
        $manager_id = $perform_generator->get_relationship(manager::class)->id;
        $subject_id = $perform_generator->get_relationship(subject::class)->id;

        // Add three relationships to section1.
        $returned_section = $section1->update_relationships(
            [
                [
                    'id' => $appraiser_id,
                    'can_view' => true,
                ],
                [
                    'id' => $manager_id,
                    'can_view' => true,
                ],
                [
                    'id' => $subject_id,
                    'can_view' => true,
                ],
            ]
        );
        $this->assertEquals($section1, $returned_section);
        $this->assert_section_relationships($section1, [appraiser::class, manager::class, subject::class]);
        $this->assert_section_relationships($section2, []);
        $this->assert_activity_relationships($activity1, [appraiser::class, manager::class, subject::class]);
        $this->assert_activity_relationships($activity2, []);

        // Remove one relationship.
        $section1->update_relationships(
            [
                [
                    'id' => $appraiser_id,
                    'can_view' => true,
                ],
                [
                    'id' => $manager_id,
                    'can_view' => true,
                ]
            ]
        );
        $this->assert_section_relationships($section1, [appraiser::class, manager::class]);
        $this->assert_section_relationships($section2, []);
        $this->assert_activity_relationships($activity1, [appraiser::class, manager::class]);
        $this->assert_activity_relationships($activity2, []);

        // Add to section2.
        $section2->update_relationships(
            [
                [
                    'id' => $manager_id,
                    'can_view' => true,
                ],
                [
                    'id' => $subject_id,
                    'can_view' => true,
                ]
            ]
        );
        $this->assert_section_relationships($section1, [appraiser::class, manager::class]);
        $this->assert_section_relationships($section2, [manager::class, subject::class]);
        $this->assert_activity_relationships($activity1, [appraiser::class, manager::class, subject::class]);
        $this->assert_activity_relationships($activity2, []);

        // Remove all from section1.
        $section1->update_relationships([]);
        $this->assert_section_relationships($section1, []);
        $this->assert_section_relationships($section2, [manager::class, subject::class]);
        $this->assert_activity_relationships($activity1, [manager::class, subject::class]);
        $this->assert_activity_relationships($activity2, []);

        // Remove all from section2.
        $section2->update_relationships([]);
        $this->assert_section_relationships($section1, []);
        $this->assert_section_relationships($section2, []);
        $this->assert_activity_relationships($activity1, []);
        $this->assert_activity_relationships($activity2, []);
    }
}