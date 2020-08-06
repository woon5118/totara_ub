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

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {
    $settings->add(new admin_setting_configtext('totara_airnotifier_host', get_string('airnotifier_host', 'message_totara_airnotifier'), get_string('config_airnotifier_host', 'message_totara_airnotifier'), 'https://push.totaralearning.com', PARAM_URL));
    $settings->add(new admin_setting_configtext('totara_airnotifier_appname', get_string('airnotifier_appname', 'message_totara_airnotifier'), get_string('config_airnotifier_appname', 'message_totara_airnotifier'), 'Totara', PARAM_RAW));
    $settings->add(new admin_setting_configpasswordunmask('totara_airnotifier_appcode', get_string('airnotifier_appcode', 'message_totara_airnotifier'), get_string('config_airnotifier_appcode', 'message_totara_airnotifier'), ''));
}
