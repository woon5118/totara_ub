<?php
/*
 * This file is part of Totara Learn
 *
 * Copyright (C) 2020 onwards Totara Learning Solutions LTD
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
 * @author  Oleg Demeshev <oleg.demeshev@totaralearning.com>
 * @package mod_facetoface
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');
}

global $CFG;
require_once($CFG->dirroot.'/mod/facetoface/lib.php');
require_once($CFG->dirroot.'/mod/facetoface/tests/facetoface_testcase.php');

use mod_facetoface\signup;
use mod_facetoface\signup_helper;
use mod_facetoface\seminar_event;
use mod_facetoface\attendance\attendance_helper;
use mod_facetoface\task\send_notifications_task;

class mod_facetoface_send_notification_task_manual_testcase extends mod_facetoface_facetoface_testcase {

    /**
     * Test manual notifications
     */
    public function test_send_upcoming_notifications() {
        global $DB;

        $seed = $this->seed_data();

        $sink = $this->redirectEmails();
        $cron = new send_notifications_task();
        $cron->testing = true;

        // Signup, and clear automated message (booking confirmation).
        $signup = signup::create($seed['users'][0]->id, $seed['seminarevent']);
        signup_helper::signup($signup);
        $cron->execute();
        $this->execute_adhoc_tasks();

        $sink->clear();

        // Make notification manual
        $notificationrec = $DB->get_record('facetoface_notification', ['conditiontype'=> 32]);
        $notificationrec->type = MDL_F2F_NOTIFICATION_MANUAL;
        $notificationrec->issent = 0;
        $notificationrec->status = 1;
        $notificationrec->recipients = json_encode(
            [
                'past_events' => 0,
                'events_in_progress' => 0,
                'upcoming_events' => 1,
                'fully_attended' => 0,
                'partially_attended' => 0,
                'unable_to_attend' => 0,
                'no_show' => 0,
                'waitlisted' => 0,
                'user_cancelled' => 0,
                'requested' => 0,
            ]
        );
        $notificationrec->title = 'TEST';
        $DB->update_record('facetoface_notification', $notificationrec);

        $cron->execute();
        $this->execute_adhoc_tasks();

        $messages = $sink->get_messages();
        $sink->clear();
        $this->assertCount(1, $messages);
        $message = current($messages);
        $this->assertEquals('TEST', $message->subject);
        $this->assertEquals('test@example.com', $message->to);

        // Confirm that messages sent only once
        $cron->execute();
        $this->execute_adhoc_tasks();
        $this->assertEmpty($sink->get_messages());
        $sink->close();
    }

    /**
     * Test manual notifications
     */
    public function test_send_past_notifications() {
        global $DB;

        $seed = $this->seed_data();

        $sink = $this->redirectEmails();
        $cron = new send_notifications_task();
        $cron->testing = true;

        // Signup, and clear automated message (booking confirmation).
        $signup = signup::create($seed['users'][0]->id, $seed['seminarevent']);
        signup_helper::signup($signup);
        $cron->execute();
        $this->execute_adhoc_tasks();

        $sink->clear();

        $sessiondate = $DB->get_record('facetoface_sessions_dates', ['sessionid' => $seed['seminarevent']->get_id()]);
        $sessiondate->timestart = time() - DAYSECS;
        $sessiondate->timefinish = time() - DAYSECS + 60;
        $DB->update_record('facetoface_sessions_dates', $sessiondate);

        // Make notification manual
        $notificationrec = $DB->get_record('facetoface_notification', ['conditiontype'=> 32]);
        $notificationrec->type = MDL_F2F_NOTIFICATION_MANUAL;
        $notificationrec->issent = 0;
        $notificationrec->status = 1;
        $notificationrec->recipients = json_encode(
            [
                'past_events' => 1,
                'events_in_progress' => 0,
                'upcoming_events' => 0,
                'fully_attended' => 0,
                'partially_attended' => 0,
                'unable_to_attend' => 0,
                'no_show' => 0,
                'waitlisted' => 0,
                'user_cancelled' => 0,
                'requested' => 0,
            ]
        );
        $notificationrec->title = 'TEST';
        $DB->update_record('facetoface_notification', $notificationrec);

        $cron->execute();
        $this->execute_adhoc_tasks();

        $messages = $sink->get_messages();
        $sink->clear();
        $this->assertCount(1, $messages);
        $message = current($messages);
        $this->assertEquals('TEST', $message->subject);
        $this->assertEquals('test@example.com', $message->to);

        // Confirm that messages sent only once
        $cron->execute();
        $this->execute_adhoc_tasks();
        $this->assertEmpty($sink->get_messages());
        $sink->close();
    }

    public function test_send_in_progress_notifications() {
        global $DB;

        $seed = $this->seed_data();

        $sink = $this->redirectEmails();
        $cron = new send_notifications_task();
        $cron->testing = true;

        // Signup, and clear automated message (booking confirmation).
        $signup = signup::create($seed['users'][0]->id, $seed['seminarevent']);
        signup_helper::signup($signup);
        $cron->execute();
        $this->execute_adhoc_tasks();

        $sink->clear();

        $sessiondate = $DB->get_record('facetoface_sessions_dates', ['sessionid' => $seed['seminarevent']->get_id()]);
        $sessiondate->timestart = time() - DAYSECS;
        $sessiondate->timefinish = time() + DAYSECS + 60;
        $DB->update_record('facetoface_sessions_dates', $sessiondate);

        // Make notification manual
        $notificationrec = $DB->get_record('facetoface_notification', ['conditiontype'=> 32]);
        $notificationrec->type = MDL_F2F_NOTIFICATION_MANUAL;
        $notificationrec->issent = 0;
        $notificationrec->status = 1;
        $notificationrec->recipients = json_encode(
            [
                'past_events' => 0,
                'events_in_progress' => 1,
                'upcoming_events' => 0,
                'fully_attended' => 0,
                'partially_attended' => 0,
                'unable_to_attend' => 0,
                'no_show' => 0,
                'waitlisted' => 0,
                'user_cancelled' => 0,
                'requested' => 0,
            ]
        );
        $notificationrec->title = 'TEST';
        $DB->update_record('facetoface_notification', $notificationrec);

        $cron->execute();
        $this->execute_adhoc_tasks();

        $messages = $sink->get_messages();
        $sink->clear();
        $this->assertCount(1, $messages);
        $message = current($messages);
        $this->assertEquals('TEST', $message->subject);
        $this->assertEquals('test@example.com', $message->to);

        // Confirm that messages sent only once
        $cron->execute();
        $this->execute_adhoc_tasks();
        $this->assertEmpty($sink->get_messages());
        $sink->close();
    }

    /**
     * Prepare course, seminar, event, session, three users enrolled on course.
     */
    protected function seed_data() {
        $course1 = $this->getDataGenerator()->create_course();
        $facetofacegenerator = $this->getDataGenerator()->get_plugin_generator('mod_facetoface');
        $facetofacedata = array(
            'name' => 'facetoface1',
            'course' => $course1->id
        );
        $facetoface1 = $facetofacegenerator->create_instance($facetofacedata);

        // Session that starts in 24hrs time.
        // This session should trigger a mincapacity warning now as cutoff is 24:01 hrs before start time.
        $sessiondate = new stdClass();
        $sessiondate->timestart = time() + DAYSECS;
        $sessiondate->timefinish = time() + DAYSECS + 60;
        $sessiondate->sessiontimezone = 'Pacific/Auckland';

        $sessiondata = array(
            'facetoface' => $facetoface1->id,
            'capacity' => 3,
            'allowoverbook' => 1,
            'sessiondates' => array($sessiondate),
            'mincapacity' => '1',
            'cutoff' => DAYSECS - 60
        );
        $sessionid = $facetofacegenerator->add_session($sessiondata);

        $student1 = $this->getDataGenerator()->create_user(['email' => 'test@example.com']);
        $student2 = $this->getDataGenerator()->create_user();
        $student3 = $this->getDataGenerator()->create_user();

        $this->getDataGenerator()->enrol_user($student1->id, $course1->id, 'student');
        $this->getDataGenerator()->enrol_user($student2->id, $course1->id, 'student');
        $this->getDataGenerator()->enrol_user($student3->id, $course1->id, 'student');

        return [
            'course' => $course1,
            'seminarevent' => new seminar_event($sessionid),
            'users' => [$student1, $student2, $student3]
        ];
    }
}