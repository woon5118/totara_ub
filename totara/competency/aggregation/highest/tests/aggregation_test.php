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
 * @package aggregation_highest
 */

use totara_competency\entities\scale_value;
use totara_competency\pathway_aggregator;
use totara_competency\entities\pathway_achievement;
use aggregation_highest\highest;
use totara_competency\base_achievement_detail;

class aggregation_highest_aggregation_testcase extends advanced_testcase {

    public function test_with_empty_pathways() {
        $user_id = 101;

        $aggregation = new highest();
        $aggregation->set_user_ids([$user_id])
                    ->set_pathways([])
                    ->aggregate();

        $this->assertNull($aggregation->get_achieved_value_id($user_id));
        $this->assertEquals([], $aggregation->get_achieved_via($user_id));
    }

    public function test_with_single_pathway_returning_null() {
        $user_id = 101;

        $achievement_detail = $this->getMockForAbstractClass(base_achievement_detail::class);

        $pathway1 = $this->getMockBuilder(\totara_competency\pathway::class)
                         ->setMethods(['aggregate_current_value', 'get_id'])
                         ->getMockForAbstractClass();
        $pathway1->method('aggregate_current_value')->willReturn($achievement_detail);
        $pathway1->method('get_id')->willReturn(201);

        (new pathway_aggregator($pathway1))->aggregate([$user_id]);

        $aggregation = new \aggregation_highest\highest();
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

        $achievement_detail = $this->getMockForAbstractClass(base_achievement_detail::class);
        $achievement_detail->set_scale_value_id($scale_value->id);

        $pathway1 = $this->getMockBuilder(\totara_competency\pathway::class)
                         ->setMethods(['aggregate_current_value', 'get_id'])
                         ->getMockForAbstractClass();
        $pathway1->method('aggregate_current_value')->willReturn($achievement_detail);
        $pathway1->method('get_id')->willReturn(201);

        (new pathway_aggregator($pathway1))->aggregate([$user_id]);

        $aggregation = new highest();
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

        $achievement_detail1 = $this->getMockForAbstractClass(base_achievement_detail::class);
        $pathway1 = $this->getMockBuilder(\totara_competency\pathway::class)
                         ->setMethods(['aggregate_current_value', 'get_id'])
                         ->getMockForAbstractClass();
        $pathway1->method('aggregate_current_value')->willReturn($achievement_detail1);
        $pathway1->method('get_id')->willReturn(201);

        (new pathway_aggregator($pathway1))->aggregate([$user_id]);

        $scale_value2 = new scale_value(array_pop($scale_values));
        $achievement_detail2 = $this->getMockForAbstractClass(base_achievement_detail::class);
        $achievement_detail2->set_scale_value_id($scale_value2->id);
        $pathway2 = $this->getMockBuilder(\totara_competency\pathway::class)
                         ->setMethods(['aggregate_current_value', 'get_id'])
                         ->getMockForAbstractClass();
        $pathway2->method('aggregate_current_value')->willReturn($achievement_detail2);
        $pathway2->method('get_id')->willReturn(202);

        (new pathway_aggregator($pathway2))->aggregate([$user_id]);

        $scale_value3 = new scale_value(array_pop($scale_values));
        $achievement_detail3 = $this->getMockForAbstractClass(base_achievement_detail::class);
        $achievement_detail3->set_scale_value_id($scale_value3->id);
        $pathway3a = $this->getMockBuilder(\totara_competency\pathway::class)
                         ->setMethods(['aggregate_current_value', 'get_id'])
                         ->getMockForAbstractClass();
        $pathway3a->method('aggregate_current_value')->willReturn($achievement_detail3);
        $pathway3a->method('get_id')->willReturn(203);

        (new pathway_aggregator($pathway3a))->aggregate([$user_id]);

        $pathway3b = $this->getMockBuilder(\totara_competency\pathway::class)
                         ->setMethods(['aggregate_current_value', 'get_id'])
                         ->getMockForAbstractClass();
        $pathway3b->method('aggregate_current_value')->willReturn($achievement_detail3);
        $pathway3b->method('get_id')->willReturn(204);

        (new pathway_aggregator($pathway3b))->aggregate([$user_id]);

        $aggregation = new \aggregation_highest\highest();
        $aggregation->set_user_ids([$user_id])
                    ->set_pathways([$pathway1, $pathway2, $pathway3a, $pathway3b])
                    ->aggregate();

        $current_achievement3a = pathway_achievement::get_current($pathway3a, $user_id);
        $current_achievement3b = pathway_achievement::get_current($pathway3b, $user_id);

        $this->assertEquals($scale_value3->id, $aggregation->get_achieved_value_id($user_id));
        $this->assertEquals([$current_achievement3a, $current_achievement3b], $aggregation->get_achieved_via($user_id));
    }
}