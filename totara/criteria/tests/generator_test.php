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
 * @author Brendan Cox <brendan.cox@totaralearning.com>
 * @author Riana Rossouw <riana.rossouw@totaralearning.com>
 * @package totara_criteria
 */

use criteria_coursecompletion\coursecompletion;
use criteria_linkedcourses\linkedcourses;
use criteria_onactivate\onactivate;
use totara_criteria\criterion;

class totara_criteria_generator_testcase extends \advanced_testcase {

    private function setup_data() {
        $data = new class() {
            public $courses;
        };

        $prefix = 'Course ';
        for ($i = 1; $i <= 5; $i++) {
            $record = [
                'shortname' => $prefix . $i,
                'fullname' => $prefix . $i,
            ];

            $data->courses[$i] = $this->getDataGenerator()->create_course($record);
        }

        return $data;
    }

    /**
     * Test coursecompletion generator with single course
     */
    public function test_coursecompletion_generator_all_single_course() {
        $data = $this->setup_data();

        $generator = $this->getDataGenerator()->get_plugin_generator('totara_criteria');

        $record = ['courseids' => [$data->courses[1]->id]];
        $cc = $generator->create_coursecompletion($record);

        $this->validate_coursecompletion($cc, $record);
    }

    /**
     * Test coursecompletion generator with multiple courses - ALL aggregation
     */
    public function test_coursecompletion_generator_all_multi_courses() {
        $data = $this->setup_data();

        $generator = $this->getDataGenerator()->get_plugin_generator('totara_criteria');

        $record = [
            'aggregation' => ['method' => criterion::AGGREGATE_ALL],
            'courseids' =>[$data->courses[1]->id, $data->courses[2]->id, $data->courses[3]->id],
        ];

        $cc = $generator->create_coursecompletion($record);

        $this->validate_coursecompletion($cc, $record);
    }

    /**
     * Test coursecompletion generator with multiple courses - ANY aggregation
     */
    public function test_coursecompletion_generator_any_multi_courses() {
        $data = $this->setup_data();

        $generator = $this->getDataGenerator()->get_plugin_generator('totara_criteria');

        $record = [
            'aggregation' => [
                'method' => criterion::AGGREGATE_ANY_N,
                'req_items' => 2,
            ],
            'courseids' =>[$data->courses[2]->id, $data->courses[3]->id, $data->courses[4]->id, $data->courses[5]->id],
        ];

        $cc = $generator->create_coursecompletion($record);

        $this->validate_coursecompletion($cc, $record);
    }

    /**
     * Test linkedcourses generator defaults
     */
    public function test_linkedcourses_generator_defaults() {
        $generator = $this->getDataGenerator()->get_plugin_generator('totara_criteria');

        $lc = $generator->create_linkedcourses();

        $record = [
            'aggregation' => [
                'method' => criterion::AGGREGATE_ALL,
                'req_items' => 1,
            ],
        ];

        $this->validate_linkedcourses($lc, $record);
    }

    /**
     * Test linkedcourses generator requiring all mandatory linked courses
     */
    public function test_linkedcourses_generator_all_mandatory() {
        $generator = $this->getDataGenerator()->get_plugin_generator('totara_criteria');

        $record = [
            'aggregation' => [
                'method' => criterion::AGGREGATE_ALL,
                'req_items' => 1,
            ],
        ];

        $lc = $generator->create_linkedcourses($record);

        $this->validate_linkedcourses($lc, $record);
    }

    /**
     * Test linkedcourses generator requiring some linked courses
     */
    public function test_linkedcourses_generator_some() {
        $generator = $this->getDataGenerator()->get_plugin_generator('totara_criteria');

        $record = [
            'aggregation' => [
                'method' => criterion::AGGREGATE_ANY_N,
                'req_items' => 2,
            ],
        ];

        $lc = $generator->create_linkedcourses($record);

        $this->validate_linkedcourses($lc, $record);
    }


    /**
     * Test onactivate generator
     */
    public function test_onactivate_generator() {
        $generator = $this->getDataGenerator()->get_plugin_generator('totara_criteria');

        $oa = $generator->create_onactivate();

        $this->validate_onactivate($oa);
    }


    /**
     * Test childcompetency generator defaults
     */
    public function test_childcompetency_generator_defaults() {
        $generator = $this->getDataGenerator()->get_plugin_generator('totara_criteria');

        $cc = $generator->create_childcompetency();

        $record = [
            'aggregation' => [
                'method' => criterion::AGGREGATE_ALL,
                'req_items' => 1,
            ],
        ];

        $this->validate_childcompetency($cc, $record);
    }

    /**
     * Test childcompetency generator requiring all child competencies
     */
    public function test_childcompetency_generator_all_mandatory() {
        $generator = $this->getDataGenerator()->get_plugin_generator('totara_criteria');

        $record = [
            'aggregation' => [
                'method' => criterion::AGGREGATE_ALL,
                'req_items' => 1,
            ],
        ];

        $cc = $generator->create_childcompetency($record);

        $this->validate_childcompetency($cc, $record);
    }

    /**
     * Test childcompetency generator requiring some child competencies
     */
    public function test_childcompetency_generator_some() {
        $generator = $this->getDataGenerator()->get_plugin_generator('totara_criteria');

        $record = [
            'aggregation' => [
                'method' => criterion::AGGREGATE_ANY_N,
                'req_items' => 2,
            ],
        ];

        $cc = $generator->create_childcompetency($record);

        $this->validate_childcompetency($cc, $record);
    }



     /**
     * Validate the generated coursecompletion against the source record
     *
     * @param  coursecompletion $cc Generated coursecompletion
     * @param  array Srecord Source record
     */
    private function validate_coursecompletion(coursecompletion $cc, array $record) {
        global $DB;

        $rows = $DB->get_records('totara_criteria');
        $this->assertEquals(1, count($rows));
        $row = reset($rows);
        $this->assertEquals($cc->get_id(), $row->id);

        // Aggregation
        if (isset($record['aggregation'])) {
            $this->assertEquals($record['aggregation']['method'] ?? criterion::AGGREGATE_ALL, $cc->get_aggregation_method());

            if (isset($record['aggregation']['req_items'])) {
                $this->assertEquals(['req_items' => $record['aggregation']['req_items']], $cc->get_aggregation_params());
            }
        }

        $rows = $DB->get_records('totara_criteria_item');
        $this->assertEquals(count($record['courseids']), count($rows));

        foreach ($rows as $row) {
            $this->assertEquals('course', $row->item_type);
            $this->assertTrue(in_array($row->item_id, $record['courseids']));
            $this->assertEquals($cc->get_id(), $row->criterion_id);
        }
    }

    /**
     * Validate the generated linkedcourses against the source record
     *
     * @param  linkedcourses $lc Generated linkedcourses
     * @param  array Srecord Source record
     */
    private function validate_linkedcourses(linkedcourses $lc, array $record) {
        global $DB;

        $rows = $DB->get_records('totara_criteria');
        $this->assertEquals(1, count($rows));
        $row = reset($rows);
        $this->assertEquals($lc->get_id(), $row->id);

        // Aggregation
        if (isset($record['aggregation'])) {
            $this->assertEquals($record['aggregation']['method'] ?? criterion::AGGREGATE_ALL, $lc->get_aggregation_method());

            if (isset($record['aggregation']['req_items'])) {
                $this->assertEquals(['req_items' => $record['aggregation']['req_items']], $lc->get_aggregation_params());
            }
        }
    }

    /**
     * Validate the generated onactivate criterion
     *
     * @param  onactivate $oa Generated criterion
     */
    private function validate_onactivate(onactivate $oa) {
        global $DB;

        $rows = $DB->get_records('totara_criteria');
        $this->assertEquals(1, count($rows));
        $row = reset($rows);
        $this->assertEquals($oa->get_id(), $row->id);
    }

    /**
     * Validate the generated childcompetency against the source record
     *
     * @param  childcompetency $cc Generated childcompetency
     * @param  array Srecord Source record
     */
    private function validate_childcompetency(\criteria_childcompetency\childcompetency $cc, array $record) {
        global $DB;

        $rows = $DB->get_records('totara_criteria');
        $this->assertEquals(1, count($rows));
        $row = reset($rows);
        $this->assertEquals($cc->get_id(), $row->id);

        // Aggregation
        if (isset($record['aggregation'])) {
            $this->assertEquals($record['aggregation']['method'] ?? criterion::AGGREGATE_ALL, $cc->get_aggregation_method());

            if (isset($record['aggregation']['req_items'])) {
                $this->assertEquals(['req_items' => $record['aggregation']['req_items']], $cc->get_aggregation_params());
            }
        }
    }

}
