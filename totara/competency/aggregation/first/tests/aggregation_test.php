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
 * @package aggregation_first
 */

use totara_competency\base_achievement_detail;
use totara_competency\entities\scale_value;
use totara_competency\pathway_aggregator;
use totara_competency\entities\pathway_achievement;
use aggregation_first\first;

class aggregation_first_aggregation_testcase extends advanced_testcase {

    public function test_with_empty_pathways() {
        $user_id = 101;

        $aggregation = new first();
        $aggregation->set_user_ids([$user_id])
                    ->set_pathways([])
                    ->aggregate();

        $this->assertNull($aggregation->get_achieved_value_id($user_id));
        $this->assertEquals([], $aggregation->get_achieved_via($user_id));
    }

    public function test_with_single_pathway_returning_null() {
        $user_id = 101;

        $pathway1 = $this->getMockBuilder(\totara_competency\pathway::class)
                         ->setMethods(['aggregate_current_value', 'get_id', 'get_sortorder'])
                         ->getMockForAbstractClass();
        $achievement_detail = $this->getMockForAbstractClass(base_achievement_detail::class);
        $pathway1->method('aggregate_current_value')->willReturn($achievement_detail);
        $pathway1->method('get_id')->willReturn(201);
        $pathway1->method('get_sortorder')->willReturn(1);

        (new pathway_aggregator($pathway1))->aggregate([$user_id]);

        $aggregation = new first();
        $aggregation->set_user_ids([$user_id])
                    ->set_pathways([$pathway1])
                    ->aggregate();

        $this->assertNull($aggregation->get_achieved_value_id($user_id));
        $this->assertEquals([], $aggregation->get_achieved_via($user_id));
    }

    public function test_with_single_pathway_returning_value() {
        global $DB;

        $user_id = 101;

        $scale = $this->getDataGenerator()->get_plugin_generator('totara_hierarchy')->create_scale('comp');
        $scale_values = $DB->get_records('comp_scale_values', ['scaleid' => $scale->id]);
        $scale_value = new scale_value(array_pop($scale_values));

        $pathway1 = $this->getMockBuilder(\totara_competency\pathway::class)
                         ->setMethods(['aggregate_current_value', 'get_id', 'get_sortorder'])
                         ->getMockForAbstractClass();
        $achievement_detail = $this->getMockForAbstractClass(base_achievement_detail::class);
        $achievement_detail->set_scale_value_id($scale_value->id);
        $pathway1->method('aggregate_current_value')->willReturn($achievement_detail);
        $pathway1->method('get_id')->willReturn(201);
        $pathway1->method('get_sortorder')->willReturn(1);

        (new pathway_aggregator($pathway1))->aggregate([$user_id]);

        $aggregation = new first();
        $aggregation->set_user_ids([$user_id])
                    ->set_pathways([$pathway1])
                    ->aggregate();

        // Reload current achievement as values will need to be strings from the database for the expected and actual to match.
        $current_achievement = pathway_achievement::get_current($pathway1, $user_id);

        $this->assertEquals($scale_value->id, $aggregation->get_achieved_value_id($user_id));
        $this->assertEquals([$current_achievement], $aggregation->get_achieved_via($user_id));
    }

    public function test_multiple_pathways_returning_value_or_null() {
        global $DB;

        $user_id = 101;

        $scale = $this->getDataGenerator()->get_plugin_generator('totara_hierarchy')->create_scale('comp');
        $scale_values = $DB->get_records('comp_scale_values', ['scaleid' => $scale->id], 'sortorder DESC');

        $pathway1 = $this->getMockBuilder(\totara_competency\pathway::class)
                         ->setMethods(['aggregate_current_value', 'get_id', 'get_sortorder'])
                         ->getMockForAbstractClass();
        $achievement_detail1 = $this->getMockForAbstractClass(base_achievement_detail::class);
        $pathway1->method('aggregate_current_value')->willReturn($achievement_detail1);
        $pathway1->method('get_id')->willReturn(201);
        $pathway1->method('get_sortorder')->willReturn(1);

        (new pathway_aggregator($pathway1))->aggregate([$user_id]);

        $scale_value2 = new scale_value(array_pop($scale_values));
        $pathway2 = $this->getMockBuilder(\totara_competency\pathway::class)
                         ->setMethods(['aggregate_current_value', 'get_id', 'get_sortorder'])
                         ->getMockForAbstractClass();
        $achievement_detail2 = $this->getMockForAbstractClass(base_achievement_detail::class);
        $achievement_detail2->set_scale_value_id($scale_value2->id);
        $pathway2->method('aggregate_current_value')->willReturn($achievement_detail2);
        $pathway2->method('get_id')->willReturn(202);
        $pathway2->method('get_sortorder')->willReturn(2);

        (new pathway_aggregator($pathway2))->aggregate([$user_id]);

        $scale_value3 = new scale_value(array_pop($scale_values));
        $pathway3 = $this->getMockBuilder(\totara_competency\pathway::class)
                         ->setMethods(['aggregate_current_value', 'get_id', 'get_sortorder'])
                         ->getMockForAbstractClass();
        $achievement_detail3 = $this->getMockForAbstractClass(base_achievement_detail::class);
        $achievement_detail3->set_scale_value_id($scale_value3->id);
        $pathway3->method('aggregate_current_value')->willReturn($achievement_detail3);
        $pathway3->method('get_id')->willReturn(203);
        $pathway3->method('get_sortorder')->willReturn(3);

        (new pathway_aggregator($pathway3))->aggregate([$user_id]);

        $aggregation = new first();
        $aggregation->set_user_ids([$user_id])
            // I order pathway 3 before pathway 2 in the input array.
            // It should be reordered according to its get_sortorder value and that should mean that achievement2 is the achieved.
            ->set_pathways([$pathway1, $pathway3, $pathway2])
            ->aggregate();

        $current_achievement2 = pathway_achievement::get_current($pathway2, $user_id);

        // It skipped the null on the first pathway as it is lookingfor the first proper value.
        // pathway2 would have been ordered ahead of pathway3, and so we get the below:
        $this->assertEquals($scale_value2->id, $aggregation->get_achieved_value_id($user_id));
        $this->assertEquals([$current_achievement2], $aggregation->get_achieved_via($user_id));
    }
}