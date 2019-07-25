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

/**
 * Additional room functionality.
 */
final class room_helper {

    /**
     * Room data
     *
     * @param \stdClass $data to be saved includes:
     *      - int {facetoface_room}.id
     *      - string {facetoface_room}.name
     *      - int {facetoface_room}.capacity
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
}