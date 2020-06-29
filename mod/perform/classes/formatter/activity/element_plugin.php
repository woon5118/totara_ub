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

use core\webapi\formatter\field\string_field_formatter;
use core\webapi\formatter\formatter;

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
            'admin_form_component' => null, // not formatted, because this admin vue component name
            'admin_display_component' => null, // not formatted, because this admin vue component name
            'admin_read_only_display_component' => null, // not formatted, because this admin vue component name
            'participant_form_component' => null, //not formatted, because this participant form vue component name
            'participant_response_component' => null //not formatted, because this participant response display vue component name
        ];
    }

    protected function get_field(string $field) {
        switch ($field) {
            case 'plugin_name':
                return $this->object->get_plugin_name();
            case 'name':
                return $this->object->get_name();
            case 'admin_form_component':
                return $this->object->get_admin_form_component();
            case 'admin_display_component':
                return $this->object->get_admin_display_component();
            case 'admin_read_only_display_component':
                return $this->object->get_admin_read_only_display_component();
            case 'participant_form_component':
                return $this->object->get_participant_form_component();
            case 'participant_response_component':
                return $this->object->get_participant_response_component();
            default:
                throw new \coding_exception('Unexpected field passed to formatter');
        }
    }

    protected function has_field(string $field): bool {
        $fields = [
            'plugin_name',
            'name',
            'admin_form_component',
            'admin_display_component',
            'admin_read_only_display_component',
            'participant_form_component',
            'participant_response_component'
        ];
        return in_array($field, $fields);
    }
}