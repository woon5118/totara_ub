<?php
/**
 * This file is part of Totara Learn
 *
 * Copyright (C) 2020 onwards Totara Learning Solutions LTD
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
 * @author Kian Nguyen <kian.nguyen@totaralearning.com>
 * @package container_workspace
 */
defined('MOODLE_INTERNAL') || die();

use totara_webapi\phpunit\webapi_phpunit_helper;
use totara_userdata\userdata\target_user;
use container_workspace\userdata\workspace as user_data_workspace;

class container_workspace_webapi_get_workspace_testcase extends advanced_testcase {
    use webapi_phpunit_helper;

    /**
     * @return void
     */
    public function test_get_workspace_when_owner_is_null(): void {
        $generator = $this->getDataGenerator();
        $user_one = $generator->create_user();

        $this->setUser($user_one);

        /** @var container_workspace_generator $workspace_generator */
        $workspace_generator = $generator->get_plugin_generator('container_workspace');
        $workspace = $workspace_generator->create_workspace();

        // Log in as admin and delete the workspace.
        $this->setAdminUser();
        delete_user($user_one);

        $user_one = core_user::get_user($user_one->id);
        $target_user = new target_user($user_one);

        user_data_workspace::execute_purge($target_user, context_system::instance());

        // Now the workspace does not have any owner - test if we are able to fetch the workspace.
        $result = $this->execute_graphql_operation(
            'container_workspace_get_workspace',
            ['id' => $workspace->get_id()]
        );

        $this->assertEmpty($result->errors);
        $this->assertNotEmpty($result->data);
        $this->assertArrayHasKey('workspace', $result->data);

        $workspace_data = $result->data['workspace'];

        $this->assertArrayHasKey('owner', $workspace_data);
        $this->assertNull($workspace_data['owner']);

        $this->assertArrayHasKey('name', $workspace_data);
        $this->assertEquals($workspace->get_name(), $workspace_data['name']);

        $this->assertArrayHasKey('id', $workspace_data);
        $this->assertEquals($workspace->get_id(), $workspace_data['id']);
    }
}
