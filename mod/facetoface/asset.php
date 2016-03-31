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
 * @author Valerii Kuznetsov <valerii.kuznetsov@totaralms.com>
 * @package mod_facetoface
 */

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once($CFG->dirroot . '/mod/facetoface/lib.php');
require_once($CFG->dirroot . '/totara/customfield/field/location/define.class.php');

$id = required_param('id', PARAM_INT);
$debug = optional_param('debug', 0, PARAM_INT);

if (!$asset = facetoface_get_asset($id)) {
    print_error('error:incorrectassetid', 'facetoface');
}

require_login(0, false);

$systemcontext = context_system::instance();
$PAGE->set_context($systemcontext);

$baseurl = new moodle_url('/mod/facetoface/asset.php', array('id' => $asset->id));
$PAGE->set_url($baseurl);

// Verify global restrictions.
$shortname = 'facetoface_summary_asset';
$reportrecord = $DB->get_record('report_builder', array('shortname' => $shortname));
$globalrestrictionset = rb_global_restriction_set::create_from_page_parameters($reportrecord);

$report = reportbuilder_get_embedded_report($shortname, array('assetid' => $asset->id), false, 0, $globalrestrictionset);
if (!$report) {
    print_error('error:couldnotgenerateembeddedreport', 'totara_reportbuilder');
}

$title = get_string('viewasset', 'facetoface');
$PAGE->set_title($title);
$PAGE->set_button($report->edit_button());
$PAGE->set_heading($title);

echo $OUTPUT->header();
$renderer = $PAGE->get_renderer('mod_facetoface');
echo $renderer->heading($PAGE->title);

echo $renderer->render_asset_details($asset);

$report->display_restrictions();

echo $renderer->heading(get_string('upcomingsessionsinasset', 'facetoface'));

if ($debug) {
    $report->debug($debug);
}

$reportrenderer = $PAGE->get_renderer('totara_reportbuilder');
echo $reportrenderer->print_description($report->description, $report->_id);

$report->display_search();
$report->display_sidebar_search();
echo $report->display_saved_search_options();
$report->display_table();

if (!empty($backurl)) {
    echo $renderer->single_button($backurl, get_string('goback', 'facetoface'), 'get');
}

if (has_capability('mod/facetoface:addinstance', $systemcontext)) {
    echo $renderer->single_button(new moodle_url('/mod/facetoface/asset/manage.php'), get_string('backtoassets', 'facetoface'), 'get');
}

$report->include_js();
echo $renderer->footer();