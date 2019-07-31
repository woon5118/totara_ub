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
use totara_engage\local\helper;

final class share_item_task extends adhoc_task {
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

        $url = new \moodle_url('/totara/engage/shared_with_you.php');
        $html_url = \html_writer::tag('a', $url->out(true), ['href' => $url->out(true)]);

        $message = new message();
        $message_data= new \stdClass();
        $message_data->fullname = fullname($sharer);
        $message_data->name = $data->item_name;
        $message_data->url = $html_url;
        $message_content = get_string('messagecontentshared', 'totara_engage', $message_data);

        $message->courseid = SITEID;
        $message->component = 'totara_engage';
        $message->name = 'notification';
        $message->userfrom = $sharer;
        $message->userto = $recipient;
        $message->notification = 1;
        $message->subject = get_string('messagecontentsharedsubject', 'totara_engage', $data->component);
        $message->fullmessage = strip_tags($message_content);
        $message->fullmessageformat = FORMAT_HTML;
        $message->fullmessagehtml = $message_content;

        message_send($message);
    }
}