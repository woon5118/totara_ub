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
 * @package totara_competency
 * @subpackage test
 */

use core\webapi\execution_context;
use pathway_criteria_group\criteria_group;
use pathway_criteria_group\entities\criteria_group as criteria_group_entity;
use totara_competency\entities\assignment;
use totara_competency\entities\pathway;
use totara_competency\entities\scale_value;
use totara_competency\plugin_types;
use totara_competency\webapi\resolver\query\scale_achievements;

defined('MOODLE_INTERNAL') || die();


/**
 * Tests the query to fetch all scales and their items for single value pathways
 */
class totara_competency_webapi_resolver_query_scale_achievements_testcase extends advanced_testcase {

    private function get_execution_context(string $type = 'dev', ?string $operation = null) {
        return execution_context::create($type, $operation);
    }

    public function test_load_for_non_existing_assignment() {
        $this->setAdminUser();

        $generator = $this->getDataGenerator();

        $user1 = $generator->create_user();

        $args = ['assignment_id' => 999];

        $result = scale_achievements::resolve($args, $this->get_execution_context());
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function test_load_for_assignment_with_no_pathways_present() {
        $this->setAdminUser();

        $data = $this->create_data();

        $args = ['assignment_id' => $data->assignment1->id];

        $result = scale_achievements::resolve($args, $this->get_execution_context());
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function test_load_for_assignment_with_pathways_present() {
        $this->activate_additional_pathways();

        $this->setAdminUser();

        $data = $this->create_data();

        $args = ['assignment_id' => $data->assignment1->id];

        $sortorder = 1;

        $pathway1 = new pathway();
        $pathway1->comp_id = $data->assignment1->competency_id;
        $pathway1->sortorder = $sortorder;
        $pathway1->path_type = 'fake_multivalue_type';
        $pathway1->path_instance_id = 0;
        $pathway1->status = \totara_competency\pathway::PATHWAY_STATUS_ACTIVE;
        $pathway1->save();

        $sortorder++;

        $scale_values = scale_value::repository()
            ->where('scaleid', $data->scale->id)
            ->order_by('sortorder', 'asc')
            ->get();

        $expected_ids = [];

        // Each scale value should have one criteria_group more
        // than the previous one, starting with one.
        $pathway_amount = 1;
        foreach ($scale_values as $scale_value) {
            $ids = [];
            for ($i = 1; $i <= $pathway_amount; $i ++) {
                $criteria_group = new criteria_group_entity();
                $criteria_group->scale_value_id = $scale_value->id;
                $criteria_group->save();

                $pathway = new pathway();
                $pathway->comp_id = $data->assignment1->competency_id;
                $pathway->sortorder = $sortorder;
                $pathway->path_type = 'criteria_group';
                $pathway->path_instance_id = $criteria_group->id;
                $pathway->status = \totara_competency\pathway::PATHWAY_STATUS_ACTIVE;
                $pathway->save();

                $ids[] = $criteria_group->id;
            }
            $expected_ids[] = $ids;
            $pathway_amount++;
            $sortorder++;
        }

        $pathway3 = new pathway();
        $pathway3->comp_id = $data->assignment1->competency_id;
        $pathway3->sortorder = $sortorder;
        $pathway3->path_type = 'learning_plan';
        $pathway3->path_instance_id = 0;
        $pathway3->status = \totara_competency\pathway::PATHWAY_STATUS_ACTIVE;
        $pathway3->save();

        $result = scale_achievements::resolve($args, $this->get_execution_context());
        $this->assertCount(3, $result);
        $expected_item_amount = 1;
        $i = 0;
        foreach ($result as $value) {
            $this->assertInstanceOf(scale_value::class, $value['scale_value']);
            $this->assertCount($expected_item_amount, $value['items']);
            $actual_ids = [];
            /** @var \totara_competency\pathway $item */
            foreach ($value['items'] as $item) {
                $this->assertInstanceOf(criteria_group::class, $item);
                $actual_ids[] = $item->get_path_instance_id();
            }
            $this->assertEqualsCanonicalizing($expected_ids[$i], $actual_ids);
            $expected_item_amount++;
            $i++;
        }
    }

    protected function create_data() {
        $assign_generator = $this->generator()->assignment_generator();

        $data = new class() {
            public $scale;
            public $fw1;
            public $comp1;
            public $assignment1;
        };

        /** @var totara_hierarchy_generator $hierarchy_generator */
        $hierarchy_generator =  $this->getDataGenerator()->get_plugin_generator('totara_hierarchy');

        $data->scale = $hierarchy_generator->create_scale('comp');

        $data->fw1 = $hierarchy_generator->create_comp_frame(['scale' => $data->scale->id]);

        $data->comp1 = $this->generator()->create_competency(null, $data->fw1->id, [
            'shortname' => 'c-chef',
            'fullname' => 'Chef proficiency',
            'description' => 'Bossing around',
            'idnumber' => 'cook-chef-c',
        ]);

        $data->assignment1 = $assign_generator->create_user_assignment(
            $data->comp1->id,
            null,
            ['status' => assignment::STATUS_ACTIVE, 'type' => assignment::TYPE_ADMIN]
        );

        return $data;
    }

    /**
     * Get assignment specific generator
     *
     * @return totara_competency_generator|component_generator_base
     */
    protected function generator() {
        return $this->getDataGenerator()->get_plugin_generator('totara_competency');
    }

    private function activate_additional_pathways() {
        global $CFG;
        require_once $CFG->dirroot.'/totara/competency/tests/fixtures/fake_multivalue_type.php';
        require_once $CFG->dirroot.'/totara/competency/tests/fixtures/fake_singlevalue_type.php';

        plugin_types::enable_plugin('fake_multivalue_type', 'pathway', 'totara_competency');
        plugin_types::enable_plugin('fake_singlevalue_type', 'pathway', 'totara_competency');
    }

}