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
 * @author Mark Metcalfe <mark.metcalfe@totaralearning.com>
 * @package mod_perform
 */

use core\entities\user;
use totara_core\advanced_feature;
use totara_job\job_assignment;
use totara_webapi\phpunit\webapi_phpunit_helper;

/**
 * @group perform
 */
class mod_perform_webapi_resolver_query_selectable_users_testcase extends advanced_testcase {

    private const QUERY = 'mod_perform_selectable_users';

    use webapi_phpunit_helper;

    public function test_ajax_query_successful(): void {
        $generator = self::getDataGenerator();

        // Deliberately giving the users first names so we can assert order as the query result should be sorted by name.
        $manager_user = $generator->create_user(['firstname' => 'Arya']);
        $manager_ja = job_assignment::create_default($manager_user->id);
        $appraiser_user = $generator->create_user(['firstname' => 'Sansa']);

        $user1 = $generator->create_user(['firstname' => 'Bran']);
        job_assignment::create([
            'userid' => $user1->id, 'managerjaid' => $manager_ja->id, 'idnumber' => 1
        ]);
        job_assignment::create([
            'userid' => $user1->id, 'appraiserid' => $appraiser_user->id, 'idnumber' => 2
        ]);

        $user2 = $generator->create_user(['firstname' => 'Jon']);
        job_assignment::create([
            'userid' => $user2->id, 'appraiserid' => $appraiser_user->id, 'idnumber' => 4
        ]);

        // Manager user can see user 1 and themselves
        self::setUser($manager_user);
        $this->assert_same_users([$manager_user, $user1], $this->get_query_data());

        // Appraiser user can see user 1, user 2 and themselves
        self::setUser($appraiser_user);
        $this->assert_same_users([$user1, $user2, $appraiser_user], $this->get_query_data());

        // user1 can see their appraiser and manager, and themselves
        self::setUser($user1);
        $this->assert_same_users([$manager_user, $user1, $appraiser_user], $this->get_query_data());

        // user1 can see their appraiser, and themselves
        self::setUser($user2);
        $this->assert_same_users([$user2, $appraiser_user], $this->get_query_data());
    }

    public function test_ajax_query_failed(): void {
        self::setUser();
        $result = $this->parsed_graphql_operation(self::QUERY);
        $this->assert_webapi_operation_failed($result, 'not logged in');

        self::setAdminUser();
        advanced_feature::disable('performance_activities');
        $result = $this->parsed_graphql_operation(self::QUERY);
        $this->assert_webapi_operation_failed($result, 'Feature performance_activities is not available.');
    }

    /**
     * @param object[]|user[] $expected_users Users created in the test.
     * @param array $actual_users User data returned by GraphQL.
     */
    private function assert_same_users($expected_users, $actual_users): void {
        $this->assertCount(count($expected_users), $actual_users);

        foreach ($expected_users as $i => $expected_user) {
            $this->assertEquals($expected_user->id, $actual_users[$i]['id']);
            $this->assertEquals(fullname($expected_user), $actual_users[$i]['fullname']);
        }
    }

    private function get_query_data(): array {
        $result = $this->parsed_graphql_operation(self::QUERY);
        $this->assert_webapi_operation_successful($result);
        return $this->get_webapi_operation_data($result);
    }

}
