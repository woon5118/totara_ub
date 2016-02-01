<?php
/*
 * This file is part of Totara LMS
 *
 * Copyright (C) 2010 onwards Totara Learning Solutions LTD
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
 * @author Eugene Venter <eugene@catalyst.net.nz>
 * @author Alastair Munro <alastair.munro@totaralms.com>
 * @package totara
 * @subpackage facetoface
 */

require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->dirroot . '/mod/facetoface/lib.php');
require_once($CFG->dirroot . '/totara/core/js/lib/setup.php');

global $DB, $OUTPUT;

$delete = optional_param('delete', 0, PARAM_INT);
$show = optional_param('show', 0, PARAM_INT);
$hide = optional_param('hide', 0, PARAM_INT);
$confirm = optional_param('confirm', 0, PARAM_BOOL);
$page = optional_param('page', 0, PARAM_INT);
$debug = optional_param('debug', 0, PARAM_INT);

// Check permissions.
admin_externalpage_setup('modfacetofacerooms');

$returnurl = new moodle_url('/admin/settings.php', array('section' => 'modsettingfacetoface'));

$report = reportbuilder_get_embedded_report('facetoface_rooms', array(), false, 0);
$redirectto = new moodle_url('/mod/facetoface/room/manage.php', $report->get_current_url_params());

// Handle actions.
if ($delete) {
    if (!$room = $DB->get_record('facetoface_room', array('id' => $delete))) {
        print_error('error:roomdoesnotexist', 'facetoface');
    }

    $roominuse = $DB->count_records_select('facetoface_sessions_dates', "roomid = :id", array('id' => $delete));
    if ($roominuse) {
        print_error('error:roomisinuse', 'facetoface');
    }

    if (!$confirm) {
        echo $OUTPUT->header();
        $confirmurl = new moodle_url($redirectto, array('delete' => $delete, 'confirm' => 1, 'sesskey' => sesskey()));
        echo $OUTPUT->confirm(get_string('deleteroomconfirm', 'facetoface', format_string($room->name)), $confirmurl, $redirectto);
        echo $OUTPUT->footer();
        die;
    }

    require_sesskey();
    room_delete($delete);

    totara_set_notification(get_string('roomdeleted', 'facetoface'), $redirectto, array('class' => 'notifysuccess'));

} else if ($show) {

    require_sesskey();
    if (!$room = $DB->get_record('facetoface_room', array('id' => $show))) {
        print_error('error:roomdoesnotexist', 'facetoface');
    }

    $DB->update_record('facetoface_room', array('id' => $show, 'hidden' => 0));

    totara_set_notification(get_string('roomshown', 'facetoface'), $redirectto, array('class' => 'notifysuccess'));

} else if ($hide) {

    require_sesskey();
    if (!$room = $DB->get_record('facetoface_room', array('id' => $hide))) {
        print_error('error:roomdoesnotexist', 'facetoface');
    }

    $DB->update_record('facetoface_room', array('id' => $hide, 'hidden' => 1));

    totara_set_notification(get_string('roomhidden', 'facetoface'), $redirectto, array('class' => 'notifysuccess'));
}

// Check for form submission.
if (($data = data_submitted()) && !empty($data->bulk_update)) {
    if (!confirm_sesskey()) {
        print_error('confirmsesskeybad', 'error');
    }

    if ($data->bulk_update == 'delete') {
        // Perform bulk delete action.
        if ($rooms = $DB->get_records('facetoface_room', null, '', 'id')) {

            $selected = facetoface_get_selected_report_items('room', null, $rooms);

            foreach ($selected as $item) {
                $DB->delete_records('facetoface_room', array('id' => $item->id));
            }
        }
    }

    facetoface_reset_selected_report_items('room');
    redirect($redirectto);
}

local_js(array(
    TOTARA_JS_DIALOG,
    )
);

$PAGE->set_button($report->edit_button());

echo $OUTPUT->header();

$report->display_restrictions();

echo $OUTPUT->heading(get_string('managerooms', 'facetoface'));

if ($debug) {
    $report->debug($debug);
}

$reportrenderer = $PAGE->get_renderer('totara_reportbuilder');
echo $reportrenderer->print_description($report->description, $report->_id);

$report->display_search();
$report->display_sidebar_search();
echo $report->display_saved_search_options();
$report->display_table();

$addurl = new moodle_url('/mod/facetoface/room/edit.php');

echo $OUTPUT->container_start('buttons');
echo $OUTPUT->single_button($addurl, get_string('addnewroom', 'facetoface'), 'get');
echo $OUTPUT->container_end();

echo $OUTPUT->footer();
