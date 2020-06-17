<?php
/*
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
 * @author Tatsuhiro Kirihara <tatsuhiro.kirihara@totaralearning.com>
 * @package mod_facetoface
 */

namespace mod_facetoface\watcher;

use mod_facetoface\facilitator_list;
use mod_facetoface\facilitator_user;
use mod_facetoface\hook\event_is_being_cancelled;
use mod_facetoface\hook\resources_are_being_updated;
use mod_facetoface\hook\service\seminar_session_resource;
use mod_facetoface\hook\sessions_are_being_updated;
use mod_facetoface\notice_sender;

final class seminar_session_watcher {
    /**
     * Get an array of facilitators attached to the sessions.
     *
     * @param seminar_session_resource[] $sessions
     * @return integer[]
     */
    private static function get_recipients(array $sessions): array {
        $time = time();
        $recipients = [];
        foreach ($sessions as $sess) {
            if ($sess->get_session()->is_over($time)) {
                continue;
            }
            if ($sess->has_facilitators()) {
                $facs = $sess->get_facilitator_list(true);
                foreach ($facs as $fac) {
                    /** @var facilitator_user $fac */
                    $recipients[] = $fac->get_userid();
                }
            }
        }
        return array_unique($recipients);
    }

    /**
     * @param sessions_are_being_updated $hook
     */
    public static function sessions_updated(sessions_are_being_updated $hook) {
        // Notify time updated.
        $olddates = $hook->seminarevent->get_sessions()->to_records();
        $recipients = self::get_recipients($hook->sessionstobeupdated);
        foreach ($recipients as $recipient) {
            notice_sender::session_facilitator_datetime_changed($recipient, $hook->seminarevent, $olddates);
        }

        // Notify cancellation.
        $recipients = self::get_recipients($hook->sessionstobedeleted);
        foreach ($recipients as $recipient) {
            notice_sender::session_facilitator_cancellation($recipient, $hook->seminarevent);
        }
    }

    /**
     * @param event_is_being_cancelled $hook
     */
    public static function event_cancelled(event_is_being_cancelled $hook) {
        if ($hook->seminarevent->is_over(time())) {
            return;
        }
        $recipients = [];
        $facs = facilitator_list::from_seminarevent($hook->seminarevent->get_id(), true);
        foreach ($facs as $fac) {
            /** @var facilitator_user $fac */
            $recipients[] = $fac->get_userid();
        }
        $recipients = array_unique($recipients);
        foreach ($recipients as $recipient) {
            notice_sender::session_facilitator_cancellation($recipient, $hook->seminarevent);
        }
    }

    /**
     * @param resources_are_being_updated $hook
     */
    public static function resources_updated(resources_are_being_updated $hook) {
        if ($hook->session->get_session()->is_over(time())) {
            return;
        }
        $seminarevent = $hook->session->get_event();
        $old_facilitators = array_unique(array_map(function (facilitator_user $fac) {
            return $fac->get_userid();
        }, iterator_to_array(facilitator_list::from_session($hook->session->get_session_id(), true), false)));
        $new_facilitators = self::get_recipients([$hook->session]);

        // Send an assignment notification.
        $assigned_recipients = array_diff($new_facilitators, $old_facilitators);
        foreach ($assigned_recipients as $recipient) {
            notice_sender::session_facilitator_assigned($recipient, $seminarevent);
        }

        // Send an unassignment notification.
        $unassigned_recipients = array_diff($old_facilitators, $new_facilitators);
        foreach ($unassigned_recipients as $recipient) {
            notice_sender::session_facilitator_unassigned($recipient, $seminarevent);
        }
    }
}
