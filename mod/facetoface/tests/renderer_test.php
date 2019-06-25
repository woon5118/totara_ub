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
use mod_facetoface\seminar;
use mod_facetoface\seminar_event;
use mod_facetoface\seminar_event_helper;
use mod_facetoface\seminar_session;
use mod_facetoface\seminar_session_list;
use mod_facetoface\signup;
use mod_facetoface\signup_helper;
use mod_facetoface\attendance\attendance_helper;
use mod_facetoface\signup\condition\event_taking_attendance;
use mod_facetoface\signup\state\booked;
use mod_facetoface\signup\state\fully_attended;
use mod_facetoface\signup\state\not_set;

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

        $renderer = $this->create_f2f_renderer();

        // Create reflection class in order to test the private method.
        $reflection = new \ReflectionClass(get_class($renderer));
        $method = $reflection->getMethod('get_regdates_tooltip_info');
        $method->setAccessible(true);

        $timezone = core_date::get_user_timezone();

        $seminarevent = new seminar_event();
        $seminarevent->set_registrationtimestart($registrationtimestart ?? 0);
        $seminarevent->set_registrationtimefinish($registrationtimefinish ?? 0);

        // Run the method and get the output.
        $actualoutput = $method->invokeArgs($renderer, array($seminarevent, $displaytimezones));

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

        $renderer = $this->create_f2f_renderer();

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

        // 1. Test with seminar_event::to_record()
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

        // 2. Test with seminar_event_helper::get_sessiondata()
        $session = seminar_event_helper::get_sessiondata($seminarevent, null, true);

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
     * Test the method get_signup_link in an ordinary situation.
     */
    public function test_get_signup_link_default() {
        $this->resetAfterTest(true);

        [ $seminarid, $sessionid, $roomid ] = $this->create_seminar_session_and_room(null, false, false, 0);
        $seminarevent = new seminar_event($sessionid);

        $renderer = $this->create_f2f_renderer();
        // hard-coded url instead of \mod_facetoface_renderer::DEFAULT_SIGNUP_LINK, to catch possible regression
        $expected = '/mod/facetoface/signup.php';

        $link = $renderer->get_signup_link($seminarevent);
        $this->assertEquals($expected, $link);

        $expected = '/somewhere/else/signup.php';

        $this->resetDebugging();
        $renderer->set_signup_link($expected);
        $debugging = $this->getDebuggingMessages()[0]->message;
        $this->assertDebuggingCalled();

        $this->assertContains('deprecated', $debugging);
        $this->assertContains('mod_facetoface\\hook\\alternative_signup_link', $debugging);
        $link = $renderer->get_signup_link($seminarevent);
        $this->assertEquals($expected, $link);
    }

    /**
     * Test the method get_signup_link by activating enrol_totara_facetoface_plugin.
     * If the plugin is missing, the whole test will be skipped.
     */
    public function test_get_signup_link_with_enrol_plugin() {
        $this->resetAfterTest(true);
        global $DB;
        /** @var moodle_database $DB */

        $plugin = enrol_get_plugin('totara_facetoface');
        if ($plugin === null) {
            $this->markTestSkipped('enrol_totara_facetoface_plugin is not available');
        }

        $this->assertInstanceOf('enrol_totara_facetoface_plugin', $plugin);
        $enabled = enrol_get_plugins(true);
        $enabled['guest'] = true;
        $enabled['totara_facetoface'] = true;
        set_config('enrol_plugins_enabled', implode(',', array_keys($enabled)));

        [ $seminarid, $session1id, $roomid ] = $this->create_seminar_session_and_room(null, false, false, 0);
        $seminar = new seminar($seminarid);
        $seminarevent1 = new seminar_event($session1id);
        $course = $DB->get_record('course', array('id' => $seminar->get_course()));

        $session2id = $this->make_session((object)['id' => $seminarid], (object)['id' => $roomid], [ time() + YEARSECS ], false);
        $seminarevent2 = new seminar_event($session1id);

        $user = $this->getDataGenerator()->create_user();
        $context = context_course::instance($course->id);
        $renderer1 = $this->create_f2f_renderer();
        $renderer2 = $this->create_f2f_renderer();

        $alter = '/somewhere/else/signup.php';
        $renderer2->set_signup_link($alter);
        $this->assertDebuggingCalled();

        $this->setUser($user);

        $instid = $plugin->add_instance($course, ['name' => 'totara_facetoface', 'status' => ENROL_INSTANCE_ENABLED, 'customint6' => 1]);

        // hard-coded url to catch possible regression
        $expected = '/enrol/totara_facetoface/signup.php';

        $link = $renderer1->get_signup_link($seminarevent1);
        $this->assertEquals($expected, $link);
        $link = $renderer1->get_signup_link($seminarevent2);
        $this->assertEquals($expected, $link);

        $link = $renderer2->get_signup_link($seminarevent1);
        $this->assertEquals($alter, $link);
        $link = $renderer2->get_signup_link($seminarevent2);
        $this->assertEquals($alter, $link);

        $inst = $DB->get_record('enrol', array('id' => $instid), '*', MUST_EXIST);

        // once enrolled the signup link must be default
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $this->assertNotEmpty($studentrole);

        $plugin->enrol_user($inst, $user->id, $studentrole->id);

        // hard-coded url instead of \mod_facetoface_renderer::DEFAULT_SIGNUP_LINK, to catch possible regression
        $expected = '/mod/facetoface/signup.php';

        $link = $renderer1->get_signup_link($seminarevent1);
        $this->assertEquals($expected, $link);
        $link = $renderer1->get_signup_link($seminarevent2);
        $this->assertEquals($expected, $link);

        $link = $renderer2->get_signup_link($seminarevent1);
        $this->assertEquals($alter, $link);
        $link = $renderer2->get_signup_link($seminarevent2);
        $this->assertEquals($alter, $link);
    }

    /**
     * Instantiate the mod_facetoface_renderer, set system context and initialise page.
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
        // Stop testing if the default renderer is not used.
        if (get_class($renderer) !== 'mod_facetoface_renderer') {
            $this->markTestSkipped('The facetoface renderer has been overridden.');
        }
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
        $sessiondate->timestart = is_numeric($timestart) ? (string)(int)$timestart : strtotime($timestart);
        $sessiondate->timefinish = is_numeric($timeend) ? (string)(int)$timeend : strtotime($timeend);
        $sessiondate->sessiontimezone = '99';
        $sessiondate->roomid = (string)(int)$roomid;
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
     * @param seminar_event $seminarevent
     * @param string|null $stateclass
     * @return array [ user_id => \mod_facetoface\signup ]
     */
    private function create_users_signups(int $numusers, seminar_event $seminarevent, $stateclass = null) : array {
        global $DB;

        $generator = $this->getDataGenerator();
        $users = [];
        for ($i = 0; $i < $numusers; $i++) {
            $user = $generator->create_user();
            $generator->enrol_user($user->id, $seminarevent->get_seminar()->get_course());

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
        // Fix for incorrectly quoted html entities in the room links to suppress warnings until TL-20224 is resolved
        $html = preg_replace('/\&(?=b(|=\d+)" id=)/', '&amp;', $html);
        // Replace semantic tags with generic tags because DOMDocument does not understand them
        $html = str_replace([ '<nav', '</nav' ], [ '<div', '</div' ], $html);
        $html = str_replace([ '<time', '</time' ], [ '<span', '</span' ], $html);

        $doc = new DOMDocument();
        $doc->loadHTML($html);
        return $doc;
    }

    /**
     * Get the nodes of the table cells of the first row.
     *
     * @param string $html
     * @param integer $expectednumcells
     * @return DOMNodeList
     */
    private function get_table_cells(string $html, int $expectednumcells) : DOMNodeList {
        $doc = self::new_domdocument($html);

        $tables = $doc->getElementsByTagName('table');
        $this->assertCount(1, $tables);

        $hdrs = $tables[0]->getElementsByTagName('thead')[0]->getElementsByTagName('tr')[0]->getElementsByTagName('th');
        $this->assertCount($expectednumcells, $hdrs);

        $rows = $tables[0]->getElementsByTagName('tbody')[0]->getElementsByTagName('tr');
        $this->assertCount(1, $rows);

        $cells = $rows[0]->getElementsByTagName('td');
        $this->assertCount($expectednumcells, $cells);

        return $cells;
    }

    /**
     * @return array
     */
    public function data_provider_for_session_list_table_waitlisted() {
        return [
            // NOTE: The HTML output is actually "<ul><li>Wait-listed</li><li>Booking open</li></ul>"
            [ false, false, 7, [ '0 / 1', 'Wait-listedBooking open', '', '', '', '' ] ],
            [ false, true, 8, [ '0 / 1', 'Wait-listedBooking open', '', '', '', '', '' ] ],
            [ true, false, 7, [ '0 / 1', 'Cancelled', '', '', '', 'Cancelled' ] ],
            [ true, true, 8, [ '0 / 1', 'Cancelled', '', '', '', 'Cancelled', '' ] ],
        ];
    }

    /**
     * Test a wait-listed event on a session list table.
     *
     * @dataProvider data_provider_for_session_list_table_waitlisted
     */
    public function test_session_list_table_waitlisted(bool $cancelled, bool $sessionattendance, int $cols, array $expections) {
        $this->resetAfterTest(true);

        $course = $this->getDataGenerator()->create_course();
        $f2f = $this->facetoface_generator->create_instance([
            'course' => $course->id,
            'sessionattendance' => (int)$sessionattendance,
        ]);
        $sessionid = $this->facetoface_generator->add_session(['facetoface' => $f2f->id, 'sessiondates' => [], 'capacity' => 1, 'cancelledstatus' => (int)$cancelled]);

        $seminarevent = new seminar_event($sessionid);
        $session = $seminarevent->to_record();
        $session->sessiondates = $seminarevent->get_sessions()->sort('timestart')->to_records(false);
        $renderer = $this->create_f2f_renderer();

        $outhtml = $renderer->print_session_list_table([ $session ], true, false, false, [], null, false, true, $sessionattendance, 0);
        $cells = $this->get_table_cells($outhtml, $cols);

        $this->assertSame($expections[0], $cells[0]->nodeValue, 'Capacity');
        $this->assertSame($expections[1], $cells[1]->nodeValue, 'Event status');
        $this->assertSame($expections[2], $cells[2]->nodeValue, 'Sign-up period');
        $this->assertSame($expections[3], $cells[3]->nodeValue, 'Session times');
        $this->assertSame($expections[4], $cells[4]->nodeValue, 'Room');
        $this->assertSame($expections[5], $cells[5]->nodeValue, 'Session status');
        // Attendance tracking table column is visible only if session attendance tracking is enabled
        if (isset($expections[6])) {
            $this->assertSame($expections[6], $cells[6]->nodeValue, 'Attendance tracking');
        }
    }

    /**
     * @return array
     */
    public function data_provider_for_session_list_table_room_sessionstatus() {
        $yes = true;
        $no_ = false;
        return [
            [ $no_, $no_, 7, -DAYSECS, [ 'Chamber', 'Session over' ], 'Past event' ],
            [ $no_, $no_, 7, -MINSECS, [ 'Chamber', 'Session in progress' ], 'In progress' ],
            [ $no_, $no_, 7, +DAYSECS, [ 'Chamber', 'Upcoming' ], 'Future event' ],
            [ $no_, $yes, 8, -DAYSECS, [ 'Chamber', 'Session over' ], 'Past event' ],
            [ $no_, $yes, 8, -MINSECS, [ 'Chamber', 'Session in progress' ], 'In progress' ],
            [ $no_, $yes, 8, +DAYSECS, [ 'Chamber', 'Upcoming' ], 'Future event' ],
            [ $yes, $no_, 7, -DAYSECS, [ 'Chamber', 'Cancelled' ], 'Past event' ],
            [ $yes, $no_, 7, -MINSECS, [ 'Chamber', 'Cancelled' ], 'In progress' ],
            [ $yes, $no_, 7, +DAYSECS, [ 'Chamber', 'Cancelled' ], 'Future event' ],
            [ $yes, $yes, 8, -DAYSECS, [ 'Chamber', 'Cancelled' ], 'Past event' ],
            [ $yes, $yes, 8, -MINSECS, [ 'Chamber', 'Cancelled' ], 'In progress' ],
            [ $yes, $yes, 8, +DAYSECS, [ 'Chamber', 'Cancelled' ], 'Future event' ],
        ];
    }

    /**
     * Test room and session status columns on a session list table.
     *
     * @dataProvider data_provider_for_session_list_table_room_sessionstatus
     */
    public function test_session_list_table_room_sessionstatus(bool $cancelled, bool $sessionattendance, int $cols, int $timestart, array $expections, string $tag) {
        $this->resetAfterTest(true);

        $now = time();
        $course = $this->getDataGenerator()->create_course();
        $f2f = $this->facetoface_generator->create_instance([
            'course' => $course->id,
            'sessionattendance' => (int)$sessionattendance,
        ]);
        $room = $this->facetoface_generator->add_site_wide_room([ 'name' => 'Chamber', 'allowconflicts' => 1 ]);
        $date = $this->prepare_date($now + $timestart, $now + $timestart + HOURSECS, $room->id);
        $sessionid = $this->facetoface_generator->add_session(['facetoface' => $f2f->id, 'sessiondates' => [$date], 'capacity' => 1, 'cancelledstatus' => (int)$cancelled ]);

        $seminarevent = new seminar_event($sessionid);
        $session = $seminarevent->to_record();
        $session->sessiondates = $seminarevent->get_sessions()->sort('timestart')->to_records(false);
        $renderer = $this->create_f2f_renderer();

        $outhtml = $renderer->print_session_list_table([ $session ], true, false, false, [], null, false, true, $sessionattendance, seminar::ATTENDANCE_TIME_ANY);
        $cells = $this->get_table_cells($outhtml, $cols);

        $this->assertSame($expections[0], $cells[4]->nodeValue, 'Room');
        $this->assertSame($expections[1], $cells[5]->nodeValue, 'Session status');
    }

    /**
     * @return array
     */
    public function data_provider_for_session_list_table_attendance_tracking_not_saved() {
        $yes = true;
        $no_ = false;
        $unlock = event_taking_attendance::UNLOCKED_SECS_PRIOR_TO_START * 2 / 3;
        return [
            [ $no_, $no_, -DAYSECS, [ 'No attendees', 'No attendees', 'No attendees' ], 'Past event' ],
            [ $no_, $no_, -MINSECS, [ 'No attendees', 'No attendees', 'No attendees' ], 'In progress' ],
            [ $no_, $no_, +$unlock, [ 'No attendees', 'No attendees', 'No attendees' ], 'Future event unlocked' ],
            [ $no_, $no_, +DAYSECS, [ 'No attendees', 'No attendees', 'No attendees' ], 'Future event locked' ],
            [ $no_, $yes, -DAYSECS, [ 'Take attendance', 'Take attendance', 'Take attendance' ], 'Past event' ],
            [ $no_, $yes, -MINSECS, [ 'Will open at session end time', 'Take attendance', 'Take attendance' ], 'In progress' ],
            [ $no_, $yes, +$unlock, [ 'Will open at session end time', 'Take attendance', 'Take attendance' ], 'Future event unlocked' ],
            [ $no_, $yes, +DAYSECS, [ 'Will open at session end time', 'Will open at session start time', 'Take attendance' ], 'Future event locked' ],
            [ $yes, $no_, -DAYSECS, [ '', '', '' ], 'Past event' ],
            [ $yes, $no_, -MINSECS, [ '', '', '' ], 'In progress' ],
            [ $yes, $no_, +$unlock, [ '', '', '' ], 'Future event unlocked' ],
            [ $yes, $no_, +DAYSECS, [ '', '', '' ], 'Future event locked' ],
            [ $yes, $yes, -DAYSECS, [ '', '', '' ], 'Past event' ],
            [ $yes, $yes, -MINSECS, [ '', '', '' ], 'In progress' ],
            [ $yes, $yes, +$unlock, [ '', '', '' ], 'Future event unlocked' ],
            [ $yes, $yes, +DAYSECS, [ '', '', '' ], 'Future event locked' ],
        ];
    }

    /**
     * Test attendance tracking columns on a session list table while not taking attendance.
     *
     * @dataProvider data_provider_for_session_list_table_attendance_tracking_not_saved
     */
    public function test_session_list_table_attendance_tracking_not_saved(bool $cancelled, bool $signupuser, int $timestart, array $expections, string $tag) {
        $this->resetAfterTest(true);

        $sessionattendance = true;
        $cols = 8;

        $now = time();
        $course = $this->getDataGenerator()->create_course();
        $f2f = $this->facetoface_generator->create_instance([
            'course' => $course->id,
            'sessionattendance' => (int)$sessionattendance,
        ]);
        $room = $this->facetoface_generator->add_site_wide_room([ 'name' => 'Chamber', 'allowconflicts' => 1 ]);

        // Create a future session so that sign-up and cancellation work
        $date = $this->prepare_date($now + DAYSECS, $now + DAYSECS * 2, $room->id);
        $sessionid = $this->facetoface_generator->add_session(['facetoface' => $f2f->id, 'sessiondates' => [$date], 'capacity' => 1 ]);

        $seminarevent = new seminar_event($sessionid);
        $this->assertCount(1, $seminarevent->get_sessions(true));

        $seminarsession = seminar_session_list::from_seminar_event($seminarevent)->current();
        /** @var seminar_session $seminarsession */

        if ($signupuser) {
            $this->create_users_signups(1, $seminarevent, booked::class);
        }

        if ($cancelled) {
            $this->assertTrue($seminarevent->cancel());
        }

        // Set the actual time to the session
        $seminarsession
            ->set_timestart($now + $timestart)
            ->set_timefinish($now + $timestart + HOURSECS)
            ->save();

        $session = $seminarevent->to_record();
        $session->sessiondates = [ $seminarsession->to_record() ];

        $renderer = $this->create_f2f_renderer();

        $outhtml = $renderer->print_session_list_table([ $session ], true, false, false, [], null, false, true, $sessionattendance, seminar::ATTENDANCE_TIME_END);
        $cells = $this->get_table_cells($outhtml, $cols);
        $this->assertSame($expections[0], $cells[6]->nodeValue);

        $outhtml = $renderer->print_session_list_table([ $session ], true, false, false, [], null, false, true, $sessionattendance, seminar::ATTENDANCE_TIME_START);
        $cells = $this->get_table_cells($outhtml, $cols);
        $this->assertSame($expections[1], $cells[6]->nodeValue);

        $outhtml = $renderer->print_session_list_table([ $session ], true, false, false, [], null, false, true, $sessionattendance, seminar::ATTENDANCE_TIME_ANY);
        $cells = $this->get_table_cells($outhtml, $cols);
        $this->assertSame($expections[2], $cells[6]->nodeValue);
    }

    /**
     * @return array
     */
    public function data_provider_for_session_list_table_attendance_tracking_saved() {
        $yes = true;
        $no_ = false;
        $noset = not_set::get_code();
        $fully = fully_attended::get_code();
        $unlock = event_taking_attendance::UNLOCKED_SECS_PRIOR_TO_START * 2 / 3;
        return [
            [ $no_, $noset, -DAYSECS, [ 'Take attendance', 'Take attendance', 'Take attendance' ], 'Past event' ],
            [ $no_, $noset, -MINSECS, [ 'Will open at session end time', 'Take attendance', 'Take attendance' ], 'In progress' ],
            [ $no_, $noset, +$unlock, [ 'Will open at session end time', 'Take attendance', 'Take attendance' ], 'Future event unlocked' ],
            [ $no_, $noset, +DAYSECS, [ 'Will open at session end time', 'Will open at session start time', 'Take attendance' ], 'Future event locked' ],
            [ $no_, $fully, -DAYSECS, [ 'Attendance saved', 'Attendance saved', 'Attendance saved' ], 'Past event' ],
            [ $no_, $fully, -MINSECS, [ 'Will open at session end time', 'Attendance saved', 'Attendance saved' ], 'In progress' ],
            [ $no_, $fully, +$unlock, [ 'Will open at session end time', 'Attendance saved', 'Attendance saved' ], 'Future event unlocked' ],
            [ $no_, $fully, +DAYSECS, [ 'Will open at session end time', 'Will open at session start time', 'Attendance saved' ], 'Future event locked' ],
            [ $yes, $noset, -DAYSECS, [ '', '', '' ], 'Past event' ],
            [ $yes, $noset, -MINSECS, [ '', '', '' ], 'In progress' ],
            [ $yes, $noset, +$unlock, [ '', '', '' ], 'Future event unlocked' ],
            [ $yes, $noset, +DAYSECS, [ '', '', '' ], 'Future event locked' ],
            [ $yes, $fully, -DAYSECS, [ '', '', '' ], 'Past event' ],
            [ $yes, $fully, -MINSECS, [ '', '', '' ], 'In progress' ],
            [ $yes, $fully, +$unlock, [ '', '', '' ], 'Future event unlocked' ],
            [ $yes, $fully, +DAYSECS, [ '', '', '' ], 'Future event locked' ],
        ];
    }

    /**
     * Test attendance tracking columns on a session list table when taking attendance.
     *
     * @dataProvider data_provider_for_session_list_table_attendance_tracking_saved
     */
    public function test_session_list_table_attendance_tracking_saved(bool $cancelled, int $attendancecode, int $timestart, array $expections, string $tag) {
        $this->resetAfterTest(true);

        $sessionattendance = true;
        $cols = 8;

        $now = time();
        $course = $this->getDataGenerator()->create_course();
        $f2f = $this->facetoface_generator->create_instance([
            'course' => $course->id,
            'sessionattendance' => (int)$sessionattendance,
            'attendancetime' => seminar::ATTENDANCE_TIME_ANY,
        ]);
        $room = $this->facetoface_generator->add_site_wide_room([ 'name' => 'Chamber', 'allowconflicts' => 1 ]);

        // Create a future session so that sign-up and cancellation work
        $date = $this->prepare_date($now + DAYSECS, $now + DAYSECS * 2, $room->id);
        $sessionid = $this->facetoface_generator->add_session(['facetoface' => $f2f->id, 'sessiondates' => [$date], 'capacity' => 1 ]);

        $seminarevent = new seminar_event($sessionid);
        $this->assertCount(1, $seminarevent->get_sessions(true));

        $seminarsession = seminar_session_list::from_seminar_event($seminarevent)->current();
        /** @var seminar_session $seminarsession */

        $signup = current($this->create_users_signups(1, $seminarevent, booked::class));

        $this->assertTrue(attendance_helper::process_session_attendance([$signup->get_id() => $attendancecode], $seminarsession->get_id()));

        if ($cancelled) {
            $this->assertTrue($seminarevent->cancel());
        }

        // Set the actual time to the session
        $seminarsession
            ->set_timestart($now + $timestart)
            ->set_timefinish($now + $timestart + HOURSECS)
            ->save();

        $session = $seminarevent->to_record();
        $session->sessiondates = [ $seminarsession->to_record() ];

        $renderer = $this->create_f2f_renderer();

        $outhtml = $renderer->print_session_list_table([ $session ], true, false, false, [], null, false, true, $sessionattendance, seminar::ATTENDANCE_TIME_END);
        $cells = $this->get_table_cells($outhtml, $cols);
        $this->assertSame($expections[0], $cells[6]->nodeValue);

        $outhtml = $renderer->print_session_list_table([ $session ], true, false, false, [], null, false, true, $sessionattendance, seminar::ATTENDANCE_TIME_START);
        $cells = $this->get_table_cells($outhtml, $cols);
        $this->assertSame($expections[1], $cells[6]->nodeValue);

        $outhtml = $renderer->print_session_list_table([ $session ], true, false, false, [], null, false, true, $sessionattendance, seminar::ATTENDANCE_TIME_ANY);
        $cells = $this->get_table_cells($outhtml, $cols);
        $this->assertSame($expections[2], $cells[6]->nodeValue);
    }

    /**
     * Ensure that print_session_list_table() renders the list using the given $sessions parameter.
     */
    public function test_print_session_list_table_with_crafted_records() {
        global $DB;
        /** @var \moodle_database $DB */
        $this->resetAfterTest(true);

        $course = $this->getDataGenerator()->create_course();
        $f2f = $this->facetoface_generator->create_instance(['course' => $course->id]);
        $room1 = $this->facetoface_generator->add_custom_room([ 'name' => 'Chamber', 'allowconflicts' => 1 ]);
        $room2 = $this->facetoface_generator->add_custom_room([ 'name' => 'Arena', 'allowconflicts' => 1 ]);

        // Create a bunch of sessions
        $date1 = $this->prepare_date('1 Dec last year', '1 Jan next year', $room1->id);
        $date2 = $this->prepare_date('1 Feb next year', '1 Feb next year +1 hour', $room1->id);
        $date3 = $this->prepare_date('1 Mar next year', '1 Mar next year +1 hour', $room1->id);
        $date4 = $this->prepare_date('1 Apr next year', '1 Apr next year +1 hour', $room1->id);
        $date5 = $this->prepare_date('1 Aug last year', '1 Aug last year +1 hour', $room1->id);
        $date6 = $this->prepare_date('1 Sep last year', '1 Sep last year +1 hour', $room1->id);
        $session1id = $this->facetoface_generator->add_session(['facetoface' => $f2f->id, 'sessiondates' => [$date1, $date2, $date3], 'capacity' => 10 ]);
        $session2id = $this->facetoface_generator->add_session(['facetoface' => $f2f->id, 'sessiondates' => [], 'capacity' => 10 ]);
        $session3id = $this->facetoface_generator->add_session(['facetoface' => $f2f->id, 'sessiondates' => [$date4], 'capacity' => 10 ]);
        $session4id = $this->facetoface_generator->add_session(['facetoface' => $f2f->id, 'sessiondates' => [$date5], 'capacity' => 10 ]);
        $session5id = $this->facetoface_generator->add_session(['facetoface' => $f2f->id, 'sessiondates' => [$date6], 'capacity' => 10 ]);

        // Make the session list in the order of [ future, far_past, ongoing, waitlisted, past ]
        $sessions = [];
        $sessions[$session3id] = seminar_event_helper::get_sessiondata(new seminar_event($session3id), null);
        $sessions[$session4id] = seminar_event_helper::get_sessiondata(new seminar_event($session4id), null);
        $sessions[$session1id] = seminar_event_helper::get_sessiondata(new seminar_event($session1id), null, true);
        $sessions[$session2id] = seminar_event_helper::get_sessiondata(new seminar_event($session2id), null);
        $sessions[$session5id] = seminar_event_helper::get_sessiondata(new seminar_event($session5id), null);

        // .. and shuffle session dates of the ongoing event as [ $date2, $date3, $date1 ]
        $firstid = array_key_first($sessions[$session1id]->sessiondates);
        $firstrecord = array_shift($sessions[$session1id]->sessiondates);
        $sessions[$session1id]->sessiondates[$firstid] = $firstrecord;

        // .. and change the room of one event to Arena
        $DB->set_field('facetoface_sessions_dates', 'roomid', $room2->id, ['id' => $firstid]);

        $renderer = $this->create_f2f_renderer();
        $html = $renderer->print_session_list_table($sessions, false, true, false, array(), null, true, false, false, 0, false, false);

        // .. then the database contains Arena while our $sessions don't
        $this->assertContains('Chamber', $html);
        $this->assertNotContains('Arena', $html);

        $doc = self::new_domdocument($html);
        $tables = $doc->getElementsByTagName('table');
        $this->assertCount(1, $tables);
        $rows = $tables[0]->getElementsByTagName('tbody')[0]->getElementsByTagName('tr');
        $this->assertCount(7, $rows);

        /** @var DOMNode[] $rows */
        // .. and the list is sorted by custom order
        $this->assertContains('April', $rows[0]->nodeValue);
        $this->assertContains('August', $rows[1]->nodeValue);
        $this->assertContains('February', $rows[2]->nodeValue);
        $this->assertContains('March', $rows[3]->nodeValue);
        $this->assertContains('December', $rows[4]->nodeValue);
        $this->assertContains('Wait-listed', $rows[5]->nodeValue);
        $this->assertContains('September', $rows[6]->nodeValue);
    }
}
