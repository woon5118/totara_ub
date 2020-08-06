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
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Johannes Cilliers <johannes.cilliers@totaralearning.com>
 * @package performelement_numeric_rating_scale
 */

namespace performelement_numeric_rating_scale;

use coding_exception;
use core\collection;
use mod_perform\models\activity\element;
use mod_perform\models\activity\respondable_element_plugin;

class numeric_rating_scale extends respondable_element_plugin {

    /**
     * @inheritDoc
     */
    public function validate_response(?string $encoded_response_data, ?element $element): collection {
        $answer_value = $this->decode_answer_value($encoded_response_data);

        $errors = new collection();

        if ($answer_value !== '') {
            $this->validate_value($answer_value, $element, $errors);
        } elseif ($element->is_required) {
            $errors->append(new answer_required_error());
        }

        return $errors;
    }

    /**
     * Get the answer value from encoded json data.
     * @param string|null $encoded_response_data
     * @return string
     */
    protected function decode_answer_value(?string $encoded_response_data): ?string {
        $response_data = json_decode($encoded_response_data, true);

        if (empty($response_data) || !isset($response_data['answer_value'])) {
            throw new coding_exception('Invalid response data format, expected "answer_value" field');
        }

        return $response_data['answer_value'];
    }

    /**
     * @param int $answer_value
     * @param element|null $element
     * @param collection $errors
     */
    protected function validate_value(int $answer_value, ?element $element, collection $errors): void {
        $data = json_decode($element->data, true);

        // If element does not have any data then we have a problem.
        if (empty($data) || !isset($data['lowValue']) || !isset($data['highValue'])) {
            throw new coding_exception('Invalid element data');
        }

        $low = $data['lowValue'];
        $high = $data['highValue'];

        // Confirm that the response value is in valid range.
        if ($answer_value < $low || $answer_value > $high) {
            $errors->append(new answer_invalid_error());
        }
    }

}
