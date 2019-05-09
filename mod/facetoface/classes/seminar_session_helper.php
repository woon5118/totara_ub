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

/**
 * Additional seminar_session functionality.
 */
final class seminar_session_helper {

    /**
     * Return Event session status as a localised string.
     * @param \stdClass $session    {facetoface_sessions} database record
     * @param \stdClass|null $date  {facetoface_sessions_dates} database record
     * @return string               Non-empty string if success
     *                              Empty string if the event is waitlisted or the function fails
     */
    public static function get_status(\stdClass $session, ?\stdClass $date): string {

        $timenow = time();
        // NOTE: Use the following syntax if a seminar_event instance is required
        // $seminarevent = (new seminar_event())->from_record($session, false);
        $seminarsession = $date !== null ? (new seminar_session())->from_record($date, false) : null;
        if (!empty($session->cancelledstatus)) {
            $status = 'cancelled';
        } else if ($date === null) {
            // Empty for wait-listed events.
            return '';
        } else if ($seminarsession->is_over($timenow)) {
            $status = 'over';
        } else if ($seminarsession->is_start($timenow) && !$seminarsession->is_over($timenow)) {
            $status = 'inprogress';
        } else if (!$seminarsession->is_start($timenow)) {
            $status = 'upcoming';
        } else {
            debugging(
                "Logically impossible session times (start={$date->timestart} end={$date->timefinish} now={$timenow})",
                DEBUG_DEVELOPER
            );
            return '';
        }
        return get_string('sessionstatus:' . $status, 'mod_facetoface');
    }
}
