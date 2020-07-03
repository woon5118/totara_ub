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
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Tatsuhiro Kirihara <tatsuhiro.kirihara@totaralearning.com>
 * @package mod_perform
 */

namespace mod_perform\notification;

use coding_exception;
use core\message\message;
use core\orm\collection;
use core_user;
use dml_exception;
use mod_perform\models\activity\activity as activity_model;
use mod_perform\models\activity\notification_message as notification_message_model;
use mod_perform\models\activity\notification_recipient as notification_recipient_model;
use stdClass;

/**
 * The dealer class.
 */
class dealer {
    /** @var notification_recipient_model[] */
    private $recipients;

    /** @var composer */
    private $composer;

    /** @var integer */
    private $user_id;

    /** @var integer|null */
    private $job_assignment_id;

    /** @var integer */
    private $course_id;

    /**
     * Constructor. *Do not instantiate this class directly. Use the factory class.*
     *
     * @param activity_model $activity
     * @param notification_recipient_model[]|collection $notification_recipients
     * @param composer $composer
     * @param integer $subject_user_id
     * @param integer|null $job_assignment_id
     */
    public function __construct(activity_model $activity, $notification_recipients, composer $composer, int $subject_user_id, ?int $job_assignment_id) {
        if ($notification_recipients instanceof collection) {
            $notification_recipients = iterator_to_array($notification_recipients);
        }
        foreach ($notification_recipients as $i => $recipient) {
            if (!($recipient instanceof notification_recipient_model)) {
                throw new coding_exception("recipients[{$i}] is not a notification_recipient model");
            }
        }
        $this->recipients = $notification_recipients;
        $this->composer = $composer;
        $this->course_id = $activity->course;
        $this->user_id = $subject_user_id;
        $this->job_assignment_id = $job_assignment_id;
    }

    /**
     * @return integer
     */
    public function get_user_id(): int {
        return $this->user_id;
    }

    /**
     * @return integer|null
     */
    public function get_job_assignment_id(): ?int {
        return $this->job_assignment_id;
    }

    /**
     * Post a notification.
     */
    public function post(): void {
        foreach ($this->recipients as $recipient) {
            $relationship = $recipient->relationship;
            $users = $relationship->get_users(['user_id' => $this->user_id, 'job_assignment_id' => $this->job_assignment_id]);
            if (empty($users)) {
                continue;
            }
            if (!$this->composer->set_relationship($relationship)) {
                continue;
            }
            $message = $this->composer->compose($relationship);
            foreach ($users as $user) {
                $this->send_notification(core_user::NOREPLY_USER, $user, $message);
            }
            $this->save_history($recipient, time());
        }
    }

    /**
     * Create a historical record.
     *
     * @param notification_recipient_model $recipient
     */
    private function save_history(notification_recipient_model $recipient): void {
        notification_message_model::create($recipient, time());
    }

    /**
     * Send a notification.
     *
     * @param stdClass|integer|string $from user object or user id or NOREPLY_USER or SUPPORT_USER
     * @param stdClass|integer|string $to user object or user id or NOREPLY_USER or SUPPORT_USER
     * @param string $event_type
     * @return void
     */
    private function send_notification($from, $to, message $message): void {
        $from = self::resolve_user($from);
        $to = self::resolve_user($to);

        $eventdata = clone $message;
        $eventdata->courseid         = $this->course_id;
        $eventdata->modulename       = 'perform';
        $eventdata->userfrom         = $from;
        $eventdata->userto           = $to;

        $eventdata->name            = 'activity_notification';
        $eventdata->component       = 'mod_perform';
        $eventdata->notification    = 1;

        message_send($eventdata);
    }

    /**
     * @param stdClass|integer|string $user
     * @return stdClass
     * @throws coding_exception
     * @throws dml_exception
     */
    private static function resolve_user($user): stdClass {
        if (is_object($user)) {
            return $user;
        }
        if (is_number($user)) {
            return core_user::get_user($user, '*', MUST_EXIST);
        }
        throw new coding_exception('invalid user passed');
    }
}
