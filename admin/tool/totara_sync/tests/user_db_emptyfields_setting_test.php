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
 * @author Alastair Munro <alastair.munro@totaralearning.com>
 * @package tool_totara_sync
 */


defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/admin/tool/totara_sync/tests/source_database_testcase.php');
require_once($CFG->dirroot . '/admin/tool/totara_sync/sources/source_user_database.php');

/**
 * @group tool_totara_sync
 */
class tool_totara_sync_user_database_testcase extends totara_sync_database_testcase {

    /**
     * Setup function
     */
    public function setUp() {
        $this->elementname = 'user';
        $this->sourcetable = 'totara_sync_user_source';

        parent::setUp();

        set_config('source_user', 'totara_sync_source_user_database', 'totara_sync');

        $this->resetAfterTest(true);
        $this->preventResetByRollback();
        $this->setAdminUser();

        $this->create_external_db_table();
    }

    /**
     * Teardown function
     */
    public function tearDown() {

        $this->elementname = null;
        $this->sourcetable = null;

        parent::tearDown();
    }

    /**
     * Create table for external DB test
     */
    public function create_external_db_table() {

        $dbman = $this->ext_dbconnection->get_manager();
        $table = new xmldb_table($this->dbtable);

        // Drop table first, if it exists
        if ($dbman->table_exists($this->dbtable)) {
            $dbman->drop_table($table);
        }

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE);
        $table->add_field('idnumber', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL);
        $table->add_field('username', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL);
        $table->add_field('deleted', XMLDB_TYPE_INTEGER, '1');
        $table->add_field('firstname', XMLDB_TYPE_CHAR, '255');
        $table->add_field('lastname', XMLDB_TYPE_CHAR, '255');
        $table->add_field('email', XMLDB_TYPE_CHAR, '100');
        $table->add_field('city', XMLDB_TYPE_CHAR, '120');
        $table->add_field('country', XMLDB_TYPE_CHAR, '2');
        $table->add_field('firstnamephonetic', XMLDB_TYPE_CHAR, '255');
        $table->add_field('lastnamephonetic',  XMLDB_TYPE_CHAR,'255');
        $table->add_field('middlename', XMLDB_TYPE_CHAR, '255');

        $table->add_field('alternamename', XMLDB_TYPE_CHAR, '255');
        $table->add_field('description', XMLDB_TYPE_TEXT);
        $table->add_field('url', XMLDB_TYPE_CHAR, '255');
        $table->add_field('institution', XMLDB_TYPE_CHAR, '255');
        $table->add_field('department', XMLDB_TYPE_CHAR, '255');
        $table->add_field('phone1', XMLDB_TYPE_CHAR, '20');
        $table->add_field('phone2', XMLDB_TYPE_CHAR, '20');
        $table->add_field('address', XMLDB_TYPE_CHAR, '255');

        $table->add_field('suspended', XMLDB_TYPE_INTEGER, 1);

        // idnumber,timemodified,username,deleted,firstname,lastname,email,firstnamephonetic,
        // lastnamephonetic,middlename,alternatename,city,country,description,url,
        // institution,department,phone1,phone2,address,customfield_textinput,
        // customfield_checkbox

        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        $dbman->create_table($table);
    }


    public function test_deleted_field_null() {
        global $DB;

        $user1 = $this->getDataGenerator()->create_user(
            [
                'username' => 'user1', 'idnumber' => 'id101', 'firstname' => 'User',
                'lastname' => 'One', 'deleted' => 0, 'totarasync' => 1, 'email' => 'user@example.com'
            ]
        );

        $user2 = $this->getDataGenerator()->create_user(
            [
                'username' => 'user2', 'idnumber' => 'id102', 'firstname' => 'User',
                'lastname' => 'Two', 'deleted' => 1, 'totarasync' => 1, 'email' => 'user2@example.com'
            ]
        );

        $entry = new stdClass();
        $entry->idnumber = 'id101';
        $entry->username = 'userupdated';
        $entry->timemodified = 0;
        $entry->deleted = 0;
        $entry->firstname = 'User';
        $entry->lastname = 'One';
        $entry->deleted = null;
        $this->ext_dbconnection->insert_record($this->dbtable, $entry);

        $entry = new stdClass();
        $entry->idnumber = 'id102';
        $entry->username = 'user2updated';
        $entry->timemodified = 0;
        $entry->deleted = 0;
        $entry->firstname = 'User';
        $entry->lastname = 'Two';
        $entry->deleted = null;
        $id = $this->ext_dbconnection->insert_record($this->dbtable, $entry);

        $source = new totara_sync_source_user_database();
        $source->set_config('import_idnumber', '1');
        $source->set_config('import_username', '1');
        $source->set_config('import_firstname', '1');
        $source->set_config('import_lastname', '1');
        $source->set_config('import_deleted', '1');
        $source->set_config('import_suspended', '1');

        $element = new totara_sync_element_user();
        $element->set_config('allow_update', '1');
        $element->set_config('allow_delete', '1');
        $this->assertTrue($element->sync());

        $user1_actual = $DB->get_record('user', ['id' => $user1->id]);
        $this->assertEquals('userupdated', $user1_actual->username);
        $this->assertEquals('User', $user1_actual->firstname);
        $this->assertEquals('One', $user1_actual->lastname);
        $this->assertEquals('0', $user1_actual->deleted);

        $user2_actual = $DB->get_record('user', ['id' => $user2->id]);
        $user2email = 'user2@example.com';
        $this->assertEquals($user2email . '.'. $user2_actual->timemodified, $user2_actual->username);
        $this->assertEquals('User', $user2_actual->firstname);
        $this->assertEquals('Two', $user2_actual->lastname);
        $this->assertEquals('1', $user2_actual->deleted);

    }

    public function test_suspended_field_null() {
        global $DB;

        $user = $this->getDataGenerator()->create_user(
            [
                'username' => 'user1', 'idnumber' => 'id101', 'firstname' => 'user',
                'lastname' => 'one', 'suspended' => 0, 'totarasync' => 1
            ]
        );

        $user1 = $this->getDataGenerator()->create_user(
            [
                'username' => 'user2', 'idnumber' => 'id102', 'firstname' => 'user',
                'lastname' => 'two', 'suspended' => 1, 'totarasync' => 1
            ]
        );

        $entry = new stdClass();
        $entry->idnumber = 'id101';
        $entry->username = 'userupdated';
        $entry->timemodified = 0;
        $entry->deleted = 0;
        $entry->firstname = 'User';
        $entry->lastname = 'One';
        $entry->suspended = null;
        $this->ext_dbconnection->insert_record($this->dbtable, $entry);

        $entry = new stdClass();
        $entry->idnumber = 'id102';
        $entry->username = 'user2updated';
        $entry->timemodified = 0;
        $entry->deleted = 0;
        $entry->firstname = 'User';
        $entry->lastname = 'Two';
        $entry->suspended = null;
        $this->ext_dbconnection->insert_record($this->dbtable, $entry);

        $source = new totara_sync_source_user_database();
        $source->set_config('import_idnumber', '1');
        $source->set_config('import_username', '1');
        $source->set_config('import_firstname', '1');
        $source->set_config('import_lastname', '1');
        $source->set_config('import_deleted', '1');
        $source->set_config('import_suspended', '1');

        $element = new totara_sync_element_user();
        $element->set_config('allow_update', '1');
        $element->set_config('allow_delete', '1');
        $this->assertTrue($element->sync());

        $user = $DB->get_record('user', ['idnumber' => 'id101']);
        $this->assertEquals('userupdated', $user->username);
        $this->assertEquals('User', $user->firstname);
        $this->assertEquals('One', $user->lastname);
        $this->assertEquals('0', $user->suspended);

        $user1 = $DB->get_record('user', ['idnumber' => 'id102']);
        $this->assertEquals('user2updated', $user1->username);
        $this->assertEquals('User', $user1->firstname);
        $this->assertEquals('Two', $user1->lastname);
        $this->assertEquals('1', $user1->suspended);
    }

    public function test_suspended_field_empty() {
        global $DB;

        $user = $this->getDataGenerator()->create_user(
            [
                'username' => 'user1', 'idnumber' => 'id101', 'firstname' => 'user',
                'lastname' => 'one', 'suspended' => 0, 'totarasync' => 1
            ]
        );

        $user1 = $this->getDataGenerator()->create_user(
            [
                'username' => 'user2', 'idnumber' => 'id102', 'firstname' => 'user',
                'lastname' => 'two', 'suspended' => 1, 'totarasync' => 1
            ]
        );

        $entry = new stdClass();
        $entry->idnumber = 'id101';
        $entry->username = 'userupdated';
        $entry->timemodified = 0;
        $entry->deleted = 0;
        $entry->firstname = 'User';
        $entry->lastname = 'One';
        $entry->suspended = '';
        $this->ext_dbconnection->insert_record($this->dbtable, $entry);

        $entry = new stdClass();
        $entry->idnumber = 'id102';
        $entry->username = 'user2updated';
        $entry->timemodified = 0;
        $entry->deleted = 0;
        $entry->firstname = 'User';
        $entry->lastname = 'Two';
        $entry->suspended = '';
        $this->ext_dbconnection->insert_record($this->dbtable, $entry);

        $source = new totara_sync_source_user_database();
        $source->set_config('import_idnumber', '1');
        $source->set_config('import_username', '1');
        $source->set_config('import_firstname', '1');
        $source->set_config('import_lastname', '1');
        $source->set_config('import_deleted', '1');
        $source->set_config('import_suspended', '1');

        $element = new totara_sync_element_user();
        $element->set_config('allow_update', '1');
        $element->set_config('allow_delete', '1');
        $this->assertTrue($element->sync());

        $user = $DB->get_record('user', ['idnumber' => 'id101']);
        $this->assertEquals('userupdated', $user->username);
        $this->assertEquals('User', $user->firstname);
        $this->assertEquals('One', $user->lastname);
        $this->assertEquals('0', $user->suspended);

        $user1 = $DB->get_record('user', ['idnumber' => 'id102']);
        $this->assertEquals('user2updated', $user1->username);
        $this->assertEquals('User', $user1->firstname);
        $this->assertEquals('Two', $user1->lastname);
        $this->assertEquals('0', $user1->suspended);
    }

    /**
     * Test behaviour of nulls
     */
    public function test_nulls_in_required_fields() {
        global $DB;

        $user = $this->getDataGenerator()->create_user(
            [
                'username' => 'user1', 'idnumber' => 'id101', 'firstname' => 'user',
                'lastname' => 'one', 'totarasync' => 1
            ]
        );

        $entry = new stdClass();
        $entry->idnumber = 'id101';
        $entry->username = 'userupdated';
        $entry->timemodified = 0;
        $entry->deleted = 0;
        $entry->firstname = null;
        $entry->lastname = null;
        $this->ext_dbconnection->insert_record($this->dbtable, $entry);

        $source = new totara_sync_source_user_database();
        $source->set_config('import_idnumber', '1');
        $source->set_config('import_username', '1');
        $source->set_config('import_firstname', '1');
        $source->set_config('import_lastname', '1');
        $source->set_config('import_deleted', '1');

        $element = new totara_sync_element_user();
        $element->set_config('allow_update', '1');
        $element->set_config('allow_delete', '1');
        // Should fail because nulls in required fields.
        $this->assertFalse($element->sync());

        // Check user is now as we expect.
        $user = $DB->get_record('user', ['idnumber' => 'id101']);
        $this->assertEquals('user1', $user->username);
        $this->assertEquals('user', $user->firstname);
        $this->assertEquals('one', $user->lastname);
    }

    public function test_nulls_in_other_fields() {
        global $DB;

        $user = $this->getDataGenerator()->create_user(
            [
                'username' => 'user1', 'idnumber' => 'id101', 'firstname' => 'user',
                'lastname' => 'one', 'firstnamephonetic' => 'userphonetic', 'lastnamephonetic' => 'onephonetic',
                'middlename' => 'john', 'totarasync' => 1,
                'city' => 'Brighton', 'country' => 'GB'
            ]
        );

        $entry = new stdClass();
        $entry->idnumber = 'id101';
        $entry->username = 'userupdated';
        $entry->timemodified = 0;
        $entry->deleted = 0;
        $entry->firstname = 'user';
        $entry->lastname = 'one';
        $entry->firstnamephonetic = null;
        $entry->lastnamephonetic = null;
        $entry->middlename = null;
        $entry->alternatename = null;
        $entry->city = null;
        $entry->country = null;
        $this->ext_dbconnection->insert_record($this->dbtable, $entry);

        $source = new totara_sync_source_user_database();
        $source->set_config('import_idnumber', '1');
        $source->set_config('import_username', '1');
        $source->set_config('import_firstname', '1');
        $source->set_config('import_lastname', '1');
        $source->set_config('import_firstnamephonetic', '1');
        $source->set_config('import_lastnamephonetic', '1');
        $source->set_config('import_middlename', '1');
        $source->set_config('import_deleted', '1');
        $source->set_config('import_city', '1');
        $source->set_config('import_country', '1');

        $element = new totara_sync_element_user();
        $element->set_config('allow_update', '1');
        $element->set_config('allow_delete', '1');
        $this->assertTrue($element->sync());

        $user = $DB->get_record('user', ['idnumber' => 'id101']);
        $this->assertEquals('userupdated', $user->username);
        $this->assertEquals('user', $user->firstname);
        $this->assertEquals('one', $user->lastname);
        $this->assertEquals('userphonetic', $user->firstnamephonetic);
        $this->assertEquals('onephonetic', $user->lastnamephonetic);
        $this->assertEquals('john', $user->middlename);
        $this->assertEquals('0', $user->suspended);
        $this->assertEquals('Brighton', $user->city);
        $this->assertEquals('GB', $user->country);
    }

    /*
     * Test behaviour of empty strings in required fields
     */
    public function test_empty_strings_required_fields() {
        global $DB;

        $user = $this->getDataGenerator()->create_user(
            [
                'username' => 'user1', 'idnumber' => 'id101', 'firstname' => 'user',
                'lastname' => 'one', 'totarasync' => 1
            ]
        );

        $entry = new stdClass();
        $entry->idnumber = 'id101';
        $entry->username = 'user1';
        $entry->timemodified = 0;
        $entry->deleted = 0;
        $entry->firstname = '';
        $entry->lastname = '';
        $this->ext_dbconnection->insert_record($this->dbtable, $entry);

        $source = new totara_sync_source_user_database();
        $source->set_config('import_idnumber', '1');
        $source->set_config('import_username', '1');
        $source->set_config('import_firstname', '1');
        $source->set_config('import_lastname', '1');
        $source->set_config('import_deleted', '1');

        $element = new totara_sync_element_user();
        $element->set_config('allow_update', '1');
        $element->set_config('allow_delete', '1');
        $element->sync();

        $user = $DB->get_record('user', ['idnumber' => 'id101']);
        $this->assertEquals('user1', $user->username);
        $this->assertEquals('user', $user->firstname);
        $this->assertEquals('one', $user->lastname);
    }

    public function test_empty_strings_other_fields() {
        global $DB;

        $user = $this->getDataGenerator()->create_user(
            [
                'username' => 'user1', 'idnumber' => 'id101', 'firstname' => 'user',
                'lastname' => 'one', 'firstnamephonetic' => 'userphonetic', 'lastnamephonetic' => 'onephonetic',
                'middlename' => 'john', 'totarasync' => 1,
                'city' => 'Brighton', 'country' => 'GB'
            ]
        );

        $entry = new stdClass();
        $entry->idnumber = 'id101';
        $entry->username = 'userupdated';
        $entry->timemodified = 0;
        $entry->deleted = 0;
        $entry->firstname = 'user';
        $entry->lastname = 'one';
        $entry->firstnamephonetic = '';
        $entry->lastnamephonetic = '';
        $entry->middlename = '';
        $entry->alternatename = '';
        $entry->city = '';
        $entry->country = '';
        $this->ext_dbconnection->insert_record($this->dbtable, $entry);

        $source = new totara_sync_source_user_database();
        $source->set_config('import_idnumber', '1');
        $source->set_config('import_username', '1');
        $source->set_config('import_firstname', '1');
        $source->set_config('import_lastname', '1');
        $source->set_config('import_firstnamephonetic', '1');
        $source->set_config('import_lastnamephonetic', '1');
        $source->set_config('import_middlename', '1');
        $source->set_config('import_deleted', '1');
        $source->set_config('import_city', '1');
        $source->set_config('import_country', '1');

        $element = new totara_sync_element_user();
        $element->set_config('allow_update', '1');
        $element->set_config('allow_delete', '1');
        $this->assertTrue($element->sync());

        $user = $DB->get_record('user', ['idnumber' => 'id101']);
        $this->assertEquals('userupdated', $user->username);
        $this->assertEquals('user', $user->firstname);
        $this->assertEquals('one', $user->lastname);
        $this->assertEquals('', $user->firstnamephonetic);
        $this->assertEquals('', $user->lastnamephonetic);
        $this->assertEquals('', $user->middlename);
        $this->assertEquals('', $user->city);
        $this->assertEquals('', $user->country);
    }
}
