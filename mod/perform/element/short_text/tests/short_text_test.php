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
use performelement_short_text\answer_length_exceeded_error;
use performelement_short_text\answer_required_error;
use performelement_short_text\short_text;

/**
 * @group perform
 */
class mod_perform_element_short_text_testcase extends advanced_testcase {

    /**
     * @dataProvider invalid_response_data_format_provider
     * @param array|null $response_data
     * @throws coding_exception
     */
    public function test_validate_response_invalid_format(array $response_data): void {
        /** @var short_text $short_text */
        $short_text = element_plugin::load_by_plugin('short_text');

        $this->expectException(coding_exception::class);
        $this->expectExceptionMessage('Invalid response data format, expected "answer_text" field');

        $short_text->validate_response(json_encode($response_data));
    }

    public function invalid_response_data_format_provider(): array {
        return [
            'missing key' => [['irrelevant_key' => 1]],
        ];
    }


    /**
     * @dataProvider validation_provider
     * @param collection $expected_errors
     * @param string $answer_text
     * @throws coding_exception
     */
    public function test_validation(collection $expected_errors, string $answer_text): void {
        /** @var short_text $short_text */
        $short_text = element_plugin::load_by_plugin('short_text');

        $errors = $short_text->validate_response(json_encode(['answer_text' => $answer_text]));

        self::assertEquals($expected_errors, $errors);
    }

    public function validation_provider(): array {
        return [
            'no errors' => [
                new collection(), 'A short answer'
            ],
            'missing answer' => [
                new collection([new answer_required_error()]), '',
            ],
            'answer too long' => [
                new collection([new answer_length_exceeded_error()]), random_string(short_text::MAX_ANSWER_LENGTH + 1)
            ],
        ];
    }

}