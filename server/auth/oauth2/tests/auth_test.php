<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Auth oauth2 auth functions tests.
 *
 * @package    auth_oauth2
 * @category   test
 * @copyright  2019 Shamim Rezaie <shamim@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

/**
 * Tests for the \auth_oauth2\auth class.
 *
 * @copyright  2019 Shamim Rezaie
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @group core_auth
 */
class auth_oauth2_auth_testcase extends advanced_testcase {

    public function test_get_password_change_info() {
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user(['auth' => 'oauth2']);
        $auth = get_auth_plugin($user->auth);
        $info = $auth->get_password_change_info($user);

        $this->assertEqualsCanonicalizing(
                ['subject', 'message'],
                array_keys($info));
        $this->assertStringContainsString(
                'your password cannot be reset because you are using your account on another site to log in',
                $info['message']);
    }

    /**
     * Test that \oauth2_auth\auth::update_user() returns an array, not object.
     */
    public function test_update_user_array() {
        global $USER;

        $this->setAdminUser();

        $oauth = new \auth_oauth2\auth();
        $reflection = new ReflectionClass($oauth);
        $method = $reflection->getMethod('update_user');
        $method->setAccessible(true);

        // Positive test.
        $USER->auth = 'oauth2';
        $result = $method->invoke($oauth, [], $USER);
        $this->assertIsArray($result);

        // Negative test.
        $USER->auth = 'oauth';
        $result = $method->invoke($oauth, [], $USER);
        $this->assertIsArray($result);
    }

    public function test_user_confirm_secret_is_required() {
        global $DB;

        $auth_plugin = get_auth_plugin('oauth2');
        $user = $this->getDataGenerator()->create_user(['auth' => $auth_plugin->authtype, 'secret' => 'abc']);
        $DB->set_field('user', 'confirmed', false, ['id' => $user->id]);

        // Fail with wrong secret.
        self::assertEquals(AUTH_CONFIRM_ERROR, $auth_plugin->user_confirm($user->username, 'xyz'));

        // Fail with 'true' (previous security vulnerability - see TL-29941).
        self::assertEquals(AUTH_CONFIRM_ERROR, $auth_plugin->user_confirm($user->username, true));

        // Pass with correct secret.
        self::assertEquals(AUTH_CONFIRM_OK, $auth_plugin->user_confirm($user->username, 'abc'));

        // Pass with correct secret but already confirmed.
        self::assertEquals(AUTH_CONFIRM_ALREADY, $auth_plugin->user_confirm($user->username, 'abc'));
    }
}