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

use totara_webapi\phpunit\webapi_phpunit_helper;

/**
 * Tests the totara job create assignment mutation
 */
class core_webapi_resolver_type_user_testcase extends advanced_testcase {
    
    use webapi_phpunit_helper;

    public function test_resolver_id() {
        $user = $this->getDataGenerator()->create_user();
        self::assertSame($user->id, $this->resolve_graphql_type('core_user', 'id', $user, []));
    }

    public function test_resolver_password() {
        $user = new stdClass();
        $user->id = '100';
        $user->password = 'test';
        self::assertNull($this->resolve_graphql_type('core_user', 'password', $user, []));
    }

    public function test_resolver_secret() {
        $user = new stdClass();
        $user->id = '100';
        $user->secret = 'test';
        self::assertNull($this->resolve_graphql_type('core_user', 'secret', $user, []));
    }

    public function test_resolver_fake_set_field() {
        $user = new stdClass();
        $user->id = '100';
        $user->blah = 'test';
        $this->expectExceptionMessage('Coding error detected, it must be fixed by a programmer: Unknown user field');
        self::assertNull($this->resolve_graphql_type('core_user', 'blah', $user, []));
    }

    public function test_resolver_fake_unset_field() {
        $user = new stdClass();
        $user->id = '100';
        $this->expectExceptionMessage('Coding error detected, it must be fixed by a programmer: Unknown user field');
        self::assertNull($this->resolve_graphql_type('core_user', 'blah', $user, []));
    }

    public function test_inaccessible_field() {
        $user = $this->getDataGenerator()->create_user();
        $user->emailstop = '1';
        self::assertNull($this->resolve_graphql_type('core_user', 'emailstop', $user, []));
    }

    public function test_resolver_idnumber() {
        global $CFG;
        $CFG->showuseridentity = 'idnumber';
        $value = 'test';
        $user = $this->getDataGenerator()->create_user(['idnumber' => $value]);
        self::assertNull($this->resolve_graphql_type('core_user', 'idnumber', $user, []));

        $this->setAdminUser();
        self::assertSame($value, $this->resolve_graphql_type('core_user', 'idnumber', $user, []));
    }

    public function test_resolver_fullname() {
        $user = $this->getDataGenerator()->create_user(['firstname' => 'Joe', 'lastname' => 'Smith']);

        try {
            $this->resolve_graphql_type('core_user', 'fullname', $user, []);
            $this->fail('Exception expected');
        } catch (coding_exception $exception) {
            self::assertContains('You did not check you can view a user before resolving them', $exception->getMessage());
        }

        $this->setAdminUser();
        self::assertSame(fullname($user), $this->resolve_graphql_type('core_user', 'fullname', $user, []));
    }

    public function test_resolver_firstname() {
        $user = $this->getDataGenerator()->create_user(['firstname' => 'test']);
        self::assertNull($this->resolve_graphql_type('core_user', 'firstname', $user, []));

        $this->setAdminUser();
        self::assertSame('test', $this->resolve_graphql_type('core_user', 'firstname', $user, []));
    }

    public function test_resolver_lastname() {
        $user = $this->getDataGenerator()->create_user(['lastname' => 'test']);
        self::assertNull($this->resolve_graphql_type('core_user', 'lastname', $user, []));

        $this->setAdminUser();
        self::assertSame('test', $this->resolve_graphql_type('core_user', 'lastname', $user, []));
    }

    public function test_resolver_email() {
        global $CFG;
        $this->setAdminUser();
        $value = 'email@example.com';
        $user = $this->getDataGenerator()->create_user(['email' => $value]);
        self::assertSame($value, $this->resolve_graphql_type('core_user', 'email', $user, []));
        $CFG->showuseridentity = 'idnumber';
        $user->maildisplay = 0;
        self::assertSame($value, $this->resolve_graphql_type('core_user', 'email', $user, []));

        $this->setUser($this->getDataGenerator()->create_user());
        self::assertNull($this->resolve_graphql_type('core_user', 'email', $user, []));

        $this->setGuestUser();
        self::assertNull($this->resolve_graphql_type('core_user', 'email', $user, []));
    }

    public function test_resolver_address() {
        $value = 'Example address goes here';
        $user = $this->getDataGenerator()->create_user(['address' => $value]);
        self::assertNull($this->resolve_graphql_type('core_user', 'address', $user, []));

        $this->setAdminUser();
        self::assertSame($value, $this->resolve_graphql_type('core_user', 'address', $user, []));
    }

    public function test_resolver_phone1() {
        $value = '444';
        $user = $this->getDataGenerator()->create_user(['phone1' => $value]);
        self::assertNull($this->resolve_graphql_type('core_user', 'phone1', $user, []));

        $this->setAdminUser();
        self::assertSame($value, $this->resolve_graphql_type('core_user', 'phone1', $user, []));
    }

    public function test_resolver_phone2() {
        $value = '555';
        $user = $this->getDataGenerator()->create_user(['phone2' => $value]);
        self::assertNull($this->resolve_graphql_type('core_user', 'phone2', $user, []));

        $this->setAdminUser();
        self::assertSame($value, $this->resolve_graphql_type('core_user', 'phone2', $user, []));
    }

    public function test_resolver_department() {
        $value = 'Development';
        $user = $this->getDataGenerator()->create_user(['department' => $value]);
        self::assertNull($this->resolve_graphql_type('core_user', 'department', $user, []));

        $this->setAdminUser();
        self::assertSame($value, $this->resolve_graphql_type('core_user', 'department', $user, []));
    }

    public function test_resolver_institution() {
        $value = 'Totara Learning Solutions';
        $user = $this->getDataGenerator()->create_user(['institution' => $value]);
        self::assertNull($this->resolve_graphql_type('core_user', 'institution', $user, []));

        $this->setAdminUser();
        self::assertSame($value, $this->resolve_graphql_type('core_user', 'institution', $user, []));
    }

    public function test_resolver_city() {
        $value = 'Wellington';
        $user = $this->getDataGenerator()->create_user(['city' => $value]);
        self::assertNull($this->resolve_graphql_type('core_user', 'city', $user, []));

        $this->setAdminUser();
        self::assertSame($value, $this->resolve_graphql_type('core_user', 'city', $user, []));
    }

    public function test_resolver_country() {
        $value = 'nz';
        $user = $this->getDataGenerator()->create_user(['country' => $value]);
        self::assertNull($this->resolve_graphql_type('core_user', 'country', $user, []));

        $this->setAdminUser();
        self::assertSame($value, $this->resolve_graphql_type('core_user', 'country', $user, []));
    }

    public function test_resolver_lang() {
        $value = 'en';
        $user = $this->getDataGenerator()->create_user(['lang' => $value]);
        self::assertNull($this->resolve_graphql_type('core_user', 'lang', $user, []));

        $this->setAdminUser();
        self::assertSame($value, $this->resolve_graphql_type('core_user', 'lang', $user, []));
    }

    public function test_resolver_timezone() {
        $value = 'Pacific/Auckland';
        $user = $this->getDataGenerator()->create_user(['timezone' => $value]);
        self::assertNull($this->resolve_graphql_type('core_user', 'timezone', $user, []));

        $this->setAdminUser();
        self::assertSame($value, $this->resolve_graphql_type('core_user', 'timezone', $user, []));
    }

    public function test_resolver_theme() {
        $value = 'basis';
        $user = $this->getDataGenerator()->create_user(['theme' => $value]);
        self::assertNull($this->resolve_graphql_type('core_user', 'theme', $user, []));

        $this->setAdminUser();
        self::assertSame($value, $this->resolve_graphql_type('core_user', 'theme', $user, []));
    }

    public function test_resolver_interests() {
        $value = 'dancing';
        $user = $this->getDataGenerator()->create_user(['interests' => $value]);
        self::assertNull($this->resolve_graphql_type('core_user', 'interests', $user, []));

        $this->setAdminUser();
        self::assertSame($value, $this->resolve_graphql_type('core_user', 'interests', $user, []));
    }

    public function test_resolver_description() {
        global $CFG;

        $user = $this->getDataGenerator()->create_user(['description' => '<p>This is a test</p>']);
        $this->setUser($user);
        self::assertSame('<p>This is a test</p>', $this->resolve_graphql_type('core_user', 'description', $user, []));
        self::assertSame('<p>This is a test</p>', $this->resolve_graphql_type('core_user', 'description', $user, ['format' => \core\format::FORMAT_HTML]));
        self::assertSame("This is a test\n", $this->resolve_graphql_type('core_user', 'description', $user, ['format' => \core\format::FORMAT_PLAIN]));
        self::assertSame('<p>This is a test</p>', $this->resolve_graphql_type('core_user', 'description', $user, ['format' => \core\format::FORMAT_RAW]));

        $context = \context_user::instance($user->id);
        $roleid = $this->getDataGenerator()->create_role([]);
        role_assign($roleid, $user->id, $context);
        assign_capability('moodle/user:editownprofile', CAP_PROHIBIT, $roleid, $context);
        self::assertSame('<p>This is a test</p>', $this->resolve_graphql_type('core_user', 'description', $user, []));
        self::assertSame('<p>This is a test</p>', $this->resolve_graphql_type('core_user', 'description', $user, ['format' => \core\format::FORMAT_HTML]));
        self::assertSame("This is a test\n", $this->resolve_graphql_type('core_user', 'description', $user, ['format' => \core\format::FORMAT_PLAIN]));
        self::assertNull($this->resolve_graphql_type('core_user', 'description', $user, ['format' => \core\format::FORMAT_RAW]));

        $user2 = $this->getDataGenerator()->create_user();
        $this->setUser($user2);
        self::assertNull($this->resolve_graphql_type('core_user', 'description', $user, []));
        self::assertNull($this->resolve_graphql_type('core_user', 'description', $user, ['format' => \core\format::FORMAT_HTML]));
        self::assertNull($this->resolve_graphql_type('core_user', 'description', $user, ['format' => \core\format::FORMAT_PLAIN]));
        self::assertNull($this->resolve_graphql_type('core_user', 'description', $user, ['format' => \core\format::FORMAT_RAW]));

        $CFG->forceloginforprofiles = false;
        self::assertSame('<p>This is a test</p>', $this->resolve_graphql_type('core_user', 'description', $user, []));
        self::assertSame('<p>This is a test</p>', $this->resolve_graphql_type('core_user', 'description', $user, ['format' => \core\format::FORMAT_HTML]));
        self::assertSame("This is a test\n", $this->resolve_graphql_type('core_user', 'description', $user, ['format' => \core\format::FORMAT_PLAIN]));
        self::assertSame(null, $this->resolve_graphql_type('core_user', 'description', $user, ['format' => \core\format::FORMAT_RAW]));
    }

    public function test_resolver_descriptionformat() {
        $user = $this->getDataGenerator()->create_user(['description' => 'test', 'descriptionformat' => FORMAT_HTML]);
        self::assertNull($this->resolve_graphql_type('core_user', 'descriptionformat', $user, []));

        $this->setAdminUser();
        self::assertSame(FORMAT_HTML, $this->resolve_graphql_type('core_user', 'descriptionformat', $user, []));
    }

    public function test_resolver_profileimageurl() {
        $user = $this->getDataGenerator()->create_user([]);
        self::assertNull($this->resolve_graphql_type('core_user', 'profileimageurl', $user, []));

        $this->setAdminUser();
        self::assertContains('theme/image.php/_s/basis/core/1/u/f1', $this->resolve_graphql_type('core_user', 'profileimageurl', $user, []));
    }

    public function test_resolver_profileimageurlsmall() {
        $user = $this->getDataGenerator()->create_user([]);
        self::assertNull($this->resolve_graphql_type('core_user', 'profileimageurlsmall', $user, []));

        $this->setAdminUser();
        self::assertContains('theme/image.php/_s/basis/core/1/u/f2', $this->resolve_graphql_type('core_user', 'profileimageurlsmall', $user, []));
    }

    public function test_resolver_profileimagealt() {
        $user = $this->getDataGenerator()->create_user(['imagealt' => 'test']);
        self::assertNull($this->resolve_graphql_type('core_user', 'profileimagealt', $user, []));

        $this->setAdminUser();
        self::assertSame('test', $this->resolve_graphql_type('core_user', 'profileimagealt', $user, []));
    }

    public function test_resolver_firstaccess() {
        global $DB;
        $value = time();
        $user = $this->getDataGenerator()->create_user([]);
        self::assertNull($this->resolve_graphql_type('core_user', 'firstaccess', $user, []));
        $DB->set_field('user', 'firstaccess', $value, ['id' => $user->id]);
        $user = $DB->get_record('user', ['id' => $user->id]);
        self::assertEquals(null, $this->resolve_graphql_type('core_user', 'firstaccess', $user, []));

        $this->setAdminUser();
        self::assertEquals($value, $this->resolve_graphql_type('core_user', 'firstaccess', $user, []));
    }

    public function test_resolver_lastaccess() {
        global $DB;
        $value = time();
        $user = $this->getDataGenerator()->create_user([]);
        self::assertNull($this->resolve_graphql_type('core_user', 'lastaccess', $user, []));
        $DB->set_field('user', 'lastaccess', $value, ['id' => $user->id]);
        $user = $DB->get_record('user', ['id' => $user->id]);
        self::assertEquals(null, $this->resolve_graphql_type('core_user', 'lastaccess', $user, []));

        $this->setAdminUser();
        self::assertEquals($value, $this->resolve_graphql_type('core_user', 'lastaccess', $user, []));
    }

    public function test_resolver_guest_user() {
        global $DB;
        $this->setAdminUser();
        $value = time();
        $user = guest_user();
        self::assertNull($this->resolve_graphql_type('core_user', 'firstaccess', $user, []));

        $DB->set_field('user', 'firstaccess', $value, ['id' => $user->id]);
        $user = $DB->get_record('user', ['id' => $user->id]);
        self::assertEquals($user->firstaccess, $this->resolve_graphql_type('core_user', 'firstaccess', $user, []));
    }

    public function test_resolver_deleted_user() {
        global $DB;
        $this->setAdminUser();
        $value = time();
        $user = $this->getDataGenerator()->create_user([]);
        self::assertNull($this->resolve_graphql_type('core_user', 'firstaccess', $user, []));
        $DB->set_field('user', 'firstaccess', $value, ['id' => $user->id]);
        $user = $DB->get_record('user', ['id' => $user->id]);
        self::assertEquals($value, $this->resolve_graphql_type('core_user', 'firstaccess', $user, []));

        delete_user($user);

        $user = $DB->get_record('user', ['id' => $user->id]);
        self::assertEquals(null, $this->resolve_graphql_type('core_user', 'firstaccess', $user, []));
    }

    public function test_resolver_hidden_user_fields() {
        global $CFG;
        $CFG->hiddenuserfields = 'description';
        $this->setAdminUser();
        $user1 = $this->getDataGenerator()->create_user(['description' => 'test']);
        self::assertSame('test', $this->resolve_graphql_type('core_user', 'description', $user1, []));

        $this->setUser($this->getDataGenerator()->create_user());
        self::assertNull($this->resolve_graphql_type('core_user', 'description', $user1, []));
    }
}