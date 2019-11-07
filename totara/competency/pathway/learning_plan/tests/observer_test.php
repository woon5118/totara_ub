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
 * @package pathway_learning_plan
 */

use totara_plan\event\competency_value_set;
use totara_competency\entities\scale_value;
use pathway_learning_plan\learning_plan;
use totara_competency\entities\competency;
use totara_competency\entities\competency_achievement;
use totara_competency\entities\pathway_achievement;
use totara_competency\competency_achievement_aggregator;
use totara_competency\achievement_configuration;
use totara_competency\models\assignment_actions;
use tassign_competency\expand_task;

class pathway_learning_plan_observer_testcase extends advanced_testcase {

    public function test_event_for_competency_with_lp_pathway() {
        global $DB;

        $user = $this->getDataGenerator()->create_user();

        /** @var totara_hierarchy_generator $totara_hierarchy_generator */
        $totara_hierarchy_generator = $this->getDataGenerator()->get_plugin_generator('totara_hierarchy');

        $scale = $totara_hierarchy_generator->create_scale(
            'comp',
            [],
            [
                ['name' => 'Great', 'proficient' => 1, 'sortorder' => 1, 'default' => 0],
                ['name' => 'Good', 'proficient' => 0, 'sortorder' => 2, 'default' => 1],
                ['name' => 'Bad', 'proficient' => 0, 'sortorder' => 3, 'default' => 0]
            ]
        );

        $compfw = $totara_hierarchy_generator->create_comp_frame(['scale' => $scale->id]);
        $comp = $totara_hierarchy_generator->create_comp(['frameworkid' => $compfw->id]);
        $competency = new competency($comp);

        $lp_pathway = new learning_plan();
        $lp_pathway->set_competency($competency);
        $lp_pathway->save();

        $great = scale_value::repository()->where('name', '=', 'Great')->one();
        $good = scale_value::repository()->where('name', '=', 'Good')->one();

        /** @var tassign_competency_generator $assignment_generator */
        $assignment_generator = $this->getDataGenerator()->get_plugin_generator('totara_competency')->assignment_generator();
        $assignment = $assignment_generator->create_user_assignment($comp->id, $user->id);
        (new assignment_actions())->activate([$assignment->id]);
        (new expand_task($DB))->expand_all();

        $lp_value_record = new stdClass();
        $lp_value_record->competency_id = $comp->id;
        $lp_value_record->user_id = $user->id;
        $lp_value_record->scale_value_id = $great->id;
        $lp_value_record->date_assigned = time();
        $lp_value_record->id = $DB->insert_record('dp_plan_competency_value', $lp_value_record);

        $count = competency_achievement::repository()
            ->where('user_id', '=', $user->id)
            ->where('comp_id', '=', $comp->id)
            ->count();
        $this->assertEquals(0, $count);

        competency_value_set::create_from_record($lp_value_record)->trigger();

        $pathway_achievement = pathway_achievement::get_current($lp_pathway, $user->id);
        $this->assertEquals($great->id, $pathway_achievement->scale_value_id);

        (new competency_achievement_aggregator(new achievement_configuration($competency)))->aggregate([$user->id]);

        $achievements = competency_achievement::repository()
            ->where('user_id', '=', $user->id)
            ->where('comp_id', '=', $comp->id)
            ->get();
        $this->assertCount(1, $achievements);
        $this->assertEquals($great->id, $achievements->first()->scale_value_id);

        // Part 2 of this test (as we have data set up already): account for pathways being archived.

        $lp_pathway->delete();

        $lp_value_record->scale_value_id = $good->id;
        $DB->update_record('dp_plan_competency_value', $lp_value_record);

        competency_value_set::create_from_record($lp_value_record)->trigger();

        // The latest pathway achievement should still equal great. Because the pathway was archived.
        $pathway_achievement = pathway_achievement::get_current($lp_pathway, $user->id);
        $this->assertEquals($great->id, $pathway_achievement->scale_value_id);

        // Aggregate. There are no active pathways now. So we are left with no value.
        (new competency_achievement_aggregator(new achievement_configuration($competency)))->aggregate([$user->id]);
        $achievements = competency_achievement::repository()
            ->where('user_id', '=', $user->id)
            ->where('comp_id', '=', $comp->id)
            ->where('status', '=', competency_achievement::ACTIVE_ASSIGNMENT)
            ->get();
        $this->assertCount(1, $achievements);
        $this->assertNull($achievements->first()->scale_value_id);

        // Part 3: Now do the above again, but there will be a new active pathway instead.

        $new_lp_pathway = new learning_plan();
        $new_lp_pathway->set_competency($competency);
        $new_lp_pathway->save();

        // Trigger the event for the observer to catch.
        competency_value_set::create_from_record($lp_value_record)->trigger();

        // The latest pathway achievement should now equal good.
        $pathway_achievement = pathway_achievement::get_current($new_lp_pathway, $user->id);
        $this->assertEquals($good->id, $pathway_achievement->scale_value_id);

        (new competency_achievement_aggregator(new achievement_configuration($competency)))->aggregate([$user->id]);
        $achievements = competency_achievement::repository()
            ->where('user_id', '=', $user->id)
            ->where('comp_id', '=', $comp->id)
            ->where('status', '=', competency_achievement::ACTIVE_ASSIGNMENT)
            ->get();
        $this->assertCount(1, $achievements);
        $this->assertEquals($good->id, $achievements->first()->scale_value_id);
    }

    public function test_event_for_competency_without_lp_pathway() {
        global $DB;

        $user = $this->getDataGenerator()->create_user();

        /** @var totara_hierarchy_generator $totara_hierarchy_generator */
        $totara_hierarchy_generator = $this->getDataGenerator()->get_plugin_generator('totara_hierarchy');

        $scale = $totara_hierarchy_generator->create_scale(
            'comp',
            [],
            [
                ['name' => 'Great', 'proficient' => 1, 'sortorder' => 1, 'default' => 0],
                ['name' => 'Good', 'proficient' => 0, 'sortorder' => 2, 'default' => 1],
                ['name' => 'Bad', 'proficient' => 0, 'sortorder' => 3, 'default' => 0]
            ]
        );

        $compfw = $totara_hierarchy_generator->create_comp_frame(['scale' => $scale->id]);
        $comp = $totara_hierarchy_generator->create_comp(['frameworkid' => $compfw->id]);
        $competency = new competency($comp);

        // No learning plan pathway being set up for competency here.
        // Let's just make sure none is being added by default:
        $this->assertCount(0, $DB->get_records('totara_competency_pathway', ['path_type' => 'learning_plan']));

        $great = scale_value::repository()->where('name', '=', 'Great')->one();

        /** @var tassign_competency_generator $assignment_generator */
        $assignment_generator = $this->getDataGenerator()->get_plugin_generator('totara_competency')->assignment_generator();
        $assignment = $assignment_generator->create_user_assignment($comp->id, $user->id);
        (new assignment_actions())->activate([$assignment->id]);
        (new expand_task($DB))->expand_all();

        $record = new stdClass();
        $record->competency_id = $comp->id;
        $record->user_id = $user->id;
        $record->scale_value_id = $great->id;
        $record->date_assigned = time();
        $record->id = $DB->insert_record('dp_plan_competency_value', $record);

        $count = competency_achievement::repository()
            ->where('user_id', '=', $user->id)
            ->where('comp_id', '=', $comp->id)
            ->count();
        $this->assertEquals(0, $count);

        competency_value_set::create_from_record($record)->trigger();

        (new competency_achievement_aggregator(new achievement_configuration($competency)))->aggregate([$user->id]);

        $achievements = competency_achievement::repository()
            ->where('user_id', '=', $user->id)
            ->where('comp_id', '=', $comp->id)
            ->get();
        $this->assertCount(1, $achievements);

        // Has not achieved any scale value despite receiving a rating via a learning plan.
        $this->assertEquals(null, $achievements->first()->scale_value_id);
    }
}
