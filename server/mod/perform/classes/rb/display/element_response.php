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
use mod_perform\entity\activity\element as element_entity;
use mod_perform\formatter\response\element_response_formatter;
use mod_perform\models\activity\element;
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

        $element = element::load_by_entity(new element_entity([
            'id' => $extrafields->element_id,
            'context_id' => $extrafields->element_context_id,
            'plugin_name' => $extrafields->element_type,
            'data' => $extrafields->element_data,
        ]));
        $element_plugin = $element->get_element_plugin();

        if (!$element_plugin->get_is_respondable()) {
            // Nothing to display!
            return '';
        }

        if ($format === 'html') {
            $output_format = format::FORMAT_HTML;
        } else {
            $output_format = format::FORMAT_PLAIN;
        }

        // Convert response data into actual answer.
        $formatted_response_data = element_response_formatter::get_instance($element, $output_format)
            ->set_response_id($extrafields->response_id)
            ->format($response_data);
        $response = $element_plugin->decode_response($formatted_response_data, $element->data);

        if (is_array($response)) {
            $response = implode(', ', $response);
        } else if (is_string($response)) {
            $response = trim($response);
        }

        return $response;
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
