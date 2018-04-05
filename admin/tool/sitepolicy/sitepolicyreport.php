<?php
/**
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
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Courteney Brownie <courteney.brownie@totaralearning.com>
 * @author Riana Rossouw <riana.rossouw@totaralearning.com>
 * @package tool_sitepolicy
 *
 * @global moodle_database $DB
 * @global moodle_page $PAGE
 */

require(__DIR__ . '/../../../config.php');
require_once($CFG->dirroot . '/totara/reportbuilder/lib.php');

admin_externalpage_setup('tool_sitepolicy-userconsentreport');

$sid = optional_param('sid', '0', PARAM_INT);
$format = optional_param('format', '', PARAM_TEXT); //export format
$debug = optional_param('debug', 0, PARAM_INT);

// Default to current user.
$userid = $USER->id;

$strheading = get_string('alerts', 'totara_message');

$shortname = 'tool_sitepolicy';
$data = array(
    'userid' => $userid,
);

// Verify global restrictions.
$reportrecord = $DB->get_record('report_builder', array('shortname' => $shortname));
$globalrestrictionset = rb_global_restriction_set::create_from_page_parameters($reportrecord);

if (!$report = reportbuilder_get_embedded_report($shortname, $data, false, $sid, $globalrestrictionset)) {
    print_error('error:couldnotgenerateembeddedreport', 'totara_reportbuilder');
}

$logurl = $PAGE->url->out_as_local_url();
if ($format != '') {
    $report->export_data($format);
    die;
}

\totara_reportbuilder\event\report_viewed::create_from_report($report)->trigger();

/** @var totara_reportbuilder_renderer $output */
$output = $PAGE->get_renderer('totara_reportbuilder');

$PAGE->set_button($report->edit_button());

echo $output->header();

$countfiltered = $report->get_filtered_count();
$strheading = get_string('embeddedtitle', 'rb_source_tool_sitepolicy');
$heading = $strheading . ': ' . $output->result_count_info($report);
echo $output->heading($heading);

if ($debug) {
    $report->debug($debug);
}

$report->display_search();
$report->display_sidebar_search();

echo $report->display_saved_search_options();

$report->display_table();

// Export button.
$output->export_select($report, $sid);

echo $output->footer();
