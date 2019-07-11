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

require_once(__DIR__ . '/../../../../config.php');
require_once($CFG->dirroot . '/mod/facetoface/lib.php');
require_once($CFG->dirroot . '/mod/facetoface/dialogs/seminar_dialog_content.php');

$facetofaceid = required_param('facetofaceid', PARAM_INT); // Necessary when creating new sessions.
$sessionid = required_param('sessionid', PARAM_INT);       // Empty when adding new session.
$timestart = required_param('timestart', PARAM_INT);
$timefinish = required_param('timefinish', PARAM_INT);
$offset = optional_param('offset', 0, PARAM_INT);
$search = optional_param('search', 0, PARAM_INT);
$selected = optional_param('selected', 0, PARAM_INT);

if (empty($timestart) || empty($timefinish)) {
    print_error('notimeslotsspecified', 'facetoface');
}

$seminar = new \mod_facetoface\seminar($facetofaceid);
if (!$seminar->exists()) {
    print_error('error:incorrectfacetofaceid', 'facetoface');
}

$seminarevent = new \mod_facetoface\seminar_event($sessionid);
if (!$seminarevent->exists()) {
    // If it doesn't exist we'll need to set the facetofaceid for the event.
    $seminarevent->set_facetoface($seminar->get_id());
} else if ($seminarevent->get_facetoface() != $seminar->get_id()) {
    // If the event and seminar don't match up something is wrong.
    print_error('error:incorrectcoursemodulesession', 'facetoface');
}

$cm = $seminar->get_coursemodule();
$context = $seminar->get_contextmodule($cm->id);

ajax_require_login($seminar->get_course(), false, $cm);
require_sesskey();
require_capability('mod/facetoface:editevents', $context);

$params = [
    'facetofaceid' => $seminar->get_id(),
    'sessionid' => $seminarevent->get_id(),
    'timestart' => $timestart,
    'timefinish' => $timefinish,
    'selected' => $selected,
    'offset' => $offset
];
$PAGE->set_context($context);
$PAGE->set_url('/mod/facetoface/room/ajax/sessionrooms.php', $params);

// Legacy Totara HTML ajax, this should be converted to json + AJAX_SCRIPT.
send_headers('text/html; charset=utf-8', false);

// Setup / loading data
$roomlist = \mod_facetoface\room_list::get_available_rooms(0, 0 , $seminarevent);
$availablerooms = \mod_facetoface\room_list::get_available_rooms($timestart, $timefinish, $seminarevent);
$unavailablerooms = [];
$allrooms = [];

foreach ($roomlist as $room) {
    // Note: We'll turn the room class into a stdClass container here until customfields and dialogs play nicely with the room class.
    $roomdata = $room->to_record();

    $roomdata->fullname = (string)$room . " (" . get_string("capacity", "facetoface") . ": {$roomdata->capacity})";
    if (!$availablerooms->contains($roomdata->id)) {
        $unavailablerooms[$roomdata->id] = $roomdata->id;
        $roomdata->fullname .= get_string('roomalreadybooked', 'facetoface');
    }
    if ($roomdata->custom) {
        $roomdata->fullname .= ' (' . get_string('facetoface', 'facetoface') . ': ' . format_string($seminar->get_name()) . ')';
    }

    $allrooms[$roomdata->id] = $roomdata;
}

// Display page.
$dialog = new \seminar_dialog_content();
$dialog->searchtype = 'facetoface_room';
$dialog->proxy_dom_data(['id', 'name', 'custom', 'capacity']);
$dialog->items = $allrooms;
$dialog->disabled_items = $unavailablerooms;
$dialog->lang_file = 'facetoface';
$dialog->createid = 'show-editcustomroom' . $offset . '-dialog';
$dialog->customdata = $params;
$dialog->string_nothingtodisplay = 'error:nopredefinedrooms';
// Additional url parameters needed for pagination in the search tab.
$dialog->urlparams = $params;

echo $dialog->generate_markup();