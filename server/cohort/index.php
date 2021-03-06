<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Cohort related management functions, this file needs to be included manually.
 *
 * @package    core_cohort
 * @copyright  2010 Petr Skoda  {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('REPORT_BUILDER_IGNORE_PAGE_PARAMETERS', 1); // Specify parameters via reportbuidler constructor!

require('../config.php');
require_once($CFG->dirroot.'/cohort/lib.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->dirroot.'/totara/reportbuilder/lib.php');

$contextid = optional_param('contextid', 0, PARAM_INT);
$page = optional_param('page', 0, PARAM_INT);
$sid = optional_param('sid', '0', PARAM_INT);
$showall = optional_param('showall', 0, PARAM_BOOL);
$format = optional_param('format', '', PARAM_TEXT); //export format
$debug = optional_param('debug', 0, PARAM_INT); // Debug level for the report.

require_login(null, false);

if ($contextid) {
    $context = context::instance_by_id($contextid, MUST_EXIST);
} else {
    $context = context_system::instance();
    if (!empty($USER->tenantid) and !has_capability('moodle/cohort:view', $context)) {
        $tenant = \core\record\tenant::fetch($USER->tenantid);
        $context = context_coursecat::instance($tenant->categoryid);
        $contextid = $context->id;
    }
}
require_capability('moodle/cohort:view', $context);
$PAGE->set_context($context);
$syscontext = context_system::instance();

if ($context->contextlevel == CONTEXT_SYSTEM) {
    admin_externalpage_setup('cohorts', '', ['showall' => $showall], '', array('pagelayout'=>'report'));

} else if ($context->contextlevel == CONTEXT_COURSECAT) {
    $showall = 0;
    $category = $DB->get_record('course_categories', array('id'=>$context->instanceid), '*', MUST_EXIST);
    if (!empty($USER->tenantid) and !has_capability('moodle/cohort:view', $syscontext) and !empty($context->tenantid) and $category->parent == 0) {
        admin_externalpage_setup('cohorts', '', null, '', array('pagelayout'=>'report'));
    } else {
        $PAGE->set_pagelayout('report');
        $PAGE->set_url('/cohort/index.php', array('contextid' => $context->id));
        $PAGE->set_title(get_string('cohorts', 'cohort'));
        $PAGE->set_heading($COURSE->fullname);
    }

} else {
    redirect(new moodle_url('/'));
}

$manager = has_capability('moodle/cohort:manage', $context);

if ($showall) {
    $data = array('contextid' => null);
} else {
    $data = array('contextid' => $context->id);
}

// Verify global restrictions.
$shortname = 'cohort_admin';
$reportrecord = $DB->get_record('report_builder', array('shortname' => $shortname));
$globalrestrictionset = rb_global_restriction_set::create_from_page_parameters($reportrecord);

$config = (new rb_config())->set_global_restriction_set($globalrestrictionset)->set_embeddata($data)->set_sid($sid);
$report = reportbuilder::create_embedded($shortname, $config);
if (!empty($format)) {
    $report->export_data($format);
    die;
}

$report->include_js();
/** @var totara_reportbuilder_renderer $output */
$output = $PAGE->get_renderer('totara_reportbuilder');

echo $OUTPUT->header();
// This must be done after the header and before any other use of the report.
list($reporthtml, $debughtml) = $output->report_html($report, $debug);
echo $debughtml;

$report->display_restrictions();

if ($showall) {
    $heading = get_string('cohortsin', 'cohort', get_string('allcohorts' , 'core_cohort'));
} else {
    $heading = get_string('cohortsin', 'cohort', $context->get_context_name());
}
// We don't worry about showing a total count here. That is costly to generate!
$heading .= ' (' . $report->get_filtered_count() . ')';
echo $OUTPUT->heading($heading);

$baseurl = new moodle_url('/cohort/index.php', array('contextid' => $context->id, 'showall' => $showall));
if ($editcontrols = cohort_edit_controls($context, $baseurl)) {
    echo $OUTPUT->render($editcontrols);
}

// Print saved search options and filters.
$report->display_saved_search_options();
$report->display_search();
$report->display_sidebar_search();

echo $reporthtml;
$output->export_select($report, $sid);

echo $OUTPUT->footer();
