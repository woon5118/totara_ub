<?php
/**
 * This file is part of Totara LMS
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
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author  Sam Hemelryk <sam.hemelryk@totaralearning.com>
 * @package totara_engage
 */

namespace totara_engage\watcher;

use core_user\hook\allow_view_profile;

/**
 * Class to handle user hooks.
 */
final class core_user {

    /**
     * Handles the allow_view_profile hook call.
     *
     * This method will give access to the users profile if any of the Engage features requiring it have been enabled.
     *
     * @param allow_view_profile $hook
     */
    public static function handle_allow_view_profile(allow_view_profile $hook) {
        global $CFG;
        if ($hook->has_permission()) {
            // They already have permission.
            return;
        }
        // Fastest resolution. This is never set through the interface, and must be defined in config.php
        // explicit type checks on true|false ensure that it did not come from the database.
        if (isset($CFG->totara_engage_allow_view_profiles)) {
            if ($CFG->totara_engage_allow_view_profiles === true) {
                $hook->give_permission();
                return;
            } else if ($CFG->totara_engage_allow_view_profiles === false) {
                return;
            }
        }
        // Finally, check if any of the engage features have been turned on.
        if (\totara_engage\lib::allow_view_user_profile()) {
            $hook->give_permission();
        }
    }

}
