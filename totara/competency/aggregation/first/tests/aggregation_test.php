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

use totara_competency\aggregation_users_table;
use totara_competency\base_achievement_detail;
use totara_competency\entities\scale_value;
use totara_competency\entities\pathway_achievement;
use aggregation_first\first;
use totara_competency\pathway_evaluator;
use totara_competency\pathway_evaluator_user_source_table;

class aggregation_first_aggregation_testcase extends advanced_testcase {

    public function test_with_empty_pathways() {
        $user_id = 101;

        $aggregation = new first();
        $aggregation->set_pathways([])
                    ->aggregate_for_user($user_id);

        $this->assertNull($aggregation->get_achieved_value_id($user_id));
        $this->assertEquals([], $aggregation->get_achieved_via($user_id));
    }

    public function test_with_single_pathway_returning_null() {
        $user = $this->getDataGenerator()->create_user();

        /** @var totara_competency_generator $competency_generator */
        $competency_generator = $this->getDataGenerator()->get_plugin_generator('totara_competency');
        $competency = $competency_generator->create_competency();

        $pathway1 = $this->getMockBuilder(\totara_competency\pathway::class)
            ->setMethods(['aggregate_current_value', 'get_id', 'get_sortorder', 'set_competency', 'get_competency'])
            ->getMockForAbstractClass();
        $pathway1->set_competency($competency);

        $achievement_detail = $this->getMockForAbstractClass(base_achievement_detail::class);
        $pathway1->method('aggregate_current_value')->willReturn($achievement_detail);
        $pathway1->method('get_id')->willReturn(201);
        $pathway1->method('get_sortorder')->willReturn(1);

        $source_table = new aggregation_users_table();
        $source_table->queue_for_aggregation($user->id, 1);
        $pw_user_source = new pathway_evaluator_user_source_table($source_table, true);
        $pathway_evaluator = $this->getMockForAbstractClass(pathway_evaluator::class, [$pathway1, $pw_user_source]);
        $pathway_evaluator->aggregate();

        $aggregation = new first();
        $aggregation->set_pathways([$pathway1])
                    ->aggregate_for_user($user->id);

        $this->assertNull($aggregation->get_achieved_value_id($user->id));
        $this->assertEquals([], $aggregation->get_achieved_via($user->id));
    }

    public function test_with_single_pathway_returning_value() {
        global $DB;

        $user = $this->getDataGenerator()->create_user();

        /** @var totara_competency_generator $competency_generator */
        $competency_generator = $this->getDataGenerator()->get_plugin_generator('totara_competency');
        $competency = $competency_generator->create_competency();

        $scale = $this->getDataGenerator()->get_plugin_generator('totara_hierarchy')->create_scale('comp');
        $scale_values = $DB->get_records('comp_scale_values', ['scaleid' => $scale->id]);
        $scale_value = new scale_value(array_pop($scale_values));

        $pathway1 = $this->getMockBuilder(\totara_competency\pathway::class)
                         ->setMethods(['aggregate_current_value', 'get_id', 'get_sortorder', 'set_competency', 'get_competency'])
                         ->getMockForAbstractClass();
        $pathway1->set_competency($competency);

        $achievement_detail = $this->getMockForAbstractClass(base_achievement_detail::class);
        $achievement_detail->set_scale_value_id($scale_value->id);
        $pathway1->method('aggregate_current_value')->willReturn($achievement_detail);
        $pathway1->method('get_id')->willReturn(201);
        $pathway1->method('get_sortorder')->willReturn(1);

        $source_table = new aggregation_users_table();
        $source_table->queue_for_aggregation($user->id, 1);
        $pw_user_source = new pathway_evaluator_user_source_table($source_table, true);
        $pathway_evaluator = $this->getMockForAbstractClass(pathway_evaluator::class, [$pathway1, $pw_user_source]);
        $pathway_evaluator->aggregate();

        $aggregation = new first();
        $aggregation->set_pathways([$pathway1])
                    ->aggregate_for_user($user->id);

        // Reload current achievement as values will need to be strings from the database for the expected and actual to match.
        $current_achievement = pathway_achievement::get_current($pathway1, $user->id);

        $this->assertEquals($scale_value->id, $aggregation->get_achieved_value_id($user->id));
        $this->assertEquals([$current_achievement], $aggregation->get_achieved_via($user->id));
    }

    public function test_multiple_pathways_returning_value_or_null() {
        global $DB;

        $user = $this->getDataGenerator()->create_user();

        /** @var totara_competency_generator $competency_generator */
        $competency_generator = $this->getDataGenerator()->get_plugin_generator('totara_competency');
        $competency = $competency_generator->create_competency();

        $scale = $this->getDataGenerator()->get_plugin_generator('totara_hierarchy')->create_scale('comp');
        $scale_values = $DB->get_records('comp_scale_values', ['scaleid' => $scale->id], 'sortorder DESC');

        $pathway1 = $this->getMockBuilder(\totara_competency\pathway::class)
                         ->setMethods(['aggregate_current_value', 'get_id', 'get_sortorder', 'set_competency', 'get_competency'])
                         ->getMockForAbstractClass();
        $pathway1->set_competency($competency);
        $achievement_detail1 = $this->getMockForAbstractClass(base_achievement_detail::class);
        $pathway1->method('aggregate_current_value')->willReturn($achievement_detail1);
        $pathway1->method('get_id')->willReturn(201);
        $pathway1->method('get_sortorder')->willReturn(1);

        $source_table = new aggregation_users_table();
        $source_table->queue_for_aggregation($user->id, 1);
        $pw_user_source = new pathway_evaluator_user_source_table($source_table, true);
        $pathway_evaluator1 = $this->getMockForAbstractClass(pathway_evaluator::class, [$pathway1, $pw_user_source]);
        $pathway_evaluator1->aggregate();

        $scale_value2 = new scale_value(array_pop($scale_values));
        $pathway2 = $this->getMockBuilder(\totara_competency\pathway::class)
                         ->setMethods(['aggregate_current_value', 'get_id', 'get_sortorder', 'set_competency', 'get_competency'])
                         ->getMockForAbstractClass();
        $pathway2->set_competency($competency);
        $achievement_detail2 = $this->getMockForAbstractClass(base_achievement_detail::class);
        $achievement_detail2->set_scale_value_id($scale_value2->id);
        $pathway2->method('aggregate_current_value')->willReturn($achievement_detail2);
        $pathway2->method('get_id')->willReturn(202);
        $pathway2->method('get_sortorder')->willReturn(2);

        $pathway_evaluator2 = $this->getMockForAbstractClass(pathway_evaluator::class, [$pathway2, $pw_user_source]);
        $pathway_evaluator2->aggregate();

        $scale_value3 = new scale_value(array_pop($scale_values));
        $pathway3 = $this->getMockBuilder(\totara_competency\pathway::class)
                         ->setMethods(['aggregate_current_value', 'get_id', 'get_sortorder', 'set_competency', 'get_competency'])
                         ->getMockForAbstractClass();
        $pathway3->set_competency($competency);
        $achievement_detail3 = $this->getMockForAbstractClass(base_achievement_detail::class);
        $achievement_detail3->set_scale_value_id($scale_value3->id);
        $pathway3->method('aggregate_current_value')->willReturn($achievement_detail3);
        $pathway3->method('get_id')->willReturn(203);
        $pathway3->method('get_sortorder')->willReturn(3);

        $pathway_evaluator3 = $this->getMockForAbstractClass(pathway_evaluator::class, [$pathway3, $pw_user_source]);
        $pathway_evaluator3->aggregate();

        $aggregation = new first();
        // I order pathway 3 before pathway 2 in the input array.
        // It should be reordered according to its get_sortorder value and that should mean that achievement2 is the achieved.
        $aggregation->set_pathways([$pathway1, $pathway3, $pathway2])
            ->aggregate_for_user($user->id);

        $current_achievement2 = pathway_achievement::get_current($pathway2, $user->id);

        // It skipped the null on the first pathway as it is looking for the first proper value.
        // pathway2 would have been ordered ahead of pathway3, and so we get the below:
        $this->assertEquals($scale_value2->id, $aggregation->get_achieved_value_id($user->id));
        $this->assertEquals([$current_achievement2], $aggregation->get_achieved_via($user->id));
    }

}
