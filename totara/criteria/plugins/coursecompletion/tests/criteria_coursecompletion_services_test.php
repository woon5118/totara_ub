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
class criteria_coursecompletion_services_testcase extends advanced_testcase {

    private function setup_data() {
        $data = new class() {
            public $courses = [];
            public $criterion;
        };

        $this->setAdminUser();
        $GLOBALS['USER']->ignoresesskey = true;

        $generator = $this->getDataGenerator()->get_plugin_generator('totara_criteria');

        for ($i = 0; $i < 5; $i++) {
            $data->courses[$i] = $this->getDataGenerator()->create_course();
        }

        $data->criterion = $generator->create_coursecompletion([
            'aggregation' => [
                'method' => criterion::AGGREGATE_ANY_N,
                'req_items' => 2,
            ],
            'courseids' => [$data->courses[0]->id, $data->courses[2]->id, $data->courses[4]->id],
        ]);

        return $data;
    }

    public function test_criteria_coursecompletion_get_detail_service() {
        $data = $this->setup_data();

        $expected_result = [
            'items' => [],
            'aggregation' => [
                'method' => criterion::AGGREGATE_ANY_N,
                'reqitems' => 2,
            ],
        ];

        for ($i = 0; $i < 5; $i += 2) {
            $expected_result['items'][] = [
                'type' => 'course',
                'id' => $data->courses[$i]->id,
                'name' => $data->courses[$i]->fullname,
            ];
        }

        $res = external_api::call_external_function(
            'criteria_coursecompletion_get_detail',
            ['id' => $data->criterion->get_id()]
        );

        $result = $res['data'] ?? null;
        $error = $res['error'] ?? null;

        $this->assertEquals(false, $error);
        $this->assertTrue(is_array($result));

        $this->assertTrue(isset($result['aggregation']));
        $this->assertEqualsCanonicalizing($expected_result['aggregation'], $result['aggregation']);

        $this->assertTrue(isset($result['items']));
        $this->assertSame(count($expected_result['items']), count($result['items']));

        foreach ($result['items'] as $actual_item) {
            foreach ($expected_result['items'] as $key => $expected_item) {
                if ($actual_item['id'] == $expected_item['id'] &&
                    $actual_item['type'] == $expected_item['type'] &&
                    $actual_item['name'] == $expected_item['name']) {
                    unset($expected_result['items'][$key]);
                    break;
                }
            }
        }
        $this->assertSame(0, count($expected_result['items']));
    }

}
