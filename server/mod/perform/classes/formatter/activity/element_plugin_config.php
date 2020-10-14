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
 * @author Samantha Jayasinghe <samantha.jayasinghe@totaralearning.com>
 * @package mod_perform
 */
namespace mod_perform\formatter\activity;

use core\webapi\formatter\formatter;

/**
 * Class element_plugin
 *
 * @package mod_perform\formatter\activity
 */
class element_plugin_config extends formatter {

    protected function get_map(): array {
        return [
            'is_respondable' => null, // not formatted, because this is element type
            'has_title' => null, // not formatted, because this is element title field
            'has_reporting_id' => null, // not formatted, because this is element reporting id field
            'title_text' => null, // not formatted, because this is lang string
            'is_title_required' => null, // not formatted, because this is to check title is required
            'is_response_required_enabled' => null, // not formatted, because this is to check response is required
        ];
    }

    protected function get_field(string $field) {
        $is_respondable = $this->object->get_is_respondable();
        if (!$is_respondable &&
            in_array($field, ['has_reporting_id', 'is_response_required_enabled'])
        ) {
            return false;
        }
        switch ($field) {
            case 'is_respondable':
                return $is_respondable;
            case 'has_title':
                return $this->object->has_title();
            case 'has_reporting_id':
                return $this->object->has_reporting_id();
            case 'title_text':
                return $this->object->get_title_text();
            case 'is_title_required':
                return $this->object->is_title_required();
            case 'is_response_required_enabled':
                return $this->object->is_response_required_enabled();
            default:
                throw new \coding_exception('Unexpected field passed to formatter');
        }
    }

    protected function has_field(string $field): bool {
        $fields = [
            'is_respondable',
            'has_title',
            'has_reporting_id',
            'title_text',
            'is_title_required',
            'is_response_required_enabled',
        ];
        return in_array($field, $fields);
    }
}