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
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Cody Finegan <cody.finegan@totaralearning.com>
 * @package block_totara_recommendations
 */

use block_totara_recommendations\repository\recommendations_repository;

defined('MOODLE_INTERNAL') || die();

/**
 * @group block_totara_recommendations
 */
class totara_recommendations_courses_testcase extends advanced_testcase {
    /**
     * Test that the correct type of records are returned for micro_learning
     */
    public function test_courses_block() {
        global $DB, $CFG;
        require_once($CFG->dirroot . '/enrol/locallib.php');

        $generator = $this->getDataGenerator();

        // Create our users
        $user1 = $generator->create_user(['username' => 'user1']);
        $user2 = $generator->create_user(['username' => 'user2']);

        // Create the test courses
        $course1 = $generator->create_course(['fullname' => 'Course 1 with self-enrol & recommended']);
        $course2 = $generator->create_course(['fullname' => 'Course 2 with self-enrol & not recommended']);
        $course3 = $generator->create_course(['fullname' => 'Course 3 without self-enrol & recommended']);
        $course4 = $generator->create_course(['fullname' => 'Course 4 without self-enrol & not recommended']);

        // Enable self-enrollments for Course 1 & 2
        $enrol = $DB->get_record('enrol', array('courseid' => $course1->id, 'enrol' => 'self'), '*', MUST_EXIST);
        $enrol->status = ENROL_INSTANCE_ENABLED;
        $DB->update_record('enrol', $enrol);

        $enrol = $DB->get_record('enrol', array('courseid' => $course2->id, 'enrol' => 'self'), '*', MUST_EXIST);
        $enrol->status = ENROL_INSTANCE_ENABLED;
        $DB->update_record('enrol', $enrol);

        // Recommend course 1 & 3
        $users = [$user1, $user2];
        foreach ($users as $user) {
            $DB->insert_record('ml_recommender_users', [
                'user_id' => $user->id,
                'unique_id' => 'container_course' . $course1->id . '_user' . $user->id,
                'item_id' => $course1->id,
                'component' => 'container_course',
                'time_created' => time(),
                'score' => 1
            ]);
            $DB->insert_record('ml_recommender_users', [
                'user_id' => $user->id,
                'unique_id' => 'container_course' . $course3->id . '_user' . $user->id,
                'item_id' => $course3->id,
                'component' => 'container_course',
                'time_created' => time(),
                'score' => 1
            ]);
        }

        // User 1 is enrolled, user 2 is not
        $generator->enrol_user($user1->id, $course1->id);
        $generator->enrol_user($user1->id, $course2->id);
        $generator->enrol_user($user1->id, $course3->id);
        $generator->enrol_user($user1->id, $course4->id);

        // User 1 should not see any recommendations
        $records = recommendations_repository::get_recommended_courses(5, $user1->id);
        $this->assertEmpty($records);

        // User 2 should see Course 1 recommended
        $records = recommendations_repository::get_recommended_courses(5, $user2->id);
        $this->assertNotEmpty($records);
        $this->assertCount(1, $records);

        $record = current($records);
        $this->assertEquals($course1->id, $record->item_id);

        // Now unenrol user 1 from course 1, then see if it's recommended
        $plugin = enrol_get_plugin('manual');
        $instance = $DB->get_record('enrol', ['courseid' => $course1->id, 'enrol' => 'manual']);
        $plugin->unenrol_user($instance, $user1->id);

        // User 1 should see Course 1 recommended
        $records = recommendations_repository::get_recommended_courses(5, $user1->id);
        $this->assertNotEmpty($records);
        $this->assertCount(1, $records);

        $record = current($records);
        $this->assertEquals($course1->id, $record->item_id);
    }
}