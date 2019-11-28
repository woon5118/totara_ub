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
 * @author Brendan Cox <brendan.cox@totaralearning.com>
 * @author Riana Rossouw <riana.rossouw@totaralearning.com>
 * @package totara_competency
 */

use hierarchy_competency\event\scale_min_proficient_value_updated;
use totara_competency\aggregation_users_table;
use totara_competency\competency_aggregator_user_source;
use totara_competency\entities\achievement_via;
use totara_competency\entities\assignment;
use totara_competency\entities\competency;
use totara_competency\achievement_configuration;
use totara_competency\competency_achievement_aggregator;
use totara_competency\entities\pathway_achievement;
use totara_competency\expand_task;
use totara_competency\hook\competency_achievement_updated;
use totara_competency\models\assignment_actions;
use totara_competency\overall_aggregation;
use totara_competency\entities\scale_value;
use totara_competency\entities\competency_achievement;
use totara_competency\pathway;
use totara_competency\pathway_evaluator_user_source;
use pathway_test_pathway\test_pathway_evaluator;
use aggregation_test_aggregation\test_aggregation;


/**
 * Class totara_competency_achievement_aggregator_testcase
 *
 * Tests behaviour of the competency_achievement_aggregator class.
 */
class totara_competency_achievement_aggregator_testcase extends advanced_testcase {

    /**
     * @param pathway_achievement[] $pathway_achievements
     * @return overall_aggregation
     */
    private function create_aggregation_method_achieved_by($pathway_achievements): overall_aggregation {
        /** @var totara_competency_generator $competency_generator */
        $competency_generator = $this->getDataGenerator()->get_plugin_generator('totara_competency');

        $test_aggregation = new test_aggregation();
        $achieved_values = [];
        $achieved_vias = [];
        foreach ($pathway_achievements as $pathway_achievement) {
            $user_id = $pathway_achievement->user_id;
            $achieved_values[$user_id] = $pathway_achievement->scale_value;
            if (!isset($achieved_vias[$user_id])) {
                $achieved_vias[$user_id] = [];
            }
            $achieved_vias[$user_id][] = $pathway_achievement;
        }
        $test_aggregation->set_test_aggregated_data($achieved_values, $achieved_vias);

        return $test_aggregation;
    }

    private function generate_active_expanded_user_assignments($competency, $users, $assignments_per_user = 1) {
        global $DB;

        /** @var totara_competency_assignment_generator $assignment_generator */
        $assignment_generator = $this->getDataGenerator()->get_plugin_generator('totara_competency')->assignment_generator();

        $assignment_ids = [];
        foreach ($users as $user) {
            for ($i = 0; $i < $assignments_per_user; $i++) {
                $assignment = $assignment_generator->create_user_assignment(
                    $competency->id,
                    $user->id,
                    ['status' => assignment::STATUS_ACTIVE]
                );
                $assignment_ids[] = $assignment->id;
            }
        }

        $expand_task = new expand_task($DB);
        $expand_task->expand_all();

        return $assignment_ids;
    }

    public function test_with_no_users() {
        /** @var totara_hierarchy_generator $hierarchy_generator */
        $hierarchy_generator = $this->getDataGenerator()->get_plugin_generator('totara_hierarchy');
        $compfw = $hierarchy_generator->create_comp_frame([]);
        $comp = $hierarchy_generator->create_comp(['frameworkid' => $compfw->id]);
        $competency = new competency($comp);
        $achievement_configuration = new achievement_configuration($competency);

        $source_table = new aggregation_users_table();
        $user_source = new competency_aggregator_user_source($source_table, true);
        $aggregator = new competency_achievement_aggregator($achievement_configuration, $user_source);

        $sink = $this->redirectHooks();
        // We're mainly testing that aggregate completes without an exception.
        $aggregator->aggregate();
        $this->assertEquals(0, $sink->count());
    }

    public function test_with_one_user_requiring_completion() {
        /** @var totara_competency_generator $competency_generator */
        $competency_generator = $this->getDataGenerator()->get_plugin_generator('totara_competency');
        $competency = $competency_generator->create_competency();

        /** @var scale_value $scale_value */
        $scale_value = $competency->scale->sorted_values_high_to_low->first();

        $pathway = $competency_generator->create_test_pathway($competency);
        $pathway->set_test_aggregate_current_value($scale_value);

        $achievement_configuration = new achievement_configuration($competency);
        $achievement_configuration->set_aggregation_type('test_aggregation');

        $user = $this->getDataGenerator()->create_user();
        $this->generate_active_expanded_user_assignments($competency, [$user]);

        $this->aggregate_pathway($pathway, $user);

        $this->assertEquals(0, competency_achievement::repository()->count());

        $sink = $this->redirectHooks();
        $aggregator = $this->get_competency_aggregator_for_pathway_and_user($pathway, $user);
        $pw_achievement = pathway_achievement::get_current($pathway, $user->id);
        $aggregator->aggregate();
        $hooks = $sink->get_hooks();

        $achievements = competency_achievement::repository()->get();
        $this->assertCount(1, $achievements);
        $achievement = $achievements->shift();
        $this->assertEquals($scale_value->id, $achievement->scale_value_id);

        $via_records = achievement_via::repository()->get();
        $this->assertCount(1, $via_records);
        $via_record = $via_records->shift();
        $this->assertEquals($pw_achievement->id, $via_record->pathway_achievement_id);

        $hook = reset($hooks);
        $this->assertInstanceOf(competency_achievement_updated::class, $hook);
        $sink->close();
    }

    public function test_with_one_user_requiring_completion_via_two_pathways() {
        global $DB;

        /** @var totara_competency_generator $competency_generator */
        $competency_generator = $this->getDataGenerator()->get_plugin_generator('totara_competency');
        $competency = $competency_generator->create_competency();

        /** @var scale_value $scale_value */
        $scale_value = $competency->scale->sorted_values_high_to_low->first();

        // Two pathways that will return the same scale_value.
        $pathway1 = $competency_generator->create_test_pathway($competency);
        $pathway1->set_test_aggregate_current_value($scale_value);
        $pathway2 = $competency_generator->create_test_pathway($competency);
        $pathway2->set_test_aggregate_current_value($scale_value);

        $achievement_configuration = new achievement_configuration($competency);
        $achievement_configuration->set_aggregation_type('test_aggregation');

        $user = $this->getDataGenerator()->create_user();
        $this->generate_active_expanded_user_assignments($competency, [$user]);

        $source_table = new aggregation_users_table();
        $source_table->queue_for_aggregation($user->id, $competency->id);
        $pw_user_source = new pathway_evaluator_user_source($source_table, true);
        (new test_pathway_evaluator($pathway1, $pw_user_source))->aggregate(time());
        (new test_pathway_evaluator($pathway2, $pw_user_source))->aggregate(time());

        $achievement1 = pathway_achievement::get_current($pathway1, $user->id);
        $achievement2 = pathway_achievement::get_current($pathway2, $user->id);

        $source_table = new aggregation_users_table();
        $source_table->queue_for_aggregation($user->id, $competency->id);
        $comp_user_source = new competency_aggregator_user_source($source_table, true);
        $aggregator = new competency_achievement_aggregator($achievement_configuration, $comp_user_source);

        $aggregation_method = $this->create_aggregation_method_achieved_by([$achievement1, $achievement2]);
        $aggregator->set_aggregation_instance($aggregation_method);

        $this->assertEquals(0, $DB->count_records('totara_competency_achievement'));

        $sink = $this->redirectHooks();
        $aggregator->aggregate();
        $hooks = $sink->get_hooks();

        $comp_records = $DB->get_records('totara_competency_achievement');
        $this->assertCount(1, $comp_records);
        $comp_record = reset($comp_records);
        $this->assertEquals($scale_value->id, $comp_record->scale_value_id);

        $via_records = $DB->get_records('totara_competency_achievement_via');
        $this->assertCount(2, $via_records);
        $achievement_ids = [$achievement1->id, $achievement2->id];
        $via_record1 = array_pop($via_records);
        $this->assertContains($via_record1->pathway_achievement_id, $achievement_ids);
        $via_record2 = array_pop($via_records);
        $this->assertContains($via_record2->pathway_achievement_id, $achievement_ids);

        // This should ensure that they we did get a via record for both achievements.
        $this->assertNotEquals($via_record1->pathway_achievement_id, $via_record2->pathway_achievement_id);

        $hook = reset($hooks);
        $this->assertInstanceOf(competency_achievement_updated::class, $hook);
        $sink->close();
    }

    public function test_one_user_from_two_via_records_to_one() {
        global $DB;

        /** @var totara_competency_generator $competency_generator */
        $competency_generator = $this->getDataGenerator()->get_plugin_generator('totara_competency');
        $competency = $competency_generator->create_competency();

        /** @var scale_value $scale_value */
        $scale_value = $competency->scale->sorted_values_high_to_low->first();

        // Two pathways that will return the same scale_value.
        $pathway1 = $competency_generator->create_test_pathway($competency);
        $pathway1->set_test_aggregate_current_value($scale_value);
        $pathway2 = $competency_generator->create_test_pathway($competency);
        $pathway2->set_test_aggregate_current_value($scale_value);

        $achievement_configuration = new achievement_configuration($competency);
        $achievement_configuration->set_aggregation_type('test_aggregation');

        $user = $this->getDataGenerator()->create_user();
        $this->generate_active_expanded_user_assignments($competency, [$user]);

        $source_table = new aggregation_users_table();
        $source_table->queue_for_aggregation($user->id, $competency->id);
        $pw_user_source = new pathway_evaluator_user_source($source_table, true);
        (new test_pathway_evaluator($pathway1, $pw_user_source))->aggregate(time());
        (new test_pathway_evaluator($pathway2, $pw_user_source))->aggregate(time());

        $achievement1 = pathway_achievement::get_current($pathway1, $user->id);
        $achievement2 = pathway_achievement::get_current($pathway2, $user->id);

        $source_table = new aggregation_users_table();
        $source_table->queue_for_aggregation($user->id, $competency->id);
        $comp_user_source = new competency_aggregator_user_source($source_table, true);
        $aggregator = new competency_achievement_aggregator($achievement_configuration, $comp_user_source);

        $aggregation_method = $this->create_aggregation_method_achieved_by([$achievement1, $achievement2]);
        $aggregator->set_aggregation_instance($aggregation_method);

        $this->assertEquals(0, $DB->count_records('totara_competency_achievement'));

        $aggregator->aggregate();

        // This point is tested more in previous tests.
        $this->assertEquals(1, $DB->count_records('totara_competency_achievement', ['status' => 0]));
        $this->assertEquals(2, $DB->count_records('totara_competency_achievement_via'));

        // We'll replace the aggregation instance with one that will just say the user achieved their score via #2.
        $aggregation_method = $this->create_aggregation_method_achieved_by([$achievement2]);
        $aggregator->set_aggregation_instance($aggregation_method);

        $sink = $this->redirectHooks();
        $aggregator->aggregate();
        // Hooks aren't triggered if the value isn't updated.
        $this->assertEquals(0, $sink->count());

        // Check comp_record value. Just to make sure it hasn't been set to null or some such thing when the other
        // achievement was taken away.
        $comp_records = $DB->get_records('totara_competency_achievement');
        $this->assertCount(1, $comp_records);
        $comp_record = reset($comp_records);
        $this->assertEquals($scale_value->id, $comp_record->scale_value_id);

        // The value didn't change. So no via records are dropped. The via records give how the value was attained
        // at the time that they achieved the value.
        $via_records = $DB->get_records('totara_competency_achievement_via');
        $this->assertCount(2, $via_records);
        $via_record = array_pop($via_records);
        $this->assertEquals($via_record->pathway_achievement_id, $achievement2->id);
    }

    public function test_one_user_from_having_value_to_null() {
        global $DB;

        /** @var totara_competency_generator $competency_generator */
        $competency_generator = $this->getDataGenerator()->get_plugin_generator('totara_competency');
        $competency = $competency_generator->create_competency();

        /** @var scale_value $scale_value */
        $scale_value = $competency->scale->sorted_values_high_to_low->first();

        // Two pathways that will return the same scale_value.
        $pathway1 = $competency_generator->create_test_pathway($competency);
        $pathway1->set_test_aggregate_current_value($scale_value);
        $pathway2 = $competency_generator->create_test_pathway($competency);
        $pathway2->set_test_aggregate_current_value($scale_value);

        $achievement_configuration = new achievement_configuration($competency);
        $achievement_configuration->set_aggregation_type('test_aggregation');

        $user = $this->getDataGenerator()->create_user();
        $this->generate_active_expanded_user_assignments($competency, [$user]);

        $source_table = new aggregation_users_table();
        $source_table->queue_for_aggregation($user->id, $competency->id);
        $pw_user_source = new pathway_evaluator_user_source($source_table, true);
        (new test_pathway_evaluator($pathway1, $pw_user_source))->aggregate(time());
        (new test_pathway_evaluator($pathway2, $pw_user_source))->aggregate(time());

        $achievement1 = pathway_achievement::get_current($pathway1, $user->id);
        $achievement2 = pathway_achievement::get_current($pathway2, $user->id);

        $comp_user_source = new competency_aggregator_user_source($source_table, true);
        $aggregator = new competency_achievement_aggregator($achievement_configuration, $comp_user_source);

        $aggregation_method = $this->create_aggregation_method_achieved_by([$achievement1, $achievement2]);
        $aggregator->set_aggregation_instance($aggregation_method);

        $this->assertEquals(0, $DB->count_records('totara_competency_achievement'));

        $aggregator->aggregate();

        // This point is tested more in previous tests.
        $this->assertEquals(1, $DB->count_records('totara_competency_achievement'));
        $this->assertEquals(2, $DB->count_records('totara_competency_achievement_via'));

        // We'll replace the aggregation instance with one that will just say the user achieved their score via #2.
        $aggregation_method = $this->create_aggregation_method_achieved_by([$achievement2]);
        $aggregator->set_aggregation_instance($aggregation_method);

        $aggregator->aggregate();

        // Change what value the pathways return. We'll then need to update the achievements used.
        $pathway1->set_test_aggregate_current_value(null);
        $pathway2->set_test_aggregate_current_value(null);

        (new test_pathway_evaluator($pathway1, $pw_user_source))->aggregate(time());
        (new test_pathway_evaluator($pathway2, $pw_user_source))->aggregate(time());

        $achievement1 = pathway_achievement::get_current($pathway1, $user->id);
        $achievement2 = pathway_achievement::get_current($pathway2, $user->id);

        $aggregation_method = $this->create_aggregation_method_achieved_by([$achievement1, $achievement2]);
        $aggregator->set_aggregation_instance($aggregation_method);

        $sink = $this->redirectHooks();
        $aggregator->aggregate();
        $hooks = $sink->get_hooks();

        // Order by newest at they back so they can be popped off in that order.
        $comp_records = $DB->get_records('totara_competency_achievement', [], 'time_created ASC, id ASC');
        $this->assertCount(2, $comp_records);
        $comp_record = array_pop($comp_records);
        $this->assertNull($comp_record->scale_value_id);
        $comp_record = array_pop($comp_records);
        $this->assertEquals($scale_value->id, $comp_record->scale_value_id);

        // We have 2 more via records because the aggregation method returned 2 pathway achievements.
        // These are not filtered out by the competency_achievement_aggregator just because they are null.
        // Todo: consider what behaviour meets our needs here:
        // 1. Aggregation methods should return all pathways with a null value
        // 2. Aggregation methods shouldn't return all pathways with a null value.
        //    This is how it currently works. There are logical issues with 1 such as when null achievements are a placeholder
        // And if the aggregation method does return a null value for it's 'via' record,
        // should the competency_achievement_aggregator save it or not.
        $this->assertEquals(4, $DB->count_records('totara_competency_achievement_via'));

        // The value changed, so a hook was executed.
        $hook = reset($hooks);
        $this->assertInstanceOf(competency_achievement_updated::class, $hook);
        $sink->close();
    }

    public function test_one_user_with_change_in_scale_value() {
        global $DB;

        /** @var totara_competency_generator $competency_generator */
        $competency_generator = $this->getDataGenerator()->get_plugin_generator('totara_competency');
        $competency = $competency_generator->create_competency();

        /** @var scale_value $scale_value1 */
        $scale = $competency->scale;
        $values = $scale->sorted_values_high_to_low;

        $scale_value1 = $values->first();
        $values->next();
        /** @var scale_value $scale_value2 */
        $scale_value2 = $values->current();

        $pathway = $competency_generator->create_test_pathway($competency);
        $pathway->set_test_aggregate_current_value($scale_value1);

        $user = $this->getDataGenerator()->create_user();
        $this->generate_active_expanded_user_assignments($competency, [$user]);

        $this->aggregate_pathway($pathway, $user);

        $pw_achievement1 = pathway_achievement::get_current($pathway, $user->id);

        $this->assertEquals(0, competency_achievement::repository()->count());

        $aggregator = $this->get_competency_aggregator_for_pathway_and_user($pathway, $user);
        $aggregator->aggregate();

        // Should all be about scale value and achievement #1.
        $achievements = competency_achievement::repository()->get();
        $this->assertCount(1, $achievements);
        $achievement = $achievements->shift();
        $this->assertEquals($scale_value1->id, $achievement->scale_value_id);

        $via_records = achievement_via::repository()->get();
        $this->assertCount(1, $via_records);
        $via_record = $via_records->pop();
        $this->assertEquals($via_record->pathway_achievement_id, $pw_achievement1->id);

        $this->assertNotEquals($scale_value1->id, $scale_value2->id);

        $pathway2 = $competency_generator->create_test_pathway($competency);
        $pathway2->set_test_aggregate_current_value($scale_value2);

        $this->aggregate_pathway($pathway2, $user);
        $pw_achievement2 = pathway_achievement::get_current($pathway2, $user->id);

        $sink = $this->redirectHooks();
        $aggregator = $this->get_competency_aggregator_for_pathway_and_user($pathway2, $user);
        $aggregator->aggregate();
        $hooks = $sink->get_hooks();

        // Order by newest at they back so they can be popped off in that order.
        $achievements = competency_achievement::repository()
            ->order_by('time_created', 'asc')
            ->order_by('id', 'asc')
            ->get();
        $this->assertCount(2, $achievements);
        $comp_record2 = $achievements->pop();
        $this->assertEquals($scale_value2->id, $comp_record2->scale_value_id);
        $comp_record1 = $achievements->pop();
        $this->assertEquals($scale_value1->id, $comp_record1->scale_value_id);

        $via_records = achievement_via::repository()
            ->order_by('id', 'asc')
            ->get();
        $this->assertCount(2, $via_records);
        $via_record = $via_records->pop();
        $this->assertEquals($via_record->comp_achievement_id, $comp_record2->id);
        $via_record = $via_records->pop();
        $this->assertEquals($via_record->comp_achievement_id, $comp_record1->id);

        // The value changed, so a hook was triggered.
        $hook = reset($hooks);
        $this->assertInstanceOf(competency_achievement_updated::class, $hook);
        $sink->close();
    }

    public function test_with_one_user_with_two_assignments_requiring_completion() {
        global $DB;

        /** @var totara_competency_generator $competency_generator */
        $competency_generator = $this->getDataGenerator()->get_plugin_generator('totara_competency');
        $competency = $competency_generator->create_competency();

        /** @var scale_value $scale_value */
        $scale_value = $competency->scale->sorted_values_high_to_low->first();

        $pathway = $competency_generator->create_test_pathway($competency);
        $pathway->set_test_aggregate_current_value($scale_value);

        $achievement_configuration = new achievement_configuration($competency);
        $achievement_configuration->set_aggregation_type('test_aggregation');

        $user = $this->getDataGenerator()->create_user();
        $source_table = new aggregation_users_table();
        $source_table->queue_for_aggregation($user->id, $competency->id);
        $pw_user_source = new pathway_evaluator_user_source($source_table, true);
        (new test_pathway_evaluator($pathway, $pw_user_source))->aggregate(time());
        $assignmentids = $this->generate_active_expanded_user_assignments($competency, [$user], 2);

        $source_table = new aggregation_users_table();
        $source_table->queue_for_aggregation($user->id, $competency->id);
        $comp_user_source = new competency_aggregator_user_source($source_table, true);
        $aggregator = new competency_achievement_aggregator($achievement_configuration, $comp_user_source);

        $achievement = pathway_achievement::get_current($pathway, $user->id);
        $aggregation_method = $this->create_aggregation_method_achieved_by([$achievement]);
        $aggregator->set_aggregation_instance($aggregation_method);

        $this->assertEquals(0, $DB->count_records('totara_competency_achievement'));

        $sink = $this->redirectHooks();
        $aggregator->aggregate();
        $hooks = $sink->get_hooks();

        $comp_records = $DB->get_records('totara_competency_achievement');
        $this->assertCount(2, $comp_records);
        $comp_record1 = array_pop($comp_records);
        $this->assertEquals($scale_value->id, $comp_record1->scale_value_id);
        $this->assertEquals(0, $comp_record1->status);
        $comp_record2 = array_pop($comp_records);
        $this->assertEquals($scale_value->id, $comp_record2->scale_value_id);
        $this->assertEquals(0, $comp_record2->status);
        $this->assertNotEquals($comp_record1->assignment_id, $comp_record2->assignment_id);

        $via_records = $DB->get_records('totara_competency_achievement_via');
        $this->assertCount(2, $via_records);
        $via_record = reset($via_records);
        $this->assertEquals($achievement->id, $via_record->pathway_achievement_id);

        $hook = reset($hooks);
        $this->assertInstanceOf(competency_achievement_updated::class, $hook);

        // Follow-on scenario. One of the assignments is archived. The status on just that comp_record should reflect that.

        $disable_assignment_id = array_pop($assignmentids);

        // Don't trigger events for archiving
        $events_sink = $this->redirectEvents();
        $model = new assignment_actions();
        $model->archive([$disable_assignment_id]);
        $expand_task = new expand_task($DB);
        $expand_task->expand_all();
        $events_sink->close();

        $aggregator = new competency_achievement_aggregator($achievement_configuration, $comp_user_source);
        $aggregation_method = $this->create_aggregation_method_achieved_by([$achievement]);
        $aggregator->set_aggregation_instance($aggregation_method);
        $sink->clear();
        $aggregator->aggregate();
        $hooks = $sink->get_hooks();

        $comp_records = $DB->get_records('totara_competency_achievement');
        $this->assertCount(2, $comp_records);
        foreach ($comp_records as $comp_record) {
            if ($comp_record->assignment_id == $disable_assignment_id) {
                $this->assertEquals($scale_value->id, $comp_record->scale_value_id);
                $this->assertEquals(1, $comp_record->status);
            } else {
                $this->assertEquals($scale_value->id, $comp_record->scale_value_id);
                $this->assertEquals(0, $comp_record->status);
            }
        }

        $via_records = $DB->get_records('totara_competency_achievement_via');
        $this->assertCount(2, $via_records);
        $via_record = reset($via_records);
        $this->assertEquals($achievement->id, $via_record->pathway_achievement_id);

        $this->assertCount(0, $hooks);
        $sink->close();
    }

    public function test_change_in_minimum_proficiency() {
        // When the minimum proficient value of a scale changes. We'll need to see if that means
        // any comp records with active assignments should become superseded and replaced with a new one
        // (which is just the case if the scale value they had has gone from proficient to not or vice versa).

        /** @var totara_competency_generator $competency_generator */
        $competency_generator = $this->getDataGenerator()->get_plugin_generator('totara_competency');
        $competency = $competency_generator->create_competency();

        $keyed_scale_values = $competency
            ->scale
            ->sorted_values_high_to_low
            ->key_by('sortorder')
            ->all(true);

        $min_proficient_value = $competency->scale->min_proficient_value;
        $non_proficient_value = $keyed_scale_values[$min_proficient_value->sortorder + 1];

        // Make sure we got the right values
        $this->assertInstanceOf(scale_value::class, $non_proficient_value);
        $this->assertEquals(0, $non_proficient_value->proficient);
        $this->assertInstanceOf(scale_value::class, $min_proficient_value);
        $this->assertEquals(1, $min_proficient_value->proficient);
        $this->assertEquals($min_proficient_value->id, $competency->scale->minproficiencyid);

        $user = $this->getDataGenerator()->create_user();

        $pathway = $competency_generator->create_test_pathway($competency);
        $pathway->set_test_aggregate_current_value($non_proficient_value);

        $this->aggregate_pathway($pathway, $user);

        $this->generate_active_expanded_user_assignments($competency, [$user], 1);

        $this->assertEquals(0, competency_achievement::repository()->count());

        $aggregator = $this->get_competency_aggregator_for_pathway_and_user($pathway, $user);
        $aggregator->aggregate();

        $achievements = competency_achievement::repository()->get();
        $this->assertEquals(1, $achievements->count());
        /** @var competency_achievement $achievement */
        $achievement = $achievements->first();

        // The user has an achievement but is not considered proficient
        $this->assertEquals(competency_achievement::ACTIVE_ASSIGNMENT, $achievement->status);
        $this->assertEquals(0, $achievement->proficient);

        // alright, now change the minimum proficiency value and trigger the event
        // which is the quickest way of queuing the change
        $scale = $competency->scale;
        $scale->minproficiencyid = $non_proficient_value->id;
        $scale->save();

        $non_proficient_value->proficient = 1;
        $non_proficient_value->save();

        scale_min_proficient_value_updated::create_from_instance((object)$scale->to_array())->trigger();

        $aggregator = $this->get_competency_aggregator_for_pathway_and_user($pathway, $user);
        $aggregator->aggregate();

        $achievements = competency_achievement::repository()
            ->order_by('id', 'asc')
            ->get();
        $this->assertEquals(2, $achievements->count());

        // The first one is the old proficient one, now superseded
        $achievement = $achievements->shift();
        $this->assertEquals(competency_achievement::SUPERSEDED, $achievement->status);
        $this->assertEquals(0, $achievement->proficient);
        $this->assertEquals($non_proficient_value->id, $achievement->scale_value_id);

        // The second one is the new one with the same scale value id but without being proficient
        $achievement = $achievements->shift();
        $this->assertEquals(competency_achievement::ACTIVE_ASSIGNMENT, $achievement->status);
        $this->assertEquals(1, $achievement->proficient);
        $this->assertEquals($non_proficient_value->id, $achievement->scale_value_id);

        // ok now change it back and see if it also switches back to proficient
        $scale = $competency->scale;
        $scale->minproficiencyid = $min_proficient_value->id;
        $scale->save();

        $non_proficient_value->proficient = 0;
        $non_proficient_value->save();

        scale_min_proficient_value_updated::create_from_instance((object)$scale->to_array())->trigger();

        $aggregator = $this->get_competency_aggregator_for_pathway_and_user($pathway, $user);
        $aggregator->aggregate();

        $achievements = competency_achievement::repository()
            ->order_by('id', 'asc')
            ->get();
        $this->assertEquals(3, $achievements->count());

        // The first one is the old proficient one, now superseded
        $achievement = $achievements->shift();
        $this->assertEquals(competency_achievement::SUPERSEDED, $achievement->status);
        $this->assertEquals(0, $achievement->proficient);
        $this->assertEquals($non_proficient_value->id, $achievement->scale_value_id);

        // The second one is also superseded
        $achievement = $achievements->shift();
        $this->assertEquals(competency_achievement::SUPERSEDED, $achievement->status);
        $this->assertEquals(1, $achievement->proficient);
        $this->assertEquals($non_proficient_value->id, $achievement->scale_value_id);

        // The third one is the new one with the same scale value id but without being proficient
        $achievement = $achievements->shift();
        $this->assertEquals(competency_achievement::ACTIVE_ASSIGNMENT, $achievement->status);
        $this->assertEquals(0, $achievement->proficient);
        $this->assertEquals($non_proficient_value->id, $achievement->scale_value_id);
    }

    protected function aggregate_pathway(pathway $pathway, stdClass $user) {
        $competency = $pathway->get_competency();

        $source_table = new aggregation_users_table();
        $source_table->queue_for_aggregation($user->id, $competency->id);

        $pw_user_source = new pathway_evaluator_user_source($source_table, true);
        (new test_pathway_evaluator($pathway, $pw_user_source))->aggregate(time());
    }

    protected function get_competency_aggregator_for_pathway_and_user(pathway $pathway, stdClass $user) {
        $source_table = new aggregation_users_table();

        $achievement_configuration = new achievement_configuration($pathway->get_competency());
        $achievement_configuration->set_aggregation_type('test_aggregation');

        $comp_user_source = new competency_aggregator_user_source($source_table, true);
        $aggregator = new competency_achievement_aggregator($achievement_configuration, $comp_user_source);

        $achievement = pathway_achievement::get_current($pathway, $user->id);
        $aggregation_method = $this->create_aggregation_method_achieved_by([$achievement]);
        $aggregator->set_aggregation_instance($aggregation_method);

        return $aggregator;
    }

    public function test_archived_assignment_not_updated() {
        global $DB;

        /** @var totara_competency_generator $competency_generator */
        $competency_generator = $this->getDataGenerator()->get_plugin_generator('totara_competency');
        $competency = $competency_generator->create_competency();

        $scale = $competency->scale;
        $values = $scale->sorted_values_high_to_low;
        $scale_value1 = $values->first();
        $values->next();
        /** @var scale_value $scale_value2 */
        $scale_value2 = $values->current();

        $pathway = $competency_generator->create_test_pathway($competency);
        $pathway->set_test_aggregate_current_value($scale_value1);

        $achievement_configuration = new achievement_configuration($competency);
        $achievement_configuration->set_aggregation_type('test_aggregation');

        $user = $this->getDataGenerator()->create_user();
        $assignment_ids = $this->generate_active_expanded_user_assignments($competency, [$user]);

        $source_table = new aggregation_users_table();
        $source_table->queue_for_aggregation($user->id, $competency->id);
        $pw_user_source = new pathway_evaluator_user_source($source_table, true);
        (new test_pathway_evaluator($pathway, $pw_user_source))->aggregate(time());

        $source_table = new aggregation_users_table();
        $source_table->queue_for_aggregation($user->id, $competency->id);
        $comp_user_source = new competency_aggregator_user_source($source_table, true);
        $aggregator = new competency_achievement_aggregator($achievement_configuration, $comp_user_source);

        $achievement = pathway_achievement::get_current($pathway, $user->id);
        $aggregation_method = $this->create_aggregation_method_achieved_by([$achievement]);
        $aggregator->set_aggregation_instance($aggregation_method);

        $this->assertEquals(0, $DB->count_records('totara_competency_achievement'));

        $sink = $this->redirectHooks();
        $aggregator->aggregate();
        $hooks = $sink->get_hooks();

        $comp_records = $DB->get_records('totara_competency_achievement');
        $this->assertCount(1, $comp_records);
        $comp_record_while_assigned = reset($comp_records);
        $this->assertEquals($scale_value1->id, $comp_record_while_assigned->scale_value_id);

        $via_records = $DB->get_records('totara_competency_achievement_via');
        $this->assertCount(1, $via_records);
        $via_record = reset($via_records);
        $this->assertEquals($achievement->id, $via_record->pathway_achievement_id);

        $hook = reset($hooks);
        $this->assertInstanceOf(competency_achievement_updated::class, $hook);
        $sink->close();

        $disable_assignment_id = array_pop($assignment_ids);

        $model = new assignment_actions();
        $model->archive([$disable_assignment_id]);
        $expand_task = new expand_task($DB);
        $expand_task->expand_all();

        // Add a new pathway achievement, which would prompt a new competency record if it were possible.
        $pathway2 = $competency_generator->create_test_pathway($competency);
        $pathway2->set_test_aggregate_current_value($scale_value2);

        (new test_pathway_evaluator($pathway2, $pw_user_source))->aggregate(time());
        $achievement2 = pathway_achievement::get_current($pathway2, $user->id);

        $aggregation_method = $this->create_aggregation_method_achieved_by([$achievement2]);
        $aggregator->set_aggregation_instance($aggregation_method);

        $sink = $this->redirectHooks();
        $aggregator->aggregate();
        $hooks = $sink->get_hooks();

        // There should still be one record.
        $comp_records = $DB->get_records('totara_competency_achievement');
        $this->assertCount(1, $comp_records);

        $comp_record_while_archived = reset($comp_records);

        // It should not equal the new scale value. It should equal the one in the original competency record.
        $this->assertEquals($comp_record_while_assigned->scale_value_id, $comp_record_while_archived->scale_value_id);
        $this->assertEquals($comp_record_while_assigned->proficient, $comp_record_while_archived->proficient);
        $this->assertEquals(competency_achievement::ARCHIVED_ASSIGNMENT, $comp_record_while_archived->status);
    }

    public function test_superseded_record_not_updated() {
        global $DB;

        /** @var totara_competency_generator $competency_generator */
        $competency_generator = $this->getDataGenerator()->get_plugin_generator('totara_competency');
        $competency = $competency_generator->create_competency();

        $scale = $competency->scale;
        $values = $scale->sorted_values_high_to_low;

        /** @var scale_value $scale_value1 */
        $scale_value1 = $values->first();
        $values->next();
        /** @var scale_value $scale_value2 */
        $scale_value2 = $values->current();
        $values->next();
        /** @var scale_value $scale_value3 */
        $scale_value3 = $values->current();

        $pathway = $competency_generator->create_test_pathway($competency);
        $pathway->set_test_aggregate_current_value($scale_value1);

        $achievement_configuration = new achievement_configuration($competency);
        $achievement_configuration->set_aggregation_type('test_aggregation');

        $user = $this->getDataGenerator()->create_user();
        $this->generate_active_expanded_user_assignments($competency, [$user]);

        $source_table = new aggregation_users_table();
        $source_table->queue_for_aggregation($user->id, $competency->id);
        $pw_user_source = new pathway_evaluator_user_source($source_table, true);
        (new test_pathway_evaluator($pathway, $pw_user_source))->aggregate(time());

        $source_table = new aggregation_users_table();
        $source_table->queue_for_aggregation($user->id, $competency->id);
        $comp_user_source = new competency_aggregator_user_source($source_table, true);
        $aggregator = new competency_achievement_aggregator($achievement_configuration, $comp_user_source);

        $achievement = pathway_achievement::get_current($pathway, $user->id);
        $aggregation_method = $this->create_aggregation_method_achieved_by([$achievement]);
        $aggregator->set_aggregation_instance($aggregation_method);

        $this->assertEquals(0, $DB->count_records('totara_competency_achievement'));

        $aggregator->aggregate();

        $comp_records = $DB->get_records('totara_competency_achievement');
        $this->assertCount(1, $comp_records);
        $first_comp_record = reset($comp_records);
        $this->assertEquals($scale_value1->id, $first_comp_record->scale_value_id);

        $via_records = $DB->get_records('totara_competency_achievement_via');
        $this->assertCount(1, $via_records);
        $via_record = reset($via_records);
        $this->assertEquals($achievement->id, $via_record->pathway_achievement_id);

        // Add a new pathway achievement, which should prompt a new competency record.
        $pathway2 = $competency_generator->create_test_pathway($competency);
        $pathway2->set_test_aggregate_current_value($scale_value2);

        (new test_pathway_evaluator($pathway2, $pw_user_source))->aggregate(time());
        $achievement2 = pathway_achievement::get_current($pathway2, $user->id);

        $aggregation_method = $this->create_aggregation_method_achieved_by([$achievement2]);
        $aggregator->set_aggregation_instance($aggregation_method);

        $aggregator->aggregate();

        $comp_records = $DB->get_records('totara_competency_achievement');
        $this->assertCount(2, $comp_records);
        $reloaded_first_comp_record = $DB->get_record('totara_competency_achievement', ['id' => $first_comp_record->id]);

        $this->assertEquals($first_comp_record->scale_value_id, $reloaded_first_comp_record->scale_value_id);
        $this->assertEquals(competency_achievement::SUPERSEDED, $reloaded_first_comp_record->status);

        $second_comp_record = $DB->get_record('totara_competency_achievement',
            ['status' => competency_achievement::ACTIVE_ASSIGNMENT]
        );
        $this->assertEquals($scale_value2->id, $second_comp_record->scale_value_id);


        // Now we're going to repeat the above one more time. This is to make sure that we're also not updating
        // superseded records that were created prior to aggregation.
        $pathway3 = $competency_generator->create_test_pathway($competency);
        $pathway3->set_test_aggregate_current_value($scale_value3);

        (new test_pathway_evaluator($pathway3, $pw_user_source))->aggregate(time());
        $achievement3 = pathway_achievement::get_current($pathway3, $user->id);

        $aggregation_method = $this->create_aggregation_method_achieved_by([$achievement3]);
        $aggregator->set_aggregation_instance($aggregation_method);

        $aggregator->aggregate();

        $comp_records = $DB->get_records('totara_competency_achievement');
        $this->assertCount(3, $comp_records);
        $reloaded_first_comp_record = $DB->get_record('totara_competency_achievement', ['id' => $first_comp_record->id]);

        // It's the same checks as above. Checking the first comp record now against what it was originally as well as it's
        // current status.
        $this->assertEquals($first_comp_record->scale_value_id, $reloaded_first_comp_record->scale_value_id);
        $this->assertEquals(competency_achievement::SUPERSEDED, $reloaded_first_comp_record->status);

        $third_comp_record = $DB->get_record('totara_competency_achievement',
            ['status' => competency_achievement::ACTIVE_ASSIGNMENT]
        );
        $this->assertEquals($scale_value3->id, $third_comp_record->scale_value_id);
    }
}
