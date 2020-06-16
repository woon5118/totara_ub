<?php
/*
 * This file is part of Totara Perform
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
 * @author Oleg Demeshev <oleg.demeshev@totaralearning.com>
 * @package performelement_multi_choice_multi
 */

use core\collection;
use mod_perform\models\activity\element_plugin;
use performelement_multi_choice_multi\answer_required_error;
use performelement_multi_choice_multi\multi_choice_multi;

/**
 * @group perform
 */
class mod_perform_element_multi_choice_multi_testcase extends advanced_testcase {

    /**
     * @dataProvider invalid_response_data_format_provider
     * @param array|null $response_data
     * @throws coding_exception
     */
    public function test_validate_response_invalid_format(array $response_data): void {
        /** @var multi_choice_multi $element_type */
        $element_type = element_plugin::load_by_plugin('multi_choice_multi');

        $this->expectException(coding_exception::class);
        $this->expectExceptionMessage('Invalid response data format, expected "answer_option" field');

        $element = $this->perform_generator()->create_element(['title' => 'element one', 'is_required' => true]);
        $element_type->validate_response(json_encode($response_data), $element);
    }

    public function invalid_response_data_format_provider(): array {
        return [
            'missing key' => [['irrelevant_key' => 1]],
        ];
    }


    /**
     * @dataProvider validation_provider
     * @param collection $expected_errors
     * @param array $answer_option
     * @throws coding_exception
     */
    public function test_validation(collection $expected_errors, array $answer_option): void {
        /** @var multi_choice_multi $element_type */
        $element_type = element_plugin::load_by_plugin('multi_choice_multi');

        $element = $this->perform_generator()->create_element(['title' => 'element one', 'is_required' => true]);
        $errors = $element_type->validate_response(json_encode(['answer_option' => $answer_option]), $element);

        self::assertEquals($expected_errors, $errors);
    }

    public function validation_provider(): array {
        $json = '{"options":[{"name":"option_0","value":"11"},{"name":"option_1","value":"12"},{"name":"option_2","value":"13"}]}';
        return [
            'no errors' => [
                new collection(),
                [
                    'options' =>
                    [
                        'name' => 'option_0',
                        'value' => '1'
                    ],
                    [
                        'name' => 'option_2',
                        'value' => '1'
                    ]
                ]
            ],
            'missing answer' => [
                new collection([new answer_required_error()]), [],
            ],
        ];
    }

    /**
     * @return component_generator_base|mod_perform_generator
     */
    protected function perform_generator() {
        if (!isset($this->perform_generator)) {
            $this->perform_generator = $this->getDataGenerator()->get_plugin_generator('mod_perform');
        }
        return $this->perform_generator;
    }
}