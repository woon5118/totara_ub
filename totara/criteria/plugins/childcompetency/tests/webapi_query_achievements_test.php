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
 * @author Aleksandr Baishev <aleksandr.baishev@totaralearning.com>
 * @package criteria_childcompetency
 */

use core\orm\collection;
use core\orm\query\builder;
use core\webapi\execution_context;
use criteria_childcompetency\webapi\resolver\query\achievements;
use tassign_competency\entities\assignment;
use tassign_competency\entities\competency_assignment_user_log;
use tassign_competency\expand_task;
use totara_assignment\user_groups;
use totara_competency\entities\assignment_availability;
use totara_criteria\criterion;
use totara_criteria\criterion_not_found_exception;
use totara_competency\entities\competency;

class criteria_childcompetency_webapi_query_achievements_testcase extends \advanced_testcase {

     /**
      * Test configuration display - aggregate all
      */
    public function test_it_allows_to_view_own_child_competency_achievements() {

        $data = $this->create_data();

        // Logging in...
        $this->setUser($data['user']);

        // Righto, let's assert what we need.
        $result = achievements::resolve([
            'instance_id' => $data['criterion']->get_id(),
            'user_id' => $data['user']->id,
            'assignment_id' => $data['assignment']->id,
        ], $this->get_execution_context());

        /** @var collection $items */
        $items = $result['items'] ?? null;

        $this->assertEquals(criterion::AGGREGATE_ALL, $result['aggregation_method']);
        $this->assertEquals(1, $result['required_items']);
        $this->assertInstanceOf(collection::class, $items);

        $this->assertEqualsCanonicalizing(
            [$data['competencies'][1]->id, $data['competencies'][2]->id, $data['competencies'][3]->id],
            $items->pluck('id')
        );

        // Now let's assert that it returns correct achievement values

        // Competency 1
        $this->assertEquals($data['proficient_value']->id, $items->item($data['competencies'][1]->id)['value']->id);

        // Competency 2
        $this->assertNull($items->item($data['competencies'][2]->id)['value']);

        // Competency 3
        $this->assertNull($items->item($data['competencies'][3]->id)['value']);

        // Now let's take away the capability and check that there is an error
        assign_capability('totara/competency:view_own_profile', CAP_PROHIBIT, $data['role']->id, context_system::instance(), true);

        $this->expectException(required_capability_exception::class);
        $this->expectExceptionMessage('Sorry, but you do not currently have permissions to do that (View own competency profile)');
        achievements::resolve([
            'instance_id' => $data['criterion']->get_id(),
            'user_id' => $data['user']->id,
            'assignment_id' => $data['assignment']->id,
        ], $this->get_execution_context());
    }
     /**
      * Test configuration display - aggregate all
      */
    public function test_it_allows_to_view_other_child_competency_achievements() {

        $data = $this->create_data();

        $logged_user = $this->getDataGenerator()->create_user();

        $this->getDataGenerator()->role_assign($data['role']->id, $logged_user->id);
        assign_capability('totara/competency:view_other_profile', CAP_ALLOW, $data['role']->id, context_user::instance($data['user']->id));

        $this->setUser($logged_user);

        // Righto, let's assert what we need.
        $result = achievements::resolve([
            'instance_id' => $data['criterion']->get_id(),
            'user_id' => $data['user']->id,
            'assignment_id' => $data['assignment']->id,
        ], $this->get_execution_context());

        /** @var collection $items */
        $items = $result['items'] ?? null;

        $this->assertEquals(criterion::AGGREGATE_ALL, $result['aggregation_method']);
        $this->assertEquals(1, $result['required_items']);
        $this->assertInstanceOf(collection::class, $items);


        $this->assertEqualsCanonicalizing(
            [$data['competencies'][1]->id, $data['competencies'][2]->id, $data['competencies'][3]->id],
            $items->pluck('id')
        );

        // Now let's assert that it returns correct achievement values

        // Competency 1
        $this->assertEquals($data['proficient_value']->id, $items->item($data['competencies'][1]->id)['value']->id);

        // Competency 2
        $this->assertNull($items->item($data['competencies'][2]->id)['value']);

        // Competency 3
        $this->assertNull($items->item($data['competencies'][3]->id)['value']);

        // Now let's take away the capability and check that there is an error
        assign_capability('totara/competency:view_other_profile', CAP_PROHIBIT, $data['role']->id, context_user::instance($data['user']->id), true);

        $this->expectException(required_capability_exception::class);
        $this->expectExceptionMessage('Sorry, but you do not currently have permissions to do that (View profile of other users)');
        achievements::resolve([
            'instance_id' => $data['criterion']->get_id(),
            'user_id' => $data['user']->id,
            'assignment_id' => $data['assignment']->id,
        ], $this->get_execution_context());
    }

    public function test_it_returns_whether_child_competency_has_been_assigned() {
        $data = $this->create_data();

        // Logging in...
        $this->setUser($data['user']);

        // Let's create a competency with no assignment...
        $na_competency = $this->assignment_generator()->create_competency(['parentid' => $data['competencies'][0]->id], $data['competencies'][0]->frameworkid); // No value

        // Archived competency
        $archived_competency = $this->assignment_generator()->create_competency(['parentid' => $data['competencies'][0]->id], $data['competencies'][0]->frameworkid);
        $archived_assignment = $this->assignment_generator()
            ->create_assignment([
                'user_group_type' => user_groups::USER,
                'user_group_id' => $data['user']->id,
                'competency_id' => $archived_competency->id,
                'archived' => assignment::STATUS_ARCHIVED,
            ]);

        // Let's make some competencies self-assignable
        $comp = new competency($data['competencies'][2]->id);
        $comp->availability()->save((new assignment_availability())->set_attribute('availability', 1));
        $comp->availability()->save((new assignment_availability())->set_attribute('availability', 2));

        // Let's make some competencies self-assignable
        $comp = new competency($data['competencies'][3]->id);
        $comp->availability()->save((new assignment_availability())->set_attribute('availability', 2));

        // Let's make some competencies self-assignable
        $comp = new competency($na_competency->id);
        $comp->availability()->save((new assignment_availability())->set_attribute('availability', 1));

        // Fake log entry
        builder::table('totara_assignment_competencies_users_log')
            ->insert([
                'assignment_id' => $archived_assignment->id,
                'user_id' => $data['user']->id,
                'action' => competency_assignment_user_log::ACTION_UNASSIGNED_ARCHIVED,
                'created_at' => time(),
            ]);

        (new expand_task(builder::get_db()))->expand_all();

        // Righto, let's assert what we need.
        $result = achievements::resolve([
            'instance_id' => $data['criterion']->get_id(),
            'user_id' => $data['user']->id,
            'assignment_id' => $data['assignment']->id,
        ], $this->get_execution_context());

        /** @var collection $items */
        $items = $result['items'] ?? null;

        $this->assertEqualsCanonicalizing(
            [$data['competencies'][1]->id, $data['competencies'][2]->id, $data['competencies'][3]->id, $na_competency->id, $archived_competency->id],
            $items->pluck('id')
        );

        // Now let's assert that it returns correct achievement values

        // Competency 1
        $this->assertEquals($data['proficient_value']->id, $items->item($data['competencies'][1]->id)['value']->id);
        $this->assertFalse( $items->item($data['competencies'][1]->id)['self_assignable']);
        $this->assertTrue( $items->item($data['competencies'][1]->id)['assigned']);

        // Competency 2
        $this->assertNull($items->item($data['competencies'][2]->id)['value']);
        $this->assertTrue( $items->item($data['competencies'][2]->id)['self_assignable']);
        $this->assertTrue( $items->item($data['competencies'][2]->id)['assigned']);

        // Competency 3
        $this->assertNull($items->item($data['competencies'][3]->id)['value']);
        $this->assertFalse( $items->item($data['competencies'][3]->id)['self_assignable']);
        $this->assertTrue( $items->item($data['competencies'][3]->id)['assigned']);

        // Competency 4
        $this->assertNull($items->item($na_competency->id)['value']);
        $this->assertTrue( $items->item($na_competency->id)['self_assignable']);
        $this->assertFalse( $items->item($na_competency->id)['assigned']);

        // Competency 5
        $this->assertNull($items->item($archived_competency->id)['value']);
        $this->assertFalse( $items->item($archived_competency->id)['self_assignable']);
        $this->assertTrue( $items->item($archived_competency->id)['assigned']);


        // Let's assert whether these competencies have been assigned
    }


    public function test_it_works_with_no_competencies() {

        $data = $this->create_data();

        // Logging in...
        $this->setUser($data['user']);

        // Righto, let's assert what we need.
        $result = achievements::resolve([
            'instance_id' => $data['criterion']->get_id(),
            'user_id' => $data['user']->id,
            'assignment_id' => $data['assignments'][2]->id,
        ], $this->get_execution_context());

        /** @var collection $items */
        $items = $result['items'] ?? null;

        $this->assertEquals(criterion::AGGREGATE_ALL, $result['aggregation_method']);
        $this->assertEquals(1, $result['required_items']);
        $this->assertInstanceOf(collection::class, $items);

        $this->assertEmpty($items);
    }

    public function test_it_throws_you_not_found_exception_for_criterion() {
        $data = $this->create_data();

        // Logging in...
        $this->setUser($data['user']);

        $this->expectException(criterion_not_found_exception::class);

        achievements::resolve([
            'instance_id' => 0,
            'user_id' => $GLOBALS['USER']->id,
            'assignment_id' => $data['assignment']->id,
        ], $this->get_execution_context());
    }

    public function test_it_throws_you_not_found_exception_for_assignment() {
        $data = $this->create_data();

        // Logging in...
        $this->setUser($data['user']);

        $this->expectException(criterion_not_found_exception::class);

        achievements::resolve([
            'instance_id' => $data['criterion']->get_id(),
            'user_id' => $GLOBALS['USER']->id,
            'assignment_id' => -1,
        ], $this->get_execution_context());
    }

    public function test_it_requires_login() {
        $data = $this->create_data();

        // Logging in...

        $this->expectException(require_login_exception::class);

        achievements::resolve([
            'instance_id' => $data['criterion']->get_id(),
            'user_id' => $GLOBALS['USER']->id,
            'assignment_id' => $data['assignment']->id,
        ], $this->get_execution_context());
    }

    public function create_data() {
        $competencies = [];
        $assignments = [];

        // Create competency
        // Create another competency
        // Create child competency
        // Create another child competency

        $competencies[] = $this->assignment_generator()->create_competency();

        // These 3 we're looking for
        $competencies[] = $this->assignment_generator()->create_competency(['parentid' => $competencies[0]->id], $competencies[0]->frameworkid); // Achieved
        $competencies[] = $this->assignment_generator()->create_competency(['parentid' => $competencies[0]->id], $competencies[0]->frameworkid); // Not achieved
        $competencies[] = $this->assignment_generator()->create_competency(['parentid' => $competencies[0]->id], $competencies[0]->frameworkid); // No value

        $competencies[] = $this->assignment_generator()->create_competency(['parentid' => $competencies[1]->id], $competencies[0]->frameworkid);
        $competencies[] = $this->assignment_generator()->create_competency();

        // Create child competency criterion
        $criterion = $this->generator()->create_childcompetency([
            'aggregation' => [
                'method' => criterion::AGGREGATE_ALL,
            ],
            'competency' => $competencies[0]->id,
        ]);


        $user = $this->getDataGenerator()->create_user();

        $role = builder::table('role')
            ->where('shortname', 'student')
            ->one();

        $this->getDataGenerator()->role_assign($role->id, $user->id);

        // Assign capability
        assign_capability('totara/competency:view_own_profile', CAP_ALLOW, $role->id, context_system::instance());

        // This is the main user assignment for a parent competency
        $assignment = $this->assignment_generator()->create_assignment([
            'user_group_type' => user_groups::USER,
            'user_group_id' => $user->id,
            'competency_id' => $competencies[0]->id,
        ]);

        $assignments[] = $this->assignment_generator()->create_assignment([
            'user_group_type' => user_groups::USER,
            'user_group_id' => $user->id,
            'competency_id' => $competencies[1]->id,
        ]);

        $assignments[] = $this->assignment_generator()->create_assignment([
            'user_group_type' => user_groups::USER,
            'user_group_id' => $user->id,
            'competency_id' => $competencies[2]->id,
        ]);

        $assignments[] = $this->assignment_generator()->create_assignment([
            'user_group_type' => user_groups::USER,
            'user_group_id' => $user->id,
            'competency_id' => $competencies[3]->id,
        ]);

        $proficient_value = builder::table('comp_scale_values')
            ->join('comp_scale_assignments', 'scaleid', 'scaleid')
            ->where('comp_scale_assignments.frameworkid', $competencies[1]->frameworkid)
            ->where('proficient', 1)
            ->order_by('sortorder')
            ->first();

        $not_proficient_value = builder::table('comp_scale_values')
            ->join('comp_scale_assignments', 'scaleid', 'scaleid')
            ->where('comp_scale_assignments.frameworkid', $competencies[1]->frameworkid)
            ->where('proficient', 0)
            ->order_by('sortorder')
            ->first();

        // Let's create bogus achievement values
        $this->create_achievement_record($assignments[0], $user->id, $proficient_value);
        $this->create_achievement_record($assignments[1], $user->id, $not_proficient_value);

        return [
            'criterion' => $criterion,
            'competencies' => $competencies,
            'assignments' => $assignments,
            'assignment' => $assignment,
            'proficient_value' => $proficient_value,
            'not_proficient_value' => $not_proficient_value,
            'role' => $role,
            'user' => $user,
        ];
    }

    protected function create_achievement_record($assignment, $user_id, $scale_value, $attributes = []) {
        $attributes = array_merge([
            'assignment_id' => $assignment->id,
            'status' => 0,
            'proficient' => $scale_value->proficient,
            'user_id' => $user_id,
            'comp_id' => $assignment->competency_id,
            'scale_value_id' => $scale_value->id,
            'time_created' => time(),
            'time_status' => time(),
            'time_proficient' => time(),
            'time_scale_value' => time(),
            'last_aggregated' => time(),
        ], $attributes);

        return builder::get_db()->insert_record('totara_competency_achievement', (object) $attributes);
    }

    /**
     * Get criteria data generator
     *
     * @return totara_criteria_generator
     */
    protected function generator() {
        return $this->getDataGenerator()->get_plugin_generator('totara_criteria');
    }

    /**
     * Get criteria data generator
     *
     * @return tassign_competency_generator
     */
    protected function assignment_generator() {
        return $this->getDataGenerator()->get_plugin_generator('tassign_competency');
    }

    private function get_execution_context(string $type = 'dev', ?string $operation = null) {
        return execution_context::create($type, $operation);
    }
}
