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

defined('MOODLE_INTERNAL') || die();

/**
 * Tests covering tenant generator.
 */
class totara_tenant_generator_testcase extends advanced_testcase {
    public function test_enable_tenants() {
        global $CFG;

        /** @var totara_tenant_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('totara_tenant');

        $this->assertSame('0', $CFG->tenantsenabled);
        $generator->enable_tenants();
        $this->assertSame('1', $CFG->tenantsenabled);
    }

    public function test_disable_tenants() {
        global $CFG;

        /** @var totara_tenant_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('totara_tenant');

        $generator->enable_tenants();
        $this->assertSame('1', $CFG->tenantsenabled);
        $generator->disable_tenants();
        $this->assertSame('0', $CFG->tenantsenabled);
    }

    public function test_create_tenant() {
        global $DB, $USER;

        /** @var totara_tenant_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('totara_tenant');
        $generator->enable_tenants();

        $this->setAdminUser();

        $this->setCurrentTimeStart();
        $tenant = $generator->create_tenant(null);
        $coursecat = $DB->get_record('course_categories', ['id' => $tenant->categoryid], '*', MUST_EXIST);
        $audience = $DB->get_record('cohort', ['id' => $tenant->cohortid], '*', MUST_EXIST);
        $this->assertTrue($DB->record_exists('tenant', ['id' => $tenant->id]));
        $this->assertRegExp('/^Tenant \d+$/', $tenant->name);
        $this->assertRegExp('/^tenantidnumber\d+$/', $tenant->idnumber);
        $this->assertSame('', $tenant->description);
        $this->assertSame(FORMAT_HTML, $tenant->descriptionformat);
        $this->assertSame('0', $tenant->suspended);
        $this->assertTimeCurrent($tenant->timecreated);
        $this->assertSame($USER->id, $tenant->usercreated);
        $this->assertRegExp('/^Tenant \d+ category$/', $coursecat->name);
        $this->assertSame('', $coursecat->description);
        $this->assertSame($tenant->idnumber, $coursecat->idnumber);
        $this->assertRegExp('/^Tenant \d+ audience/', $audience->name);
        $this->assertSame('', $audience->description);
        $this->assertSame($audience->idnumber, $audience->idnumber);

        $data = new stdClass();
        $data->name = 'Prvni tenant';
        $data->idnumber = 'tenant1';
        $data->description = 'Nejaky tenant';
        $data->descriptionformat = FORMAT_MARKDOWN;
        $data->suspended = '1';
        $data->categoryname = 'Kategorie pro prvniho tenanta';
        $data->cohortname = 'Skupina pro prvniho tenanta';
        $this->setCurrentTimeStart();
        $tenant = $generator->create_tenant($data);
        $coursecat = $DB->get_record('course_categories', ['id' => $tenant->categoryid], '*', MUST_EXIST);
        $audience = $DB->get_record('cohort', ['id' => $tenant->cohortid], '*', MUST_EXIST);
        $this->assertTrue($DB->record_exists('tenant', ['id' => $tenant->id]));
        $this->assertSame($data->name, $tenant->name);
        $this->assertSame($data->idnumber, $tenant->idnumber);
        $this->assertSame($data->description, $tenant->description);
        $this->assertSame($data->descriptionformat, $tenant->descriptionformat);
        $this->assertSame($data->suspended, $tenant->suspended);
        $this->assertTimeCurrent($tenant->timecreated);
        $this->assertSame($USER->id, $tenant->usercreated);
        $this->assertSame($data->categoryname, $coursecat->name);
        $this->assertSame('', $coursecat->description);
        $this->assertSame($tenant->idnumber, $coursecat->idnumber);
        $this->assertSame($data->cohortname, $audience->name);
        $this->assertSame('', $audience->description);
        $this->assertSame($audience->idnumber, $audience->idnumber);
    }

    public function test_create_user() {
        global $DB;

        /** @var totara_tenant_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('totara_tenant');
        $generator->enable_tenants();

        $tenantusermanagerrole = $DB->get_record('role', ['shortname' => 'tenantusermanager']);
        $tenantdomainmanagerrole = $DB->get_record('role', ['shortname' => 'tenantdomainmanager']);

        $tenant1 = $generator->create_tenant(null);
        $tenant2 = $generator->create_tenant(null);
        $tenant3 = $generator->create_tenant(null);

        $this->setAdminUser();

        $data = ['tenantid' => $tenant1->id];
        $user1 = $this->getDataGenerator()->create_user($data);
        $usercontext = context_user::instance($user1->id);
        $this->assertSame($tenant1->id, $user1->tenantid);
        $this->assertSame($tenant1->id, $usercontext->tenantid);
        $this->assertTrue($DB->record_exists('cohort_members', ['cohortid' => $tenant1->cohortid, 'userid' => $user1->id]));
        $this->assertSame(1, $DB->count_records('cohort_members', ['cohortid' => $tenant1->cohortid]));

        $data = ['tenantmember' => $tenant2->idnumber];
        $user2 = $this->getDataGenerator()->create_user($data);
        $usercontext = context_user::instance($user2->id);
        $this->assertSame($tenant2->id, $user2->tenantid);
        $this->assertSame($tenant2->id, $usercontext->tenantid);
        $this->assertTrue($DB->record_exists('cohort_members', ['cohortid' => $tenant2->cohortid, 'userid' => $user2->id]));
        $this->assertSame(1, $DB->count_records('cohort_members', ['cohortid' => $tenant2->cohortid]));

        $user3 = $this->getDataGenerator()->create_user();
        $usercontext = context_user::instance($user3->id);
        $this->assertNull($user3->tenantid);
        $this->assertNull($usercontext->tenantid);

        $data = ['tenantparticipant' => "{$tenant1->idnumber}, {$tenant3->idnumber}"];
        $user4 = $this->getDataGenerator()->create_user($data);
        $usercontext = context_user::instance($user4->id);
        $this->assertNull($user4->tenantid);
        $this->assertNull($usercontext->tenantid);
        $this->assertTrue($DB->record_exists('cohort_members', ['cohortid' => $tenant1->cohortid, 'userid' => $user4->id]));
        $this->assertTrue($DB->record_exists('cohort_members', ['cohortid' => $tenant3->cohortid, 'userid' => $user4->id]));
        $this->assertSame(2, $DB->count_records('cohort_members', ['cohortid' => $tenant1->cohortid]));
        $this->assertSame(1, $DB->count_records('cohort_members', ['cohortid' => $tenant2->cohortid]));
        $this->assertSame(1, $DB->count_records('cohort_members', ['cohortid' => $tenant3->cohortid]));

        $functionroleid = function ($ra) {
            return $ra->roleid;
        };

        $data = ['tenantusermanager' => $tenant2->idnumber . ', '. $tenant3->idnumber];
        $user5 = $this->getDataGenerator()->create_user($data);
        $result = get_user_roles(context_tenant::instance($tenant1->id), $user5->id, false);
        $this->assertSame([], array_values(array_map($functionroleid, $result)));
        $result = get_user_roles(context_tenant::instance($tenant2->id), $user5->id, false);
        $this->assertSame([$tenantusermanagerrole->id], array_values(array_map($functionroleid, $result)));
        $result = get_user_roles(context_tenant::instance($tenant3->id), $user5->id, false);
        $this->assertSame([$tenantusermanagerrole->id], array_values(array_map($functionroleid, $result)));
        $result = get_user_roles(context_coursecat::instance($tenant1->categoryid), $user5->id, false);
        $this->assertSame([], array_values(array_map($functionroleid, $result)));
        $result = get_user_roles(context_coursecat::instance($tenant2->categoryid), $user5->id, false);
        $this->assertSame([], array_values(array_map($functionroleid, $result)));
        $result = get_user_roles(context_coursecat::instance($tenant3->categoryid), $user5->id, false);
        $this->assertSame([], array_values(array_map($functionroleid, $result)));

        $data = ['tenantdomainmanager' => $tenant1->idnumber . ', '. $tenant3->idnumber];
        $user5 = $this->getDataGenerator()->create_user($data);
        $result = get_user_roles(context_tenant::instance($tenant1->id), $user5->id, false);
        $this->assertSame([], array_values(array_map($functionroleid, $result)));
        $result = get_user_roles(context_tenant::instance($tenant2->id), $user5->id, false);
        $this->assertSame([], array_values(array_map($functionroleid, $result)));
        $result = get_user_roles(context_tenant::instance($tenant3->id), $user5->id, false);
        $this->assertSame([], array_values(array_map($functionroleid, $result)));
        $result = get_user_roles(context_coursecat::instance($tenant1->categoryid), $user5->id, false);
        $this->assertSame([$tenantdomainmanagerrole->id], array_values(array_map($functionroleid, $result)));
        $result = get_user_roles(context_coursecat::instance($tenant2->categoryid), $user5->id, false);
        $this->assertSame([], array_values(array_map($functionroleid, $result)));
        $result = get_user_roles(context_coursecat::instance($tenant3->categoryid), $user5->id, false);
        $this->assertSame([$tenantdomainmanagerrole->id], array_values(array_map($functionroleid, $result)));
    }
}
