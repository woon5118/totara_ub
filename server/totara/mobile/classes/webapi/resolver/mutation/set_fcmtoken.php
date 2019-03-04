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
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author David Curry <david.curry@totaralearning.com>
 * @package totara_mobile
 */

namespace totara_mobile\webapi\resolver\mutation;

use core\webapi\execution_context;
use core\webapi\resolver\has_middleware;
use core\webapi\mutation_resolver;
use core\webapi\middleware\require_login;

/**
 * Mutation to set an FCM Token for a device
 */
class set_fcmtoken implements mutation_resolver, has_middleware {

    /**
     * Sets the fcm token for the given device.
     *
     * @param array $args
     * @param execution_context $ec
     * @return bool
     */
    public static function resolve(array $args, execution_context $ec) {
        global $DB, $USER;

        require_capability('totara/mobile:use', \context_user::instance($USER->id));

        // Only allow mobile contexts to set fcm tokens.
        if (!($ec instanceof \totara_mobile\webapi\execution_context)) {
            return false;
        }

        // Make sure the context is linked to a valid device.
        $deviceid = $ec->get_device_id();
        if (!$deviceid) {
            return false;
        }

        // Make sure the device belongs to the logged in user... just in case.
        $userid = $DB->get_field('totara_mobile_devices', 'userid', ['id' => $deviceid]);
        if ($userid != $USER->id) {
            return false;
        }

        // The token can be empty (reset) but the argument has to be handed through.
        if (!isset($args['token'])) {
            return false;
        }

        // Finally, update the field.
        $token = $args['token'];
        return $DB->set_field('totara_mobile_devices', 'fcmtoken', $token, ['id' => $deviceid]);
    }


    // You can't do this unless you're logged in.
    public static function get_middleware(): array {
        return [
            new require_login()
        ];
    }
}
