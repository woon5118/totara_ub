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
 * @author Fabian Derschatta <fabian.derschatta@totaralearning.com>
 * @package core_user
 */

use core_user\userdata\customfields;
use totara_userdata\userdata\export;
use totara_userdata\userdata\item;
use totara_userdata\userdata\target_user;

defined('MOODLE_INTERNAL') || die();

/**
 * @group userdata
 * Test purging, exporting and counting of custom fields
 */
class core_user_userdata_customfields_testcase extends advanced_testcase {

    /**
     * test compatible context levels
     */
    public function test_compatible_context_levels() {
        $expectedcontextlevels = [CONTEXT_SYSTEM];
        $this->assertEquals($expectedcontextlevels, customfields::get_compatible_context_levels());
    }

    /**
     * test if data is correctly purged
     */
    public function test_purge() {
        global $DB;

        $this->resetAfterTest(true);

        /** @var totara_core_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('totara_core');
        
        $field1 = $generator->create_custom_profile_field(array('datatype' => 'text'));
        $field2 = $generator->create_custom_profile_field(array('datatype' => 'text'));
        $field3 = $generator->create_custom_profile_field(array('datatype' => 'text'));

        $controluser = new target_user($this->getDataGenerator()->create_user());
        $activeuser = new target_user($this->getDataGenerator()->create_user());
        $suspendeduser = new target_user($this->getDataGenerator()->create_user(['suspended' => 1]));
        $deleteduser = new target_user($this->getDataGenerator()->create_user(['deleted' => 1]));

        $this->set_profile_field_value($activeuser, $field1, 'abc');
        $this->set_profile_field_value($activeuser, $field2, 'abc');
        $this->set_profile_field_value($activeuser, $field3, 'abc');

        $this->set_profile_field_value($suspendeduser, $field1, 'abc');
        $this->set_profile_field_value($suspendeduser, $field2, 'abc');
        $this->set_profile_field_value($suspendeduser, $field3, 'abc');

        $this->set_profile_field_value($deleteduser, $field1, 'abc');
        $this->set_profile_field_value($deleteduser, $field2, 'abc');
        $this->set_profile_field_value($deleteduser, $field3, 'abc');

        $this->set_profile_field_value($controluser, $field1, 'abc');
        $this->set_profile_field_value($controluser, $field2, 'abc');
        $this->set_profile_field_value($controluser, $field3, 'abc');

        $this->assertTrue(customfields::is_purgeable($activeuser->status));
        $this->assertTrue(customfields::is_purgeable($suspendeduser->status));
        $this->assertTrue(customfields::is_purgeable($deleteduser->status));

        // We want to catch the events fired.
        $sink = $this->redirectEvents();

        /****************************
         * PURGE activeuser
         ***************************/
        $result = customfields::execute_purge($activeuser, context_system::instance());
        $this->assertEquals(item::RESULT_STATUS_SUCCESS, $result);
        $this->assertEmpty($DB->get_records('user_info_data', ['userid' => $activeuser->id]));

        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $this->assertInstanceOf(\core\event\user_updated::class, reset($events));
        $sink->clear();

        /****************************
         * PURGE suspendeduser
         ***************************/
        $result = customfields::execute_purge($suspendeduser, context_system::instance());
        $this->assertEquals(item::RESULT_STATUS_SUCCESS, $result);
        $this->assertEmpty($DB->get_records('user_info_data', ['userid' => $suspendeduser->id]));

        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $this->assertInstanceOf(\core\event\user_updated::class, reset($events));
        $sink->clear();

        /****************************
         * PURGE deleteduser
         ***************************/
        $result = customfields::execute_purge($deleteduser, context_system::instance());
        $this->assertEquals(item::RESULT_STATUS_SUCCESS, $result);
        $this->assertEmpty($DB->get_records('user_info_data', ['userid' => $deleteduser->id]));

        $events = $sink->get_events();
        $this->assertCount(0, $events);

        // Control users entries are untouched.
        $this->assertCount(3, $DB->get_records('user_info_data', ['userid' => $controluser->id]));
    }

    /**
     * test if data is correctly counted
     */
    public function test_count() {
        global $DB;

        $this->resetAfterTest(true);

        /** @var totara_core_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('totara_core');

        $field1 = $generator->create_custom_profile_field(array('datatype' => 'text'));
        $field2 = $generator->create_custom_profile_field(array('datatype' => 'text'));
        $field3 = $generator->create_custom_profile_field(array('datatype' => 'text'));

        $activeuser = new target_user($this->getDataGenerator()->create_user());
        $suspendeduser = new target_user($this->getDataGenerator()->create_user(['suspended' => 1]));
        $deleteduser = new target_user($this->getDataGenerator()->create_user(['deleted' => 1]));
        $user = new target_user($this->getDataGenerator()->create_user());

        $this->set_profile_field_value($activeuser, $field1, 'abc');
        $this->set_profile_field_value($activeuser, $field2, 'abc');
        $this->set_profile_field_value($activeuser, $field3, 'abc');

        $this->set_profile_field_value($suspendeduser, $field1, 'abc');
        $this->set_profile_field_value($suspendeduser, $field3, 'abc');

        $this->set_profile_field_value($deleteduser, $field1, 'abc');

        // Do the count.
        $result = customfields::execute_count(new target_user($activeuser), context_system::instance());
        $this->assertEquals(3, $result);

        $result = customfields::execute_count(new target_user($suspendeduser), context_system::instance());
        $this->assertEquals(2, $result);

        $result = customfields::execute_count(new target_user($deleteduser), context_system::instance());
        $this->assertEquals(1, $result);

        $result = customfields::execute_count(new target_user($user), context_system::instance());
        $this->assertEquals(0, $result);
    }


    /**
     * test if data is correctly counted
     */
    public function test_export() {
        global $DB;

        $this->resetAfterTest(true);

        /** @var totara_core_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('totara_core');

        $field1 = $generator->create_custom_profile_field(array('datatype' => 'text'));
        $field2 = $generator->create_custom_profile_field(array('datatype' => 'text'));
        $field3 = $generator->create_custom_profile_field(array('datatype' => 'text'));

        $activeuser = new target_user($this->getDataGenerator()->create_user());
        $deleteduser = new target_user($this->getDataGenerator()->create_user(['deleted' => 1]));
        $user = new target_user($this->getDataGenerator()->create_user());

        $this->set_profile_field_value($activeuser, $field1, 'a');
        $this->set_profile_field_value($activeuser, $field2, 'b');
        $this->set_profile_field_value($activeuser, $field3, 'c');

        $this->set_profile_field_value($deleteduser, $field1, 'd');
        $this->set_profile_field_value($deleteduser, $field2, 'e');
        $this->set_profile_field_value($deleteduser, $field3, 'f');

        /****************************
         * EXPORT activeuser
         ***************************/

        $result = customfields::execute_export(new target_user($activeuser), context_system::instance());
        $this->assertInstanceOf(export::class, $result);
        $this->assertCount(3, $result->data);

        $expectedfield1 = ['shortname' => $field1->shortname, 'data' => 'a'];
        $expectedfield2 = ['shortname' => $field2->shortname, 'data' => 'b'];
        $expectedfield3 = ['shortname' => $field3->shortname, 'data' => 'c'];

        $this->assertContains($expectedfield1, $result->data);
        $this->assertContains($expectedfield2, $result->data);
        $this->assertContains($expectedfield3, $result->data);

        /****************************
         * EXPORT deleteduser
         ***************************/

        $result = customfields::execute_export(new target_user($deleteduser), context_system::instance());
        $this->assertInstanceOf(export::class, $result);
        $this->assertCount(3, $result->data);

        $expectedfield1 = ['shortname' => $field1->shortname, 'data' => 'd'];
        $expectedfield2 = ['shortname' => $field2->shortname, 'data' => 'e'];
        $expectedfield3 = ['shortname' => $field3->shortname, 'data' => 'f'];

        $this->assertContains($expectedfield1, $result->data);
        $this->assertContains($expectedfield2, $result->data);
        $this->assertContains($expectedfield3, $result->data);

        /****************************
         * EXPORT user
         ***************************/

        $result = customfields::execute_export(new target_user($user), context_system::instance());
        $this->assertInstanceOf(export::class, $result);
        $this->assertCount(0, $result->data);
    }

    /**
     * @param stdClass $user
     * @param stdClass $field
     * @param string $data
     * @param int $dataformat
     *
     * @return int
     */
    private function set_profile_field_value(stdClass $user, stdClass $field, string $data, int $dataformat = 0): int {
        global $DB;

        $record = new stdClass();
        $record->fieldid = $field->id;
        $record->userid = $user->id;
        $record->data = $data;
        $record->dataformat = $dataformat;

        return $DB->insert_record('user_info_data', $record);
    }

}