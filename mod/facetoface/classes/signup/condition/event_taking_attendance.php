<?php
/*
 * This file is part of Totara LMS
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
 * @author Tatsuhiro Kirihara <tatsuhiro.kirihara@totaralearning.com>
 * @package mod_facetoface
 */

namespace mod_facetoface\signup\condition;

defined('MOODLE_INTERNAL') || die();

/**
 * Class event_taking_attendance
 */
class event_taking_attendance extends condition {
    const UNLOCKED_SECS_PRIOR_TO_START = 900; // 15 minutes

    /**
     * Is condition passing
     *
     * @return bool
     */
    public function pass() : bool {
        $event = $this->signup->get_seminar_event();
        return $event->is_any_attendance_open();
    }

    /**
     * Get description of condition
     *
     * @return mixed
     */
    public static function get_description() : string {
        return get_string('state_eventtakingattendance_desc', 'mod_facetoface');
    }

    /**
     * Return explanation why condition has not passed
     *
     * @return array of strings
     */
    public function get_failure() : array {
        return ['event_taking_attendance' => get_string('state_eventtakingattendance_fail', 'mod_facetoface')];
    }
}