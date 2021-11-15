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
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Chris Snyder <chris.snyder@totaralearning.com>
 * @package message_totara_airnotifier
 */
namespace message_totara_airnotifier\observer;

use totara_mobile\event\fcmtoken_received;
use totara_mobile\event\fcmtoken_removed;
use message_totara_airnotifier\airnotifier_client;

/**
 * Observer for fcmtoken management
 */
final class fcmtoken_observer {
    /**
     * comment_resolver constructor.
     */
    private function __construct() {
        // Preventing this class from construction.
    }

    /**
     * @param fcmtoken_received $event
     * @return void
     */
    public static function on_fcmtoken_received(fcmtoken_received $event): void {
        $data = $event->get_data();
        if (!empty($data['other']['fcmtoken'])) {
            airnotifier_client::register_device($data['other']['fcmtoken']);
        }
    }

    /**
     * @param fcmtoken_received $event
     * @return void
     */
    public static function on_fcmtoken_removed(fcmtoken_removed $event): void {
        $data = $event->get_data();
        if (!empty($data['other']['fcmtoken'])) {
            airnotifier_client::delete_device($data['other']['fcmtoken']);
        }
    }
}