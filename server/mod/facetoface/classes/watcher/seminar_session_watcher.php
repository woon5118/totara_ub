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

use mod_facetoface\facilitator_user;
use mod_facetoface\hook\event_is_being_cancelled;
use mod_facetoface\hook\resources_are_being_updated;
use mod_facetoface\hook\service\seminar_session_resource;
use mod_facetoface\hook\service\seminar_session_resource_dynamic;
use mod_facetoface\hook\sessions_are_being_updated;
use mod_facetoface\notice_sender;
use mod_facetoface\seminar_event;

final class seminar_session_watcher {
    /**
     * Get an array of facilitators attached to the sessions.
     *
     * @param seminar_event $seminarevent
     * @param seminar_session_resource[] $sessions
     * @return seminar_event[] facilitator_userid => seminar_event_with_filtered_sessions
     */
    private static function get_recipients(seminar_event $seminarevent, array $sessions): array {
        $f2fevent = $seminarevent->to_record();
        $time = time();
        $recipients = [];
        foreach ($sessions as $sess) {
            if ($sess->get_session()->is_over($time)) {
                continue;
            }
            if ($sess->has_facilitators()) {
                $date = $sess->get_session()->to_record();
                $facs = $sess->get_facilitator_list(true);
                foreach ($facs as $fac) {
                    /** @var facilitator_user $fac */
                    if (!isset($recipients[$fac->get_userid()])) {
                        $recipients[$fac->get_userid()] = [];
                    }
                    $recipients[$fac->get_userid()][] = $date;
                }
            }
        }
        return array_map(function ($dates) use ($f2fevent) {
            $f2fevent->sessiondates = $dates;
            $event = new seminar_event();
            $event->from_record_with_dates($f2fevent);
            return $event;
        }, $recipients);
    }

    /**
     * Compute the difference of sessions in each seminar event.
     *
     * @param seminar_event[] $source array of userid => seminar_event
     * @param seminar_event[] $destination array of userid => seminar_event
     * @return seminar_event[] array of userid => seminar_event_with_different_sessions
     */
    private static function diff_recipients(array $source, array $destination): array {
        $result = [];
        foreach ($source as $userid => $src_event) {
            $dst_event = $destination[$userid] ?? null;
            if ($dst_event) {
                $src_sessiondates = $src_event->get_sessions()->to_records();
                $dst_sessiondates = $dst_event->get_sessions()->to_records();
                $record = $src_event->to_record();
                // Two elements of each array with the same key are identical.
                $record->sessiondates = array_diff_key($src_sessiondates, $dst_sessiondates);
                if (!empty($record->sessiondates)) {
                    $diff_event = new seminar_event();
                    $diff_event->from_record_with_dates($record);
                    $result[$userid] = $diff_event;
                }
            } else {
                $result[$userid] = $src_event;
            }
        }
        return $result;
    }

    /**
     * @param sessions_are_being_updated $hook
     */
    public static function sessions_updated(sessions_are_being_updated $hook) {
        // Notify time updated.
        $olddates = $hook->seminarevent->get_sessions()->to_records();
        $recipients = self::get_recipients($hook->seminarevent, $hook->sessionstobeupdated);
        foreach ($recipients as $recipient => $seminareventfiltered) {
            notice_sender::session_facilitator_datetime_changed($recipient, $seminareventfiltered, $olddates);
        }

        // Notify cancellation.
        $recipients = self::get_recipients($hook->seminarevent, $hook->sessionstobedeleted);
        foreach ($recipients as $recipient => $seminareventfiltered) {
            notice_sender::session_facilitator_cancellation($recipient, $seminareventfiltered);
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
        $dates = array_map(function ($sess) {
            return seminar_session_resource_dynamic::from_session($sess);
        }, iterator_to_array($hook->seminarevent->get_sessions()));
        $recipients = self::get_recipients($hook->seminarevent, $dates);
        foreach ($recipients as $recipient => $seminareventfiltered) {
            notice_sender::session_facilitator_cancellation($recipient, $seminareventfiltered);
        }
    }

    /**
     * @param resources_are_being_updated $hook
     */
    public static function resources_updated(resources_are_being_updated $hook) {
        $seminarevent = $hook->seminarevent;
        if ($seminarevent->is_over()) {
            return;
        }
        $old_facilitators = self::get_recipients($seminarevent, array_map(function (seminar_session_resource $session) {
            return seminar_session_resource_dynamic::from_session($session->get_session());
        }, $hook->sessions));
        $new_facilitators = self::get_recipients($seminarevent, $hook->sessions);

        // Send an assignment notification.
        $assigned_recipients = self::diff_recipients($new_facilitators, $old_facilitators);
        foreach ($assigned_recipients as $recipient => $seminareventfiltered) {
            notice_sender::session_facilitator_assigned($recipient, $seminareventfiltered);
        }

        // Send an unassignment notification.
        $unassigned_recipients = self::diff_recipients($old_facilitators, $new_facilitators);
        foreach ($unassigned_recipients as $recipient => $seminareventfiltered) {
            notice_sender::session_facilitator_unassigned($recipient, $seminareventfiltered);
        }
    }

    /**
     * @param seminar_event[] $events
     * @return array
     */
    private static function format_event_recipients(array $events): array {
        $result = [];
        foreach ($events as $userid => $event) {
            /** @var \mod_facetoface\seminar_event $event */
            $username = \core_user::get_user($userid)->username;
            $sessions = [];
            foreach ($event->get_sessions() as $s) {
                /** @var \mod_facetoface\seminar_session $s */
                $sessions[$s->get_id()] = [
                    'start' => userdate($s->get_timestart(), '%d %B %Y %I:%M:%S %p'),
                    'finish' => userdate($s->get_timefinish(), '%d %B %Y %I:%M:%S %p'),
                ];
            }
            $result[$username] = [
                'name' => $event->get_details(),
                'sessions' => $sessions,
            ];
        }
        return $result;
    }
}
