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

use mod_perform\controllers\activity\manage_activities;
use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\ExpectationException;
use mod_perform\controllers\activity\user_activities;
use mod_perform\controllers\activity\view_user_activity;

class behat_mod_perform extends behat_base {

    public const TUI_USER_ANSWER_ERROR_LOCATOR = '.tui-formFieldError';
    public const USER_QUESTION_TEXT_LOCATOR = '.tui-collapsible__header-text';

    /**
     * Navigate to the specified page and wait for JS.
     *
     * @param moodle_url $page_url
     */
    private function navigate_to_page(moodle_url $page_url): void {
        $this->getSession()->visit($this->locate_path($page_url->out(false)));
        $this->wait_for_pending_js();
    }

    /**
     * @When /^I navigate to the outstanding perform activities list page$/
     * @throws Exception
     */
    public function i_navigate_to_the_outstanding_perform_activities_page(): void {
        $this->navigate_to_page(user_activities::get_url());
    }

    /**
     * @When /^I navigate to the user activity page for id "([^"]*)"$/
     * @param int $subject_instance_id
     * @throws Exception
     */
    public function i_navigate_to_the_user_activity_profile_details_page_for_id(int $subject_instance_id): void {
        $this->navigate_to_page(view_user_activity::get_url(['subject_instance_id' => $subject_instance_id]));
    }

    /**
     * @When /^I navigate to the manage perform activities page$/
     * @throws Exception
     */
    public function i_navigate_to_the_manage_perform_activities_page(): void {
        $this->navigate_to_page(manage_activities::get_url());
    }

    /**
     * @Given /^I should see perform "([^"]*)" question "([^"]*)" is unanswered$/
     * @param string $element_type
     * @param string $question_text
     * @throws ExpectationException
     */
    public function i_should_see_perform_question_is_unanswered(string $element_type, string $question_text): void {
         $this->i_should_see_perform_question_is_answered_with($element_type, $question_text, '');
    }

    /**
     * @Given /^I should see perform "([^"]*)" question "([^"]*)" is answered with "([^"]*)"$/
     * @param $element_type
     * @param $question_text
     * @param $expected_answer_text
     * @throws ExpectationException
     */
    public function i_should_see_perform_question_is_answered_with(
        string $element_type,
        string $question_text,
        string $expected_answer_text
    ): void {
        $response = $this->find_question_response($element_type, $question_text);

        $actual_answer_text = trim($response->getText());

        if ($expected_answer_text !== $actual_answer_text) {
            throw new ExpectationException(
                "Expected answer to be \"{$expected_answer_text}\"  found \"{$actual_answer_text}\"",
                $this->getSession()
            );
        }
    }

    /**
     * @Then /^I should see "([^"]*)" has the validation error "([^"]*)"$/
     * @param string $question_text
     * @param string $expected_validation_error
     * @throws ExpectationException
     */
    public function i_should_see_perform_question_has_the_validation_error(
        string $question_text,
        string $expected_validation_error
    ): void {
        $question = $this->find_question_from_text($question_text);

        $validation_errors = $question->findAll('css', self::TUI_USER_ANSWER_ERROR_LOCATOR);

        foreach ($validation_errors as $error) {
            if ($expected_validation_error === trim($error->getText())) {
                return;
            }
        }

        throw new ExpectationException("Could not find validation error {$expected_validation_error}", $this->getSession());
    }

    /**
     * @Then /^I should see "([^"]*)" has no validation errors$/
     * @param string $question_text
     * @throws ExpectationException
     */
    public function i_should_see_perform_question_has_no_validation_errors(string $question_text): void {
        $question = $this->find_question_from_text($question_text);

        $validation_errors = $question->findAll('css', self::TUI_USER_ANSWER_ERROR_LOCATOR);

        if (count($validation_errors) > 0) {
            throw new ExpectationException(
                "Expected to not find any validation errors, found  {$validation_errors[0]->getText()}",
                $this->getSession()
            );
        }
    }

    /**
     * @When /^I answer "([^"]*)" question "([^"]*)" with "([^"]*)"$/
     * @param string $element_type
     * @param string $question_text
     * @param string $new_answer
     */
    public function i_answer_question_with(
        string $element_type,
        string $question_text,
        string $new_answer
    ): void {
        $response = $this->find_question_response($element_type, $question_text);

        $response->setValue($new_answer);
    }

    /**
     * @Given /^I answer "([^"]*)" question "([^"]*)" with "([^"]*)" characters$/
     * @param string $element_type
     * @param string $question_text
     * @param int $character_count
     */
    public function i_answer_question_with_characters(
        string $element_type,
        string $question_text,
        int $character_count
    ): void {
        $response = $this->find_question_response($element_type, $question_text);

        $new_answer = random_string($character_count);
        $response->setValue($new_answer);
    }



    private function find_question_response(string $element_type, string $question_text) {
        $question = $this->find_question_from_text($question_text);

        $response_locator = $this->get_response_element_response_locator($element_type);

        return $question->find('css', $response_locator);
    }

    private function get_response_element_response_locator(string $element_type): string {
        $map = [
            'short text' => 'textarea'
        ];

        $locator =  $map[$element_type] ?? null;

        if ($locator === null) {
            throw new ExpectationException("Invalid perform element type {$element_type}", $this->getSession());
        }

        return $locator;
    }

    private function find_question_from_text(string $question_text): NodeElement {
        /** @var NodeElement[] $questions */
        $questions = $this->find_all('css', '.tui-elementResponse');

        foreach ($questions as $question) {
            $found_question = $question->find('css', self::USER_QUESTION_TEXT_LOCATOR);

            if ($found_question === null) {
                continue;
            }

            if (trim($found_question->getText()) === $question_text) {
                return $question;
            }
        }

        throw new ExpectationException("Question not found with text ${$question_text}", $this->getSession());
    }

}
