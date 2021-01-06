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
* @author Oleg Demeshev <oleg.demeshev@totaralearning.com>
* @package mod_facetoface
*/

namespace mod_facetoface;

use core\orm\collection;
use mod_facetoface\traits\crud_mapper;
use core\orm\query\builder;

defined('MOODLE_INTERNAL') || die();

class room_virtualmeeting implements seminar_iterator_item {

    use crud_mapper;

    /**
     * Virtual meeting identifier options
     */
    const VIRTUAL_MEETING_NONE = '@none';
    const VIRTUAL_MEETING_INTERNAL = '@internal';

    /**
     * @var int {facetoface_room_virtualmeeting}.id
     */
    private $id = 0;
    /**
     * @var int|null {facetoface_room_virtualmeeting}.status
     */
    private $status = null;
    /**
     * @var int {facetoface_room_virtualmeeting}.roomid
     */
    private $roomid = 0;
    /**
     * @var string {facetoface_room_virtualmeeting}.plugin
     */
    private $plugin = '';
    /**
     * @var string {facetoface_room_virtualmeeting}.options
     */
    private $options = '';
    /**
     * @var int {facetoface_room_virtualmeeting}.userid
     */
    private $userid = 0;
    /**
     * @var string facetoface_room_virtualmeeting table name
     */
    const DBTABLE = 'facetoface_room_virtualmeeting';

    /**
     * Seminar room virtual meeting constructor
     * @param int $id {facetoface_room_virtualmeeting}.id If 0 - new Seminar Room virtual meeting will be created
     */
    public function __construct(int $id = 0) {
        if ((int)$id > 0) {
            $this->id = $id;
            $this->load();
        }
    }

    /**
     * Loads a room virtual meeting
     * @return room_virtualmeeting
     */
    public function load(): room_virtualmeeting {
        return $this->crud_load();
    }

    /**
     * Create/update {facetoface_room_virtualmeeting}.record
     */
    public function save() {
        $this->crud_save();
    }

    /**
     * Map data object to class instance.
     * @param \stdClass $object
     * @return room_virtualmeeting
     */
    public function from_record(\stdClass $object): room_virtualmeeting {
        return $this->map_object($object);
    }

    /**
     * Given a roomid, loads the associated room_virtualmeeting record and returns as an object
     *
     * @param int $roomid
     * @return room_virtualmeeting
     */
    public static function from_roomid(int $roomid): room_virtualmeeting {
        $record = builder::table(self::DBTABLE)
            ->where('roomid', '=', $roomid)
            ->one(false);
        if ($record) {
            return new room_virtualmeeting($record->id);
        } else {
            return new room_virtualmeeting(0);
        }
    }

    /**
     * Delete {facetoface_room_virtualmeeting}.record where id from database
     */
    public function delete(): void {
        global $DB;
        $DB->delete_records(self::DBTABLE, ['id' => $this->id]);
        // Re-load instance with default values.
        $this->map_object((object)get_object_vars(new self()));
    }

    /**
     * Delete {facetoface_room_virtualmeeting}.record where roomid from database
     * @param int $roomid
     */
    public static function delete_by_roomid(int $roomid): void {
        global $DB;
        $DB->delete_records(self::DBTABLE, ['roomid' => $roomid]);
    }

    /**
     * Check whether the virtual meeting exists yet or not.
     * If the virtual meeting has been saved into the database the $id field should be non-zero
     * @return bool - true if the virtual meeting has an $id, false if it hasn't
     */
    public function exists(): bool {
        return (bool)$this->get_id();
    }

    /**
     * @return int
     */
    public function get_id(): int {
        return (int)$this->id;
    }

    /**
     * @return int
     */
    public function get_roomid(): int {
        return (int)$this->roomid;
    }

    /**
     * @param int $roomid
     * @return room_virtualmeeting
     */
    public function set_roomid(int $roomid): room_virtualmeeting {
        $this->roomid = $roomid;
        return $this;
    }

    /**
     * @return string
     */
    public function get_plugin(): string {
        return (string)$this->plugin;
    }

    /**
     * @param string $plugin
     * @return room_virtualmeeting
     */
    public function set_plugin(string $plugin): room_virtualmeeting {
        $this->plugin = $plugin;
        return $this;
    }

    /**
     * @return int
     */
    public function get_userid(): int {
        return (int)$this->userid;
    }

    /**
     * @param int $userid
     * @return room_virtualmeeting
     */
    public function set_userid(int $userid): room_virtualmeeting {
        $this->userid = $userid;
        return $this;
    }

    /**
     * Get room_virtualmeeting instance by room id
     * @param room $room
     * @return room_virtualmeeting
     */
    public static function get_virtual_meeting(room $room): room_virtualmeeting {

        $records = builder::table('facetoface_room_virtualmeeting', 'frvm')
            ->join(['facetoface_room', 'fr'], 'roomid', 'id')
            ->where('frvm.roomid', $room->get_id())
            ->fetch();
        $virtual_meeting = new room_virtualmeeting();
        if ($records) {
            return $virtual_meeting->from_record(array_shift($records));
        }
        return $virtual_meeting;
    }

    /**
     * Take users who created or edited room with virtualmeeting
     * @param int $eventid
     * @return collection
     */
    public static function get_virtualmeeting_creators_in_all_sessions(int $eventid): collection {

        $users = builder::table('user', 'u')
            ->join(['facetoface_room_virtualmeeting', 'frvm'], 'u.id', '=', 'frvm.userid')
            ->join(['facetoface_room_dates', 'frd'], 'frvm.roomid', '=', 'frd.roomid')
            ->join(['facetoface_sessions_dates', 'sd'], 'frd.sessionsdateid', '=', 'sd.id')
            ->where('u.deleted', 0)
            ->where('u.suspended', 0)
            ->where('sd.sessionid', $eventid)
            ->select_raw('distinct u.id, u.*')
            ->get();

        return $users;
    }
}
