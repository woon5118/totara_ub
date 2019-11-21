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

use pathway_learning_plan\learning_plan;
use totara_competency\aggregation_users_table;
use totara_competency\entities\competency;
use totara_competency\entities\competency_achievement;
use totara_competency\entities\scale_value;
use totara_competency\expand_task;
use totara_competency\models\assignment_actions;

class pathway_learning_plan_totara_plan_observer_testcase extends advanced_testcase {

    public function test_event_for_competency_with_lp_pathway() {
        global $DB;

        $this->setAdminUser();

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

        /** @var totara_plan_generator $plan_generator */
        $plan_generator = $this->getDataGenerator()->get_plugin_generator('totara_plan');
        $plan = $plan_generator->create_learning_plan(['userid' => $user->id]);
        $plan_generator->add_learning_plan_competency($plan->id, $comp->id);

        $lp_pathway = new learning_plan();
        $lp_pathway->set_competency($competency);
        $lp_pathway->save();

        $great = scale_value::repository()->where('name', '=', 'Great')->one();
        $good = scale_value::repository()->where('name', '=', 'Good')->one();

        /** @var totara_competency_assignment_generator $assignment_generator */
        $assignment_generator = $this->getDataGenerator()->get_plugin_generator('totara_competency')->assignment_generator();
        $assignment = $assignment_generator->create_user_assignment($comp->id, $user->id);
        (new assignment_actions())->activate([$assignment->id]);
        (new expand_task($DB))->expand_all();

        $count = competency_achievement::repository()
            ->where('user_id', '=', $user->id)
            ->where('comp_id', '=', $comp->id)
            ->count();
        $this->assertEquals(0, $count);

        $development_plan = new development_plan($plan->id);
        /** @var dp_competency_component $component */
        $component = $development_plan->get_component('competency');
        $component->set_value($comp->id, $user->id, $great->id, new stdClass());

        // Verify that a row was inserted in the aggregation queue
        $source_table = new aggregation_users_table();
        $this->assertTrue($DB->record_exists($source_table->get_table_name(),
            [
                $source_table->get_user_id_column() => $user->id,
                $source_table->get_competency_id_column() => $comp->id,
                $source_table->get_process_key_column() => null
            ]
        ));

        $source_table->truncate();

        // Part 2 of this test (as we have data set up already): account for pathways being archived.

        $lp_pathway->delete();

        $component->set_value($comp->id, $user->id, $good->id, new stdClass());

        // As the user is still assigned to the competency
        $this->assertFalse($DB->record_exists($source_table->get_table_name(), []));
    }

    public function test_event_for_competency_without_lp_pathway() {
        global $DB;

        $this->setAdminUser();

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

        /** @var totara_plan_generator $plan_generator */
        $plan_generator = $this->getDataGenerator()->get_plugin_generator('totara_plan');
        $plan = $plan_generator->create_learning_plan(['userid' => $user->id]);
        $plan_generator->add_learning_plan_competency($plan->id, $comp->id);

        // No learning plan pathway being set up for competency here.
        // Let's just make sure none is being added by default:
        $this->assertCount(0, $DB->get_records('totara_competency_pathway', ['path_type' => 'learning_plan']));

        $great = scale_value::repository()->where('name', '=', 'Great')->one();

        /** @var totara_competency_assignment_generator $assignment_generator */
        $assignment_generator = $this->getDataGenerator()->get_plugin_generator('totara_competency')->assignment_generator();
        $assignment = $assignment_generator->create_user_assignment($comp->id, $user->id);
        (new assignment_actions())->activate([$assignment->id]);
        (new expand_task($DB))->expand_all();

        $count = competency_achievement::repository()
            ->where('user_id', '=', $user->id)
            ->where('comp_id', '=', $comp->id)
            ->count();
        $this->assertEquals(0, $count);

        $development_plan = new development_plan($plan->id);
        /** @var dp_competency_component $component */
        $component = $development_plan->get_component('competency');
        $component->set_value($comp->id, $user->id, $great->id, new stdClass());

        // Verify that a row was inserted in the aggregation queue
        $source_table = new aggregation_users_table();
        $this->assertTrue($DB->record_exists($source_table->get_table_name(),
            [
                $source_table->get_user_id_column() => $user->id,
                $source_table->get_competency_id_column() => $comp->id,
                $source_table->get_process_key_column() => null
            ]
        ));
    }

}
