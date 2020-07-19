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

define('AJAX_SCRIPT', true);

require_once(__DIR__ . '/../../../../config.php');
require_once($CFG->dirroot . '/mod/facetoface/lib.php');

use mod_facetoface\room;

$facetofaceid = required_param('facetofaceid', PARAM_INT);
$itemseq = required_param('itemids', PARAM_SEQUENCE);
$itemids = explode(',', $itemseq);

if (empty($itemids) || empty($itemids[0])) {
    exit();
}

$seminar = new \mod_facetoface\seminar($facetofaceid);
$cm = $seminar->get_coursemodule();
$context = $seminar->get_contextmodule($cm->id);

ajax_require_login($seminar->get_course(), false, $cm);
require_sesskey();
require_capability('mod/facetoface:editevents', $context);

$PAGE->set_context($context);
$PAGE->set_url('/mod/facetoface/room/ajax/room_item.php', ['facetofaceid' => $facetofaceid, 'itemids' => $itemseq]);

$rooms = array();
foreach($itemids as $itemid) {
    $room = new room($itemid);
    $res = (object)[
        'id' => $room->get_id(),
        'name' => $room->get_name(),
        'hidden' => $room->get_hidden(),
        'custom' => $room->get_custom(),
        'capacity' => $room->get_capacity()
    ];
    $rooms[] = $res;
}

// Render rooms list.
echo json_encode(array_values($rooms));