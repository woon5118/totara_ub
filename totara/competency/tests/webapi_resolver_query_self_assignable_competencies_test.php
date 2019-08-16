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
 * @package totara_competency
 * @subpackage test
 */

use core\webapi\execution_context;
use totara_competency\webapi\resolver\query\self_assignable_competencies;
use totara_job\job_assignment;

defined('MOODLE_INTERNAL') || die();


/**
 * Tests the query to fetch all self assignable competencies
 */
class totara_competency_webapi_resolver_query_self_assignable_competencies_testcase extends advanced_testcase {

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
        $this->assertIsArray($result);
        $this->assertCount(0, $result['items']);
        $this->assertEquals(0, $result['total']);
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
        $this->assertIsArray($result);
        $this->assertCount(0, $result['items']);
        $this->assertEquals(0, $result['total']);
    }

    /**
     * Get hierarchy specific generator
     *
     * @return tassign_competency_generator|component_generator_base
     */
    protected function generator() {
        return $this->getDataGenerator()->get_plugin_generator('tassign_competency');
    }

}