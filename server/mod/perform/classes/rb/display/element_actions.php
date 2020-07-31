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
use totara_tui\output\component;

class element_actions extends base {

    /**
     * Handles the display
     *
     * @param int $element_id
     * @param string $format
     * @param \stdClass $row
     * @param \rb_column $column
     * @param \reportbuilder $report
     * @return string
     */
    public static function display($element_id, $format, \stdClass $row, \rb_column $column, \reportbuilder $report) {
        global $OUTPUT;

        if ($format !== 'html') {
            // Only applicable to the HTML format.
            return '';
        }

        $buttons = [];

        // Static to prevent expensive check once per row.
        if (!isset($can_export)) {
            static $can_export = false;
            $can_export = util::can_potentially_report_on_subjects($report->reportfor);
        }

        if ($can_export) {
            $title = get_string('export', 'mod_perform');
            $buttons[] = \html_writer::link(
                new \moodle_url('/mod/perform/reporting/performance/export.php', array('action' => 'item', 'element_id' => $element_id, 'export' => 'Export', 'format' => 'csv')),
                $OUTPUT->flex_icon('export', array('alt' => $title)),
                array('title' => $title)
            );
        }

        // TODO TL-26399 add preview element icon as a vue component.
        //      Basic example of a Vue component render below - replace with your own component and pass in element_id as a prop:
        $buttons[] = $OUTPUT->render(new component('tui/components/icons/common/Preview', ['size' => 100, 'element_id' => $element_id]));

        if ($buttons) {
            return implode ('', $buttons);
        } else {
            return '';
        }
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
