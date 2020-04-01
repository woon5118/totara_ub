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

namespace performelement_short_text;

use coding_exception;
use core\collection;
use core_text;
use mod_perform\models\activity\element_plugin;

class short_text extends element_plugin {

    public const MAX_ANSWER_LENGTH = 1024;

    protected $answer_text;

    /**
     * @inheritDoc
     */
    public function set_response_data(?array $response_data): element_plugin {
        if ($response_data === null) {
            return $this;
        }

        if (!is_array($response_data) || !array_key_exists('answer_text', $response_data)) {
            throw new coding_exception('Invalid response data format, expected "answer_text" field');
        }

        $this->answer_text = $response_data['answer_text'];

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function validate_response(): collection {
        $errors = new collection();

        if ((string) $this->answer_text === '') {
            $errors->append(new answer_required_error());
        }

        if (core_text::strlen($this->answer_text) > self::MAX_ANSWER_LENGTH) {
            $errors->append(new answer_length_exceeded_error());
        }

        return $errors;
    }
}
