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
 * @subpackage test
 */

use core\orm\collection;
use core\webapi\execution_context;
use totara_competency\entities\competency as competency_entity;
use totara_competency\webapi\resolver\query\self_assignable_competencies;
use totara_job\job_assignment;

defined('MOODLE_INTERNAL') || die();


/**
 * Tests the totara job create assignment mutation
 */
class tassign_competency_webapi_resolver_query_self_assignable_competencies_testcase extends advanced_testcase {

    private function get_execution_context(string $type = 'dev', ?string $operation = null) {
        return execution_context::create($type, $operation);
    }

    private function get_args(array $args = []): array {
        return array_merge(
            [
                'user_id' => null,
                'filters' => [],
                'limit' => 0,
                'cursor' => null,
                'order_by' => null,
                'order_dir' => null
            ],
            $args
        );
    }

    public function test_returns_empty_result() {
        $generator = $this->getDataGenerator();

        $user1 = $generator->create_user();

        $this->setUser($user1);

        $args = $this->get_args(['user_id' => $user1->id]);

        $result = self_assignable_competencies::resolve($args, $this->get_execution_context());
        $this->assertInstanceOf(collection::class, $result);
        $this->assertCount(0, $result);
    }

    public function test_user_cannot_self_assign_without_permission() {
        global $DB;

        $generator = $this->getDataGenerator();

        $user1 = $generator->create_user();

        $user_role_id = $DB->get_record('role', ['shortname' => 'user'])->id;
        unassign_capability('tassign/competency:assignself', $user_role_id);

        $this->setUser($user1);

        $args = $this->get_args(['user_id' => $user1->id]);

        $this->expectException(required_capability_exception::class);

        self_assignable_competencies::resolve($args, $this->get_execution_context());
    }

    public function test_user_by_default_can_not_assign_to_other_user() {
        $generator = $this->getDataGenerator();

        $user1 = $generator->create_user();
        $user2 = $generator->create_user();

        $this->setUser($user1);

        $args = $this->get_args(['user_id' => $user2->id]);

        $this->expectException(required_capability_exception::class);

        self_assignable_competencies::resolve($args, $this->get_execution_context());
    }

    public function test_manager_can_assign_to_team_member() {
        $generator = $this->getDataGenerator();

        $user1 = $generator->create_user();
        $user2 = $generator->create_user();

        // User is now managing another user and can assign competencies for them
        $manager_job = job_assignment::create(['userid' => $user1->id, 'idnumber' => 1]);
        job_assignment::create(['userid' => $user2->id, 'idnumber' => 2, 'managerjaid' => $manager_job->id]);

        $this->setUser($user1);

        $args = $this->get_args(['user_id' => $user2->id]);

        $result = self_assignable_competencies::resolve($args, $this->get_execution_context());
        $this->assertInstanceOf(collection::class, $result);
    }

    public function test_user_can_assign_to_others_with_permission() {
        global $DB;

        $generator = $this->getDataGenerator();

        $user1 = $generator->create_user();
        $user2 = $generator->create_user();

        $user2_context = context_user::instance($user2->id);

        $user_role_id = $DB->get_record('role', ['shortname' => 'user'])->id;
        assign_capability('tassign/competency:assignother', CAP_ALLOW, $user_role_id, $user2_context->id);

        $this->setUser($user1);

        $args = $this->get_args(['user_id' => $user2->id]);

        $result = self_assignable_competencies::resolve($args, $this->get_execution_context());
        $this->assertInstanceOf(collection::class, $result);
    }

    public function test_only_competencies_with_self_assign_setting_are_returned() {
        global $DB;

        $generator = $this->getDataGenerator();
        /** @var tassign_competency_generator $assign_generator */
        $assign_generator = $generator->get_plugin_generator('tassign_competency');

        $fw = $assign_generator->hierarchy_generator()->create_comp_frame([]);

        $comp1 = $assign_generator->create_competency([
            'shortname' => 'c-chef',
            'fullname' => 'Chef proficiency',
            'description' => 'Bossing around',
            'idnumber' => 'cook-chef-c',
        ], $fw->id);

        $comp2 = $assign_generator->create_competency([
            'shortname' => 'c-chef',
            'fullname' => 'Chef proficiency',
            'description' => 'Bossing around',
            'idnumber' => 'cook-chef-c',
        ], $fw->id);

        $user1 = $generator->create_user();

        $this->setUser($user1);

        $args = $this->get_args(['user_id' => $user1->id]);

        // No competency has self assignment activated yet
        $result = self_assignable_competencies::resolve($args, $this->get_execution_context());
        $this->assertInstanceOf(collection::class, $result);
        $this->assertCount(0, $result);

        // Activate self assignment for the first competency
        $DB->insert_record(
            'comp_assign_availability',
            ['comp_id' => $comp1->id, 'availability' => competency_entity::ASSIGNMENT_CREATE_SELF]
        );

        $result = self_assignable_competencies::resolve($args, $this->get_execution_context());
        $this->assertInstanceOf(collection::class, $result);
        $this->assertCount(1, $result);
        $competency = $result->first();
        $this->assertEquals($comp1->id, $competency->id);

        // Activate self assignment for the second competency and check that it's in the result
        $DB->insert_record(
            'comp_assign_availability',
            ['comp_id' => $comp2->id, 'availability' => competency_entity::ASSIGNMENT_CREATE_SELF]
        );

        $result = self_assignable_competencies::resolve($args, $this->get_execution_context());
        $this->assertInstanceOf(collection::class, $result);
        $this->assertCount(2, $result);
        $expected_comp_ids = [$comp1->id, $comp2->id];
        $actual_comp_is = $result->pluck('id');
        $this->assertEqualsCanonicalizing($expected_comp_ids, $actual_comp_is);

        // Finally verify that the self assignment availability does not affect other users
        $user2 = $generator->create_user();

        $this->setUser($user1);

        // User is now managing another user and can assign competencies for them
        $manager_job = job_assignment::create(['userid' => $user1->id, 'idnumber' => 1]);
        job_assignment::create(['userid' => $user2->id, 'idnumber' => 2, 'managerjaid' => $manager_job->id]);

        $args = $this->get_args(['user_id' => $user2->id]);

        $result = self_assignable_competencies::resolve($args, $this->get_execution_context());
        $this->assertInstanceOf(collection::class, $result);
        $this->assertCount(0, $result);
    }

    public function test_only_competencies_with_other_assign_setting_are_returned() {
        global $DB;

        $generator = $this->getDataGenerator();
        /** @var tassign_competency_generator $assign_generator */
        $assign_generator = $generator->get_plugin_generator('tassign_competency');

        $fw = $assign_generator->hierarchy_generator()->create_comp_frame([]);

        $comp1 = $assign_generator->create_competency([
            'shortname' => 'c-chef',
            'fullname' => 'Chef proficiency',
            'description' => 'Bossing around',
            'idnumber' => 'cook-chef-c',
        ], $fw->id);

        $comp2 = $assign_generator->create_competency([
            'shortname' => 'c-chef',
            'fullname' => 'Chef proficiency',
            'description' => 'Bossing around',
            'idnumber' => 'cook-chef-c',
        ], $fw->id);

        $user1 = $generator->create_user();
        $user2 = $generator->create_user();

        // User is now managing another user and can assign competencies for them
        $manager_job = job_assignment::create(['userid' => $user1->id, 'idnumber' => 1]);
        job_assignment::create(['userid' => $user2->id, 'idnumber' => 2, 'managerjaid' => $manager_job->id]);

        $this->setUser($user1);

        $args = $this->get_args(['user_id' => $user2->id]);

        // No competency has other assignment activated yet
        $result = self_assignable_competencies::resolve($args, $this->get_execution_context());
        $this->assertInstanceOf(collection::class, $result);
        $this->assertCount(0, $result);

        // Activate other assignment for the first competency
        $DB->insert_record(
            'comp_assign_availability',
            ['comp_id' => $comp1->id, 'availability' => competency_entity::ASSIGNMENT_CREATE_OTHER]
        );

        $result = self_assignable_competencies::resolve($args, $this->get_execution_context());
        $this->assertInstanceOf(collection::class, $result);
        $this->assertCount(1, $result);
        $competency = $result->first();
        $this->assertEquals($comp1->id, $competency->id);

        // Activate self assignment for the second competency and check that it's in the result
        $DB->insert_record(
            'comp_assign_availability',
            ['comp_id' => $comp2->id, 'availability' => competency_entity::ASSIGNMENT_CREATE_OTHER]
        );

        $result = self_assignable_competencies::resolve($args, $this->get_execution_context());
        $this->assertInstanceOf(collection::class, $result);
        $this->assertCount(2, $result);
        $expected_comp_ids = [$comp1->id, $comp2->id];
        $actual_comp_is = $result->pluck('id');
        $this->assertEqualsCanonicalizing($expected_comp_ids, $actual_comp_is);

        // Now make sure that the other assignment does not affect self assignment
        $args = $this->get_args(['user_id' => $user1->id]);

        $result = self_assignable_competencies::resolve($args, $this->get_execution_context());
        $this->assertInstanceOf(collection::class, $result);
        $this->assertCount(0, $result);
    }

}