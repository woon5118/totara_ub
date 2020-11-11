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
 * @author Angela Kuznetsova <angela.kuznetsova@totaralearning.com>
 * @package performelement_date_picker
 */

namespace performelement_date_picker;

use coding_exception;
use core\collection;
use mod_perform\models\activity\element;
use mod_perform\models\activity\respondable_element_plugin;

class date_picker extends respondable_element_plugin {

    /**
     * @inheritDoc
     */
    public function get_sortorder(): int {
        return 10;
    }

    /**
     * @inheritDoc
     */
    public function validate_response(
        ?string $encoded_response_data,
        ?element $element,
        $is_draft_validation = false
    ): collection {

        $response_data = json_decode($encoded_response_data, true);

        $errors = new collection();

        if ($this->fails_required_validation(is_null($response_data), $element, $is_draft_validation)) {
            $errors->append(new answer_required_error());
        }

        if (!is_null($response_data)) {
            if (!isset($response_data['iso'])) {
                $errors->append(new date_iso_required_error());
            } else {
                $date_object = \DateTime::createFromFormat('Y-m-d', $response_data['iso']);

                if ($date_object === false) {
                    $errors->append(new invalid_date_error());
                }
            }
        }

        return $errors;
    }

    /**
     * Pull the answer text string out of the encoded json data.
     *
     * @param string|null $encoded_response_data
     * @param string|null $encoded_element_data
     * @return string|string[]
     */
    public function decode_response(?string $encoded_response_data, ?string $encoded_element_data) {
        return userdate(
            $this->get_response_timestamp($encoded_response_data),
            get_string('strftimedatefullshort', 'langconfig')
        );
    }

    /**
     * @inheritDoc
     */
    public function format_response_lines(?string $encoded_response_data, ?string $encoded_element_data): array {
        $decoded_response = $this->get_response_timestamp($encoded_response_data);

        if ($decoded_response === null) {
            return [];
        }

        $formatted_date = userdate(
            $decoded_response,
            get_string('strftimedate', 'langconfig')
        );

        return [$formatted_date];
    }

    /**
     * Pull the timestamp of the response date out of the encoded response.
     *
     * @param string|null $encoded_response_data
     * @return int|null
     */
    private function get_response_timestamp(?string $encoded_response_data): ?int {
        $response_data = json_decode($encoded_response_data, true);

        if ($response_data === null) {
            return null;
        }

        $date_object = \DateTime::createFromFormat('Y-m-d', $response_data['iso']);

        return $date_object->getTimestamp();
    }
}
