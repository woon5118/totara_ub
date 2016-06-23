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
 * Unit tests for mod/facetoface/room/lib.php
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    // It must be included from a Moodle page.
}

global $CFG;
require_once($CFG->dirroot . '/mod/facetoface/room/lib.php');

class mod_facetoface_roomlib_testcase extends advanced_testcase {

    /** @var testing_data_generator */
    private $data_generator;

    /** @var mod_facetoface_generator */
    private $facetoface_generator;

    /** @var totara_customfield_generator */
    private $customfield_generator;

    private $cfprefix = 'facetofaceroom', $cftableprefix = 'facetoface_room';

    public function setUp() {
        $this->resetAfterTest(true);
        parent::setUp();

        $this->data_generator = $this->getDataGenerator();
        $this->facetoface_generator = $this->data_generator->get_plugin_generator('mod_facetoface');
        $this->customfield_generator = $this->data_generator->get_plugin_generator('totara_customfield');
    }

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

    /**
     * This is the most basic test to make sure that customfields are deleted
     * when a room is deleted via room_delete().
     */
    public function test_room_delete_customfield_text() {
        $this->resetAfterTest(true);
        global $DB;

        $sitewideroom = $this->facetoface_generator->add_site_wide_room(array());
        // Create a room customfield, text type.
        $roomcftextids = $this->customfield_generator->create_text($this->cftableprefix, array('fullname' => 'roomcftext'));
        // Add some text to it.
        $this->customfield_generator->set_text($sitewideroom, $roomcftextids['roomcftext'], 'Some test text', $this->cfprefix, $this->cftableprefix);
        $cfdata = customfield_get_data($sitewideroom, $this->cftableprefix, $this->cfprefix);
        $this->assertEquals('Some test text', $cfdata['roomcftext']);
        $this->assertEquals(1, $DB->count_records('facetoface_room_info_data', array('facetofaceroomid' => $sitewideroom->id)));

        // Delete the room.
        room_delete($sitewideroom->id);

        // We'll make sure the site-wide room was definitely deleted.
        $this->assertEquals(0, $DB->count_records('facetoface_room', array('id' => $sitewideroom->id)));

        //Get the customfield data again after deletion.
        $cfdata = customfield_get_data($sitewideroom, $this->cftableprefix, $this->cfprefix);
        $this->assertEmpty($cfdata);
        $this->assertEquals(0, $DB->count_records('facetoface_room_info_data', array('facetofaceroomid' => $sitewideroom->id)));
    }

    /**
     * Tests that room_delete also gets rid of files records when
     * deleting custom fields.
     */
    public function test_room_delete_customfield_file() {
        $this->resetAfterTest(true);
        global $DB;

        // Create both a site-wide and custom room.
        $sitewideroom = $this->facetoface_generator->add_site_wide_room(array());
        $customroom = $this->facetoface_generator->add_custom_room(array());

        // The file handing used by functions during this test requires $USER to be set.
        $this->setAdminUser();

        // Create a file custom field.
        $roomcffileids = $this->customfield_generator->create_file($this->cftableprefix, array('roomcffile' => array()));
        $roomcffileid = $roomcffileids['roomcffile'];

        // Create several files.
        $itemid1 = 1;
        $filename = 'testfile1.txt';
        $filecontent = 'Test file content';
        $testfile1 = $this->customfield_generator->create_test_file_from_content($filename, $filecontent, $itemid1);

        $itemid2 = 2;
        $filename = 'testfile1.txt';
        $filecontent = 'Test file content';
        $testfile1copy = $this->customfield_generator->create_test_file_from_content($filename, $filecontent, $itemid2);
        $filename = 'testfile2.txt';
        $filecontent = 'Other test file content';
        $testfile2 = $this->customfield_generator->create_test_file_from_content($filename, $filecontent, $itemid2);

        // Add $testfile1 only to the $sitewideroom.
        $this->customfield_generator->set_file($sitewideroom, $roomcffileid, $itemid1, $this->cfprefix, $this->cftableprefix);
        // Add both $testfile1 and $testfile2 to the $customroom.
        $this->customfield_generator->set_file($customroom, $roomcffileid, $itemid2, $this->cfprefix, $this->cftableprefix);
        //$this->customfield_generator->set_file($customroom, $roomcffileid, $testfile2, $this->cfprefix, $this->cftableprefix);

        $infodata_sitewide_cffile = $DB->get_record('facetoface_room_info_data',
            array('facetofaceroomid' => $sitewideroom->id, 'fieldid' => $roomcffileid));
        $this->assertNotEmpty($infodata_sitewide_cffile);
        // Sitewide should now have testfile1 but not testfile2.
        $this->assertEquals(1, $DB->count_records('files',
            array('filearea' => 'facetofaceroom_filemgr', 'filename' => 'testfile1.txt', 'itemid' => $infodata_sitewide_cffile->id)));
        $this->assertEquals(0, $DB->count_records('files',
            array('filearea' => 'facetofaceroom_filemgr', 'filename' => 'testfile2.txt', 'itemid' => $infodata_sitewide_cffile->id)));

        $infodata_custom_cffile = $DB->get_record('facetoface_room_info_data',
            array('facetofaceroomid' => $customroom->id, 'fieldid' => $roomcffileid));
        $this->assertNotEmpty($infodata_custom_cffile);
        // Sitewide should now have both testfile1 and testfile2.
        $this->assertEquals(1, $DB->count_records('files',
            array('filearea' => 'facetofaceroom_filemgr', 'filename' => 'testfile1.txt', 'itemid' => $infodata_custom_cffile->id)));
        $this->assertEquals(1, $DB->count_records('files',
            array('filearea' => 'facetofaceroom_filemgr', 'filename' => 'testfile2.txt', 'itemid' => $infodata_custom_cffile->id)));

        // Delete the site-wide room.
        room_delete($sitewideroom->id);

        // We'll make sure the site-wide room was definitely deleted and the custom room wasn't.
        $this->assertEquals(0, $DB->count_records('facetoface_room', array('id' => $sitewideroom->id)));
        $this->assertEquals(1, $DB->count_records('facetoface_room', array('id' => $customroom->id)));

        // We don't want to overwrite the original $infodata_sitewide_cffile object because we want to use
        // it's id value for the next check.
        $infodata_sitewide_cffile_again = $DB->get_record('facetoface_room_info_data',
            array('facetofaceroomid' => $sitewideroom->id, 'fieldid' => $roomcffileid));
        $this->assertEmpty($infodata_sitewide_cffile_again);
        // There should be no files left with the id from the info_data record.
        $this->assertEquals(0, $DB->count_records('files',
            array('filearea' => 'facetofaceroom_filemgr', 'itemid' => $infodata_sitewide_cffile->id)));

        // Nothing should have changed for the custom room values.
        $infodata_custom_cffile = $DB->get_record('facetoface_room_info_data',
            array('facetofaceroomid' => $customroom->id, 'fieldid' => $roomcffileid));
        $this->assertNotEmpty($infodata_custom_cffile);
        // Sitewide should now have both testfile1 and testfile2.
        $this->assertEquals(1, $DB->count_records('files',
            array('filearea' => 'facetofaceroom_filemgr', 'filename' => 'testfile1.txt', 'itemid' => $infodata_custom_cffile->id)));
        $this->assertEquals(1, $DB->count_records('files',
            array('filearea' => 'facetofaceroom_filemgr', 'filename' => 'testfile2.txt', 'itemid' => $infodata_custom_cffile->id)));

        // Now we get rid of the custom room to make sure nothing about it being custom prevents deletion of custom files.
        room_delete($customroom->id);
        $infodata_custom_cffile_again = $DB->get_record('facetoface_room_info_data',
            array('facetofaceroomid' => $customroom->id, 'fieldid' => $roomcffileid));
        $this->assertEmpty($infodata_custom_cffile_again);
        // There should be no files left with the id from the info_data record.
        $this->assertEquals(0, $DB->count_records('files',
            array('filearea' => 'facetofaceroom_filemgr', 'itemid' => $infodata_custom_cffile->id)));
    }

    /**
     * Tests the that the deletion of rooms will also delete custom field data when there
     * are several types in use.
     */
    public function test_room_delete_customfield_mixed() {
        $this->resetAfterTest(true);
        global $DB;

        // Create both a site-wide and custom room.
        $sitewideroom = $this->facetoface_generator->add_site_wide_room(array());
        $customroom = $this->facetoface_generator->add_custom_room(array());

        // The file handing used by functions during this test requires $USER to be set.
        $this->setAdminUser();

        // Create various custom fields, including datetime, file and text types.
        $roomcffileids = $this->customfield_generator->create_file($this->cftableprefix, array('roomcffile' => array()));
        $roomcffileid = $roomcffileids['roomcffile'];

        // Create a text custom field.
        $roomcftextids = $this->customfield_generator->create_text($this->cftableprefix, array('fullname' => 'roomcftext'));
        $roomcftextid = $roomcftextids['roomcftext'];

        $roomcfdateids = $this->customfield_generator->create_datetime($this->cftableprefix, array('roomcfdate' => array()));
        $roomcfdateid = $roomcfdateids['roomcfdate'];

        // Add data to the rooms for each custom field type.

        // Create several files.
        $itemid1 = 1;
        $filename = 'testfile1.txt';
        $filecontent = 'Test file content';
        $testfile1 = $this->customfield_generator->create_test_file_from_content($filename, $filecontent, $itemid1);

        $itemid2 = 2;
        $filename = 'testfile1.txt';
        $filecontent = 'Test file content';
        $testfile1copy = $this->customfield_generator->create_test_file_from_content($filename, $filecontent, $itemid2);
        $filename = 'testfile2.txt';
        $filecontent = 'Other test file content';
        $testfile2 = $this->customfield_generator->create_test_file_from_content($filename, $filecontent, $itemid2);

        // Add $testfile1 only to the $sitewideroom.
        $this->customfield_generator->set_file($sitewideroom, $roomcffileid, $itemid1, $this->cfprefix, $this->cftableprefix);
        // Add both $testfile1 and $testfile2 to the $customroom.
        $this->customfield_generator->set_file($customroom, $roomcffileid, $itemid2, $this->cfprefix, $this->cftableprefix);
        //$this->customfield_generator->set_file($customroom, $roomcffileid, $testfile2, $this->cfprefix, $this->cftableprefix);

        $this->customfield_generator->set_text($sitewideroom, $roomcftextid, 'Here is some text', $this->cfprefix, $this->cftableprefix);
        $this->customfield_generator->set_text($customroom, $roomcftextid, 'Some other text', $this->cfprefix, $this->cftableprefix);

        $sitewidedate = 1000000;
        $customdate = 200000000;
        $this->customfield_generator->set_datetime($sitewideroom, $roomcfdateid, $sitewidedate, $this->cfprefix, $this->cftableprefix);
        $this->customfield_generator->set_datetime($customroom, $roomcfdateid, $customdate, $this->cfprefix, $this->cftableprefix);

        // Check all the data is as expecting before deleting any room.
        $infodata_sitewide_cffile = $DB->get_record('facetoface_room_info_data',
            array('facetofaceroomid' => $sitewideroom->id, 'fieldid' => $roomcffileid));
        $this->assertNotEmpty($infodata_sitewide_cffile);
        // Sitewide should now have testfile1 but not testfile2.
        $this->assertEquals(1, $DB->count_records('files',
            array('filearea' => 'facetofaceroom_filemgr', 'filename' => 'testfile1.txt', 'itemid' => $infodata_sitewide_cffile->id)));
        $this->assertEquals(0, $DB->count_records('files',
            array('filearea' => 'facetofaceroom_filemgr', 'filename' => 'testfile2.txt', 'itemid' => $infodata_sitewide_cffile->id)));

        $infodata_custom_cffile = $DB->get_record('facetoface_room_info_data',
            array('facetofaceroomid' => $customroom->id, 'fieldid' => $roomcffileid));
        $this->assertNotEmpty($infodata_custom_cffile);
        // Sitewide should now have both testfile1 and testfile2.
        $this->assertEquals(1, $DB->count_records('files',
            array('filearea' => 'facetofaceroom_filemgr', 'filename' => 'testfile1.txt', 'itemid' => $infodata_custom_cffile->id)));
        $this->assertEquals(1, $DB->count_records('files',
            array('filearea' => 'facetofaceroom_filemgr', 'filename' => 'testfile2.txt', 'itemid' => $infodata_custom_cffile->id)));

        // We can't currently test files with whats returned from customfield_get_data, but we can text the others.
        $cfdata = customfield_get_data($sitewideroom, $this->cftableprefix, $this->cfprefix);

        $this->assertEquals('Here is some text', $cfdata['roomcftext']);
        $this->assertEquals(3, $DB->count_records('facetoface_room_info_data', array('facetofaceroomid' => $sitewideroom->id)));
        $this->assertEquals(userdate($sitewidedate, get_string('strftimedaydatetime', 'langconfig')), $cfdata['roomcfdate']);

        $cfdata = customfield_get_data($customroom, $this->cftableprefix, $this->cfprefix);

        $this->assertEquals('Some other text', $cfdata['roomcftext']);
        $this->assertEquals(3, $DB->count_records('facetoface_room_info_data', array('facetofaceroomid' => $customroom->id)));
        $this->assertEquals(userdate($customdate, get_string('strftimedaydatetime', 'langconfig')), $cfdata['roomcfdate']);

        // Now we'll delete the custom room.
        room_delete($customroom->id);

        // We'll make sure the custom room was definitely deleted and the site-wide room wasn't.
        $this->assertEquals(1, $DB->count_records('facetoface_room', array('id' => $sitewideroom->id)));
        $this->assertEquals(0, $DB->count_records('facetoface_room', array('id' => $customroom->id)));

        // Let's check the files first.

        $infodata_sitewide_cffile = $DB->get_record('facetoface_room_info_data',
            array('facetofaceroomid' => $sitewideroom->id, 'fieldid' => $roomcffileid));
        $this->assertNotEmpty($infodata_sitewide_cffile);
        $this->assertEquals(1, $DB->count_records('files',
            array('filearea' => 'facetofaceroom_filemgr', 'filename' => 'testfile1.txt', 'itemid' => $infodata_sitewide_cffile->id)));

        // Nothing should have changed for the custom room values.
        $infodata_custom_cffile_again = $DB->get_record('facetoface_room_info_data',
            array('facetofaceroomid' => $customroom->id, 'fieldid' => $roomcffileid));
        $this->assertEmpty($infodata_custom_cffile_again);
        // There should be no files left with the id from the info_data record.
        $this->assertEquals(0, $DB->count_records('files',
            array('filearea' => 'facetofaceroom_filemgr', 'itemid' => $infodata_custom_cffile->id)));

        // Now the rest of the fields.
        $cfdata = customfield_get_data($sitewideroom, $this->cftableprefix, $this->cfprefix);

        $this->assertEquals('Here is some text', $cfdata['roomcftext']);
        $this->assertEquals(3, $DB->count_records('facetoface_room_info_data', array('facetofaceroomid' => $sitewideroom->id)));
        $this->assertEquals(userdate($sitewidedate, get_string('strftimedaydatetime', 'langconfig')), $cfdata['roomcfdate']);

        $cfdata = customfield_get_data($customroom, $this->cftableprefix, $this->cfprefix);

        $this->assertEmpty($cfdata);
        $this->assertEquals(0, $DB->count_records('facetoface_room_info_data', array('facetofaceroomid' => $customroom->id)));
    }
}
