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
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Nathan Lewis <nathan.lewis@totaralearning.com>
 * @package mod_perform
 */

use core\collection;
use mod_perform\models\activity\element_plugin;
use performelement_custom_rating_scale\custom_rating_scale;
use performelement_date_picker\date_picker;
use performelement_short_text\answer_length_exceeded_error;
use performelement_short_text\answer_required_error;
use performelement_short_text\short_text;

/**
 * @group perform
 */
class date_picker_testcase extends advanced_testcase {

    public function test_format_response_lines(): void {
        $date_picker = date_picker::load_by_plugin('date_picker');
        $response = ['iso' => '2020-12-04'];

        $element_data = [];

        $lines = $date_picker->format_response_lines(json_encode($response), json_encode($element_data));
        self::assertCount(1, $lines);
        self::assertEquals('4 December 2020', $lines[0]);

        $lines = $date_picker->format_response_lines(json_encode(null), json_encode($element_data));
        self::assertCount(0, $lines);
    }

}