<?php
/**
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
 * @author Kian Nguyen <kian.nguyen@totaralearning.com>
 * @package editor_weka
 */
defined('MOODLE_INTERNAL') || die();

class editor_weka_get_files_testcase extends advanced_testcase {
    /**
     * @return void
     */
    protected function setUp(): void {
        global $CFG;
        parent::setUp();

        require_once("{$CFG->dirroot}/lib/filelib.php");
        require_once("{$CFG->dirroot}/lib/editor/weka/lib.php");
    }

    /**
     * @return void
     */
    public function test_get_area_files(): void {
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();

        $context = \context_course::instance($course->id);

        // Save a few files to the course.
        $fs = get_file_storage();
        $file_ids = [];

        for ($i = 0; $i < 5; $i++) {
            $file_record = new stdClass();
            $file_record->contextid = $context->id;
            $file_record->itemid = 0;
            $file_record->component = 'course';
            $file_record->filearea = 'summary';
            $file_record->filename = "file_{$i}.png";
            $file_record->filepath = '/';

            $file = $fs->create_file_from_string($file_record, uniqid());
            $file_ids[] = $file->get_id();
        }

        // Fetching the files from weka text editor.
        $weka_editor = new weka_texteditor();
        $weka_editor->set_contextid($context->id);

        $fetched_files = $weka_editor->get_files('course', 'summary', 0);
        self::assertNotEmpty($fetched_files);
        self::assertCount(5, $fetched_files);

        foreach ($fetched_files as $fetched_file) {
            self::assertContains($fetched_file->get_id(), $file_ids);
        }
    }

    /**
     * @return void
     */
    public function test_get_draft_files(): void {
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();

        $context = \context_course::instance($course->id);

        // Create a file for course summary.
        $fs = get_file_storage();

        $file_record = new stdClass();
        $file_record->contextid = $context->id;
        $file_record->itemid = 0;
        $file_record->component = 'course';
        $file_record->filearea = 'summary';
        $file_record->filepath = '/';
        $file_record->filename = 'file.png';

        $file = $fs->create_file_from_string($file_record, "I'm not angry, I'm just Disappointed");

        // Move the files to draft area for admin user.
        $this->setAdminUser();
        $draft_item_id = null;

        file_prepare_draft_area($draft_item_id, $context->id, 'course', 'summary', 0);

        // Fetch draft files with weka editor.
        $weka_editor = new weka_texteditor();
        $weka_editor->set_contextid($context->id);

        $fetched_files = $weka_editor->get_draft_files($draft_item_id);
        self::assertNotEmpty($fetched_files);
        self::assertCount(1, $fetched_files);

        $fetched_file = reset($fetched_files);

        self::assertEquals('user', $fetched_file->get_component());
        self::assertEquals('draft', $fetched_file->get_filearea());

        // The draft file will be completely different from the area file.
        self::assertNotEquals($file->get_id(), $fetched_file->get_id());

        // However the content will be the same.
        self::assertEquals($file->get_contenthash(), $fetched_file->get_contenthash());
    }
}