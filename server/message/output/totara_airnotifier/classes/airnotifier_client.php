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

namespace message_totara_airnotifier;

use curl;
use message_totara_airnotifier\event\fcmtoken_rejected;

defined('MOODLE_INTERNAL') || die();

/**
 * Class airnotifier_client implements methods for communicating with an AirNotifier server.
 *
 * @package message_totara_airnotifier
 */
class airnotifier_client {

    /**
     * Determine whether we are in unit test environment, and if so which one.
     *
     * @return string|null
     */
    private static function testing(): ?string {
        if ((defined('BEHAT_SITE_RUNNING') && BEHAT_SITE_RUNNING)) {
            global $CFG;
            return $CFG->wwwroot . '/message/output/totara_airnotifier/mock_server.php';
        } else if ((defined('PHPUNIT_TEST') && PHPUNIT_TEST)) {
            return 'phpunit';
        } else {
            return null;
        }
    }

    /**
     * Register a device token with AirNotifier
     *
     * @param string $device_id
     * @return bool
     */
    public static function register_device(string $device_id): bool {
        // Validate input
        if (empty($device_id)) {
            // No device to delete.
            return false;
        }

        $hostname = get_config(null, 'totara_airnotifier_host');
        $appname = get_config(null, 'totara_airnotifier_appname');
        $appcode = get_config(null, 'totara_airnotifier_appcode');

        if (substr($hostname,-1) == '/') {
            $hostname = substr($hostname, 0, -1);
        }

        $data = new \stdClass();
        $data->token = $device_id;
        $data->device = 'fcm';
        $data->channel = 'default';
        $data = json_encode($data);

        $ch = new curl();
        $options = [];
        $ch->setHeader(
            [
                'Accept: application/json',
                'X-AN-APP-NAME: ' . $appname,
                'X-AN-APP-KEY: ' . $appcode
            ]
        );

        if (self::testing() == 'phpunit') {
            return true;
        } else if (self::testing()) {
            $ch->setHeader(['Cookie: BEHAT=1']);
            $ch->post(self::testing().'?api=register_device', $data, $options);
        } else {
            $ch->post($hostname . '/api/v2/tokens', $data, $options);
        }

        $response = $ch->getResponse();
        if (!empty($response['HTTP/1.1']) && $response['HTTP/1.1'] == '200 OK') {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Delete a device token from AirNotifier.
     *
     * @param string $device_id
     * @return bool
     */
    public static function delete_device(string $device_id): bool {
        // Validate input
        if (empty($device_id)) {
            // No device to delete.
            return false;
        }

        $hostname = get_config(null, 'totara_airnotifier_host');
        $appname = get_config(null, 'totara_airnotifier_appname');
        $appcode = get_config(null, 'totara_airnotifier_appcode');

        if (substr($hostname,-1) == '/') {
            $hostname = substr($hostname, 0, -1);
        }

        $ch = new curl();
        $options = [];
        $ch->setHeader(
            [
                'Accept: application/json',
                'X-AN-APP-NAME: ' . $appname,
                'X-AN-APP-KEY: ' . $appcode
            ]
        );
        if (self::testing()) {
            $data = new \stdClass();
            $data->device_id = $device_id;
            if (self::testing() == 'phpunit') {
                return true;
            } else {
                $ch->setHeader(['Cookie: BEHAT=1']);
                $ch->post(self::testing() . '?api=delete_device', $data, $options);
            }
        } else {
            $ch->delete($hostname . '/api/v2/tokens/' . rawurlencode($device_id), [], $options);
        }

        $response = $ch->getResponse();
        if (!empty($response['HTTP/1.1']) && $response['HTTP/1.1'] == '200 OK') {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Push a notification to one or more devices registered with AirNotifier.
     *
     * @param array $device_ids
     * @param \stdClass $message
     * @return bool
     */
    public static function push(array $device_ids, \stdClass $message): bool {
        // Validate inputs
        if (empty($device_ids) || empty(implode('', $device_ids))) {
            // No devices to send to.
            return false;
        }
        if (empty($message->title)) {
            return false;
        }

        $hostname = get_config(null, 'totara_airnotifier_host');
        $appname = get_config(null, 'totara_airnotifier_appname');
        $appcode = get_config(null, 'totara_airnotifier_appcode');

        if (substr($hostname,-1) == '/') {
            $hostname = substr($hostname, 0, -1);
        }

        $data = new \stdClass();
        $data->token = '';
        $data->device = 'fcm';
        $data->alert = new \stdClass();
        $data->alert->title = $message->title;
        $data->alert->body = $message->title;
        $data->fcm = new \stdClass();
        $data->fcm->data = new \stdClass();
        // This is a payload for the mobile app to consume.
        $data->fcm->data->notification = 1;
        // Add badge counts for android and apple.
        $data->fcm->notification = new \stdClass();
        $data->fcm->notification->notification_count = $message->badge_count;
        $data->fcm->apns = new \stdClass();
        $data->fcm->apns->payload = new \stdClass();
        $data->fcm->apns->payload->aps = new \stdClass();
        $data->fcm->apns->payload->aps->badge = $message->badge_count;

        $ch = new curl();
        $options = [];
        $ch->setHeader(
            [
                'Accept: application/json',
                'X-AN-APP-NAME: ' . $appname,
                'X-AN-APP-KEY: ' . $appcode,
            ]
        );

        $result = [];
        foreach ($device_ids as $token) {
            $data->token = $token;
            $jsondata = json_encode($data);
            if (self::testing() == 'phpunit') {
                $result[$token] = true;
                continue;
            } else if (self::testing()) {
                $ch->setHeader(['Cookie: BEHAT=1']);
                $ch->post(self::testing().'?api=push', $jsondata, $options);
            } else {
                $ch->post($hostname . '/api/v2/push', $jsondata, $options);
            }

            $response = $ch->getResponse();
            $rejectresp = ['400 Bad Request', '404 Not Found'];
            if (!empty($response['HTTP/1.1']) && $response['HTTP/1.1'] == '202 Accepted') {
                $result[$token] = true;
            } else if (in_array($response['HTTP/1.1'], $rejectresp)) {
                fcmtoken_rejected::create_from_token($token)->trigger();
                return false;
            }
        }

        // Collapse result
        if (in_array(false, $result)) {
            return false;
        } else {
            return true;
        }
    }
}
