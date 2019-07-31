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
 * @package container_workspace
 */
namespace totara_engage\task;

use core\message\message;
use core\task\adhoc_task;
use core_user;

final class comment_content_task extends adhoc_task {
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
        $keys = ['owner', 'resourcetype', 'commenter', 'name', 'url', 'is_comment'];

        foreach ($keys as $key) {
            if (!property_exists($data, $key)) {
                debugging("The custom data for the task does not have key '{$key}'");
                return;
            }
        }

        $owner = core_user::get_user($data->owner, '*', MUST_EXIST);
        $commenter = core_user::get_user($data->commenter, '*', MUST_EXIST);

        cron_setup_user($commenter);

        $url = new \moodle_url($data->url);
        $html_url = \html_writer::tag('a', $url, ['href' => $url]);

        $message_content = null;
        $subject_content = null;

        // Initailize message body.
        $message_body = new \stdClass();
        $message_body->fullname = fullname($commenter);
        $message_body->name = $data->name;
        $message_body->url = $html_url;

        // Initailize message subject.
        $subject = new \stdClass();
        $subject->fullname = fullname($commenter);

        if ($data->is_comment) {
            $message_content = get_string('commentcontent', 'totara_engage', $message_body);
            $subject->resourcetype = $data->resourcetype;
            $subject_content = get_string('commentcontentsubject', 'totara_engage', $subject);

        } else {
            $message_content = get_string('replycontent', 'totara_engage', $message_body);
            $subject_content = get_string('replycontentsubject', 'totara_engage', $subject);
        }

        $message = new message();
        $message->courseid = SITEID;
        $message->component = 'totara_engage';
        $message->name = $data->is_comment? 'comment_notification' : 'reply_notification';
        $message->userfrom = $commenter;
        $message->userto = $owner;
        $message->notification = 1;
        $message->subject = $subject_content;
        $message->fullmessage = strip_tags($message_content);
        $message->fullmessageformat = FORMAT_HTML;
        $message->fullmessagehtml = $message_content;

        message_send($message);
    }
}