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
 * @author Samantha Jayasinghe <samantha.jayasinghe@totaralearning.com>
 * @package performelement_multi_choice_single
 */

namespace performelement_multi_choice_single;

use coding_exception;
use mod_perform\models\activity\respondable_element_plugin;

class multi_choice_single extends respondable_element_plugin {

    /**
     * Pull the answer text string out of the encoded json data.
     *
     * @param string|null $encoded_response_data
     * @param string|null $encoded_element_data
     * @return string|string[]
     * @throws coding_exception
     */
    public function decode_response(?string $encoded_response_data, ?string $encoded_element_data) {

        $response_data = json_decode($encoded_response_data, true);
        $element_data = json_decode($encoded_element_data, true);

        if ($response_data === null) {
            return null;
        }

        if (!isset($response_data['answer_option'])) {
            throw new coding_exception('Invalid response data format, expected "answer_option" field');
        }

        if ($element_data === null || !isset($element_data['options'])) {
            throw new coding_exception('Invalid element data format, expected "options" field');
        }

        foreach ($element_data['options'] as $i => $option) {
            if (!isset($option['name']) || !isset($option['value'])) {
                throw new coding_exception('Invalid element options format, expected "name" and "value" fields');
            }
            if ($option['name'] == $response_data['answer_option']) {
                return $option['value'];
            }
        }

        return null;
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
    public function get_sortorder(): int {
        return 30;
    }
}
