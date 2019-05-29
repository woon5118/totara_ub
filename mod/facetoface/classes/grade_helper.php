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

final class grade_helper {

    /**
     * Format grade into value with respect of course grade settings.
     * @param float|null $grade raw grade value
     * @param int $course course id
     * @return string locale float or empty
     */
    public static function format(?float $grade, int $course): ?string {
        global $CFG;
        require_once($CFG->libdir.'/gradelib.php');

        if (empty($course) || (int)$course <= 0) {
            debugging('Invalid course id', DEBUG_DEVELOPER);
            return '';
        }

        if (!empty($grade)) {
            $decimalpoints = grade_get_setting($course, 'decimalpoints', $CFG->grade_decimalpoints);
            $grade = format_float($grade, $decimalpoints);
        }
        return $grade;
    }
}