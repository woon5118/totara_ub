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
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Kunle Odusan <kunle.odusan@totaralearning.com>
 * @package totara_core
 */

use \totara_core\relationship\relationship_provider;
use \totara_core\relationship\helpers\relationship_collection_manager;

/**
 * Class relationship_manager_test
 */
class relationship_manager_test extends advanced_testcase {

    /**
     * Tests relationship_manager does not throws exception when getting users with valid relationship ids.
     *
     * @return void
     */
    public function test_get_users_for_relationships() {
        $relationships = relationship_provider::get_all_relationships();
        $relationship_ids = array_column($relationships, 'id');
        $relationship_manager = new relationship_collection_manager($relationship_ids);
        $users_for_relationships = $relationship_manager->get_users_for_relationships(['user_id' => 1], $relationship_ids);
        $this->assertEquals(count($users_for_relationships), count($relationship_ids));
    }

    /**
     * Tests relationship_manager throws exception when getting users with invalid relationship ids.
     *
     * @return void
     */
    public function test_throws_exception_for_get_users_for_invalid_relationship() {
        $relationships = relationship_provider::get_all_relationships();
        $relationship_manager = new relationship_collection_manager($relationships);
        $this->expectExceptionMessage('Relationship id not loaded.');
        $relationship_manager->get_users_for_relationships(['user_id' => 1], [0, - 1]);
    }

    /**
     * Tests relationship_manager throws exception when constructed with empty array.
     *
     * @return void
     */
    public function test_throws_exception_when_constructed_with_empty_array() {
        $this->expectExceptionMessage('Relationships required');
        new relationship_collection_manager([]);
    }

    /**
     * Tests relationship_manager throws exception when constructed with invalid relationship ids.
     *
     * @return void
     */
    public function test_throws_exception_when_constructed_with_invalid_id(): void {
        $this->expectExceptionMessage('Invalid relationship id');
        new relationship_collection_manager([0, - 1]);
    }

    /**
     * Tests if relationship_manager can be constructed with either relationship models or ids.
     *
     * @return void
     */
    public function test_can_be_constructed_with_relationship_models_or_ids(): void {
        $relationships = relationship_provider::get_all_relationships();

        new relationship_collection_manager($relationships);
        $this->addToAssertionCount(1);

        $relationship_ids = array_column($relationships, 'id');
        new relationship_collection_manager($relationship_ids);
        $this->addToAssertionCount(1);
    }
}
