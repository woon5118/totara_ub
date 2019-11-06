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
 * @package totara_competency
 */

use totara_competency\entities\competency;
use totara_competency\task\competency_achievement_aggregation;
use totara_competency\achievement_configuration;
use totara_competency\pathway;
use totara_competency\entities\scale_value;
use totara_competency\entities\pathway_achievement;
use totara_competency\pathway_aggregator;

/**
 * Class task_competency_achievement_aggregation_testcase
 *
 * Tests the the behaviour of the totara_competency\task\competency_achievement_aggregation class.
 *
 * While many of the tests do test for the work done by the totara_competency\aggregator class, detailed testing
 * of the aggregator itself should be done in a testcase dedicated to that.
 *
 * Including the behaviour of the aggregator in the tests does however ensure correct behaviour of the cron task
 * in certain scenarios, such as when it uses the last_aggregated field.
 */
class task_competency_achievement_aggregation_testcase extends advanced_testcase {

    private function generate_competency(): competency {
        /** @var totara_hierarchy_generator $hierarchy_generator */
        $hierarchy_generator = $this->getDataGenerator()->get_plugin_generator('totara_hierarchy');

        $scale = $hierarchy_generator->create_scale('comp');
        $compfw = $hierarchy_generator->create_comp_frame(['scale' => $scale->id]);
        $comp = $hierarchy_generator->create_comp(['frameworkid' => $compfw->id]);

        return new competency($comp);
    }

    private function generate_active_expanded_user_assignments($competency, $users) {
        global $DB;

        /** @var totara_competency_assignment_generator $assignment_generator */
        $assignment_generator = $this->getDataGenerator()->get_plugin_generator('totara_competency')->assignment_generator();

        $assignment_ids = [];
        foreach ($users as $user) {
            $assignment = $assignment_generator->create_user_assignment($competency->id, $user->id);
            $assignment_ids[] = $assignment->id;
        }

        $model = new \tassign_competency\models\assignment_actions();
        $model->activate($assignment_ids);

        $expand_task = new \tassign_competency\expand_task($DB);
        $expand_task->expand_all();

        return $assignment_ids;
    }

    private function generate_mock_pathway($competency, $scale_value): pathway {

        /** @var totara_competency_generator $competency_generator */
        $competency_generator = $this->getDataGenerator()->get_plugin_generator('totara_competency');
        /** @var test_pathway $pathway */
        $pathway = $competency_generator->create_test_pathway();
        $pathway->set_test_aggregate_current_value($scale_value);

        $pathway->set_competency($competency);
        $pathway->set_sortorder(1);
        $pathway->save();

        return $pathway;
    }

    private function create_competency_with_pathway() {
        $competency = $this->generate_competency();
        $scale_value = $competency->scale->sorted_values_high_to_low->first();

        $pathway = $this->generate_mock_pathway($competency, $scale_value);
        $config = new achievement_configuration($competency);
        $config->set_aggregation_type('first')->save_aggregation();

        return [$competency, $pathway, $scale_value];
    }

    public function test_execute_with_no_data() {
        $task = new competency_achievement_aggregation();
        $task->execute();
    }

    /**
     * Single active assignment
     * Single active pathway
     * Single active pathway achievement
     */
    public function test_aggregation_of_single_competency_and_user() {
        global $DB;

        $user = $this->getDataGenerator()->create_user();
        [$competency, $pathway, $scale_value] = $this->create_competency_with_pathway();

        $this->generate_active_expanded_user_assignments($competency, [$user]);
        (new pathway_aggregator($pathway))->aggregate([$user->id]);

        // Let's be clear on the state before running the task.
        $this->assertEquals(1, $DB->count_records(
            'totara_competency_pathway_achievement',
            ['user_id' => $user->id, 'pathway_id' => $pathway->get_id()]
        ));
        $this->assertEquals(0, $DB->count_records('totara_competency_achievement'));
        $this->assertEquals(0, $DB->count_records('totara_competency_achievement_via'));

        $task = new competency_achievement_aggregation();

        // The method to get assigned users that need updating is the main thing to test here.
        $user_ids = $task->get_assigned_users_with_updated_achievements($competency);
        $this->assertCount(1, $user_ids);
        $user_id = array_pop($user_ids);
        $this->assertEquals($user->id, $user_id);

        // Not necessary to check this sort of thing for all tests. The aggregator tests are what are really
        // responsible for testing creation and updating of comp_records.
        $task->execute();

        // A comp_record should have been generated based on the user's pathway achievement.
        $comp_records = $DB->get_records('totara_competency_achievement');
        $this->assertCount(1, $comp_records);
        $comp_record = reset($comp_records);
        $this->assertEquals($scale_value->id, $comp_record->scale_value_id);
    }

    public function test_last_aggregated_field() {
        global $DB;

        $user = $this->getDataGenerator()->create_user();
        [$competency, $pathway] = $this->create_competency_with_pathway();

        $this->generate_active_expanded_user_assignments($competency, [$user]);
        (new pathway_aggregator($pathway))->aggregate([$user->id]);

        $task = new competency_achievement_aggregation();

        $user_ids = $task->get_assigned_users_with_updated_achievements($competency);
        $this->assertCount(1, $user_ids);

        // Up until this point, this test will have been similar to test_aggregation_of_single_competency_and_user().
        $this->setCurrentTimeStart();

        $task->execute();

        $comp_records1 = $DB->get_records('totara_competency_achievement');
        $this->assertCount(1, $comp_records1);
        $comp_record1 = reset($comp_records1);

        // We've recorded the time we aggregated and we sent an event because there was a new value.
        $this->assertTimeCurrent($comp_record1->last_aggregated);

        $this->waitForSecond();

        $user_ids = $task->get_assigned_users_with_updated_achievements($competency);
        $this->assertCount(1, $user_ids);

        $task->execute();

        $comp_records2 = $DB->get_records('totara_competency_achievement');
        $this->assertCount(1, $comp_records2);
        $comp_record2 = reset($comp_records2);

        // We aggregated again. This is because the pathway aggregation time was equal to the comp_record aggregation time.
        // We catch those to do again which means we might repeat aggregation sometimes in favour of not missing any
        // pathway achievements that are updated at the time of comp record aggregation.
        $this->assertTrue($comp_record2->last_aggregated > $comp_record1->last_aggregated);

        // Todo: stop wasting time by shifting time back instead perhaps. Otherwise, we keep one test like this because accuracy of the test is also important.
        $this->waitForSecond();

        $user_ids = $task->get_assigned_users_with_updated_achievements($competency);
        $this->assertCount(0, $user_ids);

        $task->execute();

        $comp_records3 = $DB->get_records('totara_competency_achievement');
        $this->assertCount(1, $comp_records3);
        $comp_record3 = reset($comp_records3);

        // This time we didn't have to aggregate again because the pathway time was before the comp_record time.
        $this->assertEquals($comp_record3->last_aggregated, $comp_record2->last_aggregated);
    }

    /**
     * Active assignment
     * Active pathway
     * Archived pathway achievement
     */
    public function test_new_active_assignment_archived_pathway_achievement() {
        global $DB;

        $user = $this->getDataGenerator()->create_user();
        [$competency, $pathway] = $this->create_competency_with_pathway();

        $this->generate_active_expanded_user_assignments($competency, [$user]);
        (new pathway_aggregator($pathway))->aggregate([$user->id]);

        pathway_achievement::get_current($pathway, $user->id)->archive();

        $this->assertEquals(0, $DB->count_records('totara_competency_achievement'));
        $this->assertEquals(0, $DB->count_records('totara_competency_achievement_via'));

        $task = new competency_achievement_aggregation();
        $user_ids = $task->get_assigned_users_with_updated_achievements($competency);
        $this->assertCount(1, $user_ids);
    }

    /**
     * Archived assignment
     * Active pathway
     * Active pathway achievement
     */
    public function test_new_archived_assignment_active_pathway_achievement() {
        global $DB;

        $user = $this->getDataGenerator()->create_user();
        [$competency, $pathway] = $this->create_competency_with_pathway();

        $assignment_ids = $this->generate_active_expanded_user_assignments($competency, [$user]);
        (new pathway_aggregator($pathway))->aggregate([$user->id]);

        (new \tassign_competency\models\assignment_actions())->archive($assignment_ids);
        (new \tassign_competency\expand_task($DB))->expand_all();

        $this->assertEquals(0, $DB->count_records('totara_competency_achievement'));
        $this->assertEquals(0, $DB->count_records('totara_competency_achievement_via'));

        $task = new competency_achievement_aggregation();

        $task->execute();
        // We only aggregate for active assignments.
        $user_ids = $task->get_assigned_users_with_updated_achievements($competency);
        $this->assertCount(0, $user_ids);
    }

    /**
     * Begin with active assignment, pathway and achievement.
     * Then set the achievement to be archived.
     */
    public function test_updated_active_assignment_archived_pathway_achievement() {
        global $DB;

        $user = $this->getDataGenerator()->create_user();
        [$competency, $pathway, $scale_value] = $this->create_competency_with_pathway();

        $this->generate_active_expanded_user_assignments($competency, [$user]);
        (new pathway_aggregator($pathway))->aggregate([$user->id]);

        $task = new competency_achievement_aggregation();

        $user_ids = $task->get_assigned_users_with_updated_achievements($competency);
        $this->assertCount(1, $user_ids);

        $task->execute();

        // A comp_record should have been generated based on the user's pathway achievement.
        $comp_record = $DB->get_record('totara_competency_achievement', []);

        $now = time();

        pathway_achievement::get_current($pathway, $user->id)->archive();

        // Work around the last_aggregated check so that this is updated if it needs to be.
        // Todo: Edge case here where if achievement is actually updated as record is being aggregated, it will be missed.
        $DB->set_field('totara_competency_achievement', 'last_aggregated', $now - 1, ['id' => $comp_record->id]);

        $user_ids = $task->get_assigned_users_with_updated_achievements($competency);
        $this->assertCount(1, $user_ids);
    }

    /**
     * Begin with active assignment, pathway and achievement.
     * Then set the assignment to be archived.
     * Then archive the achievement and we should not see any change.
     */
    public function test_updated_archived_assignment_active_pathway_achievement() {
        global $DB;

        $user = $this->getDataGenerator()->create_user();
        [$competency, $pathway, $scale_value] = $this->create_competency_with_pathway();

        $assignment_ids = $this->generate_active_expanded_user_assignments($competency, [$user]);
        (new pathway_aggregator($pathway))->aggregate([$user->id]);

        $task = new competency_achievement_aggregation();

        $user_ids = $task->get_assigned_users_with_updated_achievements($competency);
        $this->assertCount(1, $user_ids);

        $task->execute();

        // Now archive the assignment.
        (new \tassign_competency\models\assignment_actions())->archive($assignment_ids);
        (new \tassign_competency\expand_task($DB))->expand_all();

        $user_ids = $task->get_assigned_users_with_updated_achievements($competency);
        $this->assertCount(0, $user_ids);

        $task->execute();

        // Nothing should have changed.
        $comp_record = $DB->get_record('totara_competency_achievement', []);

        $now = time();
        // Now archive the achievement.
        pathway_achievement::get_current($pathway, $user->id)->archive($now);

        // Work around the last_aggregated check so that this is updated if it needs to be.
        // Todo: Edge case here where if achievement is actually updated as record is being aggregated, it will be missed.
        $DB->set_field('totara_competency_achievement', 'last_aggregated', $now - 1, ['id' => $comp_record->id]);

        $user_ids = $task->get_assigned_users_with_updated_achievements($competency);
        $this->assertCount(0, $user_ids);
    }

    /**
     * First, an active achievement for active pathway and assignment leads to a comp_record value.
     * Then, archive the pathway itself.
     * That achievement should no longer contribute to the value.
     */
    public function test_archived_pathway_after_record_created() {

        $this->markTestSkipped(); // Some more to implement before this will pass.

        $user = $this->getDataGenerator()->create_user();

        /**
         * @var competency $competency
         * @var pathway $pathway
         * @var scale_value $scale_value
         */
        [$competency, $pathway, $scale_value] = $this->create_competency_with_pathway();

        $this->generate_active_expanded_user_assignments($competency, [$user]);
        (new pathway_aggregator($pathway))->aggregate([$user->id]);

        $task = new competency_achievement_aggregation();

        $user_ids = $task->get_assigned_users_with_updated_achievements($competency);
        $this->assertCount(1, $user_ids);

        $task->execute();

        // Now archive the pathway.
        $pathway->delete();

        $user_ids = $task->get_assigned_users_with_updated_achievements($competency);
        $this->assertCount(0, $user_ids);
    }

    /**
     * First, an active achievement for active pathway and assignment leads to a comp_record value.
     * Then, delete the pathway.
     * That achievement should no longer contribute to the value.
     */
    public function test_deleted_pathway_after_record_created() {
        $this->markTestIncomplete();
    }

    /**
     * First, create two pathways that generate different scale values.
     * Aggregate so that the user has a comp_record based off one of them.
     * Update the aggregation method such that the user should now have the other value in their comp_record.
     * Run aggregation and check this occurs.
     */
    public function test_updated_aggregation_type_after_record_created() {
        $this->markTestIncomplete();
    }
}