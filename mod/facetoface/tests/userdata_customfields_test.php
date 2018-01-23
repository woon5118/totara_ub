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
 * @author Matthias Bonk <matthias.bonk@totaralearning.com>
 * @package mod_facetoface
 */

namespace mod_facetoface\userdata;

defined('MOODLE_INTERNAL') || die();

use advanced_testcase;
use context_course;
use context_coursecat;
use context_module;
use context_system;
use phpunit_util;
use totara_userdata\userdata\export;
use totara_userdata\userdata\target_user;

global $CFG;
require_once($CFG->dirroot . '/mod/facetoface/lib.php');
require_once($CFG->dirroot . '/user/lib.php');
require_once($CFG->dirroot . '/mod/facetoface/tests/lib_test.php');

/**
 * Test customfields item
 *
 * @group userdata
 */
class mod_facetoface_userdata_customfields_test extends advanced_testcase {

    /**
     * Set up tests.
     */
    protected function setUp() {
        parent::setUp();
        $this->resetAfterTest(true);
    }

    /**
     * Test count.
     */
    public function test_count() {
        $this->setAdminUser(); // Necessary for file handling.
        $datagenerator = phpunit_util::get_data_generator();
        $f2fgenerator = $datagenerator->get_plugin_generator('mod_facetoface');

        $category1 = $datagenerator->create_category();
        $category2 = $datagenerator->create_category();

        $course1 = $datagenerator->create_course(['category' => $category1->id]);
        $course2 = $datagenerator->create_course(['category' => $category2->id]);
        $course3 = $datagenerator->create_course(['category' => $category2->id]);

        $student1 = $datagenerator->create_user();
        $student2 = $datagenerator->create_user();

        $session1 = $f2fgenerator->create_session_for_course($course1);
        $session2 = $f2fgenerator->create_session_for_course($course1);
        $session3 = $f2fgenerator->create_session_for_course($course2);
        $session4 = $f2fgenerator->create_session_for_course($course3);

        $signup = [];
        $signup[11] = $f2fgenerator->create_signup($student1, $session1, 1, 2);
        $signup[12] = $f2fgenerator->create_signup($student1, $session2, 2, 3);
        $signup[13] = $f2fgenerator->create_signup($student1, $session3, 3, 4);
        $signup[14] = $f2fgenerator->create_signup($student1, $session4, 4, 5);
        $signup[21] = $f2fgenerator->create_signup($student2, $session1, 5, 6);
        $signup[22] = $f2fgenerator->create_signup($student2, $session2, 6, 7);
        $signup[23] = $f2fgenerator->create_signup($student2, $session3, 7, 8);
        $f2fgenerator->create_cancellation($student1, $session1, 3, 1);
        $f2fgenerator->create_cancellation($student2, $session2, 3, 1);
        // Create some file customfields. These will add to the final customfield count.
        $f2fgenerator->create_file_customfield($signup[11], 'signup', 'testfile1.txt', 1);
        $f2fgenerator->create_file_customfield($signup[11], 'signup', 'testfile2.txt', 1);
        $f2fgenerator->create_file_customfield($signup[13], 'signup', 'testfile3.txt', 2);
        $f2fgenerator->create_file_customfield($signup[22], 'cancellation', 'testfile4.txt', 3);

        $targetuser1 = new target_user($student1);
        $targetuser2 = new target_user($student2);

        // System context.
        $this->assertEquals(15, customfields::execute_count($targetuser1, context_system::instance()));
        $this->assertEquals(22, customfields::execute_count($targetuser2, context_system::instance()));

        // Course context.
        $coursecontext1 = context_course::instance($course1->id);
        $coursecontext2 = context_course::instance($course2->id);
        $coursecontext3 = context_course::instance($course3->id);
        $this->assertEquals(7, customfields::execute_count($targetuser1, $coursecontext1));
        $this->assertEquals(15, customfields::execute_count($targetuser2, $coursecontext1));
        $this->assertEquals(4, customfields::execute_count($targetuser1, $coursecontext2));
        $this->assertEquals(7, customfields::execute_count($targetuser2, $coursecontext2));
        $this->assertEquals(4, customfields::execute_count($targetuser1, $coursecontext3));
        $this->assertEquals(0, customfields::execute_count($targetuser2, $coursecontext3));

        // Category context.
        $categorycontext1 = context_coursecat::instance($category1->id);
        $categorycontext2 = context_coursecat::instance($category2->id);
        $this->assertEquals(7, customfields::execute_count($targetuser1, $categorycontext1));
        $this->assertEquals(15, customfields::execute_count($targetuser2, $categorycontext1));
        $this->assertEquals(8, customfields::execute_count($targetuser1, $categorycontext2));
        $this->assertEquals(7, customfields::execute_count($targetuser2, $categorycontext2));

        // Module context
        $coursemodule2 = get_coursemodule_from_instance('facetoface', $session2->facetoface);
        $coursemodule3 = get_coursemodule_from_instance('facetoface', $session3->facetoface);
        $modulecontext2 = context_module::instance($coursemodule2->id);
        $modulecontext3 = context_module::instance($coursemodule3->id);
        $this->assertEquals(2, customfields::execute_count($targetuser1, $modulecontext2));
        $this->assertEquals(10, customfields::execute_count($targetuser2, $modulecontext2));
        $this->assertEquals(4, customfields::execute_count($targetuser1, $modulecontext3));
        $this->assertEquals(7, customfields::execute_count($targetuser2, $modulecontext3));
    }

    public function test_export() {
        $this->setAdminUser(); // Necessary for file handling.
        $datagenerator = phpunit_util::get_data_generator();
        $f2fgenerator = $datagenerator->get_plugin_generator('mod_facetoface');

        $category1 = $datagenerator->create_category();
        $category2 = $datagenerator->create_category();

        $course1 = $datagenerator->create_course(['category' => $category1->id]);
        $course2 = $datagenerator->create_course(['category' => $category2->id]);
        $course3 = $datagenerator->create_course(['category' => $category2->id]);

        $student1 = $datagenerator->create_user();
        $student2 = $datagenerator->create_user();

        $session1 = $f2fgenerator->create_session_for_course($course1);
        $session2 = $f2fgenerator->create_session_for_course($course1);
        $session3 = $f2fgenerator->create_session_for_course($course2);
        $session4 = $f2fgenerator->create_session_for_course($course3);

        // Create signups including customfield data and data params.
        $signup[11] = $f2fgenerator->create_signup($student1, $session1, 1, 2);
        $signup[12] = $f2fgenerator->create_signup($student1, $session2, 2, 3);
        $signup[13] = $f2fgenerator->create_signup($student1, $session3, 3, 4);
        $signup[14] = $f2fgenerator->create_signup($student1, $session4, 4, 5);
        $signup[21] = $f2fgenerator->create_signup($student2, $session1, 5, 6);
        $signup[22] = $f2fgenerator->create_signup($student2, $session2, 6, 7);
        $signup[23] = $f2fgenerator->create_signup($student2, $session3, 7, 8);
        $f2fgenerator->create_cancellation($student1, $session1, 3, 1);
        $f2fgenerator->create_cancellation($student1, $session3, 4, 2);
        $f2fgenerator->create_cancellation($student2, $session2, 5, 3);
        // Create some file customfields. These will add to the final customfield count.
        $f2fgenerator->create_file_customfield($signup[11], 'signup', 'testfile1.txt', 1);
        $f2fgenerator->create_file_customfield($signup[11], 'signup', 'testfile2.txt', 1);
        $f2fgenerator->create_file_customfield($signup[13], 'signup', 'testfile3.txt', 2);
        $f2fgenerator->create_file_customfield($signup[22], 'cancellation', 'testfile4.txt', 3);

        $targetuser1 = new target_user($student1);
        $targetuser2 = new target_user($student2);

        // System context.
        // Student 1
        $export = customfields::execute_export($targetuser1, context_system::instance());
        $data = $export->data;
        $files = $export->files;
        $this->assertCount(12, $data['signup']);
        $this->assertCount(7, $data['cancellation']);
        $this->assert_export_data($data['signup'], [$signup[11]->id, $signup[12]->id, $signup[13]->id, $signup[14]->id], [2, 3, 4, 5]);
        $this->assert_export_data($data['cancellation'], [$signup[11]->id, $signup[13]->id], [1, 2]);
        $this->assert_files(['testfile1.txt', 'testfile2.txt', 'testfile3.txt'], $export);

        // Student 2
        $export = customfields::execute_export($targetuser2, context_system::instance());
        $data = $export->data;
        $this->assertCount(18, $data['signup']);
        $this->assertCount(6, $data['cancellation']);
        $this->assert_export_data($data['signup'], [$signup[21]->id, $signup[22]->id, $signup[23]->id], [6, 7, 8]);
        $this->assert_export_data($data['cancellation'], [$signup[22]->id], [3]);
        $this->assert_files(['testfile4.txt'], $export);

        // Course context
        $coursecontext1 = context_course::instance($course1->id);
        $export = customfields::execute_export($targetuser1, $coursecontext1);
        $data = $export->data;
        $this->assertCount(4, $data['signup']);
        $this->assertCount(3, $data['cancellation']);
        $this->assert_export_data($data['signup'], [$signup[11]->id, $signup[12]->id], [2, 3]);
        $this->assert_export_data($data['cancellation'], [$signup[11]->id], [1]);
        $this->assert_files(['testfile1.txt', 'testfile2.txt'], $export);

        // Category context.
        $categorycontext2 = context_coursecat::instance($category2->id);
        $export = customfields::execute_export($targetuser2, $categorycontext2);
        $data = $export->data;
        $this->assertCount(7, $data['signup']);
        $this->assertCount(0, $data['cancellation']);
        $this->assert_export_data($data['signup'], [$signup[23]->id], [8]);
        $this->assert_export_data($data['cancellation'], [], []);
        $this->assert_files([], $export);

        // Module context
        $coursemodule3 = get_coursemodule_from_instance('facetoface', $session3->facetoface);
        $modulecontext3 = context_module::instance($coursemodule3->id);
        $export = customfields::execute_export($targetuser1, $modulecontext3);
        $data = $export->data;
        $this->assertCount(4, $data['signup']);
        $this->assertCount(4, $data['cancellation']);
        $this->assert_export_data($data['signup'], [$signup[13]->id], [4]);
        $this->assert_export_data($data['cancellation'], [$signup[13]->id], [2]);
        $this->assert_files(['testfile3.txt'], $export);
    }

    /**
     * Assert that the export contains the expected files with the given filenames.
     *
     * @param array $expectedfilenames
     * @param export $export
     */
    private function assert_files(array $expectedfilenames, export $export) {
        $files = $export->files;
        $this->assertCount(count($expectedfilenames), $files);
        foreach ($files as $file) {
            $this->assertContains($file->get_filename(), $expectedfilenames);
        }
    }

    /**
     * Helper method for export data assertions.
     *
     * @param array $data
     * @param array $usersignupids
     * @param array $expectedparamscount
     */
    private function assert_export_data(array $data, array $usersignupids, array $expectedparamscount) {
        $paramscount = [];
        foreach ($data as $customfielddata) {
            if (!empty($customfielddata['params'])) {
                $paramscount[] = count($customfielddata['params']);
            }
            $this->assertNotEmpty($customfielddata['data']);
            $this->assertContains($customfielddata['signupid'], $usersignupids);
        }
        sort($paramscount);
        $this->assertEquals($expectedparamscount, $paramscount);
    }
}
