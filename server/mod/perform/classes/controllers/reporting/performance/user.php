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

namespace mod_perform\controllers\reporting\performance;

use context;
use context_coursecat;
use core\output\notification;
use mod_perform\controllers\perform_controller;
use mod_perform\models\activity\activity as activity_model;
use mod_perform\util;
use mod_perform\views\embedded_report_view;
use mod_perform\views\override_nav_breadcrumbs;
use moodle_exception;
use moodle_url;
use totara_mvc\has_report;
use totara_mvc\view;

class user extends perform_controller {

    use has_report;

    public function setup_context(): context {
        $category_id = util::get_default_category_id();
        return context_coursecat::instance($category_id);
    }

    public function action() {
        $subject_user_id = $this->get_optional_param('subject_user_id', null, PARAM_INT);

        if (is_null($subject_user_id)) {
            $this->set_url(static::get_url());
            $link_url = new moodle_url('/mod/perform/reporting/performance/');
            return self::create_view('mod_perform/no_report', [
                'content' => view::core_renderer()->notification(
                    get_string('activity_report_no_params_warning_message', 'mod_perform', (object)['url' => $link_url->out(true)]),
                    notification::NOTIFY_WARNING
                )
            ]);

        }

        if (!util::can_report_on_user($subject_user_id, $this->currently_logged_in_user()->id)) {
            // Current user can't report on this subject user.
            throw new moodle_exception('error_user_unavailable', 'mod_perform');
        }

        $extra_data = [
            'subject_user_id' => $subject_user_id,
        ];

        $report = $this->load_embedded_report('subject_instance_performance_reporting', $extra_data);

        $debug = $this->get_optional_param('debug', 0, PARAM_INT);

        $this->set_url(static::get_url($extra_data));

        // Current filtered count
        $filtered_count = $report->get_filtered_count();

        // Hash of current filtered state
        $filter_hash = $report->get_search_hash();

        // Shortname of embedded report being used to generate list of items.
        $filtered_report_shortname = 'subject_instance_performance_reporting';

        // Whether or not 'Export selected' button should be disabled or not.
        $export_disabled = $filtered_count > self::BULK_EXPORT_MAX_ROWS;

        // String showning number of results
        $count_string_identifier = $filtered_count == 1 ? 'x_record_selected' : 'x_records_selected';
        $count_string = get_string($count_string_identifier, 'mod_perform', $filtered_count);

        // Data for template.
        $additional_data = [
            'filtered_report_count_string' => $count_string,
            'filtered_report_embedded_shortname' => $filtered_report_shortname,
            'filtered_report_filter_hash' => $filter_hash,
            'export_disabled' => $export_disabled,
            'extra_params' => [
                [
                    'name' => 'subject_user_id',
                    'value' => $subject_user_id,
                ],
            ],
        ];

        $subject_user = \core_user::get_user($subject_user_id);
        $subject_user_name = fullname($subject_user);

        $a = (object)[
            'target' => $subject_user_name,
            'count' => $filtered_count,
        ];
        $string_identifier = $filtered_count == 1 ? 'performance_data_for' : 'performance_data_for_plural';
        $new_heading = get_string($string_identifier, 'mod_perform', $a);

        $report_view = embedded_report_view::create_from_report($report, $debug, 'mod_perform/bulk_exportable_report')
            ->add_override(new override_nav_breadcrumbs())
            ->set_title(get_string('performance_data_for', 'mod_perform', $subject_user_name))
            ->set_additional_data($additional_data);

        $report_renderer = $report_view->get_page()->get_renderer('totara_reportbuilder');

        // We want to replace the default report heading but want to keep any reporting amd, etc.
        // Thus making use of the existing report_heading template
        $rendered_heading = $report_renderer->render_from_template(
            'totara_reportbuilder/report_heading',
            [
                'reportid' => $report->get_id(),
                'heading' => $new_heading,
                'fullname' => $report->fullname,
            ]
        );

        $report_view->set_report_heading($rendered_heading);
        return $report_view;
    }

    public static function get_base_url(): string {
        return '/mod/perform/reporting/performance/user.php';
    }
}
