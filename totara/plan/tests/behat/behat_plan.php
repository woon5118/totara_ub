<?php
/*
 * This file is part of Totara LMS
 *
 * Copyright (C) 2015 onwards Totara Learning Solutions LTD
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
 * @author Rob Tyler <rob.tyler@totaralms.com>
 * @package totara_plan
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../../lib/behat/behat_base.php');

use Behat\Behat\Context\Step\Given as Given,
    Behat\Gherkin\Node\TableNode as TableNode,
    Behat\Mink\Exception\ExpectationException as ExpectationException;


/**
 * The Totara Plan behat definition class.
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright Copyright (C) 2010-2013 Totara Learning Solutions LTD
 */
class behat_plan extends behat_base {

    /**
     * Create an empty learning plan as the given user.
     *
     * @Given /^I create a basic learning plan called "([^"]*)" for "([^"]*)"$/
     * @param string $plan_name Learning plan name
     * @param string $learner_username Learners username to have the learning plan created.
     * @return array Behat steps.
     */
    public function i_create_a_basic_learning_plan_called_for($plan_name, $learner_username) {
        global $DB, $USER;

        // Get the user data for the learner.
        $learner_record = $DB->get_record('user', array('username' => $learner_username), 'id');

        if (!$learner_record) {
            throw new ExpectationException('Learner username "' . $learner_username . '" does not exist', $this->getSession());
        }

        $steps = array ();
        // Create a basic learning plan.
        $steps[] = new Given('I log in as "' . $learner_username . '"');
        $steps[] = new Given('I focus on "My Learning" "link"');
        $steps[] = new Given('I follow "Learning Plans"');
        $steps[] = new Given('I press "Create new learning plan"');
        $steps[] = new Given('I set the field "id_name" to "' . $plan_name . '"');
        $steps[] = new Given('I press "Create plan"');
        $steps[] = new Given('I should see "Plan creation successful"');
        $steps[] = new Given('I log out');

        return $steps;
    }

    /**
     * Create an objective for tha learning plan.
     *
     * @Given /^I create an objective called "([^"]*)"$/
     * @param string $objective_name Objective name
     * @return array Behat steps.
     */
    public function i_create_an_objective_called($objective_name) {

        $steps = array ();
        $steps[] = new Given('I press "Add new objective"');
        $steps[] = new Given('I set the field "fullname" to "' . $objective_name . '"');
        $steps[] = new Given('I press "Add objective"');
        $steps[] = new Given('I should see "Objective created"');

        return $steps;
    }

}
