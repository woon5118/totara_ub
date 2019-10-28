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

use totara_criteria\course_item_evaluator;
use totara_criteria\criterion;
use totara_criteria\entities\criterion as criterion_entity;
use totara_criteria\item_evaluator;

class totara_criteria_course_item_evaluator_testcase extends advanced_testcase {

    public function test_update_item_records_no_data() {
        course_item_evaluator::update_item_records();
    }

    public function test_update_item_records_course_item_no_users() {
        global $DB;

        $course = $this->getDataGenerator()->create_course();

        /** @var totara_criteria_generator $criteria_generator */
        $criteria_generator = $this->getDataGenerator()->get_plugin_generator('totara_criteria');

        $criteria_generator->create_course_criterion_item($course);

        course_item_evaluator::update_item_records();

        $item_records = $DB->get_records('totara_criteria_item_record');
        $this->assertCount(0, $item_records);
    }

    public function test_update_item_records_course_item_no_completion() {
        global $DB;

        $course = $this->getDataGenerator()->create_course();

        /** @var totara_criteria_generator $criteria_generator */
        $criteria_generator = $this->getDataGenerator()->get_plugin_generator('totara_criteria');

        $item_id = $criteria_generator->create_course_criterion_item($course);

        $user = $this->getDataGenerator()->create_user();

        course_item_evaluator::update_item_records();

        $item_records = $DB->get_records('totara_criteria_item_record');
        $this->assertCount(0, $item_records);

        item_evaluator::create_item_records($item_id, [$user->id]);

        $item_records = $DB->get_records('totara_criteria_item_record');
        $this->assertCount(1, $item_records);
        $item_record = array_pop($item_records);
        $this->assertEquals('0', $item_record->criterion_met);

        course_item_evaluator::update_item_records();

        $item_records = $DB->get_records('totara_criteria_item_record');
        $this->assertCount(1, $item_records);
        $item_record = array_pop($item_records);
        $this->assertEquals('0', $item_record->criterion_met);
    }

    public function test_update_item_records_course_item_one_completed() {
        global $DB;

        $course = $this->getDataGenerator()->create_course();

        /** @var totara_criteria_generator $criteria_generator */
        $criteria_generator = $this->getDataGenerator()->get_plugin_generator('totara_criteria');

        $item_id = $criteria_generator->create_course_criterion_item($course);

        $user = $this->getDataGenerator()->create_user();

        /** @var completion_completion $completion */
        $completion = new completion_completion(['course' => $course->id, 'userid' => $user->id]);
        $completion->mark_complete();

        course_item_evaluator::update_item_records();

        // Still nothing created yet.
        // This is because we don't know that we want to track this user for criteria.
        $item_records = $DB->get_records('totara_criteria_item_record');
        $this->assertCount(0, $item_records);

        item_evaluator::create_item_records($item_id, [$user->id]);

        $item_records = $DB->get_records('totara_criteria_item_record');
        $this->assertCount(1, $item_records);
        $item_record = array_pop($item_records);
        $this->assertEquals('0', $item_record->criterion_met);

        course_item_evaluator::update_item_records();

        $item_records = $DB->get_records('totara_criteria_item_record');
        $this->assertCount(1, $item_records);
    }

    public function test_update_item_records_course_item_one_incomplete() {
        global $DB;

        $course = $this->getDataGenerator()->create_course();

        /** @var totara_criteria_generator $criteria_generator */
        $criteria_generator = $this->getDataGenerator()->get_plugin_generator('totara_criteria');

        $item_id = $criteria_generator->create_course_criterion_item($course);

        $user = $this->getDataGenerator()->create_user();

        /** @var completion_completion $completion */
        $completion = new completion_completion(['course' => $course->id, 'userid' => $user->id]);

        item_evaluator::create_item_records($item_id, [$user->id]);
        course_item_evaluator::update_item_records();

        $item_records = $DB->get_records('totara_criteria_item_record');
        $this->assertCount(1, $item_records);
        $item_record = array_pop($item_records);
        $this->assertEquals('0', $item_record->criterion_met);
    }

    public function test_update_item_records_course_item_incomplete_stays_incomplete() {
        global $DB;

        $course = $this->getDataGenerator()->create_course();

        /** @var totara_criteria_generator $criteria_generator */
        $criteria_generator = $this->getDataGenerator()->get_plugin_generator('totara_criteria');

        $item_id = $criteria_generator->create_course_criterion_item($course);

        $user = $this->getDataGenerator()->create_user();

        /** @var completion_completion $completion */
        $completion = new completion_completion(['course' => $course->id, 'userid' => $user->id]);

        item_evaluator::create_item_records($item_id, [$user->id]);
        course_item_evaluator::update_item_records();

        $item_records = $DB->get_records('totara_criteria_item_record');
        $this->assertCount(1, $item_records);
        $item_record = array_pop($item_records);
        $this->assertEquals('0', $item_record->criterion_met);

        // There could be an existing completion record that is not marked as complete.
        $completion->timecompleted = 0;
        $completion->status = COMPLETION_STATUS_INPROGRESS;
        $completion->mark_enrolled();
        $completion->mark_inprogress();
        $completion->reaggregate = 0;
        $completion->insert();

        $completion_record = $DB->get_record('course_completions', ['userid' => $user->id]);
        $this->assertEquals(0, $completion_record->timecompleted);
        $this->assertEquals(COMPLETION_STATUS_INPROGRESS, $completion_record->status);

        course_item_evaluator::update_item_records();

        $item_records = $DB->get_records('totara_criteria_item_record');
        $this->assertCount(1, $item_records);
        $item_record = array_pop($item_records);
        $this->assertEquals('0', $item_record->criterion_met);

        // And the record could be deleted to make it incomplete.
        $DB->delete_records('course_completions', ['id' => $completion_record->id]);

        course_item_evaluator::update_item_records();

        $item_records = $DB->get_records('totara_criteria_item_record');
        $this->assertCount(1, $item_records);
        $item_record = array_pop($item_records);
        $this->assertEquals('0', $item_record->criterion_met);
    }

    public function test_update_item_records_course_item_incomplete_becomes_complete() {
        global $DB;

        $course = $this->getDataGenerator()->create_course();

        /** @var totara_criteria_generator $criteria_generator */
        $criteria_generator = $this->getDataGenerator()->get_plugin_generator('totara_criteria');

        $item_id = $criteria_generator->create_course_criterion_item($course);

        $user = $this->getDataGenerator()->create_user();

        /** @var completion_completion $completion */
        $completion = new completion_completion(['course' => $course->id, 'userid' => $user->id]);

        item_evaluator::create_item_records($item_id, [$user->id]);
        course_item_evaluator::update_item_records();

        $item_records = $DB->get_records('totara_criteria_item_record');
        $this->assertCount(1, $item_records);
        $item_record = array_pop($item_records);
        $this->assertEquals('0', $item_record->criterion_met);

        $completion->mark_complete();
        course_item_evaluator::update_item_records();

        $item_records = $DB->get_records('totara_criteria_item_record');
        $this->assertCount(1, $item_records);
        $item_record = array_pop($item_records);
        $this->assertEquals('1', $item_record->criterion_met);
    }

    public function test_update_item_records_course_item_complete_stays_complete() {
        global $DB;

        $course = $this->getDataGenerator()->create_course();

        /** @var totara_criteria_generator $criteria_generator */
        $criteria_generator = $this->getDataGenerator()->get_plugin_generator('totara_criteria');

        $item_id = $criteria_generator->create_course_criterion_item($course);

        $user = $this->getDataGenerator()->create_user();

        /** @var completion_completion $completion */
        $completion = new completion_completion(['course' => $course->id, 'userid' => $user->id]);
        $completion->mark_complete();

        item_evaluator::create_item_records($item_id, [$user->id]);
        course_item_evaluator::update_item_records();

        $item_records = $DB->get_records('totara_criteria_item_record');
        $this->assertCount(1, $item_records);
        $item_record = array_pop($item_records);
        $this->assertEquals('1', $item_record->criterion_met);

        course_item_evaluator::update_item_records();

        $item_records = $DB->get_records('totara_criteria_item_record');
        $this->assertCount(1, $item_records);
        $item_record = array_pop($item_records);
        $this->assertEquals('1', $item_record->criterion_met);
    }

    public function test_update_item_records_course_item_complete_becomes_incomplete() {
        global $DB;

        $course = $this->getDataGenerator()->create_course();

        /** @var totara_criteria_generator $criteria_generator */
        $criteria_generator = $this->getDataGenerator()->get_plugin_generator('totara_criteria');

        $item_id = $criteria_generator->create_course_criterion_item($course);

        $user = $this->getDataGenerator()->create_user();

        /** @var completion_completion $completion */
        $completion = new completion_completion(['course' => $course->id, 'userid' => $user->id]);
        $completion->mark_complete();

        item_evaluator::create_item_records($item_id, [$user->id]);
        course_item_evaluator::update_item_records();

        $item_records = $DB->get_records('totara_criteria_item_record');
        $this->assertCount(1, $item_records);
        $item_record = array_pop($item_records);
        $this->assertEquals('1', $item_record->criterion_met);

        $completion->timecompleted = 0;
        $completion->status = COMPLETION_STATUS_INPROGRESS;
        $completion->mark_inprogress();
        $completion->reaggregate = 0;
        $completion->update();

        $completion_record = $DB->get_record('course_completions', ['userid' => $user->id]);
        $this->assertEquals(0, $completion_record->timecompleted);
        $this->assertEquals(COMPLETION_STATUS_INPROGRESS, $completion_record->status);

        course_item_evaluator::update_item_records();

        $item_records = $DB->get_records('totara_criteria_item_record');
        $this->assertCount(1, $item_records);
        $item_record = array_pop($item_records);
        $this->assertEquals('0', $item_record->criterion_met);

        // And the record could be deleted to make it incomplete.
        $DB->delete_records('course_completions', ['id' => $completion_record->id]);

        course_item_evaluator::update_item_records();

        $item_records = $DB->get_records('totara_criteria_item_record');
        $this->assertCount(1, $item_records);
        $item_record = array_pop($item_records);
        $this->assertEquals('0', $item_record->criterion_met);
    }

}