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
 * @author Samantha Jayasinghe <samantha.jayasinghe@totaralearning.com>
 * @package performelement_multi_choice_single
 */

use Behat\Mink\Exception\ExpectationException;

class behat_performelement_multi_choice_single extends behat_base {

    public const EDIT_ELEMENT_LOCATOR = '.tui-multiChoiceSingleAdminEdit';
    public const ADD_OPTION_LOCATOR   = '.tui-multiChoiceSingleAdminEdit__addOption';
    public const QUESTION_DISPLAY_OPTIONS_LOCATOR = '.tui-radio__label';

    /**
     * @When /^I click multi choice single question element$/
     * @deprecated since Totara 13.2
     */
    public function i_click_multi_choice_single_question_element(): void {
        debugging(
            '\behat_performelement_multi_choice_single::i_save_multi_choice_single_question_element_data() is deprecated and should no longer be used.'
            . ' Please use behat_mod_perform::i_add_a_custom_element() with "Multiple choice: single-select" as the parameter',
            DEBUG_DEVELOPER
        );

        behat_hooks::set_step_readonly(false);

        $behat_general = behat_context_helper::get('behat_general');

        $behat_general->i_click_on("Add element","button");
        $behat_general->i_click_on("Multiple choice: single-select", "button");
    }

    /**
     * @When /^I save multi choice single question element data$/
     * @deprecated since Totara 13.2
     */
    public function i_save_multi_choice_single_question_element_data(): void {
        debugging(
            '\behat_performelement_multi_choice_single::i_save_multi_choice_single_question_element_data() is deprecated and should no longer be used.'
            . ' Please use behat_mod_perform::i_save_the_custom_element_settings() with "save" as the parameter',
            DEBUG_DEVELOPER
        );

        behat_hooks::set_step_readonly(false);

        $done_button = $this->find('css', behat_mod_perform::ADMIN_FORM_DONE_BUTTON);
        $done_button->click();
    }

    /**
     * @When /^I click multi choice single question add new option$/
     */
    public function i_click_multi_choice_single_question_add_new_option(): void {
        behat_hooks::set_step_readonly(false);

        $edit_element = $this->find('css', self::EDIT_ELEMENT_LOCATOR);
        $add_button = $edit_element->find('css', self::ADD_OPTION_LOCATOR);
        if (!$add_button) {
            throw new ExpectationException('Multiple answers add new option not found!', $this->getSession());
        }
        $add_button->click();
    }

    /**
     * @Given /^I should see perform multi choice single question "([^"]*)" is saved with options "([^"]*)"$/
     * @param $question_text
     * @param $question_options
     *
     * @throws ExpectationException
     */
    public function i_should_see_multi_choice_single_question_is_saved_with_options(
        string $question_text,
        string $question_options
    ): void {
        /** @var behat_mod_perform $behat_mod_perform */
        $behat_mod_perform = behat_context_helper::get('behat_mod_perform');
        $question = $behat_mod_perform->find_admin_question_from_text($question_text);
        $options = $question->findAll('css', self::QUESTION_DISPLAY_OPTIONS_LOCATOR);
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
     * @Given /^I delete multi choice single question option$/
     *
     * @throws ExpectationException
     */
    public function i_delete_multi_choice_single_question_option(): void {
        behat_hooks::set_step_readonly(false);

        $delete_button = $this->find('css', '.tui-multiChoiceSingleAdminEdit .tui-repeater__delete:not([disabled])');
        $delete_button->click();
    }
}
