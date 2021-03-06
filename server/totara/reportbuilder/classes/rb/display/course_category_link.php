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
 * @author Simon Player <simon.player@totaralearning.com>
 * @package totara_reportbuilder
 */

namespace totara_reportbuilder\rb\display;

/**
 * Display class intended to convert a course category name into a link to that category's page
 *
 * @author Simon Player <simon.player@totaralearning.com>
 * @package totara_reportbuilder
 */
class course_category_link extends base {

    /**
     * Handles the display
     *
     * @param string $value
     * @param string $format
     * @param \stdClass $row
     * @param \rb_column $column
     * @param \reportbuilder $report
     * @return string
     */
    public static function display($value, $format, \stdClass $row, \rb_column $column, \reportbuilder $report) {
        $value = format_string::display($value, $format, $row, $column, $report);

        $extrafields = self::get_extrafields_row($row, $column);
        $isexport = ($format !== 'html');

        if ($isexport) {
            return $value;
        }

        $catid = $extrafields->cat_id;
        $category = $value;
        if ($catid == 0 || !$catid) {
            return '';
        }
        $attr = (isset($extrafields->cat_visible) && $extrafields->cat_visible == 0) ? array('class' => 'dimmed') : array();
        $columns = array('coursecount' => 'course', 'programcount' => 'program', 'certifcount' => 'certification');
        foreach ($columns as $field => $viewtype) {
            if (isset($extrafields->{$field})) {
                break;
            }
        }
        switch ($viewtype) {
            case 'program':
            case 'certification':
                $url = new \moodle_url('/totara/program/index.php', array('categoryid' => $catid, 'viewtype' => $viewtype));
                break;
            default:
                $url = new \moodle_url('/course/index.php', array('categoryid' => $catid));
                break;
        }
        return \html_writer::link($url, $category, $attr);
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
