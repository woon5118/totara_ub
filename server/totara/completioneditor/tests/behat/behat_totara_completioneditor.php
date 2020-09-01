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
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Matthias Bonk <matthias.bonk@totaralearning.com>
 * @package totara_completioneditor
 */

class behat_totara_completioneditor extends behat_base {

    /**
     * Waits for the "Activity time completed" form field to exist.
     * For some reason the usual wait_for_pending_js doesn't work with rendering of that date field,
     * so it requires this extra step.
     *
     * @When /^I wait for Activity time completed form field to be ready$/
     * @return void
     */
    public function i_wait_for_activity_time_completed_form_field_to_be_ready(): void {
        behat_hooks::set_step_readonly(true);
        $this->execute("behat_general::wait_until_exists", ["[name='cmctimecompleted[isodate]']", "css_element"]);
    }

    /**
     * Waits for the "Activity time completed" form field to disappear.
     * For some reason the usual wait_for_pending_js doesn't work with rendering of that date field,
     * so it requires this extra step.
     *
     * @Then /^Activity time completed field should not exist$/
     * @return void
     */
    public function activity_time_completed_field_should_not_exist(): void {
        behat_hooks::set_step_readonly(true);
        $this->execute("behat_general::wait_until_does_not_exists", ["[name='cmctimecompleted[isodate]']", "css_element"]);
    }
}
