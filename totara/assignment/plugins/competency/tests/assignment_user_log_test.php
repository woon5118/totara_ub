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

use tassign_competency\models\assignment_actions;
use tassign_competency\models\assignment_user_log;
use tassign_competency\entities;
use totara_job\job_assignment;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/assignment_actions_testcase.php');

class tassign_competency_user_log_testcase extends tassign_competency_assignment_actions_testcase {

    public function test_log() {
        $log = new assignment_user_log(1, 2);
        $log->log_assign();

        $this->assertTrue(entities\competency_assignment_user_log::repository()
            ->where('assignment_id', 1)
            ->where('user_id', 2)
            ->where('action', entities\competency_assignment_user_log::ACTION_ASSIGNED)
            ->exists()
        );

        $log->log_unassign_user_group();

        $this->assertTrue(entities\competency_assignment_user_log::repository()
            ->where('assignment_id', 1)
            ->where('user_id', 2)
            ->where('action', entities\competency_assignment_user_log::ACTION_UNASSIGNED_USER_GROUP)
            ->exists()
        );

        $log->log_archive();

        $this->assertTrue(entities\competency_assignment_user_log::repository()
            ->where('assignment_id', 1)
            ->where('user_id', 2)
            ->where('action', entities\competency_assignment_user_log::ACTION_UNASSIGNED_ARCHIVED)
            ->exists()
        );
    }

    public function test_log_actions() {
        ['assignments' => $assignments] = $this->generate_assignments();

        $model = new assignment_actions();

        $assignment1 = new entities\assignment($assignments[0]);
        $assignment1->status = entities\assignment::STATUS_DRAFT;
        $assignment1->save();

        $assignment2 = new entities\assignment($assignments[1]);
        $assignment2->status = entities\assignment::STATUS_ACTIVE;
        $assignment2->save();

        // ACTIVATE
        $model->activate($assignment1->id);
        $this->expand();

        // Expand should create one log record
        $this->assertEquals(1, entities\competency_assignment_user_log::repository()
            ->where('assignment_id', $assignment1->id)
            ->count()
        );
        $this->assertEquals(1, entities\competency_assignment_user_log::repository()
            ->where('assignment_id', $assignment2->id)
            ->count()
        );

        /** @var entities\competency_assignment_user_log $log */
        $log = entities\competency_assignment_user_log::repository()
            ->where('assignment_id', $assignment1->id)
            ->order_by('id', 'desc')
            ->first();

        $this->assertNotEmpty($log);
        $this->assertEquals($assignment1->id, $log->assignment_id);
        $this->assertEquals($assignment1->user_group_id, $log->user_id);
        $this->assertEquals(entities\competency_assignment_user_log::ACTION_ASSIGNED, $log->action);
        $this->assertGreaterThan(0, $log->created_at);

        // ARCHIVE
        $model->archive($assignment1->id);

        // Archive should create another log entry
        $this->assertEquals(3, entities\competency_assignment_user_log::repository()->count());

        /** @var entities\competency_assignment_user_log $log */
        $log = entities\competency_assignment_user_log::repository()
            ->where('assignment_id', $assignment1->id)
            ->order_by('id', 'desc')
            ->first();

        $this->assertNotEmpty($log);
        $this->assertEquals($assignment1->id, $log->assignment_id);
        $this->assertEquals($assignment1->user_group_id, $log->user_id);
        $this->assertEquals(entities\competency_assignment_user_log::ACTION_UNASSIGNED_ARCHIVED, $log->action);
        $this->assertGreaterThan(0, $log->created_at);

        // DELETE
        $model->delete($assignment1->id);

        // Records for assignment should be gone
        $this->assertEquals(0, entities\competency_assignment_user::repository()
            ->where('assignment_id', $assignment1->id)
            ->count()
        );
        $this->assertEquals(0, entities\competency_assignment_user_log::repository()
            ->where('assignment_id', $assignment1->id)
            ->count()
        );

        // Control record should still be there
        $this->assertEquals(1, entities\competency_assignment_user::repository()->count());
        $this->assertEquals(1, entities\competency_assignment_user_log::repository()->count());
    }

    public function test_log_user_added_to_user_group() {
        ['competencies' => $competencies] = $this->generate_assignments();

        $hierarchy_generator = $this->generator()->hierarchy_generator();
        $fw = $hierarchy_generator->create_pos_frame(['fullname' => 'Framework 2']);
        $pos = $hierarchy_generator->create_pos(['frameworkid' => $fw->id, 'fullname' => 'Position 1']);
        $user = $this->generator()->create_user();

        $job_data = [
            'userid' => $user->id,
            'idnumber' => 'dev1',
            'fullname' => 'Developer',
            'positionid' => $pos->id
        ];
        job_assignment::create($job_data);

        $record = $this->generator()->create_position_assignment(
            $competencies[0]->id,
            $pos->id,
            ['status' => entities\assignment::STATUS_ACTIVE]
        );
        $assignment = new entities\assignment($record);

        $this->assertEquals(0, entities\competency_assignment_user_log::repository()->count());

        $this->expand();

        $this->assertEquals(1, entities\competency_assignment_user::repository()->count());
        $this->assertEquals(1, entities\competency_assignment_user_log::repository()->count());

        /** @var entities\competency_assignment_user_log $log */
        $log = entities\competency_assignment_user_log::repository()->one();

        $this->assertNotEmpty($log);
        $this->assertEquals($assignment->id, $log->assignment_id);
        $this->assertEquals($user->id, $log->user_id);
        $this->assertEquals(entities\competency_assignment_user_log::ACTION_ASSIGNED, $log->action);
        $this->assertGreaterThan(0, $log->created_at);

        $job_assignment = job_assignment::get_first($user->id);
        job_assignment::delete($job_assignment);

        $this->expand();

        // User got unassigned
        $this->assertEquals(0, entities\competency_assignment_user::repository()
            ->join(entities\assignment::TABLE, 'assignment_id', 'id')
            ->where(entities\assignment::TABLE.'.type', entities\assignment::TYPE_ADMIN)
            ->count()
        );
        $this->assertEquals(2, entities\competency_assignment_user_log::repository()
            ->where('assignment_id', $assignment->id)
            ->count()
        );

        /** @var entities\competency_assignment_user_log $log */
        $log = entities\competency_assignment_user_log::repository()
            ->where('assignment_id', $assignment->id)
            ->order_by('id', 'desc')
            ->first();

        $this->assertNotEmpty($log);
        $this->assertEquals($assignment->id, $log->assignment_id);
        $this->assertEquals($user->id, $log->user_id);
        $this->assertEquals(entities\competency_assignment_user_log::ACTION_UNASSIGNED_USER_GROUP, $log->action);
        $this->assertGreaterThan(0, $log->created_at);
    }

    public function test_log_draft() {
        ['assignments' => $assignments] = $this->generate_assignments();

        $assignment1 = new entities\assignment($assignments[0]);
        $assignment1->status = entities\assignment::STATUS_DRAFT;
        $assignment1->save();

        // Expand and check that there are the expected records in the assignment user table
        $this->expand();
        $this->assertEquals(0, entities\competency_assignment_user::repository()->count());
        $this->assertEquals(0, entities\competency_assignment_user_log::repository()->count());

        $model = new assignment_actions();
        $model->delete($assignment1->id);

        $this->assertEquals(0, entities\competency_assignment_user::repository()->count());
        $this->assertEquals(0, entities\competency_assignment_user_log::repository()->count());
    }

    private function expand() {
        // We need the expanded users for the logging to work
        $expand_task = new \tassign_competency\expand_task($GLOBALS['DB']);
        $expand_task->expand_all();
    }


}