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
 * Tests the totara job create assignment mutation
 */
class totara_job_webapi_resolver_mutation_create_assignment_testcase extends advanced_testcase {

    private function get_execution_context(string $type = 'dev', ?string $operation = null) {
        return \core\webapi\execution_context::create($type, $operation);
    }

    public function test_resolve_nologgedin() {
        $this->resetAfterTest();
        $user = $this->getDataGenerator()->create_user();

        try {
            mutation\create_assignment::resolve(['userid' => $user->id, 'idnumber' => 'j1'], $this->get_execution_context());
            self::fail('Expected a moodle_exception: cannot view job assignments');
        } catch (\moodle_exception $ex) {
            self::assertSame('Course or activity not accessible. (You are not logged in)', $ex->getMessage());
        }
    }

    public function test_resolve_guestuser() {
        $this->resetAfterTest();
        $this->setGuestUser();
        $user = $this->getDataGenerator()->create_user();

        try {
            mutation\create_assignment::resolve(['userid' => $user->id, 'idnumber' => 'j1'], $this->get_execution_context());
            self::fail('Expected a moodle_exception: cannot view job assignments');
        } catch (\coding_exception $ex) {
            self::assertContains('No permission to create job assignments.', $ex->getMessage());
        }
    }

    public function test_resolve_normaluser() {
        $this->resetAfterTest();
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        try {
            mutation\create_assignment::resolve(['userid' => $user->id, 'idnumber' => 'j1'], $this->get_execution_context());
            self::fail('Expected a moodle_exception: cannot view job assignments');
        } catch (\coding_exception $ex) {
            self::assertContains('No permission to create job assignments.', $ex->getMessage());
        }
    }

    public function test_resolve_adminuser() {
        $this->resetAfterTest();
        $this->setAdminUser();
        $user = $this->getDataGenerator()->create_user();
        $manager = $this->getDataGenerator()->create_user();
        $appraiser = $this->getDataGenerator()->create_user();

        /** @var totara_hierarchy_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('totara_hierarchy');
        $framework = $generator->create_pos_frame([]);
        $typeid = $generator->create_pos_type([]);
        $position1 = $generator->create_pos(['frameworkid' => $framework->id, 'typeid' => $typeid]);
        $framework = $generator->create_org_frame([]);
        $typeid = $generator->create_org_type([]);
        $organisation1 = $generator->create_org(['frameworkid' => $framework->id, 'typeid' => $typeid]);

        // Starting with none.
        self::assertCount(0, job_assignment::get_all($user->id));

        // Create a basic job.
        $jaid = mutation\create_assignment::resolve(['userid' => $user->id, 'idnumber' => 'j1'], $this->get_execution_context());
        self::assertIsNumeric($jaid);
        self::assertCount(1, job_assignment::get_all($user->id));
        $job = job_assignment::get_with_id($jaid);
        self::assertInstanceOf(job_assignment::class, $job);
        self::assertSame($job->userid, $user->id);
        self::assertSame($job->idnumber, 'j1');

        $now = time();
        $managerjaid = mutation\create_assignment::resolve(['userid' => $manager->id, 'idnumber' => 'jm1'], $this->get_execution_context());

        // Create a job with lots of detail.
        $ja2id = mutation\create_assignment::resolve([
            'userid' => $user->id,
            'idnumber' => 'j2',
            'fullname' => 'Test & test',
            'shortname' => 'Te&Te',
            'description' => '<p>This is a <strong>complex</strong> test</p>',
            'positionid' => $position1->id,
            'organisationid' => $organisation1->id,
            'startdate' => $now - 86400,
            'enddate' => $now + 86400,
            'managerjaid' => $managerjaid,
            'appraiserid' => $appraiser->id
        ], $this->get_execution_context());
        self::assertIsNumeric($ja2id);
        self::assertCount(2, job_assignment::get_all($user->id));
        $job2 = job_assignment::get_with_id($ja2id);
        self::assertInstanceOf(job_assignment::class, $job);
        self::assertSame($job2->userid, $user->id);
        self::assertSame($job2->idnumber, 'j2');
        self::assertSame($job2->fullname, 'Test & test');
        self::assertSame($job2->shortname, 'Te&Te');
        self::assertSame($job2->description, '<p>This is a <strong>complex</strong> test</p>');
        self::assertSame($job2->positionid, $position1->id);
        self::assertSame($job2->organisationid, $organisation1->id);
        self::assertEquals($job2->startdate, $now - 86400);
        self::assertEquals($job2->enddate, $now + 86400);
        self::assertSame($job2->managerjaid, $managerjaid);
        self::assertSame($job2->appraiserid, $appraiser->id);

        // No userid.
        try {
            mutation\create_assignment::resolve(['idnumber' => 'j1'], $this->get_execution_context());
            $this->fail('Exception expected.');
        } catch (\moodle_exception $ex) {
            self::assertContains('A required parameter (userid) was missing (idnumber)', $ex->getMessage());
        }

        // Duplicate id number.
        try {
            mutation\create_assignment::resolve(['userid' => $job->userid, 'idnumber' => $job->idnumber], $this->get_execution_context());
            $this->fail('Exception expected.');
        } catch (\moodle_exception $ex) {
            self::assertContains('Tried to create job assignment idnumber which is not unique for this user', $ex->getMessage());
        }
    }

    /**
     * Integration test of the AJAX mutation through the GraphQL stack.
     */
    public function test_ajax_query() {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        $user = $this->getDataGenerator()->create_user();
        $appraiser = $this->getDataGenerator()->create_user();
        $manager = $this->getDataGenerator()->create_user();
        $managerja = job_assignment::create(['userid' => $manager->id, 'idnumber' => 'j1']);
        /** @var totara_hierarchy_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('totara_hierarchy');
        $orgframework = $generator->create_org_frame([]);
        $organisation = $generator->create_org(['frameworkid' => $orgframework->id, 'typeid' => $generator->create_org_type([])]);
        $posframework = $generator->create_pos_frame([]);
        $position = $generator->create_pos(['frameworkid' => $posframework->id, 'typeid' => $generator->create_pos_type([])]);

        $map = function($obj) {
            return (array)$obj;
        };

        $result = \totara_webapi\graphql::execute_operation(
            \core\webapi\execution_context::create('ajax', 'totara_job_create_assignment'),
            [
                'userid' => $user->id,
                'idnumber' => 'j1'
            ]
        );
        $job = $DB->get_record('job_assignment', ['userid' => $user->id, 'idnumber' => 'j1'], '*', IGNORE_MISSING);
        self::assertSame(
            ['data' => ['totara_job_create_assignment' => $job->id]],
            array_map($map, $result->toArray(true))
        );
        self::assertSame(null, $job->fullname);
        self::assertSame(null, $job->shortname);
        self::assertSame(null, $job->description);
        self::assertSame(null, $job->positionid);
        self::assertSame(null, $job->organisationid);
        self::assertSame(null, $job->startdate);
        self::assertSame(null, $job->enddate);
        self::assertSame(null, $job->managerjaid);
        self::assertSame(null, $job->tempmanagerjaid);
        self::assertSame(null, $job->tempmanagerexpirydate);
        self::assertSame(null, $job->appraiserid);
        self::assertSame('0', $job->totarasync);

        $now = time();
        $result = \totara_webapi\graphql::execute_operation(
            \core\webapi\execution_context::create('ajax', 'totara_job_create_assignment'),
            [
                'userid' => $user->id,
                'idnumber' => 'j2',
                'fullname' => 'Test fullname',
                'shortname' => 'Test shortname',
                'description' => '<p>Test description</p>',
                'positionid' => $position->id,
                'organisationid' => $organisation->id,
                'startdate' => $now - 86400,
                'enddate' => $now + 86400,
                'managerjaid' => $managerja->id,
                'appraiserid' => $appraiser->id
            ]
        );
        $job = $DB->get_record('job_assignment', ['userid' => $user->id, 'idnumber' => 'j2'], '*', IGNORE_MISSING);
        self::assertSame(
            ['data' => ['totara_job_create_assignment' => $job->id]],
            array_map($map, $result->toArray(true))
        );
        self::assertSame('Test fullname', $job->fullname);
        self::assertSame('Test shortname', $job->shortname);
        self::assertSame('<p>Test description</p>', $job->description);
        self::assertSame($position->id, $job->positionid);
        self::assertSame($organisation->id, $job->organisationid);
        self::assertSame((string)($now - 86400), $job->startdate);
        self::assertSame((string)($now + 86400), $job->enddate);
        self::assertSame((string)$managerja->id, $job->managerjaid);
        self::assertSame(null, $job->tempmanagerjaid);
        self::assertSame(null, $job->tempmanagerexpirydate);
        self::assertSame((string)$appraiser->id, $job->appraiserid);
        self::assertSame('0', $job->totarasync);
    }
}