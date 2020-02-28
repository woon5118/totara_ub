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
 * @author Murali Nair <murali.nair@totaralearning.com>
 * @package mod_perform
 * @category test
 */

defined('MOODLE_INTERNAL') || die();

use core\webapi\execution_context;

use mod_perform\webapi\resolver\query\user_grouping_users;
use mod_perform\webapi\resolver\type\user_grouping_user;

use totara_webapi\graphql;

/**
 * @coversDefaultClass user_grouping_users.
 *
 * @group perform
 *
 * TODO: this should be combined with totara_competency/user_groups and put into
 * totara core somewhere.
 */
class mod_perform_webapi_query_user_grouping_users_testcase extends advanced_testcase {
    /**
     * @covers ::resolve
     */
    public function test_find(): void {
        $groups_by_id = $this->setup_env();

        $context = $this->get_webapi_context();
        $actual_groups = user_grouping_users::resolve([], $context);

        // NB every test runs with these 2 users already created: admin and guest.
        // Hence need to account for these.
        $this->assertCount(count($groups_by_id) + 2, $actual_groups, 'wrong retrieve count');

        $admin_id = (int)get_admin()->id;

        foreach ($actual_groups as $group) {
            $group_id = $group->id;
            if ($group_id === $admin_id || isguestuser($group_id)) {
                continue;
            }

            $expected = $groups_by_id[$group_id] ?? null;

            $this->assertNotNull($expected, 'unknown group retrieved');
            $this->assertEquals($expected->firstname, $group->firstname, 'wrong first name');
            $this->assertEquals($expected->lastname, $group->lastname, 'wrong last name');
            $this->assertEquals(fullname($expected), fullname($group), 'wrong full name');
            $this->assertEquals($expected->idnumber, $group->idnumber, 'wrong idnumber');
        }
    }


    /**
     * @covers ::resolve
     */
    public function test_successful_ajax_call(): void {
        $groups_by_id = $this->setup_env(1);

        $context = $this->get_webapi_context();
        $actual_groups = $this->exec_graphql($context, []);

        // NB every test runs with these 2 users already created: admin and guest.
        // Hence need to account for these.
        $this->assertCount(count($groups_by_id) + 2, $actual_groups, 'wrong retrieve count');

        $admin_id = (int)get_admin()->id;

        foreach ($actual_groups as $group) {
            $group_id = (int)$group['id'] ?? null;
            if ($group_id === $admin_id || isguestuser($group_id)) {
                continue;
            }

            $this->assertNotNull($group_id, 'no retrieved group id');
            $this->assertArrayHasKey($group_id, $groups_by_id, 'unknown assignment');

            $expected = $this->graphql_return($groups_by_id[$group_id], $context);
            $this->assertEquals($expected, $group, 'wrong graphql return');
        }
    }

    /**
     * Generates test data.
     *
     * @param int $count no of users to generate.
     *
     * @return array[int=>stdClass] the mapping of user ids to users.
     */
    private function setup_env(int $count=10): array {
        $this->setAdminUser();

        $grouping = [];
        foreach (range(0, $count - 1) as $unused) {
            $group = $this->getDataGenerator()->create_user();
            $grouping[$group->id] = $group;
        }

        return $grouping;
    }

    /**
     * Given the input group, returns the data the graphql call is supposed to
     * return.
     *
     * @param \stdClass $group source datea.
     * @param execution_context $context graphql execution context.
     *
     * @return array the expected graphql data values.
     */
    private function graphql_return(\stdClass $group, execution_context $context): array {
        $resolve = function (string $field) use ($group, $context) {
            return user_grouping_user::resolve($field, $group, [], $context);
        };

        return [
            'id' => $resolve('id'),
            'firstname' => $resolve('firstname'),
            'lastname' => $resolve('lastname'),
            'fullname' => $resolve('fullname'),
            'idnumber' => $resolve('idnumber')
        ];
    }

    /**
     * Executes the test query via AJAX.
     *
     * @param execution_context $context graphql execution context.
     * @param array $args ajax arguments if any.
     *
     * @return array|string either the retrieved items or the error string for
     *         failures.
     */
    private function exec_graphql(execution_context $context, array $args=[]) {
        $result = graphql::execute_operation($context, $args)->toArray(true);

        $op = $context->get_operationname();
        $errors = $result['errors'] ?? null;
        if ($errors) {
            $error = $errors[0];
            $msg = $error['debugMessage'] ?? $error['message'];

            return sprintf(
                "invocation of %s://%s failed: %s",
                $context->get_type(),
                $op,
                $msg
            );
        }

        return array_values($result['data'][$op]);
    }

    /**
     * Creates an graphql execution context.
     *
     * @return execution_context the context.
     */
    private function get_webapi_context(): execution_context {
        return execution_context::create('ajax', 'mod_perform_user_grouping_users');
    }
}
