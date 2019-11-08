<?php
/*
* This file is part of Totara Learn
*
* Copyright (C) 2018 onwards Totara Learning Solutions LTD
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
* @author Oleg Demeshev <oleg.demeshev@totaralearning.com>
* @package mod_facetoface
*/

namespace mod_facetoface;

use context_course;

/**
 * Additional room functionality.
 */
final class room_helper {

    /**
     * The 15 minutes is time prior to the start of a session
     */
    const JOIN_NOW_TIME = MINSECS * 15;

    /**
     * Room data
     *
     * @param \stdClass $data to be saved includes:
     *      - int {facetoface_room}.id
     *      - string {facetoface_room}.name
     *      - int {facetoface_room}.capacity
     *      - string {facetoface_room}.url
     *      - int {facetoface_room}.allowconflicts
     *      - string {facetoface_room}.description
     *      - bool {facetoface_room}.custom (optional)
     *      - int {facetoface_room}.hidden
     * @return room
     */
    public static function save(\stdClass $data): room {
        global $TEXTAREA_OPTIONS;

        $custom = $data->custom ?? false; // $data->custom is not always passed
        if ($data->id) {
            $room = new room($data->id);
            if (!$custom && $room->get_custom()) {
                $room->publish();
            } else {
                // NOTE: Do nothing if the room is already published because we can't unpublish it
            }
        } else {
            if ($custom) {
                $room = room::create_custom_room();
            } else {
                $room = new room();
            }
        }
        $room->set_name($data->name);
        $room->set_allowconflicts($data->allowconflicts);
        $room->set_capacity($data->roomcapacity);
        $room->set_url($data->url);

        if (!$room->exists()) {
            $room->save();
        }

        // Export data to store in customfields and description.
        $data->id = $room->get_id();
        customfield_save_data($data, 'facetofaceroom', 'facetoface_room');

        // Update description.
        $data = file_postupdate_standard_editor(
            $data,
            'description',
            $TEXTAREA_OPTIONS,
            $TEXTAREA_OPTIONS['context'],
            'mod_facetoface',
            'room',
            $room->get_id()
        );
        $room->set_description($data->description);
        $room->save();
        // Return new/updated asset.
        return $room;
    }

    /**
     * Sync the list of rooms for a given seminar event date
     * @param integer $date Seminar date Id
     * @param array $rooms List of room Ids
     * @return bool
     */
    public static function sync(int $date, array $rooms = []): bool {
        global $DB;

        if (empty($rooms)) {
            return $DB->delete_records('facetoface_room_dates', ['sessionsdateid' => $date]);
        }

        $oldrooms = $DB->get_fieldset_select('facetoface_room_dates', 'roomid', 'sessionsdateid = :date_id', ['date_id' => $date]);

        // WIPE THEM AND RECREATE if certain conditions have been met.
        if ((count($rooms) == count($oldrooms)) && empty(array_diff($rooms, $oldrooms))) {
            return true;
        }

        $res = $DB->delete_records('facetoface_room_dates', ['sessionsdateid' => $date]);

        foreach ($rooms as $room) {
            $res &= $DB->insert_record('facetoface_room_dates', (object) [
                'sessionsdateid' => $date,
                'roomid' => intval($room)
            ],false);
        }

        return !!$res;
    }

    /**
     * Get rooms for specific session.
     * @param int $sessionid
     * @return string
     */
    public static function get_session_roomids(int $sessionid): string {
        global $DB;

        $roomid = $DB->sql_group_concat($DB->sql_cast_2char('frd.roomid'), ',');
        $sql = "SELECT {$roomid} AS roomids
                  FROM {facetoface_sessions_dates} fsd
             LEFT JOIN {facetoface_room_dates} frd ON frd.sessionsdateid = fsd.id
                 WHERE fsd.id = :id";
        $ret = $DB->get_field_sql($sql, array('id' => $sessionid));
        return $ret ? $ret : '';
    }

    /**
     * Can we display 'Join now' link button?
     * @param seminar_event $seminarevent
     * @param seminar_session $session
     * @param signup|null $signup signup instance or null to use the signup of the current user
     * @param int $time time stamp or 0 to use the current time
     * @return bool
     */
    public static function show_joinnow(seminar_event $seminarevent, seminar_session $session, ?signup $signup = null, int $time = 0): bool {
        global $USER;
        if (is_null($signup)) {
            $signup = signup::create($USER->id, $seminarevent);
        }
        return self::has_time_come($session, $time) && (signup_helper::is_booked($signup, false) || self::seek_event_role($seminarevent));
    }

    /**
     * room::show_joinnow(html button)::has_time_come(to display 'Join now' button)?
     * Has time come to display the 'Join now' link button
     * from 15 minutes prior to the session start time, until the session end time
     * @param seminar_session $session
     * @param int $time time stamp or 0 to use the current time
     * @return bool
     */
    private static function has_time_come(seminar_session $session, int $time = 0): bool {
        if ($time <= 0) {
            $time = time();
        }
        if (($session->get_timestart() - self::JOIN_NOW_TIME) < $time && $time < $session->get_timefinish()) {
            return true;
        }
        return false;
    }

    /**
     * Seek any event role/s in current seminar event which may assigned
     * @param seminar_event $seminarevent
     * @return bool
     */
    private static function seek_event_role(seminar_event $seminarevent): bool {
        global $USER;
        // Private cache.
        static $userlist = [];
        if (isset($userlist[$seminarevent->get_id()][$USER->id])) {
            return (bool)$userlist[$seminarevent->get_id()][$USER->id];
        }

        $anyrole = false;
        $context = context_course::instance($seminarevent->get_seminar()->get_course());
        $userroles = get_user_roles($context, $USER->id, false);
        $eventroles = trainer_helper::get_trainer_roles($context);
        foreach ($eventroles as $i => $eventrole) {
            foreach ($userroles as $j => $userrole) {
                if ((int)$userrole->roleid == (int)$eventrole->id) {
                    $anyrole = true;
                    // Exit second foreach
                    break 1;
                }
            }
            if ($anyrole) {
                // Exit first foreach
                break;
            }
        }
        $userlist[$seminarevent->get_id()][$USER->id] = $anyrole;
        return (bool)$userlist[$seminarevent->get_id()][$USER->id];
    }
}