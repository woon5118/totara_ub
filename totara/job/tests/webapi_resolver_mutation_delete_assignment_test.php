<?php
/*
 * This file is part of Totara LMS
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
 * @author Sam Hemelryk <sam.hemelryk@totaralearning.com>
 * @package totara_job
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/totara/job/lib.php');

use \totara_job\job_assignment;
use \totara_job\webapi\resolver\mutation;

/**
 * Tests the totara job delete assignment mutation
 */
class totara_job_webapi_resolver_mutation_delete_assignment_testcase extends advanced_testcase {

    private function get_execution_context(string $type = 'dev', ?string $operation = null) {
        return \core\webapi\execution_context::create($type, $operation);
    }

    private function create_job_assignment($data) {
        $data = (array)$data;
        if (!array_key_exists('userid', $data)) {
            $user = $this->getDataGenerator()->create_user();
            $data['userid'] = $user->id;
        }
        if (!array_key_exists('idnumber', $data)) {
            $data['idnumber'] = 'job_x';
        }
        $instance = job_assignment::create($data);
        return $instance;
    }

    public function test_resolve_nologgedin() {
        $job1 = $this->create_job_assignment([]);
        $job2 = $this->create_job_assignment(['userid' => $job1->userid, 'idnumber' => 'j2']);
        try {
            mutation\delete_assignment::resolve(['userid' => $job1->userid, 'assignmentid' => $job2->id], $this->get_execution_context());
            self::fail('Expected a moodle_exception: cannot view job assignments');
        } catch (\moodle_exception $ex) {
            self::assertSame('Course or activity not accessible. (You are not logged in)', $ex->getMessage());
        }
    }

    public function test_resolve_guestuser() {
        $this->setGuestUser();

        $job1 = $this->create_job_assignment([]);
        $job2 = $this->create_job_assignment(['userid' => $job1->userid, 'idnumber' => 'j2']);
        try {
            mutation\delete_assignment::resolve(['userid' => $job1->userid, 'assignmentid' => $job2->id], $this->get_execution_context());
            self::fail('Expected a moodle_exception: cannot view job assignments');
        } catch (\coding_exception $ex) {
            self::assertContains('No permission to delete job assignments', $ex->getMessage());
        }
    }

    public function test_resolve_normaluser() {
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        $job1 = $this->create_job_assignment([]);
        $job2 = $this->create_job_assignment(['userid' => $job1->userid, 'idnumber' => 'j2']);
        try {
            mutation\delete_assignment::resolve(['userid' => $job1->userid, 'assignmentid' => $job2->id], $this->get_execution_context());
            self::fail('Expected a moodle_exception: cannot view job assignments');
        } catch (\coding_exception $ex) {
            self::assertContains('No permission to delete job assignments', $ex->getMessage());
        }
    }

    public function test_resolve_adminuser() {
        global $DB;

        $this->setAdminUser();

        $job1 = $this->create_job_assignment(['idnumber' => 'j1']);
        $job2 = $this->create_job_assignment(['userid' => $job1->userid, 'idnumber' => 'j2']);

        self::assertCount(2, job_assignment::get_all($job1->userid));
        self::assertTrue(mutation\delete_assignment::resolve(['assignmentid' => $job2->id, 'userid' => $job1->userid], $this->get_execution_context()));
        self::assertCount(1, job_assignment::get_all($job1->userid));


        // User defaults to current user, who doesn't have this job.
        try {
            mutation\delete_assignment::resolve(['assignmentid' => $job1->id], $this->get_execution_context());
            $this->fail('Exception expected.');
        } catch (\moodle_exception $ex) {
            self::assertContains('Given Job Assignment does not belong to the given user.', $ex->getMessage());
        }

        // Incorrect Job ids
        try {
            mutation\delete_assignment::resolve(['userid' => $job1->userid, 'assignmentid' => 16], $this->get_execution_context());
            $this->fail('Exception expected.');
        } catch (\moodle_exception $ex) {
            self::assertContains('Can not find data record in database', $ex->getMessage());
        }

        // Test job assignment belonging to deleted user.
        delete_user($DB->get_record('user', ['id' => $job1->userid]));
        try {
            mutation\delete_assignment::resolve(['userid' => $job1->userid, 'assignmentid' => $job1->id], $this->get_execution_context());
            self::fail('Expected a moodle_exception: cannot view job assignments');
        } catch (\dml_exception $ex) {
            self::assertContains('Can not find data record in database', $ex->getMessage());
        }
    }

    /**
     * Integration test of the AJAX mutation through the GraphQL stack.
     */
    public function test_ajax_query() {
        global $DB;

        $this->setAdminUser();

        $user = $this->getDataGenerator()->create_user();
        $job1 =  $this->create_job_assignment(['userid' => $user->id, 'idnumber' => 'j1']);
        $job2 =  $this->create_job_assignment(['userid' => $user->id, 'idnumber' => 'j2']);
        $job3 =  $this->create_job_assignment(['userid' => $user->id, 'idnumber' => 'j3']);

        $map = function($obj) {
            return (array)$obj;
        };
        $result = \totara_webapi\graphql::execute_operation(
            \core\webapi\execution_context::create('ajax', 'totara_job_delete_assignment'),
            ['userid' => $user->id, 'assignmentid' => $job3->id]
        );
        self::assertSame(
            ['data' => ['totara_job_delete_assignment' => true]],
            array_map($map, $result->toArray(true))
        );
        self::assertEquals([$job1->id, $job2->id], array_keys($DB->get_records('job_assignment', ['userid' => $job1->userid], 'sortorder ASC', 'id')));

        $result = \totara_webapi\graphql::execute_operation(
            \core\webapi\execution_context::create('ajax', 'totara_job_delete_assignment'),
            ['userid' => $user->id, 'assignmentid' => $job2->id]
        );
        self::assertSame(
            ['data' => ['totara_job_delete_assignment' => true]],
            array_map($map, $result->toArray(true))
        );
        self::assertEquals([$job1->id], array_keys($DB->get_records('job_assignment', ['userid' => $job1->userid], 'sortorder ASC', 'id')));

        $result = \totara_webapi\graphql::execute_operation(
            \core\webapi\execution_context::create('ajax', 'totara_job_delete_assignment'),
            ['userid' => $user->id, 'assignmentid' => $job1->id]
        );
        self::assertSame(
            ['data' => ['totara_job_delete_assignment' => true]],
            array_map($map, $result->toArray(true))
        );
        self::assertEquals([], array_keys($DB->get_records('job_assignment', ['userid' => $job1->userid], 'sortorder ASC', 'id')));
    }

}