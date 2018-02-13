<?php
/*
 * This file is part of Totara Learn
 *
 * Copyright (C) 2018 onwards Totara Learning Solutions LTD
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
 * @author Nathan Lewis <nathan.lewis@totaralearning.com>
 * @package totara_job
 */

use totara_job\job_assignment;
use totara_job\userdata\job_assignments;
use totara_userdata\userdata\item;
use totara_userdata\userdata\target_user;

defined('MOODLE_INTERNAL') || die();

/**
 * To test, run this from the command line from the $CFG->dirroot.
 * vendor/bin/phpunit --verbose totara_job_userdata_job_assignments_testcase totara/job/tests/userdata_job_assignments_test.php
 */
class totara_job_userdata_job_assignments_testcase extends \advanced_testcase {

    /**
     * Test function is_compatible_context_level with all possible contexts.
     */
    public function test_get_compatible_context_levels() {
        $this->assertEquals(array(CONTEXT_SYSTEM), job_assignments::get_compatible_context_levels());
    }

    /**
     * Test function is_purgeable.
     */
    public function test_is_purgeable() {
        $this->assertTrue(job_assignments::is_purgeable(target_user::STATUS_ACTIVE));
        $this->assertTrue(job_assignments::is_purgeable(target_user::STATUS_SUSPENDED));
        $this->assertTrue(job_assignments::is_purgeable(target_user::STATUS_DELETED));
    }

    /**
     * Test function is_exportable.
     */
    public function test_is_exportable() {
        $this->assertTrue(job_assignments::is_exportable());
    }

    /**
     * Test function is_countable.
     */
    public function test_is_countable() {
        $this->assertTrue(job_assignments::is_countable());
    }

    /**
     * Set up data that'll be purged, exported or counted.
     */
    private function setup_data() {
        $data = new class() {
            /** @var \stdClass */
            public $user1, $user2, $user3, $user4, $user5;

            /** @var job_assignment */
            public $ja1a, $ja1b, $ja2, $ja3a, $ja3b, $ja3c, $ja4, $ja5;

            /** @var target_user */
            public $targetuser, $targetuser2;
        };

        $this->resetAfterTest(true);

        // Set up users.
        $data->user1 = $this->getDataGenerator()->create_user(); // One managed by user2, one by user3.
        $data->user2 = $this->getDataGenerator()->create_user(); // Managed by user2.
        $data->user3 = $this->getDataGenerator()->create_user(); // Target user! One managed by user4, one by user5, one with no manager.
        $data->user4 = $this->getDataGenerator()->create_user(); // Managed by user5.
        $data->user5 = $this->getDataGenerator()->create_user(); // Top manager.

        // Set up some management hierarchies.
        $data->ja5 = job_assignment::create_default($data->user5->id);
        $data->ja4 = job_assignment::create_default($data->user4->id, array('managerjaid' => $data->ja5->id));
        $data->ja3a = job_assignment::create_default($data->user3->id, array('managerjaid' => $data->ja4->id));
        $data->ja3b = job_assignment::create_default($data->user3->id, array('managerjaid' => $data->ja5->id));
        $data->ja3c = job_assignment::create_default($data->user3->id);
        $data->ja2 = job_assignment::create_default($data->user2->id, array('managerjaid' => $data->ja3a->id));
        $data->ja1a = job_assignment::create_default($data->user1->id, array('managerjaid' => $data->ja2->id));
        $data->ja1b = job_assignment::create_default($data->user1->id, array('managerjaid' => $data->ja3a->id));

        $descriptionwithfile = array('description' => 'aklshfafa sfhlasfklas hfa');
        $data->ja3b->update($descriptionwithfile);

        // Set up the target user.
        $data->targetuser = new target_user($data->user3);
        $data->targetuser2 = new target_user($data->user2);

        return $data;
    }

    /**
     * Test the purge function. Make sure that the control data is not affected.
     */
    public function test_purge() {
        global $DB;

        $data = $this->setup_data();

        // Get the expected data, by modifying the actual data. We only need the list, not the details, because the
        // job assignment function should ensure the management hierarchy is correctly rewritten and files are handled.
        $expectedjas = $DB->get_records('job_assignment', array(), 'id', 'id, userid');
        foreach ($expectedjas as $key => $expectedja) {
            if ($expectedja->userid == $data->targetuser->id) {
                unset($expectedjas[$key]);
            }
        }

        // Execute the purge.
        $status = job_assignments::execute_purge($data->targetuser, context_system::instance());
        $this->assertEquals(item::RESULT_STATUS_SUCCESS, $status);

        // Check the results.
        $this->assertEquals($expectedjas, $DB->get_records('job_assignment', array(), 'id', 'id, userid'));
    }

    /**
     * Tests that files are included in purge.
     */
    public function test_purge_includes_attachments() {
        global $CFG, $TEXTAREA_OPTIONS;

        require_once($CFG->libdir . '/formslib.php');

        $data = $this->setup_data();

        $systemcontext = context_system::instance();
        $fs = get_file_storage();

        // Target's attachments.
        $filerecord = ['contextid' => SYSCONTEXTID,
            'component' => 'totara_job',
            'filearea' => 'job_assignment',
            'itemid' => $data->ja3a->id,
            'filepath' => '/',
            'filename' => 'testx.png',
            'timecreated' => time(),
            'timemodified' => time()];
        $fs->create_file_from_string($filerecord, 'testfilecontents1');

        $filerecord = ['contextid' => SYSCONTEXTID,
            'component' => 'totara_job',
            'filearea' => 'job_assignment',
            'itemid' => $data->ja3b->id,
            'filepath' => '/',
            'filename' => 'testz.png',
            'timecreated' => time(),
            'timemodified' => time()];
        $fs->create_file_from_string($filerecord, 'testfilecontents1');

        // Control's attachment.
        $filerecord = ['contextid' => SYSCONTEXTID,
            'component' => 'totara_job',
            'filearea' => 'job_assignment',
            'itemid' => $data->ja2->id,
            'filepath' => '/',
            'filename' => 'control.png',
            'timecreated' => time(),
            'timemodified' => time()];
        $fs->create_file_from_string($filerecord, 'testfilecontents2');

        // Check that the file table contains the correct files.
        $expectedfiles = [];
        $expectedfiles['3a'] = $fs->get_area_files($TEXTAREA_OPTIONS['context']->id,
            'totara_job', 'job_assignment', $data->ja3a->id, "timemodified, id", false);
        $expectedfiles['3b'] = $fs->get_area_files($TEXTAREA_OPTIONS['context']->id,
            'totara_job', 'job_assignment', $data->ja3b->id, "timemodified, id", false);
        $expectedfiles['3c'] = $fs->get_area_files($TEXTAREA_OPTIONS['context']->id,
            'totara_job', 'job_assignment', $data->ja3c->id, "timemodified, id", false);
        $expectedfiles['2'] = $fs->get_area_files($TEXTAREA_OPTIONS['context']->id,
            'totara_job', 'job_assignment', $data->ja2->id, "timemodified, id", false);
        $this->assertNotEmpty($expectedfiles['3a']);
        $this->assertNotEmpty($expectedfiles['3b']);
        $this->assertEmpty($expectedfiles['3c']);
        $this->assertNotEmpty($expectedfiles['2']);

        // Run the purge.
        job_assignments::execute_purge($data->targetuser, context_system::instance());

        // Check that the purge removed what it should have and nothing more.
        $expectedfiles = [];
        $expectedfiles['3a'] = $fs->get_area_files($TEXTAREA_OPTIONS['context']->id,
            'totara_job', 'job_assignment', $data->ja3a->id, "timemodified, id", false);
        $expectedfiles['3b'] = $fs->get_area_files($TEXTAREA_OPTIONS['context']->id,
            'totara_job', 'job_assignment', $data->ja3b->id, "timemodified, id", false);
        $expectedfiles['3c'] = $fs->get_area_files($TEXTAREA_OPTIONS['context']->id,
            'totara_job', 'job_assignment', $data->ja3c->id, "timemodified, id", false);
        $expectedfiles['2'] = $fs->get_area_files($TEXTAREA_OPTIONS['context']->id,
            'totara_job', 'job_assignment', $data->ja2->id, "timemodified, id", false);
        $this->assertEmpty($expectedfiles['3a']);
        $this->assertEmpty($expectedfiles['3b']);
        $this->assertEmpty($expectedfiles['3c']);
        $this->assertNotEmpty($expectedfiles['2']);
    }

    /**
     * Test the export function. Make sure that the control data is not exported.
     */
    public function test_export() {
        global $DB;

        $data = $this->setup_data();

        // Get the expected data.
        $expecteddata = $DB->get_records('job_assignment', array('userid' => $data->targetuser->id));

        // Execute the export.
        $result = job_assignments::execute_export($data->targetuser, context_system::instance());

        // Check the results.
        $this->assertCount(0, $result->files);
        $this->assertCount(3, $result->data['job_assignments']);

        $this->assertEquals($expecteddata, $result->data['job_assignments']);
    }

    /**
     * Tests the files are included in the export.
     */
    public function test_export_includes_attachments() {
        global $CFG;

        $data = $this->setup_data();

        $systemcontext = context_system::instance();
        $fs = get_file_storage();

        // Target's attachments.
        $filerecord = ['contextid' => SYSCONTEXTID,
            'component' => 'totara_job',
            'filearea' => 'job_assignment',
            'itemid' => $data->ja3a->id,
            'filepath' => '/',
            'filename' => 'testx.png',
            'timecreated' => time(),
            'timemodified' => time()];
        $fs->create_file_from_string($filerecord, 'testfilecontents1');

        $filerecord = ['contextid' => SYSCONTEXTID,
            'component' => 'totara_job',
            'filearea' => 'job_assignment',
            'itemid' => $data->ja3b->id,
            'filepath' => '/',
            'filename' => 'testz.png',
            'timecreated' => time(),
            'timemodified' => time()];
        $fs->create_file_from_string($filerecord, 'testfilecontents1');

        // Control's attachment.
        $filerecord = ['contextid' => SYSCONTEXTID,
            'component' => 'totara_job',
            'filearea' => 'job_assignment',
            'itemid' => $data->ja2->id,
            'filepath' => '/',
            'filename' => 'control.png',
            'timecreated' => time(),
            'timemodified' => time()];
        $fs->create_file_from_string($filerecord, 'testfilecontents2');

        // Check that the export contains only the target's file.
        $export = job_assignments::execute_export($data->targetuser, $systemcontext);
        $this->assertCount(2, $export->files);
        $this->assertCount(2, $export->data['files']);
        if ($export->files[0]->get_itemid() == $data->ja3a->id) {
            $file3a = $export->files[0];
            $file3b = $export->files[1];
        } else {
            $file3a = $export->files[1];
            $file3b = $export->files[0];
        }
        $this->assertEquals($data->ja3a->id, $file3a->get_itemid());
        $this->assertEquals($data->ja3b->id, $file3b->get_itemid());

        $export = job_assignments::execute_export($data->targetuser2, $systemcontext);
        $this->assertCount(1, $export->files);
        $this->assertCount(1, $export->data['files']);
        $file = $export->files[0];
        $this->assertEquals($data->ja2->id, $file->get_itemid());

        // Check that the export doesn't contain anything.
        job_assignments::execute_purge($data->targetuser, context_system::instance());
        $export = job_assignments::execute_export($data->targetuser, $systemcontext);
        $this->assertCount(0, $export->files);
    }

    /**
     * Test the count function.
     */
    public function test_count() {
        $data = $this->setup_data();

        $this->assertEquals(3, job_assignments::execute_count($data->targetuser, context_system::instance()));
        $this->assertEquals(1, job_assignments::execute_count($data->targetuser2, context_system::instance()));
        job_assignments::execute_purge($data->targetuser, context_system::instance());
        $this->assertEquals(0, job_assignments::execute_count($data->targetuser, context_system::instance()));
    }
}