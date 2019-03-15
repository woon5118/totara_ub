<?php
/*
 * This file is part of Totara LMS
 *
 * Copyright (C) 2019 onwards Totara Learning Solutions LTD
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
 * @package mod_facetoface
 */

namespace mod_facetoface\query\event\filter;

defined('MOODLE_INTERNAL') || die();

final class room_filter extends filter {
    /**
     * @var int
     */
    private $roomid;

    /**
     * room_filter constructor.
     *
     * @param int $roomid
     */
    public function __construct(int $roomid) {
        parent::__construct('room');
        $this->roomid = $roomid;
    }

    /**
     * We do allow the room id for filtering to be changed here anyway.
     *
     * @param int $roomid
     *
     * @return room_filter
     */
    public function set_roomid(int $roomid): room_filter {
        $this->roomid = $roomid;
        return $this;
    }

    /**
     * @return array
     * @inheritdoc
     */
    public function get_where_and_params(): array {
        if (0 == $this->roomid) {
            // No point to query for a room with id as zero.
            return ["(1=1)", []];
        }

        $sql = "
            s.id IN (
                SELECT sd.sessionid
                FROM {facetoface_sessions_dates} sd
                WHERE sd.roomid = :roomid
            )
        ";

        $params = ['roomid' => $this->roomid];
        return [$sql, $params];
    }
}