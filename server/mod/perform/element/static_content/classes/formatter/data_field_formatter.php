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
 * @package performelement_static_content
 *
 */

namespace performelement_static_content\formatter;

use core\webapi\formatter\field\base;
use core\webapi\formatter\field\string_field_formatter;


/**
 * This formatter runs the input text through the string field formatter
 *
 * @package performelement_static_content\formatter
 */
class data_field_formatter extends base {

    /**
     * Runs format on the input text
     *
     * @param object $data the json encoded data
     * @return false|string
     */
    protected function get_default_format($data) {

        $formatter = new string_field_formatter($this->format, $this->context);

        $text = json_decode($data, true);
        $text['textValue'] = $formatter->format($text['textValue']);
        $text = json_encode($text);
        if ($text === false) {
            throw new \coding_exception('Error encoding the formatted options');
        }

        return $text;
    }

}