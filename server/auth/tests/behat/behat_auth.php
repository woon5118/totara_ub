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
 * Basic authentication steps definitions.
 *
 * @package    core_auth
 * @category   test
 * @copyright  2012 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

use Behat\Mink\Exception\ExpectationException;

require_once(__DIR__ . '/../../../lib/behat/behat_base.php');

/**
 * Log in log out steps definitions.
 *
 * @package    core_auth
 * @category   test
 * @copyright  2012 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_auth extends behat_base {

    /**
     * Logs in the user. There should exist a user with the same value as username and password.
     *
     * @Given /^I log in as "(?P<username_string>(?:[^"]|\\")*)"$/
     */
    public function i_log_in_as($username) {
        \behat_hooks::set_step_readonly(false);
        // Visit login page.
        $this->getSession()->visit($this->locate_path('login/index.php'));
        $this->wait_for_pending_js();

        // Enter username and password.
        $this->execute('behat_forms::i_set_the_field_to', array('Username', $this->escape($username)));
        $this->execute('behat_forms::i_set_the_field_to', array('Password', $this->escape($username)));

        // Press log in button, no need to check for exceptions as it will checked after this step execution.
        $this->execute('behat_forms::press_button', get_string('login'));
    }

    /**
     * Logs out of the system.
     *
     * @Given /^I log out$/
     */
    public function i_log_out() {
        \behat_hooks::set_step_readonly(false);

        // Wait for page to be loaded.
        $this->wait_for_pending_js();

        $content = $this->getSession()->getPage()->getContent();
        $sesskey = [];
        if (preg_match('/"sesskey":"([a-zA-Z0-9]+)"/', $content, $sesskey) !== 1) {
            throw new ExpectationException(
                "Unable to retrieve sesskey",
                $this->getSession()
            );
        }

        // Add the sesskey parameter otherwise we will be prompted to confirm the logout.
        $this->getSession()->visit($this->locate_path('/login/logout.php?sesskey=' . $sesskey[1]));
    }

    /**
     * Confirms a user
     *
     * @Given /^confirm self-registered login as user "(?P<username_string>(?:[^"]|\\")*)"$/
     */
    public function confirm_selfregistered_login_as_user($username) {
        global $CFG, $DB;

        \behat_hooks::set_step_readonly(false);

        $user = $DB->get_record('user', array('username' => $username));

        $this->getSession()->visit($this->locate_path('/login/confirm.php?data=' . $user->secret . '/' . $user->username));
    }
}
