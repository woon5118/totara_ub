<?php
/*
* This file is part of Totara Learn
*
* Copyright (C) 2019 onwards Totara Learning Solutions LTD
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
* @author Oleg Demeshev <oleg.demeshev@totaralearning.com>
* @package mod_facetoface
*/

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->dirroot . '/mod/facetoface/lib.php');
require_once($CFG->dirroot . '/mod/facetoface/rb_sources/rb_facetoface_summary_facilitator_embedded.php');

use \mod_facetoface\facilitator;
use \mod_facetoface\facilitator_user;

$facilitatorid = optional_param('facilitatorid', 0, PARAM_INT);
$backurl = optional_param('b', '', PARAM_URL);
$sid = optional_param('sid', '0', PARAM_INT);
$debug = optional_param('debug', 0, PARAM_INT);
$popup = optional_param('popup', 0, PARAM_INT);

require_login(0, false);

$systemcontext = context_system::instance();
$PAGE->set_context($systemcontext);

$params = ['facilitatorid' => $facilitatorid, 'sid' => $sid, 'debug' => $debug, 'popup' => $popup];
$baseurl = new moodle_url('/mod/facetoface/reports/facilitators.php', $params);
$PAGE->set_url($baseurl);
if ($popup) {
    $PAGE->set_pagelayout('popup');
}

if (!$facilitatorid) {
    echo $OUTPUT->header();
    $managefacilitatorsurl = new moodle_url('/mod/facetoface/facilitator/manage.php');
    echo $OUTPUT->container(get_string('selectfacilitator', 'mod_facetoface', $managefacilitatorsurl->out()));
    echo $OUTPUT->footer();
    exit();
}

$report = null;
if (rb_facetoface_summary_facilitator_embedded::is_capable_static($USER->id)) {
    // Verify global restrictions.
    $shortname = 'facetoface_summary_facilitator';
    $reportrecord = $DB->get_record('report_builder', array('shortname' => $shortname));
    $globalrestrictionset = rb_global_restriction_set::create_from_page_parameters($reportrecord);
    $config = (new rb_config())->set_global_restriction_set($globalrestrictionset)->set_sid($sid);
    $report = reportbuilder::create_embedded($shortname, $config);
    if (!$report) {
        print_error('error:couldnotgenerateembeddedreport', 'totara_reportbuilder');
    }
    $PAGE->set_button($report->edit_button());
}

$title = get_string('viewfacilitator', 'mod_facetoface');
$PAGE->set_title($title);
$PAGE->set_heading($title);

echo $OUTPUT->header();

$facilitator = new facilitator($facilitatorid);
$facilitator_user = new facilitator_user($facilitator);
/** @var mod_facetoface_renderer $renderer */
$renderer = $PAGE->get_renderer('mod_facetoface');
$renderer->setcontext($systemcontext);

echo $renderer->heading($PAGE->title);
echo $renderer->render(
    \mod_facetoface\output\facilitator_details::create($facilitator_user)
);

if ($report) {
    /** @var totara_reportbuilder_renderer $reportrenderer */
    $reportrenderer = $PAGE->get_renderer('totara_reportbuilder');
    // This must be done after the header and before any other use of the report.
    list($reporthtml, $debughtml) = $reportrenderer->report_html($report, $debug);
    echo $debughtml;

    $report->display_restrictions();

    echo $renderer->heading(get_string('upcomingsessionsinfacilitator', 'mod_facetoface'));
    echo $reportrenderer->print_description($report->description, $report->_id);

    // Print saved search options and filters.
    $report->display_saved_search_options();
    $report->display_search();
    $report->display_sidebar_search();
    echo $reporthtml;

    if (!$popup && !empty($backurl)) {
        echo $renderer->single_button($backurl, get_string('goback', 'mod_facetoface'), 'get');
    }

    if (!$popup && has_capability('mod/facetoface:addinstance', $systemcontext)) {
        echo $renderer->single_button(new moodle_url('/mod/facetoface/facilitator/manage.php'), get_string('backtofacilitators', 'mod_facetoface'), 'get');
    }

    $report->include_js();
}

echo $renderer->footer();