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
use \totara_job\webapi\resolver\query;

/**
 * Tests the totara job assignments query resolver
 */
class totara_job_webapi_resolver_query_assignments_testcase extends advanced_testcase {

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
        $job =  $this->create_job_assignment([]);
        try {
            query\assignments::resolve(['userid' => $job->userid], $this->get_execution_context());
            self::fail('Expected a moodle_exception: cannot view job assignments');
        } catch (\moodle_exception $ex) {
            self::assertSame('Course or activity not accessible. (You are not logged in)', $ex->getMessage());
        }
    }

    public function test_resolve_guestuser() {
        $this->setGuestUser();
        $job =  $this->create_job_assignment([]);
        try {
            query\assignments::resolve(['userid' => $job->userid], $this->get_execution_context());
            self::fail('Expected a moodle_exception: cannot view job assignments');
        } catch (\moodle_exception $ex) {
            self::assertSame('Sorry, but you do not currently have permissions to do that (view job assignments)', $ex->getMessage());
        }
    }

    public function test_resolve_adminuser() {
        global $DB;

        $this->setAdminUser();

        $job1 =  $this->create_job_assignment([]);
        $result = query\assignments::resolve(['userid' => $job1->userid, 'idnumber' => 'job_1'], $this->get_execution_context());
        self::assertEquals([$job1->sortorder => $job1], $result);

        $job2 =  $this->create_job_assignment(['userid' => $job1->userid, 'idnumber' => 'job_2']);
        $result = query\assignments::resolve(['userid' => $job1->userid], $this->get_execution_context());
        self::assertEquals([$job1->sortorder => $job1, $job2->sortorder => $job2], $result);

        // Test no user provided.
        try {
            query\assignments::resolve([], $this->get_execution_context());
            self::fail('Expected a moodle_exception: no user argument provided');
        } catch (\moodle_exception $ex) {
            self::assertContains('A required parameter (userid) was missing', $ex->getMessage());
        }

        // Test invalid user id.
        try {
            query\assignments::resolve(['userid' => 0], $this->get_execution_context());
            self::fail('Expected a moodle_exception: cannot view job assignments');
        } catch (\dml_exception $ex) {
            self::assertContains('Can not find data record in database.', $ex->getMessage());
        }
        try {
            query\assignments::resolve(['userid' => '-2'], $this->get_execution_context());
            self::fail('Expected a moodle_exception: cannot view job assignments');
        } catch (\dml_exception $ex) {
            self::assertContains('Can not find data record in database.', $ex->getMessage());
        }

        // Test job assignment belonging to deleted user.
        delete_user($DB->get_record('user', ['id' => $job1->userid]));
        try {
            query\assignments::resolve(['userid' => $job1->userid], $this->get_execution_context());
            self::fail('Expected a moodle_exception: cannot view job assignments');
        } catch (\dml_exception $ex) {
            self::assertContains('Can not find data record in database.', $ex->getMessage());
        }
    }

    /**
     * Integration test of the AJAX query through the GraphQL stack.
     */
    public function test_ajax_query() {

        $this->setAdminUser();

        $user = $this->getDataGenerator()->create_user();
        $appraiser = $this->getDataGenerator()->create_user();
        $manager = $this->getDataGenerator()->create_user();
        $managerja = $this->create_job_assignment(['userid' => $manager->id, 'idnumber' => 'j1']);
        $job1 =  $this->create_job_assignment(['userid' => $user->id, 'idnumber' => 'j1']);
        $job2 =  $this->create_job_assignment(['userid' => $user->id, 'idnumber' => 'j2', 'managerjaid' => $managerja->id, 'appraiserid' => $appraiser->id]);

        $result = \totara_webapi\graphql::execute_operation(
            \core\webapi\execution_context::create('ajax', 'totara_job_assignments'),
            ['userid' => $user->id]
        );
        $expected = [
            'data' => [
                'totara_job_assignments' => [
                    [
                        'id' => $job1->id,
                        'fullname' => 'Unnamed job assignment (ID: j1)',
                        'idnumber' => 'j1',
                        'managerja' => null,
                        'appraiser' => null,
                    ],
                    [
                        'id' => $job2->id,
                        'fullname' => 'Unnamed job assignment (ID: j2)',
                        'idnumber' => 'j2',
                        'managerja' => [
                            'user' => [
                                'fullname' => fullname($manager)
                            ]
                        ],
                        'appraiser' => [
                            'fullname' => fullname($appraiser)
                        ],
                    ],
                ]
            ]
        ];
        self::assertSame(
            $expected,
            array_map(
                function($obj) {
                    return (array)$obj;
                },
                $result->toArray(\GraphQL\Error\Debug::INCLUDE_DEBUG_MESSAGE)
            )
        );

        $this->setUser($user);
        $result = \totara_webapi\graphql::execute_operation(
            \core\webapi\execution_context::create('ajax', 'totara_job_assignments'),
            ['userid' => $user->id]
        );
        self::assertSame(
            $expected,
            array_map(
                function($obj) {
                    return (array)$obj;
                },
                $result->toArray(\GraphQL\Error\Debug::INCLUDE_DEBUG_MESSAGE)
            )
        );

        // Invalid userid
        $result = \totara_webapi\graphql::execute_operation(
            \core\webapi\execution_context::create('ajax', 'totara_job_assignments'),
            ['userid' => 'apples']
        );
        $expected = [
            'errors' => [
                [
                    'message' => 'Internal server error',
                    'extensions' => [
                        'category' => 'internal'
                    ],
                    'locations' => [
                        [
                            'line' => 1,
                            'column' => 30
                        ]
                    ]
                ]
            ]
        ];
        self::assertSame(
            $expected,
            array_map(
                function($obj) {
                    return (array)$obj;
                },
                $result->toArray()
            )
        );

        // Missing userid
        $result = \totara_webapi\graphql::execute_operation(
            \core\webapi\execution_context::create('ajax', 'totara_job_assignments'),
            []
        );
        $expected = [
            'errors' => [
                [
                    'message' => 'Variable "$userid" of required type "core_id!" was not provided.',
                    'extensions' => [
                        'category' => 'graphql'
                    ],
                    'locations' => [
                        [
                            'line' => 1,
                            'column' => 30
                        ]
                    ]
                ]
            ]
        ];
        self::assertSame(
            $expected,
            array_map(
                function($obj) {
                    return (array)$obj;
                },
                $result->toArray(\GraphQL\Error\Debug::INCLUDE_DEBUG_MESSAGE)
            )
        );
    }
}