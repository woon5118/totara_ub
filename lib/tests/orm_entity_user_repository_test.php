<?php
/*
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
 * @package core
 * @category test
 */

use core\entities\user;

/**
 * Class core_orm_entity_user_repository_testcase
 *
 * @package core
 * @group orm
 */
class core_orm_entity_user_repository_testcase extends advanced_testcase {

    private $user_1;

    private $user_2;

    protected function setUp(): void {
        parent::setUp();
        $this->user_1 = self::getDataGenerator()->create_user([
            'firstname' => 'Samuel',
            'middlename' => 'Leroy',
            'lastname' => 'Jackson',
        ]);
        $this->user_2 = self::getDataGenerator()->create_user([
            'firstname' => 'Ewan',
            'middlename' => 'Gordon',
            'lastname' => 'McGregor',
        ]);
    }

    protected function tearDown(): void {
        parent::tearDown();
        $this->user_1 = $this->user_2 = null;
    }

    /**
     * This is just a sanity check for the repository specifically,
     * The actual logic is thoroughly tested in core_datalib_testcase.
     */
    public function test_filter_users_by_fullname(): void {
        // 'On' is found in user_1's lastname, and in user_2's middlename.
        $query_string = 'On';

        $simple_query = user::repository()
            ->filter_by_full_name($query_string);

        $simple_query_result = $simple_query->get()->all();
        $this->assertCount(1, $simple_query_result);
        $this->assertEquals($this->user_1->id, $simple_query_result[0]->id);

        $complex_query = user::repository()
            ->as('auser')
            ->select_full_name_fields_only()
            ->add_select_raw('COUNT(prefs.id) AS preferences_count')
            ->left_join(['user_preferences', 'prefs'], 'id', 'userid')
            ->group_by('id')
            ->filter_by_full_name($query_string);

        $complex_query_result = $complex_query->get()->all();
        $this->assertCount(1, $complex_query_result);
        $this->assertEquals($this->user_1->id, $complex_query_result[0]->id);
    }

    /**
     * This is just a sanity check for the repository specifically,
     * The actual logic is thoroughly tested in core_datalib_testcase.
     */
    public function test_order_users_by_fullname(): void {
        $simple_query = user::repository()
            ->where_in('id', [$this->user_1->id, $this->user_2->id])
            ->order_by_full_name();

        $simple_query_result = $simple_query->get()->all();
        $this->assertCount(2, $simple_query_result);
        $this->assertEquals($this->user_2->id, $simple_query_result[0]->id);
        $this->assertEquals($this->user_1->id, $simple_query_result[1]->id);

        $complex_query = user::repository()
            ->as('auser')
            ->select_full_name_fields_only()
            ->add_select_raw('COUNT(prefs.id) AS preferences_count')
            ->left_join(['user_preferences', 'prefs'], 'id', 'userid')
            ->group_by(['id', 'firstname', 'lastname'])
            ->filter_by_full_name('e')
            ->where_in('id', [$this->user_1->id, $this->user_2->id])
            ->order_by_full_name();

        $complex_query_result = $complex_query->get()->all();
        $this->assertCount(2, $complex_query_result);
        $this->assertEquals($this->user_2->id, $complex_query_result[0]->id);
        $this->assertEquals($this->user_1->id, $complex_query_result[1]->id);
    }

    /**
     * This is just a sanity check for the repository specifically,
     * The actual logic is thoroughly tested in core_outputcomponents_testcase.
     */
    public function test_select_user_picture_fields(): void {
        $simple_query = user::repository()
            ->where('id', $this->user_1->id)
            ->select_user_picture_fields();

        $simple_query_result = $simple_query->get()->all();
        $this->assertEquals($this->user_1->id, $simple_query_result[0]->id);
        $this->assertEquals($this->user_1->picture, $simple_query_result[0]->picture);

        $complex_query = user::repository()
            ->as('auser')
            ->select_user_picture_fields()
            ->add_select_raw('COUNT(prefs.id) AS preferences_count')
            ->left_join(['user_preferences', 'prefs'], 'id', 'userid')
            ->group_by(['id', 'firstname', 'lastname'])
            ->filter_by_full_name('e')
            ->where('id', $this->user_1->id);

        $complex_query_result = $complex_query->get()->all();
        $this->assertEquals($this->user_1->id, $complex_query_result[0]->id);
        $this->assertEquals($this->user_1->picture, $complex_query_result[0]->picture);
    }

}
