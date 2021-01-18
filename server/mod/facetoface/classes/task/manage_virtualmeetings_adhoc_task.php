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
 * @author Chris Snyder <chris.snyder@totaralearning.com>
 * @package mod_facetoface
 */

namespace mod_facetoface\task;

defined('MOODLE_INTERNAL') || die();

use coding_exception;
use DateTime;
use core\entity\user;
use core\plugininfo\virtualmeeting as virtualmeeting_plugininfo;
use core\task\adhoc_task;
use totara_core\http\exception\request_exception;
use totara_core\virtualmeeting\exception\auth_exception;
use totara_core\virtualmeeting\virtual_meeting as virtualmeeting_model;
use \facetoface_notification;
use mod_facetoface\room;
use mod_facetoface\room_list;
use mod_facetoface\room_virtualmeeting;
use mod_facetoface\room_dates_virtualmeeting;
use mod_facetoface\seminar;
use mod_facetoface\seminar_event;
use mod_facetoface\seminar_session;

/**
 * This class manages the creation, update, and deletion of virtualmeetings associated with seminar rooms.
 */
class manage_virtualmeetings_adhoc_task extends adhoc_task {

    /**
     * Create adhoc task for managing virutalmeeting rooms in a seminar event
     * @param int $seminar_event_id
     * @return manage_virtualmeetings_adhoc_task
     */
    public static function create_from_seminar_event_id(int $seminar_event_id): manage_virtualmeetings_adhoc_task {
        global $USER;
        if (empty($seminar_event_id)) {
            throw new coding_exception('No seminar event id set.');
        }
        $task = new self();
        $task->set_component('mod_facetoface');
        $task->set_custom_data(['seminar_event_id' => $seminar_event_id, 'user_id' => $USER->id]);

        return $task;
    }

    /**
     * @inheritDoc
     */
    public function execute() {
        $custom_data = $this->get_custom_data();

        if (empty($custom_data->seminar_event_id)) {
            throw new coding_exception('No seminar event id set.');
        }

        // Load seminarevent and seminar activity
        try {
            $seminarevent = new seminar_event($custom_data->seminar_event_id);
        } catch (\dml_missing_record_exception $e) {
            // This seminar event has been deleted, leave everything for the cleanup task.
            return;
        }
        $seminar = new seminar($seminarevent->get_facetoface());

        // Track failures
        $failures = [];

        // Get the list of virtualmeeting rooms in this event.
        $virtualmeeting_rooms = $this->virtualmeeting_rooms_in_seminarevent($custom_data->seminar_event_id);

        // Ignore virtualmeeting rooms where plugin is not available
        foreach ($virtualmeeting_rooms as $room) {
            if (empty($custom_data->user_id)) {
                $failures[] = 'No user id set. Perhaps a left over task from a previous version.';
                break;
            }
            $room_virtualmeeting = room_virtualmeeting::from_roomid($room->get_id());
            if ($custom_data->user_id != $room_virtualmeeting->get_userid()) {
                continue;
            }
            // Set operative $user
            $user = new user($room_virtualmeeting->get_userid());
            try {
                // Use of an unavailable or not-configured plugin IS a failure.
                $plugininfo = virtualmeeting_plugininfo::load($room_virtualmeeting->get_plugin());
                if (!$plugininfo->is_available()) {
                    throw new \Exception("virtualmeeting plugin is not configured.");
                }
                // Create virtualmeetings for sessions that do not have matching room_dates_virtualmeeting records
                $room_dates = $this->room_dates_with_virtualmeetings($room, $custom_data->seminar_event_id);

                // Process
                foreach ($room_dates as $room_date) {
                    $session = new seminar_session($room_date->sessionsdateid);
                    // Create? or Update?
                    if (empty($room_date->roomdatevirtualmeetingid)) {
                        $meeting = virtualmeeting_model::create(
                            $room_virtualmeeting->get_plugin(),
                            $user,
                            $seminar->get_name(),
                            DateTime::createFromFormat('U', $session->get_timestart()),
                            DateTime::createFromFormat('U', $session->get_timefinish())
                        );
                        $room_dates_virtualmeeting = new room_dates_virtualmeeting();
                        $room_dates_virtualmeeting->set_roomdateid($room_date->id);
                        $room_dates_virtualmeeting->set_virtualmeetingid($meeting->get_id());
                        $room_dates_virtualmeeting->save();
                    } else {
                        $meeting = virtualmeeting_model::load_by_id($room_date->virtualmeetingid);
                        // Still same user?
                        if ($meeting->userid != $user->id) {
                            throw new auth_exception("Unable to use a virtualmeeting room which does not belong to you.");
                        }
                        // Still same plugin?
                        if ($meeting->plugin == $room_virtualmeeting->get_plugin()) {
                            $meeting->update($seminar->get_name(), DateTime::createFromFormat('U', $session->get_timestart()), DateTime::createFromFormat('U', $session->get_timefinish()));
                        } else {
                            // Different plugin, create a new virtualmeeting
                            throw new \Exception("Cannot switch a virtualmeeting plugin.");
                        }
                    }
                }
            } catch (auth_exception $e) {
                $failures[] = "Room {$room->get_name()} {$room_virtualmeeting->get_plugin()} authorisation problem: {$e->getMessage()}";
            } catch (request_exception $e) {
                $failures[] = "Room {$room->get_name()} {$room_virtualmeeting->get_plugin()} request failed: {$e->getMessage()}";
            } catch (\Exception $e) {
                $failures[] = "Room {$room->get_name()} {$room_virtualmeeting->get_plugin()} error: {$e->getMessage()}";
            }
        }

        // Send notification of failures
        if (!empty($failures)) {
            // Write the exact failures to the debugging log
            if (!defined('BEHAT_SITE_RUNNING') && (!defined('PHPUNIT_TEST') || !PHPUNIT_TEST)) {
                $failure_report = "Virtual room creation failures in seminar {$seminar->get_name()} event {$seminarevent->get_id()}:\n";
                $failure_report .= implode("\n", $failures);
                debugging($failure_report, DEBUG_DEVELOPER);
            }
            $session = ['facetoface' => $seminar->get_id()];
            $notification = new facetoface_notification($session, false);
            $notification->send_notification_virtual_meeting_creation_failure($seminarevent);
        }
    }

    private function virtualmeeting_rooms_in_seminarevent(int $seminar_event_id): room_list {
        $sql = "SELECT DISTINCT fr.*
                  FROM {facetoface_room} fr
            INNER JOIN {facetoface_room_virtualmeeting} frvm ON frvm.roomid = fr.id
            INNER JOIN {facetoface_room_dates} frd ON frd.roomid = fr.id
            INNER JOIN {facetoface_sessions_dates} fsd ON fsd.id = frd.sessionsdateid
                 WHERE fsd.sessionid = :seminareventid AND fr.custom = 1
              ORDER BY fr.name ASC, fr.id ASC";

        return new room_list($sql, ['seminareventid' => $seminar_event_id]);
    }

    private function room_dates_with_virtualmeetings(room $room, int $seminar_event_id): array {
        global $DB;

        $sql = "SELECT frd.*, frdvm.id AS roomdatevirtualmeetingid, frdvm.virtualmeetingid
                  FROM {facetoface_room_dates} frd
             LEFT JOIN {facetoface_room_dates_virtualmeeting} frdvm ON frdvm.roomdateid = frd.id
            INNER JOIN {facetoface_room} fr ON fr.id = frd.roomid
            INNER JOIN {facetoface_sessions_dates} fsd ON fsd.id = frd.sessionsdateid
                 WHERE fsd.sessionid = :seminareventid AND fr.id = :roomid
              ORDER BY frd.id ASC";
        $params = ['seminareventid' => $seminar_event_id, 'roomid' => $room->get_id()];

        return $DB->get_records_sql($sql, $params);
    }
}
