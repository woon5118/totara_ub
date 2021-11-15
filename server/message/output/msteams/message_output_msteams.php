<?php
/**
 * This file is part of Totara Learn
 *
 * Copyright (C) 2019 onwards Totara Learning Solutions LTD
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
 * @author Johannes Cilliers <johannes.cilliers@totaralearning.com>
 * @package message_msteams
 */

use message_msteams\task\send_user_message_adhoc_task;
use totara_msteams\bot\message\card\card_factory;
use totara_msteams\bot\message\card\user_message_card;

require_once($CFG->dirroot.'/message/output/lib.php');

/**
 * The Microsoft Teams message processor.
 */
class message_output_msteams extends message_output {

    /**
     * Processes the message and sends a notification via Microsoft Teams
     *
     * @param stdClass $eventdata the event data submitted by the message sender plus $eventdata->savedmessageid
     * @return true if ok, false if error
     */
    function send_message($eventdata) {
        global $DB;

        // Get message details.
        $message = $DB->get_record('message', ['id' => $eventdata->savedmessageid]);
        if (!$message) {
            // notification cannot be found, exit.
            mtrace('ERROR: handle_notification_sent - notification cannot be found');
            return true;
        }

        $adhocktask = new send_user_message_adhoc_task();
        $adhocktask->set_custom_data(serialize($message));
        $adhocktask->set_component('message_msteams');
        \core\task\manager::queue_adhoc_task($adhocktask);
        return true;
    }

    /**
     * Creates necessary fields in the messaging config form.
     *
     * @param array $preferences An array of user preferences
     */
    function config_form($preferences) {

    }

    /**
     * Parses the submitted form data and saves it into preferences array.
     *
     * @param stdClass $form preferences form class
     * @param array $preferences preferences array
     */
    function process_form($form, &$preferences) {

    }

    /**
     * Loads the config data from database to put on the form during initial form display
     *
     * @param array $preferences preferences array
     * @param int $userid the user id
     */
    function load_data(&$preferences, $userid) {

    }

    /**
     * Tests whether the Microsoft Teams settings have been configured
     * @return boolean true if Microsoft Teams is configured
     */
    function is_system_configured() {
        return \totara_core\advanced_feature::is_enabled('totara_msteams');
    }

    /**
     * Tests whether the Microsoft Teams settings have been configured on user level
     * @param  object $user the user object, defaults to $USER.
     * @return bool has the user made all the necessary settings
     * in their profile to allow this plugin to be used.
     */
    function is_user_configured($user = null) {
        return true;
    }
}
