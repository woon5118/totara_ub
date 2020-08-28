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
 * @author Qingyang Liu <qingyang.liu@totaralearning.com>
 * @package totara_engage
 */
namespace totara_engage\task;

use core\message\message;
use core\task\adhoc_task;
use core_user;
use totara_engage\output\share_message;

final class share_notify_task extends adhoc_task {
    /**
     * Constructor.
     */
    public function __construct() {
        $this->set_component('totara_engage');
    }

    /**
     * @return void
     */
    public function execute() {
        global $OUTPUT;

        $data = $this->get_custom_data();
        $keys = ['component', 'recipient_id', 'sharer_id', 'item_name'];
    
        foreach ($keys as $key) {
            if (!property_exists($data, $key)) {
                debugging("The custom data for the task does not have key '{$key}'");
                return;
            }
        }

        $sharer = core_user::get_user($data->sharer_id, '*', MUST_EXIST);
        $recipient = core_user::get_user($data->recipient_id, '*', MUST_EXIST);

        cron_setup_user($sharer);

        $message_body = new \stdClass();
        $message_body->fullname = fullname($sharer);
        $message_body->name = $data->item_name;

        $template = share_message::create($message_body);
        $message_body = $OUTPUT->render($template);

        $message = new message();
        $message->courseid = SITEID;
        $message->component = 'totara_engage';
        $message->name = 'share_notification';
        $message->userfrom = $sharer;
        $message->userto = $recipient;
        $message->notification = 1;
        $message->subject = get_string('share_message_subject', 'totara_engage', $data->component);
        $message->fullmessage = html_to_text($message_body);
        $message->fullmessageformat = FORMAT_HTML;
        $message->fullmessagehtml = $message_body;

        message_send($message);
    }
}