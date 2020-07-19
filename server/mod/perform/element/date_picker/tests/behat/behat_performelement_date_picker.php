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
 * @author Angela Kuznetsova <angela.jayasinghe@totaralearning.com>
 * @package performelement_date_picker
 */
use Behat\Mink\Element\NodeElement;

class behat_performelement_date_picker extends behat_base {

    public const DONE_BUTTON_LOCATOR  = '.tui-elementAdminFormActionButtons__done';

    /**
     * @When /^I click date picker question element$/
     */
    public function i_click_date_picker_question_element(): void {
        $behat_general = behat_context_helper::get('behat_general');

        $behat_general->i_click_on("Add element","button");
        $behat_general->i_click_on("Date picker","link");
    }

    /**
     * @When /^I save date picker question element data$/
     */
    public function i_save_date_picker_question_element_data(): void {
        $done_button = $this->find('css', self::DONE_BUTTON_LOCATOR);
        $done_button->click();
    }
}
