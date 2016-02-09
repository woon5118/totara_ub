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
 * @package totara_program
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/totara/reportbuilder/tests/reportcache_advanced_testcase.php');
require_once($CFG->dirroot . '/totara/program/lib.php');
require_once($CFG->dirroot . '/totara/certification/lib.php');

/**
 * Program module PHPUnit test class.
 *
 * To test, run this from the command line from the $CFG->dirroot.
 * vendor/bin/phpunit --verbose totara_program_lib_testcase totara/program/tests/lib_test.php
 */
class totara_program_lib_testcase extends reportcache_advanced_testcase {

    /**
     * Test that prog_update_completion handles programs and certs.
     */
    public function test_prog_update_completion_progs_and_certs() {
        global $DB;

        $this->resetAfterTest(true);

        // Set up some stuff.
        $user = $this->getDataGenerator()->create_user();
        $program = $this->getDataGenerator()->create_program();
        $certification = $this->getDataGenerator()->create_certification();
        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();
        $course3 = $this->getDataGenerator()->create_course();
        $course4 = $this->getDataGenerator()->create_course();
        $course5 = $this->getDataGenerator()->create_course();
        $course6 = $this->getDataGenerator()->create_course();

        // Add the courses to the program and certification.
        $this->getDataGenerator()->add_courseset_program($program->id,
            array($course1->id, $course2->id));
        $this->getDataGenerator()->add_courseset_program($certification->id,
            array($course3->id, $course4->id), CERTIFPATH_CERT);
        $this->getDataGenerator()->add_courseset_program($certification->id,
            array($course5->id, $course6->id), CERTIFPATH_RECERT);

        // Assign the user to the program and cert as an individual.
        $this->getDataGenerator()->assign_to_program($program->id, ASSIGNTYPE_INDIVIDUAL, $user->id);
        $this->getDataGenerator()->assign_to_program($certification->id, ASSIGNTYPE_INDIVIDUAL, $user->id);

        // Mark all the courses complete, with traceable time completed.
        $completion = new completion_completion(array('userid' => $user->id, 'course' => $course1->id));
        $completion->mark_complete(1000);
        $completion = new completion_completion(array('userid' => $user->id, 'course' => $course2->id));
        $completion->mark_complete(2000);
        $completion = new completion_completion(array('userid' => $user->id, 'course' => $course3->id));
        $completion->mark_complete(3000);
        $completion = new completion_completion(array('userid' => $user->id, 'course' => $course4->id));
        $completion->mark_complete(4000);
        $completion = new completion_completion(array('userid' => $user->id, 'course' => $course5->id));
        $completion->mark_complete(6000);
        $completion = new completion_completion(array('userid' => $user->id, 'course' => $course6->id));
        $completion->mark_complete(5000);

        // Check the existing data.
        $this->assertEquals(2, $DB->count_records('prog_completion', array('coursesetid' => 0)));
        $this->assertEquals(1, $DB->count_records('certif_completion'));

        // Update the certification so that the user is expired.
        list($certcompletion, $progcompletion) = certif_load_completion($certification->id, $user->id);
        $progcompletion->status = STATUS_PROGRAM_INCOMPLETE;
        $progcompletion->timecompleted = 0;
        $certcompletion->status = CERTIFSTATUS_EXPIRED;
        $certcompletion->renewalstatus = CERTIFRENEWALSTATUS_EXPIRED;
        $certcompletion->certifpath = CERTIFPATH_CERT;
        $certcompletion->timecompleted = 0;
        $certcompletion->timewindowopens = 0;
        $certcompletion->timeexpires = 0;
        certif_write_completion($certcompletion, $progcompletion); // Contains data validation, so we don't need to check it here.

        // The program should currently be complete. Update the program so that the user is incomplete.
        $progcompletion = $DB->get_record('prog_completion',
            array('programid' => $program->id, 'userid' => $user->id, 'coursesetid' => 0));
        $this->assertEquals(STATUS_PROGRAM_COMPLETE, $progcompletion->status);
        $this->assertEquals(2000, $progcompletion->timecompleted);
        $progcompletion->status = STATUS_PROGRAM_INCOMPLETE;
        $progcompletion->timecompleted = 0;
        $DB->update_record('prog_completion', $progcompletion);

        // Call prog_update_completion, which should process all programs for the user.
        prog_update_completion($user->id);

        // Verify that the program is marked completed.
        $progcompletion = $DB->get_record('prog_completion',
            array('programid' => $program->id, 'userid' => $user->id, 'coursesetid' => 0));
        $this->assertEquals(STATUS_PROGRAM_COMPLETE, $progcompletion->status);
        $this->assertEquals(2000, $progcompletion->timecompleted);

        // Verify the the user was marked complete using the dates in the primary cert path courses.
        list($certcompletion, $progcompletion) = certif_load_completion($certification->id, $user->id);
        $this->assertEquals(4000, $certcompletion->timecompleted);
        $this->assertEquals(4000, $progcompletion->timecompleted);

        // Update the certification so that the recertification window is open.
        list($certcompletion, $progcompletion) = certif_load_completion($certification->id, $user->id);
        $progcompletion->status = STATUS_PROGRAM_INCOMPLETE;
        $progcompletion->timecompleted = 0;
        $certcompletion->renewalstatus = CERTIFRENEWALSTATUS_DUE;
        certif_write_completion($certcompletion, $progcompletion); // Contains data validation, so we don't need to check it here.

        // Update the course completions all courses. The recertification should have the new date, but the complete program
        // won't be effected.
        $DB->execute("UPDATE {course_completions} SET timecompleted = timecompleted + 10000");

        // Call prog_update_completion, which should process all programs for the user.
        prog_update_completion($user->id);

        // Verify that the program is marked completed (with the original completion date).
        $progcompletion = $DB->get_record('prog_completion',
            array('programid' => $program->id, 'userid' => $user->id, 'coursesetid' => 0));
        $this->assertEquals(STATUS_PROGRAM_COMPLETE, $progcompletion->status);
        $this->assertEquals(2000, $progcompletion->timecompleted);

        // Verify the the user was marked complete using the (increased) dates in the recertification path courses.
        list($certcompletion, $progcompletion) = certif_load_completion($certification->id, $user->id);
        $this->assertEquals(16000, $certcompletion->timecompleted);
        $this->assertEquals(16000, $progcompletion->timecompleted);
    }
}
