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
 * @copyright 2016 onwards Totara Learning Solutions LTD
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Joby Harding <joby.harding@totaralearning.com>
 * @author    Petr Skoda <petr.skoda@totaralms.com>
 * @package   theme_roots
 *
 * NOTE: this code is based on code from bootstrap theme by Bas Brands and other contributors.
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . "/course/classes/management_renderer.php");

class theme_roots_core_course_management_renderer extends core_course_management_renderer {
    public function grid_start($id = null, $class = null) {
        $gridclass = 'row';
        if (is_null($class)) {
            $class = $gridclass;
        } else {
            $class .= ' ' . $gridclass;
        }
        $attributes = array();
        if (!is_null($id)) {
            $attributes['id'] = $id;
        }
        return html_writer::start_div($class, $attributes);
    }

    public function grid_column_start($size, $id = null, $class = null) {

        // Calculate Bootstrap grid sizing.
        $bootstrapclass = 'col-md-'.$size;

        if (is_null($class)) {
            $class = $bootstrapclass;
        } else {
            $class .= ' ' . $bootstrapclass;
        }
        $attributes = array();
        if (!is_null($id)) {
            $attributes['id'] = $id;
        }
        return html_writer::start_div($class, $attributes);
    }

    protected function detail_pair($key, $value, $class ='') {
        $html = html_writer::start_div('detail-pair row '.preg_replace('#[^a-zA-Z0-9_\-]#', '-', $class));
        $html .= html_writer::div(html_writer::span($key), 'pair-key col-sm-3');
        $html .= html_writer::div(html_writer::span($value), 'pair-value col-sm-9');
        $html .= html_writer::end_div();
        return $html;
    }
}
