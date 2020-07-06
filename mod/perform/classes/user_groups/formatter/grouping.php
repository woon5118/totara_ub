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
 * Maps the grouping class into the GraphQL mod_perform_user_grouping type.
 *
 * TODO: this should be combined with totara_competency/user_groups and put into
 * totara core somewhere.
 */
class grouping extends formatter {
    /**
     * {@inheritdoc}
     */
    protected function get_map(): array {
        return [
            'id' => null,
            'type' => string_field_formatter::class,
            'type_label' =>  string_field_formatter::class,
            'name' => string_field_formatter::class,
            'size' => null
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function get_field(string $field) {
        switch ($field) {
            case 'id':
                return $this->object->get_id();

            case 'type':
                return $this->object->get_type();

            case 'type_label':
                return $this->object->get_type_label();

            case 'name':
                return $this->object->get_name();

            case 'size':
                return $this->object->get_size();
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function has_field(string $field): bool {
        $recognized = [
            'id',
            'type',
            'type_label',
            'name',
            'size'
        ];

        return in_array($field, $recognized);
    }
}