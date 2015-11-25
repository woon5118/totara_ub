<?php
/**
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
 * @author Lee Campbell <lee@learningpool.com>
 * @package mod_facetoface
 */

namespace mod_facetoface\rb\display;

/**
 * Class describing column display formatting.
 *
 * @author Lee Campbell <lee@learningpool.com>
 * @package mod_facetoface
 */
class link_f2f_session extends \totara_reportbuilder\rb\display\base {
    public static function display($date, $format, \stdClass $row, \rb_column $column, \reportbuilder $report) {
        global $OUTPUT, $CFG;

        $isexport = ($format !== 'html');
        $extra = self::get_extrafields_row($row, $column);

        if ($date && is_numeric($date)) {
            if (empty($extra->timezone) or empty($CFG->facetoface_displaysessiontimezones)) {
                $targetTZ = \core_date::get_user_timezone();
            } else {
                $targetTZ = \core_date::normalise_timezone($extra->timezone);
            }
            $date = userdate($date, get_string('strftimedate', 'langconfig'), $targetTZ);
            if ($isexport) {
                return $date;
            }
            return $OUTPUT->action_link(new \moodle_url('/mod/facetoface/attendees.php', array('s' => $extra->session_id)), $date);
        } else {
            $unknownstr = get_string('unknowndate', 'rb_source_facetoface_summary');
            if ($isexport) {
                return $unknownstr;
            }
            return $OUTPUT->action_link(new \moodle_url('/mod/facetoface/attendees.php', array('s' => $extra->session_id)), $unknownstr);
        }
    }
}