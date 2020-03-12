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

use mod_perform\models\activity\activity_relationship;
use mod_perform\models\activity\section_relationship;

require_once(__DIR__.'/relationship_testcase.php');

/**
 * @group perform
 */
class mod_perform_section_relationship_model_testcase extends mod_perform_relationship_testcase {

    public function test_create_invalid_classname() {
        $this->setAdminUser();
        $perform_generator = $this->perform_generator();
        $activity1 = $perform_generator->create_activity_in_container();

        $this->expectException(coding_exception::class);
        $this->expectExceptionMessage('Invalid class_name');

        activity_relationship::create_with_class_name($activity1, 'non-existent-classname');
    }

    public function test_create_successful() {
        $this->setAdminUser();
        $perform_generator = $this->perform_generator();
        $activity1 = $perform_generator->create_activity_in_container();
        $section1 = $perform_generator->create_section($activity1);
        $section2 = $perform_generator->create_section($activity1);

        $this->assert_section_relationships($section1, []);
        $this->assert_section_relationships($section2, []);

        $subject_activity_relationship = activity_relationship::create_with_class_name($activity1, 'subject');
        $section_relationship = section_relationship::create($section1, $subject_activity_relationship);
        $this->assertEquals($section1->id, $section_relationship->section_id);
        $this->assert_section_relationships($section1, ['subject']);
        $this->assert_section_relationships($section2, []);

        // Add another one to the same section.
        $manager_activity_relationship = activity_relationship::create_with_class_name($activity1, 'manager');
        section_relationship::create($section1, $manager_activity_relationship);
        $this->assert_section_relationships($section1, ['subject', 'manager']);
        $this->assert_section_relationships($section2, []);

        // Add another one to the other section.
        $appraiser_activity_relationship = activity_relationship::create_with_class_name($activity1, 'appraiser');
        section_relationship::create($section2, $appraiser_activity_relationship);
        $this->assert_section_relationships($section1, ['subject', 'manager']);
        $this->assert_section_relationships($section2, ['appraiser']);
    }

    public function test_delete_successful() {
        $this->setAdminUser();
        /** @var mod_perform_generator $perform_generator */
        $perform_generator = $this->perform_generator();
        $activity1 = $perform_generator->create_activity_in_container();
        $activity2 = $perform_generator->create_activity_in_container();
        $section1 = $perform_generator->create_section($activity1);
        $section2 = $perform_generator->create_section($activity1);
        $section3 = $perform_generator->create_section($activity2);

        $this->assert_section_relationships($section1, []);
        $this->assert_section_relationships($section2, []);
        $this->assert_section_relationships($section3, []);

        $subject1_activity_relationship = activity_relationship::create_with_class_name($activity1, 'subject');
        $manager1_activity_relationship = activity_relationship::create_with_class_name($activity1, 'manager');
        $appraiser1_activity_relationship = activity_relationship::create_with_class_name($activity1, 'appraiser');
        $appraiser2_activity_relationship = activity_relationship::create_with_class_name($activity2, 'appraiser');

        $subject1_section_relationship = section_relationship::create($section1, $subject1_activity_relationship);
        section_relationship::create($section1, $manager1_activity_relationship);
        $appraiser1_section_relationship = section_relationship::create($section1, $appraiser1_activity_relationship);
        section_relationship::create($section2, $appraiser2_activity_relationship);
        $this->assert_section_relationships($section1, ['manager', 'subject', 'appraiser']);
        $this->assert_section_relationships($section2, ['appraiser']);
        $this->assert_section_relationships($section3, []);

        $subject1_section_relationship->delete();
        $this->assert_section_relationships($section1, ['manager', 'appraiser']);
        $this->assert_section_relationships($section2, ['appraiser']);
        $this->assert_section_relationships($section3, []);

        $appraiser1_section_relationship->delete();
        $this->assert_section_relationships($section1, ['manager']);
        $this->assert_section_relationships($section2, ['appraiser']);
        $this->assert_section_relationships($section3, []);
    }
}