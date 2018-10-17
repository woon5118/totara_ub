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
 * Class seminar_event_list represents all events in one activity
 */
final class seminar_event_list implements \Iterator {

    use traits\seminar_iterator;

    /**
     * Add seminar_event to list
     * @param seminar_event $item
     */
    public function add(seminar_event $item) {
        $this->items[$item->get_id()] = $item;
    }

    /**
     * Create list of events in seminar
     * @param seminar $seminar
     * @return seminar_event_list
     */
    public static function form_seminar(seminar $seminar) {
        global $DB;
        $seminarevents = $DB->get_records('facetoface_sessions', ['facetoface' => $seminar->get_id()]);
        $list = new static();
        foreach ($seminarevents as $seminarevent) {
            $item = new seminar_event();
            $list->add($item->from_record($seminarevent));
        }
        return $list;
    }

    /**
     * Create list of all events in seminar
     * @return seminar_event_list
     */
    public static function get_all() {
        global $DB;
        $seminarevents = $DB->get_records('facetoface_sessions');
        $list = new static();
        foreach ($seminarevents as $seminarevent) {
            $item = new seminar_event();
            $list->add($item->from_record($seminarevent));
        }
        return $list;
    }
}