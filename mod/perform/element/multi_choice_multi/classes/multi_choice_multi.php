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
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Oleg Demeshev <oleg.demeshev@totaralearning.com>
 * @package performelement_multi_choice_multi
 */

namespace performelement_multi_choice_multi;

use core\collection;
use coding_exception;
use mod_perform\models\activity\element;
use mod_perform\models\activity\element_plugin;

class multi_choice_multi extends element_plugin {
    /**
     * @inheritDoc
     */
    public function validate_response(?string $encoded_response_data, ?element $element): collection {
        $answer_option = $this->decode_answer_option($encoded_response_data);

        $errors = new collection();

        if (empty($answer_option) && $element->is_required) {
            $errors->append(new answer_required_error());
        }

        // TODO: TL-25497

        return $errors;
    }

    /**
     * Pull the answer_option out of the encoded json data.
     * @param string|null $encoded_response_data
     * @return array|null
     */
    protected function decode_answer_option(?string $encoded_response_data): ?array {
        $response_data = json_decode($encoded_response_data, true);

        if ($response_data === null) {
            return null;
        }

        if (!is_array($response_data) || !array_key_exists('answer_option', $response_data)) {
            throw new coding_exception('Invalid response data format, expected "answer_option" field');
        }

        return $response_data['answer_option'];
    }

}