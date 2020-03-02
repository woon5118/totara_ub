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
 * @author Mark Metcalfe <mark.metcalfe@totaralearning.com>
 * @package totara_competency
 */

use Behat\Gherkin\Node\TableNode as TableNode;
use totara_competency\achievement_configuration;
use totara_competency\entities\assignment;
use totara_competency\entities\competency;
use totara_competency\models\assignment as assignment_model;
use Behat\Mink\Exception\ExpectationException;
use core\entities\user;

class behat_totara_competency extends behat_base {

    private const COMPETENCY_PROFILE_LIST_VIEW_TOGGLE_LOCATOR = '.tui-iconBtn--toggle .fa-th-list';

    private const TOTARA_COMPETENCY_PROFILE_PATH = 'totara/competency/profile/';
    private const TOTARA_COMPETENCY_PROFILE_DETAIL_PATH = 'totara/competency/profile/details';
    private const TOTARA_COMPETENCY_USER_ASSIGNMENT_PATH = 'totara/competency/profile/assign/';

    /**
     * @var totara_competency_generator
     */
    protected $generator;

    /**
     * Opens the current users competency profile.
     *
     * @When /^I navigate to my competency profile$/
     * @return void
     * @throws Exception
     */
    public function i_navigate_to_my_competency_profile(): void {
        $this->getSession()->visit($this->locate_path(self::TOTARA_COMPETENCY_PROFILE_PATH));
        $this->wait_for_pending_js();
    }

    /**
     * @When /^I navigate to the competency self assignment page$/
     * @return void
     * @throws Exception
     */
    public function i_navigate_to_the_competency_self_assignment_page(): void {
        $this->getSession()->visit($this->locate_path(self::TOTARA_COMPETENCY_USER_ASSIGNMENT_PATH));
        $this->wait_for_pending_js();
    }

    /**
     * @Then /^I should be on my competency profile$/
     */
    public function i_should_be_on_my_competency_profile(): void {
        $expected_path = $this->normalize_index_url($this->locate_path(self::TOTARA_COMPETENCY_PROFILE_PATH));
        $actual_url = $this->normalize_index_url($this->getSession()->getCurrentUrl());

        if ($expected_path !== $actual_url) {
            $exception_message = "Expected the current url to be {$expected_path}, instead was {$actual_url}";
            throw new ExpectationException($exception_message, $this->getSession());
        }
    }

    /**
     * @Given /^I navigate to the competency user assignment page for guest user$/
     * @return void
     * @throws Exception
     */
    public function i_navigate_to_the_competency_user_assignment_page_for_guest_user(): void {
        /** @var User $user */
        $user = User::repository()
            ->select('id')
            ->where('username', 'guest')
            ->order_by('id')
            ->first();

        $url = $this->locate_path((string) new moodle_url(self::TOTARA_COMPETENCY_USER_ASSIGNMENT_PATH, ['user_id' => $user->id]));

        $this->getSession()->visit($url);
        $this->wait_for_pending_js();
    }

    /**
     * @When /^I change the competency profile to list view$/
     */
    public function i_change_the_competency_profile_to_list_view(): void {
        $this->find('css', self::COMPETENCY_PROFILE_LIST_VIEW_TOGGLE_LOCATOR)->click();
    }

    /**
     * Create a scale with a name and scale values.
     *
     * @Given /^a competency scale called "(?P<scalename_string>(?:[^"]|\\")*)" exists with the following values:$/
     * @param string $scalename
     * @param TableNode $table
     * @throws Exception
     */
    public function competency_scale_called_exists($scalename, TableNode $table) {
        \behat_hooks::set_step_readonly(true); // Backend action.

        $scale_values = $this->parse_table(
            $table,
            ['name', 'proficient', 'default', 'sortorder'],
            ['idnumber', 'description']
        );

        $this->get_data_generator()->create_scale($scalename, null, $scale_values);
    }

    /**
     * Create the default achievement paths for a competency.
     *
     * @Given /^the default achievement paths exist for the "(?P<competency_string>(?:[^"]|\\")*)" competency$/
     * @param string $competency
     */
    public function the_default_achievement_paths_exist_for_the_competency($competency) {
        global $DB;
        \behat_hooks::set_step_readonly(true); // Backend action.

        $competency_id = $DB->get_field(competency::TABLE, 'id', ['idnumber' => $competency]);
        $config = new achievement_configuration(new competency($competency_id));
        $config->link_default_preset();
    }

    /**
     * Archive all assignments for a given competency.
     *
     * @Given /^all assignments for the "(?P<competency_string>(?:[^"]|\\")*)" competency are archived$/
     * @param string $competency
     */
    public function all_assignments_for_the_competency_are_archived($competency) {
        \behat_hooks::set_step_readonly(true); // Backend action.

        $assignments = assignment::repository()
            ->filter_by_active()
            ->join([competency::TABLE, 'comp'], 'competency_id', 'id')
            ->where('comp.idnumber', $competency)
            ->get_lazy();
        foreach ($assignments as $assignment) {
            assignment_model::load_by_entity($assignment)->archive();
        }
    }

    /**
     * @Given /^I navigate to the competency profile details page for the "([^"]*)" competency$/
     * @Given /^I navigate to the competency profile details page for competency id "([^"]*)"$/
     * @Given /^I navigate to the competency profile details page for the "([^"]*)" competency and user "([^"]*)"$/
     * @param string|int $competency
     * @param string|int|null $user
     * @throws moodle_exception
     */
    public function i_navigate_to_the_competency_profile_details_page_for($competency, $user = null): void {
        $competency_id = $this->resolve_competency_id($competency);
        $query_params = ['competency_id' => $competency_id];

        if ($user) {
            $query_params['user_id'] = $this->resolve_user_id($user);
        }

        $detail_page_url = new moodle_url(self::TOTARA_COMPETENCY_PROFILE_DETAIL_PATH, $query_params);

        $this->getSession()->visit($this->locate_path($detail_page_url->out(false)));
        $this->wait_for_pending_js();
    }

    private function resolve_competency_id($competency): int {
        if (is_numeric($competency)) {
            return $competency;
        }

        /** @var competency $competency */
        $competency = competency::repository()->where('fullname', $competency)->get()->first();

        return $competency->id;
    }

    private function resolve_user_id($user): int {
        if (is_numeric($user)) {
            return $user;
        }

        /** @var user $user_entity */
        $user_entity = user::repository()->where('name', $user)->get()->first();

        return $user_entity->id;
    }

    /**
     * Turn the table into an array of key=>value records.
     *
     * @param TableNode $table
     * @param array $required_columns
     * @param array $optional_columns
     * @return array
     * @throws Exception
     */
    private function parse_table(TableNode $table, array $required_columns, array $optional_columns = []): array {
        $table = $table->getHash();
        $first_row = reset($table);

        // Check required fields are present.
        foreach ($required_columns as $column) {
            if (!isset($first_row[$column])) {
                throw new Exception("The {$column} field must be defined!");
            }
        }

        // Copy values, ready to pass on to the generator.
        $records = [];

        foreach ($table as $row) {
            $record = [];
            foreach ($row as $field_name => $value) {
                if (in_array($field_name, $required_columns)) {
                    $record[$field_name] = $value;
                } else if (in_array($field_name, $optional_columns)) {
                    $record[$field_name] = $value;
                } else {
                    throw new Exception("Unknown field {$field_name} in the table definition!");
                }
            }
            $records[] = $record;
        }

        return $records;
    }

    /**
     * @return totara_competency_generator
     */
    private function get_data_generator() {
        global $CFG;
        require_once($CFG->dirroot . '/totara/competency/tests/generator/totara_competency_generator.class.php');

        if (is_null($this->generator)) {
            $this->generator = new totara_competency_generator(new testing_data_generator());
        }

        return $this->generator;
    }

    private function normalize_index_url(string $url): string {
        return str_replace($url, 'index.php', '');
    }

}
