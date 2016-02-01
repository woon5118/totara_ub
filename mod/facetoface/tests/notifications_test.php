<?php
/*
 * This file is part of Totara LMS
 *
 * Copyright (C) 2015 onwards Totara Learning Solutions LTD
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
 * facetoface module PHPUnit archive test class
 *
 * To test, run this from the command line from the $CFG->dirroot
 * vendor/bin/phpunit mod_facetoface_notifications_testcase mod/facetoface/tests/notifications_test.php
 *
 * @package    mod_facetoface
 * @subpackage phpunit
 * @author     Oleg Demeshev <oleg.demeshev@totaralms.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/mod/facetoface/lib.php');
require_once($CFG->dirroot . '/totara/hierarchy/prefix/position/lib.php');
require_once($CFG->dirroot . '/mod/facetoface/room/lib.php');
require_once($CFG->dirroot . '/totara/customfield/field/datetime/define.class.php');
require_once($CFG->dirroot . '/totara/customfield/field/datetime/field.class.php');

class mod_facetoface_notifications_testcase extends advanced_testcase {

    /**
     * Intercept emails and stores them locally for later verification.
     */
    private $emailsink = NULL;


    /**
     * Original configuration value to enable sending emails.
     */
    private $cfgemail = NULL;

    /**
     * PhpUnit fixture method that runs before the test method executes.
     */
    public function setUp() {
        global $CFG;

        parent::setUp();

        $this->preventResetByRollback();
        $this->resetAfterTest();

        $this->emailsink = $this->redirectEmails();
        $this->assertTrue(phpunit_util::is_redirecting_phpmailer());

        $this->cfgemail = isset($CFG->noemailever) ? $CFG->noemailever : NULL;
        $CFG->noemailever = false;
    }

    public function test_cancellation_send_delete_session() {

        $session = $this->f2f_generate_data();

        // Call facetoface_delete_session function for session1.
        $this->emailsink = $this->redirectEmails();
        facetoface_delete_session($session);
        $this->emailsink->close();

        $emails = $this->get_emails();
        $this->assertCount(4, $emails, 'Wrong no of cancellation notifications sent out.');
    }

    public function test_cancellation_nonesend_delete_session() {

        $session = $this->f2f_generate_data(false);

        // Call facetoface_delete_session function for session1.
        $this->emailsink = $this->redirectEmails();
        facetoface_delete_session($session);
        $this->emailsink->close();

        $emails = $this->get_emails();
        $this->assertCount(0, $emails, 'Error: cancellation notifications should not be sent out.');
    }

    /**
     * Create course, users, face-to-face, session
     *
     * @param bool $future, time status: future or past, to test cancellation notifications
     * @return object $session
     */
    public function f2f_generate_data($future = true) {
        global $DB;

        $this->resetAfterTest(true);
        $this->setAdminUser();

        $teacher1 = $this->getDataGenerator()->create_user();
        $student1 = $this->getDataGenerator()->create_user();
        $student2 = $this->getDataGenerator()->create_user();
        $manager = $this->getDataGenerator()->create_user();

        $assignment = new position_assignment(array('userid' => $student1->id, 'type' => POSITION_TYPE_PRIMARY));
        $assignment->managerid = $manager->id;
        assign_user_position($assignment, true);

        $assignment = new position_assignment(array('userid' => $student2->id, 'type' => POSITION_TYPE_PRIMARY));
        $assignment->managerid = $manager->id;
        assign_user_position($assignment, true);

        $course = $this->getDataGenerator()->create_course();

        $teacherrole = $DB->get_record('role', array('shortname' => 'editingteacher'));
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));

        $this->getDataGenerator()->enrol_user($teacher1->id, $course->id, $teacherrole->id);
        $this->getDataGenerator()->enrol_user($student1->id, $course->id, $studentrole->id);
        $this->getDataGenerator()->enrol_user($student2->id, $course->id, $studentrole->id);

        $facetofacegenerator = $this->getDataGenerator()->get_plugin_generator('mod_facetoface');

        $facetofacedata = array(
            'name' => 'facetoface',
            'course' => $course->id
        );
        $facetoface = $facetofacegenerator->create_instance($facetofacedata);

        $sessiondate = new stdClass();
        $sessiondate->sessiontimezone = 'Pacific/Auckland';
        if ($future) {
            $sessiondate->timestart = time() + WEEKSECS;
            $sessiondate->timefinish = time() + WEEKSECS + 60;
        } else {
            $sessiondate->timestart = time() - WEEKSECS;
            $sessiondate->timefinish = time() - WEEKSECS + 60;
        }
        $sessiondate->assetids = array();

        $sessiondata = array(
            'facetoface' => $facetoface->id,
            'capacity' => 3,
            'allowoverbook' => 1,
            'sessiondates' => array($sessiondate),
            'mincapacity' => '1',
            'cutoff' => DAYSECS - 60
        );

        $sessionid = $facetofacegenerator->add_session($sessiondata);
        $session = facetoface_get_session($sessionid);
        $session->sessiondates = facetoface_get_session_dates($session->id);

        $discountcode = 'GET15OFF';
        $notificationtype = 1;
        $statuscode = MDL_F2F_STATUS_REQUESTED;

        // Signup user1.
        $this->emailsink = $this->redirectEmails();
        $this->setUser($student1);
        facetoface_user_signup($session, $facetoface, $course, $discountcode, $notificationtype, $statuscode);
        $this->emailsink->close();

        // Signup user2.
        $this->emailsink = $this->redirectEmails();
        $this->setUser($student2);
        facetoface_user_signup($session, $facetoface, $course, $discountcode, $notificationtype, $statuscode);
        $this->emailsink->close();

        return $session;
    }

    /**
     * @return array of timestamps for use in testing.
     */
    private function create_array_of_times() {
        $times = array(
            'start1' => time() + 1 * DAYSECS,
            'end1' => time() + 1 * DAYSECS + 2 * HOURSECS,
            'other1' => time() + 5 * DAYSECS,
            'start2' => time() + 3 * DAYSECS + 30 * MINSECS,
            'end2' => time() + 4 * DAYSECS + 6 * HOURSECS,
            'other2' => time() - 4 * DAYSECS
        );
        if (date('G', $times['other1']) == 0) {
            $times['other1'] += 1; // Otherwise a different display format will be used for customfield_datetime.
        }
        if (date('G', $times['other2']) == 0) {
            $times['other2'] += 1; // Otherwise a different display format will be used for customfield_datetime.
        }

        return $times;
    }

    /**
     * Returns the email in the sink.
     *
     * @return array $emails a list of email bodies.
     */
    private function get_emails() {
        $emails = array();
        foreach ($this->emailsink->get_messages() as $email) {
            $emails[] = trim($email->body);
        }

        return $emails;
    }

    /**
     * PhpUnit fixture method that runs after the test method executes.
     */
    public function tearDown() {
        global $CFG;
        if (isset($this->cfgemail)) {
            $CFG->noemailever = $this->cfgemail;
            unset($this->cfgemail);
        }

        $this->emailsink->close();
        unset($this->emailsink);

        parent::tearDown();
    }

    /**
     * Tests the facetoface_notification_loop_session_placeholders function alone, without relying on proper working
     * of functions for saving to and retrieving from database.
     */
    public function test_facetoface_notification_loop_session_placeholders() {
        $this->resetAfterTest(true);

        // We'll use the server timezone otherwise this test will fail in some parts of the world and not others.
        $timezone = core_date::get_server_timezone();

        $times = $this->create_array_of_times();

        $msg = "Testing with non-saved session.[#sessions] Start time is [session:starttime]. Finish time is [session:finishtime].[/sessions] That is all.";
        $dataset['sessions'] = array();
        $session = new stdClass();
        $sessiondate = new stdClass();
        $sessiondate->sessiontimezone = $timezone;
        $sessiondate->timestart = $times['start1'];
        $sessiondate->timefinish = $times['end1'];
        $session->sessiondates = array($sessiondate);
        $replacedmsg = facetoface_notification_loop_session_placeholders($msg, $session);
        $expectedstart = userdate($times['start1'], get_string('strftimetime', 'langconfig'), $timezone);
        $expectedend = userdate($times['end1'], get_string('strftimetime', 'langconfig'), $timezone);
        $this->assertEquals("Testing with non-saved session. Start time is ".$expectedstart.". Finish time is ".$expectedend.". That is all.", $replacedmsg);
    }

    /**
     * Tests the facetoface_notification_loop_session_placeholders function alone, without relying on proper working
     * of functions for saving to and retrieving from database. In this case, there are two lots of tags.
     */
    public function test_facetoface_notification_loop_session_placeholders_double() {
        $this->resetAfterTest(true);

        // We'll use the server timezone otherwise this test will fail in some parts of the world and not others.
        $timezone = core_date::get_server_timezone();

        $times = $this->create_array_of_times();

        $msg = "Testing with non-saved session.[#sessions]Start time is [session:starttime]. Finish time is [session:finishtime].\n[/sessions]";
        $msg .= "[#sessions]Start date is [session:startdate]. Finish date is [session:finishdate].\n[/sessions]";
        $msg .= "That is all.";
        $dataset['sessions'] = array();
        $session = new stdClass();
        $sessiondate1 = new stdClass();
        $sessiondate1->sessiontimezone = $timezone;
        $sessiondate1->timestart = $times['start1'];
        $sessiondate1->timefinish = $times['end1'];
        $sessiondate2 = new stdClass();
        $sessiondate2->sessiontimezone = $timezone;
        $sessiondate2->timestart = $times['start2'];;
        $sessiondate2->timefinish = $times['end2'];
        $session->sessiondates = array($sessiondate1, $sessiondate2);
        $replacedmsg = facetoface_notification_loop_session_placeholders($msg, $session);

        // Get strings for display of dates and times in email.
        $startdate1 = userdate($times['start1'], get_string('strftimedate', 'langconfig'), $timezone);
        $starttime1 = userdate($times['start1'], get_string('strftimetime', 'langconfig'), $timezone);
        $enddate1 = userdate($times['end1'], get_string('strftimedate', 'langconfig'), $timezone);
        $endtime1 = userdate($times['end1'], get_string('strftimetime', 'langconfig'), $timezone);
        $startdate2 = userdate($times['start2'], get_string('strftimedate', 'langconfig'), $timezone);
        $starttime2 = userdate($times['start2'], get_string('strftimetime', 'langconfig'), $timezone);
        $enddate2 = userdate($times['end2'], get_string('strftimedate', 'langconfig'), $timezone);
        $endtime2 = userdate($times['end2'], get_string('strftimetime', 'langconfig'), $timezone);

        $expectedmsg = "Testing with non-saved session.";
        $expectedmsg .= "Start time is ".$starttime1.". Finish time is ".$endtime1.".\n";
        $expectedmsg .= "Start time is ".$starttime2.". Finish time is ".$endtime2.".\n";
        $expectedmsg .= "Start date is ".$startdate1.". Finish date is ".$enddate1.".\n";
        $expectedmsg .= "Start date is ".$startdate2.". Finish date is ".$enddate2.".\n";
        $expectedmsg .= "That is all.";
        $this->assertEquals($expectedmsg, $replacedmsg);
    }

    /**
     * Tests facetoface_notification_loop_session_placeholders function with data returned by functions generally used
     * to retrieve facetoface session data.
     */
    public function test_facetoface_notification_loop_session_placeholders_with_session() {
        $this->resetAfterTest(true);
        global $DB;

        // We'll use the server timezone otherwise this test will fail in some parts of the world and not others.
        $timezone = core_date::get_server_timezone();

        $times = $this->create_array_of_times();

        $course = $this->getDataGenerator()->create_course();

        /** @var mod_facetoface_generator $facetofacegenerator */
        $facetofacegenerator = $this->getDataGenerator()->get_plugin_generator('mod_facetoface');

        $facetofacedata = array(
            'name' => 'facetoface',
            'course' => $course->id
        );
        $facetoface = $facetofacegenerator->create_instance($facetofacedata);

        // Create a room to add to a session date. Ideally this would use an existing function rather than
        // a direct db insert - none exists while writing this test.
        $room = new stdClass();
        $room->name = 'Room One';
        $room->capacity = 20;
        $room->timemodified = time();
        $room->timecreated = $room->timemodified;
        $room->id = $DB->insert_record('facetoface_room', $room);

        $sessiondate1 = new stdClass();
        $sessiondate1->sessiontimezone = $timezone;
        $sessiondate1->timestart = $times['start1'];
        $sessiondate1->timefinish = $times['end1'];
        $sessiondate1->roomid = $room->id;
        $sessiondate1->assetids = array();

        $sessiondate2 = new stdClass();
        $sessiondate2->sessiontimezone = $timezone;
        $sessiondate2->timestart = $times['start2'];
        $sessiondate2->timefinish = $times['end2'];
        $sessiondate2->assetids = array();

        $sessiondata = array(
            'facetoface' => $facetoface->id,
            'capacity' => 3,
            'allowoverbook' => 1,
            'sessiondates' => array($sessiondate1, $sessiondate2),
            'mincapacity' => '1',
            'cutoff' => DAYSECS - 60
        );

        $sessionid = $facetofacegenerator->add_session($sessiondata);

        $session = $DB->get_record('facetoface_sessions', array('id' => $sessionid));
        $session->sessiondates = facetoface_get_session_dates($session->id);
        $rooms = facetoface_get_session_rooms($session->id);

        $msg = "The details for each session:\n";
        $msg .= "[#sessions]* Start time of [session:startdate] at [session:starttime]";
        $msg .= " and end time of [session:finishdate] at [session:finishtime] ([session:timezone]).\n";
        $msg .= "  Location is [session:room:name].\n";
        $msg .= "[/sessions]";
        $msg .= "Those are all the details.";

        $replacedmsg = facetoface_notification_loop_session_placeholders($msg, $session, $rooms);

        // Get strings for display of dates and times in email.
        $startdate1 = userdate($times['start1'], get_string('strftimedate', 'langconfig'), $timezone);
        $starttime1 = userdate($times['start1'], get_string('strftimetime', 'langconfig'), $timezone);
        $enddate1 = userdate($times['end1'], get_string('strftimedate', 'langconfig'), $timezone);
        $endtime1 = userdate($times['end1'], get_string('strftimetime', 'langconfig'), $timezone);
        $startdate2 = userdate($times['start2'], get_string('strftimedate', 'langconfig'), $timezone);
        $starttime2 = userdate($times['start2'], get_string('strftimetime', 'langconfig'), $timezone);
        $enddate2 = userdate($times['end2'], get_string('strftimedate', 'langconfig'), $timezone);
        $endtime2 = userdate($times['end2'], get_string('strftimetime', 'langconfig'), $timezone);

        $expectedmsg = "The details for each session:\n";
        $expectedmsg .= "* Start time of ".$startdate1." at ".$starttime1." and end time of ".$enddate1." at ".$endtime1." (".$timezone.").\n";
        $expectedmsg .= "  Location is Room One.\n";
        $expectedmsg .= "* Start time of ".$startdate2." at ".$starttime2." and end time of ".$enddate2." at ".$endtime2." (".$timezone.").\n";
        $expectedmsg .= "  Location is .\n";
        $expectedmsg .= "Those are all the details.";

        $this->assertEquals($expectedmsg, $replacedmsg);
    }

    public function test_facetoface_notification_loop_session_placeholders_room_customfields() {
        $this->resetAfterTest(true);
        global $DB;

        // We'll use the server timezone otherwise this test will fail in some parts of the world and not others.
        $timezone = core_date::get_server_timezone();

        $times = $this->create_array_of_times();

        $course = $this->getDataGenerator()->create_course();

        /** @var mod_facetoface_generator $facetofacegenerator */
        $facetofacegenerator = $this->getDataGenerator()->get_plugin_generator('mod_facetoface');

        $facetofacedata = array(
            'name' => 'facetoface',
            'course' => $course->id
        );
        $facetoface = $facetofacegenerator->create_instance($facetofacedata);

        /** @var totara_customfield_generator $customfieldgenerator */
        $customfieldgenerator = $this->getDataGenerator()->get_plugin_generator('totara_customfield');

        $customfields = array();

        // Create a datetime customfield.
        $cfsettings = array('Room Date' => array('shortname' => 'roomdate', 'startyear' => 2015, 'endyear' => 2030));
        $customfields += $customfieldgenerator->create_datetime('facetoface_room', $cfsettings);

        // Create a text customfield.
        $cfsettings = array('Room Text'); // Will have the shortname of RoomText
        $customfields += $customfieldgenerator->create_text('facetoface_room', $cfsettings);

        // Create a location customfield.
        $cfsettings = array('Room Location' => array('shortname' => 'roomlocation')); // Will have the shortname of RoomText
        $customfields += $customfieldgenerator->create_location('facetoface_room', $cfsettings);

        // Create a room to add to a session date.
        $room1 = new stdClass();
        $room1->name = 'Room One';
        $room1->capacity = 20;
        $room1->timemodified = time();
        $room1->timecreated = $room1->timemodified;
        $room1->id = $DB->insert_record('facetoface_room', $room1);

        $customfieldgenerator->set_datetime($room1, $customfields['Room Date'], $times['other1'], 'facetofaceroom', 'facetoface_room');
        $customfieldgenerator->set_text($room1, $customfields['Room Text'], 'Details about the room', 'facetofaceroom', 'facetoface_room');
        $location1 = new stdClass();
        $customfieldgenerator->set_location_address($room1, $customfields['Room Location'], '150 Willis Street', 'facetofaceroom', 'facetoface_room');

        // Create another room to add to a session date.
        $room2 = new stdClass();
        $room2->name = 'Room Two';
        $room2->capacity = 40;
        $room2->timemodified = time();
        $room2->timecreated = $room2->timemodified;
        $room2->id = $DB->insert_record('facetoface_room', $room2);

        $customfieldgenerator->set_datetime($room2, $customfields['Room Date'], $times['other2'], 'facetofaceroom', 'facetoface_room');

        // Set up the face-to-face session.
        $sessiondate1 = new stdClass();
        $sessiondate1->sessiontimezone = $timezone;
        $sessiondate1->timestart = $times['start1'];
        $sessiondate1->timefinish = $times['end1'];
        $sessiondate1->roomid = $room1->id;
        $sessiondate1->assetids = array();

        $sessiondate2 = new stdClass();
        $sessiondate2->sessiontimezone = $timezone;
        $sessiondate2->timestart = $times['start2'];
        $sessiondate2->timefinish = $times['end2'];
        $sessiondate2->roomid = $room2->id;
        $sessiondate2->assetids = array();

        $sessiondata = array(
            'facetoface' => $facetoface->id,
            'capacity' => 3,
            'allowoverbook' => 1,
            'sessiondates' => array($sessiondate1, $sessiondate2),
            'mincapacity' => '1',
            'cutoff' => DAYSECS - 60
        );

        $sessionid = $facetofacegenerator->add_session($sessiondata);

        // Now get all the date we've created.
        $session = $DB->get_record('facetoface_sessions', array('id' => $sessionid));
        $session->sessiondates = facetoface_get_session_dates($session->id);
        $rooms = facetoface_get_session_rooms($session->id);
        // Get data for room custom fields.
        $roomcustomfields = array();
        foreach($rooms as $room) {
            $roomcustomfields[$room->id] = customfield_get_data($room, 'facetoface_room', 'facetofaceroom', false);
        }

        $msg = "The details for each session:\n";
        $msg .= "[#sessions]";
        $msg .= "[session:room:name] has custom date of [session:room:cf_roomdate].\n";
        $msg .= "[session:room:name] has custom text of [session:room:cf_RoomText].\n";
        $msg .= "[session:room:name] has a custom location of [session:room:cf_roomlocation].\n";
        $msg .= "[/sessions]";
        $msg .= "Those are all the details.";

        $replacedmsg = facetoface_notification_loop_session_placeholders($msg, $session, $rooms, $roomcustomfields);

        $expectedmsg = "The details for each session:\n";
        $expectedmsg .= "Room One has custom date of ".userdate($times['other1'], get_string('strftimedaydatetime', 'langconfig'), $timezone).".\n";
        $expectedmsg .= "Room One has custom text of Details about the room.\n";
        $expectedmsg .= "Room One has a custom location of 150 Willis Street.\n";
        $expectedmsg .= "Room Two has custom date of ".userdate($times['other2'], get_string('strftimedaydatetime', 'langconfig'), $timezone).".\n";
        $expectedmsg .= "Room Two has custom text of .\n";
        $expectedmsg .= "Room Two has a custom location of .\n";
        $expectedmsg .= "Those are all the details.";

        $this->assertEquals($expectedmsg, $replacedmsg);
    }
}