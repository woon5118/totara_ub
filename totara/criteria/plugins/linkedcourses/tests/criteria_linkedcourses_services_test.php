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
 */

use totara_criteria\criterion;

global $CFG;
require_once($CFG->dirroot . '/lib/externallib.php');

/**
 * @group totara_criteria
 */
class criteria_linkedcourses_services_testcase extends advanced_testcase {

    private function setup_data() {
        $data = new class() {
            public $criterion;
        };

        $this->setAdminUser();
        $GLOBALS['USER']->ignoresesskey = true;

        $generator = $this->getDataGenerator()->get_plugin_generator('totara_criteria');

        $record = [
            'aggregation' => [
                'method' => criterion::AGGREGATE_ANY_N,
                'req_items' => 2,
            ],
            'competency' => 1,
        ];

        $data->criterion = $generator->create_linkedcourses($record);

        return $data;
    }

    public function test_criteria_linkedcourses_get_detail_service() {
        $data = $this->setup_data();

        $expected_result = [
            'metadata' => [
                [
                    'metakey' => criterion::METADATA_COMPETENCY_KEY,
                    'metavalue' => 1,
                ],

            ],
            'aggregation' => [
                'method' => criterion::AGGREGATE_ANY_N,
                'reqitems' => 2,
            ],
        ];

        $res = \external_api::call_external_function(
            'criteria_linkedcourses_get_detail',
            ['id' => $data->criterion->get_id()]
        );

        $result = $res['data'] ?? null;
        $error = $res['error'] ?? null;

        $this->assertEquals(false, $error);
        $this->assertTrue(is_array($result));
        $this->assertEquals($expected_result, $result);
    }

}
