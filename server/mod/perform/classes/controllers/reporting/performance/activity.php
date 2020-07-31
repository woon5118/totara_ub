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
use context_system;
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

class activity extends perform_controller {

    use has_report;

    /**
     * mod_perform\models\activity\activity instance
     * @var activity_model $activity
     */
    private $activity = null;

    public function setup_context(): context {
        if ($this->get_optional_param('activity_id', null, PARAM_INT)) {
            return $this->get_activity()->get_context();
        } else {
            $category_id = util::get_default_category_id();
            return context_coursecat::instance($category_id);
        }
    }

    public function action() {
        $activity_id = $this->get_optional_param('activity_id', null, PARAM_INT);

        if (is_null($activity_id)) {
            $this->set_url(static::get_url());
            $link_url = new moodle_url('/mod/perform/reporting/performance/');
            return self::create_view('mod_perform/no_report', [
                'content' => view::core_renderer()->notification(
                    get_string('activity_report_no_params_warning_message', 'mod_perform', (object)['url' => $link_url->out(true)]),
                    notification::NOTIFY_WARNING
                )
            ]);
        }

        $reportable_activities = util::get_reportable_activities($this->currently_logged_in_user()->id);
        if (!$reportable_activities->find('id', $activity_id)) {
            // Current user can't report on any subject users within this activity.
            throw new moodle_exception('error_activity_unavailable', 'mod_perform');
        }

        $activity_name = $this->get_activity()->name;

        $extra_data = [
            'activity_id' => $activity_id,
        ];

        $report = $this->load_embedded_report('element_performance_reporting', $extra_data);

        $debug = $this->get_optional_param('debug', 0, PARAM_INT);

        $this->set_url(static::get_url($extra_data));

        // Current filtered count
        $filtered_count = $report->get_filtered_count();

        // Hash of current filtered state
        // This is used to ensure we're not using stale data.
        $filter_hash = $report->get_search_hash();

        // Shortname of embedded report being used to generate list of items.
        $filtered_report_shortname = 'element_performance_reporting';

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
                    'name' => 'activity_id',
                    'value' => $activity_id,
                ],
            ],
        ];

        $a = (object)[
            'target' => $activity_name,
            'count' => $filtered_count,
        ];
        $string_identifier = $filtered_count == 1 ? 'performance_data_for' : 'performance_data_for_plural';
        $new_heading = get_string($string_identifier, 'mod_perform', $a);

        $report_view = embedded_report_view::create_from_report($report, $debug, 'mod_perform/bulk_exportable_report')
            ->add_override(new override_nav_breadcrumbs())
            ->set_title(get_string('performance_data_for', 'mod_perform', $activity_name))
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
        return '/mod/perform/reporting/performance/activity.php';
    }

    private function get_activity(): activity_model {
        if (!isset($this->activity)) {
            try {
                $activity_id = $this->get_required_param('activity_id', PARAM_INT);
                $this->activity = activity_model::load_by_id($activity_id);
            } catch (\Exception $e) {
                throw new moodle_exception('error_activity_id_wrong', 'mod_perform', '', null, $e);
            }
        }
        return $this->activity;
    }
}
