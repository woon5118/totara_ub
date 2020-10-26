<?php
/**
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

use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\DriverException;
use Behat\Mink\Exception\ElementNotFoundException;
use Behat\Mink\Exception\ExpectationException;
use core\entities\user;
use core\orm\entity\entity;
use mod_perform\controllers\activity\edit_activity;
use mod_perform\controllers\activity\manage_activities;
use mod_perform\controllers\activity\manage_participation;
use mod_perform\controllers\activity\print_user_activity;
use mod_perform\controllers\activity\user_activities;
use mod_perform\controllers\activity\view_external_participant_activity;
use mod_perform\controllers\activity\view_user_activity;
use mod_perform\controllers\perform_controller;
use mod_perform\controllers\reporting\performance\view_only_user_activity;
use mod_perform\entities\activity\activity;
use mod_perform\entities\activity\external_participant;
use mod_perform\entities\activity\participant_instance;
use mod_perform\entities\activity\track;
use mod_perform\entities\activity\track_user_assignment;
use mod_perform\entities\activity\subject_instance;
use mod_perform\notification\factory;
use mod_perform\task\check_notification_trigger_task;

class behat_mod_perform extends behat_base {

    public const PERFORM_ELEMENT_VALIDATION_ERROR_LOCATOR = '.tui-formFieldError';
    public const PERFORM_ELEMENT_LOCATOR = '.tui-participantContent__sectionItem';
    public const PERFORM_ELEMENT_QUESTION_TEXT_LOCATOR = '.tui-participantContent__sectionItem-contentHeader';
    public const PERFORM_ELEMENT_QUESTION_OPTIONAL_LOCATOR = '.tui-performRequiredOptionalIndicator--optional';
    public const PERFORM_ELEMENT_QUESTION_REQUIRED_LOCATOR = '.tui-performRequiredOptionalIndicator--required';
    public const SHORT_TEXT_RESPONSE_LOCATOR = 'input';
    public const LONG_TEXT_RESPONSE_LOCATOR = 'textarea';
    public const MULTI_CHOICE_RESPONSE_LOCATOR = 'radio';
    public const PERFORM_ELEMENT_OTHER_RESPONSE_CONTAINER_LOCATOR = '.tui-otherParticipantResponses';
    public const PERFORM_ELEMENT_OTHER_RESPONSE_RELATION_LOCATOR = '.tui-otherParticipantResponses .tui-formLabel';
    public const TUI_OTHER_PARTICIPANT_RESPONSES_ANONYMOUS_RESPONSE_PARTICIPANT_LOCATOR = '.tui-otherParticipantResponses__anonymousResponse-participant';
    public const SHORT_TEXT_ANSWER_LOCATOR = '.tui-shortTextElementParticipantResponse__answer';
    public const MULTI_CHOICE_ANSWER_LOCATOR = '.tui-elementEditMultiChoiceSingleParticipantResponse__answer';
    public const PERFORM_ACTIVITY_PRINT_SECTION_LOCATOR = '.tui-participantContentPrint .tui-participantContentPrint__section .tui-participantContentPrint__section:nth-of-type(%d)';
    public const PERFORM_ACTIVITY_YOUR_RELATIONSHIP_VALUE_EXTERNAL = '.tui-participantContent__user-relationshipValue';
    public const PERFORM_ACTIVITY_GENERAL_INFORMATION_RELATIONSHIP_LOCATOR = '.tui-participantGeneralInformation__relationship-heading';
    public const PERFORM_SHOW_OTHERS_RESPONSES_LOCATOR = '.tui-participantContent__sectionHeading-otherResponseSwitch button';
    public const MANAGE_CONTENT_PARTICIPANT_NAME_LOCATOR = '.tui-performActivitySectionRelationship__item-name';
    public const MANAGE_CONTENT_ADD_RESPONDING_PARTICIPANTS_BUTTON_LABEL = '.tui-performManageActivityContent__items .tui-performActivitySection:nth-of-type(%d) [aria-label=\'Add participants\']';
    public const MANAGE_CONTENT_ADD_VIEW_ONLY_PARTICIPANTS_BUTTON_LABEL = '.tui-performManageActivityContent__items .tui-performActivitySection:nth-of-type(%d) [aria-label=\'Add view-only participants\']';
    public const MANAGE_CONTENT_ACTIVITY_SECTION = '.tui-performManageActivityContent__items .tui-performActivitySection:nth-of-type(%d)';
    public const MANAGE_CONTENT_ACTIVITY_SECTION_CONTENT_SUMMARY = '.tui-grid-item:nth-of-type(%d) .tui-performActivitySectionElementSummary__count';
    public const INSTANCE_INFO_CARD_LABEL_LOCATOR = '.tui-instanceInfoCard__info-label';
    public const INSTANCE_INFO_CARD_VALUE_LOCATOR = '.tui-instanceInfoCard__info-value';

    public const TUI_USER_ANSWER_ERROR_LOCATOR = '.tui-formFieldError';
    public const USER_QUESTION_TEXT_LOCATOR = '.tui-collapsible__header-text';
    public const TUI_TAB_ELEMENT = '.tui-tabs__tabs';
    public const SCHEDULE_SAVE_LOCATOR = '.tui-performAssignmentSchedule__form-buttons .tui-formBtn--prim';

    public const TUI_TRASH_ICON_BUTTON = "button[aria-label='Delete %s']";

    public const QUESTION_DISPLAY_LOCATOR = '.tui-performAdminCustomElement';
    public const EDIT_QUESTION_DISPLAY_TITLE_LOCATOR = '.tui-performAdminCustomElement__content-titleText';
    public const QUESTION_DRAG_ITEM_LOCATOR = '.tui-performEditSectionContentModal__draggableItem';
    public const QUESTION_DRAG_MOVE_ICON_LOCATOR = '.tui-performAdminCustomElement__moveIcon';
    public const RESPONSE_VISIBILITY_DESCRIPTION_LOCATOR = '.tui-participantContent__sectionHeadingOtherResponsesDescription';

    /**
     * Navigate to the specified page and wait for JS.
     *
     * @param moodle_url $page_url
     */
    private function navigate_to_page(moodle_url $page_url): void {
        behat_hooks::set_step_readonly(false);

        $this->getSession()->visit($this->locate_path($page_url->out(false)));
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
     * @param int $participant_instance_id
     * @throws Exception
     */
    public function i_navigate_to_the_user_activity_profile_details_page_for_id(int $participant_instance_id): void {
        $this->navigate_to_page(view_user_activity::get_url(['participant_instance_id' => $participant_instance_id]));
    }

    /**
     * @When /^I navigate to the manage perform activities page$/
     * @throws Exception
     */
    public function i_navigate_to_the_manage_perform_activities_page(): void {
        $this->navigate_to_page(new moodle_url(manage_activities::URL));
    }

    /**
     * @When /^I navigate to the edit perform activities page for activity "([^"]*)"$/
     * @param string $activity_name
     */
    public function i_navigate_to_the_edit_perform_activities_page_for(string $activity_name): void {
        $activity = $this->get_activity_by_name($activity_name);
        $this->navigate_to_page(edit_activity::get_url(['activity_id' => $activity->id]));
    }

    /**
     * @When /^I navigate to the external participants form for user "([^"]*)"$/
     * @param string|null $user_fullname
     */
    public function i_navigate_to_the_external_participant_form_for_user(string $user_fullname = null): void {
        /** @var external_participant $external_participant */
        $external_participant = external_participant::repository()
            ->where('name', $user_fullname)
            ->one();

        if (!$external_participant) {
            $this->fail("External participant with name '{$user_fullname}' not found.");
        }

        $this->navigate_to_page(view_external_participant_activity::get_url(['token' => $external_participant->token]));
    }

    /**
     * @When /^I navigate to the external participants form with the wrong token$/
     */
    public function i_navigate_to_the_external_participant_form_wrong_token(): void {
        $this->navigate_to_page(view_external_participant_activity::get_url(['token' => 'idontexist']));
    }

    /**
     * @Then /^I should see perform "([^"]*)" question "([^"]*)" is unanswered$/
     * @param string $element_type
     * @param string $question_text
     * @throws ExpectationException
     */
    public function i_should_see_perform_question_is_unanswered(string $element_type, string $question_text): void {
         $this->i_should_see_perform_question_is_answered_with($element_type, $question_text, '');
    }

    /**
     * @Then /^activity section "(?P<section_number>\d+)" should exist$/
     * @param int $section_number
     */
    public function section_exists(int $section_number): void {
        $section_selector = sprintf(self::MANAGE_CONTENT_ACTIVITY_SECTION, $section_number);
        $this->execute('behat_general::should_exist', [$section_selector, 'css_element']);
    }

    /**
     * @Then /^activity section "(?P<section_number>\d+)" should not exist$/
     * @param int $section_number
     */
    public function section_not_exists(int $section_number): void {
        $section_selector = sprintf(self::MANAGE_CONTENT_ACTIVITY_SECTION, $section_number);
        $this->execute('behat_general::should_not_exist', [$section_selector, 'css_element']);
    }

    /**
     * @Then /^"(?P<element_string>(?:[^"]|\\")*)" "(?P<selector_string>(?:[^"]|\\")*)" in the activity section should exist$/
     * @Then /^"(?P<element_string>(?:[^"]|\\")*)" "(?P<selector_string>(?:[^"]|\\")*)" in the "(?P<section_number>\d+)" activity section should exist$/
     *
     * @param string $element
     * @param string $selector_type
     * @param int $section_number
     * @return void
     */
    public function element_in_section_exist(string $element, string $selector_type, int $section_number = 1): void {
        $section_node = $this->get_section_node($section_number);
        $this->find_element_in_container($section_node, $element, $selector_type, true);
    }

    /**
     * @Then /^"(?P<element_string>(?:[^"]|\\")*)" "(?P<selector_string>(?:[^"]|\\")*)" in the activity section should not exist$/
     * @Then /^"(?P<element_string>(?:[^"]|\\")*)" "(?P<selector_string>(?:[^"]|\\")*)" in the "(?P<section_number>\d+)" activity section should not exist$/
     *
     * @param string $element
     * @param string $selector_type
     * @param int $section_number
     * @return void
     */
    public function element_in_section_no_exist(string $element, string $selector_type, int $section_number = 1): void {
        $section_node = $this->get_section_node($section_number);
        $element_found = $this->find_element_in_container($section_node, $element, $selector_type, false);
        if ($element_found !== null) {
            throw new ExpectationException(
                'The element "' . $element_found . '" in the section '.$section_number.' should not exist but was found.',
                $this->getSession()
            );
        }
    }

    /**
     * @Then /^I should see "(?P<text_string>(?:[^"]|\\")*)" in the activity section$/
     * @Then /^I should see "(?P<text_string>(?:[^"]|\\")*)" in the "(?P<section_number>\d+)" activity section$/
     *
     * @param string $text
     * @param int $section_number
     * @return void
     */
    public function i_should_see_in_section(string $text, int $section_number = 1): void {
        $section_selector = sprintf(self::MANAGE_CONTENT_ACTIVITY_SECTION, $section_number);
        $this->execute('behat_general::assert_element_contains_text',
            [$text, $section_selector, 'css_element']
        );
    }

    /**
     * @Then /^I should not see "(?P<text_string>(?:[^"]|\\")*)" in the activity section$/
     * @Then /^I should not see "(?P<text_string>(?:[^"]|\\")*)" in the "(?P<section_number>\d+)" activity section$/
     *
     * @param string $text
     * @param int $section_number
     * @return void
     */
    public function i_should_not_see_in_section(string $text, int $section_number = 1): void {
        $section_selector = sprintf(self::MANAGE_CONTENT_ACTIVITY_SECTION, $section_number);
        $this->execute('behat_general::assert_element_not_contains_text',
            [$text, $section_selector, 'css_element']
        );
    }

    /**
     * @When /^I click on "(?P<element_string>(?:[^"]|\\")*)" "(?P<selector_string>(?:[^"]|\\")*)" in the activity section$/
     * @When /^I click on "(?P<element_string>(?:[^"]|\\")*)" "(?P<selector_string>(?:[^"]|\\")*)" in the "(?P<section_number>\d+)" activity section$/
     *
     * @param string $element
     * @param string $selector_type
     * @param int $section_number
     * @return void
     */
    public function i_click_on_css_element_in_section(string $element, string $selector_type, int $section_number = 1): void {
        behat_hooks::set_step_readonly(false);

        $section_node = $this->get_section_node($section_number);
        $element_found = $this->find_element_in_container($section_node, $element, $selector_type);
        $element_found->click();
    }

    /**
     * @When /^I click on "(?P<element_string>(?:[^"]|\\")*)" "(?P<selector_string>(?:[^"]|\\")*)" in the "(?P<container_element_string>(?:[^"]|\\")*)" "(?P<container_selector_string>(?:[^"]|\\")*)" of the activity section$/
     * @When /^I click on "(?P<element_string>(?:[^"]|\\")*)" "(?P<selector_string>(?:[^"]|\\")*)" in the "(?P<container_element_string>(?:[^"]|\\")*)" "(?P<container_selector_string>(?:[^"]|\\")*)" of the "(?P<section_number>\d+)" activity section$/
     *
     * @param string $element
     * @param string $selector_type
     * @param string $container_element
     * @param string $container_selector
     * @param int $section_number
     * @return void
     */
    public function i_click_on_the_in_the_section(
        string $element,
        string $selector_type,
        string $container_element,
        string $container_selector,
        int $section_number = 1
    ): void {
        behat_hooks::set_step_readonly(false);

        $section_node = $this->get_section_node($section_number);
        $container_found = $this->find_element_in_container($section_node, $container_element, $container_selector);
        $element_found = $this->find_element_in_container($container_found, $element, $selector_type);
        $element_found->click();
    }

    /**
     * @When /^I set the title of activity section "(?P<section_number>\d+)" to "([^"]*)"$/
     * @When /^I set the title of activity section "(?P<section_number>\d+)" to '([^']*)'$/
     *
     * @param int $section_number
     * @param string $section_title
     * @return void
     */
    public function i_set_the_title_of_section_to(int $section_number, string $section_title): void {
        behat_hooks::set_step_readonly(false);

        $section_node = $this->get_section_node($section_number, true);

        $editing_node = $section_node->find('css', '.tui-performActivitySection--editing');
        if ($editing_node === null) {
            throw new ExpectationException("Section {$section_number} is not in edit mode", $this->getSession());
        }

        $editing_node->find('css', 'input')->setValue($section_title);
    }

    /**
     * @Given /^I should see perform "([^"]*)" question "([^"]*)" is saved with options "([^"]*)"$/
     * @param $question_text
     * @param $question_options
     *
     * @throws ExpectationException
     */
    public function i_should_see_multiple_answers_question_is_saved_with_options(
        string $type,
        string $question_text,
        string $question_options
    ): void {
        /** @var behat_mod_perform $behat_mod_perform */
        $locator = ($type == 'checkbox') ? '.tui-checkbox__label' : '.tui-radio__label';
        $question = $this->find_admin_question_from_text($question_text);
        $options = $question->findAll('css', $locator);
        $expected_options = explode(",", $question_options);
        $actual_options = [];
        foreach ($options as $option) {
            $actual_options[] = trim($option->getText());
        }
        if ($expected_options != $actual_options) {
            throw new ExpectationException("Question {$question_text} not found with options {$question_options}", $this->getSession());
        }
    }

    /**
     * Get the node for the given activity section.
     *
     * @param int $section_number
     * @param bool $required
     * @return NodeElement|null
     */
    private function get_section_node(int $section_number, bool $required = true): ?NodeElement {
        $section_selector = sprintf(self::MANAGE_CONTENT_ACTIVITY_SECTION, $section_number);

        // Transforming from steps definitions selector/locator format to Mink format and getting the NodeElement.
        $section_node = $this->get_selected_node('css_element', $section_selector);

        if ($required && $section_node === null) {
            throw new ExpectationException(
                'The element "' . $section_node . '" container element could not be found',
                $this->getSession()
            );
        }

        return $section_node;
    }

    /**
     * Find element in given container
     *
     * @param NodeElement $container
     * @param string $element
     * @param string $selector_type
     * @param bool $required
     * @return NodeElement|null
     */
    private function find_element_in_container(
        NodeElement $container,
        string $element,
        string $selector_type,
        bool $required = true
    ): ?NodeElement {
        [$element_selector, $element_locator] = $this->transform_selector($selector_type, $element);

        $element_found = $container->find($element_selector, $element_locator);

        if ($required && $element_found === null) {
            throw new ExpectationException(
                'The element "' . $element . '" in the type '.$selector_type.' could not be found.',
                $this->getSession()
            );
        }

        return $element_found;
    }

    /**
     * @Then /^I should see "(?P<text_string>(?:[^"]|\\")*)" in the "(?P<summary_item>(?:[^"]|\\")*)" element summary of the activity section$/
     * @Then /^I should see "(?P<text_string>(?:[^"]|\\")*)" in the "(?P<summary_item>(?:[^"]|\\")*)" element summary of "(?P<section_number>\d+)" activity section$/
     *
     * @param int $count
     * @param string $summary_item
     * @param int $section_number
     * @return void
     */
    public function i_should_see_in_section_summary(int $count, string $summary_item, int $section_number = 1): void {
        $summary_map = [
            'required' => 1,
            'optional' => 2,
            'other' => 3,
        ];
        $css_selector = sprintf(
            self::MANAGE_CONTENT_ACTIVITY_SECTION . ' ' . self::MANAGE_CONTENT_ACTIVITY_SECTION_CONTENT_SUMMARY,
            $section_number, $summary_map[$summary_item]
        );
        $this->execute('behat_general::assert_element_contains_text',
            [$count, $css_selector, 'css_element']
        );
    }

    /**
     * @Then /^I should see perform "([^"]*)" question is "([^"]*)"$/
     * @param string $question_text
     * @param string $required
     *
     * @throws ExpectationException
     */
    public function i_should_see_perform_question_is_required(string $question_text, string $required): void {
        $is_required = $required == 'required';
        $this->find_required_question_from_text($question_text, $is_required);
    }

    /**
     * @Then /^I should see perform "([^"]*)" question "([^"]*)" is answered with "([^"]*)"$/
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
        $this->wait_for_pending_js();

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
     * @Then /^I should see perform activity relationship to user "([^"]*)" as an "([(internal)|(external)]*)" participant$/
     * @Then /^I should see perform activity relationship to user "([^"]*)"$/
     *
     * @param string $expected_relation
     * @param string $participant_source
     */
    public function i_should_see_perform_activity_relationship_to_user(string $expected_relation, string $participant_source = 'internal'): void {
        $locator = $participant_source === 'internal'
            ? self::PERFORM_ACTIVITY_GENERAL_INFORMATION_RELATIONSHIP_LOCATOR
            : self::PERFORM_ACTIVITY_YOUR_RELATIONSHIP_VALUE_EXTERNAL;

        $this->execute('behat_general::assert_element_contains_text',
            [$expected_relation, $locator, 'css_element']
        );
    }

    /**
     * @Then /^I should (|not )see "([^"]*)" in perform activity print section "([0-9]*)"$/
     *
     * @param string $should_or_should_not
     * @param string $expected_text
     * @param string $section_number
     */
    public function i_should_see_in_print_section(string $should_or_should_not, string $expected_text, string $section_number): void {
        $method = $should_or_should_not === 'not ' ? 'assert_element_not_contains_text' : 'assert_element_contains_text';
        $this->execute('behat_general::'. $method,
            [$expected_text, sprintf(self::PERFORM_ACTIVITY_PRINT_SECTION_LOCATOR, $section_number), 'css_element']
        );
    }

    /**
     * @When /^I click show others responses$/
     * @readonly
     */
    public function i_click_show_others_responses(): void {
        behat_hooks::set_step_readonly(false);

        $this->find('css', self::PERFORM_SHOW_OTHERS_RESPONSES_LOCATOR)->click();
    }

    /**
     * @Then /^I should see that show others responses is toggled "([(on)|(off)]*)"$/
     * @param string|bool $expected_state
     * @throws ExpectationException
     */
    public function i_should_see_that_show_others_responses_is(string $expected_state): void {
        if ($expected_state == 'on') {
            $checked = $this->find('css', self::PERFORM_SHOW_OTHERS_RESPONSES_LOCATOR)->hasAttribute('aria-pressed');

            if (!$checked) {
                throw new ExpectationException('Others responses toggle was not on', $this->getSession());
            }
        }
    }

    /**
     * @Then /^I should not see the show others responses toggle$/
     */
    public function i_should_not_see_the_show_others_responses_toggle(): void {
        try {
            $found = $this->find('css', self::PERFORM_SHOW_OTHERS_RESPONSES_LOCATOR, false, false, 0.1);

            if ($found !== null) {
                $this->fail('Show other responses toggle was found');
            }
        } catch (ElementNotFoundException $e) {
            // Element was not found.
        }
    }

    /**
     * @Then /^I should see perform "([^"]*)" question "([^"]*)" is unanswered by "([^"]*)"$/
     * @param $element_type
     * @param $question_text
     * @param $expected_relation
     */
    public function i_should_see_perform_question_is_unanswered_by(
        string $element_type,
        string $question_text,
        string $expected_relation
    ): void {
        $this->i_should_see_perform_question_is_answered_by_with(
            $element_type,
            $question_text,
            $expected_relation,
            null
        );
    }

    /**
     * @Then /^I should see perform "([^"]*)" question "([^"]*)" is answered by "([^"]*)" with "([^"]*)"$/
     * @param $element_type
     * @param $question_text
     * @param $expected_relation
     * @param $expected_answer_text
     */
    public function i_should_see_perform_question_is_answered_by_with(
        string $element_type,
        string $question_text,
        string $expected_relation,
        ?string $expected_answer_text
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

        if ($expected_answer_text === null && $has_relation) {
            return;
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
        behat_hooks::set_step_readonly(false);

        $this->wait_for_pending_js();

        $response = $this->find_question_response($element_type, $question_text);

        $response->setValue($new_answer);
    }

    /**
     * @Given /^I should see "([^"]*)" as answer "([0-9]*|any)" in the anonymous responses group for question "([^"]*)"$/
     * @param string $expected_answer
     * @param string $response_number
     * @param string $question_text
     */
    public function i_should_see_as_answer_in_the_anonymous_responses_group_for_question(
        string $expected_answer,
        string $response_number,
        string $question_text
    ): void {
        $question = $this->find_question_from_text($question_text);

        $anonymous_responses = $question->findAll('css', self::TUI_OTHER_PARTICIPANT_RESPONSES_ANONYMOUS_RESPONSE_PARTICIPANT_LOCATOR);

        if (is_number($response_number)) {
            $response_number = (int)$response_number;
            if ($response_number < 1) {
                $this->fail("Invalid response number {$response_number}. Expected 1 or above");
            }

            $actual_response = $anonymous_responses[(int)$response_number - 1];
            $actual_answer_text = trim($actual_response->getText());

            if ($actual_answer_text !== $expected_answer) {
                $this->fail("Expected response \"{$response_number}\" to be \"{$expected_answer}\", but found \"{$actual_answer_text}\"");
            }
        } else if ($response_number === 'any') {
            $fnd = false;
            foreach ($anonymous_responses as $response) {
                $actual_answer_text = trim($response->getText());
                if ($expected_answer === $actual_answer_text) {
                    $fnd = true;
                    break;
                }
            }

            if (!$fnd) {
                $this->fail("Expected response \"{$expected_answer}\" not found in question \"{$question_text}\"");
            }
        } else {
            $this->fail("Invalid response number \"{$response_number}\". Expected a number or \"any\"");
        }
    }

    /**
     * @When /^I answer "([^"]*)" question "([^"]*)" with "([^"]*)" characters$/
     * @param string $element_type
     * @param string $question_text
     * @param int $character_count
     */
    public function i_answer_question_with_characters(
        string $element_type,
        string $question_text,
        int $character_count
    ): void {
        behat_hooks::set_step_readonly(false);

        $response = $this->find_question_response($element_type, $question_text);

        $new_answer = random_string($character_count);
        $response->setValue($new_answer);
    }

    /**
     * @When /^I add a "([^"]*)" activity content element$/
     * @param string $element_name
     */
    public function i_add_a_custom_element(string $element_name): void {
        behat_hooks::set_step_readonly(false);

        /** @var behat_general $behat_general */
        $behat_general = behat_context_helper::get('behat_general');
        $behat_general->i_click_on("Add element","button");

        /** @var behat_totara_tui $behat_totara_tui */
        $behat_totara_tui = behat_context_helper::get('behat_totara_tui');
        $behat_totara_tui->i_click_on_dropdown_option($element_name);
    }

    /**
     * @When /^I (save|cancel saving) the activity content element$/
     * @param string $is_saving
     */
    public function i_save_the_custom_element_settings(string $is_saving): void {
        behat_hooks::set_step_readonly(false);

        $button_text = $is_saving === 'save' ? 'Done' : 'Cancel';

        /** @var behat_general $behat_general */
        $behat_general = behat_context_helper::get('behat_general');
        $behat_general->i_click_on_in_the($button_text, 'button', '.tui-performEditSectionContentModal__form', 'css_element');
    }

    /**
     * @param string $action_type
     * @param string $question_text
     * @return NodeElement|null
     */
    private function find_action_for_question(string $action_type, string $question_text): ?NodeElement {
        /** @var NodeElement[] $questions */
        $questions = $this->find_all('css', self::QUESTION_DISPLAY_LOCATOR);

        foreach ($questions as $question) {
            $title = $question->find('css', self::EDIT_QUESTION_DISPLAY_TITLE_LOCATOR);
            $actual_title = trim(str_replace('*','', $title->getText()));

            if ($actual_title == $question_text) {
                $action_button = $question
                    ->find('css', self::QUESTION_DISPLAY_LOCATOR)
                    ->find('css', 'button[title*="' . $action_type . '"]');
                if ($action_button) {
                    return $action_button;
                }
            }
        }
        return null;
    }

    /**
     * @When /^I click on the (Reporting ID|Edit element|Delete element) action for question "([^"]*)"$/
     * @param string $question_text
     */
    public function i_click_on_the_action_for_question(string $action_type, string $question_text): void {
        behat_hooks::set_step_readonly(false);

        $action = $this->find_action_for_question($action_type, $question_text);

        if (!$action) {
            throw new ExpectationException(
                "Action {$action_type} for question with text {$question_text} not found", $this->getSession()
            );
        }

        $action->click();
    }

    /**
     * @Then /^I (should|should not) see the (Reporting ID|Edit element|Delete element) action for question "([^"]*)"$/
     * @param string $should_or_not
     * @param string $action_type
     * @param string $question_text
     * @throws ExpectationException
     */
    public function i_should_see_action_for_question(string $should_or_not, string $action_type, string $question_text): void {
        behat_hooks::set_step_readonly(true);

        $should_be_visible = $should_or_not === 'should';
        $action = $this->find_action_for_question($action_type, $question_text);

        if ($should_be_visible && !$action) {
            throw new ExpectationException(
                "Action {$action_type} for question with text {$question_text} not found", $this->getSession()
            );
        }

        if (!$should_be_visible && $action) {
            throw new ExpectationException(
                "Action {$action_type} for question with text {$question_text} was visible when it shouldn't have",
                $this->getSession()
            );
        }
    }

    /**
     * @Then /^I should see drag icon visible in the question "([^"]*)"$/
     * @param string $question_text
     */
    public function i_should_see_drag_icon_visible(string $question_text) {
        $element = $this->find_element_drag_wrapper_from_text($question_text);

        $element->click();
        $move_icon = $element->find('css', self::QUESTION_DRAG_MOVE_ICON_LOCATOR);

        if (!$move_icon->isVisible()) {
            throw new ExpectationException("move icon should be visible", $this->getSession());
        }
    }

    /**
     * @Then /^I should not see drag icon visible in the question "([^"]*)"$/
     * @param string $question_text
     */
    public function i_should_not_see_drag_icon_visible(string $question_text) {
        $element = $this->find_element_drag_wrapper_from_text($question_text);

        $element->click();
        $move_icon = $element->find('css', self::QUESTION_DRAG_MOVE_ICON_LOCATOR);

        if ($move_icon) {
            throw new ExpectationException("move icon should not be visible", $this->getSession());
        }
    }

    /**
     * @param string $question_text
     *
     * @return NodeElement
     * @throws ExpectationException
     */
    private function find_element_drag_wrapper_from_text(string $question_text): NodeElement {
        $drag_items = $this->find_all('css', self::QUESTION_DRAG_ITEM_LOCATOR);

        foreach ($drag_items as $question) {
            $question_title = $question->find('css', self::EDIT_QUESTION_DISPLAY_TITLE_LOCATOR);

            $actual_title = trim(str_replace('*', '', $question_title->getText()));

            if ($actual_title === $question_text) {
                return $question;
            }
        }
        throw new ExpectationException("Question not found with text {$question_text}", $this->getSession());
    }

    /**
     * @When /^I close popovers$/
     */
    public function close_all_popovers() {
        behat_hooks::set_step_readonly(false);

        /** @var NodeElement[] $popover_close_buttons */
        $popover_close_buttons = $this->find_all('css', '.tui-popoverFrame__close');
        foreach ($popover_close_buttons as $close_button) {
            if ($close_button->isVisible()) {
                $close_button->click();
            }
        }
    }

    /**
     * @Given /^I navigate to manage perform activity content page$/
     * @Given /^I navigate to manage perform activity content page of "(?P<section_number>\d+)" activity section$/
     */
    public function i_navigate_to_manage_perform_activity_content_page(int $section_number = 1): void {
        behat_hooks::set_step_readonly(false);

        $behat_general = behat_context_helper::get('behat_general');
        $behat_general->i_click_on_in_the(
            "Content",
            "link",
            self::TUI_TAB_ELEMENT,
            "css_element"
        );

        $this->wait_for_pending_js();

        $section_node = $this->get_section_node($section_number);
        $element_node = $this->find_element_in_container($section_node, "Edit content elements", "button");
        $element_node->click();
    }

    /**
     * @When /^I save the activity schedule$/
     */
    public function i_save_the_activity_schedule(): void {
        behat_hooks::set_step_readonly(false);

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
            'long text' => self::LONG_TEXT_RESPONSE_LOCATOR,
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
            $actual_title = trim(str_replace(['(optional)', '*'], ['', ''], $found_question->getText()));
            if ($actual_title === $question_text) {
                return $question;
            }
        }

        throw new ExpectationException("Question not found with text {$question_text}", $this->getSession());
    }

    public function find_admin_question_from_text(string $question_text): NodeElement {
        /** @var NodeElement[] $questions */
        $questions = $this->find_all('css', self::QUESTION_DISPLAY_LOCATOR);

        foreach ($questions as $question) {
            $question_title = $question->find('css', self::EDIT_QUESTION_DISPLAY_TITLE_LOCATOR);

            $actual_title = trim(str_replace('*','', $question_title->getText()));

            if ($actual_title === $question_text) {
                return $question;
            }
        }

        throw new ExpectationException("Question not found with text {$question_text}", $this->getSession());
    }

    private function find_required_question_from_text(string $question_text, bool $is_required): NodeElement {
        /** @var NodeElement[] $questions */
        $questions = $this->find_all('css', self::PERFORM_ELEMENT_LOCATOR);

        foreach ($questions as $question) {
            $found_question = $question->find('css', self::PERFORM_ELEMENT_QUESTION_TEXT_LOCATOR);

            if ($found_question !== null && trim($found_question->getText()) === $question_text) {
                if ($is_required) {
                    $required_found = $found_question->getParent()->find('css', self::PERFORM_ELEMENT_QUESTION_REQUIRED_LOCATOR);
                    if ($required_found === null) {
                        $this->fail('Found question but it is not required.');
                    }
                } else {
                    $required_found = $found_question->getParent()->find('css', self::PERFORM_ELEMENT_QUESTION_OPTIONAL_LOCATOR);
                    if ($required_found === null) {
                        $this->fail('Found question but it is not optional.');
                    }
                }

                return $question;
            }
        }

        throw new ExpectationException("Required Question not found with text {$question_text}", $this->getSession());
    }

    /**
     * @Then /^I should see no perform activity participants$/
     */
    public function i_should_see_no_participants(): void {
        $this->ensure_element_does_not_exist(
            self::MANAGE_CONTENT_PARTICIPANT_NAME_LOCATOR,
            'css_element'
        );
    }

    /**
     * @Then /^I should see "([^"]*)" as the perform activity participants$/
     * @Then /^I should see "([^"]*)" as the perform activity (view-only|responding) participants$/
     * @param $expected_participant_list
     * @param string $group
     * @throws ExpectationException
     */
    public function i_should_see_as_the_participants(
        $expected_participant_list,
        string $group = 'responding'
    ): void {
        $group_container = $this->find_participant_group_container($group);

        /** @var NodeElement[] $rows */
        $rows = $group_container->findAll('css', self::MANAGE_CONTENT_PARTICIPANT_NAME_LOCATOR);

        $expected_participants = explode(',', $expected_participant_list);

        foreach ($expected_participants as $index => $expected_participant) {
            if (trim($rows[$index]->getText()) !== trim($expected_participant)) {
                $this->fail("{$expected_participant} was not found in the {$index} position");
            }
        }
    }

    /**
     * @Then /^the mod perform (responding|view-only) participants popover should match:$/
     * @param TableNode $table
     * @param string $group
     */
    public function the_mod_perform_participants_popover_should_match(TableNode $table, string $group): void {
        $group_container = $this->find_participant_group_container($group);

        foreach ($table->getHash() as $hash) {
            $input = $group_container->find('css', "input[name=\"{$hash['name']}\"]");

            if ($hash['checked'] && !$input->isChecked()) {
                $this->fail("{$hash['name']} did not have the correct checked value");
            }

            if (!$hash['enabled'] && $input->getAttribute('disabled') !== 'disabled') {
                $this->fail("{$hash['name']} did not have the correct enabled value");
            }
        }
    }

    /**
     * @When /^I select "([^"]*)" in the (responding|view-only) participants popover(| then click cancel)$/
     * @param string $participant_list
     * @param string $group
     * @param string $then_click_cancel
     */
    public function i_select_in_the_participants_popover(
        string $participant_list,
        string $group,
        string $then_click_cancel
    ): void {
        $relationships = explode(',', $participant_list);

        $group_container = $this->find_participant_group_container($group);

        foreach ($relationships as $relationship) {
            $relationship = trim($relationship);

            $input = $group_container->find('css', "input[name=\"{$relationship}\"]");
            $input->getParent()->find('css', 'label')->click();
        }

        if ($then_click_cancel) {
            // "Cancel".
            $group_container->find('css', behat_totara_tui::SECONDARY_BTN)->click();
        } else {
            // "Done".
            $group_container->find('css', behat_totara_tui::PRIMARY_BTN)->click();
        }
    }

    /**
     * @When /^I click the add (responding|view-only) participant button$/
     * @When /^I click the add (responding|view-only) participant button in "([^"]*)" activity section$/
     *
     * @param string $responding
     * @param int $section_number
     * @return void
     * @throws ExpectationException
     */
    public function i_click_the_add_participant_button(
        string $responding,
        int $section_number = 1
    ): void {
        behat_hooks::set_step_readonly(false);

        if ($responding === 'responding') {
            $selector = self::MANAGE_CONTENT_ADD_RESPONDING_PARTICIPANTS_BUTTON_LABEL;
        } else {
            $selector = self::MANAGE_CONTENT_ADD_VIEW_ONLY_PARTICIPANTS_BUTTON_LABEL;
        }

        $css_selector = sprintf($selector, $section_number);

        $this->find(
            'css',
            $css_selector
        )->click();
    }

    /**
     * @When /^I should see the add participant button is disabled$/
     * @When /^I should see the add participant button is disabled in "([^"]*)" activity section$/
     *
     * @param int $section_number
     * @return void
     */
    public function i_should_see_the_add_participant_button_is_disabled(int $section_number = 1): void {
        $css_selector = sprintf(self::MANAGE_CONTENT_ADD_RESPONDING_PARTICIPANTS_BUTTON_LABEL, $section_number);
        $this->execute('behat_general::the_element_should_be_disabled',
            [$css_selector, 'css_element']
        );
    }

    /**
     * @When /^I should see the subject instance was created "(?P<date>(?:[^"]|\\")*)" in the "(?P<element>(?:[^"]|\\")*)" "(?P<selector_type>(?:[^"]|\\")*)"$/
     *
     * @param string $date
     * @param string $element
     * @param string $selector_type
     * @return void
     */
    public function i_should_see_the_subject_instance_was_created(string $date, string $element, string $selector_type): void {
        $date_text = sprintf("Created %s", $date);
        $this->execute('behat_general::assert_element_contains_text',
            [$date_text, $element, $selector_type]
        );
    }

    /**
     * @When /^I should see the subject instance should be completed before "(?P<date>(?:[^"]|\\")*)" in the "(?P<element>(?:[^"]|\\")*)" "(?P<selector_type>(?:[^"]|\\")*)"$/
     *
     * @param string $date
     * @param string $element
     * @param string $selector_type
     * @return void
     */
    public function i_should_see_the_subject_instance_is_due(string $date, string $element, string $selector_type): void {
        $date_text = sprintf("Due date: %s", $date);
        $this->execute('behat_general::assert_element_contains_text',
            [$date_text, $element, $selector_type]
        );
    }

    /**
     * @When /^Subject instances for "(?P<track_description>(?:[^"]|\\")*)" track are due "(?P<due_date>(?:[^"]|\\")*)"$/
     *
     * @param string $track_description
     * @param string $due_date
     * @return void
     */
    public function subject_instances_for_track_are_due(string $track_description,string $due_date): void {
        $track = track::repository()
            ->where('description', $track_description)
            ->select('id')
            ->order_by('id')
            ->first_or_fail();

        $user_assignments = track_user_assignment::repository()
            ->where('track_id', $track->id)
            ->select('id')
            ->get();
        subject_instance::repository()
            ->where_in('track_user_assignment_id', $user_assignments->pluck('id'))
            ->update(
                [
                    'due_date' => $due_date
                ]
            );
    }

    /**
     * @When /^I should see the add participant button$/
     * @When /^I should see the add participant button in "([^"]*)" activity section$/
     *
     * @param int $section_number
     * @return void
     */
    public function i_should_see_the_add_participant_button(int $section_number = 1): void {
        $css_selector = sprintf(self::MANAGE_CONTENT_ADD_RESPONDING_PARTICIPANTS_BUTTON_LABEL, $section_number);
        $this->ensure_element_exists(
            $css_selector,
            'css_element'
        );
    }

    /**
     * @When /^I remove "([^"]*)" as a perform activity participant$/
     * @param string $participant_to_remove
     */
    public function i_remove_as_a_participant(string $participant_to_remove): void {
        behat_hooks::set_step_readonly(false);

        /** @var NodeElement[] $rows */
        $rows = $this->find_all('css', self::MANAGE_CONTENT_PARTICIPANT_NAME_LOCATOR);

        foreach ($rows as $participant_row) {
            if (trim($participant_row->getText()) === $participant_to_remove) {
                $participant_row->find('css', sprintf( self::TUI_TRASH_ICON_BUTTON, $participant_to_remove))->click();
                return;
            }
        }

        $this->fail("{$participant_to_remove} participant not found");
    }

    /**
     * @Given /^I should see "([^"]*)" in the perform activity response visibility description$/
     * @param string $expected_text
     * @throws ExpectationException
     */
    public function i_should_see_in_the_perform_activity_response_visibility_description(string $expected_text): void {
        if ($expected_text === '') {
            return;
        }

        $actual_text = $this->find('css', self::RESPONSE_VISIBILITY_DESCRIPTION_LOCATOR)->getText();

        if (strpos($actual_text, $expected_text) === false) {
            $this->fail("{$expected_text} was not found in the response visibility description: Actual text: {$actual_text}");
        }
    }

    /**
     * Display information about the time, the instance creation time and the due date, then pause.
     *
     * @Given /^(?:|I )pause to check the time for perform activity notification$/
     */
    public function i_pause_to_check_the_time_for_perform_activity_notification(): void {
        global $CFG, $DB;
        /** @var moodle_database $DB */
        \behat_hooks::set_step_readonly(true);
        // Pick the first record belonging to the current user.
        // Yes this is not ideal as the user could have more than one instances, but hey it is just convenience.
        // First, we need to extract the current user id from the profile link because $USER->id is not correctly set.
        $el = $this->find('css', '.logininfo a[title="View profile"]');
        if ($el && !empty($href = $el->getAttribute('href')) && preg_match('/id=(\d+)/', $href, $matches)) {
            $userid = $matches[1];
        } else {
            $userid = 0; // user id is unknown
        }
        $record = current($DB->get_records('perform_subject_instance', ['subject_user_id' => $userid], '', '*', 0, 1)) ?: new stdClass();
        $clock = \mod_perform\notification\factory::create_clock();
        $due = !empty($record->due_date) ? $record->due_date : 0;
        $creation = !empty($record->created_at) ? $record->created_at : 0;
        $time = $clock->get_time();
        // Windows don't support ANSI code by default, but with ANSICON.
        $isansicon = getenv('ANSICON');
        $ansi = !(($CFG->ostype === 'WINDOWS') && empty($isansicon));
        $out_a_time = function ($int, $clr, $tm, $info) use ($ansi) {
            $str = userdate($tm, '%b %d %Y %p %I:%M', 99, false, false);
            if ($ansi) {
                return "\033[{$int};{$clr}m{$str}\033[0m  ({$info})\n";
            } else {
                return "{$str}  ({$info})\n";
            }
        };
        // Display the due date, the adjusted time and the instance creation time respectively.
        $current_bias = get_config('mod_perform', 'notification_time_travel') ?: 0;
        $info_time = sprintf('%d day %d hour in the %s', (int)($current_bias / 86400), (int)($current_bias / 3600) % 24, $current_bias > 0 ? 'future' : 'past');
        $tz = ' in ' . \core_date::get_server_timezone();
        $text = $ansi ? "\033[s\n" : "\n";
        $text .= $out_a_time(1, 31, $due ?: -2682315804, $due ? ('due date' . $tz) : 'due date not set');
        $text .= $out_a_time(1, 32, $time, $current_bias ? ($info_time . $tz) : 'time not adjusted');
        $text .= $out_a_time(0, 33, $creation, $creation ? ('instance created' . $tz) : 'instance not created');
        $text .= $ansi ? "\033[4A\033[0m\033[u\033[4B" : '';
        fwrite(STDOUT, $text);
        $this->execute('behat_general::i_pause_scenario_executon');
    }

    /**
     * @Given /^I time travel to "midnight (past|future)" for perform activity notification$/
     * @param string $direction
     */
    public function i_time_travel_to_midnight_for_the_perform_activity_notification(string $direction): void {
        $time = \mod_perform\notification\factory::create_clock()->get_time();
        $midnight = \mod_perform\notification\conditions\after_midnight::get_last_midnight($time);
        if ($direction === 'future') {
            if ($time > $midnight) {
                $midnight += DAYSECS;
            }
        } else if ($direction === 'past') {
            // do nothing
        } else {
            $this->fail('direction must be future or past');
        }
        \mod_perform\notification\factory::create_clock_with_time_offset($midnight - $time);
    }

    /**
     * @Given /^I time travel to "(\d+) (day|days|hour|hours) (past|future)" for perform activity notification$/
     * @param string $time
     * @param string $unit
     * @param string $direction
     */
    public function i_time_travel_to_for_the_perform_activity_notification(string $time, string $unit, string $direction): void {
        $bias = (int)$time;
        if (!is_number($time) || $bias === 0) {
            $this->fail('time must be an integer');
        }
        if ($unit === 'days' || $unit === 'day') {
            $bias *= DAYSECS;
        } else if ($unit === 'hours' || $unit === 'hour') {
            $bias *= HOURSECS;
        } else {
            $this->fail('unit must be days or hours');
        }
        if ($direction === 'future') {
            // do nothing
        } else if ($direction === 'past') {
            $bias = -$bias;
        } else {
            $this->fail('direction must be future or past');
        }
        // create_clock_with_time_offset stores $bias in the database for further tasks.
        factory::create_clock_with_time_offset($bias);
        $this->execute('behat_tool_task::i_run_the_scheduled_task', [check_notification_trigger_task::class]);
    }

    /**
     * @When /^I navigate to the perform manage participation subject instances report for activity "([^"]*)"$/
     * @param string $activity_name
     */
    public function i_navigate_to_the_perform_manage_participation_subject_instances_report_for_activity(
        string $activity_name
    ): void {
        $activity = $this->get_activity_by_name($activity_name);

        $this->navigate_to_page(manage_participation::get_url(
            ['activity_id' => $activity->id]
        ));
    }

    /**
     * @Given /^I should see "([^"]*)" in the "([^"]*)" line of the perform activities instance info card$/
     * @param string $expected_value
     * @param string $label_text
     * @throws ExpectationException
     */
    public function i_should_see_in_the_line_of_the_perform_activities_instance_info_card(
        string $expected_value,
        string $label_text
    ): void {
        $labels = $this->find_all('css', self::INSTANCE_INFO_CARD_LABEL_LOCATOR);

        $value_index = null;
        foreach ($labels as $i => $label) {
            if (trim($label->getText()) === $label_text) {
                $value_index = $i;
                break;
            }
        }

        if ($value_index === null) {
            $this->fail("Label not found with the text: {$label_text}");
        }

        $value = $this->find_all('css', self::INSTANCE_INFO_CARD_VALUE_LOCATOR)[$value_index];

        $actual_text = trim($value->getText());

        if ($actual_text !== $expected_value) {
            $this->fail("'{$label_text}' value was not '{$expected_value}', found '{$actual_text}'");
        }
    }

    /**
     * @Given /^I should see today's date in the "([^"]*)" line of the perform activities instance info card$/
     */
    public function i_should_see_todays_date_in_the_line_of_the_perform_activities_instance_info_card(string $label_text) {
        $today_date_formatted = (new DateTime())->format('j F Y');

        $this->i_should_see_in_the_line_of_the_perform_activities_instance_info_card($today_date_formatted, $label_text);
    }

    /**
     * @Given /^I navigate to the mod perform response data report for "([^"]*)" activity$/
     * @param string $activity_name
     */
    public function i_navigate_to_the_mod_perform_response_data_report_for_activity(string $activity_name): void {
        $activity = $this->get_activity_by_name($activity_name);

        $url = \mod_perform\controllers\reporting\performance\activity::get_url(['activity_id' => $activity->id]);

        $this->navigate_to_page($url);
    }

    /**
     * @Given /^I navigate to the mod perform subject instance report for user "([^"]*)"$/
     * @param string $user_name
     */
    public function i_navigate_to_the_mod_perform_subject_instance_report_for_user(string $user_name): void {
        $user = $this->get_user_by_username($user_name);

        $url = \mod_perform\controllers\reporting\performance\user::get_url(['subject_user_id' => $user->id]);

        $this->navigate_to_page($url);
    }

    /**
     * Convenience method to fail from an ExpectationException.
     *
     * @param string $error error message.
     */
    private function fail(string $error): void {
        throw new ExpectationException($error, $this->getSession());
    }

    private function get_activity_by_name(string $activity_name): entity {
        $activity = activity::repository()
            ->where('name', $activity_name)
            ->one();

        if (!$activity) {
            throw new DriverException('Activity with name \''.$activity_name.'\' not found.');
        }

        return $activity;
    }

    private function find_participant_group_container(string $group): NodeElement
    {
        if ($group === 'responding') {
            $participant_group = 'Responding participants';
        } else {
            $participant_group = 'View-only participants';
        }

        $headings = $this->find_all('css', '.tui-performActivitySection__participant-heading');

        $headings_for_group = array_filter($headings, function (NodeElement $heading) use ($participant_group) {
            return trim($heading->getText()) === $participant_group;
        });

        /** @var NodeElement $heading */
        $heading = reset($headings_for_group);

        return $heading->getParent();
    }

    /**
     * @Given /^I navigate to the view only report view of performance activity "([^"]*)" where "([^"]*)" is the subject$/
     * @param string $activity_name
     * @param string $subject_user_name
     */
    public function i_navigate_to_the_read_only_report_view_of_performance_activity_for(
        string $activity_name,
        string $subject_user_name
    ): void {
        $target_subject_instance = $this->get_subject_instance_from_activity_and_subject($activity_name, $subject_user_name);

        $url = view_only_user_activity::get_url(['subject_instance_id' => $target_subject_instance->id]);

        $this->navigate_to_page($url);
    }

    /**
     * @When /^I navigate to the "(view|print)" user activity page for performance activity "([^"]*)" where "([^"]*)" is the subject and "([^"]*)" is the participant$/
     * @param string $page_type
     * @param string $activity_name
     * @param string $subject_user_name
     * @param string $participant_user_name
     * @throws Exception
     */
    public function i_navigate_to_the_user_activity_page_for_activity_subject_participant(
        string $page_type,
        string $activity_name,
        string $subject_user_name,
        string $participant_user_name
    ): void {
        $participant_instance = $this->get_participant_instance_from_activity_subject_participant(
            $activity_name, $subject_user_name, $participant_user_name
        );

        /** @var perform_controller $controller_class */
        if ($page_type === 'print') {
            $controller_class = print_user_activity::class;
            $params = ['participant_section_id' => $participant_instance->participant_sections->first()->id];
        } else {
            $controller_class = view_user_activity::class;
            $params = ['participant_instance_id' => $participant_instance->id];
        }

        $this->navigate_to_page($controller_class::get_url($params));
    }

    private function get_user_by_username(string $user_name): entity {
        return user::repository()
            ->where('username', $user_name)
            ->one(true);
    }

    private function get_subject_instance_from_activity_and_subject(string $activity_name, string $subject_user_name): entity {
        return subject_instance::repository()
            ->as('si')
            ->join([track_user_assignment::TABLE, 'tua'], 'tua.id', 'si.track_user_assignment_id')
            ->join([track::TABLE, 't'], 't.id', 'tua.track_id')
            ->join([activity::TABLE, 'a'], 'a.id', 't.activity_id')
            ->where('a.name', $activity_name)
            ->join([user::TABLE, 'u'], 'u.id', 'si.subject_user_id')
            ->where('u.username', $subject_user_name)
            ->one(true);
    }

    private function get_participant_instance_from_activity_subject_participant(
        string $activity_name,
        string $subject_user_name,
        string $participant_user_name
    ): participant_instance {
        return participant_instance::repository()
            ->as('pi')
            ->join([user::TABLE, 'pu'], 'pu.id', 'pi.participant_id')
            ->join([subject_instance::TABLE, 'si'], 'si.id', 'pi.subject_instance_id')
            ->join([track_user_assignment::TABLE, 'tua'], 'tua.id', 'si.track_user_assignment_id')
            ->join([track::TABLE, 't'], 't.id', 'tua.track_id')
            ->join([activity::TABLE, 'a'], 'a.id', 't.activity_id')
            ->join([user::TABLE, 'su'], 'su.id', 'si.subject_user_id')
            ->where('a.name', $activity_name)
            ->where('su.username', $subject_user_name)
            ->where('pu.username', $participant_user_name)
            ->order_by('id')
            ->first(true);
    }

}
