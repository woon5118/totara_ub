<?php
/*
 * This file is part of Totara Perform
 *
 * Copyright (C) 2020 onwards Totara Learning Solutions LTD
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
 * @author Simon Coggins <simon.coggins@totaralearning.com>
 * @package mod_perform
 */

namespace mod_perform\rb\display;

use mod_perform\util;
use totara_reportbuilder\rb\display\base;

class subject_instance_name_linked_to_view_form extends base {

    /**
     * Handles the display
     *
     * @param $subject_instance_id
     * @param string $format
     * @param \stdClass $row
     * @param \rb_column $column
     * @param \reportbuilder $report
     * @return string
     * @throws \coding_exception
     * @throws \moodle_exception
     */
    public static function display($activity_name, $format, \stdClass $row, \rb_column $column, \reportbuilder $report) {
        global $OUTPUT;

        if ($format !== 'html') {
            return $activity_name;
        }

        // Static to prevent expensive check once per row.
        if (!isset($can_view_form)) {
            static $can_view_form = false;
            $can_view_form = util::can_potentially_report_on_subjects($report->reportfor);
        }

        if (!$can_view_form) {
            return format_string($activity_name);
        }

        $extrafields = self::get_extrafields_row($row, $column);
        $subject_instance_id = $extrafields->subject_instance_id;

        return \html_writer::link(
            // TODO put the right URL here once we have it.
            new \moodle_url('/mod/perform/reporting/performance/view_form.php', array('subject_instance_id' => $subject_instance_id)),
            format_string($activity_name)
        );
    }

    /**
     * Is this column graphable?
     *
     * @param \rb_column $column
     * @param \rb_column_option $option
     * @param \reportbuilder $report
     * @return bool
     */
    public static function is_graphable(\rb_column $column, \rb_column_option $option, \reportbuilder $report) {
        return false;
    }

}
