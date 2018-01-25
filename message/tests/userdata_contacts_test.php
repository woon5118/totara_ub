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
 * @package core_message
 */

namespace core_message\userdata;

use context_system;
use context_user;
use totara_userdata\userdata\export;
use totara_userdata\userdata\item;
use totara_userdata\userdata\target_user;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/userdata_messages_testcase.php');

/**
 * @group userdata
 */
class core_message_userdata_contacts_testcase extends userdata_messages_testcase {

    /**
     * Testing the compatible context and is_[purgable|exportable|countable] methods
     */
    public function test_general_properties() {
        $this->assertEquals([CONTEXT_SYSTEM], contacts::get_compatible_context_levels());
        $this->assertTrue(contacts::is_exportable());
        $this->assertTrue(contacts::is_countable());
        $this->assertTrue(contacts::is_purgeable(target_user::STATUS_ACTIVE));
        $this->assertTrue(contacts::is_purgeable(target_user::STATUS_SUSPENDED));
        $this->assertTrue(contacts::is_purgeable(target_user::STATUS_DELETED));
    }

    /**
     * test if contacts are purged
     */
    public function test_purge() {
        global $DB;

        $this->resetAfterTest(true);

        // Set up users.
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $user3 = $this->getDataGenerator()->create_user();

        // User 1 blocked user 2.
        $DB->insert_record('message_contacts', (object)['userid' => $user1->id, 'contactid' => $user2->id]);
        // User 1 blocked user 3.
        $DB->insert_record('message_contacts', (object)['userid' => $user1->id, 'contactid' => $user3->id]);
        // User 2 blocked user 1.
        $DB->insert_record('message_contacts', (object)['userid' => $user2->id, 'contactid' => $user1->id]);
        // User 3 blocked user 2.
        $DB->insert_record('message_contacts', (object)['userid' => $user3->id, 'contactid' => $user2->id]);

        $targetuser = new target_user($user1);
        // Purge data.
        $result = contacts::execute_purge($targetuser, context_system::instance());
        $this->assertEquals(item::RESULT_STATUS_SUCCESS, $result);

        // Check if expected data is there.
        $this->assertEmpty($DB->get_record('message_contacts', ['userid' => $user1->id, 'contactid' => $user2->id]));
        $this->assertEmpty($DB->get_record('message_contacts', ['userid' => $user1->id, 'contactid' => $user3->id]));

        // User 2 and 3 shouldn't be affected.
        $this->assertNotEmpty($DB->get_record('message_contacts', ['userid' => $user2->id, 'contactid' => $user1->id]));
        $this->assertNotEmpty($DB->get_record('message_contacts', ['userid' => $user3->id, 'contactid' => $user2->id]));
    }

    /**
     * test if contacts are correctly counte
     */
    public function test_count() {
        global $DB;

        $this->resetAfterTest(true);

        // Set up users.
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $user3 = $this->getDataGenerator()->create_user();

        // User 1 blocked user 2.
        $DB->insert_record('message_contacts', (object)['userid' => $user1->id, 'contactid' => $user2->id]);
        // User 1 blocked user 3.
        $DB->insert_record('message_contacts', (object)['userid' => $user1->id, 'contactid' => $user3->id]);
        // User 2 blocked user 1.
        $DB->insert_record('message_contacts', (object)['userid' => $user2->id, 'contactid' => $user1->id]);
        // User 3 blocked user 2.
        $DB->insert_record('message_contacts', (object)['userid' => $user3->id, 'contactid' => $user2->id]);

        // Count data for user 1.
        $targetuser = new target_user($user1);
        $result = contacts::execute_count($targetuser, context_system::instance());
        $this->assertEquals(2, $result);

        // Count data for user 3.
        $targetuser = new target_user($user3);
        $result = contacts::execute_count($targetuser, context_system::instance());
        $this->assertEquals(1, $result);
    }

    /**
     * test if contacts are exported (and only the contacts added by the user himself)
     */
    public function test_export() {
        global $DB;

        $this->resetAfterTest(true);

        // Set up users.
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $user3 = $this->getDataGenerator()->create_user();

        // User 1 blocked user 2.
        $contactid1 = $DB->insert_record('message_contacts', (object)['userid' => $user1->id, 'contactid' => $user2->id]);
        // User 1 blocked user 3.
        $contactid2 = $DB->insert_record('message_contacts', (object)['userid' => $user1->id, 'contactid' => $user3->id]);
        // User 2 blocked user 1.
        $contactid3 = $DB->insert_record('message_contacts', (object)['userid' => $user2->id, 'contactid' => $user1->id]);
        // User 3 blocked user 2.
        $contactid4 = $DB->insert_record('message_contacts', (object)['userid' => $user3->id, 'contactid' => $user2->id]);

        // Count data for user 1.
        $targetuser = new target_user($user1);
        $result = contacts::execute_export($targetuser, context_system::instance());
        $this->assertInstanceOf(export::class, $result);
        $this->assertCount(2, $result->data);
        $this->assertArrayHasKey($contactid1, $result->data);
        $this->assertArrayHasKey($contactid2, $result->data);

        // Count data for user 2.
        $targetuser = new target_user($user2);
        $result = contacts::execute_export($targetuser, context_system::instance());
        $this->assertInstanceOf(export::class, $result);
        $this->assertCount(1, $result->data);
        $this->assertArrayHasKey($contactid3, $result->data);

        // Count data for user 3.
        $targetuser = new target_user($user3);
        $result = contacts::execute_export($targetuser, context_system::instance());
        $this->assertInstanceOf(export::class, $result);
        $this->assertCount(1, $result->data);
        $this->assertArrayHasKey($contactid4, $result->data);
    }

}
