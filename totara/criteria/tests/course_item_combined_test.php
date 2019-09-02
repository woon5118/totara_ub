<?php
/*
 * This file is part of Totara Learn
 *
 * Copyright (C) 2019 onwards Totara Learning Solutions LTD
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
 * @author Brendan Cox <brendan.cox@totaralearning.com>
 * @author Riana Rossouw <riana.rossouw@totaralearning.com>
 * @package totara_criteria
 */

use totara_competency\aggregation_users_table;
use totara_criteria\course_item_combined;
use totara_criteria\course_item_evaluator;
use totara_criteria\criterion;
use totara_criteria\item_evaluator;

class totara_criteria_course_item_combined_testcase extends advanced_testcase {

    private function setup_data() {
        $data = new class() {
            public $users;
            public $course;
            public $coursecompletion;
            public $temp_table_def;
        };

        $this->setAdminUser();
        $GLOBALS['USER']->ignoresesskey = true;

        $data->users = [];
        $prefix = 'User ';
        for ($i = 1; $i <= 2; $i++) {
            $data->users[$i] = $this->getDataGenerator()->create_user();
        }

        $record = [
            'shortname' => "Course $i",
            'fullname' => "Course $i",
        ];

        $data->course = $this->getDataGenerator()->create_course($record);

        $generator = $this->getDataGenerator()->get_plugin_generator('totara_criteria');

        $record = ['courseids' => [$data->course->id]];
        $data->coursecompletion = $generator->create_coursecompletion($record);

        $data->temp_table_def = new aggregation_users_table('totara_competency_temp_users',
            'user_id',
            'has_changed',
            'process_key'
        );

        return $data;
    }

    private function insert_temp_users(array $user_ids, int $has_changed = 0) {
        global $DB;

        $temp_records = [];
        foreach ($user_ids as $id) {
            $temp_records[] = ['user_id' => $id, 'has_changed' => $has_changed];
        }

        $DB->insert_records('totara_competency_temp_users', $temp_records);
    }


    public function test_update_completion_no_data_table() {
        global $DB;

        $data = $this->setup_data();

        $this->assertSame(0, $DB->count_records('totara_criteria_item_record'));
        $item_combined = new course_item_combined($data->temp_table_def);
        $item_combined->update_completion($data->coursecompletion);
        $this->assertSame(0, $DB->count_records('totara_criteria_item_record'));
    }

    public function test_update_completion_no_data_list() {
        global $DB;

        $data = $this->setup_data();

        $this->assertSame(0, $DB->count_records('totara_criteria_item_record'));
        $item_combined = new course_item_combined([]);
        $item_combined->update_completion($data->coursecompletion);
        $this->assertSame(0, $DB->count_records('totara_criteria_item_record'));
    }

    public function test_update_completion_create_and_delete_table() {
        global $DB;

        $data = $this->setup_data();

        $this->assertSame(0, $DB->count_records('totara_criteria_item_record'));

        $test_users = [1, 2, 3];

        // New assignments only
        $this->insert_temp_users($test_users);
        $item_combined = new course_item_combined($data->temp_table_def);
        $item_combined->update_completion($data->coursecompletion);

        // No criteria were met
        $item_records = $DB->get_records('totara_criteria_item_record');
        $this->assertSame(count($test_users), count($item_records));
        foreach ($item_records as $record) {
            $this->assertEquals(0, $record->criterion_met);
        }

        // All users have changes
        $temp_records = $DB->get_records('totara_competency_temp_users');
        $this->assertSame(count($test_users), count($temp_records));
        foreach ($temp_records as $record) {
            $this->assertEquals(1, $record->has_changed);
        }

        // Add and delete some assignments
        $DB->delete_records('totara_competency_temp_users', ['user_id' => 2]);
        $this->insert_temp_users([10, 11]);
        $test_users = [1, 3, 10, 11];

        $item_combined->update_completion($data->coursecompletion);

        // User 2 should no longer have an item_record, but 10 and 11 should have been added
        $item_records = $DB->get_records('totara_criteria_item_record');
        $this->assertSame(count($test_users), count($item_records));
        foreach ($item_records as $record) {
            $this->assertTrue(in_array($record->user_id, $test_users));
            $this->assertEquals(0, $record->criterion_met);
        }

        // Delete all
        $DB->delete_records('totara_competency_temp_users');
        $item_combined->update_completion($data->coursecompletion);

        $item_records = $DB->get_records('totara_criteria_item_record');
        $this->assertSame(0, count($item_records));
    }

    public function test_update_completion_create_and_delete_list() {
        global $DB;

        $data = $this->setup_data();

        $this->assertSame(0, $DB->count_records('totara_criteria_item_record'));

        $test_users = [1, 2, 3];

        // New assignments only
        $this->insert_temp_users($test_users);
        $item_combined = new course_item_combined($test_users);
        $item_combined->update_completion($data->coursecompletion);

        // No criteria were met
        $item_records = $DB->get_records('totara_criteria_item_record');
        $this->assertSame(count($test_users), count($item_records));
        foreach ($item_records as $record) {
            $this->assertEquals(0, $record->criterion_met);
        }

        // All users have changes, but as we are not using the temp table, the temp table should not have been touched
        $temp_records = $DB->get_records('totara_competency_temp_users');
        $this->assertSame(count($test_users), count($temp_records));
        foreach ($temp_records as $record) {
            $this->assertEquals(0, $record->has_changed);
        }

        // Delete some assignments, add others - using a list, so need a new course_item_combined with the new list of ids
        $test_users = [1, 3, 10, 11];
        $item_combined = new course_item_combined($test_users);
        $item_combined->update_completion($data->coursecompletion);

        // User 2 should no longer have an item_record, but 10 and 11 should have been added
        $item_records = $DB->get_records('totara_criteria_item_record');
        $this->assertSame(count($test_users), count($item_records));
        foreach ($item_records as $record) {
            $this->assertTrue(in_array($record->user_id, $test_users));
            $this->assertEquals(0, $record->criterion_met);
        }

        // Delete all
        $test_users = [];
        $item_combined = new course_item_combined($test_users);
        $item_combined->update_completion($data->coursecompletion);

        $item_records = $DB->get_records('totara_criteria_item_record');
        $this->assertSame(0, count($item_records));
    }

    public function test_update_completion_new_course_completion_table() {
        global $DB;

        $data = $this->setup_data();

        $test_users = [$data->users[1]->id, $data->users[2]->id];

        // Setting up ...
        // 2 users assigned
        $this->insert_temp_users($test_users);

        // Create the initial item_records and wait for a second to ensure we have unique timestamps
        $item_combined = new course_item_combined($data->temp_table_def);
        $item_combined->update_completion($data->coursecompletion);
        $this->waitForSecond();

        // Reset the has_changed flag for all assignments which was set when the item_records were created
        $DB->execute('UPDATE {totara_competency_temp_users} SET has_changed = 0');

        // Mark user1 to have completed the course
        /** @var completion_completion $completion */
        $completion = new completion_completion(['course' => $data->course->id, 'userid' => $data->users[1]->id]);
        $completion->mark_complete();

        // Now for the real test ...
        // Testing that we mark item_records updated since the last time we performed the completion evaluation
        $this->waitForSecond();
        $item_combined->update_completion($data->coursecompletion);

        // The criterion_met should have been updated for user1, but not user2.
        $item_records = $DB->get_records('totara_criteria_item_record');
        $this->assertSame(count($test_users), count($item_records));

        foreach ($item_records as $record) {
            $this->assertTrue(in_array($record->user_id, $test_users));
            if ($record->user_id == $data->users[1]->id) {
                $this->assertEquals(1, $record->criterion_met);
            } else {
                $this->assertEquals(0, $record->criterion_met);
            }
        }

        // Similarly - user1's has_changes in the temp table should be set, but not user2's
        $temp_records = $DB->get_records('totara_competency_temp_users');
        $this->assertSame(count($test_users), count($temp_records));
        foreach ($temp_records as $record) {
            if ($record->user_id == $data->users[1]->id) {
                $this->assertEquals(1, $record->has_changed);
            } else {
                $this->assertEquals(0, $record->has_changed);
            }
        }
    }

    public function test_update_completion_new_course_completion_list() {
        global $DB;

        $data = $this->setup_data();

        $test_users = [$data->users[1]->id, $data->users[2]->id];

        // Setting up ...
        // Create the initial item_records and wait for a second to ensure we have unique timestamps
        $item_combined = new course_item_combined($test_users);
        $item_combined->update_completion($data->coursecompletion);
        $this->waitForSecond();

        // Mark user1 to have completed the course
        /** @var completion_completion $completion */
        $completion = new completion_completion(['course' => $data->course->id, 'userid' => $data->users[1]->id]);
        $completion->mark_complete();

        // Now for the real test ...
        // Testing that we mark item_records updated since the last time we performed the completion evaluation
        $this->waitForSecond();
        $item_combined->update_completion($data->coursecompletion);

        // The criterion_met should have been updated for user1, but not user2.
        $item_records = $DB->get_records('totara_criteria_item_record');
        $this->assertSame(count($test_users), count($item_records));

        foreach ($item_records as $record) {
            $this->assertTrue(in_array($record->user_id, $test_users));
            if ($record->user_id == $data->users[1]->id) {
                $this->assertEquals(1, $record->criterion_met);
            } else {
                $this->assertEquals(0, $record->criterion_met);
            }
        }
    }

    public function test_update_completion_course_completion_no_longer_exist_table() {
        global $DB;

        $data = $this->setup_data();

        $test_users = [$data->users[1]->id, $data->users[2]->id];

        // Setting up ...
        // 2 users assigned
        $this->insert_temp_users($test_users);

        // Create the initial item_records - tested in another function
        $item_combined = new course_item_combined($data->temp_table_def);
        $item_combined->update_completion($data->coursecompletion);
        // Reset the has_changed flag
        $DB->execute('UPDATE {totara_competency_temp_users} SET has_changed = 0');

        // Now for the real test ...
        // Testing that the sql statements to catch item_records with invalid criterion_met values are updated correctly

        $DB->execute('UPDATE {totara_criteria_item_record} SET criterion_met = 1 WHERE user_id = :user2',
            ['user2' => $data->users[2]->id]);

        $this->waitForSecond();
        $item_combined->update_completion($data->coursecompletion);

        // The criterion_met should have been updated for user1, but not user2.
        $item_records = $DB->get_records('totara_criteria_item_record');
        $this->assertSame(count($test_users), count($item_records));

        foreach ($item_records as $record) {
            $this->assertTrue(in_array($record->user_id, $test_users));
            $this->assertEquals(0, $record->criterion_met);
        }

        // Similarly - user1's has_changes in the temp table should be set, but not user2's
        $temp_records = $DB->get_records('totara_competency_temp_users');
        $this->assertSame(count($test_users), count($temp_records));
        foreach ($temp_records as $record) {
            if ($record->user_id == $data->users[1]->id) {
                $this->assertEquals(0, $record->has_changed);
            } else {
                $this->assertEquals(1, $record->has_changed);
            }
        }
    }

    public function test_update_completion_course_completion_no_longer_exist_list() {
        global $DB;

        $data = $this->setup_data();

        $test_users = [$data->users[1]->id, $data->users[2]->id];

        // Setting up ...
        // Create the initial item_records - tested in another function
        $item_combined = new course_item_combined($test_users);
        $item_combined->update_completion($data->coursecompletion);

        // Now for the real test ...
        // Testing that the sql statements to catch item_records with invalid criterion_met values are updated correctly

        $DB->execute('UPDATE {totara_criteria_item_record} SET criterion_met = 1 WHERE user_id = :user2',
            ['user2' => $data->users[2]->id]);

        $this->waitForSecond();
        $item_combined->update_completion($data->coursecompletion);

        // The criterion_met should have been updated for user1, but not user2.
        $item_records = $DB->get_records('totara_criteria_item_record');
        $this->assertSame(count($test_users), count($item_records));

        foreach ($item_records as $record) {
            $this->assertTrue(in_array($record->user_id, $test_users));
            $this->assertEquals(0, $record->criterion_met);
        }
    }

    public function test_update_completion_course_completion_missed_by_observer_table() {
        global $DB;

        $data = $this->setup_data();

        $test_users = [$data->users[1]->id, $data->users[2]->id];

        // Setting up ...
        // 2 users assigned
        $this->insert_temp_users($test_users);

        // Create the initial item_records and wait for a second to ensure we have unique timestamps
        $item_combined = new course_item_combined($data->temp_table_def);
        $item_combined->update_completion($data->coursecompletion);
        $this->waitForSecond();

        // Reset the has_changed flag for all assignments which was set when the item_records were created
        $DB->execute('UPDATE {totara_competency_temp_users} SET has_changed = 0');

        // Mark user1 to have completed the course
        /** @var completion_completion $completion */
        $completion = new completion_completion(['course' => $data->course->id, 'userid' => $data->users[1]->id]);
        $completion->mark_complete();

        // For testing purposes only - resetting the item_record.criterion_met flag that is set in the
        // event observer when the course was marked as completed by user1
        // This is still a valid test as it handles the case where the event observer failed to set the criterion_met flag
        // for some or other reason.
        $DB->execute('UPDATE {totara_criteria_item_record} SET criterion_met = 0');

        // Now for the real test ...
        // Testing that the sql statements to catch item_records with invalid criterion_met values are updated correctly
        $this->waitForSecond();
        $item_combined->update_completion($data->coursecompletion);

        // The criterion_met should have been updated for user1, but not user2.
        $item_records = $DB->get_records('totara_criteria_item_record');
        $this->assertSame(count($test_users), count($item_records));

        foreach ($item_records as $record) {
            $this->assertTrue(in_array($record->user_id, $test_users));
            if ($record->user_id == $data->users[1]->id) {
                $this->assertEquals(1, $record->criterion_met);
            } else {
                $this->assertEquals(0, $record->criterion_met);
            }
        }

        // Similarly - user1's has_changes in the temp table should be set, but not user2's
        $temp_records = $DB->get_records('totara_competency_temp_users');
        $this->assertSame(count($test_users), count($temp_records));
        foreach ($temp_records as $record) {
            if ($record->user_id == $data->users[1]->id) {
                $this->assertEquals(1, $record->has_changed);
            } else {
                $this->assertEquals(0, $record->has_changed);
            }
        }
    }

    public function test_update_completion_completion_missed_by_observer_list() {
        global $DB;

        $data = $this->setup_data();

        $test_users = [$data->users[1]->id, $data->users[2]->id];

        // Setting up ...
        // Create the initial item_records and wait for a second to ensure we have unique timestamps
        $item_combined = new course_item_combined($test_users);
        $item_combined->update_completion($data->coursecompletion);
        $this->waitForSecond();

        // Mark user1 to have completed the course
        /** @var completion_completion $completion */
        $completion = new completion_completion(['course' => $data->course->id, 'userid' => $data->users[1]->id]);
        $completion->mark_complete();

        // For testing purposes only - resetting the item_record.criterion_met flag that is set in the
        // event observer when the course was marked as completed by user1
        // This is still a valid test as it handles the case where the event observer failed to set the criterion_met flag
        // for some or other reason.
        $DB->execute('UPDATE {totara_criteria_item_record} SET criterion_met = 0');

        // Now for the real test ...
        // Testing that the sql statements to catch item_records with invalid criterion_met values are updated correctly
        $this->waitForSecond();
        $item_combined->update_completion($data->coursecompletion);

        // The criterion_met should have been updated for user1, but not user2.
        $item_records = $DB->get_records('totara_criteria_item_record');
        $this->assertSame(count($test_users), count($item_records));

        foreach ($item_records as $record) {
            $this->assertTrue(in_array($record->user_id, $test_users));
            if ($record->user_id == $data->users[1]->id) {
                $this->assertEquals(1, $record->criterion_met);
            } else {
                $this->assertEquals(0, $record->criterion_met);
            }
        }
    }

}
