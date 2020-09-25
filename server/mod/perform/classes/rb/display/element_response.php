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
 * @author Riana Rossouw <riana.rossouw@totaralearning.com>
 * @package mod_perform
 */

namespace mod_perform\rb\display;

use core\format;
use mod_perform\formatter\response\element_response_formatter;
use mod_perform\models\activity\element_plugin;
use mod_perform\models\activity\respondable_element_plugin;
use totara_reportbuilder\rb\display\base;

class element_response extends base {

    /**
     * Handles the display
     *
     * @param string $response_data
     * @param string $format
     * @param \stdClass $row
     * @param \rb_column $column
     * @param \reportbuilder $report
     * @return string
     */
    public static function display($response_data, $format, \stdClass $row, \rb_column $column, \reportbuilder $report) {
        $extrafields = self::get_extrafields_row($row, $column);

        $default_category_context = $column->extracontext['default_category_context'];

        if ($format === 'html') {
            $output_format = format::FORMAT_HTML;
        } else {
            $output_format = format::FORMAT_PLAIN;
        }

        // Convert response data into actual answer.
        /** @var respondable_element_plugin $element_plugin */
        $element_plugin = element_plugin::load_by_plugin($extrafields->element_type);
        if ($element_plugin instanceof respondable_element_plugin) {
            $formatter_class = element_response_formatter::for_plugin($element_plugin);
            $formatter = new $formatter_class($output_format, $default_category_context);
            $response_data = $formatter->format($response_data);

            $response = $element_plugin->decode_response($response_data, $extrafields->element_data);
            $a = is_array($response) ? implode(', ', $response) : $response;
            return $a;
        }

        return '';
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
