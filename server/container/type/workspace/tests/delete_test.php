<?php
/**
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
 * @author Qingyang Liu <qingyang.liu@totaralearning.com>
 * @package container_workspace
 */

use container_workspace\local\workspace_helper;
use container_workspace\tracker\tracker;
use core\webapi\execution_context;
use totara_core\advanced_feature;
use totara_webapi\graphql;
use totara_webapi\phpunit\webapi_phpunit_helper;

defined('MOODLE_INTERNAL') || die();

class container_workspace_delete_testcase extends advanced_testcase {
    private const MUTATION = 'container_workspace_delete_workspace';

    use webapi_phpunit_helper;

    /**
     * @return void
     */
    public function test_delete_workspace(): void {
        global $DB, $USER;
        $this->setAdminUser();

        $workspace = workspace_helper::create_workspace('Workspace 1010', $USER->id);

        $sql = '
            SELECT 1 FROM "ttr_course" c
            INNER JOIN "ttr_workspace" wo ON wo.course_id = c.id
            WHERE c.id = :course_id
        ';

        $this->assertTrue($DB->record_exists_sql($sql, ['course_id' => $workspace->id]));

        workspace_helper::delete_workspace($workspace, $USER->id);
        $this->assertFalse($DB->record_exists_sql($sql, ['course_id' => $workspace->id]));
    }

    /**
     * @return void
     */
    public function test_delete_workspace_via_graphql(): void {
        global $DB;
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        $workspace = workspace_helper::create_workspace('Workspace 1010',  $user->id);

        $tracker = new tracker($user->id);
        $tracker->visit_workspace($workspace);
        $this->assertEquals($workspace->id, $tracker->get_last_visit_workspace(), 'wrong visited workspace');

        $ec = execution_context::create('ajax', self::MUTATION);
        $result = graphql::execute_operation(
            $ec,
            ['workspace_id' => $workspace->get_id()]
        );

        $this->assertEmpty($result->errors);
        $this->assertNotEmpty($result->data);

        $sql = '
            SELECT 1 FROM "ttr_course" c
            INNER JOIN "ttr_workspace" wo ON wo.course_id = c.id
            WHERE c.id = :course_id
        ';

        $this->assertFalse($DB->record_exists_sql($sql, ['course_id' => $workspace->id]));
        $this->assertNull($tracker->get_last_visit_workspace(), 'wrong visited workspace');
    }

    /**
     * @return void
     */
    public function test_failed_graphql_call(): void {
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        $workspace = workspace_helper::create_workspace('Workspace 1010',  $user->id);
        $args = ['workspace_id' => $workspace->get_id()];

        $feature = 'container_workspace';
        advanced_feature::disable($feature);
        $result = $this->parsed_graphql_operation(self::MUTATION, $args);
        $this->assert_webapi_operation_failed($result, "Feature $feature is not available.");
        advanced_feature::enable($feature);

        $result = $this->parsed_graphql_operation(self::MUTATION, []);
        $this->assert_webapi_operation_failed($result, 'Variable "$workspace_id" of required type "param_integer!" was not provided.');

        $args['workspace_id'] = 1293;
        $result = $this->parsed_graphql_operation(self::MUTATION, $args);
        $this->assert_webapi_operation_failed($result, 'Can not find data record in database');

        $user1 = $this->getDataGenerator()->create_user();
        self::setUser($user1);
        $tracker = new tracker($user1->id);
        $tracker->visit_workspace($workspace);
        $this->assertEquals($workspace->id, $tracker->get_last_visit_workspace(), 'wrong visited workspace');

        $args = ['workspace_id' => $workspace->get_id()];
        $result = $this->parsed_graphql_operation(self::MUTATION, $args);
        $this->assert_webapi_operation_failed($result, 'The actor cannot delete the workspace');
        $this->assertEquals($workspace->id, $tracker->get_last_visit_workspace(), 'wrong visited workspace');
    }
}