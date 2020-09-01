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
 * @author David Curry <david.curry@totaralearning.com>
 * @package message_totara_airnotifier
 */

defined('MOODLE_INTERNAL') || die();

use \totara_mobile\local\device;

global $CFG;
require_once($CFG->dirroot . '/message/output/totara_airnotifier/message_output_totara_airnotifier.php');

/**
 * Tests airnotifier event triggers and observers
 */
class air_notifier_message_output_testcase extends advanced_testcase {

    /**
     * Test the message send functionality.
     * Note; This test also covers the hook and event used by message_send
     */
    public function test_air_notifier_message_send() {
        global $DB;

        set_config('enable', true, 'totara_mobile');

        // Create user and set the as the current user.
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user->id);

        // And mock up a device record.
        $now = time();
        $device = new \stdClass();
        $device->userid = $user->id;
        $device->keyprefix = 'qweqweqwe';
        $device->keyhash = 'abcdefghijklmnopqrstuvwxyz';
        $device->timeregistered = $now;
        $device->timelastaccess = $now;
        $device->appname = 'applicationname';
        $device->appversion = '0.01';
        $device->fcmtoken = 'abc123';
        $device->id = $DB->insert_record('totara_mobile_devices', $device);

        // Mock some message data.
        $mockdata = new \stdClass();
        $mockdata->userto = $user;
        $mockdata->userfrom = $user;
        $mockdata->subject = 'Test message';
        $mockdata->smallmessage = 'smallmessage';
        $mockdata->fullmessage = 'Big message with lots of words in it.';
        $mockdata->courseid = 1;
        $mockdata->component = 'test';

        $output = new message_output_totara_airnotifier();
        $result = $output->send_message($mockdata);
        $this->assertTrue($result);

        // Note: This returns true but really fails to push, to test this further (eventsink, messagesink)
        //       we'd need to fake the push success, or set up a test server to hit.
    }

    /**
     * Test the system configuration.
     */
    public function test_air_notifier_system_configuration() {
        $output = new message_output_totara_airnotifier();

        $this->assertFalse($output->is_system_configured());
        set_config('totara_airnotifier_host', 'http://localtest.com');
        $this->assertFalse($output->is_system_configured());
        set_config('totara_airnotifier_appname', 'apptest');
        $this->assertFalse($output->is_system_configured());
        set_config('totara_airnotifier_appcode', 'abc123');
        $this->assertTrue($output->is_system_configured());
    }

    /**
     * Test the air notifier defaults
     */
    public function test_air_notifier_defaults() {
        $output = new message_output_totara_airnotifier();

        $default = $output->get_default_messaging_settings();
        $this->assertEquals(11, $default); // MESSAGE_PERMITTED + MESSAGE_DEFAULT_LOGGEDIN + MESSAGE_DEFAULT_LOGGEDOFF;
    }

    /**
     * Not super important but quickly check the abstract functions
     */
    public function test_abstract_function_implementations() {
        $output = new message_output_totara_airnotifier();

        $mock = null;
        $this->assertNull($output->config_form(null));
        $this->assertTrue($output->process_form(null, $mock));
        $this->assertTrue($output->load_data($mock, null));
    }
 }
