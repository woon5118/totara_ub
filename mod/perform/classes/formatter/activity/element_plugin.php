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

namespace mod_perform\formatter\activity;

use totara_core\formatter\field\string_field_formatter;
use totara_core\formatter\formatter;

/**
 * Class element_plugin
 *
 * @package mod_perform\formatter\activity
 */
class element_plugin extends formatter {

    protected function get_map(): array {
        return [
            'plugin_name' => null, // Not formatted, because this is an internal key.
            'name' => string_field_formatter::class,
        ];
    }

    protected function get_field(string $field) {
        switch ($field) {
            case 'plugin_name':
                return $this->object->get_plugin_name();
            case 'name':
                return $this->object->get_name();
            default:
                throw new \coding_exception('Unexpected field passed to formatter');
        }
    }

    protected function has_field(string $field): bool {
        $fields = ['plugin_name', 'name'];
        return in_array($field, $fields);
    }
}