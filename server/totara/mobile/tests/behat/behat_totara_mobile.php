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
 * @author Chris Snyder <chris.snyder@totaralearning.com>
 * @package totara_mobile
 */

require_once(__DIR__ . '/../../../../lib/behat/behat_base.php');

/**
 * Class step definitions for Totara mobile plugin.
 *
 */
class behat_totara_mobile extends behat_base {

    /**
     * Opens the mobile device emulator
     *
     * @Given /^I am using the mobile emulator$/
     */
    public function i_am_using_modile_emulator() {
        \behat_hooks::set_step_readonly(false);
        $this->getSession()->visit($this->locate_path('/totara/mobile/device_emulator.php'));
        $this->wait_for_pending_js();
    }

    /**
     * Tries to detect the mobile app installer page at https://mobile.totaralearning.com/register/
     *
     * @Given /^I am at the totara mobile app installer$/
     */
    public function i_am_at_the_totara_mobile_app_installer() {
        $this->assertSession()->addressEquals('https://mobile.totaralearning.com/register/');
    }

}
