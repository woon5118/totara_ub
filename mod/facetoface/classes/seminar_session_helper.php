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
 * @author  Oleg Demeshev <oleg.demeshev@totaralearning.com>
 * @package mod_facetoface
 */

namespace mod_facetoface;

defined('MOODLE_INTERNAL') || die();

final class seminar_session_helper {

    /**
     * Return Event session status.
     * @param $session object
     * @param $date null|\stdClass
     * @return bool|string
     */
    public static function get_status(\stdClass $session, $date):? string {

        $timenow = time();
        if (!empty($session->cancelledstatus)) {
            $status = 'cancelled';
        } else if ($date === null) {
            // Empty for wait-listed events.
            return '';
        } else if ($date->timefinish < $timenow) {
            $status = 'over';
        } else if ($date->timestart <= $timenow && $timenow <= $date->timefinish) {
            $status = 'inprogress';
        } else if ($date->timestart > $timenow) {
            $status = 'upcoming';
        } else {
            debugging(
                "Logically impossible session times (start={$date->timestart} end={$date->timefinish} now={$timenow})",
                DEBUG_DEVELOPER
            );
            return false;
        }
        return get_string('sessionstatus:' . $status, 'mod_facetoface');
    }
}