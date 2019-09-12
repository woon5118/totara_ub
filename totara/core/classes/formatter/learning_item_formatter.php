<?php
/**
 * This file is part of Totara LMS
 *
 * Copyright (C) 2019 onwards Totara Learning Solutions LTD
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
 * @author David Curry <david.curry@totaralearning.com>
 * @package totara_core
 */

namespace totara_core\formatter;

use totara_core\formatter\formatter;
use totara_core\formatter\field\date_field_formatter;
use totara_core\formatter\field\string_field_formatter;
use totara_core\formatter\field\text_field_formatter;
use core_course\formatter\course_formatter;
use totara_program\formatter\program_formatter;
use totara_certification\formatter\certification_formatter;

class learning_item_formatter extends formatter {

    protected function get_map(): array {
        $map = [
            'id' => null,
            'itemtype' => null,
            'itemcomponent' => null,
            'fullname' => 'item_fullname_formatter',
            'shortname' => 'item_shortname_formatter',
            'progress' => null,
            'idnumber' => null,
            'duedate' => date_field_formatter::class,
            'description' => 'item_description_formatter',
            'description_format' => null,
            'url_view' => null,
        ];

        return $map;
    }

    public function item_shortname_formatter($value, $format) {
        return self::item_inherited_field_formatter('shortname', $value, $format);
    }

    public function item_fullname_formatter($value, $format) {
        return self::item_inherited_field_formatter('fullname', $value, $format);
    }

    public function item_description_formatter($value, $format) {
        return self::item_inherited_field_formatter('summary', $value, $format);
    }

    /**
     * Calls the formatter for the specific itemtype on the given field
     *
     * @param $field
     * @param $value
     * @param $format
     * @return
     */
    private function item_inherited_field_formatter($field, $value, $format) {
        $item = (object)['id' => $this->object->id, $field => $value];
        switch ($this->object->itemtype) {
            case 'course':
                $itemformatter = new course_formatter($item, $this->context);
                break;
            case 'program':
                $itemformatter = new program_formatter($item, $this->context);
                break;
            case 'certification':
                $itemformatter = new certification_formatter($item, $this->context);
                break;
            default:
                throw new \coding_exception('Unrecognised learning item type, please add to the learning item formatter.');
        }

        return $itemformatter->format($field, $format);
    }
}
