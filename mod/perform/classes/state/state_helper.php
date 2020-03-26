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
 * @author Matthias Bonk <matthias.bonk@totaralearning.com>
 * @package mod_perform
 */

namespace mod_perform\state;

use coding_exception;

defined('MOODLE_INTERNAL') || die();

/**
 * This class provides some state related convenience methods.
 */
class state_helper {

    /**
     * Get all state classes for the given object type.
     *
     * @param string $object_type
     * @return string[]
     */
    public static function get_all_states(string $object_type): array {
        return \core_component::get_namespace_classes(
            'state\\' . $object_type,
            'mod_perform\state\state',
            'mod_perform'
        );
    }

    /**
     * Get state class from DB code.
     *
     * @param int $code The code to create a state of.
     * @param string $object_type
     * @return string
     */
    public static function from_code(int $code, string $object_type): string {
        $all_states = static::get_all_states($object_type);
        foreach ($all_states as $state_class) {
            if (call_user_func([$state_class, 'get_code']) === $code) {
                return $state_class;
            }
        }
        throw new coding_exception("Cannot find state with code: $code");
    }
}
