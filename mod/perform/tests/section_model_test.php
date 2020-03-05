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

use mod_perform\models\activity\section;

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

    public function test_update_relationships() {
        self::setAdminUser();
        $perform_generator = $this->perform_generator();
        $activity1 = $perform_generator->create_activity_in_container(['activity_name' => 'Activity 1']);
        $activity2 = $perform_generator->create_activity_in_container(['activity_name' => 'Activity 2']);
        $section1 = $perform_generator->create_section($activity1);
        $section2 = $perform_generator->create_section($activity1);
        $this->assert_section_relationships($section1, []);
        $this->assert_section_relationships($section2, []);

        // Add three relationships to section1.
        $returned_section = $section1->update_relationships(['appraiser', 'manager', 'subject']);
        $this->assertEquals($section1, $returned_section);
        $this->assert_section_relationships($section1, ['appraiser', 'manager', 'subject']);
        $this->assert_section_relationships($section2, []);
        $this->assert_activity_relationships($activity1, ['appraiser', 'manager', 'subject']);
        $this->assert_activity_relationships($activity2, []);

        // Remove one relationship.
        $section1->update_relationships(['appraiser', 'manager']);
        $this->assert_section_relationships($section1, ['appraiser', 'manager']);
        $this->assert_section_relationships($section2, []);
        $this->assert_activity_relationships($activity1, ['appraiser', 'manager']);
        $this->assert_activity_relationships($activity2, []);

        // Add to section2.
        $section2->update_relationships(['manager', 'subject']);
        $this->assert_section_relationships($section1, ['appraiser', 'manager']);
        $this->assert_section_relationships($section2, ['manager', 'subject']);
        $this->assert_activity_relationships($activity1, ['appraiser', 'manager', 'subject']);
        $this->assert_activity_relationships($activity2, []);

        // Remove all from section1.
        $section1->update_relationships([]);
        $this->assert_section_relationships($section1, []);
        $this->assert_section_relationships($section2, ['manager', 'subject']);
        $this->assert_activity_relationships($activity1, ['manager', 'subject']);
        $this->assert_activity_relationships($activity2, []);

        // Remove all from section2.
        $section2->update_relationships([]);
        $this->assert_section_relationships($section1, []);
        $this->assert_section_relationships($section2, []);
        $this->assert_activity_relationships($activity1, []);
        $this->assert_activity_relationships($activity2, []);
    }
}