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
 * @package core
 */

defined('MOODLE_INTERNAL') || die();

use \core\webapi\resolver\type\user;

/**
 * Tests the totara job create assignment mutation
 */
class core_webapi_resolver_type_user_testcase extends advanced_testcase {

    private function get_execution_context(string $type = 'dev', ?string $operation = null) {
        return \core\webapi\execution_context::create($type, $operation);
    }

    public function test_resolver_id() {
        $user = $this->getDataGenerator()->create_user();
        self::assertSame($user->id,  user::resolve('id', $user, [], $this->get_execution_context()));
    }

    public function test_resolver_password() {
        $user = new stdClass();
        $user->id = '100';
        $user->password = 'test';
        self::assertNull(user::resolve('password', $user, [], $this->get_execution_context()));
    }

    public function test_resolver_secret() {
        $user = new stdClass();
        $user->id = '100';
        $user->secret = 'test';
        self::assertNull(user::resolve('secret', $user, [], $this->get_execution_context()));
    }

    public function test_resolver_fake_set_field() {
        $user = new stdClass();
        $user->id = '100';
        $user->blah = 'test';
        $this->expectExceptionMessage('Coding error detected, it must be fixed by a programmer: Unknown user field');
        self::assertNull(user::resolve('blah', $user, [], $this->get_execution_context()));
    }

    public function test_resolver_fake_unset_field() {
        $user = new stdClass();
        $user->id = '100';
        $this->expectExceptionMessage('Coding error detected, it must be fixed by a programmer: Unknown user field');
        self::assertNull(user::resolve('blah', $user, [], $this->get_execution_context()));
    }

    public function test_inaccessible_field() {
        $user = $this->getDataGenerator()->create_user();
        $user->emailstop = '1';
        self::assertNull(user::resolve('emailstop', $user, [], $this->get_execution_context()));
    }

    public function test_resolver_idnumber() {
        global $CFG;
        $CFG->showuseridentity = 'idnumber';
        $value = 'test';
        $user = $this->getDataGenerator()->create_user(['idnumber' => $value]);
        self::assertNull(user::resolve('idnumber', $user, [], $this->get_execution_context()));

        $this->setAdminUser();
        self::assertSame($value,  user::resolve('idnumber', $user, [], $this->get_execution_context()));
    }

    public function test_resolver_fullname() {
        $user = $this->getDataGenerator()->create_user(['firstname' => 'Joe', 'lastname' => 'Smith']);

        try {
            user::resolve('fullname', $user, [], $this->get_execution_context());
            $this->fail('Exception expected');
        } catch (coding_exception $exception) {
            self::assertContains('You did not check you can view a user before resolving them', $exception->getMessage());
        }

        $this->setAdminUser();
        self::assertSame(fullname($user),  user::resolve('fullname', $user, [], $this->get_execution_context()));
    }

    public function test_resolver_firstname() {
        $user = $this->getDataGenerator()->create_user(['firstname' => 'test']);
        self::assertNull(user::resolve('firstname', $user, [], $this->get_execution_context()));

        $this->setAdminUser();
        self::assertSame('test',  user::resolve('firstname', $user, [], $this->get_execution_context()));
    }

    public function test_resolver_lastname() {
        $user = $this->getDataGenerator()->create_user(['lastname' => 'test']);
        self::assertNull(user::resolve('lastname', $user, [], $this->get_execution_context()));

        $this->setAdminUser();
        self::assertSame('test',  user::resolve('lastname', $user, [], $this->get_execution_context()));
    }

    public function test_resolver_email() {
        global $CFG;
        $this->setAdminUser();
        $value = 'email@example.com';
        $user = $this->getDataGenerator()->create_user(['email' => $value]);
        self::assertSame($value,  user::resolve('email', $user, [], $this->get_execution_context()));
        $CFG->showuseridentity = 'idnumber';
        $user->maildisplay = 0;
        self::assertSame($value,  user::resolve('email', $user, [], $this->get_execution_context()));

        $this->setUser($this->getDataGenerator()->create_user());
        self::assertNull(user::resolve('email', $user, [], $this->get_execution_context()));

        $this->setGuestUser();
        self::assertNull(user::resolve('email', $user, [], $this->get_execution_context()));
    }

    public function test_resolver_address() {
        $value = 'Example address goes here';
        $user = $this->getDataGenerator()->create_user(['address' => $value]);
        self::assertNull(user::resolve('address', $user, [], $this->get_execution_context()));

        $this->setAdminUser();
        self::assertSame($value,  user::resolve('address', $user, [], $this->get_execution_context()));
    }

    public function test_resolver_phone1() {
        $value = '444';
        $user = $this->getDataGenerator()->create_user(['phone1' => $value]);
        self::assertNull(user::resolve('phone1', $user, [], $this->get_execution_context()));

        $this->setAdminUser();
        self::assertSame($value,  user::resolve('phone1', $user, [], $this->get_execution_context()));
    }

    public function test_resolver_phone2() {
        $value = '555';
        $user = $this->getDataGenerator()->create_user(['phone2' => $value]);
        self::assertNull(user::resolve('phone2', $user, [], $this->get_execution_context()));

        $this->setAdminUser();
        self::assertSame($value,  user::resolve('phone2', $user, [], $this->get_execution_context()));
    }

    public function test_resolver_department() {
        $value = 'Development';
        $user = $this->getDataGenerator()->create_user(['department' => $value]);
        self::assertNull(user::resolve('department', $user, [], $this->get_execution_context()));

        $this->setAdminUser();
        self::assertSame($value,  user::resolve('department', $user, [], $this->get_execution_context()));
    }

    public function test_resolver_institution() {
        $value = 'Totara Learning Solutions';
        $user = $this->getDataGenerator()->create_user(['institution' => $value]);
        self::assertNull(user::resolve('institution', $user, [], $this->get_execution_context()));

        $this->setAdminUser();
        self::assertSame($value,  user::resolve('institution', $user, [], $this->get_execution_context()));
    }

    public function test_resolver_city() {
        $value = 'Wellington';
        $user = $this->getDataGenerator()->create_user(['city' => $value]);
        self::assertNull(user::resolve('city', $user, [], $this->get_execution_context()));

        $this->setAdminUser();
        self::assertSame($value,  user::resolve('city', $user, [], $this->get_execution_context()));
    }

    public function test_resolver_country() {
        $value = 'nz';
        $user = $this->getDataGenerator()->create_user(['country' => $value]);
        self::assertNull(user::resolve('country', $user, [], $this->get_execution_context()));

        $this->setAdminUser();
        self::assertSame($value,  user::resolve('country', $user, [], $this->get_execution_context()));
    }

    public function test_resolver_lang() {
        $value = 'en';
        $user = $this->getDataGenerator()->create_user(['lang' => $value]);
        self::assertNull(user::resolve('lang', $user, [], $this->get_execution_context()));

        $this->setAdminUser();
        self::assertSame($value,  user::resolve('lang', $user, [], $this->get_execution_context()));
    }

    public function test_resolver_timezone() {
        $value = 'Pacific/Auckland';
        $user = $this->getDataGenerator()->create_user(['timezone' => $value]);
        self::assertNull(user::resolve('timezone', $user, [], $this->get_execution_context()));

        $this->setAdminUser();
        self::assertSame($value,  user::resolve('timezone', $user, [], $this->get_execution_context()));
    }

    public function test_resolver_theme() {
        $value = 'basis';
        $user = $this->getDataGenerator()->create_user(['theme' => $value]);
        self::assertNull(user::resolve('theme', $user, [], $this->get_execution_context()));

        $this->setAdminUser();
        self::assertSame($value,  user::resolve('theme', $user, [], $this->get_execution_context()));
    }

    public function test_resolver_interests() {
        $value = 'dancing';
        $user = $this->getDataGenerator()->create_user(['interests' => $value]);
        self::assertNull(user::resolve('interests', $user, [], $this->get_execution_context()));

        $this->setAdminUser();
        self::assertSame($value,  user::resolve('interests', $user, [], $this->get_execution_context()));
    }

    public function test_resolver_description() {
        global $CFG;

        $user = $this->getDataGenerator()->create_user(['description' => '<p>This is a test</p>']);
        $this->setUser($user);
        self::assertSame('<p>This is a test</p>',  user::resolve('description', $user, [], $this->get_execution_context()));
        self::assertSame('<p>This is a test</p>',  user::resolve('description', $user, ['format' => \core\format::FORMAT_HTML], $this->get_execution_context()));
        self::assertSame("This is a test\n",  user::resolve('description', $user, ['format' => \core\format::FORMAT_PLAIN], $this->get_execution_context()));
        self::assertSame('<p>This is a test</p>',  user::resolve('description', $user, ['format' => \core\format::FORMAT_RAW], $this->get_execution_context()));

        $context = \context_user::instance($user->id);
        $roleid = $this->getDataGenerator()->create_role([]);
        role_assign($roleid, $user->id, $context);
        assign_capability('moodle/user:editownprofile', CAP_PROHIBIT, $roleid, $context);
        self::assertSame('<p>This is a test</p>',  user::resolve('description', $user, [], $this->get_execution_context()));
        self::assertSame('<p>This is a test</p>',  user::resolve('description', $user, ['format' => \core\format::FORMAT_HTML], $this->get_execution_context()));
        self::assertSame("This is a test\n",  user::resolve('description', $user, ['format' => \core\format::FORMAT_PLAIN], $this->get_execution_context()));
        self::assertNull(user::resolve('description', $user, ['format' => \core\format::FORMAT_RAW], $this->get_execution_context()));

        $user2 = $this->getDataGenerator()->create_user();
        $this->setUser($user2);
        self::assertNull(user::resolve('description', $user, [], $this->get_execution_context()));
        self::assertNull(user::resolve('description', $user, ['format' => \core\format::FORMAT_HTML], $this->get_execution_context()));
        self::assertNull(user::resolve('description', $user, ['format' => \core\format::FORMAT_PLAIN], $this->get_execution_context()));
        self::assertNull(user::resolve('description', $user, ['format' => \core\format::FORMAT_RAW], $this->get_execution_context()));

        $CFG->forceloginforprofiles = false;
        self::assertSame('<p>This is a test</p>', user::resolve('description', $user, [], $this->get_execution_context()));
        self::assertSame('<p>This is a test</p>', user::resolve('description', $user, ['format' => \core\format::FORMAT_HTML], $this->get_execution_context()));
        self::assertSame("This is a test\n", user::resolve('description', $user, ['format' => \core\format::FORMAT_PLAIN], $this->get_execution_context()));
        self::assertSame(null, user::resolve('description', $user, ['format' => \core\format::FORMAT_RAW], $this->get_execution_context()));
    }

    public function test_resolver_descriptionformat() {
        $user = $this->getDataGenerator()->create_user(['description' => 'test', 'descriptionformat' => FORMAT_HTML]);
        self::assertNull(user::resolve('descriptionformat', $user, [], $this->get_execution_context()));

        $this->setAdminUser();
        self::assertSame(FORMAT_HTML,  user::resolve('descriptionformat', $user, [], $this->get_execution_context()));
    }

    public function test_resolver_profileimageurl() {
        $user = $this->getDataGenerator()->create_user([]);
        self::assertNull(user::resolve('profileimageurl', $user, [], $this->get_execution_context()));

        $this->setAdminUser();
        self::assertContains('theme/image.php/_s/basis/core/1/u/f1',  user::resolve('profileimageurl', $user, [], $this->get_execution_context()));
    }

    public function test_resolver_profileimageurlsmall() {
        $user = $this->getDataGenerator()->create_user([]);
        self::assertNull(user::resolve('profileimageurlsmall', $user, [], $this->get_execution_context()));

        $this->setAdminUser();
        self::assertContains('theme/image.php/_s/basis/core/1/u/f2',  user::resolve('profileimageurlsmall', $user, [], $this->get_execution_context()));
    }

    public function test_resolver_profileimagealt() {
        $user = $this->getDataGenerator()->create_user(['imagealt' => 'test']);
        self::assertNull(user::resolve('profileimagealt', $user, [], $this->get_execution_context()));

        $this->setAdminUser();
        self::assertSame('test',  user::resolve('profileimagealt', $user, [], $this->get_execution_context()));
    }

    public function test_resolver_firstaccess() {
        global $DB;
        $value = time();
        $user = $this->getDataGenerator()->create_user([]);
        self::assertNull(user::resolve('firstaccess', $user, [], $this->get_execution_context()));
        $DB->set_field('user', 'firstaccess', $value, ['id' => $user->id]);
        $user = $DB->get_record('user', ['id' => $user->id]);
        self::assertEquals(null,  user::resolve('firstaccess', $user, [], $this->get_execution_context()));

        $this->setAdminUser();
        self::assertEquals($value,  user::resolve('firstaccess', $user, [], $this->get_execution_context()));
    }

    public function test_resolver_lastaccess() {
        global $DB;
        $value = time();
        $user = $this->getDataGenerator()->create_user([]);
        self::assertNull(user::resolve('lastaccess', $user, [], $this->get_execution_context()));
        $DB->set_field('user', 'lastaccess', $value, ['id' => $user->id]);
        $user = $DB->get_record('user', ['id' => $user->id]);
        self::assertEquals(null,  user::resolve('lastaccess', $user, [], $this->get_execution_context()));

        $this->setAdminUser();
        self::assertEquals($value,  user::resolve('lastaccess', $user, [], $this->get_execution_context()));
    }

    public function test_resolver_guest_user() {
        global $DB;
        $this->setAdminUser();
        $value = time();
        $user = guest_user();
        self::assertNull(user::resolve('firstaccess', $user, [], $this->get_execution_context()));

        $DB->set_field('user', 'firstaccess', $value, ['id' => $user->id]);
        $user = $DB->get_record('user', ['id' => $user->id]);
        self::assertEquals($user->firstaccess,  user::resolve('firstaccess', $user, [], $this->get_execution_context()));
    }

    public function test_resolver_deleted_user() {
        global $DB;
        $this->setAdminUser();
        $value = time();
        $user = $this->getDataGenerator()->create_user([]);
        self::assertNull(user::resolve('firstaccess', $user, [], $this->get_execution_context()));
        $DB->set_field('user', 'firstaccess', $value, ['id' => $user->id]);
        $user = $DB->get_record('user', ['id' => $user->id]);
        self::assertEquals($value,  user::resolve('firstaccess', $user, [], $this->get_execution_context()));

        delete_user($user);

        $user = $DB->get_record('user', ['id' => $user->id]);
        self::assertEquals(null,  user::resolve('firstaccess', $user, [], $this->get_execution_context()));
    }

    public function test_resolver_hidden_user_fields() {
        global $CFG;
        $CFG->hiddenuserfields = 'description';
        $this->setAdminUser();
        $user1 = $this->getDataGenerator()->create_user(['description' => 'test']);
        self::assertSame('test',  user::resolve('description', $user1, [], $this->get_execution_context()));

        $this->setUser($this->getDataGenerator()->create_user());
        self::assertNull(user::resolve('description', $user1, [], $this->get_execution_context()));
    }
}