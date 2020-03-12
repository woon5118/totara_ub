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

use mod_perform\entities\activity\activity_relationship as activity_relationship_entity;
use mod_perform\entities\activity\section;
use mod_perform\entities\activity\section_relationship as section_relationship_entity;
use mod_perform\models\activity\activity;
use mod_perform\models\activity\activity_relationship;
use mod_perform\models\activity\section_relationship;

require_once(__DIR__.'/relationship_testcase.php');

/**
 * @group perform
 */
class mod_perform_activity_relationship_model_testcase extends mod_perform_relationship_testcase {

    public function test_create_invalid_classname() {
        $this->setAdminUser();
        $perform_generator = $this->perform_generator();
        $activity1 = $perform_generator->create_activity_in_container();

        $this->expectException(coding_exception::class);
        $this->expectExceptionMessage('Invalid class_name');

        activity_relationship::create_with_class_name($activity1, 'non-existent-classname');
    }

    public function test_create_successful2() {
        $this->setAdminUser();
        $perform_generator = $this->perform_generator();
        $activity1 = $perform_generator->create_activity_in_container();
        $activity2 = $perform_generator->create_activity_in_container();

        $this->assert_activity_relationships($activity1, []);
        $this->assert_activity_relationships($activity2, []);

        activity_relationship::create_with_class_name($activity1, 'subject');
        $this->assert_activity_relationships($activity1, ['subject']);
        $this->assert_activity_relationships($activity2, []);

        // Add another one to the same activity.
        activity_relationship::create_with_class_name($activity1, 'manager');
        $this->assert_activity_relationships($activity1, ['subject', 'manager']);
        $this->assert_activity_relationships($activity2, []);

        // Add another one to the other activity.
        activity_relationship::create_with_class_name($activity1, 'appraiser');
        $this->assert_activity_relationships($activity1, ['subject', 'manager', 'appraiser']);
        $this->assert_activity_relationships($activity2, []);
    }

    public function test_delete_prevented_by_section_relationship() {
        $this->setAdminUser();
        /** @var mod_perform_generator $perform_generator */
        $perform_generator = $this->perform_generator();
        $activity = $perform_generator->create_activity_in_container();
        $section = $perform_generator->create_section($activity);

        $subject_activity_relationship = activity_relationship::create_with_class_name($activity, 'subject');
        section_relationship::create($section, $subject_activity_relationship);

        $this->expectException(coding_exception::class);
        $this->expectExceptionMessage('Cannot delete activity relationship because it is still in use');

        $subject_activity_relationship->delete();
    }

    public function test_delete_successful() {
        $this->setAdminUser();
        /** @var mod_perform_generator $perform_generator */
        $perform_generator = $this->perform_generator();
        $activity1 = $perform_generator->create_activity_in_container();
        $activity2 = $perform_generator->create_activity_in_container();

        $this->assert_activity_relationships($activity1, []);
        $this->assert_activity_relationships($activity2, []);

        $subject1_activity_relationship = activity_relationship::create_with_class_name($activity1, 'subject');
        activity_relationship::create_with_class_name($activity1, 'manager');
        $appraiser1_activity_relationship = activity_relationship::create_with_class_name($activity1, 'appraiser');
        activity_relationship::create_with_class_name($activity2, 'appraiser');

        $this->assert_activity_relationships($activity1, ['manager', 'subject', 'appraiser']);
        $this->assert_activity_relationships($activity2, ['appraiser']);

        $subject1_activity_relationship->delete();
        $this->assert_activity_relationships($activity1, ['manager', 'appraiser']);
        $this->assert_activity_relationships($activity2, ['appraiser']);

        $appraiser1_activity_relationship->delete();
        $this->assert_activity_relationships($activity1, ['manager']);
        $this->assert_activity_relationships($activity2, ['appraiser']);
    }
}