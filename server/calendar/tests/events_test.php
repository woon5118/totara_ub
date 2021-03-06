<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * This file contains the class that handles testing of the calendar events.
 *
 * @package core_calendar
 * @copyright 2014 Ankit Agarwal <ankit.agrr@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
global $CFG;
require_once($CFG->dirroot . '/calendar/tests/externallib_test.php');

/**
 * This file contains the class that handles testing of the calendar events.
 *
 * @package core_calendar
 * @copyright 2014 Ankit Agarwal <ankit.agrr@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_calendar_events_testcase extends advanced_testcase {

    /**
     * The test user.
     */
    private $user;

    /**
     * The test course.
     */
    private $course;

    protected function tearDown(): void {
        $this->user = null;
        $this->course = null;
        parent::tearDown();
    }

    /**
     * Test set up.
     */
    protected function setUp(): void {
        global $USER;
        // The user we are going to test this on.
        $this->setAdminUser();
        $this->user = $USER;
        $this->course = self::getDataGenerator()->create_course();
    }

    /**
     * Tests for calendar_event_created event.
     */
    public function test_calendar_event_created() {

        $this->resetAfterTest();

        // Catch the events.
        $sink = $this->redirectEvents();

        // Create a calendar event.
        $record = new stdClass();
        $record->courseid = 0;
        $time = time();
        $calevent = core_calendar_externallib_testcase::create_calendar_event('event', $this->user->id, 'user', 0, $time,
                $record); // User event.

        // Capture the event.
        $events = $sink->get_events();
        $sink->clear();

        // Validate the event.
        $event = $events[0];
        $this->assertInstanceOf('\core\event\calendar_event_created', $event);
        $this->assertEquals('event', $event->objecttable);
        $this->assertEquals(0, $event->courseid);
        $this->assertEquals($calevent->context, $event->get_context());
        $expectedlog = array(0, 'calendar', 'add', 'event.php?action=edit&amp;id=' . $calevent->id , $calevent->name);
        $other = array('repeatid' => 0, 'timestart' => $time, 'name' => 'event');
        $this->assertEquals($other, $event->other);
        $this->assertEventLegacyLogData($expectedlog, $event);
        $this->assertEventContextNotUsed($event);

        // Now we create a repeated course event.
        $record = new stdClass();
        $record->courseid = $this->course->id;
        $calevent = core_calendar_externallib_testcase::create_calendar_event('course', $this->user->id, 'course', 10, $time,
                $record);
        $events = $sink->get_events();
        $sink->close();

        $this->assertEquals(10, count($events));
        foreach ($events as $event) {
            $this->assertInstanceOf('\core\event\calendar_event_created', $event);
            $this->assertEquals('event', $event->objecttable);
            $this->assertEquals($this->course->id, $event->courseid);
            $this->assertEquals($calevent->context, $event->get_context());
        }
    }

    /**
     * Tests for event validations related to calendar_event_created event.
     */
    public function test_calendar_event_created_validations() {
        $this->resetAfterTest();
        $context = context_user::instance($this->user->id);

        // Test not setting other['repeatid'].
        try {
            \core\event\calendar_event_created::create(array(
                'context'  => $context,
                'objectid' => 2,
                'other' => array(
                    'timestart' => time(),
                    'name' => 'event'
                )
            ));
            $this->fail("Event validation should not allow \\core\\event\\calendar_event_created to be triggered without
                    other['repeatid']");
        } catch (coding_exception $e) {
            $this->assertStringContainsString('The \'repeatid\' value must be set in other.', $e->getMessage());
        }

        // Test not setting other['name'].
        try {
            \core\event\calendar_event_created::create(array(
                'context'  => $context,
                'objectid' => 2,
                'other' => array(
                    'repeatid' => 0,
                    'timestart' => time(),
                )
            ));
            $this->fail("Event validation should not allow \\core\\event\\calendar_event_created to be triggered without
                    other['name']");
        } catch (coding_exception $e) {
            $this->assertStringContainsString('The \'name\' value must be set in other.', $e->getMessage());
        }

        // Test not setting other['timestart'].
        try {
            \core\event\calendar_event_created::create(array(
                'context'  => $context,
                'objectid' => 2,
                'other' => array(
                    'name' => 'event',
                    'repeatid' => 0,
                )
            ));
            $this->fail("Event validation should not allow \\core\\event\\calendar_event_deleted to be triggered without
                    other['timestart']");
        } catch (coding_exception $e) {
            $this->assertStringContainsString('The \'timestart\' value must be set in other.', $e->getMessage());
        }
    }

    /**
     * Tests for calendar_event_updated event.
     */
    public function test_calendar_event_updated() {

        $this->resetAfterTest();

        // Create a calendar event.
        $record = new stdClass();
        $record->courseid = 0;
        $time = time();
        $calevent = core_calendar_externallib_testcase::create_calendar_event('event', $this->user->id, 'user', 0, $time,
                $record); // User event.

        // Catch the events.
        $sink = $this->redirectEvents();
        $prop = new stdClass();
        $prop->name = 'new event';
        $calevent->update($prop); // Update calender event.
        // Capture the event.
        $events = $sink->get_events();

        // Validate the event.
        $event = $events[0];
        $this->assertInstanceOf('\core\event\calendar_event_updated', $event);
        $this->assertEquals('event', $event->objecttable);
        $this->assertEquals(0, $event->courseid);
        $this->assertEquals($calevent->context, $event->get_context());
        $expectedlog = array(0, 'calendar', 'edit', 'event.php?action=edit&amp;id=' . $calevent->id , $calevent->name);
        $this->assertEventLegacyLogData($expectedlog, $event);
        $other = array('repeatid' => 0, 'timestart' => $time, 'name' => 'new event');
        $this->assertEquals($other, $event->other);
        $this->assertEventContextNotUsed($event);

        // Now we create a repeated course event and update it.
        $record = new stdClass();
        $record->courseid = $this->course->id;
        $calevent = core_calendar_externallib_testcase::create_calendar_event('course', $this->user->id, 'course', 10, time(),
                $record);

        $sink->clear();
        $prop = new stdClass();
        $prop->name = 'new event';
        $prop->repeateditall = true;
        $calevent->update($prop); // Update calender event.
        $events = $sink->get_events();
        $sink->close();

        $this->assertEquals(10, count($events));
        foreach ($events as $event) {
            $this->assertInstanceOf('\core\event\calendar_event_updated', $event);
            $this->assertEquals('event', $event->objecttable);
            $this->assertEquals($this->course->id, $event->courseid);
            $this->assertEquals($calevent->context, $event->get_context());
        }
    }

    /**
     * Tests for event validations related to calendar_event_created event.
     */
    public function test_calendar_event_updated_validations() {
        $this->resetAfterTest();
        $context = context_user::instance($this->user->id);

        // Test not setting other['repeatid'].
        try {
            \core\event\calendar_event_updated::create(array(
                'context'  => $context,
                'objectid' => 2,
                'other' => array(
                    'timestart' => time(),
                    'name' => 'event'
                )
            ));
            $this->fail("Event validation should not allow \\core\\event\\calendar_event_updated to be triggered without
                    other['repeatid']");
        } catch (coding_exception $e) {
            $this->assertStringContainsString('The \'repeatid\' value must be set in other.', $e->getMessage());
        }

        // Test not setting other['name'].
        try {
            \core\event\calendar_event_updated::create(array(
                'context'  => $context,
                'objectid' => 2,
                'other' => array(
                    'repeatid' => 0,
                    'timestart' => time(),
                )
            ));
            $this->fail("Event validation should not allow \\core\\event\\calendar_event_updated to be triggered without
                    other['name']");
        } catch (coding_exception $e) {
            $this->assertStringContainsString('The \'name\' value must be set in other.', $e->getMessage());
        }

        // Test not setting other['timestart'].
        try {
            \core\event\calendar_event_updated::create(array(
                'context'  => $context,
                'objectid' => 2,
                'other' => array(
                    'name' => 'event',
                    'repeatid' => 0,
                )
            ));
            $this->fail("Event validation should not allow \\core\\event\\calendar_event_deleted to be triggered without
                    other['timestart']");
        } catch (coding_exception $e) {
            $this->assertStringContainsString('The \'timestart\' value must be set in other.', $e->getMessage());
        }
    }

    /**
     * Tests for calendar_event_deleted event.
     */
    public function test_calendar_event_deleted() {
        global $DB;

        $this->resetAfterTest();

        // Create a calendar event.
        $record = new stdClass();
        $record->courseid = 0;
        $record->repeatid = 0;
        $time = time();
        $calevent = core_calendar_externallib_testcase::create_calendar_event('event', $this->user->id, 'user', 0, $time,
            $record); // User event.
        $dbrecord = $DB->get_record('event', array('id' => $calevent->id), '*', MUST_EXIST);

        // Catch the events.
        $sink = $this->redirectEvents();
        $calevent->delete(false);
        $events = $sink->get_events();

        // Validate the event.
        $event = $events[0];
        $this->assertInstanceOf('\core\event\calendar_event_deleted', $event);
        $this->assertEquals('event', $event->objecttable);
        $this->assertEquals(0, $event->courseid);
        $this->assertEquals($calevent->context, $event->get_context());
        $other = array('repeatid' => 0, 'timestart' => $time, 'name' => 'event');
        $this->assertEquals($other, $event->other);
        $this->assertEventContextNotUsed($event);
        $this->assertEquals($dbrecord, $event->get_record_snapshot('event', $event->objectid));

        // Now we create a repeated course event and delete it.
        $record = new stdClass();
        $record->courseid = $this->course->id;
        $calevent = core_calendar_externallib_testcase::create_calendar_event('course', $this->user->id, 'course', 10, time(),
            $record);

        $sink->clear();
        $prop = new stdClass();
        $prop->name = 'new event';
        $prop->repeateditall = true;
        $calevent->delete(true);
        $events = $sink->get_events();
        $sink->close();

        $this->assertEquals(10, count($events));
        foreach ($events as $event) {
            $this->assertInstanceOf('\core\event\calendar_event_deleted', $event);
            $this->assertEquals('event', $event->objecttable);
            $this->assertEquals($this->course->id, $event->courseid);
            $this->assertEquals($calevent->context, $event->get_context());
        }
    }

    /**
     * Tests for event validations related to calendar_event_deleted event.
     */
    public function test_calendar_event_deleted_validations() {
        $this->resetAfterTest();
        $context = context_user::instance($this->user->id);

        // Test not setting other['repeatid'].
        try {
            \core\event\calendar_event_deleted::create(array(
                'context'  => $context,
                'objectid' => 2,
                'other' => array(
                    'timestart' => time(),
                    'name' => 'event'
                )
            ));
            $this->fail("Event validation should not allow \\core\\event\\calendar_event_deleted to be triggered without
                    other['repeatid']");
        } catch (coding_exception $e) {
            $this->assertStringContainsString('The \'repeatid\' value must be set in other.', $e->getMessage());
        }

        // Test not setting other['name'].
        try {
            \core\event\calendar_event_deleted::create(array(
                'context'  => $context,
                'objectid' => 2,
                'other' => array(
                    'repeatid' => 0,
                    'timestart' => time(),
                )
            ));
            $this->fail("Event validation should not allow \\core\\event\\calendar_event_deleted to be triggered without
                    other['name']");
        } catch (coding_exception $e) {
            $this->assertStringContainsString('The \'name\' value must be set in other.', $e->getMessage());
        }

        // Test not setting other['timestart'].
        try {
            \core\event\calendar_event_deleted::create(array(
                'context'  => $context,
                'objectid' => 2,
                'other' => array(
                    'name' => 'event',
                    'repeatid' => 0,
                )
            ));
            $this->fail("Event validation should not allow \\core\\event\\calendar_event_deleted to be triggered without
                    other['timestart']");
        } catch (coding_exception $e) {
            $this->assertStringContainsString('The \'timestart\' value must be set in other.', $e->getMessage());
        }
    }

    /**
     * Tests for calendar_subscription_added event.
     */
    public function test_calendar_subscription_created() {
        global $CFG;
        require_once($CFG->dirroot . '/calendar/lib.php');
        $this->resetAfterTest(true);

        // Create a mock subscription.
        $subscription = new stdClass();
        $subscription->eventtype = 'site';
        $subscription->name = 'test';
        $subscription->courseid = $this->course->id;

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $id = calendar_add_subscription($subscription);

        $events = $sink->get_events();
        $event = reset($events);
        // Check that the event data is valid.
        $this->assertInstanceOf('\core\event\calendar_subscription_created', $event);
        $this->assertEquals($id, $event->objectid);
        $this->assertEquals($subscription->courseid, $event->other['courseid']);
        $this->assertEquals($subscription->eventtype, $event->other['eventtype']);
        $this->assertDebuggingNotCalled();
        $sink->close();

    }

    /**
     * Tests for calendar_subscription_updated event.
     */
    public function test_calendar_subscription_updated() {
        global $CFG;
        require_once($CFG->dirroot . '/calendar/lib.php');
        $this->resetAfterTest(true);

        // Create a mock subscription.
        $subscription = new stdClass();
        $subscription->eventtype = 'site';
        $subscription->name = 'test';
        $subscription->courseid = $this->course->id;
        $subscription->id = calendar_add_subscription($subscription);
        // Now edit it.
        $subscription->name = 'awesome';

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        calendar_update_subscription($subscription);
        $events = $sink->get_events();
        $event = reset($events);
        // Check that the event data is valid.
        $this->assertInstanceOf('\core\event\calendar_subscription_updated', $event);
        $this->assertEquals($subscription->id, $event->objectid);
        $this->assertEquals($subscription->courseid, $event->other['courseid']);
        $this->assertEquals($subscription->eventtype, $event->other['eventtype']);
        $this->assertDebuggingNotCalled();
        $sink->close();

    }

    /**
     * Tests for calendar_subscription_deleted event.
     */
    public function test_calendar_subscription_deleted() {
        global $CFG;
        require_once($CFG->dirroot . '/calendar/lib.php');
        $this->resetAfterTest(true);

        // Create a mock subscription.
        $subscription = new stdClass();
        $subscription->eventtype = 'site';
        $subscription->name = 'test';
        $subscription->courseid = $this->course->id;
        $subscription->id = calendar_add_subscription($subscription);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        calendar_delete_subscription($subscription);
        $events = $sink->get_events();
        $event = reset($events);
        // Check that the event data is valid.
        $this->assertInstanceOf('\core\event\calendar_subscription_deleted', $event);
        $this->assertEquals($subscription->id, $event->objectid);
        $this->assertEquals($subscription->courseid, $event->other['courseid']);
        $this->assertDebuggingNotCalled();
        $sink->close();

    }
}
