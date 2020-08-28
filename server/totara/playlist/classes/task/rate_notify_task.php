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
 * @package totara_playlist
 */
namespace totara_playlist\task;

use core\message\message;
use core\task\adhoc_task;
use core_user;
use totara_playlist\output\rating_message;


final class rate_notify_task extends adhoc_task {
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
        $keys = ['owner', 'name', 'url', 'rater'];

        foreach ($keys as $key) {
            if (!property_exists($data, $key)) {
                debugging("The custom data for the task does not have key '{$key}'");
                return;
            }
        }

        $owner = core_user::get_user($data->owner, '*', MUST_EXIST);
        $rater = core_user::get_user($data->rater, '*', MUST_EXIST);

        cron_setup_user($rater);

        $url = new \moodle_url($data->url);
        $template = rating_message::create($data->name ,$url);
        $message_body = $OUTPUT->render($template);

        $message = new message();
        $message->courseid = SITEID;
        $message->component = 'totara_playlist';
        $message->name = 'rating_playlist_notification';
        $message->userfrom = $rater;
        $message->userto = $owner;
        $message->notification = 1;
        $message->subject = get_string('rating_message_subject', 'totara_playlist');
        $message->fullmessage = html_to_text($message_body);
        $message->fullmessageformat = FORMAT_HTML;
        $message->fullmessagehtml = $message_body;

        message_send($message);
    }
}