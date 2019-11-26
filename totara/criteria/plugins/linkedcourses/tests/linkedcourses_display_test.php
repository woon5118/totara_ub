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

use criteria_linkedcourses\linkedcourses_display;
use totara_criteria\criterion;

class criteria_linkedcourses_display_testcase extends advanced_testcase {

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
     * Data provider for test_configuration
     */
    public function data_provider_test_configuration() {
        return [
            [
                'aggregation' => [
                    'method' => criterion::AGGREGATE_ALL,
                ],
            ],
            [
                'aggregation' => [
                    'method' => criterion::AGGREGATE_ANY_N,
                    'req_items' => 2
                ],
            ],
        ];
    }

    /**
     * Test configuration display
     *
     * @dataProvider data_provider_test_configuration
     * @param $aggregation
     */
    public function test_configuration($aggregation) {
        $this->setup_data();

        $generator = $this->getDataGenerator()->get_plugin_generator('totara_criteria');

        $creation_params = [];
        $creation_params['aggregation'] = $aggregation;
        $creation_params['competency'] = 1;

        $cc = $generator->create_linkedcourses($creation_params);
        $display_configuration = (new linkedcourses_display($cc))->get_configuration();

        $expected = (object)[
            'item_type' => get_string('linkedcourses', 'criteria_linkedcourses'),
            'item_aggregation' => get_string('completeall', 'totara_criteria'),
            'items' => [],
        ];

        if ($aggregation['method'] == criterion::AGGREGATE_ANY_N) {
            $expected->item_aggregation = get_string(
                'aggregate_any',
                'totara_criteria',
                (object)['x' => $aggregation['req_items'] ?? 1]
            );
        }

        $this->assertEqualsCanonicalizing($expected, $display_configuration);
    }

}
