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
 * @author Fabian Derschatta <fabian.derschatta@totaralearning.com>
 * @package tassign_competency
 * @category test
 */

use tassign_competency\entities;
use totara_job\job_assignment;
use core\orm\query\table;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/assignment_actions_testcase.php');

class tassign_competency_actions_archive_testcase extends tassign_competency_assignment_actions_testcase {

    public function test_archiving_draft() {
        ['assignments' => $assignments] = $this->generate_assignments();

        $assignment1 = new entities\assignment($assignments[0]);
        $assignment1->status = entities\assignment::STATUS_DRAFT;
        $assignment1->save();

        $assignment2 = new entities\assignment($assignments[1]);
        $assignment2->status = entities\assignment::STATUS_DRAFT;
        $assignment2->save();

        $assignment3 = new entities\assignment($assignments[2]);
        $assignment3->status = entities\assignment::STATUS_ACTIVE;
        $assignment3->save();

        $this->expand();

        $model = new \tassign_competency\models\assignment_actions();
        $affected_ids = $model->archive([$assignment1->id, $assignment2->id]);
        $this->assertEmpty($affected_ids);

        $assignment1->refresh();
        $assignment2->refresh();
        $assignment3->refresh();

        // None got archived
        $this->assertEquals(entities\assignment::STATUS_DRAFT, $assignment1->status);
        $this->assertEquals(entities\assignment::STATUS_DRAFT, $assignment2->status);
        $this->assertEquals(entities\assignment::STATUS_ACTIVE, $assignment3->status);
    }

    public function test_archiving_single() {
        ['assignments' => $assignments] = $this->generate_assignments();

        $assignment1 = new entities\assignment($assignments[0]);
        $assignment1->status = entities\assignment::STATUS_ACTIVE;
        $assignment1->save();

        $assignment2 = new entities\assignment($assignments[1]);
        $assignment2->status = entities\assignment::STATUS_ACTIVE;
        $assignment2->save();

        $this->expand();

        $model = new \tassign_competency\models\assignment_actions();
        $affected_ids = $model->archive($assignment1->id);
        $this->assertEquals([$assignment1->id], $affected_ids);

        $assignment1->refresh();
        $assignment2->refresh();

        $this->assertEquals(entities\assignment::STATUS_ARCHIVED, $assignment1->status);
        // this one is untouched
        $this->assertEquals(entities\assignment::STATUS_ACTIVE, $assignment2->status);
    }

    public function test_archiving_active() {
        ['assignments' => $assignments] = $this->generate_assignments();

        $assignment1 = new entities\assignment($assignments[0]);
        $assignment1->status = entities\assignment::STATUS_ACTIVE;
        $assignment1->save();

        $assignment2 = new entities\assignment($assignments[1]);
        $assignment2->status = entities\assignment::STATUS_ACTIVE;
        $assignment2->save();

        $assignment3 = new entities\assignment($assignments[2]);
        $assignment3->status = entities\assignment::STATUS_ACTIVE;
        $assignment3->save();

        $this->expand();
        $this->assertEquals(3, entities\competency_assignment_user::repository()->count());

        $model = new \tassign_competency\models\assignment_actions();
        $affected_ids = $model->archive([$assignment1->id, $assignment2->id]);
        $this->assertEquals([$assignment1->id, $assignment2->id], $affected_ids);

        $assignment1->refresh();
        $assignment2->refresh();
        $assignment3->refresh();

        $this->assertEquals(entities\assignment::STATUS_ARCHIVED, $assignment1->status);
        $this->assertEquals(entities\assignment::STATUS_ARCHIVED, $assignment2->status);
        // this one is untouched
        $this->assertEquals(entities\assignment::STATUS_ACTIVE, $assignment3->status);

        // archived user records where cleaned up
        $this->assertEquals(1, entities\competency_assignment_user::repository()->count());
    }

    public function test_archiving_active_with_continued_tracking() {
        ['competencies' => $competencies, 'assignments' => $assignments] = $this->generate_assignments();

        $gen = $this->generator();
        $hierarchy_generator = $gen->hierarchy_generator();

        $user1 = $gen->create_user();
        $user2 = $gen->create_user();
        $user3 = $gen->create_user();

        $status = ['status' => entities\assignment::STATUS_ACTIVE];

        $fw = $hierarchy_generator->create_pos_frame(['fullname' => 'Pos Framework']);
        $pos = $hierarchy_generator->create_pos(['frameworkid' => $fw->id, 'fullname' => 'Position 1']);
        $pos_assignment = $gen->create_position_assignment($competencies[0]->id, $pos->id, $status);

        $job_data = [
            'userid' => $user1->id,
            'idnumber' => 'dev13',
            'fullname' => 'Developer',
            'positionid' => $pos->id
        ];
        job_assignment::create($job_data);

        $fw = $hierarchy_generator->create_org_frame(['fullname' => 'Org Framework']);
        $org = $hierarchy_generator->create_org(['frameworkid' => $fw->id, 'fullname' => 'Organisation 1']);
        $org_assignment = $gen->create_organisation_assignment($competencies[1]->id, $org->id, $status);

        $job_data = [
            'userid' => $user2->id,
            'idnumber' => 'dev13',
            'fullname' => 'Developer',
            'organisationid' => $org->id
        ];
        job_assignment::create($job_data);

        $cohort = $gen->create_cohort();
        $coh_assignment = $gen->create_cohort_assignment($competencies[2]->id, $cohort->id, $status);

        cohort_add_member($cohort->id, $user3->id);

        $assignment1 = new entities\assignment($assignments[0]);
        $assignment1->status = entities\assignment::STATUS_ACTIVE;
        $assignment1->save();

        $assignment2 = new entities\assignment($assignments[1]);
        $assignment2->status = entities\assignment::STATUS_ACTIVE;
        $assignment2->save();

        $assignment3 = new entities\assignment($assignments[2]);
        $assignment3->status = entities\assignment::STATUS_ACTIVE;
        $assignment3->save();

        $this->expand();
        $this->assertEquals(6, entities\competency_assignment_user::repository()->count());

        $expected_ids = [$assignment1->id, $pos_assignment->id, $org_assignment->id, $coh_assignment->id];

        $model = new \tassign_competency\models\assignment_actions();
        $affected_ids = $model->archive($expected_ids, true);
        sort($expected_ids);
        sort($affected_ids);
        $this->assertEquals($expected_ids, $affected_ids);

        // One user assignment is gone and for the other group ones new system assignments should have been created
        $this->assertEquals(5, entities\competency_assignment_user::repository()->count());
        // Make sure we have the expected assignments
        $this->assertEquals(2, entities\competency_assignment_user::repository()
            ->where(
                'assignment_id',
                [$assignment2->id, $assignment3->id]
            )
            ->count()
        );
        // New system assignments should have been created
        $this->assertEquals(
            3,
            entities\competency_assignment_user::repository()
                ->join((new table(entities\assignment::TABLE))->as('ass'), 'assignment_id', 'id')
                ->where('ass.type', entities\assignment::TYPE_SYSTEM)
                ->where('user_id', [$user1->id, $user2->id, $user3->id])
                ->count()
        );
    }

    public function test_archiving_active_without_continued_tracking() {
        ['competencies' => $competencies, 'assignments' => $assignments] = $this->generate_assignments();

        $gen = $this->generator();
        $hierarchy_generator = $gen->hierarchy_generator();

        $user1 = $gen->create_user();
        $user2 = $gen->create_user();
        $user3 = $gen->create_user();

        $status = ['status' => entities\assignment::STATUS_ACTIVE];

        $fw = $hierarchy_generator->create_pos_frame(['fullname' => 'Pos Framework']);
        $pos = $hierarchy_generator->create_pos(['frameworkid' => $fw->id, 'fullname' => 'Position 1']);
        $pos_assignment = $gen->create_position_assignment($competencies[0]->id, $pos->id, $status);

        $job_data = [
            'userid' => $user1->id,
            'idnumber' => 'dev13',
            'fullname' => 'Developer',
            'positionid' => $pos->id
        ];
        job_assignment::create($job_data);

        $fw = $hierarchy_generator->create_org_frame(['fullname' => 'Org Framework']);
        $org = $hierarchy_generator->create_org(['frameworkid' => $fw->id, 'fullname' => 'Organisation 1']);
        $org_assignment = $gen->create_organisation_assignment($competencies[1]->id, $org->id, $status);

        $job_data = [
            'userid' => $user2->id,
            'idnumber' => 'dev13',
            'fullname' => 'Developer',
            'organisationid' => $org->id
        ];
        job_assignment::create($job_data);

        $cohort = $gen->create_cohort();
        $coh_assignment = $gen->create_cohort_assignment($competencies[2]->id, $cohort->id, $status);

        cohort_add_member($cohort->id, $user3->id);

        $assignment1 = new entities\assignment($assignments[0]);
        $assignment1->status = entities\assignment::STATUS_ACTIVE;
        $assignment1->save();

        $assignment2 = new entities\assignment($assignments[1]);
        $assignment2->status = entities\assignment::STATUS_ACTIVE;
        $assignment2->save();

        $assignment3 = new entities\assignment($assignments[2]);
        $assignment3->status = entities\assignment::STATUS_ACTIVE;
        $assignment3->save();

        $this->expand();
        $this->assertEquals(6, entities\competency_assignment_user::repository()->count());

        $expected_ids = [$assignment1->id, $pos_assignment->id, $org_assignment->id, $coh_assignment->id];

        $model = new \tassign_competency\models\assignment_actions();
        $affected_ids = $model->archive($expected_ids, false);
        sort($expected_ids);
        sort($affected_ids);
        $this->assertEquals($expected_ids, $affected_ids);

        // User should be gone
        $this->assertEquals(2, entities\competency_assignment_user::repository()->count());
        // No new system assignments should have been created
        $this->assertEquals(0,  entities\assignment::repository()
                ->where('type', entities\assignment::TYPE_SYSTEM)
                ->count()
        );
    }

    public function test_archiving_mix() {
        ['assignments' => $assignments] = $this->generate_assignments();

        $assignment1 = new entities\assignment($assignments[0]);
        $assignment1->status = entities\assignment::STATUS_DRAFT;
        $assignment1->save();

        $assignment2 = new entities\assignment($assignments[1]);
        $assignment2->status = entities\assignment::STATUS_ACTIVE;
        $assignment2->save();

        $assignment3 = new entities\assignment($assignments[2]);
        $assignment3->status = entities\assignment::STATUS_ARCHIVED;
        $assignment3->save();

        $model = new \tassign_competency\models\assignment_actions();
        $affected_ids = $model->archive([$assignment1->id, $assignment2->id, $assignment3->id]);
        $this->assertEquals([$assignment2->id], $affected_ids);

        $assignment1->refresh();
        $assignment2->refresh();
        $assignment3->refresh();

        $this->assertEquals(entities\assignment::STATUS_DRAFT, $assignment1->status);
        $this->assertEquals(entities\assignment::STATUS_ARCHIVED, $assignment2->status);
        $this->assertEquals(entities\assignment::STATUS_ARCHIVED, $assignment3->status);
    }

    public function test_archiving_archived() {
        ['assignments' => $assignments] = $this->generate_assignments();

        $assignment1 = new entities\assignment($assignments[0]);
        $assignment1->status = entities\assignment::STATUS_ARCHIVED;
        $assignment1->save();

        $assignment2 = new entities\assignment($assignments[1]);
        $assignment2->status = entities\assignment::STATUS_ARCHIVED;
        $assignment2->save();

        $assignment3 = new entities\assignment($assignments[2]);
        $assignment3->status = entities\assignment::STATUS_ACTIVE;
        $assignment3->save();

        $model = new \tassign_competency\models\assignment_actions();
        $affected_ids = $model->archive([$assignment1->id, $assignment2->id]);
        $this->assertEmpty($affected_ids);

        $assignment1->refresh();
        $assignment2->refresh();
        $assignment3->refresh();

        $this->assertEquals(entities\assignment::STATUS_ARCHIVED, $assignment1->status);
        $this->assertEquals(entities\assignment::STATUS_ARCHIVED, $assignment2->status);
        // this one is untouched
        $this->assertEquals(entities\assignment::STATUS_ACTIVE, $assignment3->status);
    }

    private function expand() {
        // We need the expanded users for the logging to work
        $expand_task = new \tassign_competency\expand_task($GLOBALS['DB']);
        $expand_task->expand_all();
    }

}