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
 * @package modules
 * @subpackage facetoface
 */

require_once('../../config.php');
require_once('lib.php');

$s = required_param('s', PARAM_INT); // Facetoface session ID.
$backtoallsessions = optional_param('backtoallsessions', 0, PARAM_INT);

if (!$session = facetoface_get_session($s)) {
    print_error('error:incorrectcoursemodulesession', 'facetoface');
}
if (!$facetoface = $DB->get_record('facetoface', array('id' => $session->facetoface))) {
    print_error('error:incorrectfacetofaceid', 'facetoface');
}
if (!$course = $DB->get_record('course', array('id' => $facetoface->course))) {
    print_error('error:coursemisconfigured', 'facetoface');
}
if (!$cm = get_coursemodule_from_instance("facetoface", $facetoface->id, $course->id)) {
    print_error('error:incorrectcoursemoduleid', 'facetoface');
}

require_course_login($course, true, $cm);
$context = context_course::instance($course->id);
require_capability('mod/facetoface:view', $context);

$pagetitle = format_string($facetoface->name);

$PAGE->set_cm($cm);
$PAGE->set_url('/mod/facetoface/signup_tsandcs.php', array('s' => $s));

$PAGE->set_title($pagetitle);
$PAGE->set_heading($course->fullname);

echo $OUTPUT->header();

$heading = get_string('selfapprovaltandc', 'mod_facetoface');

echo $OUTPUT->box_start();
echo $OUTPUT->heading($heading);

echo format_text($facetoface->selfapprovaltandc, FORMAT_PLAIN);

echo html_writer::start_div('box generalbox');
$url = new moodle_url('/mod/facetoface/signup.php', array('s' => $s));
echo html_writer::end_div();

echo html_writer::start_div('box generalbox');
echo html_writer::link($url, get_string('goback', 'mod_facetoface'));
echo html_writer::end_div();

echo $OUTPUT->box_end();
echo $OUTPUT->footer($course);