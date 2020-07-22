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

use core\collection;
use mod_perform\entities\activity\element as element_entity;
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
            throw new \coding_exception('Respondable elements must include a title');
        }
    }

}
