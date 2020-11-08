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

namespace mod_perform\models\activity;

use coding_exception;
use core\collection;
use mod_perform\entity\activity\element as element_entity;
use mod_perform\models\response\element_validation_error;

/**
 * Class element_plugin
 *
 * Base class for defining a question/respondable type of element (something that accepts responses),
 * including its specific behaviour.
 *
 * @package mod_perform\models\activity
 */
abstract class respondable_element_plugin extends element_plugin {

    /**
     * Hook method to validate the response data.
     * This method is responsible for decoding the raw response data and validating it.
     *
     * Should return a collection of element_validation_errors (or an empty collection when there are no errors).
     *
     * @param string|null $encoded_response_data
     * @param element|null $element
     *
     * @return collection|element_validation_error[]
     * @see element_validation_error
     */
    public function validate_response(?string $encoded_response_data, ?element $element): collection {
        return new collection();
    }

    public function validate_element(element_entity $element) {
        // All respondable elements require a title.
        if (empty(trim($element->title))) {
            throw new coding_exception('Respondable elements must include a title');
        }
    }

    /**
     * Method which accepts the response data and element data and outputs the decoded response.
     * This method handles any re-formatting that is internal to the element (e.g. the element
     * knows how to structure the response based on the element data) but does NOT do any output
     * formatting such as format_string().
     *
     * @param string|null $encoded_response_data
     * @param string|null $encoded_element_data
     * @return string|string[]
     * @throws coding_exception
     */
    abstract public function decode_response(?string $encoded_response_data, ?string $encoded_element_data);

    /**
     * Format a response into lines ready to be displayed.
     *
     * @param string|null $encoded_response_data
     * @param string|null $encoded_element_data
     * @return string[]
     */
    public function format_response_lines(?string $encoded_response_data, ?string $encoded_element_data): array {
        $decoded_response = $this->decode_response(
            $encoded_response_data,
            $encoded_element_data
        );

        if ($decoded_response === null) {
            return [];
        }

        // Wrap scalars values in an array.
        return (array) $decoded_response;
    }

    /**
     * Returns example response data that is in a format valid for the element. Used for generating records during testing.
     *
     * @return string
     */
    public function get_example_response_data(): string {
        return '""';
    }

    /**
     * @inheritDoc
     */
    public function get_group(): int {
        return self::GROUP_QUESTION;
    }

    /**
     * @inheritDoc
     */
    final public function has_title(): bool {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function get_title_text():string {
        return get_string('question_title', 'mod_perform');
    }

    /**
     * @inheritDoc
     */
    final public function is_title_required(): bool {
        return true;
    }

    /**
     * Return true if element has reporting id
     *
     * @return bool
     */
    public function has_reporting_id(): bool {
        return true;
    }

    /**
     * Return true if element response required enabled
     *
     * @return bool
     */
    public function is_response_required_enabled(): bool {
        return true;
    }

    /**
     * This method return element's user form vue component name.
     *
     * @return string
     */
    public function get_participant_response_component(): string {
        return 'mod_perform/components/element/participant_form/ResponseDisplay';
    }

    /**
     * Pull the answer text string out of the encoded json data.
     *
     * @param string|null $encoded_response_data
     * @return string|null
     */
    protected function decode_simple_string_response(?string $encoded_response_data): ?string {
        if ($encoded_response_data === null) {
            return null;
        }

        $decoded_response = json_decode($encoded_response_data, true);

        if ($decoded_response === null) {
            return null;
        }

        if (!is_string($decoded_response)) {
            throw new coding_exception('Invalid response data format, expected a string');
        }

        return $decoded_response;
    }

}
