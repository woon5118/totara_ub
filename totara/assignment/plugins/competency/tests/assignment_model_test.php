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

use tassign_competency\entities\assignment as assignment_entity;
use tassign_competency\entities\competency as competency_entity;
use tassign_competency\entities\competency_assignment_user;
use tassign_competency\expand_task;
use tassign_competency\models\assignment as assignment_model;
use tassign_competency\models\user_group\user;
use totara_assignment\user_groups;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/assignment_model_base_testcase.php');

class tassign_competency_assignment_model_testcase extends assignment_model_base_testcase {

    public function test_load_assignment() {
        $data = $this->create_data();

        $this->setUser($data->user1->id);

        $assignment = $this->create_active_user_assignment($data->comp1->id, $data->user1->id);

        $expected_assignment = assignment_entity::repository()->find($assignment->get_id());

        $loaded_assignment = assignment_model::load_by_entity($expected_assignment);
        $this->assertEquals($assignment, $loaded_assignment);

        $loaded_assignment = assignment_model::load_by_id($assignment->get_id());
        $this->assertEquals($assignment, $loaded_assignment);
    }

    public function test_load_user_assignments() {
        $data = $this->create_data();

        $this->setUser($data->user1->id);

        $assignment = $this->create_active_user_assignment($data->comp1->id, $data->user1->id);

        // another one for a different user
        $assignment2 = $this->create_active_user_assignment($data->comp1->id, $data->user2->id);

        $this->assertEquals(0, $assignment->get_assigned_users()->count());
        $this->assertEquals(0, $assignment2->get_assigned_users()->count());

        $task = new expand_task($GLOBALS['DB']);
        $task->expand_all();

        /** @var competency_assignment_user $user */
        $users = $assignment->get_assigned_users();
        $this->assertEquals(1, $users->count());
        $user = $users->first();
        $this->assertEquals($assignment->get_id(), $user->assignment_id);
        $this->assertEquals($data->user1->id, $user->user_id);
        $this->assertEquals($data->comp1->id, $user->competency_id);

        $users = $assignment2->get_assigned_users();
        $this->assertEquals(1, $users->count());
        $user = $users->first();
        $this->assertEquals($assignment2->get_id(), $user->assignment_id);
        $this->assertEquals($data->user2->id, $user->user_id);
        $this->assertEquals($data->comp1->id, $user->competency_id);
    }

    public function test_get_competency_from_assignment() {
        $data = $this->create_data();

        $this->setUser($data->user1->id);

        $assignment = $this->create_active_user_assignment($data->comp1->id, $data->user1->id);

        $comp = $assignment->get_competency();
        $this->assertInstanceOf(competency_entity::class, $comp);
        $this->assertEquals($data->comp1->id, $comp->id);
    }

    public function test_get_user_group() {
        $data = $this->create_data();

        $this->setUser($data->user1->id);

        $assignment = $this->create_active_user_assignment($data->comp1->id, $data->user1->id);

        $user_group = $assignment->get_user_group();
        $this->assertInstanceOf(user::class, $user_group);
        $this->assertEquals(user_groups::USER, $user_group->get_type());
        $this->assertEquals($data->user1->id, $user_group->get_id());
        $this->assertFalse($user_group->is_deleted());

        $this->assertEquals($user_group->get_name(), $assignment->get_user_group_name());
    }

    public function test_statuses() {
        $data = $this->create_data();

        $this->setUser($data->user1->id);

        $assignment = $this->create_active_user_assignment($data->comp1->id, $data->user1->id);

        /** @var assignment_entity $assignment_entity */
        $assignment_entity = assignment_entity::repository()->find($assignment->get_id());

        $this->assertEquals(assignment_entity::STATUS_ACTIVE, $assignment->get_status());
        $this->assertTrue($assignment->is_active());
        $this->assertFalse($assignment->is_draft());
        $this->assertFalse($assignment->is_archived());

        $assignment_entity->status = assignment_entity::STATUS_DRAFT;
        $assignment_entity->save();

        $assignment = assignment_model::load_by_id($assignment->get_id());
        $this->assertEquals(assignment_entity::STATUS_DRAFT, $assignment->get_status());
        $this->assertFalse($assignment->is_active());
        $this->assertTrue($assignment->is_draft());
        $this->assertFalse($assignment->is_archived());

        $assignment_entity->status = assignment_entity::STATUS_ARCHIVED;
        $assignment_entity->save();

        $assignment = assignment_model::load_by_id($assignment->get_id());
        $this->assertEquals(assignment_entity::STATUS_ARCHIVED, $assignment->get_status());
        $this->assertFalse($assignment->is_active());
        $this->assertFalse($assignment->is_draft());
        $this->assertTrue($assignment->is_archived());
    }

}