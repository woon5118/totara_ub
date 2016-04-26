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
 * @author Valerii Kuznetsov <valerii.kuznetsov@totaralms.com>
 * @package mod_facetoface
 */

define('AJAX_SCRIPT', true);

require_once(dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/config.php');
require_once($CFG->dirroot . '/mod/facetoface/lib.php');
require_once($CFG->dirroot . '/totara/core/dialogs/dialog_content.class.php');

$facetofaceid = required_param('facetofaceid', PARAM_INT); // Necessary when creating new sessions.
$sessionid = required_param('sessionid', PARAM_INT);       // Empty when adding new session.
$timestart = required_param('timestart', PARAM_INT);
$timefinish = required_param('timefinish', PARAM_INT);
$offset = optional_param('offset', 0, PARAM_INT);
$search = optional_param('search', 0, PARAM_INT);
$selected = optional_param('selected', '', PARAM_SEQUENCE);

if (!$facetoface = $DB->get_record('facetoface', array('id' => $facetofaceid))) {
    print_error('error:incorrectfacetofaceid', 'facetoface');
}

if (!$course = $DB->get_record('course', array('id' => $facetoface->course))) {
    print_error('error:coursemisconfigured', 'facetoface');
}

if (!$cm = get_coursemodule_from_instance('facetoface', $facetoface->id, $course->id)) {
    print_error('error:incorrectcoursemoduleid', 'facetoface');
}

if ($sessionid) {
    if (!$session = facetoface_get_session($sessionid)) {
        print_error('error:incorrectcoursemodulesession', 'facetoface');
    }
    if ($session->facetoface != $facetoface->id) {
        print_error('error:incorrectcoursemodulesession', 'facetoface');
    }
}

$context = context_module::instance($cm->id);

require_login($course, false, $cm);
require_sesskey();
require_capability('mod/facetoface:editevents', $context);

$PAGE->set_context($context);
$PAGE->set_url('/mod/facetoface/room/ajax/sessionrooms.php', array(
    'facetofaceid' => $facetofaceid,
    'sessionid' => $sessionid,
    'timestart' => $timestart,
    'timefinish' => $timefinish
));

if (empty($timestart) || empty($timefinish)) {
    print_error('notimeslotsspecified', 'facetoface');
}

// Legacy Totara HTML ajax, this should be converted to json + AJAX_SCRIPT.
send_headers('text/html; charset=utf-8', false);

// Setup / loading data
$sql = "SELECT
            DISTINCT r.*
        FROM
            {facetoface_room} r
            LEFT JOIN {facetoface_sessions_dates} fsd ON (fsd.roomid = r.id)
        WHERE
            r.custom = 0 AND r.hidden = 0
            OR (r.hidden = 0 AND r.custom > 0 AND fsd.sessionid = :sessionid)
        ORDER BY
            r.name";

$allrooms = array();
$unavailablerooms = array();
if ($rooms = $DB->get_records_sql($sql, array('sessionid' => $sessionid))) {
    foreach ($rooms as $room) {
        customfield_load_data($room, "facetofaceroom", "facetoface_room");

        $roomobject = new stdClass();
        $roomobject->id = $room->id;
        $roomobject->fullname = facetoface_room_to_string($room) .
            " (" . get_string("capacity", "facetoface") . ": {$room->capacity})";
        $roomobject->name = $room->name;
        $roomobject->capacity = $room->capacity;
        $roomobject->custom = $room->custom;

        $allrooms[$room->id] = $roomobject;
    }

    // Disable unavailable rooms.
    $excludesessionids = $sessionid ? array($sessionid) : array();
    $availablerooms = facetoface_get_available_rooms(array(array($timestart, $timefinish)), 'id', $excludesessionids);
    if ($unavailablerooms = array_diff(array_keys($allrooms), array_keys($availablerooms))) {
        // Make array keys and values the same.
        $unavailablerooms = array_combine($unavailablerooms, $unavailablerooms);

        // Add alreadybooked string to fullname.
        foreach ($unavailablerooms as $key => $unavailable) {
            if (isset($allrooms[$key])) {
                $allrooms[$key]->fullname .= get_string('roomalreadybooked', 'facetoface');
            }
        }
    }
}

// Display page.
$dialog = new totara_dialog_content();
$dialog->searchtype = 'facetoface_room';
$dialog->proxy_dom_data(array('id', 'name', 'custom', 'capacity'));
$dialog->items = $allrooms;
$dialog->disabled_items = $unavailablerooms;
$dialog->lang_file = 'facetoface';
$dialog->customdata['facetofaceid'] = $facetofaceid;
$dialog->customdata['timestart'] = $timestart;
$dialog->customdata['timefinish'] = $timefinish;
$dialog->customdata['sessionid'] = $sessionid;
$dialog->customdata['selected'] = $selected;
$dialog->customdata['offset'] = $offset;
$dialog->string_nothingtodisplay = 'error:nopredefinedrooms';

echo $dialog->generate_markup();

// May be it's better to dynamically generate create new room link during dialog every_load.
// This will allow to remove offset parameter from url.
if (!$search) {
    $addroomlinkhtml =  html_writer::link('#', get_string('createnewroom', 'facetoface'),
        array('id' => 'show-editcustomroom' . $offset . '-dialog', 'class' => 'dialog-footer'));
    echo html_writer::span($addroomlinkhtml, 'dialog-nobind');
}