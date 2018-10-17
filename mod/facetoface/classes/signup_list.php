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

defined('MOODLE_INTERNAL') || die();

/**
 * Class signup_list represents signups in seminar event
 */
final class signup_list implements \Iterator {

    use traits\seminar_iterator;

    /**
     * signup_list constructor.
     *
     * @param array $conditions optional array $fieldname => requestedvalue with AND in between
     * @param string $sort an order to sort the results in.
     */
    public function __construct(array $conditions = null, string $sort = '') {
        global $DB;

        $signupitems = $DB->get_records('facetoface_signups', $conditions, $sort, '*');
        foreach ($signupitems as $signupitem) {
            $signup = new signup();
            $this->add($signup->map_instance($signupitem));
        }
    }

    /**
     * Add signup to item list
     * @param signup $item
     */
    public function add(signup $item) {
        $this->items[$item->get_id()] = $item;
    }
}