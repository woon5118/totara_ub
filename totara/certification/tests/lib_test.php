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
 * @author Nathan Lewis <nathan.lewis@totaralms.com>
 * @package totara_certification
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/totara/reportbuilder/tests/reportcache_advanced_testcase.php');
require_once($CFG->dirroot . '/totara/certification/lib.php');
require_once($CFG->dirroot . '/totara/program/lib.php');

/**
 * Certification module PHPUnit archive test class.
 *
 * To test, run this from the command line from the $CFG->dirroot.
 * vendor/bin/phpunit --verbose totara_certification_lib_testcase totara/certification/tests/lib_test.php
 */
class totara_certification_lib_testcase extends reportcache_advanced_testcase {

    public function test_find_courses_for_certif() {
        $this->resetAfterTest(true);

        // Set up some courses and certifications.
        $courses = array();
        $certifications = array();
        for ($i = 1; $i <= 10; $i++) {
            $course = $this->getDataGenerator()->create_course();
            $courses[$course->id] = $course; // IDs match array index, to simplify testing.
            $certifications[$i] = $this->getDataGenerator()->create_certification();
        }

        // Set up some courses in the certifications.
        $this->getDataGenerator()->add_courseset_program($certifications[2]->id,
            array($courses[2]->id, $courses[3]->id, $courses[4]->id), CERTIFPATH_CERT);
        $this->getDataGenerator()->add_courseset_program($certifications[2]->id,
            array($courses[3]->id, $courses[4]->id, $courses[5]->id), CERTIFPATH_RECERT);

        $this->getDataGenerator()->add_courseset_program($certifications[3]->id,
            array($courses[2]->id, $courses[3]->id, $courses[4]->id), CERTIFPATH_CERT);
        $this->getDataGenerator()->add_courseset_program($certifications[3]->id,
            array($courses[3]->id, $courses[4]->id, $courses[5]->id), CERTIFPATH_RECERT);

        $this->getDataGenerator()->add_courseset_program($certifications[4]->id,
            array($courses[2]->id, $courses[3]->id, $courses[4]->id), CERTIFPATH_CERT);
        $this->getDataGenerator()->add_courseset_program($certifications[4]->id,
            array($courses[2]->id, $courses[3]->id, $courses[4]->id), CERTIFPATH_RECERT);

        $this->getDataGenerator()->add_courseset_program($certifications[5]->id,
            array($courses[8]->id, $courses[9]->id), CERTIFPATH_CERT);
        $this->getDataGenerator()->add_courseset_program($certifications[5]->id,
            array($courses[8]->id, $courses[9]->id), CERTIFPATH_RECERT);

        $this->getDataGenerator()->add_courseset_program($certifications[6]->id,
            array($courses[6]->id), CERTIFPATH_CERT);
        $this->getDataGenerator()->add_courseset_program($certifications[6]->id,
            array($courses[7]->id), CERTIFPATH_RECERT);

        $this->getDataGenerator()->add_courseset_program($certifications[7]->id,
            array($courses[6]->id), CERTIFPATH_CERT);

        $this->getDataGenerator()->add_courseset_program($certifications[8]->id,
            array($courses[7]->id), CERTIFPATH_RECERT);

        // Call find_courses_for_certif with each of the three params and ensure that the correct courses are returned.
        $found = array_keys(find_courses_for_certif($certifications[2]->id, 'c.id'));
        sort($found);
        $this->assertEquals(array(2, 3, 4, 5), $found);
        $found = array_keys(find_courses_for_certif($certifications[2]->id, 'c.id', CERTIFPATH_CERT));
        sort($found);
        $this->assertEquals(array(2, 3, 4), $found);
        $found = array_keys(find_courses_for_certif($certifications[2]->id, 'c.id', CERTIFPATH_RECERT));
        sort($found);
        $this->assertEquals(array(3, 4, 5), $found);

        $found = array_keys(find_courses_for_certif($certifications[3]->id));
        sort($found);
        $this->assertEquals(array(2, 3, 4, 5), $found); // Note default fields.
        $found = array_keys(find_courses_for_certif($certifications[3]->id, 'c.id', CERTIFPATH_CERT));
        sort($found);
        $this->assertEquals(array(2, 3, 4), $found);
        $found = array_keys(find_courses_for_certif($certifications[3]->id, 'c.id', CERTIFPATH_RECERT));
        sort($found);
        $this->assertEquals(array(3, 4, 5), $found);

        $found = array_keys(find_courses_for_certif($certifications[4]->id, 'c.id'));
        sort($found);
        $this->assertEquals(array(2, 3, 4), $found);
        $found = array_keys(find_courses_for_certif($certifications[4]->id, 'c.id', CERTIFPATH_CERT));
        sort($found);
        $this->assertEquals(array(2, 3, 4), $found);
        $found = array_keys(find_courses_for_certif($certifications[4]->id, 'c.id', CERTIFPATH_RECERT));
        sort($found);
        $this->assertEquals(array(2, 3, 4), $found);

        $found = array_keys(find_courses_for_certif($certifications[5]->id, 'c.id'));
        sort($found);
        $this->assertEquals(array(8, 9), $found);
        $found = array_keys(find_courses_for_certif($certifications[5]->id, 'c.id', CERTIFPATH_CERT));
        sort($found);
        $this->assertEquals(array(8, 9), $found);
        $found = array_keys(find_courses_for_certif($certifications[5]->id, 'c.id', CERTIFPATH_RECERT));
        sort($found);
        $this->assertEquals(array(8, 9), $found);

        $found = array_keys(find_courses_for_certif($certifications[6]->id, 'c.id'));
        sort($found);
        $this->assertEquals(array(6, 7), $found);
        $found = array_keys(find_courses_for_certif($certifications[6]->id, 'c.id', CERTIFPATH_CERT));
        sort($found);
        $this->assertEquals(array(6), $found);
        $found = array_keys(find_courses_for_certif($certifications[6]->id, 'c.id', CERTIFPATH_RECERT));
        sort($found);
        $this->assertEquals(array(7), $found);

        $found = array_keys(find_courses_for_certif($certifications[7]->id, 'c.id'));
        sort($found);
        $this->assertEquals(array(6), $found);
        $found = array_keys(find_courses_for_certif($certifications[7]->id, 'c.id', CERTIFPATH_CERT));
        sort($found);
        $this->assertEquals(array(6), $found);
        $found = array_keys(find_courses_for_certif($certifications[7]->id, 'c.id', CERTIFPATH_RECERT));
        sort($found);
        $this->assertEquals(array(), $found);

        $found = array_keys(find_courses_for_certif($certifications[8]->id, 'c.id'));
        sort($found);
        $this->assertEquals(array(7), $found);
        $found = array_keys(find_courses_for_certif($certifications[8]->id, 'c.id', CERTIFPATH_CERT));
        sort($found);
        $this->assertEquals(array(), $found);
        $found = array_keys(find_courses_for_certif($certifications[8]->id, 'c.id', CERTIFPATH_RECERT));
        sort($found);
        $this->assertEquals(array(7), $found);

        $found = array_keys(find_courses_for_certif($certifications[9]->id, 'c.id'));
        sort($found);
        $this->assertEquals(array(), $found);
        $found = array_keys(find_courses_for_certif($certifications[9]->id, 'c.id', CERTIFPATH_CERT));
        sort($found);
        $this->assertEquals(array(), $found);
        $found = array_keys(find_courses_for_certif($certifications[9]->id, 'c.id', CERTIFPATH_RECERT));
        sort($found);
        $this->assertEquals(array(), $found);
    }

    public function test_certif_get_content_completion_time() {
        $this->resetAfterTest(true);

        // Set up some courses and certifications.
        $courses = array();
        $certifications = array();
        for ($i = 1; $i <= 10; $i++) {
            $course = $this->getDataGenerator()->create_course();
            $courses[$course->id] = $course; // IDs match array index, to simplify testing.
            $certifications[$i] = $this->getDataGenerator()->create_certification();
        }

        // Set up some courses in the certifications.
        $this->getDataGenerator()->add_courseset_program($certifications[2]->id,
            array($courses[2]->id, $courses[3]->id, $courses[4]->id), CERTIFPATH_CERT);
        $this->getDataGenerator()->add_courseset_program($certifications[2]->id,
            array($courses[6]->id, $courses[7]->id), CERTIFPATH_RECERT);

        $this->getDataGenerator()->add_courseset_program($certifications[4]->id,
            array($courses[2]->id, $courses[3]->id), CERTIFPATH_CERT);
        $this->getDataGenerator()->add_courseset_program($certifications[4]->id,
            array($courses[3]->id, $courses[4]->id), CERTIFPATH_RECERT);

        $this->getDataGenerator()->add_courseset_program($certifications[6]->id,
            array($courses[6]->id), CERTIFPATH_CERT);
        $this->getDataGenerator()->add_courseset_program($certifications[6]->id,
            array($courses[7]->id), CERTIFPATH_RECERT);

        // Set up some users.
        $users = array();
        for ($i = 1; $i <= 5; $i++) {
            $users[$i] = $this->getDataGenerator()->create_user();
        }

        // Assign all users to all certification as individuals.
        foreach ($certifications as $certification) {
            foreach ($users as $user) {
                $this->getDataGenerator()->assign_to_program($certification->id, ASSIGNTYPE_INDIVIDUAL, $user->id);
            }
        }

        // Mark some users as complete in some courses, with identifiable completion dates.
        $completiondatas = array(
            array(1, 2, 1000),
            array(1, 3, 3000),
            array(1, 4, 2000),
            array(1, 5, 99999),
            array(1, 8, 99999),
            array(1, 9, 99999),

            array(2, 2, 8000),
            array(2, 3, 6000),
            array(2, 4, 7000),
            array(2, 5, 99999),
            array(2, 8, 99999),
            array(2, 9, 99999),

            array(3, 2, 1000),
            array(3, 3, 2000),
            array(3, 4, 4000),
            array(3, 6, 3000),
            array(3, 7, 5000),

            array(4, 2, 99999),
            array(4, 3, 99999),
            array(4, 4, 99999),
            array(4, 6, 99999),
            array(4, 7, 99999),

            array(5, 2, 1),
            array(5, 3, 1),
            array(5, 4, 1),
            array(5, 6, 1),
            array(5, 7, 1),
        );

        foreach ($completiondatas as $completiondata) {
            list($user, $course, $time) = $completiondata;
            $completion = new completion_completion(array('userid' => $users[$user]->id, 'course' => $courses[$course]->id));
            $completion->mark_complete($time);
        }

        // Call the function, checking if the correct times are returned.

        // These two check that the correct user's results are being returned.
        $this->assertEquals(3000, certif_get_content_completion_time($certifications[2]->id, $users[1]->id));
        $this->assertEquals(8000, certif_get_content_completion_time($certifications[2]->id, $users[2]->id));

        // These two check that the correct certification's results are being returned.
        $this->assertEquals(5000, certif_get_content_completion_time($certifications[2]->id, $users[3]->id));
        $this->assertEquals(4000, certif_get_content_completion_time($certifications[4]->id, $users[3]->id));

        // These check that the correct certification path results are being returned.
        $this->assertEquals(4000, certif_get_content_completion_time($certifications[2]->id, $users[3]->id, CERTIFPATH_CERT));
        $this->assertEquals(5000, certif_get_content_completion_time($certifications[2]->id, $users[3]->id, CERTIFPATH_RECERT));
        $this->assertEquals(2000, certif_get_content_completion_time($certifications[4]->id, $users[3]->id, CERTIFPATH_CERT));
        $this->assertEquals(4000, certif_get_content_completion_time($certifications[4]->id, $users[3]->id, CERTIFPATH_RECERT));
    }

    /**
     * This tests that write_certif_completion is getting the timecompleted from the correct path courses.
     */
    public function test_write_certif_completion_timecompleted_course_path() {
        global $DB;

        $this->resetAfterTest(true);

        // Set up some stuff.
        $user = $this->getDataGenerator()->create_user();
        $certification = $this->getDataGenerator()->create_certification();
        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();
        $course3 = $this->getDataGenerator()->create_course();
        $course4 = $this->getDataGenerator()->create_course();

        // Add the courses to the certification.
        $this->getDataGenerator()->add_courseset_program($certification->id,
            array($course1->id, $course2->id), CERTIFPATH_CERT);
        $this->getDataGenerator()->add_courseset_program($certification->id,
            array($course3->id, $course4->id), CERTIFPATH_RECERT);

        // Assign the user to the cert as an individual.
        $this->getDataGenerator()->assign_to_program($certification->id, ASSIGNTYPE_INDIVIDUAL, $user->id);

        // Mark all the courses complete, with traceable time completed.
        $completion = new completion_completion(array('userid' => $user->id, 'course' => $course1->id));
        $completion->mark_complete(1000);
        $completion = new completion_completion(array('userid' => $user->id, 'course' => $course2->id));
        $completion->mark_complete(2000);
        $completion = new completion_completion(array('userid' => $user->id, 'course' => $course3->id));
        $completion->mark_complete(4000);
        $completion = new completion_completion(array('userid' => $user->id, 'course' => $course4->id));
        $completion->mark_complete(3000);

        // Check the existing data.
        $this->assertEquals(1, $DB->count_records('prog_completion', array('coursesetid' => 0)));
        $this->assertEquals(1, $DB->count_records('certif_completion'));
        list($certcompletion, $progcompletion) = certif_load_completion($certification->id, $user->id);
        $this->assertEquals(2000, $progcompletion->timecompleted);
        $this->assertEquals(2000, $certcompletion->timecompleted);

        // The user is now certified. Update the certification so that the window is open.
        $progcompletion->status = STATUS_PROGRAM_INCOMPLETE;
        $progcompletion->timecompleted = 0;
        $certcompletion->renewalstatus = CERTIFRENEWALSTATUS_DUE;
        certif_write_completion($certcompletion, $progcompletion); // Contains data validation, so we don't need to check it here.

        // Indirectly call write_certif_completion, causing the user to be marked certified again.
        prog_update_completion($user->id);

        // Verify the the user was marked complete using the dates in the recert path courses.
        list($certcompletion, $progcompletion) = certif_load_completion($certification->id, $user->id);
        $this->assertEquals(4000, $progcompletion->timecompleted);
        $this->assertEquals(4000, $certcompletion->timecompleted);

        // Update the certification so that the user is expired.
        $progcompletion->status = STATUS_PROGRAM_INCOMPLETE;
        $progcompletion->timecompleted = 0;
        $certcompletion->status = CERTIFSTATUS_EXPIRED;
        $certcompletion->renewalstatus = CERTIFRENEWALSTATUS_EXPIRED;
        $certcompletion->certifpath = CERTIFPATH_CERT;
        $certcompletion->timecompleted = 0;
        $certcompletion->timewindowopens = 0;
        $certcompletion->timeexpires = 0;
        certif_write_completion($certcompletion, $progcompletion); // Contains data validation, so we don't need to check it here.

        // Indirectly call write_certif_completion, causing the user to be marked certified again.
        prog_update_completion($user->id);

        // Verify the the user was marked complete using the dates in the primary cert path courses.
        list($certcompletion, $progcompletion) = certif_load_completion($certification->id, $user->id);
        $this->assertEquals(2000, $certcompletion->timecompleted);
        $this->assertEquals(2000, $progcompletion->timecompleted);
    }
}
