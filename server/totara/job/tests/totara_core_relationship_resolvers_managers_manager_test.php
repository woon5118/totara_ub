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
 * @author Nathan Lewis <nathan.lewis@totaralearning.com>
 * @package totara_job
 */

use core\entity\tenant;
use totara_core\relationship\relationship;
use totara_core\relationship\relationship_resolver_dto;
use totara_job\job_assignment;
use totara_job\relationship\resolvers\managers_manager;

/**
 * @group totara_core_relationship
 * @covers \totara_job\relationship\resolvers\managers_manager
 */
class totara_job_totara_core_relationship_resolvers_managers_manager_test extends \advanced_testcase {

    private function create_data(): stdClass {
        $data = new stdClass();

        $data->managersmanager1 = self::getDataGenerator()->create_user();
        $data->managersmanager2 = self::getDataGenerator()->create_user();
        $data->managersmanager3 = self::getDataGenerator()->create_user();
        $data->managersmanager4 = self::getDataGenerator()->create_user();

        $data->manager1 = self::getDataGenerator()->create_user();
        $data->manager2 = self::getDataGenerator()->create_user();
        $data->manager3 = self::getDataGenerator()->create_user();
        $data->manager4 = self::getDataGenerator()->create_user();

        $data->user1 = self::getDataGenerator()->create_user();
        $data->user2 = self::getDataGenerator()->create_user();
        $data->user3 = self::getDataGenerator()->create_user();
        $data->user4 = self::getDataGenerator()->create_user();

        $data->managersmanager1_ja1 = job_assignment::create_default($data->managersmanager1->id);
        $data->managersmanager1_ja2 = job_assignment::create_default($data->managersmanager1->id);
        $data->managersmanager2_ja1 = job_assignment::create_default($data->managersmanager2->id);
        $data->managersmanager3_ja1 = job_assignment::create_default($data->managersmanager3->id);
        $data->managersmanager4_ja1 = job_assignment::create_default($data->managersmanager4->id);

        $data->manager1_ja1 = job_assignment::create_default(
            $data->manager1->id,
            ['managerjaid' => $data->managersmanager1_ja1->id]
        );
        $data->manager1_ja2 = job_assignment::create_default(
            $data->manager1->id,
            ['managerjaid' => $data->managersmanager2_ja1->id]
        );
        $data->manager2_ja1 = job_assignment::create_default(
            $data->manager2->id,
            ['managerjaid' => $data->managersmanager3_ja1->id]
        );
        $data->manager3_ja1 = job_assignment::create_default(
            $data->manager3->id
            // Manager 3 has no manager.
        );
        $data->manager4_ja1 = job_assignment::create_default(
            $data->manager3->id,
            ['managerjaid' => $data->managersmanager4_ja1->id]
        );

        $data->user1_ja1 = job_assignment::create_default($data->user1->id, ['managerjaid' => $data->manager1_ja1->id]);
        $data->user1_ja2 = job_assignment::create_default($data->user1->id, ['managerjaid' => $data->manager2_ja1->id]);
        $data->user2_ja1 = job_assignment::create_default($data->user2->id); // No manager.
        $data->user3_ja1 = job_assignment::create_default($data->user3->id, ['managerjaid' => $data->manager3_ja1->id]);
        $data->user4_ja1 = job_assignment::create_default($data->user4->id, ['managerjaid' => $data->manager4_ja1->id]);

        $relationship = relationship::load_by_idnumber('managers_manager');
        $data->resolver = new managers_manager($relationship);

        return $data;
    }

    public function test_get_users_from_job_assignment_id(): void {
        $data = $this->create_data();

        // Managersmanager1 is the manager's manager of user1 in ja1.
        $relationship_resolver_dtos = $data->resolver->get_users(
            ['job_assignment_id' => $data->user1_ja1->id],
            context_user::instance($data->user1->id)
        );
        $this->assertEquals(
            [$data->managersmanager1->id],
            relationship_resolver_dto::get_user_ids($relationship_resolver_dtos)
        );

        // Managersmanager3 is the manager's manager of user1 in ja2.
        $relationship_resolver_dtos = $data->resolver->get_users(
            ['job_assignment_id' => $data->user1_ja2->id],
            context_user::instance($data->user1->id)
        );
        $this->assertEquals(
            [$data->managersmanager3->id],
            relationship_resolver_dto::get_user_ids($relationship_resolver_dtos)
        );

        // User2 is not managed by anyone (they have no manager).
        $this->assertEquals(
            [],
            $data->resolver->get_users(
                ['job_assignment_id' => $data->user2_ja1->id],
                context_user::instance($data->user2->id)
            )
        );

        // User3 is not manager's managed by anyone (they have a manager, but no manager's manager).
        $this->assertEquals(
            [],
            $data->resolver->get_users(
                ['job_assignment_id' => $data->user3_ja1->id],
                context_user::instance($data->user3->id)
            )
        );
    }

    public function test_get_users_from_user_id(): void {
        $data = $this->create_data();

        // User1 has two manager's managers.
        $relationship_resolver_dtos = $data->resolver->get_users(
            ['user_id' => $data->user1->id],
            context_user::instance($data->user1->id)
        );
        $this->assertEqualsCanonicalizing(
            [$data->managersmanager1->id, $data->managersmanager3->id],
            relationship_resolver_dto::get_user_ids($relationship_resolver_dtos)
        );

        // User2 is not managed by anyone (they have no manager).
        $this->assertEquals(
            [],
            $data->resolver->get_users(
                ['user_id' => $data->user2->id],
                context_user::instance($data->user2->id)
            )
        );

        // User3 is not manager's managed by anyone (they have a manager, but no manager's manager).
        $this->assertEquals(
            [],
            $data->resolver->get_users(
                ['user_id' => $data->user3->id],
                context_user::instance($data->user3->id)
            )
        );
    }

    public function test_get_users_with_incorrect_attributes(): void {
        $data = $this->create_data();

        $data->resolver->get_users(
            ['job_assignment_id' => -1],
            context_user::instance($data->user1->id)
        );
        $data->resolver->get_users(
            ['user_id' => -1],
            context_user::instance($data->user1->id)
        );
        $data->resolver->get_users(
            ['job_assignment_id' => -1, 'user_id' => -1],
            context_user::instance($data->user1->id)
        );
        $data->resolver->get_users(
            ['job_assignment_id' => -1, 'incorrect attribute' => -1],
            context_user::instance($data->user1->id)
        );
        $data->resolver->get_users(
            ['user_id' => -1, 'incorrect attribute' => -1],
            context_user::instance($data->user1->id)
        );

        $this->expectException(coding_exception::class);
        $this->expectExceptionMessage(
            'The fields inputted into the ' . managers_manager::class . ' relationship resolver are invalid'
        );

        $data->resolver->get_users(
            ['incorrect attribute' => -1],
            context_user::instance($data->user1->id)
        );
    }

    public function test_get_users_with_no_attributes(): void {
        $data = $this->create_data();

        $this->expectException(coding_exception::class);
        $this->expectExceptionMessage(
            'The fields inputted into the ' . managers_manager::class . ' relationship resolver are invalid'
        );

        $data->resolver->get_users(
            [],
            context_user::instance($data->user1->id)
        );
    }

    public function test_get_users_with_multi_tenancy_enabled(): void {
        $generator = $this->getDataGenerator();

        /** @var totara_tenant_generator $tenant_generator */
        $tenant_generator = $generator->get_plugin_generator('totara_tenant');

        $tenant_generator->enable_tenants();

        $tenant1 = $tenant_generator->create_tenant();
        $tenant2 = $tenant_generator->create_tenant();

        $tenant1 = new tenant($tenant1);
        $tenant2 = new tenant($tenant2);

        $user1 = self::getDataGenerator()->create_user(['tenantid' => $tenant1->id]);
        $user2 = self::getDataGenerator()->create_user(['tenantid' => $tenant1->id]);
        $user2_manager = self::getDataGenerator()->create_user(['tenantid' => $tenant1->id]);
        $user3 = self::getDataGenerator()->create_user(['tenantid' => $tenant2->id]);
        $user3_manager = self::getDataGenerator()->create_user(['tenantid' => $tenant2->id]);
        $system_user = self::getDataGenerator()->create_user();
        $system_user_manager = self::getDataGenerator()->create_user();

        $user2managerja = job_assignment::create_default($user2_manager->id);
        $user3managerja = job_assignment::create_default($user3_manager->id);
        $system_user_manager_ja = job_assignment::create_default($system_user_manager->id);

        $user2ja = job_assignment::create_default($user2->id, ['managerjaid' => $user2managerja->id]);
        $user3ja = job_assignment::create_default($user3->id, ['managerjaid' => $user3managerja->id]);
        $system_user_ja = job_assignment::create_default($system_user->id, ['managerjaid' => $system_user_manager_ja->id]);

        job_assignment::create_default($user1->id, ['managerjaid' => $user2ja->id]);
        job_assignment::create_default($user1->id, ['managerjaid' => $user3ja->id]);

        $relationship = relationship::load_by_idnumber('managers_manager');
        $manager_resolver = new managers_manager($relationship);

        $users = $manager_resolver->get_users(
            ['user_id' => $user1->id],
            context_user::instance($user1->id)
        );

        $this->assertCount(1, $users);
        $dto = $users[0];
        $this->assertEquals($user2_manager->id, $dto->get_user_id());

        // If we pass the system context we should also get the manager from the other tenant
        $users = $manager_resolver->get_users(
            ['user_id' => $user1->id],
            context_system::instance()
        );

        $this->assertCount(2, $users);
        $actual_user_ids = [];
        foreach ($users as $user) {
            $actual_user_ids[] = $user->get_user_id();
        }
        $this->assertEqualsCanonicalizing(
            [$user2_manager->id, $user3_manager->id],
            $actual_user_ids
        );

        // Assign a manager who is in the system context
        job_assignment::create_default($user1->id, ['managerjaid' => $system_user_ja->id]);

        // We should still get only the ones in the same tenant if we pass a context which is in a tenant
        $users = $manager_resolver->get_users(
            ['user_id' => $user1->id],
            context_user::instance($user1->id)
        );

        $this->assertCount(1, $users);
        $dto = $users[0];
        $this->assertEquals($user2_manager->id, $dto->get_user_id());

        // If we pass the system context we should also get the system user
        $users = $manager_resolver->get_users(
            ['user_id' => $user1->id],
            context_system::instance()
        );

        $this->assertCount(3, $users);
        $actual_user_ids = [];
        foreach ($users as $user) {
            $actual_user_ids[] = $user->get_user_id();
        }
        $this->assertEqualsCanonicalizing(
            [$user2_manager->id, $user3_manager->id, $system_user_manager->id],
            $actual_user_ids
        );

        // Now with tenant isolation mode on
        set_config('tenantsisolated', 1);

        // If checked in system context we should only get NON-tenant users
        $users = $manager_resolver->get_users(
            ['user_id' => $user1->id],
            context_system::instance()
        );

        $this->assertCount(1, $users);
        $actual_user_ids = [];
        foreach ($users as $user) {
            $actual_user_ids[] = $user->get_user_id();
        }
        $this->assertEqualsCanonicalizing(
            [$system_user_manager->id],
            $actual_user_ids
        );

        // If inside a tenant we should get only tenant users
        $users = $manager_resolver->get_users(
            ['user_id' => $user1->id],
            context_user::instance($user1->id)
        );

        $this->assertCount(1, $users);
        $actual_user_ids = [];
        foreach ($users as $user) {
            $actual_user_ids[] = $user->get_user_id();
        }
        $this->assertEqualsCanonicalizing(
            [$user2_manager->id],
            $actual_user_ids
        );
    }

}
