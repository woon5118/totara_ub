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

    public const PERFORM_ELEMENT_VALIDATION_ERROR_LOCATOR = '.tui-formFieldError';
    public const PERFORM_ELEMENT_LOCATOR = '.tui-participantContent__sectionItem';
    public const PERFORM_ELEMENT_QUESTION_TEXT_LOCATOR = '.tui-collapsible__header-text';
    public const SHORT_TEXT_RESPONSE_LOCATOR = 'textarea';
    public const MULTI_CHOICE_RESPONSE_LOCATOR = 'radio';
    public const PERFORM_ELEMENT_OTHER_RESPONSE_CONTAINER_LOCATOR = '.tui-otherParticipantResponses';
    public const PERFORM_ELEMENT_OTHER_RESPONSE_RELATION_LOCATOR = '.tui-otherParticipantResponses__relation .tui-formLabel';
    public const SHORT_TEXT_ANSWER_LOCATOR = '.tui-shortTextElementParticipantResponse__answer';
    public const MULTI_CHOICE_ANSWER_LOCATOR = '.tui-elementEditMultiChoiceParticipantResponse__answer';
    public const PERFORM_ACTIVITY_YOUR_RELATIONSHIP_LOCATOR = '.tui-participantContent__user-relationshipValue';
    public const PERFORM_SHOW_OTHERS_RESPONSES_INPUT_LOCATOR = '.tui-participantContent__sectionHeading-switch input';
    public const PERFORM_SHOW_OTHERS_RESPONSES_LABEL_LOCATOR = '.tui-participantContent__sectionHeading-switch label';
    public const MANAGE_CONTENT_PARTICIPANT_ROW_LOCATOR = '.mod-perform-activitySection__participant-row';
    public const MANAGE_CONTENT_ADD_PARTICIPANTS_BUTTON_LABEL = ".mod-perform-activitySection [aria-label='Add participants']";

    public const TUI_USER_ANSWER_ERROR_LOCATOR = '.tui-formFieldError';
    public const USER_QUESTION_TEXT_LOCATOR = '.tui-collapsible__header-text';
    public const TUI_TAB_ELEMENT = '.tui-tabs__tabs';
    public const SCHEDULE_SAVE_LOCATOR = '.tui_performAssignmentSchedule__action .tui-formBtn--prim';

    public const TUI_TRASH_ICON_BUTTON = "button[aria-label='Delete']";

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
     * @Given /^I should see perform activity relationship to user "([^"]*)"$/
     * @param string $expected_relation
     * @throws ExpectationException
     */
    public function i_should_see_perform_activity_relationship_to_user(string $expected_relation): void {
        $your_relationship_element = $this->find('css', self::PERFORM_ACTIVITY_YOUR_RELATIONSHIP_LOCATOR);

        if ($expected_relation === trim($your_relationship_element->getText())) {
            return;
        }

        throw new ExpectationException(
            "Could not find expected relationship to user {$expected_relation}",
            $this->getSession()
        );
    }

    /**
     * @Then /^I click show others responses$/
     * @readonly
     */
    public function i_click_show_others_responses(): void {
        $this->find('css', self::PERFORM_SHOW_OTHERS_RESPONSES_LABEL_LOCATOR)->click();
    }

    /**
     * @Then /^I should see that show others responses is toggled "([(on)|(off)]*)"$/
     * @param string|bool $expected_state
     * @throws ExpectationException
     */
    public function i_should_see_that_show_others_responses_is(string $expected_state): void {
        $expected_state = $expected_state === 'on';

        $checked = $this->find('css', self::PERFORM_SHOW_OTHERS_RESPONSES_INPUT_LOCATOR)->isChecked();

        if ($checked !== $expected_state) {
            throw new ExpectationException('Others responses toggle was not on', $this->getSession());
        }
    }

    /**
     * @Given /^I should see perform "([^"]*)" question "([^"]*)" is answered by "([^"]*)" with "([^"]*)"$/
     * @param $element_type
     * @param $question_text
     * @param $expected_relation
     * @param $expected_answer_text
     * @throws ExpectationException
     */
    public function i_should_see_perform_question_is_answered_by_with(
        string $element_type,
        string $question_text,
        string $expected_relation,
        string $expected_answer_text
    ): void {
        $has_relation = false;
        $has_answer = false;
        $question = $this->find_question_from_text($question_text);
        $other_response_element = $question->find('css', self::PERFORM_ELEMENT_OTHER_RESPONSE_CONTAINER_LOCATOR);

        $relations = $other_response_element->findAll('css', self::PERFORM_ELEMENT_OTHER_RESPONSE_RELATION_LOCATOR);
        foreach ($relations as $relation) {
            if ((strpos(trim($relation->getText()), $expected_relation) === false)) {
                continue;
            }
            $has_relation = true;
        }

        $other_responses = $this->find_question_other_responses_by_element($element_type, $other_response_element);
        foreach ($other_responses as $other_response) {
            if ($expected_answer_text !== trim($other_response->getText())) {
                continue;
            }
            $has_answer = true;
        }

        if ($has_relation && $has_answer) {
            return;
        }

        throw new ExpectationException(
            "Could not find expected other response by {$expected_relation} with {$expected_answer_text}",
            $this->getSession()
        );
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

        $validation_errors = $question->findAll('css', self::PERFORM_ELEMENT_VALIDATION_ERROR_LOCATOR);

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

        $validation_errors = $question->findAll('css', self::PERFORM_ELEMENT_VALIDATION_ERROR_LOCATOR);

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

    /**
     * @When /^I navigate to manage perform activity content page$/
     */
    public function i_navigate_to_manage_perform_activity_content_page(): void {
        $behat_general = behat_context_helper::get('behat_general');
        $behat_general->i_click_on_in_the(
            "Content",
            "link",
            self::TUI_TAB_ELEMENT,
            "css_element"
        );
        $behat_general->i_click_on("Edit content","button");
    }

    /**
     * @When /^I save the activity schedule$/
     */
    public function i_save_the_activity_schedule(): void {
        $this->find('css', self::SCHEDULE_SAVE_LOCATOR)->click();
    }

    private function find_question_response(string $element_type, string $question_text) {
        $question = $this->find_question_from_text($question_text);

        $response_locator = $this->get_response_element_response_locator($element_type);

        return $question->find('css', $response_locator);
    }

    private function find_question_other_responses_by_element(string $element_type, NodeElement $other_responses) {
        $map = [
            'short text' => self::SHORT_TEXT_ANSWER_LOCATOR,
            'multi choice' => self::MULTI_CHOICE_ANSWER_LOCATOR
        ];

        $locator =  $map[$element_type] ?? null;
        if ($locator === null) {
            throw new ExpectationException("Invalid perform element type {$element_type}", $this->getSession());
        }

        return $other_responses->findAll('css', $locator);
    }

    private function get_response_element_response_locator(string $element_type): string {
        $map = [
            'short text' => self::SHORT_TEXT_RESPONSE_LOCATOR,
            'multi choice' => self::MULTI_CHOICE_RESPONSE_LOCATOR
        ];

        $locator =  $map[$element_type] ?? null;

        if ($locator === null) {
            throw new ExpectationException("Invalid perform element type {$element_type}", $this->getSession());
        }

        return $locator;
    }

    private function find_question_from_text(string $question_text): NodeElement {
        /** @var NodeElement[] $questions */
        $questions = $this->find_all('css', self::PERFORM_ELEMENT_LOCATOR);

        foreach ($questions as $question) {
            $found_question = $question->find('css', self::PERFORM_ELEMENT_QUESTION_TEXT_LOCATOR);

            if ($found_question === null) {
                continue;
            }

            if (trim($found_question->getText()) === $question_text) {
                return $question;
            }
        }

        throw new ExpectationException("Question not found with text {$question_text}", $this->getSession());
    }

    /**
     * @Then /^I should see no perform activity participants$/
     */
    public function i_should_see_no_participants(): void {
        $this->ensure_element_does_not_exist(
            self::MANAGE_CONTENT_PARTICIPANT_ROW_LOCATOR,
            'css_element'
        );
    }

    /**
     * @Then /^I should see "([^"]*)" as the perform activity participants$/
     */
    public function i_should_see_as_the_participants($expected_participant_list): void {
        $expected_participants = explode(',', $expected_participant_list);

        /** @var NodeElement[] $rows */
        $rows = $this->find_all('css', self::MANAGE_CONTENT_PARTICIPANT_ROW_LOCATOR);

        foreach ($expected_participants as $index => $expected_participant) {
            if (trim($rows[$index]->getText()) !== trim($expected_participant)) {
                $this->fail("{$expected_participant} was not found in the {$index} position");
            }
        }
    }

    /**
     * @When /^I click the add perform activity participant button$/
     */
    public function i_click_the_add_participant_button(): void {
        $this->find(
            'css',
            self::MANAGE_CONTENT_ADD_PARTICIPANTS_BUTTON_LABEL
        )->click();
    }

    /**
     * @When /^I should see the add perform activity participant button is disabled$/
     */
    public function i_should_see_the_add_participant_button_is_disabled(): void {
        $this->execute('behat_general::the_element_should_be_disabled',
            [self::MANAGE_CONTENT_ADD_PARTICIPANTS_BUTTON_LABEL, 'css_element']
        );
    }

    /**
     * @When /^I should see the add perform activity participant button$/
     */
    public function i_should_see_the_add_participant_button(): void {
        $this->ensure_element_exists(
            self::MANAGE_CONTENT_ADD_PARTICIPANTS_BUTTON_LABEL,
            'css_element'
        );
    }

    /**
     * @When /^I remove "([^"]*)" as a perform activity participant$/
     * @param string $participant_to_remove
     */
    public function i_remove_as_a_participant(string $participant_to_remove): void {
        /** @var NodeElement[] $rows */
        $rows = $this->find_all('css', self::MANAGE_CONTENT_PARTICIPANT_ROW_LOCATOR);

        foreach ($rows as $participant_row) {
            if (trim($participant_row->getText()) === $participant_to_remove) {
                $participant_row->find('css', self::TUI_TRASH_ICON_BUTTON)->click();
                return;
            }
        }

        $this->fail("{$participant_to_remove} participant not found");
    }

    /**
     * Convenience method to fail from an ExpectationException.
     *
     * @param string $error error message.
     */
    private function fail(string $error): void {
        throw new ExpectationException($error, $this->getSession());
    }

}
