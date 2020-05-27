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
 * @author Oleg Demeshev <oleg.demeshev@totaralearning.com>
 * @package performelement_long_text
 */

namespace performelement_long_text;

use coding_exception;
use core\collection;
use mod_perform\models\activity\element;
use mod_perform\models\activity\element_plugin;

class long_text extends element_plugin {

    /**
     * @inheritDoc
     */
    public function validate_response(?string $encoded_response_data, ?element $element): collection {
        $answer_text = $this->decode_answer_text($encoded_response_data);

        $errors = new collection();

        if (trim((string)$answer_text) === '' && $element->is_required) {
            $errors->append(new answer_required_error());
        }

        return $errors;
    }

    /**
     * Pull the answer text string out of the encoded json data.
     * @param string|null $encoded_response_data
     * @return string
     */
    protected function decode_answer_text(?string $encoded_response_data): ?string {
        $response_data = json_decode($encoded_response_data, true);

        if ($response_data === null) {
            return null;
        }

        if (!is_array($response_data) || !array_key_exists('answer_text', $response_data)) {
            throw new coding_exception('Invalid response data format, expected "answer_text" field');
        }

        return $response_data['answer_text'];
    }

}
