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
 * @author Brendan Cox <brendan.cox@totaralearning.com>
 * @package mod_facetoface
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
use \mod_facetoface\{ event_time, seminar, seminar_event, seminar_session, signup, signup_helper };

require_once($CFG->dirroot . '/lib/phpunit/classes/advanced_testcase.php');

class mod_facetoface_renderer_testcase extends advanced_testcase {

    /** @var testing_data_generator $data_generator */
    private $data_generator;

    /** @var mod_facetoface_generator */
    private $facetoface_generator;

    public function setUp() {
        parent::setUp();
        $this->resetAfterTest(true);

        $this->data_generator = $this->getDataGenerator();
        $this->facetoface_generator = $this->data_generator->get_plugin_generator('mod_facetoface');
    }

    protected function tearDown() {
        $this->data_generator = null;
        $this->facetoface_generator = null;
        parent::tearDown();
    }

    public function data_provider_regdates_tooltip() {
        $data = array(
            array(1466015400, 1466425800, true),
            array(1466015400, 1466425800, false),
            array(null, 1466425800, true),
            array(null, 1466425800, false),
            array(1466015400, null, true),
            array(1466015400, null, false),
            array(null, null, true),
            array(null, null, false),
        );
        return $data;
    }

    /**
     * Tests the private method get_regdates_tooltip_info by creating a reflection class.
     *
     * @dataProvider data_provider_regdates_tooltip
     * @throws coding_exception
     */
    public function test_get_regdates_tooltip_info($registrationtimestart, $registrationtimefinish, $displaytimezones) {
        $this->resetAfterTest(true);
        global $PAGE;

        $renderer = $PAGE->get_renderer('mod_facetoface');

        // Create reflection class in order to test the private method.
        $reflection = new \ReflectionClass(get_class($renderer));
        $method = $reflection->getMethod('get_regdates_tooltip_info');
        $method->setAccessible(true);

        $timezone = core_date::get_user_timezone();

        $session = new stdClass();
        $session->registrationtimestart = $registrationtimestart;
        $session->registrationtimefinish = $registrationtimefinish;

        // Run the method and get the output.
        $actualoutput = $method->invokeArgs($renderer, array($session, $displaytimezones));

        // Create expected output string.
        $startdatestring = userdate($registrationtimestart, get_string('strftimedate', 'langconfig'), $timezone);
        $starttimestring = userdate($registrationtimestart, get_string('strftimetime', 'langconfig'), $timezone);
        $finishdatestring = userdate($registrationtimefinish, get_string('strftimedate', 'langconfig'), $timezone);
        $finishtimestring = userdate($registrationtimefinish, get_string('strftimetime', 'langconfig'), $timezone);

        // If there are no start or finish dates we will get an empty string.
        $expectedoutput = '';
        if (isset($registrationtimestart)) {
            // The Sign-up period opens text is only show if there is a sign-up period start date.
            $expectedoutput = "Sign-up period opens: " . $startdatestring . ", " . $starttimestring;
            if ($displaytimezones) {
                $expectedoutput .= " (time zone: " . $timezone . ")";
            }

            if ($registrationtimefinish) {
                // There is only a new line if both start and finish dates are there.
                $expectedoutput .= "\n";
            }
        }

        if (isset($registrationtimefinish)) {
            $expectedoutput .= "Sign-up period closes: " . $finishdatestring . ", " . $finishtimestring;
            if ($displaytimezones) {
                $expectedoutput .= " (time zone: " . $timezone . ")";
            }
        }

        $this->assertEquals($expectedoutput, $actualoutput);
    }

    /**
     * Tests the private method get_regdates_tooltip_info by testing the output of
     * the public method print_session_list_table.
     *
     * @dataProvider data_provider_regdates_tooltip
     */
    public function test_get_regdates_tooltip_info_via_print_session_list_table($registrationtimestart, $registrationtimefinish, $displaytimezones) {
        $this->resetAfterTest(true);
        global $PAGE;

        /** @var mod_facetoface_renderer $renderer */
        $renderer = $PAGE->get_renderer('mod_facetoface');
        // We need to set the url as this is queried during the run of print_session_list_table.
        $PAGE->set_url('/mod/facetoface/view.php');

        $course = $this->data_generator->create_course();

        $facetofacedata = new stdClass();
        $facetofacedata->course = $course->id;
        $facetoface = $this->facetoface_generator->create_instance($facetofacedata);
        $sessiondata = new stdClass();
        $sessiondata->facetoface = $facetoface->id;
        $sessiondata->registrationtimestart = $registrationtimestart ? (time() - 1 * DAYSECS) : null;
        $sessiondata->registrationtimefinish = $registrationtimefinish ? (time() + 2 * DAYSECS) : null;

        // We need to ensure the session is in the future.
        $sessiondate = new stdClass();
        $sessiondate->timestart = time() + 2 * DAYSECS;
        $sessiondate->timefinish = time() + 3 * DAYSECS;
        $sessiondate->sessiontimezone = 'Pacific/Auckland';
        $sessiondate->roomid = 0;
        $sessiondate->assetids = array();
        $sessiondata->sessiondates = array($sessiondate);

        $sessionid = $this->facetoface_generator->add_session($sessiondata);
        $seminarevent = new seminar_event($sessionid);
        $session = $seminarevent->to_record();
        $session->sessiondates = $seminarevent->get_sessions()->sort('timestart')->to_records(false);

        // First of all with minimal set to true. Meaning get_regdates_tooltip_info is called.
        $returnedoutput = $renderer->print_session_list_table([ $session ], false, false, $displaytimezones, array(), null, true);

        // The Sign-up period open date will always been first in the string, so we can check that it will indeed
        // be part of a a title attribute.
        if (isset($registrationtimestart)) {
            $this->assertContains('title="Sign-up period opens:', $returnedoutput);
        } else {
            $this->assertNotContains('title="Sign-up period opens:', $returnedoutput);
        }

        // Currently, text like in the strings below only appears in the Sign-up period tooltip. If other elements start
        // using the same text, then the below assertions may be less useful.
        if (isset($registrationtimefinish)) {
            $this->assertContains('Sign-up period closes:', $returnedoutput);
        } else {
            $this->assertNotContains('Sign-up period closes:', $returnedoutput);
        }

        // Now with minimal set to false, meaning other fixed strings are used for the tooltip instead of get_regdates_tooltip_info.
        $returnedoutput = $renderer->print_session_list_table(array($session), false, false, $displaytimezones, array(), null, false);

        // We shouldn't get the detailed output that comes from get_regdates_tooltip_info as this information
        // is given in another column.
        $this->assertFalse(strpos($returnedoutput, 'title="Sign-up period opens:'));
        $this->assertFalse(strpos($returnedoutput, 'Sign-up period closes:'));
    }

    /**
     * Create f2f renderer, set system context and initialise page.
     *
     * @return \mod_facetoface_renderer
     */
    private function create_f2f_renderer() : mod_facetoface_renderer {
        global $PAGE;

        // only admin can see the attendance taking column
        $this->setAdminUser();
        $sysctx = context_system::instance();

        /** @var mod_facetoface_renderer $renderer */
        $renderer = $PAGE->get_renderer('mod_facetoface');
        $renderer->setcontext($sysctx);
        $PAGE->set_context($sysctx);
        // We need to set the url as this is queried during the run of print_session_list_table.
        $PAGE->set_url('/mod/facetoface/view.php');

        return $renderer;
    }

    /**
     * @see \mod_facetoface_lib_testcase::prepare_date
     *
     * @param int|string $timestart
     * @param int|string $timeend
     * @param int|string $roomid
     * @return \stdClass
     */
    protected function prepare_date($timestart, $timeend, $roomid) {
        $sessiondate = new stdClass();
        $sessiondate->timestart = (string)$timestart;
        $sessiondate->timefinish = (string)$timeend;
        $sessiondate->sessiontimezone = '99';
        $sessiondate->roomid = (string)$roomid;
        return $sessiondate;
    }

    /**
     * Add a session to a seminar event.
     * @see \mod_facetoface_lib_testcase::make_session
     *
     * @param \stdClass $f2f
     * @param \stdClass $room
     * @param array $dates
     * @param boolean $cancelrightnow
     * @param integer $capacity
     * @return integer
     */
    private function make_session($f2f, $room, array $dates, $cancelrightnow = false, $capacity = 10) : int {
        $dates = array_map(
            function ($e) use ($room) {
                return $this->prepare_date($e, $e + HOURSECS * 3, $room->id);
            },
            $dates
        );
        $id = $this->facetoface_generator->add_session(['facetoface' => $f2f->id, 'sessiondates' => $dates, 'capacity' => $capacity]);
        if ($id && $cancelrightnow) {
            $evt = new seminar_event($id);
            $evt->cancel();
        }
        return $id;
    }

    /**
     * Data provider - [ event_time, human_readable_text, time_difference ]
     *
     * @return array
     */
    private function data_provider_session_time() {
        return [
            [ event_time::OVER, 'Past event', -DAYSECS ],
            [ event_time::INPROGRESS, 'In progress', -MINSECS ],
            [ event_time::UPCOMING, 'In three minutes', MINSECS * 3 ],
            [ event_time::UPCOMING, 'Future event', mod_facetoface\signup\condition\event_taking_attendance::UNLOCKED_SECS_PRIOR_TO_START + (MINSECS / 2) ],
        ];
    }

    /**
     * Create a seminar, a session and a room.
     *
     * @param integer|null $startdate
     * @param boolean $cancelled
     * @param boolean|int $sessionattendance
     * @param integer $attendancetime \mod_facetoface\seminar::ATTENDANCE_TIME_xxx
     * @return array [ seminar_id, session_id, room_id ]
     */
    private function create_seminar_session_and_room($startdate, bool $cancelled, $sessionattendance, int $attendancetime) : array {
        $course = $this->getDataGenerator()->create_course();
        $room = $this->facetoface_generator->add_site_wide_room([ 'name' => 'Chamber', 'allowconflicts' => 1 ]);
        $f2f = $this->facetoface_generator->create_instance(
            [
                'course' => $course->id,
                'sessionattendance' => $sessionattendance ? 1 : 0,
                'attendancetime' => $attendancetime
            ]
        );

        $dates = $startdate !== null ? [ time() + $startdate ] : [];

        $seminarid = $f2f->id;
        $sessionid = $this->make_session($f2f, $room, $dates, $cancelled, 2);
        $roomid = $room->id;
        return [ $seminarid, $sessionid, $roomid ];
    }

    /**
     * Create users and sign-ups.
     *
     * @param integer $numusers
     * @param seminar $seminar
     * @param seminar_event $seminarevent
     * @param string|null $stateclass
     * @return array [ user_id => \mod_facetoface\signup ]
     */
    private function create_users_signups(int $numusers, seminar $seminar, seminar_event $seminarevent, $stateclass = null) : array {
        global $DB;

        $generator = $this->getDataGenerator();
        $users = [];
        for ($i = 0; $i < $numusers; $i++) {
            $user = $generator->create_user();
            $generator->enrol_user($user->id, $seminar->get_course());

            $signup = signup::create($user->id, $seminarevent);

            signup_helper::signup($signup);

            if ($stateclass) {
                $state = new $stateclass($signup);
                $rc = new ReflectionClass($signup);
                $method = $rc->getMethod('update_status');
                $method->setAccessible(true);
                $method->invoke($signup, $state);
            }

            $users += [ $user->id => $signup ];
        }
        return $users;
    }

    /**
     * Create DOMDocument after silently fixing buggy html.
     *
     * @param string $html
     * @return DOMDocument
     */
    private static function new_domdocument(string $html) : DOMDocument {
        // Fix for incorrectly quoted html entities in the room links to suppress warnings
        $html = preg_replace('/\&(?=b(|=\d+)" id=)/', '&amp;', $html);

        $doc = new DOMDocument();
        $doc->loadHTML($html);
        return $doc;
    }

    /**
     * Get the nodes of the table cells of the first row.
     *
     * @param string $html
     * @param bool $sessionattendance
     * @return DOMNodeList
     */
    private function get_table_cells(string $html, bool $sessionattendance) : DOMNodeList {
        $doc = self::new_domdocument($html);

        $numcells = $sessionattendance ? 9 : 8;

        $tables = $doc->getElementsByTagName('table');
        $this->assertCount(1, $tables);

        $hdrs = $tables[0]->getElementsByTagName('thead')[0]->getElementsByTagName('tr')[0]->getElementsByTagName('th');
        $this->assertCount($numcells, $hdrs);

        $rows = $tables[0]->getElementsByTagName('tbody')[0]->getElementsByTagName('tr');
        $this->assertCount(1, $rows);

        $cells = $rows[0]->getElementsByTagName('td');
        $this->assertCount($numcells, $cells);

        return $cells;
    }

    /**
     * Data provider - [ event_time, human_readable_text, time_difference, cancelled ]
     *
     * @return array
     */
    public function data_provider_session_time_cancelled() {
        $data = [];
        foreach ($this->data_provider_session_time() as $e) {
            $data[] = [ $e[0], $e[1], $e[2], false ];
            $data[] = [ $e[0], $e[1], $e[2], true ];
        }
        return $data;
    }

    /**
     * Data provider - [ cancelled, sessionattendance ]
     *
     * @return array
     */
    public function data_provider_cancelled_session_attendance() {
        return [
            [ false, false ],
            [ false, true ],
            [ true, false ],
            [ true, true ],
        ];
    }

    /**
     * Test a wait-listed event on a session list table.
     *
     * @dataProvider data_provider_cancelled_session_attendance
     */
    public function test_session_list_table_waitlisted(bool $cancelled, bool $sessionattendance) {
        $this->resetAfterTest(true);

        $renderer = $this->create_f2f_renderer();

        [ $seminarid, $sessionid, $roomid ] = $this->create_seminar_session_and_room(null, $cancelled, $sessionattendance, 0);
        $seminarevent = new seminar_event($sessionid);
        $session = $seminarevent->to_record();
        $session->sessiondates = $seminarevent->get_sessions()->sort('timestart')->to_records(false);

        $outhtml = $renderer->print_session_list_table([ $session ], true, false, false, [], null, false, true, $sessionattendance, 0);
        $cells = $this->get_table_cells($outhtml, $sessionattendance);

        $this->assertSame('Wait-listed', $cells[0]->nodeValue); // Session dates
        $this->assertSame('', $cells[1]->nodeValue); // Session times
        $this->assertSame('', $cells[2]->nodeValue); // Room
        if ($cancelled) {
            $this->assertSame('Cancelled', $cells[3]->nodeValue); // Session status
        } else {
            $this->assertSame('', $cells[3]->nodeValue); // Session status
        }
        if ($sessionattendance) {
            $this->assertSame('', $cells[4]->nodeValue); // Attendance tracking
        }
    }

    /**
     * Data provider - [ event_time, human_readable_text, time_difference, cancelled, sessionattendance ]
     *
     * @return array
     */
    public function data_provider_session_time_cancelled_attendance() {
        $data = [];
        foreach ($this->data_provider_session_time_cancelled() as $e) {
            $data[] = [ $e[0], $e[1], $e[2], $e[3], false ];
            $data[] = [ $e[0], $e[1], $e[2], $e[3], true ];
        }
        return $data;
    }

    /**
     * Test room and session status columns on a session list table.
     *
     * @dataProvider data_provider_session_time_cancelled_attendance
     */
    public function test_session_list_table_room_sessionstatus(int $eventtime, string $name, int $startdate, bool $cancelled, bool $sessionattendance) {
        $this->resetAfterTest(true);

        $renderer = $this->create_f2f_renderer();

        $attendancetime = seminar::ATTENDANCE_TIME_ANY;
        [ $seminarid, $sessionid, $roomid ] = $this->create_seminar_session_and_room($startdate, $cancelled, $sessionattendance, $attendancetime);

        $seminarevent = new seminar_event($sessionid);
        $session = $seminarevent->to_record();
        $session->sessiondates = $seminarevent->get_sessions()->sort('timestart')->to_records(false);

        $outhtml = $renderer->print_session_list_table([ $session ], true, false, false, [], null, false, true, $sessionattendance, $attendancetime);
        $cells = $this->get_table_cells($outhtml, $sessionattendance);

        if ($eventtime == event_time::OVER) {
            $this->assertSame('', $cells[2]->nodeValue); // Room
            $this->assertSame('Session over', $cells[3]->nodeValue); // Session status
        } else if ($eventtime == event_time::INPROGRESS) {
            $this->assertNotSame('', $cells[2]->nodeValue); // Room
            $this->assertSame('Session in progress', $cells[3]->nodeValue); // Session status
        } else if ($cancelled) {
            $this->assertSame('', $cells[2]->nodeValue); // Room
            $this->assertSame('Cancelled', $cells[3]->nodeValue); // Session status
        } else {
            $this->assertNotSame('', $cells[2]->nodeValue); // Room
            $this->assertSame('Upcoming', $cells[3]->nodeValue); // Session status
        }
    }

    /**
     * Data provider - [ event_time, human_readable_text, time_difference, cancelled, attendancetime ]
     *
     * @return array
     */
    public function data_provider_session_time_cancelled_attendancetime() {
        $data = [];
        foreach ($this->data_provider_session_time_cancelled() as $e) {
            $data[] = [ $e[0], $e[1], $e[2], $e[3], seminar::ATTENDANCE_TIME_END ];
            $data[] = [ $e[0], $e[1], $e[2], $e[3], seminar::ATTENDANCE_TIME_START ];
            $data[] = [ $e[0], $e[1], $e[2], $e[3], seminar::ATTENDANCE_TIME_ANY ];
        }
        return $data;
    }

    /**
     * Test attendance tracking columns on a session list table.
     *
     * @dataProvider data_provider_session_time_cancelled_attendancetime
     */
    public function test_session_list_table_attendance_tracking(int $eventtime, string $name, int $startdate, bool $cancelled, bool $attendancetime) {
        $this->resetAfterTest(true);

        $renderer = $this->create_f2f_renderer();

        $sessionattendance = true;
        [ $seminarid, $sessionid, $roomid ] = $this->create_seminar_session_and_room($startdate, $cancelled, $sessionattendance, $attendancetime);

        $seminarevent = new seminar_event($sessionid);
        $session = $seminarevent->to_record();
        $session->sessiondates = $seminarevent->get_sessions()->sort('timestart')->to_records(false);
        $this->assertCount(1, $session->sessiondates);

        $now = time();
        $outhtml = $renderer->print_session_list_table([ $session ], true, false, false, [], null, false, true, $sessionattendance, $attendancetime);
        $cells = $this->get_table_cells($outhtml, $sessionattendance);

        $seminarsession = new seminar_session($session->sessiondates[0]->id);
        $is_open = $seminarsession->is_attendance_open($now);
        if ($eventtime === event_time::UPCOMING && $cancelled) {
            $this->assertSame('', $cells[4]->nodeValue); // Attendance tracking
        } else if ($attendancetime === seminar::ATTENDANCE_TIME_END && !$is_open) {
            $this->assertSame('Will open at session end time', $cells[4]->nodeValue); // Attendance tracking
        } else if ($attendancetime === seminar::ATTENDANCE_TIME_START && !$is_open) {
            $this->assertSame('Will open at session start time', $cells[4]->nodeValue); // Attendance tracking
        } else {
            $this->assertSame('No attendees', $cells[4]->nodeValue); // Attendance tracking
        }
    }

    /**
     * Simulate "Take attendance".
     *
     * @param array $usersignups pass an array returned by create_users_signups
     * @param int $count
     * @param int $sessiondateid
     * @param bool $process_all_attendees
     * @return boolean
     */
    private function take_session_attendance(array $usersignups, int $count, int $sessiondateid, $process_all_attendees = true) : bool {
        $i = 0;
        $attendance = [];
        foreach ($usersignups as $userid => $signup) {
            $dontmark = $i++ >= $count;
            if ($dontmark && !$process_all_attendees) {
                continue;
            }
            $attendance[$signup->get_id()] =
                $dontmark
                ? \mod_facetoface\signup\state\not_set::get_code()
                : \mod_facetoface\signup\state\unable_to_attend::get_code();
        }
        return \mod_facetoface\attendance\attendance_helper::process_session_attendance($attendance, $sessiondateid);
    }

    /**
     * Data provider - [ event_time, human_readable_text, time_difference, number_of_attendees, process_all_attendees ]
     *
     * @return array
     */
    public function data_provider_session_time_users_processall() {
        $data = [];
        foreach ($this->data_provider_session_time() as $e) {
            // NOTE: process_all_attendees is effective only if 0 < attendees < max_users
            $data[] = [ $e[0], $e[1], $e[2], 0, true ];
            $data[] = [ $e[0], $e[1], $e[2], 1, false ];
            $data[] = [ $e[0], $e[1], $e[2], 1, true ];
            $data[] = [ $e[0], $e[1], $e[2], 2, true ];
        }
        return $data;
    }

    /**
     * Test attendance tracking columns on a session list table when taking attendance.
     *
     * @dataProvider data_provider_session_time_users_processall
     */
    public function test_session_list_table_attendance_tracking_taking_attendance(int $eventtime, string $name, int $startdate, int $users, bool $process_all_attendees) {
        $this->resetAfterTest(true);
        global $DB;

        $renderer = $this->create_f2f_renderer();

        $sessionattendance = true;
        $attendancetime = seminar::ATTENDANCE_TIME_ANY;
        [ $seminarid, $sessionid, $roomid ] = $this->create_seminar_session_and_room($startdate, false, $sessionattendance, $attendancetime);

        $seminar = new seminar($seminarid);
        $seminarevent = new seminar_event($sessionid);
        $session = $seminarevent->to_record();
        $session->sessiondates = $seminarevent->get_sessions()->sort('timestart')->to_records(false);
        $this->assertCount(1, $session->sessiondates);

        $now = time();
        $can_signup = $eventtime === event_time::UPCOMING;

        if ($can_signup) {
            $helper = new \mod_facetoface\attendance\attendance_helper();

            $usersignups = $this->create_users_signups(2, $seminar, $seminarevent, \mod_facetoface\signup\state\booked::class);

            $before = $helper->get_attendees($seminarevent->get_id(), $session->sessiondates[0]->id);
            $this->assertCount(2, $before);

            $before_db = $DB->get_records_sql('SELECT * FROM {facetoface_signups_dates_status} WHERE sessiondateid = ?', [ $session->sessiondates[0]->id ]);
            $this->assertCount(0, $before_db);

            $this->assertTrue($this->take_session_attendance($usersignups, $users, $session->sessiondates[0]->id, $process_all_attendees));

            $after = $helper->get_attendees($seminarevent->get_id(), $session->sessiondates[0]->id);
            $this->assertCount(2, $after);

            $after_db = $DB->get_records_sql('SELECT * FROM {facetoface_signups_dates_status} WHERE sessiondateid = ?', [ $session->sessiondates[0]->id ]);
            $this->assertCount($users, $after_db);
        }

        $outhtml = $renderer->print_session_list_table([ $session ], true, false, false, [], null, false, true, $sessionattendance, $attendancetime);
        $cells = $this->get_table_cells($outhtml, $sessionattendance);

        $seminarsession = new seminar_session($session->sessiondates[0]->id);
        $this->assertTrue($seminarsession->is_attendance_open($now));

        if ($can_signup) {
            $links = $cells[4]->getElementsByTagName('a'); // Attendance tracking
            $this->assertCount(1, $links); // link to taking attendance
            if ($users === 2) {
                $this->assertSame('Saved', $links[0]->nodeValue); // Attendance tracking
            } else {
                $this->assertSame('Take attendance', $links[0]->nodeValue); // Attendance tracking
            }
        } else {
            $links = $cells[4]->getElementsByTagName('a'); // Attendance tracking
            $this->assertCount(0, $links); // no links
            $this->assertSame('No attendees', $cells[4]->nodeValue); // Attendance tracking
        }
    }
}
