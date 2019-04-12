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

class totara_core_upgrade_course_defaultimage_testcase extends advanced_testcase {
    /**
     * @return void
     */
    public function test_upgrade(): void {
        global $CFG;
        require_once("{$CFG->dirroot}/totara/core/db/upgradelib.php");

        $this->resetAfterTest();
        $this->setAdminUser();

        $context = context_system::instance();

        // We want the item id to not be a zero, so that this test is able to assure that the file at this itemid
        // is no longer exist after upgrade.
        $fileinfo = [
            'contextid' => $context->id,
            'component' => 'course',
            'filearea' => 'defaultimage',
            'filepath' => '/',
            'filename' => 'hello_world.png',
            'mimetype' => 'png',
            'itemid' => 999,
            'license' => 'public'
        ];

        $fs = get_file_storage();
        $fs->create_file_from_string($fileinfo, 'Hello world !!!');

        $files = $fs->get_area_files($context->id, 'course', 'defaultimage', false, 'itemid, filepath, filename', false);
        $file = reset($files);

        $url = "{$CFG->wwwroot}/pluginfile.php/{$context->id}/course/defaultimage/{$file->get_filename()}";
        set_config('defaultimage', $url, 'course');
        totara_core_upgrade_course_defaultimage_config();

        $a = get_config('course', 'defaultimage');
        $this->assertNotEmpty($a);
        $this->assertEquals($file->get_filepath() . $file->get_filename(), $a);

        // Check whether there is file presenting in mdl_file storage.
        $fs = get_file_storage();
        $context = context_system::instance();

        $files = $fs->get_area_files(
            $context->id,
            'course',
            'defaultimage',
            false,
            'itemid, filepath, filename',
            false
        );

        $this->assertCount(1, $files);

        $file = reset($files);
        $this->assertEquals(0, $file->get_itemid());

        // Start creating a default course, trying to get a default image and expect it to be equal with the
        // the one just upgraded.
        $gen = $this->getDataGenerator();
        $course = $gen->create_course();

        $themerev = theme_get_revision();
        $expected = "{$CFG->wwwroot}/pluginfile.php/{$context->id}/course/defaultimage/{$themerev}/{$file->get_filename()}";

        $imageurl = course_get_image($course);
        $this->assertEquals($expected, $imageurl->out());
    }
}