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
use performelement_date_picker\date_picker;
use performelement_date_picker\date_iso_required_error;
use performelement_date_picker\answer_required_error;
use performelement_date_picker\invalid_date_error;

/**
 * @group perform
 * @group perform_element
 */
class performelement_date_picker_testcase extends advanced_testcase {

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

    /**
     * @dataProvider validation_provider
     * @param collection $expected_errors
     * @param array|null $answer
     */
    public function test_validation(collection $expected_errors, ?array $answer): void {
        /** @var date_picker $element_type */
        $element_type = element_plugin::load_by_plugin('date_picker');

        $json = '{}';
        $element = $this->perform_generator()->create_element(['title' => 'element one', 'is_required' => true, 'data' => $json]);
        $errors = $element_type->validate_response(json_encode($answer), $element);

        self::assertEquals($expected_errors, $errors);
    }

    public function validation_provider(): array {
        return [
            'valid' => [
                new collection(),
                ['iso' => '1903-03-03'],
            ],
            'missing answer' => [
                new collection([new answer_required_error()]),
                null,
            ],
            'missing iso' => [
                new collection([new date_iso_required_error()]),
                ['i' => '1903-03-03']
            ],
            'invalid date' => [
                new collection([new invalid_date_error()]),
                ['iso' => 'not-a-date']
            ]
        ];
    }

    /**
     * @dataProvider draft_validation_provider
     * @param collection $expected_errors
     * @param array|null $answer
     */
    public function test_draft_validation(collection $expected_errors, ?array $answer): void {
        /** @var date_picker $element_type */
        $element_type = element_plugin::load_by_plugin('date_picker');

        $json = '{}';
        $element = $this->perform_generator()->create_element(['title' => 'element one', 'is_required' => true, 'data' => $json]);
        $errors = $element_type->validate_response(json_encode($answer), $element, true);

        self::assertEquals($expected_errors, $errors);
    }

    public function draft_validation_provider(): array {
        return [
            'valid' => [
                new collection(),
                ['iso' => '1903-03-03'],
            ],
            'missing answer' => [
                new collection(),
                null,
            ],
            'missing iso' => [
                new collection([new date_iso_required_error()]),
                ['i' => '1903-03-03']
            ],
            'invalid date' => [
                new collection([new invalid_date_error()]),
                ['iso' => 'not-a-date']
            ]
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