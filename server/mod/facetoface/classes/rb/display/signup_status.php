<?php
/*
 * This file is part of Totara LMS
 *
 * Copyright (C) 2016 onwards Totara Learning Solutions LTD
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
 * @author Petr Skoda <petr.skoda@totaralms.com>
 * @package mod_facetoface
 */

namespace mod_facetoface\rb\display;

/**
 * Display F2F assignment status.
 *
 * @package mod_facetoface
 */
class signup_status extends \totara_reportbuilder\rb\display\base {
    public static function display($value, $format, \stdClass $row, \rb_column $column, \reportbuilder $report) {
        // The min $value is 0 ('not set' status) and the max $value is 100 ('fully attended' status).
        $value = (int)$value;
        if ($value >= 0 && $value <= 100) {
            return \mod_facetoface\signup\state\state::from_code($value)::get_string();
        }
        // This should not happen, let's just return the raw number, and show a debugging notice in case
        // anyone cares.
        debugging('Unknown facetoface session status "'.$value.'" found', DEBUG_DEVELOPER);
        return $value;
    }

    public static function is_graphable(\rb_column $column, \rb_column_option $option, \reportbuilder $report) {
        return false;
    }
}
