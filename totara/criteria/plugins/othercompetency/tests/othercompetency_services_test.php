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
 * @author Marco Song <marco.song@totaralearning.com>
 * @package criteria_othercompetency
 */

use totara_criteria\criterion;
use totara_competency\entities\competency as competency_entity;

global $CFG;
require_once($CFG->dirroot . '/lib/externallib.php');

class criteria_othercompetency_services_testcase extends \advanced_testcase {

    /**
     * @var totara_criteria_generator
     */
    private $criteria_generator;
    /**
     * @var competency_entity[] $other_competency_items
     */
    private $other_competency_items;


    protected function setUp() {
        parent::setUp();

        /** @var totara_criteria_generator criteria_generator */
        $this->criteria_generator = $this->getDataGenerator()->get_plugin_generator('totara_criteria');

        /** @var totara_competency_generator $competency_generator */
        $competency_generator = $this->getDataGenerator()->get_plugin_generator('totara_competency');
        $this->other_competency_items = [
            $competency_generator->create_competency('<span>Other Comp 1</span>'),
            $competency_generator->create_competency('<span>Other Comp 2</span>'),
            $competency_generator->create_competency('<span>Other Comp 3</span>'),
        ];

        $this->setAdminUser();
        $GLOBALS['USER']->ignoresesskey = true;

        // Ensure othercompetency plugin is enabled
        $enabled_setting = 'criteria_types_enabled';
        set_config($enabled_setting, 'othercompetency', 'totara_criteria');
    }

    protected function tearDown() {
        parent::tearDown();
        $this->criteria_generator = null;
        $this->other_competency_items = null;
    }

    /**
     * Make sure get_detail() in the external class returns information about a given criterion
     */
    public function test_get_detail() {
        $other_competency_ids = array_column($this->other_competency_items, 'id');

        $data = [
            'aggregation' => ['method' => criterion::AGGREGATE_ALL],
            'competencyids' => $other_competency_ids,
        ];

        $criterion = $this->criteria_generator->create_othercompetency($data);

        $expected_result = [
            'items' => [],
            'aggregation' => [
                'method' => criterion::AGGREGATE_ALL,
                'reqitems' => 1,
            ],
        ];

        foreach ($this->other_competency_items as $other_competency) {
            $expected_result['items'][] = [
                'type' => 'competency',
                'id' => $other_competency->id,
                'name' => format_string($other_competency->fullname),
                'error' => get_string('error:competencycannotproficient', 'criteria_othercompetency'),
            ];
        }

        $res = external_api::call_external_function(
            'criteria_othercompetency_get_detail',
            ['id' => $criterion->get_id()]
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
                    $actual_error = $actual_item['error'] ?? '';
                    $expected_error = $expected_item['error'] ?? '';
                    if ($actual_error == $expected_error) {
                        unset($expected_result['items'][$key]);
                    }
                    break;
                }
            }
        }
        $this->assertSame(0, count($expected_result['items']));
    }

}
