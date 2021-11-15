<?php
/**
 * This file is part of Totara Learn
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
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Murali Nair <murali.nair@totaralearning.com>
 * @package mod_perform
 */

namespace performelement_long_text\formatter;

use coding_exception;
use core\format;
use core\webapi\formatter\field\text_field_formatter;
use mod_perform\formatter\response\element_response_formatter;
use performelement_long_text\long_text;

/**
 * Formats user entered responses for this element.
 */
class response_formatter extends element_response_formatter {

    /**
     * {@inheritdoc}
     */
    protected function get_default_format($value) {
        if (empty($value)) {
            return null;
        }

        $parsed_json = json_decode($value, true);
        if (!isset($parsed_json['type'], $parsed_json['content'])) {
            // The response failed validation, so the response won't have been properly formatted yet.
            return null;
        }

        $format = $this->format ?? format::FORMAT_HTML;
        $formatter = new text_field_formatter($format, $this->context);

        if ($format === format::FORMAT_PLAIN) {
            // This correctly handles images/files and ensures they get converted to URLs
            $value = content_to_text($value, FORMAT_JSON_EDITOR);
            $formatter->set_text_format(FORMAT_PLAIN);
        } else {
            $formatter->set_additional_options(['formatter' => 'totara_tui']);
            $formatter->set_text_format(FORMAT_JSON_EDITOR);
        }

        if ($this->get_response_id()) {
            $formatter->set_pluginfile_url_options(
                $this->context,
                long_text::get_response_files_component_name(),
                long_text::get_response_files_filearea_name(),
                $this->get_response_id()
            );
        } else {
            $formatter->disabled_pluginfile_url_rewrite();
        }

        $formatted_value = $formatter->format($value);

        if ($format === format::FORMAT_RAW) {
            // Response is already in JSON format so we can just return it now.
            return $formatted_value;
        }

        $formatted_value = json_encode($formatted_value);

        if ($formatted_value === false) {
            throw new coding_exception('Error encoding the formatted response');
        }

        return $formatted_value;
    }

}
