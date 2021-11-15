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
* @author Chris Snyder <chris.snyder@totaralearning.com>
* @package mod_facetoface
*/

namespace mod_facetoface;

use core\orm\query\builder;
use core\orm\query\exceptions\record_not_found_exception;
use mod_facetoface\traits\crud_mapper;
use totara_core\virtualmeeting\virtual_meeting as virtual_meeting_model;

defined('MOODLE_INTERNAL') || die();

class room_dates_virtualmeeting implements seminar_iterator_item {

    use crud_mapper;

    /**
     * @var int {facetoface_room_dates_virtualmeeting}.id
     */
    private $id = 0;
    /**
     * @var int|null {facetoface_room_dates_virtualmeeting}.status
     */
    private $status = null;
    /**
     * @var int {facetoface_room_dates_virtualmeeting}.roomdateid
     */
    private $roomdateid = 0;
    /**
     * @var int {facetoface_room_dates_virtualmeeting}.virtualmeetingid
     */
    private $virtualmeetingid = 0;
    /**
     * @var string facetoface_room_virtualmeeting table name
     */
    const DBTABLE = 'facetoface_room_dates_virtualmeeting';

    /**
     * Seminar session room instance virtual meeting constructor
     * @param int $id {facetoface_room_dates_virtualmeeting}.id If 0 - new Seminar Room session virtual meeting will be created
     */
    public function __construct(int $id = 0) {
        if ((int)$id > 0) {
            $this->id = $id;
            $this->load();
        }
    }

    /**
     * Loads a session room instance virtual meeting
     * @return room_dates_virtualmeeting
     */
    public function load(): room_dates_virtualmeeting {
        return $this->crud_load();
    }

    /**
     * Create/update {facetoface_room_dates_virtualmeeting}.record
     */
    public function save() {
        $this->crud_save();
    }

    /**
     * Map data object to class instance.
     * @param \stdClass $object
     * @return room_dates_virtualmeeting
     */
    public function from_record(\stdClass $object): room_dates_virtualmeeting {
        return $this->map_object($object);
    }

    /**
     * Delete {facetoface_room_dates_virtualmeeting}.record where id from database
     */
    public function delete(): void {
        global $DB;
        $DB->delete_records(self::DBTABLE, ['id' => $this->id]);
        // Re-load instance with default values.
        $this->map_object((object)get_object_vars(new self()));
    }

    /**
     * Delete {facetoface_room_dates_virtualmeeting}.record where roomdateid from database
     * @param int $roomdateid
     */
    public static function delete_by_roomdateid(int $roomdateid): void {
        global $DB;
        $DB->delete_records(self::DBTABLE, ['roomdateid' => $roomdateid]);
    }

    /**
     * Delete {facetoface_room_dates_virtualmeeting}.record where virtualmeetingid from database
     * @param int $virtualmeetingid
     */
    public static function delete_by_virtualmeetingid(int $virtualmeetingid): void {
        global $DB;
        $DB->delete_records(self::DBTABLE, ['virtualmeetingid' => $virtualmeetingid]);
    }

    /**
     * Get the builder instance by session and room.
     *
     * @param seminar_session|integer $sessionorid
     * @param room|integer $roomorid
     * @param string $alias
     * @return builder
     */
    public static function get_builder_by_session_room($sessionorid, $roomorid, string $alias = 'frdvm'): builder {
        if ($sessionorid instanceof seminar_session) {
            $sessionorid = $sessionorid->get_id();
        }
        if ($roomorid instanceof room) {
            $roomorid = $roomorid->get_id();
        }
        return builder::table(self::DBTABLE, $alias)
            ->join(['facetoface_room_dates', 'frd'], "{$alias}.roomdateid", 'frd.id')
            ->join([seminar_session::DBTABLE, 'fsd'], 'frd.sessionsdateid', 'fsd.id')
            ->join([room::DBTABLE, 'fr'], 'frd.roomid', 'fr.id')
            ->where('fr.id', $roomorid)
            ->where('fsd.id', $sessionorid);
    }

    /**
     * Load the instance by session and room.
     *
     * @param seminar_session|integer $sessionorid
     * @param room|integer $roomorid
     * @param boolean $nullifnotavailable
     * @return self|null
     */
    public static function load_by_session_room($sessionorid, $roomorid, bool $nullifnotavailable = false): ?self {
        $builder = self::get_builder_by_session_room($sessionorid, $roomorid, 'vm');
        $record = $builder->one();
        if (!$record && $nullifnotavailable) {
            return null;
        }
        $inst = new self();
        if ($record) {
            $inst->from_record($record);
        }
        return $inst;
    }

    /**
     * Check whether the virtual meeting instance exists yet or not.
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
    public function get_roomdateid(): int {
        return (int)$this->roomdateid;
    }

    /**
     * @param int $roomdateid
     * @return room_dates_virtualmeeting
     */
    public function set_roomdateid(int $roomdateid): room_dates_virtualmeeting {
        $this->roomdateid = $roomdateid;
        return $this;
    }

    /**
     * @return int
     */
    public function get_virtualmeetingid(): int {
        return (string)$this->virtualmeetingid;
    }

    /**
     * @param int $virtualmeetingid
     * @return room_dates_virtualmeeting
     */
    public function set_virtualmeetingid(int $virtualmeetingid): room_dates_virtualmeeting {
        $this->virtualmeetingid = $virtualmeetingid;
        return $this;
    }

    /**
     * Load a virtual meeting model.
     *
     * @return virtual_meeting_model|null
     */
    public function get_virtualmeeting(): ?virtual_meeting_model {
        if (!$this->exists() || empty($this->virtualmeetingid)) {
            return null;
        }
        try {
            return virtual_meeting_model::load_by_id($this->get_virtualmeetingid());
        } catch (record_not_found_exception $ex) {
            // swallow exception
            return null;
        }
    }
}
