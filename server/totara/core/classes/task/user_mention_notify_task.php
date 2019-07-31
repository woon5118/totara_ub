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
 * @author Kian Nguyen <kian.nguyen@totaralearning.com>
 * @package totara_core
 */

namespace totara_core\task;

use core\message\message;
use core\task\adhoc_task;
use totara_core\entity\mention;
use totara_core\output\mention_message;
use totara_core\repository\mention_repository;

/**
 * Class user_mention_notify_task
 * @package totara_core\task
 */
final class user_mention_notify_task extends adhoc_task {
    /**
     * @return void
     */
    public function execute() {
        global $DB, $OUTPUT;
        $data = $this->get_custom_data();

        if (!$data) {
            throw new \coding_exception("Missing data for executing the task");
        }

        $actor = $DB->get_record('user', ['id' => $this->get_userid()], '*', MUST_EXIST);
        $component = $this->get_component();

        $courseid = SITEID;
        $context = \context::instance_by_id($data->contextid);

        if (CONTEXT_COURSE === $context->contextlevel) {
            $courseid = $context->instanceid;
        } else if (CONTEXT_MODULE === $context->contextlevel) {
            // Fetch the courseid from the module.
            $courseid = $DB->get_field('course_modules', 'course', ['id' => $context->instanceid]);
        }

        $url = (new \moodle_url($data->url))->out();
        $area = $data->area;

        $manager = get_string_manager();

        $messagetitle = get_string('mentiontitle:comment', 'totara_core', fullname($actor));
        if ($manager->string_exists('mentiontitle:' . $area, $component)) {
            $messagetitle = get_string('mentiontitle:' . $area, $component, fullname($actor));
        }

        $bodyvars = [
            'fullname' => fullname($actor),
            'title' => empty($data->title) ? '' : "'{$data->title}'",
        ];
        $description = get_string('mentionbody:comment', 'totara_core', $bodyvars);
        if ($manager->string_exists('mentionbody:' . $area, $component)) {
            $description = get_string('mentionbody:' . $area, $component, $bodyvars);
        }

        $view = get_string('mentionview:comment', 'totara_core');
        if ($manager->string_exists('mentionview:' . $area, $component)) {
            $view = get_string('mentionview:' . $area, $component);
        }

        $template = mention_message::create($data->content, $description, $view, $url);
        $messagebody = $OUTPUT->render($template);

        /** @var mention_repository $repo */
        $repo = mention::repository();

        foreach ($data->userids as $userid) {
            $recipient = $DB->get_record('user', ['id' => $userid], '*', MUST_EXIST);

            // Need to check whether this recipient has received the email message before.
            // So that we can decide whether to resend the email or not.

            $mention = $repo->find_mention($userid, $data->instanceid, $component, $data->area);
            if (null !== $mention) {
                // Mention is already existing in the system.
                continue;
            }
            $message = new message();

            // Sending message is a part of totara core, not the one from content item.
            $message->component = 'totara_core';
            $message->name = 'mention';
            $message->userfrom = $actor;
            $message->userto = $recipient;
            $message->subject = $messagetitle;
            $message->fullmessage =  html_to_text($messagebody);
            $message->fullmessageformat = FORMAT_HTML;
            $message->fullmessagehtml = $messagebody;
            $message->contexturl = $url;
            $message->courseid = $courseid;
            message_send($message);

            $mention = new mention();
            $mention->userid = $userid;
            $mention->component = $component;
            $mention->area = $data->area;
            $mention->instanceid = $data->instanceid;

            $mention->save();
        }
    }
}