<?php
/**
 * This file is part of Totara Learn
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
 * @author Mark Metcalfe <mark.metcalfe@totaralearning.com>
 * @package totara_evidence
 */

use core\orm\query\builder;
use totara_evidence\entities\evidence_field_data;
use totara_evidence\entities\evidence_item;
use totara_evidence\entities\evidence_type;
use totara_evidence\entities\evidence_type_field;
use totara_evidence\models;

require_once(__DIR__ . '/evidence_migration_test.php');

/**
 * @group totara_evidence
 */
class totara_evidence_migration_files_testcase extends totara_evidence_migration_testcase {

    protected $fs;

    protected $context;

    protected function setUp(): void {
        parent::setUp();

        $this->fs = get_file_storage();
        $this->context = context_system::instance();
    }

    protected function tearDown(): void {
        parent::tearDown();

        $this->fs = null;
        $this->context = null;
    }

    /**
     * Make sure that files in the file manager fields of evidence items are migrated correctly
     */
    public function test_migrate_files(): void {
        $component = 'totara_customfield';
        $filearea = 'evidence_filemgr';

        $field = $this->generator()->create_evidence_field([
            'fullname' => 'file',
            'shortname' => 'file',
            'datatype' => 'file',
        ]);

        $evidence1 = builder::table('dp_plan_evidence')->insert([
            'name' => 'Evidence 1',
            'readonly' => 0,
            'evidencetypeid' => 0,
            'timecreated' => 0,
            'timemodified' => 0,
            'usermodified' => 0,
            'userid' => 0,
        ]);
        $evidence1_data = builder::table('dp_plan_evidence_info_data')->insert([
            'evidenceid' => $evidence1,
            'fieldid' => $field->id,
            'data' => '',
        ]);
        builder::table('dp_plan_evidence_info_data')->update_record([
            'id' => $evidence1_data,
            'data' => $evidence1_data,
        ]);
        $evidence1_files = [
            'evidence1_file1.txt' => $this->generator()->create_test_file([
                'filearea' => $filearea, 'filename' => 'evidence1_file1.txt', 'itemid' => $evidence1_data,
            ], 'evidence1_file1'),
            'evidence1_file2.txt' => $this->generator()->create_test_file([
                'filearea' => $filearea, 'filename' => 'evidence1_file2.txt', 'itemid' => $evidence1_data,
            ], 'evidence1_file2')
        ];
        foreach ($evidence1_files as $filename => $file) {
            $this->assert_file_has_content($file, $filename, $evidence1_data, $filearea, $component);
        }

        $evidence2 = builder::table('dp_plan_evidence')->insert([
            'name' => 'Evidence 2',
            'readonly' => 0,
            'evidencetypeid' => 0,
            'timecreated' => 0,
            'timemodified' => 0,
            'usermodified' => 0,
            'userid' => 0,
        ]);
        $evidence2_data = builder::table('dp_plan_evidence_info_data')->insert([
            'evidenceid' => $evidence2,
            'fieldid' => $field->id,
            'data' => '',
        ]);
        builder::table('dp_plan_evidence_info_data')->update_record([
            'id' => $evidence2_data,
            'data' => $evidence2_data,
        ]);
        $evidence2_files = [
            'evidence2_file1.txt' => $this->generator()->create_test_file([
                'filearea' => $filearea, 'filename' => 'evidence2_file1.txt', 'itemid' => $evidence2_data,
            ], 'evidence2_file1'),
            'evidence2_file2.txt' => $evidence2_file2 = $this->generator()->create_test_file([
                'filearea' => $filearea, 'filename' => 'evidence2_file2.txt', 'itemid' => $evidence2_data,
            ], 'evidence2_file2'),
        ];
        foreach ($evidence2_files as $filename => $file) {
            $this->assert_file_has_content($file, $filename, $evidence2_data, $filearea, $component);
        }

        // Upgrade should be able to be run multiple times without anything happening to the files
        totara_evidence_migrate();
        totara_evidence_migrate();

        /** @var evidence_field_data $new_evidence1_data */
        $new_evidence1_data = evidence_item::repository()->where('name', 'Evidence 1')->one()->data->first();
        /** @var evidence_field_data $new_evidence2_data */
        $new_evidence2_data = evidence_item::repository()->where('name', 'Evidence 2')->one()->data->first();

        // Make sure copied files exist
        foreach ($evidence1_files as $filename => $file) {
            $this->assert_file_has_content($file, $filename, $new_evidence1_data->id, $filearea, $component);
        }
        foreach ($evidence2_files as $filename => $file) {
            $this->assert_file_has_content($file, $filename, $new_evidence2_data->id, $filearea, $component);
        }

        $this->assert_file_does_not_exist('evidence1_file1.txt', $evidence1_data, $filearea, $component);
        $this->assert_file_does_not_exist('evidence1_file2.txt', $evidence1_data, $filearea, $component);
        $this->assert_file_does_not_exist('evidence2_file1.txt', $evidence2_data, $filearea, $component);
        $this->assert_file_does_not_exist('evidence2_file2.txt', $evidence2_data, $filearea, $component);

        $this->assertEmpty($this->fs->get_area_files($this->context->id, 'totara_customfield', 'temp_evidence_filemgr'));
    }

    /**
     * Make sure that images in evidence text area fields are migrated correctly
     */
    public function test_migrate_textarea_images(): void {
        global $CFG;
        $component = 'totara_customfield';
        $textarea_filearea = 'textarea';
        $evidence_filearea = 'evidence';

        // Make sure this non-evidence text area isn't migrated
        $non_evidence_textarea_file = $this->generator()->create_test_file([
            'filearea' => $textarea_filearea, 'filename' => 'do_not_migrate.txt', 'itemid' => 0,
        ], 'do_not_migrate');

        $field = $this->generator()->create_evidence_field([
            'fullname' => 'textarea',
            'shortname' => 'textarea',
            'datatype' => 'textarea',
            'param1' => implode(' ', [
                "@@PLUGINFILE@@/field_file1.txt",
                "@@PLUGINFILE@@/field%20file%202.txt",
                "@@PLUGINFILE@@/field_file1 (1).txt",
            ]),
        ]);
        $textarea_default_files = [
            'field_file_1.txt' => $this->generator()->create_test_file([
                'filearea' => $textarea_filearea, 'filename' => 'field_file_1.txt', 'itemid' => $field->id,
            ], 'field_file1'),
            'field file 2.txt' => $this->generator()->create_test_file([
                'filearea' => $textarea_filearea, 'filename' => 'field file 2.txt', 'itemid' => $field->id,
            ], 'field_file2'),
            'field_file_1 (1).txt' => $this->generator()->create_test_file([
                'filearea' => $textarea_filearea, 'filename' => 'field_file_1 (1).txt', 'itemid' => $field->id,
            ], 'field_file1' ),
        ];
        foreach ($textarea_default_files as $filename => $file) {
            $this->assert_file_has_content($file, $filename, $field->id, $textarea_filearea, $component);
        }

        $evidence = builder::table('dp_plan_evidence')->insert([
            'name' => 'Evidence',
            'readonly' => 0,
            'evidencetypeid' => 0,
            'timecreated' => 0,
            'timemodified' => 0,
            'usermodified' => 0,
            'userid' => 0,
        ]);
        $textarea_url_base = "{$CFG->wwwroot}/pluginfile.php/{$this->context->id}/{$component}/{$textarea_filearea}/{$field->id}";
        $evidence_field_data = builder::table('dp_plan_evidence_info_data')->insert([
            'evidenceid' => $evidence,
            'fieldid' => $field->id,
            'data' => implode(' ', [
                "$textarea_url_base/field_file_1.txt",
                "$textarea_url_base/field%20file%202.txt",
                "$textarea_url_base/field_file_1%20(1).txt",
                "@@PLUGINFILE@@/field_file_1.txt",
                "@@PLUGINFILE@@/field%20file%202.txt",
                "@@PLUGINFILE@@/field_file_1%20(1).txt",
            ]),
        ]);
        $manual_files = [
            'field_file_1.txt' => $this->generator()->create_test_file([
                'filename' => 'field_file_1.txt', 'itemid' => $evidence_field_data,
            ], 'evidence_file1'),
            'field file 2.txt' => $this->generator()->create_test_file([
                'filename' => 'field file 2.txt', 'itemid' => $evidence_field_data,
            ], 'evidence_file2'),
            'field_file_1 (1).txt' => $this->generator()->create_test_file([
                'filename' => 'field_file_1 (1).txt', 'itemid' => $evidence_field_data,
            ], 'evidence_file3'),
        ];
        foreach ($manual_files as $filename => $file) {
            $this->assert_file_has_content($file, $filename, $evidence_field_data, $evidence_filearea, $component);
        }

        // Upgrade should be able to be run multiple times without anything happening to the files
        totara_evidence_migrate();
        totara_evidence_migrate();

        /** @var evidence_type_field $new_field */
        $new_field = evidence_type_field::repository()->order_by('id')->first();
        /** @var evidence_field_data $new_field_data */
        $new_field_data = evidence_field_data::repository()->order_by('id')->first();

        // The original text area images must still exist
        foreach ($textarea_default_files as $filename => $file) {
            $this->assert_file_has_content($file, $filename, $new_field->id, $textarea_filearea, $component);
        }

        // The "manually" uploaded images (i.e created for the evidence item, not the textarea) must have the same file names
        foreach ($manual_files as $filename => $file) {
            $this->assert_file_has_content($file, $filename, $new_field_data->id, $evidence_filearea, $component);
        }

        // The textarea images need to have been copied into the file area for the individual evidence item
        // and renamed in order to not conflict with the manually uploaded images
        $textarea_copied_files = [
            'field file 2 (1).txt' => $textarea_default_files['field file 2.txt'],
            'field_file_1 (1) (1).txt' => $textarea_default_files['field_file_1.txt'],
            'field_file_1 (2).txt' => $textarea_default_files['field_file_1 (1).txt'],
        ];
        foreach ($textarea_copied_files as $filename => $file) {
            $this->assert_file_has_content($file, $filename, $new_field_data->id, $evidence_filearea, $component);
        }

        // Make sure the files deleted after being copied
        $this->assert_file_does_not_exist('field_file_1.txt', $field->id, $textarea_filearea, $component);
        $this->assert_file_does_not_exist('field file 2.txt', $field->id, $textarea_filearea, $component);
        $this->assert_file_does_not_exist('field_file_1 (1).txt', $field->id, $textarea_filearea, $component);
        $this->assert_file_does_not_exist('field_file_1.txt', $evidence_field_data, $evidence_filearea, $component);
        $this->assert_file_does_not_exist('field file 2.txt', $evidence_field_data, $evidence_filearea, $component);
        $this->assert_file_does_not_exist('field_file_1 (1).txt', $evidence_field_data, $evidence_filearea, $component);

        // Make sure the non-evidence text area file wasn't migrated
        $this->assert_file_has_content($non_evidence_textarea_file, 'do_not_migrate.txt', 0, $textarea_filearea, $component);

        // Make sure the temporary file areas no longer exist
        $this->assertEmpty($this->fs->get_area_files($this->context->id, 'totara_customfield', 'temp_evidence'));
        $this->assertEmpty($this->fs->get_area_files($this->context->id, 'totara_customfield', 'temp_evidence_textarea'));
    }

    /**
     * Make sure the images in the description field of an evidence type are migrated correctly
     */
    public function test_migrate_type_description_images(): void {
        $old_component = 'totara_plan';
        $old_filearea = 'dp_evidence_type';
        $file_record = ['component' => $old_component, 'filearea' => $old_filearea];

        $this->generator()->create_evidence_item(['typeid' => 0]);

        $type1 = $this->generator()->create_evidence_type(['name' => 'Type 1']);
        $type1_files = [
            'type1_file1.txt' => $this->generator()->create_test_file(array_merge($file_record, [
                'filename' => 'type1_file1.txt', 'itemid' => $type1->id
            ]), 'type1_file1'),
            'type1_file2.txt' => $this->generator()->create_test_file(array_merge($file_record, [
                'filename' => 'type1_file2.txt', 'itemid' => $type1->id
            ]), 'type1_file2')
        ];
        $type2 = $this->generator()->create_evidence_type(['name' => 'Type 2']);
        $type2_files = [
            'type2_file1.txt' => $this->generator()->create_test_file(array_merge($file_record, [
                'filename' => 'type2_file1.txt', 'itemid' => $type2->id
            ]), 'type2_file1'),
            'type2_file2.txt' => $this->generator()->create_test_file(array_merge($file_record, [
                'filename' => 'type2_file2.txt', 'itemid' => $type2->id
            ]), 'type2_file2')
        ];

        // The files should now exist
        foreach ($type1_files as $filename => $file) {
            $this->assert_file_has_content($file, $filename, $type1->id, $old_filearea, $old_component);
        }
        foreach ($type2_files as $filename => $file) {
            $this->assert_file_has_content($file, $filename, $type2->id, $old_filearea, $old_component);
        }

        totara_evidence_migrate();

        $new_component = 'totara_evidence';
        $new_filearea = models\evidence_type::DESCRIPTION_FILEAREA;
        /** @var evidence_type $new_type1 */
        $new_type1 = evidence_type::repository()->where('name', $type1->name)->one();
        /** @var evidence_type $new_type2 */
        $new_type2 = evidence_type::repository()->where('name', $type2->name)->one();

        // The files should be migrated to the new component, filearea and the new evidence types
        foreach ($type1_files as $filename => $file) {
            $this->assert_file_has_content($file, $filename, $new_type1->id, $new_filearea, $new_component);
        }
        foreach ($type2_files as $filename => $file) {
            $this->assert_file_has_content($file, $filename, $new_type2->id, $new_filearea, $new_component);
        }

        // The old files should be deleted
        $this->assertEmpty($this->fs->get_area_files($this->context->id, $old_component, $old_filearea));
    }

    private function assert_file_has_content(stored_file $file, string $filename, int $itemid,
                                             string $filearea, string $component): void {
        $this->assertEquals(
            $file->get_content(),
            $this->fs->get_file($this->context->id, $component, $filearea, $itemid, '/', $filename)->get_content()
        );
    }

    private function assert_file_does_not_exist(string $filename, int $itemid, string $filearea, string $component): void {
        $this->assertFalse(
            $this->fs->get_file(
                $this->context->id, $component, $filearea, $itemid, '/', $filename
            )
        );
    }

}
