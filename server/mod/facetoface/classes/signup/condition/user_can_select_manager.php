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
 * @author  Valerii Kuznetsov <valerii.kuznetsov@totaralearning.com>
 * @package mod_facetoface
 */

namespace mod_facetoface\signup\condition;

defined('MOODLE_INTERNAL') || die();

/**
 * Class user_has_manager
 */
class user_can_select_manager extends condition {

    /**
     * Is the restriction met.
     * @return bool
     */
    public function pass() : bool {
        global $CFG;
        if (!empty($CFG->facetoface_managerselect)) {
            return true;
        }
        return false;
    }

    /**
     * Get description of condition
     * @return string
     */
    public static function get_description() : string {
        return get_string('state_userhasmanager_desc', 'mod_facetoface');
    }

    /**
     * Return explanation why condition has not passed
     * @return array of strings
     */
    public function get_failure() : array {
        return ['user_can_select_manager' => get_string('error:missingrequiredmanager', 'mod_facetoface')];
    }
}
