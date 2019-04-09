<?php
/*
 * This file is part of Totara LMS
 *
 * Copyright (C) 2019 onwards Totara Learning Solutions LTD
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
 * @author Kian Nguyen <kian.nguyen@totaralearning.com>
 * @package totara_core
 */

defined('MOODLE_INTERNAL') || die();
global $CFG;
require_once("{$CFG->dirroot}/totara/core/db/upgradelib.php");

class totara_core_upgrade_course_defaultimage_testcase extends advanced_testcase {
    /**
     * @return array
     */
    public function provide_test_data(): array {
        global $CFG;
        $itemid1 = time();
        $itemid2 = time() + 260;

        $context = context_system::instance();

        return [
            [
                $CFG->wwwroot . "/pluginfile.php/1/course/defaultimage/{$itemid1}/egg.png",
                [
                    'contenthash' => md5('x'),
                    'pathnamehash' => md5('boom'),
                    'contextid' => $context->id,
                    'component' => 'course',
                    'filearea' => 'defaultimage',
                    'itemid' => $itemid1,
                    'filepath' => '/',
                    'filename' => 'egg.png',
                    'filesize' => time(),
                    'mimetype' => 'png',
                    'license' => 'public',
                    'timecreated' => time(),
                    'timemodified' => time(),
                    0
                ]
            ],
            [
                // Test case without www root
                "/pluginfile.php/1/course/defaultimage/{$itemid2}/egg.png",
                [
                    'contenthash' => md5('x'),
                    'pathnamehash' => md5('boom'),
                    'contextid' => $context->id,
                    'component' => 'course',
                    'filearea' => 'defaultimage',
                    'itemid' => $itemid2,
                    'filepath' => '/',
                    'filename' => 'egg.png',
                    'filesize' => time(),
                    'mimetype' => 'png',
                    'license' => 'public',
                    'timecreated' => time(),
                    'timemodified' => time(),
                    0
                ]
            ]
        ];
    }

    /**
     * @dataProvider provide_test_data
     *
     * @param string $path
     * @param array  $fileinfo
     * @return void
     */
    public function test_upgrade(string $path, array $fileinfo): void {
        global $DB, $USER;

        $this->resetAfterTest();
        $this->setAdminUser();

        $fileinfo['userid'] = $USER->id;

        $DB->insert_record('files', $fileinfo);

        set_config('defaultimage', $path, 'course');
        totara_core_upgrade_course_defaultimage_config();

        $a = get_config('course', 'defaultimage');
        $this->assertNotEmpty($a);
        $this->assertEquals($fileinfo['filepath'] . $fileinfo['filename'], $a);

        // Check whether there is file presenting in mdl_file storage.
        $fs = get_file_storage();
        $context = context_system::instance();

        $files = $fs->get_area_files($context->id, 'course', 'defaultimage');
        $this->assertNotEmpty($files);

        $files = array_values(
            array_filter(
                $files,
                function (stored_file $file): bool {
                    return !$file->is_directory();
                }
            )
        );

        $this->assertCount(1, $files);

        /** @var stored_file $file */
        $file = $files[0];

        $this->assertEquals(0, $file->get_itemid());
    }

    /**
     * Test suite of uprading default image when the file has spaces in it.
     * @return void
     */
    public function test_upgrade_file_with_spaces(): void {
        global $USER, $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        $id = time();

        $filename = rawurlencode('bohemian rhapsody.png');
        $filepath = "/pluginfile.php/1/course/defaultimage/{$id}/{$filename}";

        set_config('defaultimage', $filepath, 'course');

        // Save draft for this image, then we start saving the default image, that is the behaviour of most uploading action here
        // though.
        $record = new stdClass();
        $record->contenthash = md5(uniqid());
        $record->pathnamehash = md5(uniqid());
        $record->contextid = context_system::instance()->id;
        $record->component = 'course';
        $record->filearea = 'draft';
        $record->itemid = $id;
        $record->filepath = '/';
        $record->filename = 'bohemian rhapsody.png';
        $record->license = 'x';
        $record->filesize = time();
        $record->userid = $USER->id;
        $record->timecreated = time();
        $record->timemodified = time();

        $DB->insert_record('files', $record);

        // Start saving the proper file here, with the area of 'defaultimage'
        $record->filearea = 'defaultimage';
        $record->timecreated = time() + 20;
        $record->timemodified = time() + 20;
        $record->contenthash = md5(uniqid());
        $record->pathnamehash = md5(uniqid());

        $DB->insert_record('files', $record);

        totara_core_upgrade_course_defaultimage_config();

        // After upgrade
        $value = get_config('course', 'defaultimage');
        $this->assertEquals('/bohemian rhapsody.png', $value);

        // Check whether there is file presenting in mdl_file storage.
        $fs = get_file_storage();
        $context = context_system::instance();

        $files = $fs->get_area_files($context->id, 'course', 'defaultimage');
        $this->assertNotEmpty($files);

        $files = array_values(
            array_filter(
                $files,
                function (stored_file $file): bool {
                    return !$file->is_directory();
                }
            )
        );

        $this->assertCount(1, $files);

        /** @var stored_file $file */
        $file = $files[0];

        $this->assertEquals(0, $file->get_itemid());
    }
}