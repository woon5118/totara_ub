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
 * @author Valerii Kuznetsov <valerii.kuznetsov@totaralearning.com>
 * @package totara_facetoface
 */

/*
 * Unit tests for mod/facetoface/lib.php
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    // It must be included from a Moodle page.
}

global $CFG;
require_once($CFG->dirroot . '/mod/facetoface/room/lib.php');

class mod_facetoface_roomlib_testcase extends advanced_testcase {
    /**
     * Check that users capabilities to edit room are checked correctly
     */
    public function test_can_user_edit_room() {
        global $DB;
        $this->resetAfterTest(true);
        $this->setAdminUser();

        // Create users (teacher, student), courses (one enrolled, one not) with f2f, and enrol users on course.
        $teacher = $this->getDataGenerator()->create_user();
        $student = $this->getDataGenerator()->create_user();

        $mycourse = $this->getDataGenerator()->create_course(array('fullname'=> 'My Course'));
        $othercourse = $this->getDataGenerator()->create_course(array('fullname'=> 'Other Course'));

        $teacherrole = $DB->get_record('role', array('shortname' => 'editingteacher'));
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));

        $this->getDataGenerator()->enrol_user($teacher->id, $mycourse->id, $teacherrole->id);
        $this->getDataGenerator()->enrol_user($student->id, $mycourse->id, $studentrole->id);

        $facetofacegenerator = $this->getDataGenerator()->get_plugin_generator('mod_facetoface');

        $myfacetoface = $facetofacegenerator->create_instance(array(
            'name' => 'myfacetoface',
            'course' => $mycourse->id
        ));

        $otherfacetoface = $facetofacegenerator->create_instance(array(
            'name' => 'otherfacetoface',
            'course' => $othercourse->id
        ));

        // TODO: Create rooms (custom unassigned, custom assigned to another course, custom assigned to own course, public).
        $orphcroom = $facetofacegenerator->add_custom_room(array());
        $mycroom = $facetofacegenerator->add_custom_room(array());
        $othercroom = $facetofacegenerator->add_custom_room(array());
        $proom = $facetofacegenerator->add_site_wide_room(array());

        $mysessiondate = new stdClass();
        $mysessiondate->sessiontimezone = 'Pacific/Auckland';
        $mysessiondate->timestart = time() + WEEKSECS;
        $mysessiondate->timefinish = time() + WEEKSECS + 60;
        $mysessiondate->roomid = $mycroom->id;

        $othersessiondate = clone($mysessiondate);
        $othersessiondate->roomid = $othercroom->id;

        $facetofacegenerator->add_session(array(
            'facetoface' => $myfacetoface->id,
            'sessiondates' => array($mysessiondate)
        ));
        $facetofacegenerator->add_session(array(
            'facetoface' => $otherfacetoface->id,
            'sessiondates' => array($othersessiondate)
        ));

        // Check that admin can edit any room.
        $this->assertTrue(can_user_edit_room(2, $orphcroom->id, $myfacetoface->id));
        $this->assertTrue(can_user_edit_room(2, $mycroom->id, $myfacetoface->id));
        $this->assertTrue(can_user_edit_room(2, $mycroom->id, $otherfacetoface->id));
        $this->assertTrue(can_user_edit_room(2, $othercroom->id, $myfacetoface->id));
        $this->assertTrue(can_user_edit_room(2, $othercroom->id, $otherfacetoface->id));
        $this->assertTrue(can_user_edit_room(2, $proom->id, $myfacetoface->id));

        // Check that learner cannot edit rooms.
        $this->assertFalse(can_user_edit_room($student->id, $orphcroom->id, $myfacetoface->id));
        $this->assertFalse(can_user_edit_room($student->id, $mycroom->id, $myfacetoface->id));
        $this->assertFalse(can_user_edit_room($student->id, $mycroom->id, $otherfacetoface->id));
        $this->assertFalse(can_user_edit_room($student->id, $othercroom->id, $myfacetoface->id));
        $this->assertFalse(can_user_edit_room($student->id, $othercroom->id, $otherfacetoface->id));
        $this->assertFalse(can_user_edit_room($student->id, $proom->id, $myfacetoface->id));

        // Check that teacher can edit custom room only assigned to his course (and orphaned).
        $this->assertTrue(can_user_edit_room($teacher->id, $orphcroom->id, $myfacetoface->id));
        $this->assertTrue(can_user_edit_room($teacher->id, $mycroom->id, $myfacetoface->id));
        $this->assertFalse(can_user_edit_room($teacher->id, $mycroom->id, $otherfacetoface->id));
        $this->assertFalse(can_user_edit_room($teacher->id, $othercroom->id, $myfacetoface->id));
        $this->assertFalse(can_user_edit_room($teacher->id, $othercroom->id, $otherfacetoface->id));
        $this->assertFalse(can_user_edit_room($teacher->id, $proom->id, $myfacetoface->id));
    }
}