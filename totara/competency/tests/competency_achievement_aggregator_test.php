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

use totara_competency\entities\competency;
use totara_competency\achievement_configuration;
use totara_competency\competency_achievement_aggregator;
use totara_competency\entities\pathway_achievement;
use totara_competency\pathway_aggregation;
use totara_competency\entities\scale_value;
use totara_competency\pathway_aggregator;
use totara_competency\entities\competency_achievement;

/**
 * Class totara_competency_achievement_aggregator_testcase
 *
 * Tests behaviour of the competency_achievement_aggregator class.
 */
class totara_competency_achievement_aggregator_testcase extends advanced_testcase {

    /**
     * @param pathway_achievement[] $pathway_achievements
     * @return pathway_aggregation
     */
    private function create_aggregation_method_achieved_by($pathway_achievements): pathway_aggregation {
        /** @var totara_competency_generator $competency_generator */
        $competency_generator = $this->getDataGenerator()->get_plugin_generator('totara_competency');

        $test_aggregation = $competency_generator->create_test_aggregation();
        $achieved_value_ids = [];
        $achieved_vias = [];
        foreach ($pathway_achievements as $pathway_achievement) {
            $user_id = $pathway_achievement->user_id;
            $achieved_value_ids[$user_id] = $pathway_achievement->scale_value_id;
            if (!isset($achieved_vias[$user_id])) {
                $achieved_vias[$user_id] = [];
            }
            $achieved_vias[$user_id][] = $pathway_achievement;
        }
        $test_aggregation->set_test_aggregated_data($achieved_value_ids, $achieved_vias);

        return $test_aggregation;
    }

    private function generate_active_expanded_user_assignments($competency, $users, $assignments_per_user = 1) {
        global $DB;

        /** @var totara_competency_assignment_generator $assignment_generator */
        $assignment_generator = $this->getDataGenerator()->get_plugin_generator('totara_competency')->assignment_generator();

        $assignment_ids = [];
        foreach ($users as $user) {
            for ($i = 0; $i < $assignments_per_user; $i++) {
                $assignment = $assignment_generator->create_user_assignment($competency->id, $user->id);
                $assignment_ids[] = $assignment->id;
            }
        }

        $model = new \totara_competency\models\assignment_actions();
        $model->activate($assignment_ids);

        $expand_task = new \totara_competency\expand_task($DB);
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
        $aggregator = new competency_achievement_aggregator($achievement_configuration);

        $event_sink = $this->redirectEvents();
        // We're mainly testing that aggregate completes without an exception.
        $aggregator->aggregate([]);
        $events = $event_sink->get_events();

        $this->assertCount(0, $events);
    }

    public function test_with_one_user_requiring_completion() {
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
        $this->generate_active_expanded_user_assignments($competency, [$user]);

        (new pathway_aggregator($pathway))->aggregate([$user->id]);

        $aggregator = new competency_achievement_aggregator($achievement_configuration);

        $achievement = pathway_achievement::get_current($pathway, $user->id);
        $aggregation_method = $this->create_aggregation_method_achieved_by([$achievement]);
        $aggregator->set_aggregation_instance($aggregation_method);

        $this->assertEquals(0, $DB->count_records('totara_competency_achievement'));

        $event_sink = $this->redirectEvents();
        $aggregator->aggregate([$user->id]);
        $events = $event_sink->get_events();

        $comp_records = $DB->get_records('totara_competency_achievement');
        $this->assertCount(1, $comp_records);
        $comp_record = reset($comp_records);
        $this->assertEquals($scale_value->id, $comp_record->scale_value_id);

        $via_records = $DB->get_records('totara_competency_achievement_via');
        $this->assertCount(1, $via_records);
        $via_record = reset($via_records);
        $this->assertEquals($achievement->id, $via_record->pathway_achievement_id);

        $event = reset($events);
        $this->assertInstanceOf(\totara_competency\event\competency_achievement_updated::class, $event);
        $event_sink->close();
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

        (new pathway_aggregator($pathway1))->aggregate([$user->id]);
        (new pathway_aggregator($pathway2))->aggregate([$user->id]);

        $achievement1 = pathway_achievement::get_current($pathway1, $user->id);
        $achievement2 = pathway_achievement::get_current($pathway2, $user->id);

        $aggregator = new competency_achievement_aggregator($achievement_configuration);

        $aggregation_method = $this->create_aggregation_method_achieved_by([$achievement1, $achievement2]);
        $aggregator->set_aggregation_instance($aggregation_method);

        $this->assertEquals(0, $DB->count_records('totara_competency_achievement'));

        $event_sink = $this->redirectEvents();
        $aggregator->aggregate([$user->id]);
        $events = $event_sink->get_events();

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

        $event = reset($events);
        $this->assertInstanceOf(\totara_competency\event\competency_achievement_updated::class, $event);
        $event_sink->close();
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

        (new pathway_aggregator($pathway1))->aggregate([$user->id]);
        (new pathway_aggregator($pathway2))->aggregate([$user->id]);

        $achievement1 = pathway_achievement::get_current($pathway1, $user->id);
        $achievement2 = pathway_achievement::get_current($pathway2, $user->id);

        $aggregator = new competency_achievement_aggregator($achievement_configuration);

        $aggregation_method = $this->create_aggregation_method_achieved_by([$achievement1, $achievement2]);
        $aggregator->set_aggregation_instance($aggregation_method);

        $this->assertEquals(0, $DB->count_records('totara_competency_achievement'));

        $aggregator->aggregate([$user->id]);

        // This point is tested more in previous tests.
        $this->assertEquals(1, $DB->count_records('totara_competency_achievement', ['status' => 0]));
        $this->assertEquals(2, $DB->count_records('totara_competency_achievement_via'));

        // We'll replace the aggregation instance with one that will just say the user achieved their score via #2.
        $aggregation_method = $this->create_aggregation_method_achieved_by([$achievement2]);
        $aggregator->set_aggregation_instance($aggregation_method);

        $event_sink = $this->redirectEvents();
        $aggregator->aggregate([$user->id]);
        $events = $event_sink->get_events();

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

        // Events aren't sent if the value isn't updated.
        $this->assertCount(0, $events);
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

        (new pathway_aggregator($pathway1))->aggregate([$user->id]);
        (new pathway_aggregator($pathway2))->aggregate([$user->id]);

        $achievement1 = pathway_achievement::get_current($pathway1, $user->id);
        $achievement2 = pathway_achievement::get_current($pathway2, $user->id);

        $aggregator = new competency_achievement_aggregator($achievement_configuration);

        $aggregation_method = $this->create_aggregation_method_achieved_by([$achievement1, $achievement2]);
        $aggregator->set_aggregation_instance($aggregation_method);

        $this->assertEquals(0, $DB->count_records('totara_competency_achievement'));

        $aggregator->aggregate([$user->id]);

        // This point is tested more in previous tests.
        $this->assertEquals(1, $DB->count_records('totara_competency_achievement'));
        $this->assertEquals(2, $DB->count_records('totara_competency_achievement_via'));

        // We'll replace the aggregation instance with one that will just say the user achieved their score via #2.
        $aggregation_method = $this->create_aggregation_method_achieved_by([$achievement2]);
        $aggregator->set_aggregation_instance($aggregation_method);

        $aggregator->aggregate([$user->id]);

        // Change what value the pathways return. We'll then need to update the achievements used.
        $pathway1->set_test_aggregate_current_value(null);
        $pathway2->set_test_aggregate_current_value(null);

        (new pathway_aggregator($pathway1))->aggregate([$user->id]);
        (new pathway_aggregator($pathway2))->aggregate([$user->id]);

        $achievement1 = pathway_achievement::get_current($pathway1, $user->id);
        $achievement2 = pathway_achievement::get_current($pathway2, $user->id);

        $aggregation_method = $this->create_aggregation_method_achieved_by([$achievement1, $achievement2]);
        $aggregator->set_aggregation_instance($aggregation_method);

        $event_sink = $this->redirectEvents();
        $aggregator->aggregate([$user->id]);
        $events = $event_sink->get_events();

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
        // 2. Aggregation methods shouldn't return all pathways with a null value. This is how it currently works. There are logical issues with 1 such as when null achievements are a placeholder
        // And if the aggregation method does return a null value for it's 'via' record, should the competency_achievement_aggregator save it or not.
        $this->assertEquals(4, $DB->count_records('totara_competency_achievement_via'));

        // The value changed, so an event was sent.
        $event = reset($events);
        $this->assertInstanceOf(\totara_competency\event\competency_achievement_updated::class, $event);
        $event_sink->close();
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

        $pathway1 = $competency_generator->create_test_pathway($competency);
        $pathway1->set_test_aggregate_current_value($scale_value1);

        $achievement_configuration = new achievement_configuration($competency);
        $achievement_configuration->set_aggregation_type('test_aggregation');

        $user = $this->getDataGenerator()->create_user();
        $this->generate_active_expanded_user_assignments($competency, [$user]);

        (new pathway_aggregator($pathway1))->aggregate([$user->id]);
        $achievement1 = pathway_achievement::get_current($pathway1, $user->id);

        $aggregator = new competency_achievement_aggregator($achievement_configuration);

        $aggregation_method = $this->create_aggregation_method_achieved_by([$achievement1]);
        $aggregator->set_aggregation_instance($aggregation_method);

        $this->assertEquals(0, $DB->count_records('totara_competency_achievement'));

        $aggregator->aggregate([$user->id]);

        // Should all be about scale value and achievement #1.
        $comp_records = $DB->get_records('totara_competency_achievement');
        $this->assertCount(1, $comp_records);
        $comp_record = reset($comp_records);
        $this->assertEquals($scale_value1->id, $comp_record->scale_value_id);

        $via_records = $DB->get_records('totara_competency_achievement_via');
        $this->assertCount(1, $via_records);
        $via_record = array_pop($via_records);
        $this->assertEquals($via_record->pathway_achievement_id, $achievement1->id);

        $this->assertNotEquals($scale_value1->id, $scale_value2->id);

        $pathway2 = $competency_generator->create_test_pathway($competency);
        $pathway2->set_test_aggregate_current_value($scale_value2);

        (new pathway_aggregator($pathway2))->aggregate([$user->id]);
        $achievement2 = pathway_achievement::get_current($pathway2, $user->id);

        $aggregation_method = $this->create_aggregation_method_achieved_by([$achievement2]);
        $aggregator->set_aggregation_instance($aggregation_method);

        $event_sink = $this->redirectEvents();
        $aggregator->aggregate([$user->id]);
        $events = $event_sink->get_events();

        // Order by newest at they back so they can be popped off in that order.
        $comp_records = $DB->get_records('totara_competency_achievement', [], 'time_created ASC, id ASC');
        $this->assertCount(2, $comp_records);
        $comp_record2 = array_pop($comp_records);
        $this->assertEquals($scale_value2->id, $comp_record2->scale_value_id);
        $comp_record1 = array_pop($comp_records);
        $this->assertEquals($scale_value1->id, $comp_record1->scale_value_id);

        $via_records = $DB->get_records('totara_competency_achievement_via', [], 'id ASC');
        $this->assertCount(2, $via_records);
        $via_record = array_pop($via_records);
        $this->assertEquals($via_record->comp_achievement_id, $comp_record2->id);
        $via_record = array_pop($via_records);
        $this->assertEquals($via_record->comp_achievement_id, $comp_record1->id);

        // The value changed, so an event was sent.
        $event = reset($events);
        $this->assertInstanceOf(\totara_competency\event\competency_achievement_updated::class, $event);
        $event_sink->close();
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
        (new pathway_aggregator($pathway))->aggregate([$user->id]);
        $assignmentids = $this->generate_active_expanded_user_assignments($competency, [$user], 2);

        $aggregator = new competency_achievement_aggregator($achievement_configuration);

        $achievement = pathway_achievement::get_current($pathway, $user->id);
        $aggregation_method = $this->create_aggregation_method_achieved_by([$achievement]);
        $aggregator->set_aggregation_instance($aggregation_method);

        $this->assertEquals(0, $DB->count_records('totara_competency_achievement'));

        $event_sink = $this->redirectEvents();
        $aggregator->aggregate([$user->id]);
        $events = $event_sink->get_events();

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

        $event = reset($events);
        $this->assertInstanceOf(\totara_competency\event\competency_achievement_updated::class, $event);

        // Follow-on scenario. One of the assignments is archived. The status on just that comp_record should reflect that.

        $disable_assignment_id = array_pop($assignmentids);

        $model = new \totara_competency\models\assignment_actions();
        $model->archive([$disable_assignment_id]);
        $expand_task = new \totara_competency\expand_task($DB);
        $expand_task->expand_all();

        $aggregator = new competency_achievement_aggregator($achievement_configuration);
        $aggregation_method = $this->create_aggregation_method_achieved_by([$achievement]);
        $aggregator->set_aggregation_instance($aggregation_method);
        $event_sink->clear();
        $aggregator->aggregate([$user->id]);
        $events = $event_sink->get_events();

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

        $this->assertCount(0, $events);
        $event_sink->close();
    }

    public function test_change_in_minimum_proficiency() {
        // When the minimum proficient value of a scale changes. We'll need to see if that means
        // any comp records with active assignments should become superseded and replaced with a new one
        // (which is just the case if the scale value they had has gone from proficient to not or vice versa).

        // We may want to wait until TL-20274 is in before doing this as it might be modified following that anyway.
        $this->markTestIncomplete();
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

        (new pathway_aggregator($pathway))->aggregate([$user->id]);

        $aggregator = new competency_achievement_aggregator($achievement_configuration);

        $achievement = pathway_achievement::get_current($pathway, $user->id);
        $aggregation_method = $this->create_aggregation_method_achieved_by([$achievement]);
        $aggregator->set_aggregation_instance($aggregation_method);

        $this->assertEquals(0, $DB->count_records('totara_competency_achievement'));

        $event_sink = $this->redirectEvents();
        $aggregator->aggregate([$user->id]);
        $events = $event_sink->get_events();

        $comp_records = $DB->get_records('totara_competency_achievement');
        $this->assertCount(1, $comp_records);
        $comp_record_while_assigned = reset($comp_records);
        $this->assertEquals($scale_value1->id, $comp_record_while_assigned->scale_value_id);

        $via_records = $DB->get_records('totara_competency_achievement_via');
        $this->assertCount(1, $via_records);
        $via_record = reset($via_records);
        $this->assertEquals($achievement->id, $via_record->pathway_achievement_id);

        $event = reset($events);
        $this->assertInstanceOf(\totara_competency\event\competency_achievement_updated::class, $event);
        $event_sink->close();

        $disable_assignment_id = array_pop($assignment_ids);

        $model = new \totara_competency\models\assignment_actions();
        $model->archive([$disable_assignment_id]);
        $expand_task = new \totara_competency\expand_task($DB);
        $expand_task->expand_all();

        // Add a new pathway achievement, which would prompt a new competency record if it were possible.
        $pathway2 = $competency_generator->create_test_pathway($competency);
        $pathway2->set_test_aggregate_current_value($scale_value2);

        (new pathway_aggregator($pathway2))->aggregate([$user->id]);
        $achievement2 = pathway_achievement::get_current($pathway2, $user->id);

        $aggregation_method = $this->create_aggregation_method_achieved_by([$achievement2]);
        $aggregator->set_aggregation_instance($aggregation_method);

        $event_sink = $this->redirectEvents();
        $aggregator->aggregate([$user->id]);
        $events = $event_sink->get_events();

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

        (new pathway_aggregator($pathway))->aggregate([$user->id]);

        $aggregator = new competency_achievement_aggregator($achievement_configuration);

        $achievement = pathway_achievement::get_current($pathway, $user->id);
        $aggregation_method = $this->create_aggregation_method_achieved_by([$achievement]);
        $aggregator->set_aggregation_instance($aggregation_method);

        $this->assertEquals(0, $DB->count_records('totara_competency_achievement'));

        $aggregator->aggregate([$user->id]);

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

        (new pathway_aggregator($pathway2))->aggregate([$user->id]);
        $achievement2 = pathway_achievement::get_current($pathway2, $user->id);

        $aggregation_method = $this->create_aggregation_method_achieved_by([$achievement2]);
        $aggregator->set_aggregation_instance($aggregation_method);

        $aggregator->aggregate([$user->id]);

        $comp_records = $DB->get_records('totara_competency_achievement');
        $this->assertCount(2, $comp_records);
        $reloaded_first_comp_record = $DB->get_record('totara_competency_achievement', ['id' => $first_comp_record->id]);

        $this->assertEquals($first_comp_record->scale_value_id, $reloaded_first_comp_record->scale_value_id);
        $this->assertEquals(competency_achievement::SUPERSEDED, $reloaded_first_comp_record->status);

        $second_comp_record = $DB->get_record('totara_competency_achievement', ['status' => competency_achievement::ACTIVE_ASSIGNMENT]);
        $this->assertEquals($scale_value2->id, $second_comp_record->scale_value_id);


        // Now we're going to repeat the above one more time. This is to make sure that we're also not updating
        // superseded records that were created prior to aggregation.
        $pathway3 = $competency_generator->create_test_pathway($competency);
        $pathway3->set_test_aggregate_current_value($scale_value3);

        (new pathway_aggregator($pathway3))->aggregate([$user->id]);
        $achievement3 = pathway_achievement::get_current($pathway3, $user->id);

        $aggregation_method = $this->create_aggregation_method_achieved_by([$achievement3]);
        $aggregator->set_aggregation_instance($aggregation_method);

        $aggregator->aggregate([$user->id]);

        $comp_records = $DB->get_records('totara_competency_achievement');
        $this->assertCount(3, $comp_records);
        $reloaded_first_comp_record = $DB->get_record('totara_competency_achievement', ['id' => $first_comp_record->id]);

        // It's the same checks as above. Checking the first comp record now against what it was originally as well as it's
        // current status.
        $this->assertEquals($first_comp_record->scale_value_id, $reloaded_first_comp_record->scale_value_id);
        $this->assertEquals(competency_achievement::SUPERSEDED, $reloaded_first_comp_record->status);

        $third_comp_record = $DB->get_record('totara_competency_achievement', ['status' => competency_achievement::ACTIVE_ASSIGNMENT]);
        $this->assertEquals($scale_value3->id, $third_comp_record->scale_value_id);
    }
}
