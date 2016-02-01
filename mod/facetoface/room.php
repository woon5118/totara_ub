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
require_once($CFG->dirroot . '/mod/facetoface/rb_sources/rb_facetoface_summary_room_embedded.php');

// Face-to-face room id.
$r = required_param('r', PARAM_INT);
$backurl = optional_param('b', '', PARAM_URL);
$debug = optional_param('debug', 0, PARAM_INT);
$popup = optional_param('popup', 0, PARAM_INT);

if (!$room = facetoface_get_room($r)) {
    print_error('error:incorrectroomid', 'facetoface');
}

require_login(0, false);

$systemcontext = context_system::instance();
$PAGE->set_context($systemcontext);

$baseurl = new moodle_url('/mod/facetoface/room.php', array('r' => $room->id));
$PAGE->set_url($baseurl);
if ($popup) {
    $PAGE->set_pagelayout('popup');
}

$report = null;
if (rb_facetoface_summary_room_embedded::is_capable_static($USER->id)) {
    // Verify global restrictions.
    $shortname = 'facetoface_summary_room';
    $reportrecord = $DB->get_record('report_builder', array('shortname' => $shortname));
    $globalrestrictionset = rb_global_restriction_set::create_from_page_parameters($reportrecord);

    $report = reportbuilder_get_embedded_report($shortname, array('roomid' => $room->id), false, 0, $globalrestrictionset);
    if (!$report) {
        print_error('error:couldnotgenerateembeddedreport', 'totara_reportbuilder');
    }

    $PAGE->set_button($report->edit_button());
}

$title = get_string('viewroom', 'facetoface');
$PAGE->set_title($title);

$PAGE->set_heading($title);

echo $OUTPUT->header();
$renderer = $PAGE->get_renderer('mod_facetoface');
echo $renderer->heading($PAGE->title);

echo $renderer->render_room_details($room);

if ($report) {
    $report->display_restrictions();

    echo $renderer->heading(get_string('upcomingsessionsinroom', 'facetoface'));

    if ($debug) {
        $report->debug($debug);
    }

    $reportrenderer = $PAGE->get_renderer('totara_reportbuilder');
    echo $reportrenderer->print_description($report->description, $report->_id);

    $report->display_search();
    $report->display_sidebar_search();
    echo $report->display_saved_search_options();
    $report->display_table();

    if (!$popup && !empty($backurl)) {
        echo $renderer->single_button($backurl, get_string('goback', 'facetoface'), 'get');
    }

    if (!$popup && has_capability('mod/facetoface:addinstance', $systemcontext)) {
        echo $renderer->single_button(new moodle_url('/mod/facetoface/room/manage.php'), get_string('backtorooms', 'facetoface'), 'get');
    }

    $report->include_js();
}

customfield_define_location::define_add_js();
echo $renderer->footer();