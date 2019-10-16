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
 * @package pathway_criteria_group
 */

use criteria_coursecompletion\coursecompletion;
use pathway_criteria_group\aggregation_helper;
use pathway_criteria_group\criteria_group;
use tassign_competency\models\assignment_actions;
use totara_competency\entities\scale_value;

class pathway_criteria_group_aggregation_helper_testcase extends advanced_testcase {

    private function setup_data() {
        global $DB;

        $data = new class {
            public $competency_data = [];
            public $courses = [];
            public $users = [];

        };

        for ($i = 1; $i <= 3; $i++) {
            $data->users[$i] = $this->getDataGenerator()->create_user();
            $data->courses[$i] = $this->getDataGenerator()->create_course();
        }

        /** @var totara_hierarchy_generator $hierarchy_generator */
        $hierarchygenerator = $this->getDataGenerator()->get_plugin_generator('totara_hierarchy');

        $scale = $hierarchygenerator->create_scale(
            'comp',
            ['name' => 'Test scale', 'description' => 'Test scale'],
            [
                1 => ['name' => 'No clue', 'proficient' => 0, 'sortorder' => 1, 'default' => 1],
                2 => ['name' => 'Learning', 'proficient' => 0, 'sortorder' => 2, 'default' => 0],
                3 => ['name' => 'Getting there', 'proficient' => 0, 'sortorder' => 3, 'default' => 0],
                4 => ['name' => 'Almost there', 'proficient' => 1, 'sortorder' => 4, 'default' => 0],
                5 => ['name' => 'Arrived', 'proficient' => 1, 'sortorder' => 4, 'default' => 0],
            ]
        );
        $rows = $DB->get_records('comp_scale_values', ['scaleid' => $scale->id], 'sortorder');
        foreach ($rows as $row) {
            $scalevalues[$row->sortorder] = new scale_value($row->id);
        }

        $framework = $hierarchygenerator->create_comp_frame(['scale' => $scale->id]);

        /** @var totara_competency_generator $competency_generator */
        $competency_generator = $this->getDataGenerator()->get_plugin_generator('totara_competency');
        /** @var tassign_competency_generator $assignment_generator */
        $assignment_generator = $this->getDataGenerator()->get_plugin_generator('tassign_competency');

        $to_create = [
            'Comp A' => [
                'user_keys' => [1, 2],
                'course_keys' => [1],
            ],
            'Comp B' => [
                'user_keys' => [1, 3],
                'course_keys' => [1, 2],
            ],
            'Comp C' => [
                'user_keys' => [3],
                'course_keys' => [3],
            ],
            'Comp D' => [
                'user_keys' => [1, 2, 3],
                'course_keys' => [],
            ],
        ];

        foreach ($to_create as $competency_name => $competency_values) {
            $competency = $competency_generator->create_competency($competency_name, $framework->id);
            $criteria_ids = [];

            // Each coursecompletion criterion is created in its own pathway
            foreach ($competency_values['course_keys'] as $course_idx) {
                $criterion = new coursecompletion();
                $criterion->set_aggregation_method(coursecompletion::AGGREGATE_ALL);
                $criterion->add_items([$data->courses[$course_idx]->id]);

                $pathway = new criteria_group();
                $pathway->set_competency($competency);
                $pathway->set_scale_value($scalevalues[3]);
                $pathway->add_criterion($criterion);
                $pathway->save();

                $criteria_ids[$course_idx] = $criterion->get_id();
            }

            foreach ($competency_values['user_keys'] as $user_idx) {
                $assignment = $assignment_generator->create_user_assignment($competency->id, $data->users[$user_idx]->id);
                $model = new assignment_actions();
                $model->activate([$assignment->id]);
            }

            $data->competency_data[$competency_name] = [
                'competency_id' => $competency->id,
                'criteria_ids' => $criteria_ids,
            ];
        }

        $expand_task = new \tassign_competency\expand_task($DB);
        $expand_task->expand_all();

        return $data;
    }

    /** Test no criteria_ids provided */
    public function test_aggregate_based_on_criteria_empty_list() {
        global $DB;

        $data = $this->setup_data();
        $this->verify_adhoc_task(0);

        // No criteria_ids
        aggregation_helper::aggregate_based_on_criteria($data->users[3]->id, []);
        // No ad-hoc task should be scheduled
        $this->verify_adhoc_task(0);
    }

    /**
     * Test single competency, single criterion
     */
    public function test_aggregate_based_on_criteria_single_competency_single_criterion_assigned_user() {
        global $DB;

        $data = $this->setup_data();

        $criteria_ids = $data->competency_data['Comp A']['criteria_ids'];
        aggregation_helper::aggregate_based_on_criteria($data->users[1]->id, $criteria_ids);

        // Expecting ad-hoc task with 1 competency id
        $this->verify_adhoc_task(1,
            [
                'user_id' => $data->users[1]->id,
                'competency_ids' => [$data->competency_data['Comp A']['competency_id']],
            ]
        );
    }

    /**
     * Test single criterion, user not assigned
     */
    public function test_aggregate_based_on_criteria_single_criterion_not_assigned_user() {
        global $DB;

        $data = $this->setup_data();

        $criteria_ids = $data->competency_data['Comp A']['criteria_ids'];
        aggregation_helper::aggregate_based_on_criteria($data->users[3]->id, $criteria_ids);

        // Expecting ad-hoc task with 1 competency id
        $this->verify_adhoc_task(0);
    }

    /**
     * Test single competency, multiple criteria, user assigned
     */
    public function test_aggregate_based_on_criteria_single_competencies_multiple_criteria_assigned_user() {
        global $DB;

        $data = $this->setup_data();

        $criteria_ids = $data->competency_data['Comp B']['criteria_ids'];
        aggregation_helper::aggregate_based_on_criteria($data->users[1]->id, $criteria_ids);

        // Expecting ad-hoc task with 1 competency id
        $this->verify_adhoc_task(1,
            [
                'user_id' => $data->users[1]->id,
                'competency_ids' => [$data->competency_data['Comp B']['competency_id']],
            ]
        );
    }

    /**
     * Test single competency, multiple criteria, user not assigned
     */
    public function test_aggregate_based_on_criteria_single_competencies_multiple_criteria_not_assigned_user() {
        global $DB;

        $data = $this->setup_data();

        $criteria_ids = $data->competency_data['Comp B']['criteria_ids'];
        aggregation_helper::aggregate_based_on_criteria($data->users[2]->id, $criteria_ids);

        // Expecting ad-hoc task with 1 competency id
        $this->verify_adhoc_task(0);
    }

    /**
     * Test multiple competencies, multiple criteria, user assigned in some
     */
    public function test_aggregate_based_on_criteria_multipl_competencies_multiple_criteria() {
        global $DB;

        $data = $this->setup_data();

        $criteria_ids = array_merge(
            $data->competency_data['Comp A']['criteria_ids'],
            $data->competency_data['Comp B']['criteria_ids'],
            $data->competency_data['Comp C']['criteria_ids']);
        aggregation_helper::aggregate_based_on_criteria($data->users[3]->id, $criteria_ids);

        // Expecting ad-hoc task with 1 competency id
        $this->verify_adhoc_task(1,
            [
                'user_id' => $data->users[3]->id,
                'competency_ids' => [$data->competency_data['Comp B']['competency_id'], $data->competency_data['Comp C']['competency_id']],
            ]
        );
    }

    private function verify_adhoc_task(bool $expected, ?array $expected_custom_data = null) {
        global $DB;

        $rows = $DB->get_records('task_adhoc', ['classname' => '\totara_competency\task\competency_achievement_aggregation_adhoc']);
        $this->assertSame((int) $expected, count($rows));

        if (!is_null($expected_custom_data)) {
            $task = reset($rows);
            $this->assertEqualsCanonicalizing($expected_custom_data, json_decode($task->customdata, true));
        }
    }

}
