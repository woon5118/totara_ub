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

use container_workspace\member\member;
use core\entity\user_enrolment;

class container_workspace_multi_tenancy_join_workspace_as_tenant_participant_testcase extends advanced_testcase {
    /**
     * @var stdClass|null
     */
    private $tenant_one_user;

    /**
     * @var stdClass|null
     */
    private $tenant_two_user;

    /**
     * @var stdClass|null
     */
    private $tenant_one_participant;

    /**
     * @var stdClass|null
     */
    private $system_user;

    /**
     * @return void
     */
    protected function setUp(): void {
        $generator = self::getDataGenerator();

        /** @var totara_tenant_generator $tenant_generator */
        $tenant_generator = $generator->get_plugin_generator('totara_tenant');
        $tenant_generator->enable_tenants();

        $tenant_one = $tenant_generator->create_tenant();
        $tenant_two = $tenant_generator->create_tenant();

        $this->tenant_one_user = $generator->create_user([
            'firstname' => uniqid('tenant_one_user_'),
            'lastname' => uniqid('tenant_one_use_'),
            'tenantid' => $tenant_one->id
        ]);

        $this->tenant_two_user = $generator->create_user([
            'firstname' => uniqid('tenant_two_user_'),
            'lastname' => uniqid('tenant_two_user_'),
            'tenantid' => $tenant_two->id
        ]);

        $this->system_user = $generator->create_user([
            'firstname' => uniqid('system_user_'),
            'lastname ' => uniqid('system_user_')
        ]);

        $this->tenant_one_participant = $generator->create_user([
            'firstname' => uniqid('tenant_one_participant_'),
            'lastname' => uniqid('tenant_one_participant_')
        ]);

        $tenant_generator->set_user_participation($this->tenant_one_participant->id, [$tenant_one->id]);
    }

    /**
     * @return void
     */
    protected function tearDown(): void {
        $this->tenant_one_user = null;
        $this->tenant_two_user = null;
        $this->tenant_one_participant = null;
        $this->system_user = null;
    }

    /**
     * @return container_workspace_generator
     */
    private function get_workspace_generator(): container_workspace_generator {
        $generator = self::getDataGenerator();

        /** @var container_workspace_generator $workspace_generator */
        $workspace_generator = $generator->get_plugin_generator('container_workspace');
        return $workspace_generator;
    }

    /**
     * @return void
     */
    public function test_join_system_user_workspace_with_isolation(): void {
        global $DB;

        set_config('tenantsisolated', 1);
        $workspace_generator = $this->get_workspace_generator();

        $this->setUser($this->system_user);
        $workspace = $workspace_generator->create_workspace();

        $member = member::join_workspace($workspace, $this->tenant_one_participant->id);

        self::assertEquals($this->tenant_one_participant->id, $member->get_user_id());
        self::assertTrue($DB->record_exists(user_enrolment::TABLE, ['id' => $member->get_id()]));
    }

    /**
     * @return void
     */
    public function test_join_system_user_workspace_without_isolation(): void {
        global $DB;
        $workspace_generator = $this->get_workspace_generator();

        $this->setUser($this->system_user);
        $workspace = $workspace_generator->create_workspace();

        $member = member::join_workspace($workspace, $this->tenant_one_participant->id);

        self::assertEquals($this->tenant_one_participant->id, $member->get_user_id());
        self::assertTrue($DB->record_exists(user_enrolment::TABLE, ['id' => $member->get_id()]));
    }

    /**
     * @return void
     */
    public function test_join_tenant_one_workspace_with_isolation(): void {
        global $DB;
        set_config('tenantsisolated', 1);

        $workspace_generator = $this->get_workspace_generator();
        $this->setUser($this->tenant_one_user);

        $workspace = $workspace_generator->create_workspace();
        $member = member::join_workspace($workspace, $this->tenant_one_participant->id);

        self::assertEquals($this->tenant_one_participant->id, $member->get_user_id());
        self::assertTrue($DB->record_exists(user_enrolment::TABLE, ['id' => $member->get_id()]));
    }

    /**
     * @return void
     */
    public function test_join_tenant_one_workspace_without_isolation(): void {
        global $DB;

        $workspace_generator = $this->get_workspace_generator();
        $this->setUser($this->tenant_one_user);

        $workspace = $workspace_generator->create_workspace();
        $member = member::join_workspace($workspace, $this->tenant_one_participant->id);

        self::assertEquals($this->tenant_one_participant->id, $member->get_user_id());
        self::assertTrue($DB->record_exists(user_enrolment::TABLE, ['id' => $member->get_id()]));
    }

    /**
     * @return void
     */
    public function test_join_tenant_two_workspace_with_isolation(): void {
        set_config('tenantsisolated', 1);
        $workspace_generator = $this->get_workspace_generator();

        $this->setUser($this->tenant_two_user);
        $workspace = $workspace_generator->create_workspace();

        $this->expectException(coding_exception::class);
        $this->expectExceptionMessage("Cannot join the workspace that is not in the same tenant");

        member::join_workspace($workspace, $this->tenant_one_participant->id);
    }

    /**
     * @return void
     */
    public function test_join_tenant_two_workspace_without_isolation(): void {
        $workspace_generator = $this->get_workspace_generator();
        $this->setUser($this->tenant_two_user);

        $workspace = $workspace_generator->create_workspace();

        // To tenant two user, this tenant participant is only a system user.
        $this->expectException(coding_exception::class);
        $this->expectExceptionMessage("Cannot join the workspace that is not in the same tenant");

        member::join_workspace($workspace, $this->tenant_one_participant->id);
    }
}