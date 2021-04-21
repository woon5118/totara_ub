<?php
/**
 * This file is part of Totara Learn
 *
 * Copyright (C) 2021 onwards Totara Learning Solutions LTD
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
 * @author Cody Finegan <cody.finegan@totaralearning.com>
 * @package auth_mnet
 */

defined('MOODLE_INTERNAL') || die();

class auth_mnet_keepalive_test extends advanced_testcase {
    /**
     * @var string|null
     */
    private $original_remote_client;

    /**
     * @var array|null
     */
    private $users;

    /**
     * Test the keepalive_server method updates the session of the provided users
     * and filters out any SQL injections
     */
    public function test_keepalive(): void {
        $plugin = new auth_plugin_mnet();

        // Check the sessions for our existing users are the defaults
        foreach ($this->users as $user) {
            $this->assert_session_not_touched($user->id);
        }

        // Assert that if we keepalive two of our users, their session will updated
        $result = $plugin->keepalive_server(array(
            $this->users[0]->username,
            $this->users[1]->username,
        ));
        self::assertIsArray($result);
        self::assertEqualsCanonicalizing(array(
            'code' => 0,
            'message' => 'All ok',
            'last log id' => 1,
        ), $result);

        // User 0 & User 1 should be updated, the rest not.
        foreach ($this->users as $i => $user) {
            if ($i <= 1) {
                $this->assert_session_touched($user->id);
            } else {
                $this->assert_session_not_touched($user->id);
            }
        }

        // Run now with a SQL injection
        $result = $plugin->keepalive_server(array(
            $this->users[2]->username,
            "') OR 1=1--",
        ));
        self::assertIsArray($result);
        self::assertEqualsCanonicalizing(array(
            'code' => 0,
            'message' => 'All ok',
            'last log id' => 1,
        ), $result);

        // If the injection was run successfully, then all users would have been touched.
        // If it failed, then only user 2 would have been touched
        foreach ($this->users as $i => $user) {
            if ($i <= 2) {
                $this->assert_session_touched($user->id);
            } else {
                $this->assert_session_not_touched(
                    $user->id,
                    'sql injection succeeded, session was touched unexpectedly'
                );
            }
        }
    }

    /**
     * Test that nothing is updated when invalid users are provided
     */
    public function test_keepalive_invalid_users(): void {
        $plugin = new auth_plugin_mnet();

        // Check the sessions for our existing users are the defaults
        foreach ($this->users as $user) {
            $this->assert_session_not_touched($user->id);
        }

        // Check that nothing changes for invalid users
        $result = $plugin->keepalive_server(array('does-not-exist'));
        self::assertIsArray($result);
        self::assertEqualsCanonicalizing(array(
            'code' => 1,
            'message' => "We failed to refresh the session for the following usernames: \ndoes-not-exist\n\n",
            'last log id' => 1,
        ), $result);

        foreach ($this->users as $user) {
            $this->assert_session_not_touched($user->id);
        }
    }

    /**
     * Configure the environment so we can test keep alive
     */
    protected function setUp(): void {
        global $DB, $CFG, $MNET_REMOTE_CLIENT;

        // Hacky, but needed to mock the test case correctly (else a debug is thrown)
        if (!defined('MNET_SERVER')) {
            define('MNET_SERVER', true);
        }

        $this->original_remote_client = $MNET_REMOTE_CLIENT;
        $MNET_REMOTE_CLIENT = new stdClass();
        $MNET_REMOTE_CLIENT->last_log_id = 1;

        // Create mock user sessions
        $DB->delete_records('sessions');
        $DB->delete_records('mnet_session');
        $this->users = array();
        for ($i = 0; $i < 10; $i++) {
            $user = $this->getDataGenerator()->create_user();
            $this->users[] = $user;
            $DB->insert_record('sessions', (object) array(
                'sid' => 'session_user_' . $user->id,
                'userid' => $user->id,
                'timecreated' => 1234,
                'timemodified' => 1234,
            ));
            $DB->insert_record('mnet_session', (object) array(
                'userid' => $user->id,
                'username' => $user->username,
                'token' => 'token_' . $user->id,
                'useragent' => 'Mock Agent',
                'session_id' => 'session_user_' . $user->id,
            ));
        }

        require_once($CFG->dirroot . '/auth/mnet/auth.php');
    }

    /**
     * Reset the client back to the standard
     */
    protected function tearDown(): void {
        global $MNET_REMOTE_CLIENT;
        $MNET_REMOTE_CLIENT = $this->original_remote_client;
        $this->original_remote_client = null;
        $this->users = null;
    }

    /**
     * Asserts that the session for the user has been touched/updated.
     *
     * @param int $user_id
     */
    private function assert_session_touched(int $user_id): void {
        global $DB;

        $result = $DB->get_field('sessions', 'timemodified', array('sid' => 'session_user_' . $user_id));
        self::assertGreaterThan(1234, $result);
    }

    /**
     * Asserts that the session for the user has not been touched/is the default
     *
     * @param int $user_id
     * @param string $message
     */
    private function assert_session_not_touched(int $user_id, string $message = ''): void {
        global $DB;

        $result = $DB->get_field('sessions', 'timemodified', array('sid' => 'session_user_' . $user_id));
        self::assertEquals(1234, $result, $message);
    }
}