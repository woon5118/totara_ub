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
 * @author Alastair Munro <alastair.munro@totaralms.com>
 * @package mod_facetoface
 */

use mod_facetoface\room;

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->dirroot . '/mod/facetoface/lib.php');
require_once($CFG->dirroot . '/totara/customfield/fieldlib.php');

admin_externalpage_setup('modfacetofacerooms');

$id = optional_param('id', 0, PARAM_INT);
$room = new room($id);

$roomlisturl = new moodle_url('/mod/facetoface/room/manage.php');

if ($room->get_custom()) {
    \core\notification::error(get_string('error:incorrectroomid', 'mod_facetoface'));
    redirect($roomlisturl);
}

$customdata = ['room' => $room, 'editoroptions' => $TEXTAREA_OPTIONS];
$mform = new \mod_facetoface\form\editroom(null, $customdata, 'post', '', array('class' => 'dialog-nobind'), true, null, 'mform_modal');

if ($mform->is_cancelled()) {
    redirect($roomlisturl);
}

if ($data = $mform->get_data()) {
    $room = \mod_facetoface\room_helper::save($data);
    $message = $id ? get_string('roomupdatesuccess', 'facetoface') : get_string('roomcreatesuccess', 'facetoface');
    \core\notification::success($message);
    redirect($roomlisturl);
}

if ($id == 0) {
    $pageheading = get_string('addroom', 'facetoface');
} else {
    $pageheading = get_string('editroom', 'facetoface');
}

echo $OUTPUT->header();

echo $OUTPUT->heading($pageheading);

$mform->display();

echo $OUTPUT->footer();
