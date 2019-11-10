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

use core\orm\collection;
use totara_competency\aggregation_users_table;
use totara_competency\entities\scale_value;
use totara_competency\pathway;
use totara_competency\pathway_evaluator;
use totara_competency\entities\pathway_achievement;
use aggregation_highest\highest;
use totara_competency\base_achievement_detail;
use totara_competency\pathway_evaluator_user_source;

class aggregation_highest_aggregation_testcase extends advanced_testcase {

    public function test_with_empty_pathways() {
        $user_id = 101;

        $aggregation = new highest();
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

        $achievement_detail = $this->getMockForAbstractClass(base_achievement_detail::class);

        $pathway1 = $this->getMockBuilder(pathway::class)
                         ->setMethods(['aggregate_current_value', 'get_id', 'get_competency'])
                         ->getMockForAbstractClass();
        $pathway1->method('aggregate_current_value')->willReturn($achievement_detail);
        $pathway1->method('get_id')->willReturn(201);
        $pathway1->method('get_competency')->willReturn($competency);

        $source_table = new aggregation_users_table();
        $source_table->queue_for_aggregation($user->id, $competency->id);
        $pw_user_source = new pathway_evaluator_user_source($source_table, true);
        $pathway_evaluator = $this->getMockForAbstractClass(pathway_evaluator::class, [$pathway1, $pw_user_source]);
        $pathway_evaluator->aggregate();

        $aggregation = new highest();
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

        $achievement_detail = $this->getMockForAbstractClass(base_achievement_detail::class);
        $achievement_detail->set_scale_value_id($scale_value->id);

        $pathway1 = $this->getMockBuilder(pathway::class)
                         ->setMethods(['aggregate_current_value', 'get_id', 'get_competency'])
                         ->getMockForAbstractClass();
        $pathway1->set_competency($competency);
        $pathway1->method('aggregate_current_value')->willReturn($achievement_detail);
        $pathway1->method('get_id')->willReturn(201);
        $pathway1->method('get_competency')->willReturn($competency);

        $source_table = new aggregation_users_table();
        $source_table->queue_for_aggregation($user->id, $competency->id);
        $pw_user_source = new pathway_evaluator_user_source($source_table, true);
        $pathway_evaluator = $this->getMockForAbstractClass(pathway_evaluator::class, [$pathway1, $pw_user_source]);
        $pathway_evaluator->aggregate();

        $aggregation = new highest();
        $aggregation->set_pathways([$pathway1])
                    ->aggregate_for_user($user->id);

        // Reload current achievement as values will need to be strings from the database for the expected and actual to match.
        $current_achievement = pathway_achievement::get_current($pathway1, $user->id);

        $this->assertEquals($scale_value->id, $aggregation->get_achieved_value_id($user->id));
        $achieved_via = $aggregation->get_achieved_via($user->id);
        $this->assertContainsOnlyInstancesOf(pathway_achievement::class, $achieved_via);
        $achieved_via_ids = collection::new($achieved_via)->pluck('id');
        $this->assertEquals([$current_achievement->id], $achieved_via_ids);
    }

    public function test_multiple_pathways_returning_value_or_null() {
        global $DB;

        $user = $this->getDataGenerator()->create_user();

        /** @var totara_competency_generator $competency_generator */
        $competency_generator = $this->getDataGenerator()->get_plugin_generator('totara_competency');
        $competency = $competency_generator->create_competency();

        $scale = $this->getDataGenerator()->get_plugin_generator('totara_hierarchy')->create_scale('comp');
        $scale_values = $DB->get_records('comp_scale_values', ['scaleid' => $scale->id], 'sortorder DESC');

        // Now the ordering of scale values can be confusing. The sortorder is reversed in relation to the actual meaning.
        // This means the lower the sortorder the higher the actual value.

        // So we are now starting with the lowest and skip it as we only use the second and the highest value for this test
        $scale_value1 = new scale_value(array_shift($scale_values));

        $achievement_detail1 = $this->getMockForAbstractClass(base_achievement_detail::class);
        $pathway1 = $this->getMockBuilder(pathway::class)
                         ->setMethods(['aggregate_current_value', 'get_id', 'get_competency'])
                         ->getMockForAbstractClass();
        $pathway1->method('aggregate_current_value')->willReturn($achievement_detail1);
        $pathway1->method('get_id')->willReturn(201);
        $pathway1->method('get_competency')->willReturn($competency);

        $source_table = new aggregation_users_table();
        $source_table->queue_for_aggregation($user->id, $competency->id);
        $pw_user_source = new pathway_evaluator_user_source($source_table, true);
        $pathway_evaluator = $this->getMockForAbstractClass(pathway_evaluator::class, [$pathway1, $pw_user_source]);
        $pathway_evaluator->aggregate();

        // This is the second value and we use it to give the user an achievement
        $scale_value2 = new scale_value(array_shift($scale_values));
        $achievement_detail2 = $this->getMockForAbstractClass(base_achievement_detail::class);
        $achievement_detail2->set_scale_value_id($scale_value2->id);
        $pathway2 = $this->getMockBuilder(pathway::class)
                         ->setMethods(['aggregate_current_value', 'get_id', 'get_competency'])
                         ->getMockForAbstractClass();
        $pathway2->method('aggregate_current_value')->willReturn($achievement_detail2);
        $pathway2->method('get_id')->willReturn(202);
        $pathway2->method('get_competency')->willReturn($competency);

        $pathway_evaluator = $this->getMockForAbstractClass(pathway_evaluator::class, [$pathway2, $pw_user_source]);
        $pathway_evaluator->aggregate();

        // This is the highest value, which should be the one the users gets at the end as it's the highest
        $scale_value3 = new scale_value(array_shift($scale_values));
        $achievement_detail3 = $this->getMockForAbstractClass(base_achievement_detail::class);
        $achievement_detail3->set_scale_value_id($scale_value3->id);
        $pathway3a = $this->getMockBuilder(pathway::class)
                         ->setMethods(['aggregate_current_value', 'get_id', 'get_competency'])
                         ->getMockForAbstractClass();
        $pathway3a->method('aggregate_current_value')->willReturn($achievement_detail3);
        $pathway3a->method('get_id')->willReturn(203);
        $pathway3a->method('get_competency')->willReturn($competency);

        $pathway_evaluator = $this->getMockForAbstractClass(pathway_evaluator::class, [$pathway3a, $pw_user_source]);
        $pathway_evaluator->aggregate();

        $pathway3b = $this->getMockBuilder(pathway::class)
                         ->setMethods(['aggregate_current_value', 'get_id', 'get_competency'])
                         ->getMockForAbstractClass();
        $pathway3b->method('aggregate_current_value')->willReturn($achievement_detail3);
        $pathway3b->method('get_id')->willReturn(204);
        $pathway3b->method('get_competency')->willReturn($competency);

        $pathway_evaluator = $this->getMockForAbstractClass(pathway_evaluator::class, [$pathway3b, $pw_user_source]);
        $pathway_evaluator->aggregate();

        $aggregation = new highest();
        $aggregation->set_pathways([$pathway1, $pathway2, $pathway3a, $pathway3b])
                    ->aggregate_for_user($user->id);

        $current_achievement3a = pathway_achievement::get_current($pathway3a, $user->id);
        $current_achievement3b = pathway_achievement::get_current($pathway3b, $user->id);

        $this->assertEquals($scale_value3->id, $aggregation->get_achieved_value_id($user->id));
        $achieved_via = $aggregation->get_achieved_via($user->id);
        $this->assertContainsOnlyInstancesOf(pathway_achievement::class, $achieved_via);
        $achieved_via_ids = collection::new($achieved_via)->pluck('id');
        $this->assertEqualsCanonicalizing([$current_achievement3a->id, $current_achievement3b->id], $achieved_via_ids);
    }
}
