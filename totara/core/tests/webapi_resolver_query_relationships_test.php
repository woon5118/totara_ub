<?php
/*
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
 * @author Mark Metcalfe <mark.metcalfe@totaralearning.com>
 * @package totara_core
 */

use core\webapi\execution_context;
use totara_core\relationship\relationship;
use totara_core\webapi\resolver\query\relationships;
use totara_core\entities\relationship as relationship_entity;

require_once(__DIR__ . '/relationship_resolver_test.php');

class totara_core_webapi_resolver_query_relationships_testcase extends advanced_testcase {

    protected function setUp() {
        parent::setUp();
        relationship_entity::repository()->delete();
    }

    /**
     * @return relationship[]
     */
    private function get_query(): array {
        return relationships::resolve([], execution_context::create('dev'));
    }

    /**
     * @return relationship[]
     */
    private function create_data(): array {
        return [
            relationship::create([test_resolver_one::class]),
            relationship::create([test_resolver_two::class]),
            relationship::create([test_resolver_three::class]),
            relationship::create([test_resolver_four::class]),
        ];
    }

    public function test_resolve_query() {
        $this->setAdminUser();

        [$relationship1, $relationship2, $relationship3, $relationship4] = $this->create_data();

        $this->assertEquals(4, relationship_entity::repository()->count());

        $results = $this->get_query();
        $this->assertCount(4, $results);

        $this->assertEquals($relationship1->get_id(), $results[0]->get_id());
        $this->assertEquals($relationship2->get_id(), $results[1]->get_id());
        $this->assertEquals($relationship3->get_id(), $results[2]->get_id());
        $this->assertEquals($relationship4->get_id(), $results[3]->get_id());
    }

    public function test_require_login() {
        $this->setAdminUser();

        $this->get_query();

        $this->setUser(null);

        $this->expectException(require_login_exception::class);
        $this->get_query();
    }

}
