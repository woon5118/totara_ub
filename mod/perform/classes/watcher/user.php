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

namespace mod_perform\watcher;

use core_user\hook\allow_view_profile_field;
use mod_perform\entities\activity\participant_instance;

class user {

    /**
     * User access hook to check if one user can view another users profile field in the context of mod perform.
     *
     * @param allow_view_profile_field $hook
     */
    public static function allow_view_profile_field(allow_view_profile_field $hook): void {
        if ($hook->has_permission()) {
            return;
        }

        if (!static::is_allowed_profile_field($hook->field)) {
            return;
        }

        if (static::should_allow_view_profile($hook->viewing_user_id, $hook->target_user_id)) {
            $hook->give_permission();
        }
    }

    private static function is_allowed_profile_field(string $field): bool {
        static $allowed_profile_fields = [
            'id',
            'fullname',
            'profileimageurlsmall',
        ];

        return in_array($field, $allowed_profile_fields, true);
    }

    private static function should_allow_view_profile(int $viewing_user_id, int $target_user_id): bool {
        static $cache = [];
        $cache_key = $viewing_user_id . '-' . $target_user_id;

        if (!array_key_exists($cache_key, $cache)) {
            $result = participant_instance::repository()->user_can_view_other_users_profile(
                $viewing_user_id,
                $target_user_id
            );

            $cache[$cache_key] = $result;
        }

        return $cache[$cache_key];
    }

}