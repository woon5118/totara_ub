<?php
/*
 * This file is part of Totara Learn
 *
 * Copyright (C) 2021 onwards Totara Learning Solutions LTD
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
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Alastair Munro <alastair.munro@totaralearning.com>
 * @package tool_totara_sync
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/admin/tool/totara_sync/tests/source_csv_testcase.php');
require_once($CFG->dirroot . '/admin/tool/totara_sync/sources/source_comp_csv.php');

/**
 * @group tool_totara_sync
 */
class comp_csv_field_mapping_test extends totara_sync_csv_testcase {

    protected $elementname = 'comp';
    protected $sourcename = 'totara_sync_source_comp_csv';

    public function setUp(): void {
        global $CFG;

        parent::setUp();

        $this->source = new $this->sourcename();

        $this->filedir = $CFG->dataroot . '/totara_sync/';
        mkdir($this->filedir . '/csv/ready', 0777, true);

        set_config('element_comp_enabled', 1, 'totara_sync');
        set_config('source_comp', 'totara_sync_source_comp_csv', 'totara_sync');
        set_config('fileaccess', FILE_ACCESS_DIRECTORY, 'totara_sync');
        set_config('filesdir', $this->filedir, 'totara_sync');
    }

    public function test_comp_csv_with_field_mapping() {
        global $DB, $CFG;

        $hierarchygenerator = $this->getDataGenerator()->get_plugin_generator('totara_hierarchy');
        $compframework = $hierarchygenerator->create_framework('competency', ['idnumber' => 'COMPF1']);

        $configcsv = [
            'fieldmapping_idnumber' => 'compidnumber',

            'import_idnumber' => '1',
            'import_fullname' => '1',
            'import_frameworkidnumber' => '1',
            'import_timemodified' => '1',
            'import_typeidnumber' => '0',
            'import_deleted' => '1',
        ];

        foreach ($configcsv as $k => $v) {
            set_config($k, $v, 'totara_sync_source_comp_csv');
        }

        $data = file_get_contents(__DIR__ . '/fixtures/competencies_field_mapping_3.csv');
        $filepath = $this->filedir . '/csv/ready/comp.csv';
        file_put_contents($filepath, $data);

        $element = new totara_sync_element_comp();
        $element->set_config('allow_update', '1');
        $element->set_config('allow_create', '1');
        $result = $element->sync();
        $this->assertTrue($result);

        $competency = $DB->get_record('comp', ['fullname' => 'Competency 3']);
        $this->assertEquals('CID3', $competency->idnumber);
    }
}
