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
 * @package tassign_competency
 * @category test
 */

use tassign_competency\entities;
use totara_assignment\user_groups;

defined('MOODLE_INTERNAL') || die();

class tassign_competency_assignment_index_service_testcase extends advanced_testcase {

    use \totara_core\phpunit\webservice_utils;

    protected function setUp() {
        parent::setUp();
        $this->resetAfterTest();
        $this->setAdminUser();
    }

    public function test_load_all() {
        ['ass' => $ass] = $this->generate_data();

        $res = $this->call_webservice_api('tassign_competency_assignment_index', [
            'filters' => [],
            'page' => 0,
            'order' => '',
            'direction' => ''
        ]);

        $result = $res['data'] ?? null;

        $this->assertWebserviceSuccess($res);
        $this->assertCount(count($ass), $result['items']);
        $this->assertEquals(count($ass), $result['total']);

        $expected_ids = array_column($ass, 'id');
        sort($expected_ids);
        $actual_ids = array_column($result['items'], 'id');
        sort($actual_ids);
        $this->assertEquals($expected_ids, $actual_ids);
    }

    public function test_order_by_competency_name_asc() {
        $this->generate_data();

        $res = $this->call_webservice_api('tassign_competency_assignment_index', [
            'filters' => [],
            'page' => 0,
            'order' => 'competency_name',
            'direction' => 'asc'
        ]);

        $result = $res['data'] ?? null;

        $this->assertWebserviceSuccess($res);
        $this->assert_is_ordered_by('competency_name', 'asc', $result);
    }

    public function test_order_by_competency_name_desc() {
        $this->generate_data();

        $res = $this->call_webservice_api('tassign_competency_assignment_index', [
            'filters' => [],
            'page' => 0,
            'order' => 'competency_name',
            'direction' => 'desc'
        ]);

        $result = $res['data'] ?? null;

        $this->assertWebserviceSuccess($res);
        $this->assert_is_ordered_by('competency_name', 'desc', $result);
    }

    public function test_order_by_user_group_name_asc() {
        $this->generate_data();

        $res = $this->call_webservice_api('tassign_competency_assignment_index', [
            'filters' => [],
            'page' => 0,
            'order' => 'user_group_name',
            'direction' => 'asc'
        ]);

        $result = $res['data'] ?? null;

        $this->assertWebserviceSuccess($res);
        $this->assert_is_ordered_by('user_group_name', 'asc', $result);
    }

    public function test_order_by_user_group_name_desc() {
        $this->generate_data();

        $res = $this->call_webservice_api('tassign_competency_assignment_index', [
            'filters' => [],
            'page' => 0,
            'order' => 'user_group_name',
            'direction' => 'desc'
        ]);

        $result = $res['data'] ?? null;

        $this->assertWebserviceSuccess($res);
        $this->assert_is_ordered_by('user_group_name', 'desc', $result);
    }

    public function test_order_by_most_recently_updated() {
        ['ass' => $assignments] = $this->generate_data();

        $updated_at = 1;
        foreach ($assignments as $assignment) {
            $assignment = new entities\assignment($assignment->id);
            $assignment->updated_at = $updated_at;
            $assignment->do_not_update_timestamps()->save();
            $updated_at++;
        }

        $res = $this->call_webservice_api('tassign_competency_assignment_index', [
            'filters' => [],
            'page' => 0,
            'order' => 'most_recently_updated',
            'direction' => 'asc'
        ]);

        $result = $res['data'] ?? null;

        $this->assertWebserviceSuccess($res);
        $this->assert_is_ordered_by('updated_at', 'desc', $result);
    }

    private function assert_is_ordered_by($name, $dir, $result) {
        $columns = array_column($result['items'], $name);
        $previous = null;
        $failed = false;
        foreach ($columns as $column) {
            $column = (string) $column;
            if ($previous) {
                switch (strtolower($dir)) {
                    case 'asc':
                        $failed = strcmp($previous, $column) > 0;
                        break;
                    case 'desc':
                        $failed = strcmp($previous, $column) < 0;
                        break;
                }
                if ($failed) {
                    $this->fail("Result is not properly ordered by {$name}");
                }
            }
            $previous = $column;
        }
    }

    public function test_filter_by_assignment_types() {
        ['ass' => $ass] = $this->generate_data();

        // Only types
        $filters = ['assignmenttype' => [entities\assignment::TYPE_ADMIN, entities\assignment::TYPE_SELF]];
        $res = $this->call_webservice_api('tassign_competency_assignment_index', [
            'filters' => $filters,
            'page' => 0,
            'order' => '',
            'direction' => ''
        ]);

        $result = $res['data'] ?? null;

        $this->assertWebserviceSuccess($res);
        $this->assert_result_contains_ids([$ass[0]->id, $ass[1]->id], $result);

        // Mix of user_group_type and type
        $filters = ['assignmenttype' => [user_groups::POSITION, entities\assignment::TYPE_SELF]];
        $res = $this->call_webservice_api('tassign_competency_assignment_index', [
            'filters' => $filters,
            'page' => 0,
            'order' => '',
            'direction' => ''
        ]);

        $result = $res['data'] ?? null;

        $this->assertWebserviceSuccess($res);
        $this->assert_result_contains_ids([$ass[1]->id, $ass[3]->id], $result);

        // Just one system filter
        $filters = ['assignmenttype' => [entities\assignment::TYPE_SYSTEM]];
        $res = $this->call_webservice_api('tassign_competency_assignment_index', [
            'filters' => $filters,
            'page' => 0,
            'order' => '',
            'direction' => ''
        ]);

        $result = $res['data'] ?? null;

        $this->assertWebserviceSuccess($res);
        $this->assert_result_contains_ids([$ass[2]->id], $result);

        // Just user group types
        $filters = ['assignmenttype' => [user_groups::POSITION, user_groups::ORGANISATION]];
        $res = $this->call_webservice_api('tassign_competency_assignment_index', [
            'filters' => $filters,
            'page' => 0,
            'order' => '',
            'direction' => ''
        ]);

        $result = $res['data'] ?? null;

        $this->assertWebserviceSuccess($res);
        $this->assert_result_contains_ids([$ass[3]->id, $ass[4]->id], $result);

        // non existing types are ignored
        $filters = ['assignmenttype' => ['foo', 'bar']];
        $res = $this->call_webservice_api('tassign_competency_assignment_index', [
            'filters' => $filters,
            'page' => 0,
            'order' => '',
            'direction' => ''
        ]);

        $result = $res['data'] ?? null;

        $this->assertWebserviceSuccess($res);
        $this->assertEquals(count($ass), $result['total']);
    }

    protected function assert_result_contains_ids(array $expected_ids, array $actual_result) {
        $actual_item_ids = array_map(
            function ($item) {
                return $item['id'];
            },
            $actual_result['items']
        );
        sort($expected_ids);
        sort($actual_item_ids);
        $this->assertEquals($expected_ids, $actual_item_ids);
    }

    /**
     * Create a few competencies with knows names to test search
     */
    protected function generate_data() {
        $data = [
            'comps' => [],
            'fws' => [],
            'ass' => [],
            'types' => [],
            'pos' => [],
            'org' => []
        ];

        $hierarchy_generator = $this->generator()->hierarchy_generator();

        $fw = $hierarchy_generator->create_pos_frame(['fullname' => 'Framework 2']);
        $data['pos'][] = $pos1 = $hierarchy_generator->create_pos(['frameworkid' => $fw->id, 'fullname' => 'Position 1']);

        $fw = $hierarchy_generator->create_org_frame(['fullname' => 'Framework 3']);
        $data['org'][] = $org1 = $hierarchy_generator->create_org(['frameworkid' => $fw->id, 'fullname' => 'Organisation 1']);

        $data['fws'][] = $fw = $hierarchy_generator->create_comp_frame([]);
        $data['fws'][] = $fw2 = $hierarchy_generator->create_comp_frame([]);

        $data['types'][] = $type1 = $hierarchy_generator->create_comp_type(['idnumber' => 'type1']);
        $data['types'][] = $type2 = $hierarchy_generator->create_comp_type(['idnumber' => 'type2']);

        $data['comps'][] = $one = $this->generator()->create_competency([
            'shortname' => 'acc',
            'fullname' => 'Accounting',
            'description' => 'Counting profits',
            'idnumber' => 'accc',
            'typeid' => $type1,
        ], $fw->id);

        $data['comps'][] = $two = $this->generator()->create_competency([
            'shortname' => 'c-chef',
            'fullname' => 'Chef proficiency',
            'description' => 'Bossing around',
            'idnumber' => 'cook-chef-c',
            'typeid' => $type1,
        ], $fw2->id);

        $data['comps'][] = $three = $this->generator()->create_competency([
            'shortname' => 'des',
            'fullname' => 'Designing interiors',
            'description' => 'Decorating things',
            'idnumber' => 'des',
            'parentid' => $one->id,
            'typeid' => $type2,
        ], $fw->id);

        // Create an assignment for a competency
        $gen = $this->generator();
        $data['ass'][] = $gen->create_user_assignment($one->id, null, ['status' => entities\assignment::STATUS_ACTIVE, 'type' => entities\assignment::TYPE_ADMIN]);
        $data['ass'][] = $gen->create_user_assignment($two->id, null, ['status' => entities\assignment::STATUS_ACTIVE, 'type' => entities\assignment::TYPE_SELF]);
        $data['ass'][] = $gen->create_user_assignment($three->id, null, ['status' => entities\assignment::STATUS_ACTIVE, 'type' => entities\assignment::TYPE_SYSTEM]);
        $data['ass'][] = $gen->create_position_assignment($three->id, $pos1->id, ['status' => entities\assignment::STATUS_ACTIVE, 'type' => entities\assignment::TYPE_ADMIN]);
        $data['ass'][] = $gen->create_organisation_assignment($three->id, $org1->id, ['status' => entities\assignment::STATUS_ACTIVE, 'type' => entities\assignment::TYPE_ADMIN]);

        return $data;
    }

    /**
     * Get hierarchy specific generator
     *
     * @return tassign_competency_generator|component_generator_base
     */
    protected function generator() {
        return $this->getDataGenerator()->get_plugin_generator('tassign_competency');
    }
}