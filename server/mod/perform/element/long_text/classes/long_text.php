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
use mod_perform\models\activity\respondable_element_plugin;

class long_text extends respondable_element_plugin {

    /**
     * @inheritDoc
     */
    public function validate_response(
        ?string $encoded_response_data,
        ?element $element,
        $is_draft_validation = false
    ): collection {
        $element_data = $element->data ?? null;
        $answer_text = $this->decode_response($encoded_response_data, $element_data);

        $errors = new collection();

        if ($this->fails_required_validation(trim($answer_text) === '', $element, $is_draft_validation)) {
            $errors->append(new answer_required_error());
        }

        return $errors;
    }

    /**
     * Pull the answer text string out of the encoded json data.
     *
     * @param string|null $encoded_response_data
     * @param string|null $encoded_element_data
     * @return string|null
     */
    public function decode_response(?string $encoded_response_data, ?string $encoded_element_data): ?string {
        return $this->decode_simple_string_response($encoded_response_data);
    }

    /**
     * @inheritDoc
     */
    public function get_participant_response_component(): string {
        return 'mod_perform/components/element/participant_form/HtmlResponseDisplay';
    }

    /**
     * @inheritDoc
     */
    public function get_sortorder(): int {
        return 60;
    }
}
