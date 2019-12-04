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

use criteria_othercompetency\external;
use criteria_othercompetency\othercompetency;
use criteria_othercompetency\othercompetency_display;
use totara_criteria\criterion;

global $CFG;
require_once($CFG->dirroot . '/lib/externallib.php');

class criteria_othercompetency_services_testcase extends \advanced_testcase {

    /**
     * @var totara_competency_generator
     */
    private $competency_generator;
    /**
     * @var \totara_competency\entities\competency[] $other_competency_items
     */
    private $other_competency_items;


    protected function setUp() {
        parent::setUp();

        $this->competency_generator = $this->getDataGenerator()->get_plugin_generator('totara_competency');
        $this->other_competency_items = [
            $this->competency_generator->create_competency('<span>Other Comp 1</span>'),
            $this->competency_generator->create_competency('<span>Other Comp 2</span>'),
            $this->competency_generator->create_competency('<span>Other Comp 3</span>'),
        ];
    }

    protected function tearDown() {
        parent::tearDown();
        $this->competency_generator = null;
        $this->other_competency_items = null;
    }

    /**
     * Make sure get_detail() in the external class returns information about a given criterion
     */
    public function test_get_detail() {
        $other_competency_ids = array_column($this->other_competency_items, 'id');

        $competency = $this->competency_generator->create_competency();
        $criterion = $this->competency_generator->create_criterion(
            othercompetency::class,
            $competency,
            criterion::AGGREGATE_ALL,
            $other_competency_ids
        );

        $returned_data = external::get_detail($criterion->get_id());
        $items = $returned_data['items'];

        $this->assertEquals('Proficiency in other competencies', $returned_data['title']);
        $this->assertCount(3, $items);
        $this->assertCount(2, $returned_data['aggregation']);
    }

    /**
     * make sure names of other competencies linked are displayed for the summary page
     */
    public function test_get_display_configuration_items() {
        $other_competency_ids = array_column($this->other_competency_items, 'id');

        $competency = $this->competency_generator->create_competency();

        $criterion = $this->competency_generator->create_criterion(
            othercompetency::class,
            $competency,
            criterion::AGGREGATE_ALL,
            $other_competency_ids
        );

        $criterion_display = new othercompetency_display($criterion);
        $items = $criterion_display->get_configuration()->items;

        $this->assertCount(3, $items);

        for ($i = 0; $i < count($items); $i++) {
            $this->assertContains($this->other_competency_items[$i]->fullname,$items);
        }
    }

    /**
     * make sure return a summarized view of the criterion items for display
     */
    public function test_export_edit_items() {
        $other_competency_ids = array_column($this->other_competency_items, 'id');

        $othercompetency = new othercompetency();

        $othercompetency->set_item_ids($other_competency_ids);

        $items = $othercompetency->export_edit_items();

        $this->assertCount(3, $items);

        $expected_data = [];
        foreach ($this->other_competency_items as $item) {
            $expected_data[] = [
                'type' => 'competency',
                'id' => $item->id,
                'name' => format_string($item->fullname),
            ];
        }

        $this->assertEqualsCanonicalizing($expected_data, $items);
    }
}
