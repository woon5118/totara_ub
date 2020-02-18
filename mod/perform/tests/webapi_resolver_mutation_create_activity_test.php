<?php
/*
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
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Samantha Jayasinghe <samantha.jayasinghe@totaralearning.com>
 * @package mod_perform
 */

/**
 * @group perform
 */

use mod_perform\webapi\resolver\mutation\create_activity;
use core\webapi\execution_context;

class webapi_resolver_mutation_create_activity_testcase extends advanced_testcase {

    private function get_execution_context(string $type = 'dev', ?string $operation = null) {
        return execution_context::create($type, $operation);
    }

    public function test_create_activity() {
        $this->setAdminUser();
        $args = [
            'name'        => "Mid year performance review",
            'description' => "Test Description",
        ];

        $result = create_activity::resolve($args, $this->get_execution_context());
        $this->assertSame('Mid year performance review', $result->get_entity()->name);
        $this->assertSame('Test Description', $result->get_entity()->description);
    }


    public function test_create_activity_for_non_admin_user() {
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);
        $args = [
            'name'        => "Mid year performance review",
            'description' => "Test Description",
        ];

        $this->expectException(\container_perform\create_exception::class);
        $this->expectExceptionMessage("You do not have the permission to create a performance activity");
        create_activity::resolve($args, $this->get_execution_context());
    }

    public function test_create_activity_with_empty_name() {
        $this->setAdminUser();
        $args = [
            'name'        => "",
            'description' => "Test Description",
        ];
        $this->expectException(\container_perform\create_exception::class);
        $this->expectExceptionMessage("You are not allowed to create an activity with an empty name");
        create_activity::resolve($args, $this->get_execution_context());
    }

    public function test_create_activity_with_empty_description() {
        $this->setAdminUser();
        $args = [
            'name'        => "Mid year performance review",
            'description' => "",
        ];
        $result = create_activity::resolve($args, $this->get_execution_context());
        $this->assertSame('Mid year performance review', $result->get_entity()->name);
        $this->assertSame('', $result->get_entity()->description);

        $args = [
            'name'        => "Mid year performance review",
            'description' => null,
        ];
        $result = create_activity::resolve($args, $this->get_execution_context());
        $this->assertSame('Mid year performance review', $result->get_entity()->name);
        $this->assertNull($result->get_entity()->description);
    }
}