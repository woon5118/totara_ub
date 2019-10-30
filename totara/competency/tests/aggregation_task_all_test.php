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

use pathway_criteria_group\criteria_group;
use totara_competency\aggregation_task;
use totara_competency\aggregation_users_table;
use totara_competency\entities\competency;
use totara_competency\entities\competency_achievement;
use totara_competency\task\competency_aggregation_all;
use totara_competency\entities\pathway_achievement;
use totara_criteria\criterion;

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
class totara_competency_aggregation_all_task_testcase extends advanced_testcase {

    // TODO: These are integration tests - Need to write proper unit tests
    private function setup_data() {
        global $DB;

        $data = new class() {
            public $comp;
            public $scale;
            public $scalevalues;
            public $users;
            public $courses;
            public $criteria = [];
            public $pathways = [];

            public function setup_scale_and_competency(totara_hierarchy_generator $hierarchygenerator) {
                global $DB;

                $this->scale = $hierarchygenerator->create_scale(
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

                $framework = $hierarchygenerator->create_comp_frame(['scale' => $this->scale->id]);
                $comp = $hierarchygenerator->create_comp(['frameworkid' => $framework->id]);

                $rows = $DB->get_records('comp_scale_values', ['scaleid' => $this->scale->id], 'sortorder');
                $this->scalevalues = [];
                foreach ($rows as $row) {
                    $this->scalevalues[$row->sortorder] = $row;
                }

                $this->comp = new competency($comp);
            }

            public function setup_active_expanded_user_assignments(
                tassign_competency_generator $assignment_generator,
                array $user_idxs = []
            ) {
                global $DB;

                $assignment_ids = [];
                foreach ($user_idxs as $idx) {
                    $assignment = $assignment_generator->create_user_assignment($this->comp->id, $this->users[$idx]->id);
                    $assignment_ids[] = $assignment->id;
                }

                $model = new \tassign_competency\models\assignment_actions();
                $model->activate($assignment_ids);

                $expand_task = new \tassign_competency\expand_task($DB);
                $expand_task->expand_all();

                return $assignment_ids;
            }

            public function setup_users_and_courses(testing_data_generator $generator) {
                for ($i = 1; $i <= 10; $i++) {
                    $this->users[$i] = $generator->create_user(['username' => "user{$i}"]);
                }

                // Create courses and enroll all users in all courses
                for ($i = 1; $i <= 10; $i++) {
                    $record = [
                        'shortname' => "Course $i",
                        'fullname' => "Course $i",
                    ];

                    $this->courses[$i] = $generator->create_course($record);
                    foreach ($this->users as $user) {
                        $generator->enrol_user($user->id, $this->courses[$i]->id);
                    }
                }
            }

            public function setup_criteria(totara_criteria_generator $criteria_generator) {

                // Create coursecompletion criteria
                $this->criteria['course_1'] = $criteria_generator->create_coursecompletion([
                    'aggregation' => criterion::AGGREGATE_ALL,
                    'courseids' => [$this->courses[1]->id],
                ]);

                $this->criteria['course_2_and_3'] = $criteria_generator->create_coursecompletion([
                    'aggregation' => criterion::AGGREGATE_ALL,
                    'courseids' => [$this->courses[2]->id, $this->courses[3]->id],
                ]);

                $this->criteria['course_3_or_4_or_5'] = $criteria_generator->create_coursecompletion([
                    'aggregation' => [
                        'method' => criterion::AGGREGATE_ANY_N,
                        'req_items' => 1,
                    ],
                    'courseids' => [$this->courses[1]->id, $this->courses[3]->id, $this->courses[5]->id],
                ]);
            }

            /**
             * We can't use the results of setup_data in the data providers.
             * Therefore we make use of keys when we need to refer to elements in the $data object.
             * This function first translates the keys to the correct values and then use the correct generator function
             * to create the pathway
             *
             * @param totara_competency_generator $competency_generator
             * @param $pw_type
             * @param $pw_data
             * @return |null
             */
            public function setup_pathway($competency_generator, $pw_key, $pw_type, $pw_data) {
                if ($pw_type == 'test_pathway') {
                    $this->pathways[$pw_key] = $competency_generator->create_test_pathway($this->comp);
                    return $this->pathways[$pw_key];
                }

                $this->map_keys_to_values($pw_data);

                if (!isset($pw_data['comp_id'])) {
                    $pw_data['comp_id'] = $this->comp->id;
                }

                $methodname = "create_{$pw_type}";
                $this->pathways[$pw_key] = $competency_generator->$methodname($pw_data);
                return $this->pathways[$pw_key];
            }

            public function map_keys_to_values(&$map_array) {
                $matches = [];

                foreach ($map_array as $name => $value) {
                    if (preg_match('/(.*)_key/', $name, $matches)) {
                        switch ($matches[1]) {
                            case 'comp':
                                $map_array['comp_id'] = $this->comp->id;
                                break;

                            case 'criteria':
                                if (!is_array($value)) {
                                    $value = [$value];
                                }
                                $map_array['criteria'] = [];
                                foreach ($value as $key) {
                                    $map_array['criteria'][] = $this->criteria[$key];
                                }
                                break;

                            case 'item':
                                if (is_array($value)) {
                                    $item_type = $value[0];
                                    $item_key = $value[1];
                                    if (method_exists($this->$item_type[$item_key], 'get_id')) {
                                        $map_array['item_id'] = $this->$item_type[$item_key]->get_id();
                                    } else if (property_exists($this->$item_type[$item_key], 'id')) {
                                        $map_array['item_id'] = $this->$item_type[$item_key]->id;
                                    }
                                }
                                break;

                            case 'pathway':
                                $map_array['pathway_id'] = $this->pathways[$value]->get_id();
                                break;

                            case 'scale_value':
                                if (is_null($value)) {
                                    $map_array['scale_value_id'] = null;
                                } else {
                                    $map_array['scale_value_id'] = $this->scalevalues[$value]->id;
                                }
                                break;
                        }

                        unset($map_array[$matches[0]]);
                    }
                }
            }

        };

        /** @var totara_hierarchy_generator $hierarchy_generator */
        $hierarchygenerator = $this->getDataGenerator()->get_plugin_generator('totara_hierarchy');
        /** @var totara_criteria_generator $criteria_generator */
        $criteria_generator = $this->getDataGenerator()->get_plugin_generator('totara_criteria');

        $data->setup_scale_and_competency($hierarchygenerator);
        $data->setup_users_and_courses($this->getDataGenerator());
        $data->setup_criteria($criteria_generator);

        return $data;
    }

    public function test_execute_with_no_data() {
        global $DB;

        $task = new competency_aggregation_all();
        $task->execute();

        $rows = $DB->get_records('totara_competency_achievement');
        $this->assertSame(0, count($rows));
    }

    /**
     * Data provider for test_integrated_aggregation
     */
    public function data_provider_test_integrated_aggregation_first_run() {
        return [
            [
                'pathways' => [
                    [
                        'type' => 'criteria_group',
                        'data' => [
                            'sortorder' => 1,
                            'scale_value_key' => 2,
                            'criteria_key' => ['course_1'],
                        ]
                    ],
                ],
                'user_keys' => [1],
                'expected' => [
                    [
                        'user_key' => 1,
                        'item_records' => [
                            [
                                'item_key' => ['courses', 1],
                                'criterion_met' => 0,
                            ],
                        ],
                        'pathway_achievements' => [
                            [
                                'pathway_key' => 0,
                                'scale_value_key' => null,
                                'status' => pathway_achievement::STATUS_CURRENT,
                                'related_info' => [],
                            ],
                        ],
                        'competency_achievements' => [
                            [
                                'comp_key' => 0,
                                'scale_value_key' => null,
                                'proficient' => 0,
                                'status' => competency_achievement::ACTIVE_ASSIGNMENT,
                            ],
                        ],
                    ],
                ],
            ],

            [
                'pathways' => [
                    [
                        'type' => 'manual',
                        'data' => [
                            'sortorder' => 1,
                            'roles' => ['manager'],
                        ]
                    ],
                ],
                'user_keys' => [1],
                'expected' => [
                    [
                        'user_key' => 1,
                        'pathway_achievements' => [
                            [
                                'pathway_key' => 0,
                                'scale_value_key' => null,
                                'status' => pathway_achievement::STATUS_CURRENT,
                                'related_info' => [],
                            ],
                        ],
                        'competency_achievements' => [
                            [
                                'comp_key' => 0,
                                'scale_value_key' => null,
                                'proficient' => 0,
                                'status' => competency_achievement::ACTIVE_ASSIGNMENT,
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }


    /**
     * Integrated test of competency aggregation
     *
     * @dataProvider data_provider_test_integrated_aggregation_first_run
     */
    public function test_integrated_aggregation_first_run($pathways, $users, $expected) {
        global $DB;

        $data = $this->setup_data();

        /** @var totara_competency_generator $competency_generator */
        $competency_generator = $this->getDataGenerator()->get_plugin_generator('totara_competency');

        foreach ($pathways as $key => $pw) {
            $data->setup_pathway($competency_generator, $key, $pw['type'], $pw['data']);
        }

        // Assign user1 to the competency
        /** @var tassign_competency_generator $assignment_generator */
        $assignment_generator = $this->getDataGenerator()->get_plugin_generator('totara_competency')->assignment_generator();
        $data->setup_active_expanded_user_assignments($assignment_generator, $users);

        // Let's be clear on the state before running the task.
        $this->assertEquals(0, $DB->count_records('totara_criteria_item_record'));
        $this->assertEquals(0, $DB->count_records('totara_competency_pathway_achievement'));
        $this->assertEquals(0, $DB->count_records('totara_competency_achievement'));
        $this->assertEquals(0, $DB->count_records('totara_competency_achievement_via'));

        // Now run the task
        $task = new competency_aggregation_all();
        $task->execute();

        // Verify item_record
        $this->verify_achievement_records($expected, $data);
    }

    public function test_last_aggregated_field() {
        global $DB;

        $data = $this->setup_data();

        /** @var totara_competency_generator $competency_generator */
        $competency_generator = $this->getDataGenerator()->get_plugin_generator('totara_competency');

        $params = [
            'comp_id' => $data->comp->id,
            'sortorder' => 1,
            'scale_value_id' => $data->scalevalues[2],
            'criteria' => [$data->criteria['course_1']],
        ];

        $pw = $competency_generator->create_criteria_group($params);

        // Assign user1 to the competency
        /** @var tassign_competency_generator $assignment_generator */
        $assignment_generator = $this->getDataGenerator()->get_plugin_generator('totara_competency')->assignment_generator();
        $data->setup_active_expanded_user_assignments($assignment_generator, [1]);

        $start_time = $time = time();

        $task = new competency_aggregation_all();
        $task->set_aggregation_time($time);

        // Up until this point, this test will have been similar to test_aggregation_of_single_competency_and_user().
        // We now run the task again without any changes
        $this->setCurrentTimeStart();

        $task->execute();

        $comp_records1 = $DB->get_records('totara_competency_achievement');
        $this->assertCount(1, $comp_records1);
        $comp_record1 = reset($comp_records1);

        // We've recorded the time we aggregated and we sent an event because there was a new value.
        $this->assertTimeCurrent($comp_record1->last_aggregated);

        $time = $time + 1;
        $task->set_aggregation_time($time);
        $task->execute();

        // Nothing changed - so not expecting an update
        $comp_records2 = $DB->get_records('totara_competency_achievement');
        $this->assertCount(1, $comp_records2);
        $comp_record2 = reset($comp_records2);

        $this->assertEquals($comp_record1->last_aggregated, $comp_record2->last_aggregated);

        $time = $time + 1;
        $task->set_aggregation_time($time);
        $DB->set_field('totara_criteria_item_record', 'timeevaluated', $time);

        $task->execute();

        $comp_records3 = $DB->get_records('totara_competency_achievement');
        $this->assertCount(1, $comp_records3);
        $comp_record3 = reset($comp_records3);

        // This time we re-aggregated
        $this->assertTrue($comp_record3->last_aggregated > $comp_record1->last_aggregated);
    }


    private function verify_achievement_records($expected, $data) {
        foreach ($expected as $expected_achievement_set) {
            $this->assertTrue(isset($expected_achievement_set['user_key']));
            $this->assertTrue(isset($data->users[$expected_achievement_set['user_key']]));
            $user = $data->users[$expected_achievement_set['user_key']];

            if (isset($expected_achievement_set['item_records'])) {
                for ($i = 0; $i < count($expected_achievement_set['item_records']); $i++) {
                    $data->map_keys_to_values($expected_achievement_set['item_records'][$i]);
                }
                $this->verify_item_records($user->id, $expected_achievement_set['item_records']);
            }

            if (isset($expected_achievement_set['pathway_achievements'])) {
                for ($i = 0; $i < count($expected_achievement_set['pathway_achievements']); $i++) {
                    $data->map_keys_to_values($expected_achievement_set['pathway_achievements'][$i]);
                }
                $this->verify_pathway_achievements($user->id, $expected_achievement_set['pathway_achievements']);
            }

            if (isset($expected_achievement_set['competency_achievements'])) {
                for ($i = 0; $i < count($expected_achievement_set['competency_achievements']); $i++) {
                    $data->map_keys_to_values($expected_achievement_set['competency_achievements'][$i]);
                }
                $this->verify_competency_achievements($user->id, $expected_achievement_set['competency_achievements']);
            }
        }
    }

    private function verify_item_records(int $user_id, array $expected_rows) {
        global $DB;

        $sql =
            "SELECT tcir.*, tci.item_id
               FROM {totara_criteria_item_record} tcir
               JOIN {totara_criteria_item} tci
                 ON tci.id = tcir.criterion_item_id
              WHERE tcir.user_id = :userid";
        $actual_rows = $DB->get_records_sql($sql, ['userid' => $user_id]);
        $this->assertSame(count($expected_rows), count($actual_rows));

        foreach ($actual_rows as $actual_row) {
            foreach ($expected_rows as $key => $expected_row) {
                if ((int)$actual_row->item_id == $expected_row['item_id']) {
                    $this->assertEquals($expected_row['criterion_met'], $actual_row->criterion_met);
                    unset($expected_rows[$key]);
                    break;
                }
            }
        }

        $this->assertSame(0, count($expected_rows));
    }

    private function verify_pathway_achievements($user_id, $expected_rows) {
        global $DB;

        $actual_rows = $DB->get_records('totara_competency_pathway_achievement', ['user_id' => $user_id]);

        $this->assertSame(count($expected_rows), count($actual_rows));
        foreach ($actual_rows as $actual_row) {
            foreach ($expected_rows as $key => $expected_row) {
                if ((int)$actual_row->pathway_id == $expected_row['pathway_id'] &&
                    (int)$actual_row->status == $expected_row['status'] &&
                    (int)$actual_row->scale_value_id == $expected_row['scale_value_id'] &&
                    (!isset($expected_row['related_info']) ||
                        $actual_row->related_info == json_encode($expected_row['related_info']))) {
                    unset($expected_rows[$key]);
                    break;
                }
            }
        }

        $this->assertSame(0, count($expected_rows));
    }

    private function verify_competency_achievements($user_id, $expected_rows) {
        global $DB;

        $actual_rows = $DB->get_records('totara_competency_achievement', ['user_id' => $user_id]);

        $this->assertSame(count($expected_rows), count($actual_rows));
        foreach ($actual_rows as $actual_row) {
            foreach ($expected_rows as $key => $expected_row) {
                if ((int)$actual_row->comp_id == $expected_row['comp_id'] &&
                    (int)$actual_row->status == $expected_row['status'] &&
                    (int)$actual_row->scale_value_id == $expected_row['scale_value_id'] &&
                    (int)$actual_row->proficient == $expected_row['proficient']) {
                    unset($expected_rows[$key]);
                    break;
                }
            }
        }

        $this->assertSame(0, count($expected_rows));
    }
}
