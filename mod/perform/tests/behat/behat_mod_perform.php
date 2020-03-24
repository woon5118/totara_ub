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
 * @author Jaron Steenson <jaron.steenson@totaralearning.com>
 * @package mod_perform
 */


use mod_perform\controllers\activity\user_activities;
use mod_perform\controllers\activity\view_user_activity;

class behat_mod_perform extends behat_base {

    /**
     * @When /^I navigate to the outstanding perform activities list page$/
     * @throws Exception
     */
    public function i_navigate_to_the_outstanding_perform_activities_page(): void {
        $page_url = user_activities::get_url();

        $this->getSession()->visit($this->locate_path($page_url->out(false)));
        $this->wait_for_pending_js();
    }

    /**
     * @When /^I navigate to the user activity page for id "([^"]*)"$/
     * @param int $subject_instance_id
     * @throws Exception
     */
    public function i_navigate_to_the_user_activity_profile_details_page_for_id(int $subject_instance_id): void {
        $page_url = view_user_activity::get_url();
        $page_url->param('id', $subject_instance_id);

        $this->getSession()->visit($this->locate_path($page_url->out(false)));
        $this->wait_for_pending_js();
    }

}
