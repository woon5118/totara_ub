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
 * Tests importing generated from a csv file
 *
 * To test, run this from the command line from the $CFG->dirroot
 * vendor/bin/phpunit completion/tests/course_completion_test.php
 *
 * @package    completion
 * @subpackage tests
 * @author     Maria Torres <maria.torres@totaralms.com>
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/completion/cron.php');
require_once($CFG->dirroot . '/mod/certificate/locallib.php');
require_once($CFG->dirroot . '/totara/reportbuilder/tests/reportcache_advanced_testcase.php');
require_once($CFG->dirroot . '/completion/criteria/completion_criteria_activity.php');

define('COMPLETION_TEST_COURSES_CREATED', 3);

class core_completion_course_completion_testcase extends reportcache_advanced_testcase {

    public $user1, $user2, $user3, $users;
    public $program;
    public $course1, $course2, $course3;
    public $certificate1, $certificate2, $certificate3;
    public $coursemodule1, $coursemodule2, $coursemodule3;
    public $completioninfo1, $completioninfo2, $completioninfo3;

    /** This setUp will create: three users (user1, user2, user3), three courses (course1, course2, course3),
     *  one certification program with course1 as certification content path and course2 as re-certification path.
     *  Each course will have a certificate activity which will be used as a criterion for completion.
     *  The enrollments will be as follow:
     *  user1 will be enrolled to course1 and course2 via certification program and course3 via manual,
     *  user2 will be enrolled to course1 and course2 via certification program and
     *  user3 will be enrolled to the course1 and course3 via manual.
     */
    protected function setUp() {
        global $CFG, $DB;
        parent::setup();

        $this->resetAfterTest();
        $CFG->enablecompletion = true;

        $this->assertEquals(2, $DB->count_records('user')); // Guest + Admin

        // Create three users.
        $this->user1 = $this->getDataGenerator()->create_user();
        $this->user2 = $this->getDataGenerator()->create_user();
        $this->user3 = $this->getDataGenerator()->create_user();
        $this->users = array($this->user1, $this->user2, $this->user3);

        // Verify users were created.
        $this->assertEquals(5, $DB->count_records('user')); // Guest + Admin + these users.

        // Set default settings for courses.
        $coursedefaults = array(
            'enablecompletion' => COMPLETION_ENABLED,
            'completionstartonenrol' => 1,
            'completionprogressonview' => 1);

        // Create three courses
        for ($i = 1; $i <= COMPLETION_TEST_COURSES_CREATED; $i++) {
            $this->{"course".$i} = $this->getDataGenerator()->create_course($coursedefaults, array('createsections' => true));
            $this->{"completioninfo".$i} = new completion_info($this->{"course".$i});
            $this->assertEquals(COMPLETION_ENABLED, $this->{"completioninfo".$i}->is_enabled());
        }

        // Verify there isn't any certificate activity.
        $this->assertEquals(0, $DB->count_records('certificate'));

        // Assign a certificate activity to each course. Could be any other activity. It's necessary for the criteria completion.
        $completiondefaults = array(
            'completion' => COMPLETION_TRACKING_AUTOMATIC,
            'completionview' => COMPLETION_VIEW_REQUIRED
        );
        for ($i = 1; $i <= COMPLETION_TEST_COURSES_CREATED; $i++) {
            $courseid = $this->{"course".$i}->id;
            $this->{"certificate".$i} = $this->getDataGenerator()->create_module(
                'certificate',
                array('course' => $courseid),
                $completiondefaults
            );
            $this->{"coursemodule".$i} = get_coursemodule_from_instance('certificate', $this->{"certificate".$i}->id, $courseid);
            $this->assertEquals(COMPLETION_TRACKING_AUTOMATIC, $this->{"completioninfo".$i}->is_enabled($this->{"coursemodule".$i}));
        }
        $this->assertEquals(3, $DB->count_records('certificate'));

        // Create completion based on the certificate activity that each course has.
        /* @var core_completion_generator $cgen */
        $cgen = $this->getDataGenerator()->get_plugin_generator('core_completion');

        for ($i = 1; $i <= COMPLETION_TEST_COURSES_CREATED; $i++) {
            $cgen->enable_completion_tracking($this->{"course".$i});
            $cgen->set_activity_completion($this->{"course".$i}->id, array($this->{"certificate".$i}));
        }

        // Create a certification program.
        $data = array(
            'cert_learningcomptype' => CERTIFTYPE_PROGRAM,
            'cert_activeperiod' => '3 day',
            'cert_windowperiod' => '3 day',
            'cert_recertifydatetype' => CERTIFRECERT_COMPLETION,
            'cert_timemodified' => time(),
            'prog_fullname' => 'Certification Program1',
            'prog_shortname' => 'CP1',
        );
        $this->assertEquals(0, $DB->count_records('certif'), "Certif table isn't empty");
        $this->assertEquals(0, $DB->count_records('prog'), "Prog table isn't empty");
        $this->program = $this->getDataGenerator()->create_certification($data);
        $this->assertEquals(1, $DB->count_records('certif'),'Record count mismatch for certif');
        $this->assertEquals(1, $DB->count_records('prog'), "Record count mismatch for prog");

        // Add course1 and course2 as part of the certification's content.
        $this->getDataGenerator()->add_courseset_program($this->program->id, array($this->course1->id), CERTIFPATH_CERT);
        $this->getDataGenerator()->add_courseset_program($this->program->id, array($this->course2->id), CERTIFPATH_RECERT);
        $this->assertEquals(2, $DB->count_records('prog_courseset_course'), 'Record count mismatch for coursetsets in certification');

        $sink = $this->redirectMessages();
        // Enrol user1 and user2 to the certification program.
        $this->getDataGenerator()->assign_program($this->program->id, array($this->user1->id, $this->user2->id));
        $sink->close();

        // Enrol user1, user2 and user3 to the course1 ... and user1 and user3 to course3 (via manual).
        $this->getDataGenerator()->enrol_user($this->user1->id, $this->course1->id);
        $this->getDataGenerator()->enrol_user($this->user2->id, $this->course1->id);
        $this->getDataGenerator()->enrol_user($this->user3->id, $this->course1->id);
        $this->getDataGenerator()->enrol_user($this->user1->id, $this->course3->id);
        $this->getDataGenerator()->enrol_user($this->user3->id, $this->course3->id);
        $this->assertEquals(5, $DB->count_records('user_enrolments'), 'Record count mismatch for enrollments');
    }


    /**
     * Make sure that mark_inprogress triggers the course_in_progress event.
     */
    public function test_course_in_progress() {
        $this->resetAfterTest();

        $ccdetails = array(
            'course' => $this->course2->id,
            'userid' => $this->user3->id,
        );

        $cc = new completion_completion($ccdetails);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $cc->mark_inprogress(time());
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        $this->assertInstanceOf('\core\event\course_in_progress', $event);
        $this->assertEquals($this->course2->id, $event->courseid);
        $this->assertEquals($this->user3->id, $event->relateduserid);
    }

    /** This function will make users to complete the courses via criteria completion by viewing the certificate activity,
     *  make the user1 to complete the certification program one day before today and run the certification_cron to open
     *  the re-certification window. So, we can test that criteria completion records are deleted (when the cron runs)
     *  for the users who complete courses in the certification program path and not for all users enrolled in that course.
     */
    public function test_course_completion() {
        global $DB;

        $this->resetAfterTest(true);
        $this->setAdminUser();

        // Check there isn't data in course_completion_crit_compl.
        $this->assertEquals(0, $DB->count_records('course_completion_crit_compl'),'Record count mismatch for completion');

        // Add user1 to course2 - this will also be reset when the window opens.
        $this->getDataGenerator()->enrol_user($this->user1->id, $this->course2->id);

        // Make all users complete in their courses by viewing the certificates.
        $this->assertEquals(0, $DB->count_records('certificate_issues'));
        for ($i = 1; $i <= COMPLETION_TEST_COURSES_CREATED; $i++) {
            $courseid = $this->{"course".$i}->id;

            $coursecontext = context_course::instance($courseid);
            foreach ($this->users as $user) {
                if (!is_enrolled($coursecontext, $user->id)) {
                    continue;
                }
                // Create a certificate for the user - this replicates a user going to mod/certificate/view.php.
                certificate_get_issue($this->{"course".$i}, $user, $this->{"certificate".$i}, $this->{"coursemodule".$i});
                $params = array('userid' => $user->id, 'coursemoduleid' => $this->{"coursemodule".$i}->id);

                // Check it isn't complete.
                $completionstate = $DB->get_field('course_modules_completion', 'completionstate', $params);
                $this->assertEmpty($completionstate);

                // Complete the certificate.
                $this->{"completioninfo".$i}->set_module_viewed($this->{"coursemodule".$i}, $user->id);

                // Check it is completed.
                $completionstate = $DB->get_field('course_modules_completion', 'completionstate', $params, MUST_EXIST);
                $this->assertEquals(COMPLETION_COMPLETE, $completionstate);
            }
        }

        // When marking certificates complete using "set_module_viewed()" (above), it used the current date as
        // completion date. To cause window open, we need to move the window open date backwards. Also move
        // timecompleted backwards to prevent certification validation errors.
        $backsecs = 30 * DAYSECS;
        $DB->execute('UPDATE {certif_completion}
                         SET timewindowopens = timewindowopens - ' . $backsecs .',
                             timecompleted = timecompleted - ' . $backsecs);
        $DB->execute('UPDATE {prog_completion}
                         SET timecompleted = timecompleted - ' . $backsecs);

        // Check records in course_completion_crit_compl.
        $this->assertEquals(6, $DB->count_records('course_completion_crit_compl'), 'Record count mismatch for crit_compl');
        $completions = array(
            $this->user1->id => $this->course1->id,
            $this->user1->id => $this->course2->id,
            $this->user1->id => $this->course3->id,
            $this->user2->id => $this->course1->id,
            $this->user3->id => $this->course3->id,
            $this->user3->id => $this->course1->id
        );
        foreach ($completions as $key => $value) {
            $conditions = array('userid' => $key, 'course' => $value);
            $this->assertTrue($DB->record_exists('course_completions', $conditions));
            $this->assertTrue($DB->record_exists('course_completion_crit_compl', $conditions));
        }

        // Verify timecomplete for the certification is not null.
        $certification = $DB->get_record('certif_completion', array('certifid' => $this->program->certifid, 'userid' => $this->user1->id));
        $this->assertNotNull($certification->timecompleted, 'Time completed is NULL');

        // Run the cron.
        $certcron = new \totara_certification\task\update_certification_task();
        $certcron->execute();

        // The course completion crit compl records for user1 and user2 in the courses that are in the
        // certification should have been removed.
        $this->assertEquals(3, $DB->count_records('course_completion_crit_compl'));
        $completions = array(
            $this->user1->id => $this->course3->id,
            $this->user3->id => $this->course3->id,
            $this->user3->id => $this->course1->id
        );
        foreach ($completions as $key => $value) {
            $conditions = array('userid' => $key, 'course' => $value);
            $this->assertTrue($DB->record_exists('course_completions', $conditions));
            $this->assertTrue($DB->record_exists('course_completion_crit_compl', $conditions));
        }
    }

    /** This function will test the delete_course_completion_data function should behave as follow:
     *  No records that were mark via rpl should be deleted when the function is called without parameter userid.
     *  If userid is passed it should delete only records related to the course-userid.
     */
    public function test_delete_course_completion_data() {
        global $DB;

        $this->resetAfterTest(true);
        $this->setAdminUser();

        // Case #1: Activity completion made by users.
        // Make users to complete course1 via criteria completion.
        $this->assertEquals(0, $DB->count_records('course_completion_crit_compl'), 'Record count mismatch for completion');
        $this->assertEquals(0, $DB->count_records('certificate_issues'));
        $course = $this->course1;
        foreach ($this->users as $user) {
            // Create a certificate for the user - this replicates a user going to mod/certificate/view.php.
            certificate_get_issue($course, $user, $this->certificate1, $this->coursemodule1);
            $params = array('userid' => $user->id, 'coursemoduleid' => $this->coursemodule1->id);

            // Check it isn't complete.
            $completionstate = $DB->get_field('course_modules_completion', 'completionstate', $params);
            $this->assertEmpty($completionstate);

            // Complete the certificate.
            $this->completioninfo1->set_module_viewed($this->coursemodule1, $user->id);

            // Check its completed.
            $completionstate = $DB->get_field('course_modules_completion', 'completionstate', $params, MUST_EXIST);
            $this->assertEquals(COMPLETION_COMPLETE, $completionstate);

            // Call function to complete the activities for the courses.
            $criteria = $DB->get_record('course_completion_criteria',
                array('course' => $course->id, 'criteriatype' => COMPLETION_CRITERIA_TYPE_ACTIVITY));
            $params = array(
                'userid'     => $user->id,
                'course'     => $course->id,
                'criteriaid' => $criteria->id
            );
            $completion = new completion_criteria_completion($params);
            $completion->mark_complete();
        }
        // Check records in course_completion_crit_compl.
        $this->assertEquals(3, $DB->count_records('course_completion_crit_compl'));
        $this->assertEquals(5, $DB->count_records('course_completions'));

        // Call delete_course_completion_data for course1.
        $completion = new completion_info($course);
        $completion->delete_course_completion_data();

        // There shouldn't be records for course1, because it was not completed via RPL.
        $this->assertEquals(0, $DB->count_records('course_completions', array('course' => $this->course1->id)));
        $this->assertEquals(0, $DB->count_records('course_completion_crit_compl', array('course' => $this->course1->id)));
        // But should exists two records for course3.
        $this->assertEquals(2, $DB->count_records('course_completions', array('course' => $this->course3->id)));

        // Case #2: Course completion made via RPL.
        // Now lets complete course3 for user1 and user3 via RPL.
        $userstocomplete = array($this->user1, $this->user3);
        foreach ($userstocomplete as $user) {
            $completionrpl = new completion_completion(array('userid' => $user->id, 'course' => $this->course3->id));
            $completionrpl->rpl = 'Course completed via rpl';
            $completionrpl->status = COMPLETION_STATUS_COMPLETEVIARPL;
            $completionrpl->mark_complete();
        }

        // Verify course3 has been marked as completed for user1 and user3.
        $completion = new completion_info($this->course3);
        $this->assertTrue($completion->is_course_complete($this->user1->id));
        $this->assertTrue($completion->is_course_complete($this->user3->id));
        $this->assertEquals(0, $DB->count_records('course_completion_crit_compl', array('course' => $this->course3->id)));

        // Delete completion for course3.
        $completion->delete_course_completion_data();
        // TOTARA: As it was completed via RPL no records should be deleted in the course_completions table.
        $this->assertEquals(2, $DB->count_records('course_completions'));

        // Case #3: Activity completion made via RPL.
        // Let's complete activities via RPL for all users in course1.
        foreach ($this->users as $user) {
            $criteria = $DB->get_record('course_completion_criteria',
                array('course' => $this->course1->id, 'criteriatype' => COMPLETION_CRITERIA_TYPE_ACTIVITY));
            $completionrpl = new completion_criteria_completion(array(
                'userid' => $user->id,
                'course' => $this->course1->id,
                'criteriaid' => $criteria->id
            ));
            $completionrpl->rpl = 'Activity completed via RPL';
            $completionrpl->mark_complete();
        }

        // Ensure Course1 was not completed via RPL.
        $params = array($this->course1->id, COMPLETION_STATUS_COMPLETEVIARPL);
        $this->assertEquals(3, $DB->count_records_select('course_completions', 'course = ? AND status != ?', $params));
        // Check records before calling to delete_course_completion_data function.
        $this->assertEquals(3, $DB->count_records('course_completion_crit_compl', array('course' => $this->course1->id)));

        // Delete completion for course1.
        $completion = new completion_info($this->course1);
        $completion->delete_course_completion_data();
        // Because the activity was completed via RPL the completion records should be intact.
        $this->assertEquals(3, $DB->count_records('course_completion_crit_compl', array('course' => $this->course1->id)));
        // Course completions for the course should be deleted as they weren't completed via RPL.
        $this->assertEquals(0, $DB->count_records('course_completions', array('course' => $this->course1->id)));
    }

    /** This function will test the delete_course_completion_data function with a userid should behave as follow:
     *  All course completion records (including those marked via RPL) for the user given should be deleted
     *  when this function is called
     */
    public function test_delete_course_completion_data_with_userid() {
        global $DB;

        $this->resetAfterTest(true);
        $this->setAdminUser();

        // Case #1: Activity completion made by users.
        // Make users to complete course1 via criteria completion.
        $this->assertEquals(0, $DB->count_records('course_completion_crit_compl'), 'Record count mismatch for completion');
        $this->assertEquals(0, $DB->count_records('certificate_issues'));
        $course = $this->course1;
        foreach ($this->users as $user) {
            // Create a certificate for the user - this replicates a user going to mod/certificate/view.php.
            certificate_get_issue($course, $user, $this->certificate1, $this->coursemodule1);
            $params = array('userid' => $user->id, 'coursemoduleid' => $this->coursemodule1->id);

            // Check it isn't complete.
            $completionstate = $DB->get_field('course_modules_completion', 'completionstate', $params);
            $this->assertEmpty($completionstate);

            // Complete the certificate.
            $this->completioninfo1->set_module_viewed($this->coursemodule1, $user->id);

            // Check its completed.
            $completionstate = $DB->get_field('course_modules_completion', 'completionstate', $params, MUST_EXIST);
            $this->assertEquals(COMPLETION_COMPLETE, $completionstate);

            // Call function to complete the activities for the courses.
            $criteria = $DB->get_record('course_completion_criteria',
                array('course' => $course->id, 'criteriatype' => COMPLETION_CRITERIA_TYPE_ACTIVITY));
            $params = array(
                'userid'     => $user->id,
                'course'     => $course->id,
                'criteriaid' => $criteria->id
            );
            $completion = new completion_criteria_completion($params);
            $completion->mark_complete();
        }
        // Check records in course_completion_crit_compl.
        $this->assertEquals(3, $DB->count_records('course_completion_crit_compl'));

        // Call delete_course_completion_data with user1 id.
        $completion = new completion_info($course);
        $completion->delete_course_completion_data($this->user1->id);

        $this->assertEquals(4, $DB->count_records('course_completions'));
        // Now should be two records in completions. One for user2 and other for user3 in course1.
        $this->assertEquals(2, $DB->count_records('course_completion_crit_compl'));
        $conditions = array('userid' => $this->user1->id, 'course' => $course->id);
        $this->assertFalse($DB->record_exists('course_completions', $conditions));
        $this->assertFalse($DB->record_exists('course_completion_crit_compl', $conditions));
        $conditions['userid'] = $this->user2->id;
        $this->assertTrue($DB->record_exists('course_completions', $conditions));
        $this->assertTrue($DB->record_exists('course_completion_crit_compl', $conditions));
        $conditions['userid'] = $this->user3->id;
        $this->assertTrue($DB->record_exists('course_completions', $conditions));
        $this->assertTrue($DB->record_exists('course_completion_crit_compl', $conditions));

        // Case #2: Course completion made via RPL.
        // Now lets complete course3 for user1 and user3 via RPL.
        $userstocomplete = array($this->user1, $this->user3);
        foreach ($userstocomplete as $user) {
            $completionrpl = new completion_completion(array('userid' => $user->id, 'course' => $this->course3->id));
            $completionrpl->rpl = 'Course completed via RPL';
            $completionrpl->status = COMPLETION_STATUS_COMPLETEVIARPL;
            $completionrpl->mark_complete();
        }

        // Verify course3 has been marked as completed for user1 and user3.
        $completion = new completion_info($this->course3);
        $this->assertTrue($completion->is_course_complete($this->user1->id));
        $this->assertTrue($completion->is_course_complete($this->user3->id));
        $this->assertEquals(4, $DB->count_records('course_completions'));
        $this->assertEquals(0, $DB->count_records('course_completion_crit_compl', array('course' => $this->course3->id)));

        // Delete completion records for course3-user1.
        // Changes should be reflected in course_completions table.
        $completion->delete_course_completion_data($this->user1->id);
        $this->assertEquals(3, $DB->count_records('course_completions'));
        $this->assertEquals(0, $DB->count_records('course_completion_crit_compl', array('course' => $this->course3->id)));

        // Case #3: Activity completion made via RPL.
        // Let's complete activities via RPL for user1 and user3 in course3.
        foreach ($userstocomplete as $user) {
            $criteria = $DB->get_record('course_completion_criteria',
                array('course' => $this->course3->id, 'criteriatype' => COMPLETION_CRITERIA_TYPE_ACTIVITY));
            $completionrpl = new completion_criteria_completion(array(
                'userid' => $user->id,
                'course' => $this->course3->id,
                'criteriaid' => $criteria->id
            ));
            $completionrpl->rpl = 'Activity completed via RPL';
            $completionrpl->mark_complete();
        }

        // Check records in course_completion_crit_compl before deleting.
        $this->assertEquals(2, $DB->count_records('course_completion_crit_compl', array('course' => $this->course3->id)));

        // Delete completion for user1-course3.
        // Note that it doesn't matter that the user completed the activity via RPl, the activity completion is deleted.
        $completion = new completion_info($this->course3);
        $completion->delete_course_completion_data($this->user1->id);
        $this->assertEquals(1, $DB->count_records('course_completion_crit_compl', array('course' => $this->course3->id)));
        $this->assertEquals(3, $DB->count_records('course_completions'));
        $conditions = array('userid' => $this->user1->id, 'course' => $this->course3->id);
        $this->assertFalse($DB->record_exists('course_completions', $conditions));
        $this->assertFalse($DB->record_exists('course_completion_crit_compl', $conditions));
    }
}
