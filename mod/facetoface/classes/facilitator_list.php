<?php
/*
* This file is part of Totara Learn
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
* @author Oleg Demeshev <oleg.demeshev@totaralearning.com>
* @package mod_facetoface
*/

namespace mod_facetoface;

defined('MOODLE_INTERNAL') || die();

/**
 * Class facilitator_list represents seminar facilitators
 */
final class facilitator_list  implements \Iterator {

    use traits\seminar_iterator;

    /**
     * Add facilitator to item list
     * @param facilitator_user $item
     */
    public function add(facilitator_user $item): void {
        $this->items[$item->get_id()] = $item;
    }

    /**
     * Get available facilitators for the specified time slot, or all facilitators if $timestart and $timefinish are empty.
     * @param int|string $timestart start of requested slot
     * @param int|string $timefinish end of requested slot
     * @param seminar_event $seminarevent
     * @return facilitator_list facilitators
     *
     */
    public static function get_available($timestart, $timefinish, seminar_event $seminarevent): facilitator_list {
        global $DB, $USER;

        $list = new facilitator_list();

        $params = array();
        $params['timestart'] = (int)$timestart;
        $params['timefinish'] = (int)$timefinish;
        $params['sessionid'] = $seminarevent->get_id();
        $params['facetofaceid'] = $seminarevent->get_facetoface();

        $usernamefields = get_all_user_name_fields(true, 'u');

        $bookedfacilitators = array();
        if ($timestart and $timefinish) {
            if ($timestart > $timefinish) {
                debugging('Invalid slot specified, start cannot be later than finish', DEBUG_DEVELOPER);
            }

            $sql = "SELECT DISTINCT ff.*, {$usernamefields}
                      FROM {facetoface_facilitator} ff
                 LEFT JOIN {user} u ON u.id = ff.userid 
                      JOIN {facetoface_facilitator_dates} ffd ON ffd.facilitatorid = ff.id
                      JOIN {facetoface_sessions_dates} fsd ON fsd.id = ffd.sessionsdateid
                      JOIN {facetoface_sessions} fs ON fs.id = fsd.sessionid AND fs.cancelledstatus = 0
                     WHERE ff.allowconflicts = 0 AND fsd.sessionid <> :sessionid
                       AND (fsd.timestart < :timefinish AND fsd.timefinish > :timestart)";

            $bookedfacilitators = $DB->get_records_sql($sql, $params);
        }
        // First get all site facilitators that either allow conflicts
        // or are not occupied at the given times
        // or are already used from the current event.
        // Note that hidden facilitators may be reused in the same session if already there,
        // but are completely hidden everywhere else.
        if ($seminarevent->exists()) {
            $sql = "SELECT DISTINCT ff.*, {$usernamefields}
                      FROM {facetoface_facilitator} ff
                 LEFT JOIN {user} u ON u.id = ff.userid
                 LEFT JOIN {facetoface_facilitator_dates} ffd ON ffd.facilitatorid = ff.id
                 LEFT JOIN {facetoface_sessions_dates} fsd ON fsd.id = ffd.sessionsdateid
                     WHERE ff.custom = 0 AND (ff.hidden = 0 OR fsd.sessionid = :sessionid)";
        } else {
            $sql = "SELECT ff.*, {$usernamefields}
                      FROM {facetoface_facilitator} ff
                 LEFT JOIN {user} u ON u.id = ff.userid
                     WHERE ff.custom = 0 AND ff.hidden = 0
                  ORDER BY ff.name ASC, ff.id ASC";
        }
        $facilitators = $DB->get_records_sql($sql, $params);
        foreach ($bookedfacilitators as $rid => $unused) {
            unset($facilitators[$rid]);
        }

        // Custom facilitators in the current facetoface activity.
        if ($seminarevent->get_facetoface() > 0) {
            $sql = "SELECT DISTINCT ff.*, {$usernamefields}
                      FROM {facetoface_facilitator} ff
                 LEFT JOIN {user} u ON u.id = ff.userid
                      JOIN {facetoface_facilitator_dates} ffd ON ffd.facilitatorid = ff.id
                      JOIN {facetoface_sessions_dates} fsd ON fsd.id = ffd.sessionsdateid
                      JOIN {facetoface_sessions} fs ON fs.id = fsd.sessionid
                     WHERE ff.custom = 1 AND fs.facetoface = :facetofaceid";
            $customfacilitators = $DB->get_records_sql($sql, $params);
            foreach ($customfacilitators as $facilitator) {
                if (!isset($bookedfacilitators[$facilitator->id])) {
                    $facilitators[$facilitator->id] = $facilitator;
                }
            }
            unset($customfacilitators);
        }

        // Add custom facilitators of the current user that are not assigned yet or any more.
        $params['usercreated'] = $USER->id;
        $sql = "SELECT ff.*, {$usernamefields}
                  FROM {facetoface_facilitator} ff
             LEFT JOIN {user} u ON u.id = ff.userid
             LEFT JOIN {facetoface_facilitator_dates} ffd ON ffd.facilitatorid = ff.id
             LEFT JOIN {facetoface_sessions_dates} fsd ON fsd.id = ffd.sessionsdateid
                 WHERE fsd.id IS NULL AND ff.custom = 1 AND ff.usercreated = :usercreated
              ORDER BY ff.name ASC, ff.id ASC";
        $userfacilitators = $DB->get_records_sql($sql, $params);
        foreach ($userfacilitators as $facilitator) {
            $facilitators[$facilitator->id] = $facilitator;
        }

        // Construct all the facilitators and add them to the iterator list.
        foreach ($facilitators as $facilitatordata) {
            $facilitator = new facilitator_user($facilitatordata);
            $list->add($facilitator);
        }
        return $list;
    }

    /**
     * Get facilitators by seminar session dates
     * NOTE: using in public for learners
     * @param int $sessionid
     * @return facilitator_list
     */
    public static function from_session(int $sessionid): facilitator_list {
        global $DB;

        $usernamefields = get_all_user_name_fields(true, 'u');

        $sql = "SELECT ff.*, {$usernamefields}
                  FROM {facetoface_facilitator} ff
             LEFT JOIN {user} u ON u.id = ff.userid
            INNER JOIN {facetoface_facilitator_dates} ffd ON ffd.facilitatorid = ff.id
            INNER JOIN {facetoface_sessions_dates} fsd ON fsd.id = ffd.sessionsdateid
                 WHERE ff.hidden = 0 AND fsd.id = :sessionid";
        $records = $DB->get_records_sql($sql, ['sessionid' => $sessionid]);
        $list = new static();
        foreach ($records as $record) {
            $facilitator = new facilitator_user($record);
            $list->add($facilitator);
        }
        return $list;
    }
}