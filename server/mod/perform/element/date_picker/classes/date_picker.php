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
use mod_perform\models\activity\respondable_element_plugin;

class date_picker extends respondable_element_plugin {

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

        if ($response_data === null) {
            return null;
        }

        if (!isset($response_data['date'])) {
            throw new coding_exception('Invalid response data format, expected "date" field');
        }

        if (!isset($response_data['date']['iso'])) {
            throw new coding_exception('Invalid response data format, expected "date" field to contain "iso" property');
        }

        $date_object = \DateTime::createFromFormat('Y-m-d', $response_data['date']['iso']);

        if ($date_object === false) {
            throw new coding_exception('Invalid response data format, could not parse ISO date');
        }

        return userdate($date_object->getTimestamp(), get_string('strftimedatefullshort', 'langconfig'));
    }
}
