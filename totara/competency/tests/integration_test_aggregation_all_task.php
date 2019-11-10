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
 * @author Riana Rossouw <riana.rossouw@totaralearning.com>
 * @package totara_competency
 */

use pathway_manual\manual;
use totara_competency\entities\competency;
use totara_competency\entities\competency_achievement;
use totara_competency\entities\pathway_achievement;
use totara_competency\expand_task;
use totara_competency\linked_courses;
use totara_competency\models\assignment_actions;
use totara_competency\task\competency_aggregation_all;
use totara_job\job_assignment;

/**
 * This is an integration test with multiple users assigned to multiple competencies
 * It verifies over the competency / criteria boundaries to ensure the correct data is
 * created on all levels
 *
 * Test descriptions are defined in https://docs.google.com/spreadsheets/d/1rjnFZtI-ZJZCE8AmJjmiXtmU9S1_uIld_swteRyIKgA/edit#gid=0
 */
class totara_competency_integration_aggregation_all_task_testcase extends advanced_testcase {

    private $num_competencies = 5;
    private $num_users = 10;
    private $num_courses = 10;

    private function setup_data() {
        global $DB;

//        if (!PHPUNIT_LONGTEST) {
//            // we do not want to DDOS their server, right?
//            $this->markTestSkipped('PHPUNIT_LONGTEST is not defined');
//        }

        $data = new class() {
            public $scale;
            public $scalevalues;
            public $competencies = [];
            public $users = [];
            public $courses = [];

            /** @var testing_data_generator $generator */
            public $generator;
            /** @var totara_hierarchy_generator $hierarchy_generator */
            public $hierarchy_generator;
            /** @var totara_competency_generator $competency_generator */
            public $competency_generator;
            /** @var totara_criteria_generator $criteria_generator */
            public $criteria_generator;
            /** @var totara_competency_assignment_generator $assign_generator */
            public $assign_generator;

            public function assign_users_to_competencies(array $to_assign) {
                global $DB;

                $assignment_ids = [];
                foreach ($to_assign as $user_comp) {
                    $assignment = $this->assign_generator->create_user_assignment($user_comp['competency_id'], $user_comp['user_id']);
                    $assignment_ids[] = $assignment->id;
                }

                $model = new assignment_actions();
                $model->activate($assignment_ids);

                $expand_task = new expand_task($DB);
                $expand_task->expand_all();

                return $assignment_ids;
            }
        };

        $data->generator = $this->getDataGenerator();
        $data->hierarchy_generator = $data->generator->get_plugin_generator('totara_hierarchy');
        $data->competency_generator = $data->generator->get_plugin_generator('totara_competency');
        $data->criteria_generator = $data->generator->get_plugin_generator('totara_criteria');
        $data->assign_generator = $data->competency_generator->assignment_generator();

        $data->scale = $data->hierarchy_generator->create_scale(
            'comp',
            ['name' => 'Test scale', 'description' => 'Test scale'],
            [
                1 => ['name' => 'No clue', 'proficient' => 0, 'sortorder' => 1, 'default' => 1],
                2 => ['name' => 'Learning', 'proficient' => 0, 'sortorder' => 2, 'default' => 0],
                3 => ['name' => 'Getting there', 'proficient' => 0, 'sortorder' => 3, 'default' => 0],
                4 => ['name' => 'Almost there', 'proficient' => 1, 'sortorder' => 4, 'default' => 0],
                5 => ['name' => 'Arrived', 'proficient' => 1, 'sortorder' => 5, 'default' => 0],
            ]
        );

        $framework = $data->hierarchy_generator->create_comp_frame(['scale' => $data->scale->id]);

        $rows = $DB->get_records('comp_scale_values', ['scaleid' => $data->scale->id], 'sortorder');
        $data->scalevalues = [];
        foreach ($rows as $row) {
            $data->scalevalues[$row->sortorder] = $row;
        }

        $competencies_and_parents = [
            1 => 0,
            2 => 1,
            3 => 0,
            4 => 3,
            5 => 3
        ];

        foreach ($competencies_and_parents as $idx => $parent_idx) {
            $comp_data = [
                'frameworkid' => $framework->id,
                'parentid' => empty($parent_idx) ? 0 : $data->competencies[$parent_idx]->id,
            ];
            $comp = $data->hierarchy_generator->create_comp($comp_data);
            $data->competencies[$idx] = new competency($comp);
        }

        // Users with job assignments
        $data->users['manager'] = $data->generator->create_user(['username' => 'manager']);
        $data->users['appraiser'] = $data->generator->create_user(['username' => 'appraiser']);

        // Job assignments
        $managerja = job_assignment::create_default($data->users['manager']->id, [
            'fullname' => 'Manager job',
            'idnumber' => 'MANAGERJOB',
        ]);

        for ($i = 1; $i <= $this->num_users; $i++) {
            $data->users[$i] = $data->generator->create_user(['username' => "user{$i}"]);

            // All users get manager as manager and appraiser as appraiser
            job_assignment::create_default($data->users[$i]->id, [
                'managerjaid' => $managerja->id,
                'fullname' => 'Managed by manager',
                'idnumber' => "User{$i}managed",
            ]);

            job_assignment::create_default($data->users[$i]->id, [
                'appraiserid' => $data->users['appraiser']->id,
                'fullname' => 'Appraised by appraiser',
                'idnumber' => "User{$i}appraised",
            ]);
        }


        // Create courses and enroll all users in all courses
        for ($i = 1; $i <= $this->num_courses; $i++) {
            $record = [
                'shortname' => "Course $i",
                'fullname' => "Course $i",
            ];

            $data->courses[$i] = $data->generator->create_course($record);
            foreach ($data->users as $user) {
                $data->generator->enrol_user($user->id, $data->courses[$i]->id);
            }
        }

        return $data;
    }


    /**
     * Test competency_aggregation_all task with no criteria
     */
    public function test_aggregation_all_task_no_criteria() {
        $data = $this->setup_data();

        $this->verify_item_records([]);
        $this->verify_pathway_achievements([]);
        $this->verify_competency_achievements([]);

        (new competency_aggregation_all())->execute();
        $this->verify_item_records([]);
        $this->verify_pathway_achievements([]);
        $this->verify_competency_achievements([]);

        // Now assign users to the competencies and test again

        $to_assign = [];
        foreach ($data->users as $user) {
            foreach ($data->competencies as $competency) {
                $to_assign[] = ['user_id' => $user->id, 'competency_id' => $competency->id];
            }
        }
        $data->assign_users_to_competencies($to_assign);

        (new competency_aggregation_all())->execute();
        $this->verify_item_records([]);
        $this->verify_pathway_achievements([]);
        $this->verify_competency_achievements([]);

    }

    /**
     * Test competency_aggregate_all with a single onactivate criterion
     */
    public function test_aggregation_all_task_single_onactivate() {
        $data = $this->setup_data();

        // Create a criteria_group on competency 1 and 2 with 1 onactivate criterion on the lowest scale
        $pathways = [];
        for ($i = 1; $i <= 2; $i++) {
            $criterion = $data->criteria_generator->create_onactivate(['competency' => $data->competencies[$i]->id]);
            $pathways[$i] = $data->competency_generator->create_criteria_group($data->competencies[$i],
                [$criterion], $data->scalevalues[1]->id);
        }

        // First run the task without any assignments - should have no effect
        (new competency_aggregation_all())->execute();
        $this->verify_item_records([]);
        $this->verify_pathway_achievements([]);
        $this->verify_competency_achievements([]);

        // Now assign some users to competencies with criteria and some to competencies without criteria
        $to_assign = [];
        for ($user_idx = 1; $user_idx <= 3; $user_idx++) {
            $to_assign[] = ['user_id' => $data->users[$user_idx]->id, 'competency_id' => $data->competencies[$user_idx]->id];
            $to_assign[] = ['user_id' => $data->users[$user_idx]->id, 'competency_id' => $data->competencies[$user_idx + 1]->id];
        }
        $data->assign_users_to_competencies($to_assign);

        (new competency_aggregation_all())->execute();
        $this->verify_item_records([
            ['item_id' => $data->competencies[1]->id, 'user_id' => $data->users[1]->id, 'criterion_met' => 1],
            ['item_id' => $data->competencies[2]->id, 'user_id' => $data->users[1]->id, 'criterion_met' => 1],
            ['item_id' => $data->competencies[2]->id, 'user_id' => $data->users[2]->id, 'criterion_met' => 1],
        ]);

        $this->verify_pathway_achievements([
            [
                'pathway_id' => $pathways[1]->get_id(),
                'user_id' => $data->users[1]->id,
                'status' => pathway_achievement::STATUS_CURRENT,
                'scale_value_id' => $data->scalevalues[1]->id,
                'related_info' => ['onactivate'],
            ],
            [
                'pathway_id' => $pathways[2]->get_id(),
                'user_id' => $data->users[1]->id,
                'status' => pathway_achievement::STATUS_CURRENT,
                'scale_value_id' => $data->scalevalues[1]->id,
                'related_info' => ['onactivate'],
            ],
            [
                'pathway_id' => $pathways[2]->get_id(),
                'user_id' => $data->users[2]->id,
                'status' => pathway_achievement::STATUS_CURRENT,
                'scale_value_id' => $data->scalevalues[1]->id,
                'related_info' => ['onactivate'],
            ],
        ]);

        $this->verify_competency_achievements([
            [
                'competency_id' => $data->competencies[1]->id,
                'user_id' => $data->users[1]->id,
                'status' => competency_achievement::ACTIVE_ASSIGNMENT,
                'scale_value_id' => $data->scalevalues[1]->id,
                'proficient' => 0,
            ],
            [
                'competency_id' => $data->competencies[2]->id,
                'user_id' => $data->users[1]->id,
                'status' => competency_achievement::ACTIVE_ASSIGNMENT,
                'scale_value_id' => $data->scalevalues[1]->id,
                'proficient' => 0,
            ],
            [
                'competency_id' => $data->competencies[2]->id,
                'user_id' => $data->users[2]->id,
                'status' => competency_achievement::ACTIVE_ASSIGNMENT,
                'scale_value_id' => $data->scalevalues[1]->id,
                'proficient' => 0,
            ],
        ]);
    }

    /**
     * Test competency_aggregate_all with a single coursecompletion criterion
     */
    public function test_aggregation_all_task_single_coursecompletion() {
        $data = $this->setup_data();

        // Create a criteria_group on competency 1 with 1 coursecompletion criterion to complete course1
        // Assign users 1, 2 and 3
        // Users 1 and 3 completes the course
        $criterion = $data->criteria_generator->create_coursecompletion(['courseids' =>[$data->courses[1]->id]]);
        $pathway = $data->competency_generator->create_criteria_group($data->competencies[1],
            [$criterion], $data->scalevalues[2]->id);

        // Assigning users 1 to 3 to the competency1 and competency2 (competency2 intentionally without criteria)
        $to_assign = [];
        for ($user_idx = 1; $user_idx <= 3; $user_idx++) {
            $to_assign[] = ['user_id' => $data->users[$user_idx]->id, 'competency_id' => $data->competencies[1]->id];
            $to_assign[] = ['user_id' => $data->users[$user_idx]->id, 'competency_id' => $data->competencies[2]->id];
        }
        $data->assign_users_to_competencies($to_assign);

        // Mark users 1 and 3 to have completed the course
        foreach ([1, 3] as $user_idx) {
            $completion = new completion_completion(['course' => $data->courses[1]->id, 'userid' => $data->users[$user_idx]->id]);
            $completion->mark_complete();
        }

        // Now run the task
        (new competency_aggregation_all())->execute();
        $this->verify_item_records([
            ['item_id' => $data->courses[1]->id, 'user_id' => $data->users[1]->id, 'criterion_met' => 1],
            ['item_id' => $data->courses[1]->id, 'user_id' => $data->users[2]->id, 'criterion_met' => 0],
            ['item_id' => $data->courses[1]->id, 'user_id' => $data->users[3]->id, 'criterion_met' => 1],
        ]);

        $this->verify_pathway_achievements([
            [
                'pathway_id' => $pathway->get_id(),
                'user_id' => $data->users[1]->id,
                'status' => pathway_achievement::STATUS_CURRENT,
                'scale_value_id' => $data->scalevalues[2]->id,
                'related_info' => ['coursecompletion'],
            ],
            [
                'pathway_id' => $pathway->get_id(),
                'user_id' => $data->users[2]->id,
                'status' => pathway_achievement::STATUS_CURRENT,
                'scale_value_id' => null,
                'related_info' => [],
            ],
            [
                'pathway_id' => $pathway->get_id(),
                'user_id' => $data->users[3]->id,
                'status' => pathway_achievement::STATUS_CURRENT,
                'scale_value_id' => $data->scalevalues[2]->id,
                'related_info' => ['coursecompletion'],
            ],
        ]);

        $this->verify_competency_achievements([
            [
                'competency_id' => $data->competencies[1]->id,
                'user_id' => $data->users[1]->id,
                'status' => competency_achievement::ACTIVE_ASSIGNMENT,
                'scale_value_id' => $data->scalevalues[2]->id,
                'proficient' => 0,
            ],
            [
                'competency_id' => $data->competencies[1]->id,
                'user_id' => $data->users[2]->id,
                'status' => competency_achievement::ACTIVE_ASSIGNMENT,
                'scale_value_id' => null,
                'proficient' => 0,
            ],
            [
                'competency_id' => $data->competencies[1]->id,
                'user_id' => $data->users[3]->id,
                'status' => competency_achievement::ACTIVE_ASSIGNMENT,
                'scale_value_id' => $data->scalevalues[2]->id,
                'proficient' => 0,
            ],
        ]);
    }

    /**
     * Test competency_aggregate_all with a single linkedcourses criterion
     */
    public function test_aggregation_all_task_single_linkedcourses() {
        $data = $this->setup_data();

        $pathways = [];
        for ($i = 1; $i <= 2; $i++) {
            $criterion = $data->criteria_generator->create_linkedcourses(['competency' => $data->competencies[$i]->id]);
            $pathways[$i] = $data->competency_generator->create_criteria_group($data->competencies[$i],
                [$criterion], $data->scalevalues[3]->id);
        }

        // Link courses to competencies
        linked_courses::set_linked_courses(
            $data->competencies[1]->id,
            [
                ['id' => $data->courses[1]->id, 'linktype' => linked_courses::LINKTYPE_MANDATORY],
                ['id' => $data->courses[2]->id, 'linktype' => linked_courses::LINKTYPE_MANDATORY],
                ['id' => $data->courses[3]->id, 'linktype' => linked_courses::LINKTYPE_MANDATORY],
            ]
        );

        linked_courses::set_linked_courses(
            $data->competencies[2]->id,
            [
                ['id' => $data->courses[3]->id, 'linktype' => linked_courses::LINKTYPE_MANDATORY],
                ['id' => $data->courses[4]->id, 'linktype' => linked_courses::LINKTYPE_MANDATORY],
            ]
        );

        // Assigning users
        $to_assign = [
            ['user_id' => $data->users[1]->id, 'competency_id' => $data->competencies[1]->id],
            ['user_id' => $data->users[2]->id, 'competency_id' => $data->competencies[1]->id],
            ['user_id' => $data->users[1]->id, 'competency_id' => $data->competencies[2]->id],
            ['user_id' => $data->users[3]->id, 'competency_id' => $data->competencies[2]->id],
        ];
        $data->assign_users_to_competencies($to_assign);

        // Mark users' completion of course
        $completed = [
            1 => [1, 3],
            2 => [1, 2],
            3 => [1, 2, 3],
            4 => [4, 5],
        ];
        foreach ($completed as $course_idx => $user_idxs) {
            foreach ($user_idxs as $user_idx) {
                $completion = new completion_completion(['course' => $data->courses[$course_idx]->id, 'userid' => $data->users[$user_idx]->id]);
                $completion->mark_complete();
            }
        }

        // Now run the task
        (new competency_aggregation_all())->execute();

        // Because course3 is linked to 2 different pathways 2 different criteria items are created with the same item_id
        // That results in 2 item_records for each user on the same item_id (note that the criterion_item_ids are different)
        $this->verify_item_records([
            ['item_id' => $data->courses[1]->id, 'user_id' => $data->users[1]->id, 'criterion_met' => 1],
            ['item_id' => $data->courses[1]->id, 'user_id' => $data->users[2]->id, 'criterion_met' => 0],
            ['item_id' => $data->courses[2]->id, 'user_id' => $data->users[1]->id, 'criterion_met' => 1],
            ['item_id' => $data->courses[2]->id, 'user_id' => $data->users[2]->id, 'criterion_met' => 1],
            ['item_id' => $data->courses[3]->id, 'user_id' => $data->users[1]->id, 'criterion_met' => 1],
            ['item_id' => $data->courses[3]->id, 'user_id' => $data->users[2]->id, 'criterion_met' => 1],
            ['item_id' => $data->courses[3]->id, 'user_id' => $data->users[1]->id, 'criterion_met' => 1],
            ['item_id' => $data->courses[3]->id, 'user_id' => $data->users[3]->id, 'criterion_met' => 1],
            ['item_id' => $data->courses[4]->id, 'user_id' => $data->users[1]->id, 'criterion_met' => 0],
            ['item_id' => $data->courses[4]->id, 'user_id' => $data->users[3]->id, 'criterion_met' => 0],
        ]);

        $this->verify_pathway_achievements([
            [
                'pathway_id' => $pathways[1]->get_id(),
                'user_id' => $data->users[1]->id,
                'status' => pathway_achievement::STATUS_CURRENT,
                'scale_value_id' => $data->scalevalues[3]->id,
                'related_info' => ['linkedcourses'],
            ],
            [
                'pathway_id' => $pathways[1]->get_id(),
                'user_id' => $data->users[2]->id,
                'status' => pathway_achievement::STATUS_CURRENT,
                'scale_value_id' => null,
                'related_info' => [],
            ],
            [
                'pathway_id' => $pathways[2]->get_id(),
                'user_id' => $data->users[1]->id,
                'status' => pathway_achievement::STATUS_CURRENT,
                'scale_value_id' => null,
                'related_info' => [],
            ],
            [
                'pathway_id' => $pathways[2]->get_id(),
                'user_id' => $data->users[3]->id,
                'status' => pathway_achievement::STATUS_CURRENT,
                'scale_value_id' => null,
                'related_info' => [],
            ],
        ]);

        $this->verify_competency_achievements([
            [
                'competency_id' => $data->competencies[1]->id,
                'user_id' => $data->users[1]->id,
                'status' => competency_achievement::ACTIVE_ASSIGNMENT,
                'scale_value_id' => $data->scalevalues[3]->id,
                'proficient' => 0,
            ],
            [
                'competency_id' => $data->competencies[1]->id,
                'user_id' => $data->users[2]->id,
                'status' => competency_achievement::ACTIVE_ASSIGNMENT,
                'scale_value_id' => null,
                'proficient' => 0,
            ],
            [
                'competency_id' => $data->competencies[2]->id,
                'user_id' => $data->users[1]->id,
                'status' => competency_achievement::ACTIVE_ASSIGNMENT,
                'scale_value_id' => null,
                'proficient' => 0,
            ],
            [
                'competency_id' => $data->competencies[2]->id,
                'user_id' => $data->users[3]->id,
                'status' => competency_achievement::ACTIVE_ASSIGNMENT,
                'scale_value_id' => null,
                'proficient' => 0,
            ],
        ]);

    }

    /**
     * Test competency_aggregate_all with childcompetency criteria.
     * The child competency uses coursecompletion criteria
     */
    public function test_aggregation_all_task_coursecompletion_to_childcompetency() {
        $data = $this->setup_data();

        $pathways = [];

        // The child competency ...
        $criterion = $data->criteria_generator->create_coursecompletion(['courseids' =>[$data->courses[1]->id]]);
        $pathways['child'] = $data->competency_generator->create_criteria_group($data->competencies[2],
            [$criterion], $data->scalevalues[4]->id);

        // The parent competency ...
        $criterion = $data->criteria_generator->create_childcompetency(['competency' => $data->competencies[1]->id]);
        $pathways['parent'] = $data->competency_generator->create_criteria_group($data->competencies[1],
            [$criterion], $data->scalevalues[2]->id);

        // Assign users
        $to_assign = [
            ['user_id' => $data->users[1]->id, 'competency_id' => $data->competencies[2]->id],
            ['user_id' => $data->users[2]->id, 'competency_id' => $data->competencies[2]->id],
            ['user_id' => $data->users[3]->id, 'competency_id' => $data->competencies[2]->id],
            ['user_id' => $data->users[1]->id, 'competency_id' => $data->competencies[1]->id],
            ['user_id' => $data->users[2]->id, 'competency_id' => $data->competencies[1]->id],
        ];
        $data->assign_users_to_competencies($to_assign);

        // Mark course completions
        foreach ([2, 3] as $user_idx) {
            $completion = new completion_completion(['course' => $data->courses[1]->id, 'userid' => $data->users[$user_idx]->id]);
            $completion->mark_complete();
        }

        // Now run the task
        (new competency_aggregation_all())->execute();

        $this->verify_item_records([
            ['item_id' => $data->courses[1]->id, 'user_id' => $data->users[1]->id, 'criterion_met' => 0],
            ['item_id' => $data->courses[1]->id, 'user_id' => $data->users[2]->id, 'criterion_met' => 1],
            ['item_id' => $data->courses[1]->id, 'user_id' => $data->users[3]->id, 'criterion_met' => 1],
            ['item_id' => $data->competencies[2]->id, 'user_id' => $data->users[1]->id, 'criterion_met' => 0],
            ['item_id' => $data->competencies[2]->id, 'user_id' => $data->users[2]->id, 'criterion_met' => 1],
        ]);

        $this->verify_pathway_achievements([
            [
                'pathway_id' => $pathways['child']->get_id(),
                'user_id' => $data->users[1]->id,
                'status' => pathway_achievement::STATUS_CURRENT,
                'scale_value_id' => null,
                'related_info' => [],
            ],
            [
                'pathway_id' => $pathways['child']->get_id(),
                'user_id' => $data->users[2]->id,
                'status' => pathway_achievement::STATUS_CURRENT,
                'scale_value_id' => $data->scalevalues[4]->id,
                'related_info' => ['coursecompletion'],
            ],
            [
                'pathway_id' => $pathways['child']->get_id(),
                'user_id' => $data->users[3]->id,
                'status' => pathway_achievement::STATUS_CURRENT,
                'scale_value_id' => $data->scalevalues[4]->id,
                'related_info' => ['coursecompletion'],
            ],
            [
                'pathway_id' => $pathways['parent']->get_id(),
                'user_id' => $data->users[1]->id,
                'status' => pathway_achievement::STATUS_CURRENT,
                'scale_value_id' => null,
                'related_info' => [],
            ],
            [
                'pathway_id' => $pathways['parent']->get_id(),
                'user_id' => $data->users[2]->id,
                'status' => pathway_achievement::STATUS_CURRENT,
                'scale_value_id' => $data->scalevalues[2]->id,
                'related_info' => ['childcompetency'],
            ],
        ]);

        $this->verify_competency_achievements([
            [
                'competency_id' => $data->competencies[2]->id,
                'user_id' => $data->users[1]->id,
                'status' => competency_achievement::ACTIVE_ASSIGNMENT,
                'scale_value_id' => null,
                'proficient' => 0,
            ],
            [
                'competency_id' => $data->competencies[2]->id,
                'user_id' => $data->users[2]->id,
                'status' => competency_achievement::ACTIVE_ASSIGNMENT,
                'scale_value_id' => $data->scalevalues[4]->id,
                'proficient' => 1,
            ],
            [
                'competency_id' => $data->competencies[2]->id,
                'user_id' => $data->users[3]->id,
                'status' => competency_achievement::ACTIVE_ASSIGNMENT,
                'scale_value_id' => $data->scalevalues[4]->id,
                'proficient' => 1,
            ],
            [
                'competency_id' => $data->competencies[1]->id,
                'user_id' => $data->users[1]->id,
                'status' => competency_achievement::ACTIVE_ASSIGNMENT,
                'scale_value_id' => null,
                'proficient' => 0,
            ],
            [
                'competency_id' => $data->competencies[1]->id,
                'user_id' => $data->users[2]->id,
                'status' => competency_achievement::ACTIVE_ASSIGNMENT,
                'scale_value_id' => $data->scalevalues[2]->id,
                'proficient' => 0,
            ],
        ]);
    }

    /**
     * Test competency_aggregate_all with manual pathway.
     */
    public function test_aggregation_all_task_single_manual() {
        $data = $this->setup_data();

        /** @var manual $pathway */
        $pathway = $data->competency_generator->create_manual($data->competencies[1], [manual::ROLE_MANAGER]);

        // Assign users
        $to_assign = [
            ['user_id' => $data->users[1]->id, 'competency_id' => $data->competencies[1]->id],
            ['user_id' => $data->users[2]->id, 'competency_id' => $data->competencies[1]->id],
            ['user_id' => $data->users[3]->id, 'competency_id' => $data->competencies[1]->id],
        ];
        $data->assign_users_to_competencies($to_assign);

        $ratings = [];
        // Manager gives rating
        $ratings[2] = $pathway->set_manual_value($data->users[2]->id,
            $data->users['manager']->id,
            manual::ROLE_MANAGER,
            $data->scalevalues[4]->id
        );
        $ratings[3] = $pathway->set_manual_value($data->users[3]->id,
            $data->users['manager']->id,
            manual::ROLE_MANAGER,
            $data->scalevalues[2]->id
        );

        // Now run the task
        (new competency_aggregation_all())->execute();

        $this->verify_item_records([]);

        $this->verify_pathway_achievements([
            [
                'pathway_id' => $pathway->get_id(),
                'user_id' => $data->users[1]->id,
                'status' => pathway_achievement::STATUS_CURRENT,
                'scale_value_id' => null,
                'related_info' => [],
            ],
            [
                'pathway_id' => $pathway->get_id(),
                'user_id' => $data->users[2]->id,
                'status' => pathway_achievement::STATUS_CURRENT,
                'scale_value_id' => $data->scalevalues[4]->id,
                'related_info' => ['rating_id' => $ratings[2]->id],
            ],
            [
                'pathway_id' => $pathway->get_id(),
                'user_id' => $data->users[3]->id,
                'status' => pathway_achievement::STATUS_CURRENT,
                'scale_value_id' => $data->scalevalues[2]->id,
                'related_info' => ['rating_id' =>$ratings[3]->id],
            ],
        ]);

        $this->verify_competency_achievements([
            [
                'competency_id' => $data->competencies[1]->id,
                'user_id' => $data->users[1]->id,
                'status' => competency_achievement::ACTIVE_ASSIGNMENT,
                'scale_value_id' => null,
                'proficient' => 0,
            ],
            [
                'competency_id' => $data->competencies[1]->id,
                'user_id' => $data->users[2]->id,
                'status' => competency_achievement::ACTIVE_ASSIGNMENT,
                'scale_value_id' => $data->scalevalues[4]->id,
                'proficient' => 1,
            ],
            [
                'competency_id' => $data->competencies[1]->id,
                'user_id' => $data->users[3]->id,
                'status' => competency_achievement::ACTIVE_ASSIGNMENT,
                'scale_value_id' => $data->scalevalues[2]->id,
                'proficient' => 0,
            ],
        ]);
    }





    private function verify_item_records(array $expected_rows) {
        global $DB;

        $sql =
            "SELECT tcir.*, tci.item_id
               FROM {totara_criteria_item_record} tcir
               JOIN {totara_criteria_item} tci
                 ON tci.id = tcir.criterion_item_id";
        $actual_rows = $DB->get_records_sql($sql, []);
        $this->assertSame(count($expected_rows), count($actual_rows));

        foreach ($actual_rows as $actual_row) {
            foreach ($expected_rows as $key => $expected_row) {
                if ((int)$actual_row->item_id == $expected_row['item_id'] && (int)$actual_row->user_id == $expected_row['user_id']) {
                    $this->assertEquals($expected_row['criterion_met'], $actual_row->criterion_met);
                    unset($expected_rows[$key]);
                    break;
                }
            }
        }

        $this->assertSame(0, count($expected_rows));
    }

    private function verify_pathway_achievements($expected_rows) {
        global $DB;

        $actual_rows = $DB->get_records('totara_competency_pathway_achievement');
        $this->assertSame(count($expected_rows), count($actual_rows));

        foreach ($actual_rows as $actual_row) {
            foreach ($expected_rows as $key => $expected_row) {
                if ((int)$actual_row->pathway_id == $expected_row['pathway_id']
                    && (int)$actual_row->user_id == $expected_row['user_id']
                    && (int)$actual_row->status == $expected_row['status']
                    && (int)$actual_row->scale_value_id == $expected_row['scale_value_id']
                    && (!isset($expected_row['related_info']) ||
                        $actual_row->related_info == json_encode($expected_row['related_info']))) {
                    unset($expected_rows[$key]);
                    break;
                }
            }
        }

        $this->assertSame(0, count($expected_rows));
    }

    private function verify_competency_achievements($expected_rows) {
        global $DB;

        $actual_rows = $DB->get_records('totara_competency_achievement', []);
        $this->assertSame(count($expected_rows), count($actual_rows));

        foreach ($actual_rows as $actual_row) {
            foreach ($expected_rows as $key => $expected_row) {
                if ((int)$actual_row->comp_id == $expected_row['competency_id']
                    && (int)$actual_row->user_id == $expected_row['user_id']
                    && (int)$actual_row->status == $expected_row['status']
                    && (int)$actual_row->scale_value_id == $expected_row['scale_value_id']
                    && (int)$actual_row->proficient == $expected_row['proficient']) {
                    unset($expected_rows[$key]);
                    break;
                }
            }
        }

        $this->assertSame(0, count($expected_rows));
    }
}
