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
use core\entities\user as user_entity;
use core\message\message;
use core_user;
use dml_exception;
use mod_perform\models\activity\notification as notification_model;
use mod_perform\models\activity\notification_message as notification_message_model;
use mod_perform\models\activity\notification_recipient as notification_recipient_model;
use stdClass;
use totara_core\relationship\relationship as relationship_model;

/**
 * The dealer class.
 */
class dealer {
    /** @var notification_recipient_model[] */
    private $recipients;

    /** @var composer */
    private $composer;

    /** @var integer */
    private $course_id;

    /**
     * Constructor. *Do not instantiate this class directly. Use the factory class.*
     *
     * @param notification_model $notification
     */
    public function __construct(notification_model $notification) {
        $this->recipients = notification_recipient_model::load_by_notification($notification, true);
        $this->composer = factory::create_composer($notification->class_key);
        $this->course_id = $notification->activity->course;
    }

    /**
     * Return true if the notification has any recipients.
     *
     * @return boolean
     */
    public function has_recipients(): bool {
        return count($this->recipients) > 0;
    }

    /**
     * Post a notification.
     *
     * @param user_entity|stdClass $user
     * @param relationship_model $relationship
     * @return boolean
     */

    public function post($user, relationship_model $relationship): bool {
        // FIXME: Notifications for manual relationships do not work at the moment
        //        in TL-26488 the way this works will be refactored
        if ($relationship->type == \totara_core\entities\relationship::TYPE_MANUAL) {
            debugging('manual relationships do not work at the moment', DEBUG_DEVELOPER);
            return false;
        }
        if ($user instanceof user_entity) {
            $user = $user->get_record();
        }
        $recipient = $this->resolve_recipient($relationship);
        if (!$recipient) {
            return false;
        }
        if (!$this->composer->set_relationship($relationship)) {
            return false;
        }
        $is_reminder = $this->composer->is_reminder();
        $message = $this->composer->compose($relationship);
        $this->send_notification(core_user::NOREPLY_USER, $user, $message, $is_reminder);
        $this->save_history($recipient, time());
        return true;
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
     * @param message $message
     * @param bool $is_reminder
     * @return void
     */
    private function send_notification($from, $to, message $message, bool $is_reminder): void {
        $from = self::resolve_user($from);
        $to = self::resolve_user($to);

        $eventdata = clone $message;
        $eventdata->courseid         = $this->course_id;
        $eventdata->modulename       = 'perform';
        $eventdata->userfrom         = $from;
        $eventdata->userto           = $to;

        $eventdata->name = $is_reminder ? 'activity_reminder' : 'activity_notification';
        $eventdata->component       = 'mod_perform';
        $eventdata->notification    = 1;

        message_send($eventdata);
    }

    /**
     * @param relationship_model $relationship
     * @return notification_recipient_model|null
     */
    private function resolve_recipient(relationship_model $relationship): ?notification_recipient_model {
        foreach ($this->recipients as $recipient) {
            if ($recipient->get_relationship_id() == $relationship->id) {
                return $recipient;
            }
        }
        return null;
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
