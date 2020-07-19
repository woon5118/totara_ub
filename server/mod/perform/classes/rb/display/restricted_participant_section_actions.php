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
 * @author Nathan Lewis <nathan.lewis@totaralearning.com>
 * @package mod_perform
 */

namespace mod_perform\rb\display;

use core\output\flex_icon;
use rb_column;
use rb_column_option;
use reportbuilder;
use stdClass;
use totara_reportbuilder\rb\display\base;

/**
 * Class describing column display formatting.
 *
 * @author Nathan Lewis <nathan.lewis@totaralearning.com>
 * @package totara_reportbuilder
 */
class restricted_participant_section_actions extends base {
    /**
     * @inheritDoc
     */
    public static function display($value, $format, stdClass $row, rb_column $column, reportbuilder $report) {
        global $PAGE;

        $output = $PAGE->get_renderer('core');

        // Column uses noexport, but just to be sure...
        if ($format !== 'html') {
            return '';
        }

        $extrafields = self::get_extrafields_row($row, $column);

        $str_close = get_string('close', 'rb_source_perform_restricted_participant_section');
        $str_re_open = get_string('re_open', 'rb_source_perform_restricted_participant_section');

        // TODO: Add urls when available
        // TODO: Use close/re-open depending on status
        $close_url = '';
        $re_open_url = '';

        $close = $output->action_icon(
            $close_url,
            new flex_icon('lock', [
                'alt' => $str_close,
                'title' => $str_close,
            ])
        );

        // TODO: Use close/re-open depending on
        $out = $close;

        return $out;
    }

    public static function is_graphable(rb_column $column, rb_column_option $option, reportbuilder $report) {
        return false;
    }
}
