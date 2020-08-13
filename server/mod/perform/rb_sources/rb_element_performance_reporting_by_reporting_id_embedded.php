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
 * @author Jaron Steenson <jaron.steenson@totaralearning.com>
 * @package: mod_perform
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/perform/rb_sources/rb_element_performance_reporting_embedded.php');

class rb_element_performance_reporting_by_reporting_id_embedded extends rb_element_performance_reporting_embedded {

    public function __construct($data) {
        if (isset($data['element_identifier'])) {
            $element_identifiers_array = explode(',', $data['element_identifier']);
            $this->embeddedparams['element_identifier'] = $element_identifiers_array;
        }

        parent::__construct($data);
        $this->shortname = 'element_performance_reporting_by_reporting_id';
        $this->fullname = get_string('embedded_element_performance_reporting_by_reporting_id', 'mod_perform');
    }

    /**
     * Define the default filters for this report.
     *
     * @return array
     */
    protected function define_filters() {
        // This is the same as rb_element_performance_reporting_embedded but with reporting id (identifier) removed.
        return [
            [
                'type' => 'section',
                'value' => 'title',
            ],
            [
                'type' => 'element',
                'value' => 'type',
            ],
            [
                'type' => 'section',
                'value' => 'involved_relationships',
            ],
            [
                'type' => 'element',
                'value' => 'title',
            ],
        ];
    }

}