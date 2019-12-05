<?php
/*
 * This file is part of Totara Learn
 *
 * Copyright (C) 2018 onwards Totara Learning Solutions LTD
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
 * @author Riana Rossouw <riana.rossouw@totaralearning.com>
 * @package criteria_coursecompletion
 */

use criteria_coursecompletion\coursecompletion;
use criteria_coursecompletion\validators\coursecompletion_validator;
use totara_criteria\criterion;
use totara_criteria\entities\criterion as criterion_entity;
use totara_criteria\entities\criterion_item as criterion_item_entity;

class criteria_coursecompletion_coursecompletion_validator_testcase extends advanced_testcase {

    /**
     * Test validate_and_set_criterion_status for coursecompletion
     */
    public function test_validate_and_set_status() {
        global $CFG;

        // Completion only enabled for every second course
        $courses = [];
        for ($i = 1; $i <= 5; $i++) {
            $courses[$i] = $this->getDataGenerator()->create_course(['enablecompletion' => $i % 2]);
        }

        // Not using the generator as it will also call the function we are testing

        $CFG->enablecompletion = true;

        // Single valid course
        $criterion = $this->create_test_coursecompletion(criterion::STATUS_VALID, criterion::AGGREGATE_ALL, 1, [$courses[1]->id]);
        $this->assertSame(criterion::STATUS_VALID, coursecompletion_validator::validate_and_set_status($criterion));
        $this->assertTrue($criterion->is_valid());
        $on_disk = new criterion_entity($criterion->get_id());
        $this->assertEquals(criterion::STATUS_VALID, $on_disk->status);

        // Single non-existent course
        $criterion = $this->create_test_coursecompletion(criterion::STATUS_VALID, criterion::AGGREGATE_ALL, 1, [23456]);
        $this->assertSame(criterion::STATUS_INVALID, coursecompletion_validator::validate_and_set_status($criterion));
        $this->assertFalse($criterion->is_valid());
        $on_disk = new criterion_entity($criterion->get_id());
        $this->assertEquals(criterion::STATUS_INVALID, $on_disk->status);

        // Multiple courses - all valid
        $criterion = $this->create_test_coursecompletion(criterion::STATUS_VALID,
            criterion::AGGREGATE_ALL,
            1,
            [$courses[1]->id, $courses[3]->id, $courses[5]->id]);
        $this->assertSame(criterion::STATUS_VALID, coursecompletion_validator::validate_and_set_status($criterion));
        $this->assertTrue($criterion->is_valid());
        $on_disk = new criterion_entity($criterion->get_id());
        $this->assertEquals(criterion::STATUS_VALID, $on_disk->status);

        // Multiple courses - some invalid
        $criterion = $this->create_test_coursecompletion(criterion::STATUS_VALID,
            criterion::AGGREGATE_ALL,
            1,
            [$courses[1]->id, $courses[2]->id, $courses[3]->id, $courses[5]->id], 34567);
        $this->assertSame(criterion::STATUS_INVALID, coursecompletion_validator::validate_and_set_status($criterion));
        $this->assertFalse($criterion->is_valid());
        $on_disk = new criterion_entity($criterion->get_id());
        $this->assertEquals(criterion::STATUS_INVALID, $on_disk->status);

        // Multiple courses - all valid, but not enough
        $criterion = $this->create_test_coursecompletion(criterion::STATUS_VALID,
            criterion::AGGREGATE_ANY_N,
            3,
            [$courses[1]->id, $courses[3]->id]);
        $this->assertSame(criterion::STATUS_INVALID, coursecompletion_validator::validate_and_set_status($criterion));
        $this->assertFalse($criterion->is_valid());
        $on_disk = new criterion_entity($criterion->get_id());
        $this->assertEquals(criterion::STATUS_INVALID, $on_disk->status);

        // Multiple courses - all valid but global completion disabled
        $CFG->enablecompletion = false;
        $criterion = $this->create_test_coursecompletion(criterion::STATUS_VALID,
            criterion::AGGREGATE_ALL,
            1,
            [$courses[1]->id, $courses[3]->id, $courses[5]->id]);
        $this->assertSame(criterion::STATUS_INVALID, coursecompletion_validator::validate_and_set_status($criterion));
        $this->assertFalse($criterion->is_valid());
        $on_disk = new criterion_entity($criterion->get_id());
        $this->assertEquals(criterion::STATUS_INVALID, $on_disk->status);
    }


    /**
     * @param int $status
     * @param int $aggregation_method
     * @param int $req_items
     * @param array $item_ids
     * @return criterion
     */
    private function create_test_coursecompletion(
        int $status = criterion::STATUS_VALID,
        int $aggregation_method = criterion::AGGREGATE_ALL,
        int $req_items = 1,
        array $item_ids = []
    ): coursecompletion {
        $criterion = new criterion_entity();
        $criterion->plugin_type = 'coursecompletion';
        $criterion->aggregation_method = $aggregation_method;
        $criterion->aggregation_params = json_encode(['req_items' => $req_items]);
        $criterion->criterion_modified = time();
        $criterion->status = $status;
        $criterion->save();

        foreach ($item_ids as $item_id) {
            $this->create_criterion_item($criterion->id, $item_id);
        }

        // We want the item ids too
        return coursecompletion::fetch($criterion->id);
    }

    /**
     * @param int $criterion_id
     * @param int $item_id
     */
    private function create_criterion_item(int $criterion_id, int $item_id) {
        $item = new criterion_item_entity();
        $item->criterion_id = $criterion_id;
        $item->item_type = 'course';
        $item->item_id = $item_id;
        $item->save();
    }
}
