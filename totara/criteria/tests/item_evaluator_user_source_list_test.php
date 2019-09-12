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
 * @author Riana Rossouw <riana.rossouw@totaralearning.com>
 * @package totara_criteria
 */

use totara_criteria\item_evaluator_user_source_list;

class totara_criteria_item_evaluator_user_source_list_testcase extends advanced_testcase {

    private function setup_data() {
        $data = new class() {
            /** @var array $users */
            public $users;
            /** @var \stdClass $course */
            public $course;
            /** @var criterion $criterion */
            public $criterion;
        };

        $this->setAdminUser();
        $GLOBALS['USER']->ignoresesskey = true;

        $data->users = [];
        for ($i = 1; $i <= 2; $i++) {
            $data->users[$i] = $this->getDataGenerator()->create_user();
        }

        $record = [
            'shortname' => "course1",
            'fullname' => "Course 1",
        ];
        $data->course = $this->getDataGenerator()->create_course($record);

        /** @var \totara_criteria_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('totara_criteria');

        $record = ['courseids' => [$data->course->id]];
        $data->criterion = $generator->create_coursecompletion($record);

        return $data;
    }

    private function verify_item_records($expected) {
        global $DB;

        $records = $DB->get_records('totara_criteria_item_record');
        $this->assertSame(count($expected), count($records));
        foreach ($records as $record) {
            foreach ($expected as $key => $expected_values) {
                if ($record->user_id == $expected_values['user_id'] && $record->criterion_met == $expected_values['criterion_met']) {
                    unset($expected[$key]);
                    break;
                }
            }
        }

        $this->assertEmpty($expected);
    }


    public function test_create_item_records() {
        global $DB;

        $data = $this->setup_data();
        $this->assertSame(0, $DB->count_records('totara_criteria_item_record'));

        // No records
        $user_source = new item_evaluator_user_source_list([], true);
        $user_source->create_item_records($data->criterion->get_id(), 0);
        $this->assertSame(0, $DB->count_records('totara_criteria_item_record'));

        // Add users
        $user_ids = [1, 2, 3];
        $user_source = new item_evaluator_user_source_list($user_ids, true);
        $user_source->create_item_records($data->criterion->get_id(), 0);
        $expected = [
            ['user_id' => 1, 'criterion_met' => 0],
            ['user_id' => 2, 'criterion_met' => 0],
            ['user_id' => 3, 'criterion_met' => 0],
        ];
        $this->verify_item_records($expected);

        // Some duplicates, some new
        $user_ids = [2, 4, 6];
        $user_source = new item_evaluator_user_source_list($user_ids, true);
        $user_source->create_item_records($data->criterion->get_id(), 1);
        $expected = [
            ['user_id' => 1, 'criterion_met' => 0],
            ['user_id' => 2, 'criterion_met' => 0],
            ['user_id' => 4, 'criterion_met' => 1],
            ['user_id' => 3, 'criterion_met' => 0],
            ['user_id' => 6, 'criterion_met' => 1],
        ];
        $this->verify_item_records($expected);
    }

    public function test_delete_item_records() {
        global $DB;

        $data = $this->setup_data();
        // Add some users to start
        $user_ids = [1, 2, 3, 4, 5];
        $user_source = new item_evaluator_user_source_list($user_ids, true);
        $user_source->create_item_records($data->criterion->get_id(), 0);
        $expected = [
            ['user_id' => 1, 'criterion_met' => 0],
            ['user_id' => 2, 'criterion_met' => 0],
            ['user_id' => 3, 'criterion_met' => 0],
            ['user_id' => 4, 'criterion_met' => 0],
            ['user_id' => 5, 'criterion_met' => 0],
        ];

        // Now for the tests ...

        // When deleting from a partial set, nothing should happen
        $to_keep = [2, 4];
        $partial_source = new item_evaluator_user_source_list($to_keep, false);
        $partial_source->delete_item_records($data->criterion->get_id());
        $expected = [
            ['user_id' => 1, 'criterion_met' => 0],
            ['user_id' => 2, 'criterion_met' => 0],
            ['user_id' => 3, 'criterion_met' => 0],
            ['user_id' => 4, 'criterion_met' => 0],
            ['user_id' => 5, 'criterion_met' => 0],
        ];
        $this->verify_item_records($expected);

        // When deleting from a full set, all but the user_ids in the source should be deleted
        $to_keep = [2, 4];
        $full_source = new item_evaluator_user_source_list($to_keep, true);
        $full_source->delete_item_records($data->criterion->get_id());
        $expected = [
            ['user_id' => 2, 'criterion_met' => 0],
            ['user_id' => 4, 'criterion_met' => 0],
        ];
        $this->verify_item_records($expected);
    }

}
