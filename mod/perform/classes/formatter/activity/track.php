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

namespace mod_perform\formatter\activity;

use core\webapi\formatter\formatter;
use core\webapi\formatter\field\string_field_formatter;

defined('MOODLE_INTERNAL') || die();

/**
 * Maps the track model class into the GraphQL mod_perform_track type.
 */
class track extends formatter {
    /**
     * {@inheritdoc}
     */
    protected function get_map(): array {
        return [
            'id' => null,
            'description' => string_field_formatter::class,
            'status' => null,
            'assignments' => null
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function get_field(string $field) {
        switch ($field) {
            case 'id':
            case 'description':
            case 'status':
                return $this->object->$field;

            case 'assignments':
                return $this->object->$field->all();
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function has_field(string $field): bool {
        $recognized = [
            'id',
            'description',
            'status',
            'assignments'
        ];

        return in_array($field, $recognized);
    }
}