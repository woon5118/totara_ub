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
require_once($CFG->dirroot . '/admin/tool/totara_sync/sources/source_user_csv.php');

class user_csv_field_mapping_test extends totara_sync_csv_testcase {

    protected $elementname = 'user';
    protected $sourcename = 'totara_sync_source_user_csv';

    public function setUp(): void {
        global $CFG;

        parent::setUp();

        $this->source = new $this->sourcename();

        $this->filedir = $CFG->dataroot . '/totara_sync/';
        mkdir($this->filedir . '/csv/ready', 0777, true);

        set_config('element_user_enabled', 1, 'totara_sync');
        set_config('source_user', 'totara_sync_source_user_csv', 'totara_sync');
        set_config('fileaccess', FILE_ACCESS_DIRECTORY, 'totara_sync');
        set_config('filesdir', $this->filedir, 'totara_sync');
    }


    public function test_user_csv_with_field_mapping() {
        global $DB;

        $configcsv = [
            'csvuserencoding' => 'UTF-8',

            'import_username' => '1',
            'import_timemodified' => '1',
            'import_email' => '1',
            'import_firstname' => '1',
            'import_lastname' => '1',
            'import_idnumber' => '1',
            'import_deleted' => '1',

            'fieldmapping_idnumber' => 'useridnumber'
        ];

        foreach ($configcsv as $k => $v) {
            set_config($k, $v, 'totara_sync_source_user_csv');
        }

        $data = file_get_contents(__DIR__ . '/fixtures/user_csv_fieldmapping_1.csv');
        $filepath = $this->filedir . '/csv/ready/user.csv';
        file_put_contents($filepath, $data);

        $element = new totara_sync_element_user();
        $element->set_config('allow_update', '1');
        $element->set_config('allow_create', '1');
        $element->set_config('allow_delete', '1');
        $result = $element->sync();
        $this->assertTrue($result);

        $user = $DB->get_record('user', ['firstname' => 'Clive', 'lastname' => 'Jones']);
        $this->assertEquals('UID3', $user->idnumber);
    }

    public function test_user_csv_with_all_fields_mapped() {
        global $DB;

        $configcsv = [
            'csvuserencoding' => 'UTF-8',

            'import_address' => '1',
            'import_country' => '1',
            'import_deleted' => '1',
            'import_email' => '1',
            'import_firstname' => '1',
            'import_idnumber' => '1',
            'import_lastname' => '1',
            'import_timemodified' => '1',
            'import_username' => '1',

            'fieldmapping_address' => 'a',
            'fieldmapping_country' => 'b',
            'fieldmapping_deleted' => 'c',
            'fieldmapping_email' => 'd',
            'fieldmapping_firstname' => 'e',
            'fieldmapping_idnumber' => 'f',
            'fieldmapping_lastname' => 'g',
            'fieldmapping_timemodified' => 'h',
            'fieldmapping_username' => 'i',
        ];

        foreach ($configcsv as $k => $v) {
            set_config($k, $v, 'totara_sync_source_user_csv');
        }

        $data = file_get_contents(__DIR__ . '/fixtures/user_csv_fieldmapping_2.csv');
        $filepath = $this->filedir . '/csv/ready/user.csv';
        file_put_contents($filepath, $data);

        $element = new totara_sync_element_user();
        $element->set_config('allow_update', '1');
        $element->set_config('allow_create', '1');
        $element->set_config('allow_delete', '1');
        $result = $element->sync();
        $this->assertTrue($result);

        $user = $DB->get_record('user', ['firstname' => 'Test1', 'lastname' => 'User1']);
        $this->assertEquals('UID8', $user->idnumber);
        $this->assertEquals('123 Abc Street', $user->address);
        $this->assertEquals('GB', $user->country);
        $this->assertEquals('testuser@example.com', $user->email);
    }
}
