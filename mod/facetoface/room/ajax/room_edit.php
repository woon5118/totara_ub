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

require_once(dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/config.php');
require_once($CFG->dirroot . '/mod/facetoface/room/lib.php');

$id = optional_param('id', 0, PARAM_INT);   // Room id.

$system_context = context_system::instance();
require_login(0, false);
require_sesskey();
require_capability('totara/core:modconfig', $system_context);

// Legacy Totara HTML ajax, this should be converted to json + AJAX_SCRIPT.
send_headers('text/html; charset=utf-8', false);

$PAGE->set_context($system_context);
$PAGE->set_url('/mod/facetoface/room/ajax/room_edit.php');

$form = process_room_form(
    $id,
    function($room) {
        echo json_encode(array('id' => $room->id, 'name' => $room->name, 'capacity' => $room->capacity, 'custom' => $room->custom));
        exit();
    },
    null,
    array('noactionbuttons' => true, 'custom' => true)
);


// This is required because custom fields may use AMD module for JS and we can't re-initialise AMD
// which will happen if we call get_end_code() without setting the first arg to false.
// It must be called before form->display and importantly before get_end_code.
$amdsnippets = $PAGE->requires->get_raw_amd_js_code();

$form->display();
echo $PAGE->requires->get_end_code(false);
// Finally add our AMD code into the page.
echo html_writer::script(implode(";\n", $amdsnippets));