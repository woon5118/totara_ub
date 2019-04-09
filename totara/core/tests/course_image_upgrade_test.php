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
require_once($CFG->dirroot . "/totara/core/db/upgradelib.php");
require_once($CFG->dirroot . "/course/lib.php");

class totara_core_course_image_upgrade_testcase extends advanced_testcase {
    /**
     * Test suite: We are going to create a bunch of courses, and add the course image with illegal itemid
    * and try to run the upgrade code, and we make sure that those illegal itemid is no longer existing in the system.
     *
     * @return void
     */
    public function test_upgrade_course_images(): void {
        global $DB, $USER;
        $this->resetAfterTest();
        $this->setAdminUser();

        $gen = $this->getDataGenerator();
        $records = [];
        $courses = [];
        for ($i = 0; $i < 5; $i++) {
            $course = $gen->create_course();
            $courses[] = $course;

            $ctx = context_course::instance($course->id);
            // Start preparing the file record for it, no point to prepare draft file, because
            // in the end, draft file will be removed from system (this means for production codes)
            $rc = new stdClass();
            $rc->contenthash = md5(uniqid());
            $rc->pathnamehash = md5(uniqid());
            $rc->contextid = $ctx->id;
            $rc->component = 'course';
            $rc->filearea = 'images';
            $rc->filesize = rand(0, 120);
            $rc->itemid = $course->id;
            $rc->filepath = '/';
            $rc->filename = uniqid('file_');
            $rc->userid = $USER->id;
            $rc->mimetype = 'png';
            $rc->status = 0;
            $rc->source = null;
            $rc->author = 'Bolobala';
            $rc->license = 'Kian Bolobala Bomba';
            $rc->timecreated = time();
            $rc->timemodified = $rc->timecreated;
            $rc->sortorder = 0;

            $rc->id = $DB->insert_record('files', $rc);

            $records[$course->id] = [
                'ctx' => $ctx,
                'record' => $rc
            ];
        }

        totara_core_upgrade_course_images();
        $fs = get_file_storage();

        // Start checking for those illegal files are actually gone gone.
        foreach ($records as $record) {
            $ctx = $record['ctx'];
            $record = $record['record'];

            // There must be no draft files here. pretty sure.
            $files = $fs->get_area_files($ctx->id, 'course', 'images', $record->itemid, "itemid, filepath, filename", false);
            $this->assertEmpty($files);
        }

        // Start checking the course is still able to find its own image.
        foreach ($courses as $course) {
            $url = course_get_image($course);
            $record = $records[$course->id]['record'];

            $this->assertContains($course->cacherev, $url->out());
        }

        $this->assertTrue(true);
    }
}