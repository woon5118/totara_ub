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
 * @author Riana Rossouw <riana.rossouw@totaralearning.com>
 * @package criteria_linkedcourses
 */

namespace criteria_linkedcourses\observer;

use core\event\admin_settings_changed;
use totara_criteria\course_item_helper;


class totara_core {

    public static function admin_settings_changed(admin_settings_changed $event) {
        global $CFG;

        $cfgsetting = "enablecompletion";

        $data = $event->get_data();
        if (!isset($data['other']['olddata']["s__{$cfgsetting}"])) {
            return;
        }

        $old_value = (int)$data['other']['olddata']["s__{$cfgsetting}"];
        if (isset($CFG->$cfgsetting) && $CFG->$cfgsetting == $old_value) {
            return;
        }

        course_item_helper::global_setting_changed('linkedcourses');
    }

}
