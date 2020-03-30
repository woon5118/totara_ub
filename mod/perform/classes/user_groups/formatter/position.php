<?php
/**
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
 * @author Murali Nair <murali.nair@totaralearning.com>
 * @package mod_perform
 */

namespace mod_perform\user_groups\formatter;

use core\webapi\formatter\formatter;
use core\webapi\formatter\field\string_field_formatter;

defined('MOODLE_INTERNAL') || die();

/**
 * Maps a stdclass (from the database) into the GraphQL mod_perform_position
 * type.
 *
 * TODO: this should be combined with totara_competency/user_groups and put into
 * totara core somewhere.
 */
class position extends formatter {
    /**
     * {@inheritdoc}
     */
    protected function get_map(): array {
        return [
            'id' => null,
            'shortname' =>  string_field_formatter::class,
            'fullname' =>  string_field_formatter::class,
            'idnumber' => string_field_formatter::class,
            'description' => string_field_formatter::class
        ];
    }


    /**
     * {@inheritdoc}
     */
    protected function get_field(string $field) {
        switch ($field) {
            case 'id':
            case 'shortname':
            case 'fullname':
            case 'idnumber':
            case 'description':
                return $this->object->$field;
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function has_field(string $field): bool {
        $recognized = [
            'id',
            'shortname',
            'fullname',
            'idnumber',
            'description'
        ];

        return in_array($field, $recognized);
    }
}