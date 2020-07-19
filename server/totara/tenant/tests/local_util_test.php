<?php
/*
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
 * @author Petr Skoda <petr.skoda@totaralearning.com>
 * @package totara_tenant
 */

use totara_tenant\local\util;
use core\record\tenant;

defined('MOODLE_INTERNAL') || die();

/**
 * Tests covering tenant utility class.
 */
class totara_tenant_local_util_testcase extends advanced_testcase {
    public function test_is_valid_name() {
        /** @var totara_tenant_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('totara_tenant');
        $generator->enable_tenants();
        $this->setAdminUser();

        $tenant = $generator->create_tenant(['name' => 'pokus']);
        $this->assertTrue(util::is_valid_name('pokusnik', null));
        $this->assertTrue(util::is_valid_name('Hokus Pokus', null));
        $this->assertTrue(util::is_valid_name('pokus', $tenant->id));
        $this->assertTrue(util::is_valid_name('Pokus', $tenant->id));
        // Duplicates
        $this->assertSame('Tenant with the same name already exists', util::is_valid_name('pokus', null));
        $this->assertSame('Tenant with the same name already exists', util::is_valid_name('Pokus', null));
        // Invalid
        $this->assertSame('Required', util::is_valid_name('', null));
        $this->assertSame('Required', util::is_valid_name(' ', null));
        $this->assertSame('Invalid tenant name', util::is_valid_name('Hokus ', null));
        $this->assertSame('Invalid tenant name', util::is_valid_name('Pok<script>us', null));
    }

    public function test_is_valid_idnumber() {
        /** @var totara_tenant_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('totara_tenant');
        $generator->enable_tenants();
        $this->setAdminUser();

        $tenant = $generator->create_tenant(['idnumber' => 'pokus']);
        $this->assertTrue(util::is_valid_idnumber('pokusnik', null));
        $this->assertTrue(util::is_valid_idnumber('pokus', $tenant->id));
        // Duplicate
        $this->assertSame('Tenant with the same identifier already exists', util::is_valid_idnumber('pokus', null));
        // Invalid
        $this->assertSame('Required', util::is_valid_idnumber('', null));
        $this->assertSame('Required', util::is_valid_idnumber(' ', null));
        $this->assertSame('Invalid tenant identifier, use only lower case letters (a-z) and numbers', util::is_valid_idnumber('pokus ', null));
        $this->assertSame('Invalid tenant identifier, use only lower case letters (a-z) and numbers', util::is_valid_idnumber('Pokus', null));
        $this->assertSame('Invalid tenant identifier, use only lower case letters (a-z) and numbers', util::is_valid_idnumber('1a', null));
        $this->assertSame('Invalid tenant identifier, use only lower case letters (a-z) and numbers', util::is_valid_idnumber('a-b', null));
        $this->assertSame('Invalid tenant identifier, use only lower case letters (a-z) and numbers', util::is_valid_idnumber('a_b', null));
        $this->assertSame('Invalid tenant identifier, use only lower case letters (a-z) and numbers', util::is_valid_idnumber('šk', null));
    }

    public function test_create() {
        global $USER, $DB;
        $this->setAdminUser();

        /** @var totara_tenant_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('totara_tenant');
        $generator->enable_tenants();

        $data = new stdClass();
        $data->name = 'Prvni tenant';
        $data->idnumber = 'tenant1';
        $data->description = 'Nejaky tenant';
        $data->descriptionformat = FORMAT_MARKDOWN;
        $data->suspended = '0';
        $data->categoryname = 'Kategorie pro prvniho tenanta';
        $data->cohortname = 'Skupina pro prvniho tenanta';

        $this->setCurrentTimeStart();
        $tenant = util::create_tenant((array)$data);
        $this->assertInstanceOf(tenant::class, $tenant);
        $this->assertTrue($DB->record_exists('tenant', ['id' => $tenant->id]));
        $this->assertSame($data->name, $tenant->name);
        $this->assertSame($data->idnumber, $tenant->idnumber);
        $this->assertSame($data->description, $tenant->description);
        $this->assertSame($data->descriptionformat, $tenant->descriptionformat);
        $this->assertSame($data->suspended, $tenant->suspended);
        $this->assertTimeCurrent($tenant->timecreated);
        $this->assertSame($USER->id, $tenant->usercreated);

        $coursecat = $DB->get_record('course_categories', ['id' => $tenant->categoryid], '*', MUST_EXIST);
        $coursecatcontext = context_coursecat::instance($coursecat->id);
        $this->assertSame($data->categoryname, $coursecat->name);
        $this->assertSame('1', $coursecat->depth);
        $this->assertSame('0', $coursecat->parent);
        $this->assertSame('1', $coursecat->visible);
        $this->assertSame($tenant->id, $coursecatcontext->tenantid);

        $audience = $DB->get_record('cohort', ['id' => $tenant->cohortid], '*', MUST_EXIST);
        $this->assertSame($data->cohortname, $audience->name);
        $this->assertSame('1', $audience->cohorttype);
        $this->assertSame('1', $audience->visible);
        $this->assertSame('1', $audience->active);
        $this->assertSame('totara_tenant', $audience->component);
        $this->assertSame((string)$coursecatcontext->id, $audience->contextid);

        $data = new stdClass();
        $data->name = 'Druhy tenant';
        $data->idnumber = 'tenant2';
        $data->description = 'Nejaky jiny tenant';
        $data->descriptionformat = FORMAT_MARKDOWN;
        $data->suspended = '1';
        $data->categoryname = 'Kategorie pro druheho tenanta';
        $data->cohortname = 'Skupina pro druheho tenanta';

        $this->setCurrentTimeStart();
        $tenant = util::create_tenant((array)$data);
        $this->assertTrue($DB->record_exists('tenant', ['id' => $tenant->id]));
        $this->assertSame($data->name, $tenant->name);
        $this->assertSame($data->idnumber, $tenant->idnumber);
        $this->assertSame($data->description, $tenant->description);
        $this->assertSame($data->descriptionformat, $tenant->descriptionformat);
        $this->assertSame($data->suspended, $tenant->suspended);
        $this->assertTimeCurrent($tenant->timecreated);
        $this->assertSame($USER->id, $tenant->usercreated);

        $coursecat = $DB->get_record('course_categories', ['id' => $tenant->categoryid], '*', MUST_EXIST);
        $coursecatcontext = context_coursecat::instance($coursecat->id);
        $this->assertSame($data->categoryname, $coursecat->name);
        $this->assertSame('', $coursecat->description);
        $this->assertSame('1', $coursecat->depth);
        $this->assertSame('0', $coursecat->parent);
        $this->assertSame('0', $coursecat->visible);
        $this->assertSame($tenant->id, $coursecatcontext->tenantid);

        $audience = $DB->get_record('cohort', ['id' => $tenant->cohortid], '*', MUST_EXIST);
        $this->assertSame($data->cohortname, $audience->name);
        $this->assertSame('', $audience->description);
        $this->assertSame('1', $audience->cohorttype);
        $this->assertSame('0', $audience->visible);
        $this->assertSame('1', $audience->active);
        $this->assertSame('totara_tenant', $audience->component);
        $this->assertSame((string)$coursecatcontext->id, $audience->contextid);
    }

    public function test_update() {
        global $USER, $DB;
        $this->setAdminUser();

        /** @var totara_tenant_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('totara_tenant');
        $generator->enable_tenants();

        $data = new stdClass();
        $data->name = 'Prvni tenant';
        $data->idnumber = 'tenant1';
        $data->description = 'Nejaky tenant';
        $data->descriptionformat = FORMAT_MARKDOWN;
        $data->suspended = '1';
        $data->categoryname = 'Kategorie pro prvniho tenanta';
        $data->cohortname = 'Skupina pro prvniho tenanta';
        $oldtenant = util::create_tenant((array)$data);

        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        $data = new stdClass();
        $data->id = $oldtenant->id;
        $data->name = 'First tenant';
        $data->idnumber = 't1';
        $data->description = 'Some tennant';
        $data->descriptionformat = FORMAT_HTML;
        $data->suspended = '0';
        $data->categoryname = 'First tenant category';
        $data->cohortname = 'First tenant audience';
        $tenant = util::update_tenant((array)$data);

        $this->assertInstanceOf(tenant::class, $tenant);
        $this->assertSame($data->name, $tenant->name);
        $this->assertSame($data->idnumber, $tenant->idnumber);
        $this->assertSame($data->description, $tenant->description);
        $this->assertSame($data->descriptionformat, $tenant->descriptionformat);
        $this->assertSame($data->suspended, $tenant->suspended);
        $this->assertSame($oldtenant->timecreated, $tenant->timecreated);
        $this->assertSame($oldtenant->usercreated, $tenant->usercreated);

        $coursecat = $DB->get_record('course_categories', ['id' => $tenant->categoryid], '*', MUST_EXIST);
        $coursecatcontext = context_coursecat::instance($coursecat->id);
        $this->assertSame($data->categoryname, $coursecat->name);
        $this->assertSame('1', $coursecat->depth);
        $this->assertSame('0', $coursecat->parent);
        $this->assertSame('1', $coursecat->visible);
        $this->assertSame($tenant->id, $coursecatcontext->tenantid);

        $audience = $DB->get_record('cohort', ['id' => $tenant->cohortid], '*', MUST_EXIST);
        $this->assertSame($data->cohortname, $audience->name);
        $this->assertSame('1', $audience->cohorttype);
        $this->assertSame('0', $audience->visible);
        $this->assertSame('1', $audience->active);
        $this->assertSame('totara_tenant', $audience->component);
        $this->assertSame((string)$coursecatcontext->id, $audience->contextid);

        $data = new stdClass();
        $data->id = $oldtenant->id;
        $data->name = 'First tenant';
        $data->idnumber = 't1';
        $data->description = 'Some tennant';
        $data->descriptionformat = FORMAT_HTML;
        $data->suspended = '1';
        $data->categoryname = 'First tenant category';
        $data->cohortname = 'First tenant audience';
        $tenant = util::update_tenant((array)$data);

        $this->assertSame($data->name, $tenant->name);
        $this->assertSame($data->idnumber, $tenant->idnumber);
        $this->assertSame($data->description, $tenant->description);
        $this->assertSame($data->descriptionformat, $tenant->descriptionformat);
        $this->assertSame($data->suspended, $tenant->suspended);
        $this->assertSame($oldtenant->timecreated, $tenant->timecreated);
        $this->assertSame($oldtenant->usercreated, $tenant->usercreated);

        $coursecat = $DB->get_record('course_categories', ['id' => $tenant->categoryid], '*', MUST_EXIST);
        $coursecatcontext = context_coursecat::instance($coursecat->id);
        $this->assertSame($data->categoryname, $coursecat->name);
        $this->assertSame('1', $coursecat->depth);
        $this->assertSame('0', $coursecat->parent);
        $this->assertSame('0', $coursecat->visible);
        $this->assertSame($tenant->id, $coursecatcontext->tenantid);

        $audience = $DB->get_record('cohort', ['id' => $tenant->cohortid], '*', MUST_EXIST);
        $this->assertSame($data->cohortname, $audience->name);
        $this->assertSame('1', $audience->cohorttype);
        $this->assertSame('0', $audience->visible);
        $this->assertSame('1', $audience->active);
        $this->assertSame('totara_tenant', $audience->component);
        $this->assertSame((string)$coursecatcontext->id, $audience->contextid);
    }

    public function test_delete() {
        global $DB;

        /** @var totara_tenant_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('totara_tenant');
        $generator->enable_tenants();

        $this->setAdminUser();

        $tenant = $generator->create_tenant();
        $user = $this->getDataGenerator()->create_user(['tenantid' => $tenant->id]);
        $this->assertTrue($DB->record_exists('cohort_members', ['cohortid' => $tenant->cohortid, 'userid' => $user->id]));

        util::delete_tenant($tenant->id);
        $this->assertFalse($DB->record_exists('tenant', ['id' => $tenant->id]));

        $coursecat = $DB->get_record('course_categories', ['id' => $tenant->categoryid], '*', MUST_EXIST);
        $coursecatcontext = context_coursecat::instance($coursecat->id);
        $this->assertNull($coursecatcontext->tenantid);
        $this->assertSame('0', $coursecat->visible);

        $audience = $DB->get_record('cohort', ['id' => $tenant->cohortid], '*', MUST_EXIST);
        $this->assertSame('1', $audience->cohorttype);
        $this->assertSame('', $audience->component);

        $user = $DB->get_record('user', ['id' => $user->id], '*', MUST_EXIST);
        $usercontext = context_user::instance($user->id);
        $this->assertSame('0', $user->deleted);
        $this->assertSame('1', $user->suspended);
        $this->assertNull($user->tenantid);
        $this->assertNull($usercontext->tenantid);
        $this->assertTrue($DB->record_exists('cohort_members', ['cohortid' => $tenant->cohortid, 'userid' => $user->id]));
    }

    public function test_add_other_participant() {
        global $DB;

        /** @var totara_tenant_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('totara_tenant');
        $generator->enable_tenants();

        $this->setAdminUser();

        $tenant = $generator->create_tenant();
        $user = $this->getDataGenerator()->create_user();

        util::add_other_participant($tenant->id, $user->id);
        $this->assertNull($DB->get_field('user', 'tenantid', ['id' => $user->id]));
        $this->assertTrue($DB->record_exists('cohort_members', ['cohortid' => $tenant->cohortid, 'userid' => $user->id]));

        // Repeated adding should not cause problems.
        util::add_other_participant($tenant->id, $user->id);
        $this->assertTrue($DB->record_exists('cohort_members', ['cohortid' => $tenant->cohortid, 'userid' => $user->id]));

        // Make sure tenant members cannot be added as extra participants.
        $tenantuser = $this->getDataGenerator()->create_user(['tenantid' => $tenant->id]);
        $this->assertTrue($DB->record_exists('cohort_members', ['cohortid' => $tenant->cohortid, 'userid' => $tenantuser->id]));
        try {
            util::add_other_participant($tenant->id, $tenantuser->id);
            $this->fail('Exception expected');
        } catch (moodle_exception $ex) {
            $this->assertInstanceOf(coding_exception::class, $ex);
            $this->assertSame('Coding error detected, it must be fixed by a programmer: Only non-tenant users may be tenant participants', $ex->getMessage());
        }
        $this->assertTrue($DB->record_exists('cohort_members', ['cohortid' => $tenant->cohortid, 'userid' => $tenantuser->id]));
    }

    public function test_remove_other_participant() {
        global $DB;

        /** @var totara_tenant_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('totara_tenant');
        $generator->enable_tenants();

        $this->setAdminUser();

        $tenant = $generator->create_tenant();
        $user = $this->getDataGenerator()->create_user();

        util::add_other_participant($tenant->id, $user->id);
        $this->assertTrue($DB->record_exists('cohort_members', ['cohortid' => $tenant->cohortid, 'userid' => $user->id]));

        util::remove_other_participant($tenant->id, $user->id);
        $this->assertFalse($DB->record_exists('cohort_members', ['cohortid' => $tenant->cohortid, 'userid' => $user->id]));

        // Repeated removing should not cause problems.
        util::remove_other_participant($tenant->id, $user->id);
        $this->assertFalse($DB->record_exists('cohort_members', ['cohortid' => $tenant->cohortid, 'userid' => $user->id]));

        // Make sure tenant members cannot be removed from participants.
        $tenantuser = $this->getDataGenerator()->create_user(['tenantid' => $tenant->id]);
        $this->assertTrue($DB->record_exists('cohort_members', ['cohortid' => $tenant->cohortid, 'userid' => $tenantuser->id]));
        try {
            util::remove_other_participant($tenant->id, $tenantuser->id);
            $this->fail('Exception expected');
        } catch (moodle_exception $ex) {
            $this->assertInstanceOf(coding_exception::class, $ex);
            $this->assertSame('Coding error detected, it must be fixed by a programmer: Only non-tenant users may be tenant participants', $ex->getMessage());
        }
        $this->assertTrue($DB->record_exists('cohort_members', ['cohortid' => $tenant->cohortid, 'userid' => $tenantuser->id]));
    }

    public function test_get_user_participation() {
        /** @var totara_tenant_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('totara_tenant');
        $generator->enable_tenants();

        $this->setAdminUser();

        $tenant1 = $generator->create_tenant();
        $tenant2 = $generator->create_tenant();
        $tenant3 = $generator->create_tenant();

        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $user3 = $this->getDataGenerator()->create_user(['tenantid' => $tenant3->id]);
        $user4 = $this->getDataGenerator()->create_user();

        util::add_other_participant($tenant1->id, $user1->id);
        util::add_other_participant($tenant3->id, $user1->id);
        util::add_other_participant($tenant2->id, $user2->id);

        $result = util::get_user_participation($user1->id);
        $this->assertSame([$tenant1->id => $tenant1->id, $tenant3->id => $tenant3->id], $result);

        $result = util::get_user_participation($user2->id);
        $this->assertSame([$tenant2->id => $tenant2->id], $result);

        $result = util::get_user_participation($user3->id);
        $this->assertSame([$tenant3->id => $tenant3->id], $result);

        $result = util::get_user_participation($user4->id);
        $this->assertSame([], $result);
    }

    public function test_set_user_participation() {
        global $DB;

        /** @var totara_tenant_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('totara_tenant');
        $generator->enable_tenants();

        $this->setAdminUser();

        $tenant1 = $generator->create_tenant();
        $tenant2 = $generator->create_tenant();
        $tenant3 = $generator->create_tenant();

        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $user3 = $this->getDataGenerator()->create_user(['tenantid' => $tenant3->id]);
        $user4 = $this->getDataGenerator()->create_user(['tenantid' => $tenant3->id]);

        util::set_user_participation($user1->id, [$tenant1->id, $tenant3->id]);
        $result = util::get_user_participation($user1->id);
        $this->assertSame([$tenant1->id => $tenant1->id, $tenant3->id => $tenant3->id], $result);

        util::set_user_participation($user1->id, [$tenant2->id]);
        $result = util::get_user_participation($user1->id);
        $this->assertSame([$tenant2->id => $tenant2->id], $result);

        util::set_user_participation($user1->id, []);
        $result = util::get_user_participation($user1->id);
        $this->assertSame([], $result);
        $this->assertNull($DB->get_field('user', 'tenantid', ['id' => $user1->id]));

        util::set_user_participation($user3->id, [$tenant2->id]);
        $result = util::get_user_participation($user3->id);
        $this->assertSame([$tenant2->id => $tenant2->id], $result);
        $this->assertNull($DB->get_field('user', 'tenantid', ['id' => $user2->id]));

        util::set_user_participation($user4->id, []);
        $result = util::get_user_participation($user4->id);
        $this->assertSame([], $result);
        $this->assertNull($DB->get_field('user', 'tenantid', ['id' => $user4->id]));
    }

    public function test_migrate_user_to_tenant() {
        global $DB;

        /** @var totara_tenant_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('totara_tenant');
        $generator->enable_tenants();

        $this->setAdminUser();

        $tenant1 = $generator->create_tenant();
        $tenant2 = $generator->create_tenant();
        $tenant3 = $generator->create_tenant();

        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();

        util::migrate_user_to_tenant($user1->id, $tenant1->id);
        $result = util::get_user_participation($user1->id);
        $this->assertSame([$tenant1->id => $tenant1->id], $result);
        $this->assertSame($tenant1->id, $DB->get_field('user', 'tenantid', ['id' => $user1->id]));
        $context = context_user::instance($user1->id);
        $this->assertSame($tenant1->id, $context->tenantid);

        util::migrate_user_to_tenant($user1->id, $tenant2->id);
        $result = util::get_user_participation($user1->id);
        $this->assertSame([$tenant2->id => $tenant2->id], $result);
        $this->assertSame($tenant2->id, $DB->get_field('user', 'tenantid', ['id' => $user1->id]));
        $context = context_user::instance($user1->id);
        $this->assertSame($tenant2->id, $context->tenantid);

        util::set_user_participation($user2->id, [$tenant1->id, $tenant3->id]);
        util::migrate_user_to_tenant($user2->id, $tenant2->id);
        $result = util::get_user_participation($user2->id);
        $this->assertSame([$tenant2->id => $tenant2->id], $result);
        $this->assertSame($tenant2->id, $DB->get_field('user', 'tenantid', ['id' => $user2->id]));
        $context = context_user::instance($user2->id);
        $this->assertSame($tenant2->id, $context->tenantid);

        $admin = get_admin();
        try {
            util::migrate_user_to_tenant($admin->id, $tenant1->id);
            $this->fail('Exception expected');
        } catch (moodle_exception $e) {
            $this->assertInstanceOf(coding_exception::class, $e);
            $this->assertSame('Coding error detected, it must be fixed by a programmer: Admins cannot be migrated to tenant members', $e->getMessage());
        }
    }
}
