<?php
/*
 * This file is part of Totara LMS
 *
 * Copyright (C) 2016 onwards Totara Learning Solutions LTD
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
 * @author Lee Campbell <lee@learningpool.com>
 * @package mod_facetoface
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/totara/reportbuilder/lib.php');
require_once($CFG->dirroot . '/totara/program/lib.php');
require_once($CFG->dirroot . '/mod/facetoface/lib.php');

$debug = optional_param('debug', 0, PARAM_INT);
$sessionid = optional_param('sessionid', 0, PARAM_INT);

$url = new moodle_url('/mod/facetoface/signinsheet.php');

if (!$sessionid) {
    $PAGE->set_url($url);
    $PAGE->set_context(context_system::instance());
    $PAGE->set_heading(format_string($SITE->fullname));
    $PAGE->set_title(get_string('signinsheetreport', 'mod_facetoface'));
    echo $OUTPUT->header();
    echo $OUTPUT->container(get_string('gettosigninreport', 'mod_facetoface'), $url->out());
    echo $OUTPUT->footer();
    exit;
}

$session = $DB->get_record('facetoface_sessions', array('id' => $sessionid));
if(!$session) {
    print_error('error:sessionnotfound', 'facetoface');
}

$facetoface = $DB->get_record('facetoface', array('id' => $session->facetoface));
if(!$facetoface) {
    print_error('error:facetofacenotfound', 'facetoface');
}

$course = $DB->get_record('course', array('id' => $facetoface->course));
if(!$course) {
    print_error('error:coursenotfound', 'facetoface');
}

$cm = get_coursemodule_from_instance('facetoface', $facetoface->id, $course->id, false, MUST_EXIST);
$context = context_module::instance($cm->id);
$PAGE->set_context($context);

$format = optional_param('format', '', PARAM_TEXT); // Export format.
$PAGE->set_pagelayout('standard');
$PAGE->set_cm($cm);
$url = new moodle_url('/mod/facetoface/signinsheet.php', array('sessionid' => $sessionid));
$PAGE->set_url($url);
require_login();
require_capability('mod/facetoface:exportsessionsigninsheet', $context);

// Verify global restrictions.
$shortname = 'facetoface_signin';
$reportrecord = $DB->get_record('report_builder', array('shortname' => $shortname));
$globalrestrictionset = rb_global_restriction_set::create_from_page_parameters($reportrecord);

if (!$report = reportbuilder_get_embedded_report($shortname, array('sessionid' => $session->id, 'hasbooked' => 1), false, 0,
    $globalrestrictionset)) {
    print_error('error:couldnotgenerateembeddedreport', 'totara_reportbuilder');
}

if ($format != '') {
    \mod_facetoface\event\session_signin_sheet_exported::create_from_facetoface_session($session, $context)->trigger();

    $report->export_data('pdflandscape');
    die;
}

if ($debug) {
    $report->debug($debug);
}

$renderer = $PAGE->get_renderer('totara_reportbuilder');

$strheading = get_string('signinsheetreport', 'mod_facetoface') . ' - ' . $facetoface->name;

$PAGE->set_title($strheading);
$PAGE->set_button($report->edit_button());
$PAGE->set_heading($strheading);

echo $OUTPUT->header();

$report->display_restrictions();

echo $OUTPUT->heading($strheading);
echo $renderer->print_description($report->description, $report->_id);

$renderer->export_select($report->_id, 0);

echo $OUTPUT->footer();
