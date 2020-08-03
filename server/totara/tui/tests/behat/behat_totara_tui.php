<?php
/*
 * This file is part of Totara LMS
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
 * @package totara_core
 */
require_once(__DIR__ . '/../../../../lib/behat/behat_base.php');

use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\ElementNotFoundException;
use Behat\Mink\Exception\ExpectationException;
use WebDriver\Exception\NoSuchElement;

/**
 * This class contains helper step definitions for tui (vui) components.
 *
 */
class behat_totara_tui extends behat_base {

    private const DATA_TABLE_DEFAULT_LOCATOR = '.tui-dataTable';
    private const DATA_TABLE_DEFAULT_SELECTOR_TYPE = 'css_element';
    private const DATA_TABLE_DEFAULT_EMPTY_MESSAGE = 'No items to display';
    private const DATA_TABLE_HEADER_CELL_LOCATOR = '.tui-dataTableHeaderCell';
    private const DATA_TABLE_LOCATOR = '.tui-dataSelectTable';
    private const DATA_TABLE_ROW_LOCATOR = '.tui-dataTableRow';
    private const DATA_TABLE_CELL_LOCATOR = '.tui-dataTableCell';
    private const DATA_TABLE_CELL_CONTENT_LOCATOR = '.tui-dataTableCell__content';
    private const DATA_TABLE_EXPAND_CLOSE_LOCATOR = '.tui-dataTableExpandableRow__close';
    private const DATA_TABLE_SELECT_ALL_CHECKBOX_LOCATOR = '.tui-dataTableSelectVisibleRowsCell label';

    private const MULTI_SELECT_FILTER_LOCATOR = '.tui-multiSelectFilter';
    private const MULTI_SELECT_FILTER_LABEL_LOCATOR = '.tui-fieldset__legend';
    private const MULTI_SELECT_FILTER_OPTION_LOCATOR = '.tui-multiSelectFilter__item';

    private const BASKET_SELECT_COUNT_LOCATOR = '.tui-basket__selectedCount';
    private const BASKET_PRIMARY_ACTION_LOCATOR = '.tui-basket__actions .tui-formBtn--prim';

    private const HELP_ICON_LOCATOR = '.tui-formHelpIcon__icon';

    private const ACTION_CARD_LOCATOR = '.tui-actionCard';

    private const POPOVER_LOCATOR = '.tui-popoverFrame';
    private const POPOVER_CONTENT_LOCATOR = '.tui-popoverFrame__content';

    private const NOTIFICATION_BANNER_LOCATOR = '.tui-notificationBanner';
    private const NOTIFICATION_TOAST_LOCATOR = '.tui-notificationBanner--toast';

    private const MODAL_WRAPPER_SELECTOR = 'body > .tui-modal';
    private const MODAL_CONTENT_SELECTOR = '.tui-modalContent';

    private const COLLAPSIBLE_LOCATOR = '.tui-collapsible';
    private const COLLAPSIBLE_HEADER_TEXT_LOCATOR = '.tui-collapsible__header-text';
    private const COLLAPSIBLE_BUTTON_LOCATOR = '.tui-iconBtn.tui-collapsible__header_icons';

    private const TABS_ACTIVE_TAB_LOCATOR = '.tui-tabs__tab--active';

    private const CHECKBOX_LOCATOR = '.tui-checkbox__input';

    private const RADIO_LOCATOR = '.tui-radio__input';
    private const RADIO_LABEL_LOCATOR = '.tui-radio__label';

    private const TOGGLE_BUTTON_LOCATOR = '.tui-toggleSwitch__ui';
    private const TOGGLE_BUTTON_LABEL_LOCATOR = '.tui-toggleSwitch__btn';

    private const TUI_FORM_ROW_ACTION_LOCATOR = '.tui-formRow__action';
    private const TUI_FORM_LABEL_LOCATOR = '.tui-formLabel';
    private const TUI_FORM_ROW_CLASS = 'tui-formRow';

    private const TAGLIST_LOCATOR = '.tui-tagList';
    private const TAGLIST_DROPDOWN_BUTTON_LOCATOR = '.tui-tagList > button';
    private const TAGLIST_DROPDOWN_LIST_LOCATOR = '.tui-dropdown__content';

    /**
     * @param string $locator CSS locator
     * @param string $element_name Human understandable name of the element - e.g. 'modal', 'popover', 'picker' etc
     * @return NodeElement
     * @throws Exception
     */
    private function find_single_visible(string $locator, string $element_name): NodeElement {
        $visible = array_filter($this->find_all('css', $locator), static function (NodeElement $element) {
            return $element->isVisible();
        });
        if (count($visible) === 1) {
            return reset($visible);
        }
        throw new Exception((empty($visible) ? 'No' : 'Multiple') . " {$element_name} elements are visible");
    }

    /**
     * Find the element that is on top.
     *
     * @param string $locator CSS locator
     * @param string $element_name Human understandable name of the element - e.g. 'modal', 'popover', 'picker' etc
     * @return NodeElement
     * @throws Exception
     */
    private function find_top_visible(string $locator, string $element_name): NodeElement {
        $visible = array_filter($this->find_all('css', $locator), static function (NodeElement $element) {
            return $element->isVisible();
        });
        if (empty($visible)) {
            throw new Exception("No {$element_name} elements are visible");
        }
        return end($visible);
    }

    /**
     * @Given /^I should see the tui datatable is empty$/
     * @param string|null $table_locator
     * @param string|null $table_selector_type
     * @param string $expected_message
     */
    public function i_should_see_the_tui_datatable_is_empty(
        string $table_locator = null,
        string $table_selector_type = null,
        string $expected_message = self::DATA_TABLE_DEFAULT_EMPTY_MESSAGE
    ): void {
        // The empty table is actually just a single row by it's self, so we can't use find table here.
        if ($table_locator === null && $table_selector_type === null) {
            $table = $this->getSession()->getPage();
        } else {
            $table = $this->find($table_selector_type, $table_locator);
        }

        $table = $table->find('css', self::DATA_TABLE_LOCATOR);

        $actual_message = trim($table->getText());
        if ($expected_message !== $actual_message) {
            $exception_message = "Expected empty table to say {$expected_message}, instead said {$actual_message}";
            throw new ExpectationException($exception_message, $this->getSession());
        }
    }

    /**
     * @Then /^I should see "([^"]*)" rows in the tui datatable$/
     * @Then /^I should see "([^"]*)" rows in the tui datatable in the "(?P<element_container_string>(?:[^"]|\\")*)" "(?P<text_selector_string>[^"]*)"$/
     *
     * @param int $expected_table_row_count
     * @param string $table_locator
     * @param string $table_selector_type
     */
    public function i_should_see_number_of_rows_in_the_tui_datatable(
        int $expected_table_row_count,
        string $table_locator = self::DATA_TABLE_DEFAULT_LOCATOR,
        string $table_selector_type =
        self::DATA_TABLE_DEFAULT_SELECTOR_TYPE
    ): void {
        $table = $this->find_data_table($table_selector_type, $table_locator);

        $this->expect_data_table_row_count($table, $expected_table_row_count);
    }

    /**
     * For asserting the exact contents of a tui datatable, or exact column contents starting from the left.
     * All Rows are checked, but column checks can be incomplete.
     * @Then /^I should see the tui datatable contains:$/
     * @Then /^I should see the tui datatable in the "(?P<element_container_string>(?:[^"]|\\")*)" "(?P<text_selector_string>[^"]*)" contains:$/
     *
     * @param TableNode $expected_table_content
     * @param string|null $table_locator
     * @param string|null $table_selector_type
     * @throws coding_exception
     */
    public function i_should_see_the_tui_datatable_contain(
        TableNode $expected_table_content,
        string $table_locator = self::DATA_TABLE_DEFAULT_LOCATOR,
        string $table_selector_type =
        self::DATA_TABLE_DEFAULT_SELECTOR_TYPE
    ): void {
        $table_hash = $expected_table_content->getHash();

        $expected_table_row_count = count($table_hash);

        $table = $this->find_data_table($table_selector_type, $table_locator);

        $this->expect_data_table_row_count($table, $expected_table_row_count);

        foreach ($table_hash as $row_index => $row) {
            foreach ($row as $column_heading => $expected_column_text) {
                $this->i_should_see_under_on_row_of_datatable(
                    $expected_column_text, $column_heading, $row_index + 1, $table_locator, $table_selector_type
                );
            }
        }
    }

    /**
     * @When /^I open the dropdown menu in the tui datatable row with "([^"]*)" "([^"]*)"$/
     * @param string $cell_text
     * @param string $column_heading_text
     * @param string|null $table_locator
     * @param string $table_selector_type
     */
    public function i_open_the_dropdown_in_the_tui_datatable_row(
        string $cell_text,
        string $column_heading_text,
        string $table_locator = self::DATA_TABLE_DEFAULT_LOCATOR,
        string $table_selector_type = self::DATA_TABLE_DEFAULT_SELECTOR_TYPE
    ): void {
        behat_hooks::set_step_readonly(false);

        $this->close_visible_dropdown();

        $cell = $this->find_data_table_cell_by_text($column_heading_text, $cell_text, $table_locator, $table_selector_type);

        $cell->find('css', '.tui-dropdown')->click();
    }

    /**
     * @When /^I click on "([^"]*)" "([^"]*)" in the tui datatable row with "([^"]*)" "([^"]*)"$/
     * @param string $element
     * @param string $selector_type
     * @param string $cell_text
     * @param string $column_heading_text
     * @param string|null $table_locator
     * @param string $table_selector_type
     */
    public function i_click_on_in_the_tui_datatable_row(
        string $element,
        string $selector_type,
        string $cell_text,
        string $column_heading_text,
        string $table_locator = self::DATA_TABLE_DEFAULT_LOCATOR,
        string $table_selector_type = self::DATA_TABLE_DEFAULT_SELECTOR_TYPE
    ): void {
        behat_hooks::set_step_readonly(false);

        $cell = $this->find_data_table_cell_by_text($column_heading_text, $cell_text, $table_locator, $table_selector_type);

        list($selector, $locator) = $this->transform_selector($selector_type, $element);
        $node = $cell->find($selector, $locator);
        if ($node !== null) {
            $node->click();
        }
    }

    /**
     * If there is an opened dropdown menu, close it by pressing escape.
     *
     * @When /^I close any visible tui dropdowns$/
     */
    public function close_visible_dropdown(): void {
        behat_hooks::set_step_readonly(false);

        $open_menu = $this->find_visible_dropdown(false);

        if ($open_menu) {
            $open_menu->click();
        }
    }

    /**
     * Returns the currently visible dropdown node.
     *
     * @param bool $fail_on_not_found
     * @return NodeElement|null
     * @throws ExpectationException
     */
    private function find_visible_dropdown($fail_on_not_found = true): ?NodeElement {
        try {
            $menus = $this->find_all('css', '.tui-dropdown--open');
        } catch (ElementNotFoundException $exception) {
            $menus = [];
        }
        $found_menus = [];
        /** @var NodeElement $menu */
        foreach ($menus as $menu) {
            if ($menu->isVisible()) {
                $found_menus[] = $menu;
            }
        }
        if (count($found_menus) === 1) {
            return $found_menus[0];
        } else if (count($found_menus) > 1) {
            throw new ExpectationException("More than one open dropdown found.", $this->getSession());
        } else if ($fail_on_not_found) {
            throw new ExpectationException("Could not find any open dropdown.", $this->getSession());
        }

        return null;
    }

    /**
     * @When /^I click on "([^"]*)" in the tui dropdown for "([^"]*)" "([^"]*)" tui datatable row$/
     * @param string $drop_down_option
     * @param int $row_number
     * @param string|null $table_locator
     * @param string $table_selector_type
     */
    public function i_click_on_in_the_tui_dropdown_for_the_tui_datatable(
        string $drop_down_option,
        int $row_number,
        string $table_locator = self::DATA_TABLE_DEFAULT_LOCATOR,
        string $table_selector_type = self::DATA_TABLE_DEFAULT_SELECTOR_TYPE
    ): void {
        behat_hooks::set_step_readonly(false);

        $cell = $this->find_data_table_cell($drop_down_option, $row_number, $table_locator, $table_selector_type);

        $cell->clickLink($cell->getText());
    }

    /**
     * @When /^I click on "([^"]*)" option in the dropdown menu$/
     * @param string $option_text
     */
    public function i_click_on_dropdown_option(string $option_text): void {
        behat_hooks::set_step_readonly(false);

        $this->get_dropdown_menu_option($option_text)->click();
    }

    /**
     * @param string $option_text
     * @return NodeElement|null
     */
    private function get_dropdown_menu_option(string $option_text): ?NodeElement {
        $menu = $this->find_visible_dropdown();
        $menu_options = $menu->findAll('css', '.tui-dropdownItem');
        foreach ($menu_options as $menu_option) {
            if (strtolower($menu_option->getText()) === strtolower($option_text)) {
                return $menu_option;
            }
        }
        return null;
    }

    /**
     * @Then /^I (should|should not) see "([^"]*)" option (|disabled )in the dropdown menu$/
     * @param string $should_see_or_not
     * @param string $option_text
     * @param string $disabled
     * @throws ExpectationException
     */
    public function i_should_see_dropdown_option(
        string $should_see_or_not,
        string $option_text,
        string $disabled
    ): void {
        $should_see = ($should_see_or_not === 'should');
        $should_be_disabled = !empty($disabled);
        if (!$should_see && $should_be_disabled) {
            throw new coding_exception("Cannot check for invisible and disabled at the same time.");
        }

        $menu_option = $this->get_dropdown_menu_option($option_text);
        $found = !is_null($menu_option);

        if (!$found && $should_see) {
            throw new ExpectationException("Option '{$option_text}' could not be found.", $this->getSession());
        } else if ($found && !$should_see) {
            throw new ExpectationException("Found option '{$option_text}' when it should not be there.", $this->getSession());
        } else if (!$found && !$should_see) {
            return;
        }

        $is_disabled = !$this->is_dropdown_menu_option_enabled($menu_option);
        if ($should_be_disabled && !$is_disabled) {
            throw new ExpectationException("Option '{$option_text}' is enabled when it should not be.", $this->getSession());
        } else if (!$should_be_disabled && $is_disabled) {
            throw new ExpectationException(
                "Option '{$option_text}' was found but is disabled. If this is expected, add 'disabled' to the step.",
                $this->getSession()
            );
        }
    }

    /**
     * @param NodeElement $menu_option
     * @return bool
     */
    private function is_dropdown_menu_option_enabled(NodeElement $menu_option):bool {
        return is_null($menu_option->find('css', '.tui-dropdownItem--disabled'));
    }

    /**
     * @Then /^I should see "([^"]*)" under "([^"]*)" on row "([^"]*)" of the tui datatable$/
     * @Then /^I should see "([^"]*)" under "([^"]*)" on row "([^"]*)" of the tui datatable in the "([^"]*)" "([^"]*)"$/
     * @Then /^I should see "([^"]*)" under "([^"]*)" on row "([^"]*)" of the tui datatable in the "([^"]*)" "([^"]*)" in the "([^"]*)" tui collapsible$/
     * @param $expected_text string The expected text in the cell identified by the column header and row
     * @param $column_heading_text string The text of the column header used to identify the cell
     * @param $row_number int The row number to identify the cell, the first row is 1
     * @param string $table_locator '.my-table' etc
     * @param string|null $table_selector_type css, xpath etc
     * @param string|null $collapsible_label_text
     */
    public function i_should_see_under_on_row_of_datatable(string $expected_text, string $column_heading_text,
        int $row_number, string $table_locator = self::DATA_TABLE_DEFAULT_LOCATOR,
        string $table_selector_type = self::DATA_TABLE_DEFAULT_SELECTOR_TYPE,
        string $collapsible_label_text = null
    ): void {
        behat_hooks::set_step_readonly(true);

        /** @var NodeElement $top */
        $root = false;
        if ($collapsible_label_text !== null) {
            $root = $this->find_collapsible($collapsible_label_text);
        }

        $cell = $this->find_data_table_cell($column_heading_text, $row_number, $table_locator, $table_selector_type, $root);

        $cell_text = $cell->getText();

        if ($cell_text !== $expected_text) {
            $exception_message = "Expected cell to contain text \"{$expected_text}\" instead found \"{$cell_text}\"";
            throw new ExpectationException($exception_message, $this->getSession());
        }
    }

    /**
     * @When /^I toggle expanding row "([^"]*)" of the tui datatable$/
     * @When /^I toggle expanding row "([^"]*)" of the tui datatable in the "([^"]*)" "([^"]*)"$/
     * @When /^I toggle expanding row "([^"]*)" of the tui datatable in the "([^"]*)" "([^"]*)" in the "([^"]*)" tui collapsible$/
     * @param int $row_number The row number to identify the cell, the first row is 1
     * @param string $table_locator '.my-table' etc
     * @param string|null $table_selector_type css, xpath etc
     * @param string|null $collapsible_label_text
     */
    public function i_toggle_expanding_row_of_the_tui_datatable(
        int $row_number,
        string $table_locator = self::DATA_TABLE_DEFAULT_LOCATOR,
        string $table_selector_type = self::DATA_TABLE_DEFAULT_SELECTOR_TYPE,
        string $collapsible_label_text = null
    ): void {
        behat_hooks::set_step_readonly(false);

        /** @var NodeElement $top */
        $top = false;
        if ($collapsible_label_text !== null) {
            $top = $this->find_collapsible($collapsible_label_text);
        }

        $table = $this->find_data_table($table_selector_type, $table_locator, $top);
        $row = $this->find_data_table_row($row_number, $table);

        $expand_button = $row->find('css', '.tui-dataTableExpandCell > button');
        if (!$expand_button) {
            throw new Exception("Row $row_number of the tui datatable can not be expanded");
        }
        $expand_button->click();
    }

    /**
     * @Then /^I should see "([^"]*)" under the expanded row of the tui datatable$/
     * @Then /^I should see "([^"]*)" under the expanded row of the tui datatable in the "([^"]*)" "([^"]*)"$/
     * @Then /^I should see "([^"]*)" under the expanded row of the tui datatable in the "([^"]*)" "([^"]*)" in the "([^"]*)" tui collapsible$/
     * @param $expected_text string The expected text in the cell identified by the column header and row
     * @param string $table_locator '.my-table' etc
     * @param string|null $table_selector_type css, xpath etc
     * @param string|null $collapsible_label_text
     * @throws ExpectationException
     */
    public function i_should_see_under_the_expanded_row_of_datatable(
        string $expected_text,
        string $table_locator = self::DATA_TABLE_DEFAULT_LOCATOR,
        string $table_selector_type = self::DATA_TABLE_DEFAULT_SELECTOR_TYPE,
        string $collapsible_label_text = null
    ): void {
        behat_hooks::set_step_readonly(true);

        /** @var NodeElement $top */
        $root = false;
        if ($collapsible_label_text !== null) {
            $root = $this->find_collapsible($collapsible_label_text);
        }

        $table = $this->find_data_table($table_selector_type, $table_locator, $root);

        $row = $table->find('css', '.tui-dataTableExpandableRow');
        if ($row === null || !$row->isVisible()) {
            throw new ExpectationException('Expandable row is not visible in the tui datatable', $this->getSession());
        }

        $row_text = $row->getText();
        if (strpos($row_text, $expected_text) === false) {
            throw new ExpectationException("\"{$expected_text}\" text was not found in the tui datatable", $this->getSession());
        }
    }

    /**
     * @Then /^I should not see "([^"]*)" under the expanded row of the tui datatable$/
     * @Then /^I should not see "([^"]*)" under the expanded row of the tui datatable in the "([^"]*)" "([^"]*)"$/
     * @param $expected_text string The expected text in the cell identified by the column header and row
     * @param string $table_locator '.my-table' etc
     * @param string|null $table_selector_type css, xpath etc
     * @throws ExpectationException
     */
    public function i_should_not_see_under_the_expanded_row_of_datatable(
        string $expected_text,
        string $table_locator = self::DATA_TABLE_DEFAULT_LOCATOR,
        string $table_selector_type = self::DATA_TABLE_DEFAULT_SELECTOR_TYPE
    ): void {
        $table = $this->find_data_table($table_selector_type, $table_locator);

        $row = $table->find('css', '.tui-dataTableExpandableRow');
        if ($row === null || !$row->isVisible()) {
            throw new ExpectationException('Expandable row is not visible in the tui datatable', $this->getSession());
        }

        $row_text = $row->getText();
        if (strpos($row_text, $expected_text) !== false) {
            throw new ExpectationException(
                "\"{$expected_text}\" text was found in the tui datatable but shouldn't be there",
                $this->getSession()
            );
        }
    }

    /**
     * @Then /^I click "([^"]*)" in the "([^"]*)" tui multi select filter$/
     * @param $filter_option_text
     * @param $filter_label_text
     */
    public function i_click_in_the_tui_multi_select_filter($filter_option_text, $filter_label_text): void {
        behat_hooks::set_step_readonly(false);

        $filter = $this->find_multi_select_filter($filter_label_text);
        $filter_option = $this->find_multi_select_filter_option($filter, $filter_option_text);

        $filter_option->click();
    }

    /**
     * @Given /^I should see the "([^"]*)" "([^"]*)" tui multi select filter is active$/
     * @param $filter_option_text
     * @param $filter_label_text
     */
    public function i_should_see_the_tui_multi_select_filter_is_active($filter_option_text, $filter_label_text): void {
        $filter = $this->find_multi_select_filter($filter_label_text);
        $filter_option_checkbox = $this->find_multi_select_filter_checkbox($filter, $filter_option_text);

        if (!$filter_option_checkbox->isChecked()) {
            throw new ExpectationException("Expected the {$filter_option_text} filter option to be active", $this->getSession());
        }
    }

    /**
     * @Given /^I should see the "([^"]*)" "([^"]*)" tui multi select filter is not active$/
     * @param $filter_option_text
     * @param $filter_label_text
     * @throws ExpectationException
     */
    public function i_should_see_the_tui_multi_select_filter_is_not_active($filter_option_text, $filter_label_text): void {
        $filter = $this->find_multi_select_filter($filter_label_text);
        $filter_option_checkbox = $this->find_multi_select_filter_checkbox($filter, $filter_option_text);

        if ($filter_option_checkbox->isChecked()) {
            $exception_message = "Expected the {$filter_option_text} filter option to not be active";
            throw new ExpectationException($exception_message, $this->getSession());
        }
    }

    /**
     * @When /^I close the tui datatable expandable content$/
     */
    public function i_close_the_data_table_expandable_content(): void {
        behat_hooks::set_step_readonly(false);

        $this->find('css', self::DATA_TABLE_EXPAND_CLOSE_LOCATOR)->click();
    }

    /**

     * @Then /^I click the select all checkbox in the tui datatable$/
     * @readonly
     */
    public function i_click_on_the_select_all_checkbox_in_the_datatable(): void {
        behat_hooks::set_step_readonly(false);

        $this->find('css', self::DATA_TABLE_SELECT_ALL_CHECKBOX_LOCATOR)->click();
    }

    /**
     * @Then /^I click on the "([^"]*)" tui checkbox$/
     * @Then /^I click on the "([^"]*)" tui checkbox in the "([^"]*)" "([^"]*)"$/
     * @param string $name
     * @param string $parent_locator
     * @param string $parent_selector
     */
    public function i_click_the_tui_checkbox(string $name, string $parent_locator = null, string $parent_selector = null): void {
        behat_hooks::set_step_readonly(false);
        $locator = self::CHECKBOX_LOCATOR . "[name='{$name}']";

        $parent_element = $this;
        if (isset($parent_locator, $parent_selector)) {
            [$pre_selector, $pre_locator] = $this->transform_selector($parent_selector, $parent_locator);
            $parent_element = $this->find($pre_selector, $pre_locator);
            if ($parent_element === null || !$parent_element->isVisible()) {
                throw new ExpectationException("Couldn't find the specified element or it wasn't visible", $this->getSession());
            }
        }

        $checkbox_label = $parent_element->find('css', $locator);

        if ($checkbox_label === null) {
            $this->fail("No tui check box found with name {$name}");
        }

        $checkbox_input = $checkbox_label->getParent()->find('css', 'input');
        $this->click_hidden_element($checkbox_input);
    }

    /**
     * @When /^I click on the "([^"]*)" tui radio$/
     * @When /^I click on the "([^"]*)" tui radio in the "([^"]*)" tui radio group$/
     * @param string $radio Can be the value or label
     * @param string|null $group
     */
    public function i_click_the_tui_radio_in_the(string $radio, ?string $group = null): void {
        behat_hooks::set_step_readonly(false);

        $xpath = '//div[contains(@class, "tui-radio") and ' .
            '(label[contains(@class, "tui-radio__label") and contains(., "' . $radio . '")] or ' .
            'input[contains(@class, "tui-radio__input") and @value="' . $radio . '"' .
            ($group === null ? '' : ' and @name="' . $group . '"') .
            '])]/input[contains(@class, "tui-radio__input")]';

        /** @var NodeElement $radio_input */
        $radio_input = $this->find('xpath', $xpath);
        if ($radio_input === null) {
            $this->fail("Could not locate radio button with the label or value '{$radio}'" .
                $group === null ? '' : " in group '{$group}'"
            );
        }

        $this->click_hidden_element($radio_input);
    }

    /**
     * Click a hidden element.
     *
     * In some cases with custom tui form elements the actual <input> HTML element is hidden, so behat can't interact with it.
     * This function is a workaround for this, instead we manually click the element via running some JS in the browser.
     * This isn't ideal, but it works. Replace if there is a better solution.
     *
     * @param NodeElement $input_element
     */
    private function click_hidden_element(NodeElement $input_element): void {
        $id = $input_element->getAttribute('id');
        $click_script = "document.querySelector(\"#{$id}\").click();";
        $this->getSession()->getDriver()->executeScript($click_script);
        $this->wait_for_pending_js();
    }

    /**
     * @Given /^I should see "([^"]*)" items in the tui basket$/
     * @param int $expected_basket_item_count
     */
    public function i_should_see_items_in_the_tui_basket(int $expected_basket_item_count): void {
        $this->execute('behat_general::assert_element_contains_text',
            [$expected_basket_item_count, self::BASKET_SELECT_COUNT_LOCATOR, 'css_element']
        );

        if ($expected_basket_item_count > 0) {
            $this->execute('behat_general::the_element_should_be_enabled',
                [self::BASKET_PRIMARY_ACTION_LOCATOR, 'css_element']
            );
        } else {
            $this->execute('behat_general::the_element_should_be_disabled',
                [self::BASKET_PRIMARY_ACTION_LOCATOR, 'css_element']
            );
        }
    }

    /**
     * @Given /^the tui basket should be empty$/
     */
    public function the_tui_basket_should_be_empty(): void {
        $this->i_should_see_items_in_the_tui_basket(0);
    }

    /**
     * @Then /^I (should|should not) see "([^"]*)" in the tui modal$/
     * @param string $should_see_or_not
     * @param string $expected_text
     * @throws ExpectationException
     */
    public function i_should_see_in_the_tui_modal(string $should_see_or_not, string $expected_text): void {
        $should = $should_see_or_not === 'should';
        $modal_text = $this
            ->find_top_visible(self::MODAL_WRAPPER_SELECTOR, 'modal')
            ->find('css', self::MODAL_CONTENT_SELECTOR)
            ->getText();

        $text_is_visible = strpos($modal_text, $expected_text) !== false;
        if ($should && !$text_is_visible) {
            throw new ExpectationException("\"$expected_text\" not found in the tui modal", $this->getSession());
        }
        if (!$should && $text_is_visible) {
            throw new ExpectationException("\"$expected_text\" was found in the tui modal", $this->getSession());
        }
    }

    /**
     * @When /^I confirm the tui confirmation modal$/
     */
    public function i_confirm_the_tui_confirmation_modal(): void {
        \behat_hooks::set_step_readonly(false);

        $confirm_button = $this
            ->find_top_visible(self::MODAL_WRAPPER_SELECTOR, 'modal')
            ->find('css', self::MODAL_CONTENT_SELECTOR)
            ->find('css', '.tui-formBtn--prim:first-child');
        if ($confirm_button === null || !$confirm_button->isVisible()) {
            throw new Exception('The tui modal is not a confirmation modal', $this->getSession());
        }
        $confirm_button->click();
    }

    /**
     * @When /^I close the tui modal$/
     */
    public function i_close_the_tui_modal(): void {
        \behat_hooks::set_step_readonly(false);

        $modal = $this->find_top_visible(self::MODAL_WRAPPER_SELECTOR, 'modal');

        // 'X' close button in top right of the modal.
        $x_button = $modal->find('css', '.tui-modalContent__header-close')
            ?? $modal->find('css', '.tui-modal__outsideClose');
        if ($x_button !== null && $x_button->isVisible()) {
            $x_button->click();
            return;
        }

        /** @var NodeElement[] $buttons */
        $buttons = $modal->findAll('css', '.tui-modalContent__footer-buttons button');
        foreach ($buttons as $button) {
            // Confirmation modals have a Cancel button.
            if ($button->getText() === 'Cancel') {
                $button->click();
                return;
            }
        }

        throw new ExpectationException('Modal has no way to be closed.', $this->getSession());
    }

    /**
     * @When /^I click on the tui form help icon in the "([^"]*)" "([^"]*)"$/
     * @param string $element_locator
     * @param string $element_selector
     * @throws ExpectationException
     */
    public function i_click_on_the_help_icon(string $element_locator, string $element_selector): void {
        \behat_hooks::set_step_readonly(false);

        [$pre_selector, $pre_locator] = $this->transform_selector($element_selector, $element_locator);

        $element = $this->find($pre_selector, $pre_locator);
        if ($element === null || !$element->isVisible()) {
            throw new ExpectationException("Couldn't find the specified element or it wasn't visible", $this->getSession());
        }

        $button = $element->find('css', self::HELP_ICON_LOCATOR);
        if ($button === null || !$button->isVisible()) {
            throw new ExpectationException("Couldn't find a help button inside the specified element", $this->getSession());
        }

        $button->click();
    }

    /**
     * @Then /^I (should|should not) see "([^"]*)" in the tui action card/
     * @param string $should_see_or_not
     * @param string $expected_text
     * @throws ExpectationException
     */
    public function i_should_see_in_the_tui_action_card(string $should_see_or_not, string $expected_text): void {
        $should = $should_see_or_not === 'should';
        $popover_text = $this
            ->find_single_visible(self::ACTION_CARD_LOCATOR, 'action card')
            ->getText();

        $text_is_visible = strpos($popover_text, $expected_text) !== false;
        if ($should && !$text_is_visible) {
            throw new ExpectationException("\"$expected_text\" not found in the tui action card", $this->getSession());
        }
        if (!$should && $text_is_visible) {
            throw new ExpectationException("\"$expected_text\" was found in the tui action card", $this->getSession());
        }
    }

    /**
     * @Then /^I (should|should not) see "([^"]*)" in the tui popover$/
     * @param string $should_see_or_not
     * @param string $expected_text
     * @throws ExpectationException
     */
    public function i_should_see_in_the_tui_popover(string $should_see_or_not, string $expected_text): void {
        $should = $should_see_or_not === 'should';
        $popover_text = $this
            ->find_single_visible(self::POPOVER_CONTENT_LOCATOR, 'popover')
            ->getText();

        $text_is_visible = strpos($popover_text, $expected_text) !== false;
        if ($should && !$text_is_visible) {
            throw new ExpectationException("\"$expected_text\" not found in the tui popover", $this->getSession());
        }
        if (!$should && $text_is_visible) {
            throw new ExpectationException("\"$expected_text\" was found in the tui popover", $this->getSession());
        }
    }

    /**
     * @When /^I close the tui popover$/
     */
    public function i_close_the_tui_popover(): void {
        \behat_hooks::set_step_readonly(false);

        $this
            ->find_single_visible(self::POPOVER_LOCATOR, 'popover')
            ->find('css', '.tui-popoverFrame__close')
            ->click();
    }

    /**
     * Clicks on a toggle button with the specified aria-label or text prop.
     *
     * @When /^I click on the "([^"]*)" tui toggle button$/
     *
     * @param string $button_label
     */
    public function i_click_on_the_tui_toggle_button(string $button_label): void {
        \behat_hooks::set_step_readonly(false);

        /** @var NodeElement[] $toggles */
        $toggles = $this->find_all('css', self::TOGGLE_BUTTON_LABEL_LOCATOR);

        $specified_toggle = null;
        foreach ($toggles as $toggle) {
            if ($toggle->getText() === $button_label) {
                $specified_toggle = $toggle;
            }
        }
        if ($specified_toggle === null) {
            $specified_toggle = $this->find_single_visible(
                self::TOGGLE_BUTTON_LABEL_LOCATOR . "[aria-label='{$button_label}']",
                'toggle'
            );
        }

        $specified_toggle
            ->getParent()
            ->find('css', self::TOGGLE_BUTTON_LOCATOR)
            ->click();
    }

    /**
     * @Then /^I should see "([^"]*)" in the tui "([^"]*)" notification banner$/
     * @param string $expected_text
     * @param string $banner_type
     * @throws Exception
     */
    public function i_should_see_in_the_tui_notification_banner(string $expected_text, string $banner_type): void {
        $locator = self::NOTIFICATION_BANNER_LOCATOR . '--' . $banner_type;

        $this->execute('behat_general::assert_element_contains_text',
            [$expected_text, $locator, 'css_element']
        );
    }

    /**
     * @Then /^I should see "([^"]*)" in the tui "([^"]*)" notification toast$/
     * @param string $expected_text
     * @param string $toast_type
     * @throws Exception
     */
    public function i_should_see_in_the_tui_notification_toast(string $expected_text, string $toast_type): void {
        $locator = self::NOTIFICATION_BANNER_LOCATOR . '--' . $toast_type;
        $locator .= self::NOTIFICATION_TOAST_LOCATOR;

        $this->execute('behat_general::assert_element_contains_text',
            [$expected_text, $locator, 'css_element']
        );
    }

    /**
     * This closes all tui notification toasts if it finds any
     *
     * @When /^I close the tui notification toast$/
     */
    public function i_close_the_tui_notification_toast() {
        \behat_hooks::set_step_readonly(false);

        $locator = self::NOTIFICATION_BANNER_LOCATOR.self::NOTIFICATION_TOAST_LOCATOR;

        try {
            $toasts = $this->find_all('css', $locator);
        } catch (ElementNotFoundException $e) {
            // It can happen that the toast already vanished
            // in this case we ignore the error
            return;
        } catch (NoSuchElement $e) {
            // Sometimes you get different errors
            return;
        }
        foreach ($toasts as $toast) {
            try {
                $button = $toast->find('css', '.tui-notificationBanner__dismiss button', true);
                if ($button && $button->isVisible()) {
                    $button->click();
                    $this->wait_for_pending_js();
                }
            } catch (ElementNotFoundException $e) {
                // It can happen that the toast already vanished
                // in this case we ignore the error
                continue;
            } catch (NoSuchElement $e) {
                // Sometimes you get different errors
                return;
            }
        }
    }

    /**
     * @When /^I toggle the "([^"]*)" tui collapsible$/
     * @param string $collapsible_label_text
     * @throws Exception
     */
    public function i_toggle_the_tui_collapsible(string $collapsible_label_text) {
        \behat_hooks::set_step_readonly(false);

        /** @var NodeElement $collapsible */
        $collapsible = $this->find_collapsible($collapsible_label_text);
        if (!$collapsible || !$collapsible->isVisible()) {
            throw new ExpectationException("Could not find the '$collapsible_label_text' collapsible", $this->getSession());
        }

        $collapsible->find('css', self::COLLAPSIBLE_BUTTON_LOCATOR)->click();
    }

    /**
     * @When /^I ensure the "([^"]*)" tui collapsible is expanded$/
     * @param string $collapsible_label_text
     * @throws Exception
     */
    public function i_ensure_the_tui_collapsible_is_expanded(string $collapsible_label_text) {
        \behat_hooks::set_step_readonly(false);

        /** @var NodeElement $collapsible */
        $collapsible = $this->find_collapsible($collapsible_label_text);
        if (!$collapsible || !$collapsible->isVisible()) {
            throw new ExpectationException("Could not find the '$collapsible_label_text' collapsible", $this->getSession());
        }

        /** @var NodeElement $button */
        $button = $collapsible->find('css', self::COLLAPSIBLE_BUTTON_LOCATOR);
        $expanded = $button->getAttribute('aria-expanded');
        if ($expanded === 'false') {
            $button->click();
        }
    }

    /**
     * @Given /^the "([^"]*)" tui tab should be active$/
     * @param string $expected_tab_heading
     * @throws ExpectationException
     */
    public function the_tui_tab_should_be_active(string $expected_tab_heading): void {
        $tab = $this->find('css', self::TABS_ACTIVE_TAB_LOCATOR);

        if ($tab === null) {
            throw new ExpectationException(
                'No active tab not found',
                $this->getSession()
            );
        }

        $tab_heading_text = trim($tab->getText());

        if ($tab_heading_text !== $expected_tab_heading) {
            throw new ExpectationException(
                "Active tab did not have the correct heading, it had: {$tab_heading_text}",
                $this->getSession()
            );
        }
    }

    /**
     * @Given /^I set the "([^"]*)" tui date selector to "([^"]*)"$/
     * @param string $field_name
     * @param string $date_value day month year; 26 June 2020
     */
    public function i_set_the_tui_date_selector_to(string $field_name, string $date_value): void {
        $date_selector = $this->find_date_selector_by_name($field_name);

        [$day_to_set, $month_to_set, $year_to_set] = explode(' ', $date_value);

        $selections = [
            'day' => $day_to_set,
            'month' => $month_to_set,
            'year' => $year_to_set,
        ];

        foreach ($selections as $key => $selection) {
            /** @var NodeElement $select_node */
            $select_node = $date_selector->find('css', ".tui-dateSelector__date-$key select");
            $select_node->selectOption($selection);
        }
    }

    /**
     * @Then /^the "([^"]*)" tui date selector should be set to "([^"]*)"$/
     * @param string $field_name
     * @param string $date_value
     */
    public function the_tui_date_selector_should_be_set_to(string $field_name, string $date_value): void {
        $date_selector = $this->find_date_selector_by_name($field_name);

        [$day_expected, $month_expected, $year_expected] = explode(' ', $date_value);

        $selections = [
            'day' => $day_expected,
            'month' => $month_expected,
            'year' => $year_expected,
        ];

        foreach ($selections as $key => $expected_value) {
            /** @var NodeElement $select_node */
            $select_node = $date_selector->find('css', ".tui-dateSelector__date-$key select");

            $this->assert_selected_option($select_node, $key, $expected_value);
        }
    }

    /**
     * @Given /^I set the "([^"]*)" tui date selector timezone to "([^"]*)"$/
     * @param string $field_name
     * @param string $timezone_value
     */
    public function i_set_the_tui_date_selector_timezone_to(string $field_name, string $timezone_value): void {
        $date_selector = $this->find_date_selector_by_name($field_name);

        /** @var NodeElement $timezone_select_node */
        $timezone_select_node  = $date_selector->find('css', '.tui-dateSelector__time select');
        $timezone_select_node->selectOption($timezone_value);
    }

    /**
     * @Then /^the "([^"]*)" tui date selector timezone should be set to "([^"]*)"$/
     * @param string $field_name
     * @param string $expected_value
     */
    public function the_tui_date_selector_timezone_should_be_set_to(string $field_name, string $expected_value): void {
        $date_selector = $this->find_date_selector_by_name($field_name);

        /** @var NodeElement $timezone_select_node */
        $timezone_select_node  = $date_selector->find('css', '.tui-dateSelector__time select');
        $this->assert_selected_option($timezone_select_node, 'timezone', $expected_value);
    }

    /**
     * @Then /^the "([^"]*)" display only tui form row should contain "([^"]*)"$/
     * @param string $form_row_label
     * @param string $expected_value
     */
    public function the_display_only_tui_form_row_should_contain(string $form_row_label, string $expected_value): void {
        $form_row = $this->find_form_row_by_label($form_row_label);

        $actual_content = $form_row->find('css', self::TUI_FORM_ROW_ACTION_LOCATOR . ' > *');

        $tag = $actual_content->getTagName();

        if ($tag !== 'span') {
            $this->fail('Expected display only form row to contain a span');
        }

        if (trim($actual_content->getText()) !== $expected_value) {
            $this->fail('Form row did not contain the expected text');
        }
    }

    /**
     * @Then /^the "([^"]*)" tui form row should contain "([^"]*)"$/
     * @param string $form_row_label
     * @param string $expected_value
     */
    public function the_tui_form_row_should_contain(string $form_row_label, string $expected_value): void {
        $form_row = $this->find_form_row_by_label($form_row_label);

        $actual_content = $form_row->find('css', self::TUI_FORM_ROW_ACTION_LOCATOR . ' > *');

        if (trim($actual_content->getValue()) !== $expected_value) {
            $this->fail('Form row did not contain the expected text');
        }
    }

    /**
     * @Then /^I enter "([^"]*)" into "([^"]*)" the tui form row$/
     * @param string $form_row_label
     * @param string $value
     */
    public function i_enter_into_the_tui_form_row(string $value, string $form_row_label): void {
        $form_row = $this->find_form_row_by_label($form_row_label);

        $actual_content = $form_row->find('css', self::TUI_FORM_ROW_ACTION_LOCATOR . ' > *');

        $actual_content->setValue($value);
    }

    /**
     * @Then /^the "([^"]*)" tui form row toggle switch should be "(on|off)"$/
     * @param string $form_row_label
     * @param string $expected_value
     */
    public function the_form_row_toggle_switch_should_be(string $form_row_label, string $expected_value): void {
        $toggle_button = $this->find_form_row_toggle_button($form_row_label);

        if ($toggle_button->hasClass('tui-toggleBtn__ui--aria-pressed') && $expected_value !== 'on') {
            $this->fail('Toggle button was not ' . $expected_value);
        }
    }

    /**
     * @When /^I toggle the "([^"]*)" tui form row toggle switch$/
     * @param string $form_row_label
     */
    public function i_toggle_form_row_toggle_switch(string $form_row_label): void {
        $toggle_button = $this->find_form_row_toggle_button($form_row_label);

        $toggle_button->click();
    }

    /**
     * @Then /^I should see the following options in the tui taglist in the "([^"]*)" "([^"]*)":$/
     *
     * @param string $parent_locator
     * @param string $parent_selector
     * @param TableNode $table
     */
    public function i_should_see_in_tui_taglist(string $parent_locator, string $parent_selector, TableNode $table): void {
        behat_hooks::set_step_readonly(false);

        $parent = $this->get_selected_node($parent_selector, $parent_locator);
        if ($parent === null || !$parent->isVisible()) {
            $this->fail("Couldn't find the specified element or it wasn't visible");
        }

        $taglist = $parent->find('css', self::TAGLIST_LOCATOR);
        if ($taglist === null || !$taglist->isVisible()) {
            $this->fail("Couldn't find the taglist or it wasn't visible");
        }
        $dropdown = $parent->find('css', self::TAGLIST_DROPDOWN_LIST_LOCATOR);

        // Open the dropdown
        $parent->find('css', self::TAGLIST_DROPDOWN_BUTTON_LOCATOR)->click();
        $this->wait_for_pending_js();

        $expected_options = array_keys($table->getRowsHash());
        $actual_options = array_map(static function (NodeElement $element) {
            return $element->getText();
        }, $dropdown->findAll('css', 'a'));

        if ($expected_options !== $actual_options) {
            $this->fail('Expected and actual taglist options did not match');
        }

        // Close the dropdown
        $parent->find('css', self::TAGLIST_DROPDOWN_BUTTON_LOCATOR)->click();
        $this->wait_for_pending_js();
    }

    /**
     * @When /^I select from the tui taglist in the "([^"]*)" "([^"]*)":$/
     *
     * @param string $parent_locator
     * @param string $parent_selector
     * @param TableNode $table
     */
    public function i_select_from_tui_taglist(string $parent_locator, string $parent_selector, TableNode $table): void {
        behat_hooks::set_step_readonly(false);

        $parent = $this->get_selected_node($parent_selector, $parent_locator);
        if ($parent === null || !$parent->isVisible()) {
            $this->fail("Couldn't find the specified element or it wasn't visible");
        }

        $taglist = $parent->find('css', self::TAGLIST_LOCATOR);
        if ($taglist === null || !$taglist->isVisible()) {
            $this->fail("Couldn't find the taglist or it wasn't visible");
        }
        $dropdown = $parent->find('css', self::TAGLIST_DROPDOWN_LIST_LOCATOR);

        // Open the dropdown
        $parent->find('css', self::TAGLIST_DROPDOWN_BUTTON_LOCATOR)->click();
        $this->wait_for_pending_js();

        $specified_options = array_keys($table->getRowsHash());

        foreach ($specified_options as $specified_option) {
            $clicked = false;
            foreach ($dropdown->findAll('css', 'a') as $dropdown_option) {
                $dropdown_option_text = $dropdown_option->getText();
                if (strpos($dropdown_option_text, $specified_option) !== false) {
                    $dropdown_option->click();
                    $clicked = true;
                    $this->wait_for_pending_js();
                    break;
                }
            }

            if (!$clicked) {
                $this->fail("Specified taglist option {$specified_option} isn't available.");
            }
        }

        // Close the dropdown if it is still open
        if ($parent->find('css', '.tui-dropdown--open') !== null) {
            $parent->find('css', self::TAGLIST_DROPDOWN_BUTTON_LOCATOR)->click();
            $this->wait_for_pending_js();
        }
    }

    /**
     * @param string $table_selector_type
     * @param string $table_locator
     * @param NodeElement|false $node
     * @return NodeElement
     * @throws ExpectationException
     */
    private function find_data_table(string $table_selector_type, string $table_locator, $node = false) {
        if ($table_locator !== self::DATA_TABLE_DEFAULT_LOCATOR &&
            $table_selector_type === self::DATA_TABLE_DEFAULT_SELECTOR_TYPE) {
            $table_locator = $table_locator . ' ' . self::DATA_TABLE_DEFAULT_LOCATOR;
        }

        [$table_selector, $table_locator] = behat_selectors::get_behat_selector(
            $table_selector_type,
            $table_locator,
            $this->getSession()
        );

        return $this->find($table_selector, $table_locator, false, $node);
    }

    /**
     * @param string $column_heading_text
     * @param int $row_number
     * @param string|null $table_locator
     * @param string|null $table_selector_type
     * @param NodeElement|false $node
     * @return NodeElement|mixed
     * @throws ExpectationException
     */
    private function find_data_table_cell(string $column_heading_text,
        int $row_number,
        string $table_locator = self::DATA_TABLE_DEFAULT_SELECTOR_TYPE,
        string $table_selector_type = self::DATA_TABLE_DEFAULT_LOCATOR,
        $node = false
    ) {
        $table = $this->find_data_table($table_selector_type, $table_locator, $node);

        $heading_index = $this->find_data_table_heading_index($column_heading_text, $table);
        $table_row = $this->find_data_table_row($row_number, $table);

        return $this->find_cell($heading_index, $table_row);
    }

    /**
     * @param string $column_heading_text
     * @param string $cell_text
     * @param string $table_locator
     * @param string $table_selector_type
     * @return NodeElement
     */
    private function find_data_table_cell_by_text(
        string $column_heading_text,
        string $cell_text,
        string $table_locator = self::DATA_TABLE_DEFAULT_SELECTOR_TYPE,
        string $table_selector_type = self::DATA_TABLE_DEFAULT_LOCATOR
    ): NodeElement {
        $table = $this->find_data_table($table_selector_type, $table_locator);

        $heading_index = $this->find_data_table_heading_index($column_heading_text, $table);

        $rows = $table->findAll('css', self::DATA_TABLE_ROW_LOCATOR);

        $matches =  array_filter($rows, function (NodeElement $row) use ($heading_index, $cell_text) {
            $cell =  $this->find_cell($heading_index, $row);

            if (trim($cell->getText()) === $cell_text) {
                return $cell;
            }
        });

        return reset($matches);
    }

    /**
     * @param string $column_heading_text
     * @param NodeElement $table
     * @return int|string
     */
    private function find_data_table_heading_index(string $column_heading_text, NodeElement $table): int {
        $headings = $table->findAll('css', self::DATA_TABLE_CELL_LOCATOR . ' > .tui-dataTableCell__label');

        foreach ($headings as $index => $heading) {
            if (trim($heading->getHtml()) === $column_heading_text) {
                return $index;
            }
        }

        $headings = $table->findAll('css', self::DATA_TABLE_HEADER_CELL_LOCATOR);

        foreach ($headings as $index => $heading) {
            if ($heading->getText() === $column_heading_text) {
                return $index;
            }
        }

        throw new ExpectationException('Could not find heading in tui table: ' . $column_heading_text, $this->getSession());
    }

    /**
     * @param NodeElement $table
     * @return mixed|NodeElement
     */
    private function get_data_table_row_count(NodeElement $table): int {
        return count($table->findAll('css', self::DATA_TABLE_ROW_LOCATOR));
    }

    private function expect_data_table_row_count(NodeElement $table, int $expected_row_count): void {
        $row_count = $this->get_data_table_row_count($table);

        if ($expected_row_count !== $row_count) {
            $exception_message = "Expected table to have {$expected_row_count} rows but only had {$row_count} rows";
            throw new ExpectationException($exception_message, $this->getSession());
        }
    }

    /**
     * @param int $row_number
     * @param NodeElement $table
     * @return NodeElement
     */
    private function find_data_table_row(int $row_number, NodeElement $table): NodeElement {
        $rows = $table->findAll('css', self::DATA_TABLE_ROW_LOCATOR);

        $row_index = $row_number - 1;

        if (array_key_exists($row_index, $rows)) {
            return $rows[$row_index];
        }

        throw new ExpectationException('Could not find table row ' . $row_number, $this->getSession());
    }

    /**
     * @param int $heading_index
     * @param NodeElement $table_row
     * @return NodeElement
     * @throws ExpectationException
     */
    private function find_cell(int $heading_index, NodeElement $table_row): ?NodeElement {
        $cells = $table_row->findAll('css', self::DATA_TABLE_CELL_CONTENT_LOCATOR);

        if (array_key_exists($heading_index, $cells)) {
            return $cells[$heading_index];
        }

        throw new ExpectationException('Could not find table cell for column: ' . $heading_index, $this->getSession());
    }

    /**
     * @param $filter_label_text
     * @return NodeElement
     * @throws ExpectationException
     */
    private function find_multi_select_filter($filter_label_text): NodeElement {
        $filters = $this->find_all('css', self::MULTI_SELECT_FILTER_LOCATOR);

        $matches = array_filter($filters, static function (NodeElement $filter) use ($filter_label_text) {
            $legend = $filter->find('css', self::MULTI_SELECT_FILTER_LABEL_LOCATOR);

            return $legend !== null && $legend->getText() === $filter_label_text;
        });

        return reset($matches);
    }

    /**
     * @param NodeElement $filter
     * @param string $option_text
     * @return NodeElement
     */
    private function find_multi_select_filter_checkbox(NodeElement $filter, string $option_text): NodeElement {
        $filter_options = $this->find_multi_select_filter_option($filter, $option_text);

        return $filter_options->find('css', 'input[type=checkbox]');
    }

    /**
     * @param NodeElement $filter The filter which you want to find the option in
     * @param string $option_text The option text of the filter you want to find
     * @return NodeElement
     */
    private function find_multi_select_filter_option(NodeElement $filter, string $option_text): NodeElement {
        $filter_options = $filter->findAll('css', self::MULTI_SELECT_FILTER_OPTION_LOCATOR);

        $matches = array_filter($filter_options, static function (NodeElement $filter_option) use ($option_text) {
            return $filter_option->getText() === $option_text;
        });

        return reset($matches);
    }

    /**
     * Convenience method to fail from an ExpectationException.
     *
     * @param string $error error message.
     */
    private function fail(string $error): void {
        throw new ExpectationException($error, $this->getSession());
    }

    /**
     * @param $field_name
     * @return NodeElement
     */
    private function find_date_selector_by_name($field_name): NodeElement {
        $date_selectors = $this->find_all('css', ".tui-dateSelector");

        $matching_date_selectors = array_filter($date_selectors, function (NodeElement $element) use ($field_name) {
            return $element->getAttribute('name') === $field_name;
        });

        if (empty($matching_date_selectors)) {
            $this->fail("Couldn't find date selector with he name '{$field_name}'");
        }

        return reset($matching_date_selectors);
    }

    private function find_form_row_by_label($form_row_label): NodeElement {
        $labels = $this->find_all('css', self::TUI_FORM_LABEL_LOCATOR);

        /** @var NodeElement[] $found_labels */
        $found_labels = array_filter($labels, function (NodeElement $element) use ($form_row_label) {
            return trim($element->getText()) === $form_row_label;
        });

        $found_label = reset($found_labels);

        $form_row = $found_label->getParent()->getParent();

        if (!$form_row->hasClass(self::TUI_FORM_ROW_CLASS)) {
            $this->fail('Label was not inside of a form row');
        }

        return $form_row;
    }

    private function find_form_row_toggle_button(string $form_row_label): ?NodeElement {
        $form_row = $this->find_form_row_by_label($form_row_label);

        return $form_row->find('css', self::TOGGLE_BUTTON_LOCATOR);
    }

    private function assert_selected_option(NodeElement $select_node, string $name, string $expected_value): void {
        $value = $select_node->getValue();

        /** @var NodeElement $selected_option */
        $selected_option = $select_node->find('css', "[value='{$value}']");

        if (trim($selected_option->getText()) !== $expected_value) {
            $this->fail("{$name} did not match {$expected_value}");
        }
    }

    /**
     * @param string $collapsible_label_text
     * @param NodeElement|bool $node
     * @throws ExpectationException
     */
    private function find_collapsible(string $collapsible_label_text, $node = false) {
        $collapsibles = $this->find_all('css', self::COLLAPSIBLE_LOCATOR, $node);

        $matches = array_filter($collapsibles, static function (NodeElement $filter) use ($collapsible_label_text) {
            $legend = $filter->find('css', self::COLLAPSIBLE_HEADER_TEXT_LOCATOR);
            return $legend !== null && $legend->getText() === $collapsible_label_text;
        });

        return reset($matches);
    }

    /**
     * @When /^I click on "(?P<element_string>(?:[^"]|\\")*)" tui "(?P<selector_string>[^"]*)" in the "(?P<container_string>(?:[^"]|\\")*)" tui "(?P<container_selector_string>[^"]*)"$/
     * @param string $element
     * @param string $selector
     * @param string $container
     * @param string $container_selector
     */
    public function i_click_on_the_tui_in_the_tui(string $element, string $selector, string $container, string $container_selector) {
        behat_hooks::set_step_readonly(false);
        $parent = $this->find_tui_element($container, $container_selector);
        $node = $this->find_tui_element($element, $selector, $parent);
        $node->click();
    }

    /**
     * @param string $label
     * @param string $tui_selector
     * @param NodeElement|null $container
     * @return NodeElement
     */
    private function find_tui_element(string $label, string $tui_selector, ?NodeElement $container = null): NodeElement {
        if ($tui_selector === 'toggle button') {
            $toggles = $this->find_all('css', self::TOGGLE_BUTTON_LABEL_LOCATOR, false, $container);

            $specified_toggle = null;
            foreach ($toggles as $toggle) {
                if ($toggle->getText() === $label) {
                    $specified_toggle = $toggle;
                }
            }
            if ($specified_toggle === null && $container === null) {
                $specified_toggle = $this->find_single_visible(
                    self::TOGGLE_BUTTON_LABEL_LOCATOR . "[aria-label='{$label}']",
                    'toggle'
                );
            }

            if ($specified_toggle) {
                return $this->find('css', self::TOGGLE_BUTTON_LOCATOR, false, $specified_toggle->getParent());
            }
        }

        if ($tui_selector === 'collapsible') {
            $collapsibles = $this->find_all('css', self::COLLAPSIBLE_LOCATOR, false, $container);

            $matches = array_filter($collapsibles, static function (NodeElement $filter) use ($label) {
                $legend = $filter->find('css', self::COLLAPSIBLE_HEADER_TEXT_LOCATOR);
                return $legend !== null && $legend->getText() === $label;
            });

            $collapsible = reset($matches);
            if ($collapsible && $collapsible->isVisible()) {
                return $collapsible;
            }
        }

        if ($tui_selector === 'button') {
            [$selector, $locator] = $this->transform_selector('button', $label);
            return $this->find($selector, $locator, false, $container);
        }

        $this->fail("Could not find the '{$label}' {$tui_selector}");
    }
}
