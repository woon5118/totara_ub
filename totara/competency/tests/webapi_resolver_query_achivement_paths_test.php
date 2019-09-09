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
use tassign_competency\entities\assignment;
use totara_competency\entities\pathway;
use totara_competency\webapi\resolver\query\achievement_paths;

defined('MOODLE_INTERNAL') || die();


/**
 * Tests the query to fetch all available achievement paths (grouped by pathway types and in specific order)
 */
class totara_competency_webapi_resolver_query_achievement_paths_testcase extends advanced_testcase {

    private function get_execution_context(string $type = 'dev', ?string $operation = null) {
        return execution_context::create($type, $operation);
    }

    public function test_load_for_non_existing_assignment() {
        $this->setAdminUser();

        $generator = $this->getDataGenerator();

        $user1 = $generator->create_user();

        $args = ['assignment_id' => 999];

        $result = achievement_paths::resolve($args, $this->get_execution_context());
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function test_load_for_assignment_with_no_pathways_present() {
        $this->setAdminUser();

        $data = $this->create_data();

        $args = ['assignment_id' => $data->assignment1->id];

        $result = achievement_paths::resolve($args, $this->get_execution_context());
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function test_load_for_assignment_with_pathways_present() {
        $this->setAdminUser();

        $data = $this->create_data();

        $args = ['assignment_id' => $data->assignment1->id];

        $pathway0 = new pathway();
        $pathway0->comp_id = $data->assignment1->competency_id;
        $pathway0->sortorder = 0;
        $pathway0->path_type = 'learning_plan';
        $pathway0->path_instance_id = 0;
        $pathway0->status = \totara_competency\pathway::PATHWAY_STATUS_ACTIVE;
        $pathway0->save();

        $pathway1 = new pathway();
        $pathway1->comp_id = $data->assignment1->competency_id;
        $pathway1->sortorder = 1;
        $pathway1->path_type = 'manual';
        $pathway1->path_instance_id = 0;
        $pathway1->status = \totara_competency\pathway::PATHWAY_STATUS_ACTIVE;
        $pathway1->save();

        $pathway2 = new pathway();
        $pathway2->comp_id = $data->assignment1->competency_id;
        $pathway2->sortorder = 2;
        $pathway2->path_type = 'criteria_group';
        $pathway2->path_instance_id = 0;
        $pathway2->status = \totara_competency\pathway::PATHWAY_STATUS_ACTIVE;
        $pathway2->save();

        $pathway3 = new pathway();
        $pathway3->comp_id = $data->assignment1->competency_id;
        $pathway3->sortorder = 3;
        $pathway3->path_type = 'criteria_group';
        $pathway3->path_instance_id = 0;
        $pathway3->status = \totara_competency\pathway::PATHWAY_STATUS_ACTIVE;
        $pathway3->save();

        $pathway4 = new pathway();
        $pathway4->comp_id = $data->assignment1->competency_id;
        $pathway4->sortorder = 4;
        $pathway4->path_type = 'criteria_group';
        $pathway4->path_instance_id = 0;
        $pathway4->status = \totara_competency\pathway::PATHWAY_STATUS_ACTIVE;
        $pathway4->save();

        $pathway5 = new pathway();
        $pathway5->comp_id = $data->assignment1->competency_id;
        $pathway5->sortorder = 5;
        $pathway5->path_type = 'manual';
        $pathway5->path_instance_id = 0;
        $pathway5->status = \totara_competency\pathway::PATHWAY_STATUS_ACTIVE;
        $pathway5->save();

        $result = achievement_paths::resolve($args, $this->get_execution_context());
        $this->assertEquals(
            [
                [
                    'class' => 'MULTIVALUE',
                    'type' => 'manual',
                ],
                [
                    'class' => 'SINGLEVALUE',
                    'type' => null,
                ],
                [
                    'class' => 'MULTIVALUE',
                    'type' => 'learning_plan',
                ],
            ],
            $result
        );
    }

    public function test_load_for_assignment_with_archived_pathway_present() {
        $this->setAdminUser();

        $data = $this->create_data();

        $args = ['assignment_id' => $data->assignment1->id];

        $pathway1 = new pathway();
        $pathway1->comp_id = $data->assignment1->competency_id;
        $pathway1->sortorder = 1;
        $pathway1->path_type = 'manual';
        $pathway1->path_instance_id = 0;
        $pathway1->status = \totara_competency\pathway::PATHWAY_STATUS_ARCHIVED;
        $pathway1->save();

        $pathway2 = new pathway();
        $pathway2->comp_id = $data->assignment1->competency_id;
        $pathway2->sortorder = 2;
        $pathway2->path_type = 'criteria_group';
        $pathway2->path_instance_id = 0;
        $pathway2->status = \totara_competency\pathway::PATHWAY_STATUS_ACTIVE;
        $pathway2->save();

        $result = achievement_paths::resolve($args, $this->get_execution_context());
        $this->assertEquals(
            [
                [
                    'class' => 'SINGLEVALUE',
                    'type' => null,
                ]
            ],
            $result
        );
    }

    public function test_load_for_assignment_with_non_standard_pathway_present() {
        global $CFG;
        require_once $CFG->dirroot.'/totara/competency/tests/fixtures/fake_multivalue_type.php';
        require_once $CFG->dirroot.'/totara/competency/tests/fixtures/fake_singlevalue_type.php';

        $this->setAdminUser();

        $data = $this->create_data();

        $args = ['assignment_id' => $data->assignment1->id];

        $pathway1 = new pathway();
        $pathway1->comp_id = $data->assignment1->competency_id;
        $pathway1->sortorder = 1;
        $pathway1->path_type = 'fake_multivalue_type';
        $pathway1->path_instance_id = 0;
        $pathway1->status = \totara_competency\pathway::PATHWAY_STATUS_ACTIVE;
        $pathway1->save();

        $pathway2 = new pathway();
        $pathway2->comp_id = $data->assignment1->competency_id;
        $pathway2->sortorder = 2;
        $pathway2->path_type = 'criteria_group';
        $pathway2->path_instance_id = 0;
        $pathway2->status = \totara_competency\pathway::PATHWAY_STATUS_ACTIVE;
        $pathway2->save();

        $pathway3 = new pathway();
        $pathway3->comp_id = $data->assignment1->competency_id;
        $pathway3->sortorder = 3;
        $pathway3->path_type = 'learning_plan';
        $pathway3->path_instance_id = 0;
        $pathway3->status = \totara_competency\pathway::PATHWAY_STATUS_ACTIVE;
        $pathway3->save();

        $pathway4 = new pathway();
        $pathway4->comp_id = $data->assignment1->competency_id;
        $pathway4->sortorder = 4;
        $pathway4->path_type = 'fake_singlevalue_type';
        $pathway4->path_instance_id = 0;
        $pathway4->status = \totara_competency\pathway::PATHWAY_STATUS_ACTIVE;
        $pathway4->save();


        $result = achievement_paths::resolve($args, $this->get_execution_context());
        $this->assertEquals(
            [
                [
                    'class' => 'SINGLEVALUE',
                    'type' => null,
                ],
                [
                    'class' => 'MULTIVALUE',
                    'type' => 'learning_plan',
                ],
                [
                    'class' => 'MULTIVALUE',
                    'type' => 'fake_multivalue_type',
                ]
            ],
            $result
        );
    }

    protected function create_data() {
        $assign_generator = $this->generator();

        $data = new class() {
            public $fw1;
            public $comp1;
            public $assignment1;
        };
        $data->fw1 = $assign_generator->hierarchy_generator()->create_comp_frame([]);

        $data->comp1 = $assign_generator->create_competency([
            'shortname' => 'c-chef',
            'fullname' => 'Chef proficiency',
            'description' => 'Bossing around',
            'idnumber' => 'cook-chef-c',
        ], $data->fw1->id);

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
     * @return tassign_competency_generator|component_generator_base
     */
    protected function generator() {
        return $this->getDataGenerator()->get_plugin_generator('tassign_competency');
    }

}