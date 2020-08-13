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
 * @author: Simon Coggins <simon.coggins@totaralearning.com>
 * @package: mod_perform
 */

use mod_perform\util;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/perform/rb_sources/rb_source_perform_element.php');

class rb_element_performance_reporting_embedded extends rb_base_embedded {

    public function __construct($data) {

        if (!isset($data['activity_id'])) {
            // How to handle?
        }
        $this->url = '/mod/perform/reporting/performance/activity.php';
        $this->source = 'perform_element';
        $this->shortname = 'element_performance_reporting';
        $this->fullname = get_string('embedded_element_performance_reporting', 'mod_perform');
        $this->columns = $this->define_columns();
        $this->filters = $this->define_filters();
        $this->defaultsortcolumn = 'element_default_sort';

        if (isset($data['activity_id'])) {
            $this->embeddedparams['activity_id'] = $data['activity_id'];
        }

        parent::__construct();
    }

    /**
     * Define the default columns for this report.
     *
     * @return array
     */
    protected function define_columns() {
        return [
            [
                'type' => 'element',
                'value' => 'title',
                'heading' => get_string('question_title', 'mod_perform'),
            ],
            [
                'type' => 'section',
                'value' => 'title',
                'heading' => get_string('element_reporting_title_section_title', 'mod_perform'),
            ],
            [
                'type' => 'element',
                'value' => 'type',
                'heading' => get_string('element_reporting_title_element_type', 'mod_perform'),
            ],
            [
                'type' => 'section',
                'value' => 'responding_relationship_count',
                'heading' => get_string('element_reporting_title_responding_relationships', 'mod_perform'),
            ],
            [
                'type' => 'element',
                'value' => 'is_required',
                'heading' => get_string('element_reporting_title_required', 'mod_perform'),
            ],
            [
                'type' => 'element',
                'value' => 'identifier',
                'heading' => get_string('element_identifier', 'mod_perform'),
            ],
            [
                'type' => 'element',
                'value' => 'actions',
                'heading' => get_string('actions', 'mod_perform'),
            ],
        ];
    }

    /**
     * Define the default filters for this report.
     *
     * @return array
     */
    protected function define_filters() {
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
            [
                'type' => 'element',
                'value' => 'identifier',
            ]
        ];
    }

    /**
     * Clarify if current embedded report support global report restrictions.
     * Override to true for reports that support GRR
     *
     * @return boolean
     */
    public function embedded_global_restrictions_supported() {
        return true;
    }

    /**
     * Can searches be saved?
     *
     * @return bool
     */
    public static function is_search_saving_allowed(): bool {
        return false;
    }

    /**
     * Check if the user is capable of accessing this report.
     *
     * @param int $reportfor userid of the user that this report is being generated for
     * @param reportbuilder $report the report object - can use get_param_value to get params
     * @return boolean true if the user can access this report
     */
    public function is_capable($reportfor, $report): bool {
        return util::can_potentially_report_on_subjects($reportfor);
    }
}