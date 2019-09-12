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
 * @author Riana Rossouw <riana.rossouw@totaralearning.com>
 * @package totara_competency
 */

use pathway_criteria_group\criteria_group;
use totara_competency\aggregation_users_table;
use totara_competency\entities\pathway_achievement;
use totara_competency\entities\competency;
use totara_competency\pathway_evaluator_factory;

class totara_competency_pathway_evaluator_user_source_list_testcase extends advanced_testcase {

    private function setup_data() {
        global $DB;

        $data = new class() {
            /** @var competency $competency*/
            public $competency;
            public $criteria;
            /** @var aggregation_users_table $user_id_table */
            public $user_id_table;
        };

        $hierarchygenerator = $this->getDataGenerator()->get_plugin_generator('totara_hierarchy');

        $framework = $hierarchygenerator->create_comp_frame([]);
        $comp = $hierarchygenerator->create_comp(['frameworkid' => $framework->id]);
        $data->competency = new competency($comp->id);

        $criteria_generator = $this->getDataGenerator()->get_plugin_generator('totara_criteria');

        $data->criteria = [];
        for ($i = 1; $i <= 5; $i++) {
            $data->criteria[$i] = $criteria_generator->create_test_criterion('coursecompletion');
        }

        $data->user_id_table = new aggregation_users_table('totara_competency_temp_users',
            'user_id',
            'has_changed',
            'process_key',
            'update_operation_name'
        );

        return $data;
    }

    /**
     * Data provider for test_archive_non_assigned_achievements
     */
    public function data_provider_test_archive_non_assigned_achievements() {
        return [
            [
                'criteria_keys' => [1],
                'achievements' => [
                    1 => pathway_achievement::STATUS_CURRENT,
                    2 => pathway_achievement::STATUS_CURRENT,
                    3 => pathway_achievement::STATUS_CURRENT,
                ],
                'assigned_users' => [2],
                'expected' => [
                    1 => pathway_achievement::STATUS_ARCHIVED,
                    2 => pathway_achievement::STATUS_CURRENT,
                    3 => pathway_achievement::STATUS_ARCHIVED,
                ],
            ],

            [
                'criteria_keys' => [1, 2, 3],
                'achievements' => [
                    1 => pathway_achievement::STATUS_CURRENT,
                    2 => pathway_achievement::STATUS_CURRENT,
                    3 => pathway_achievement::STATUS_CURRENT,
                ],
                'assigned_users' => [1, 3],
                'expected' => [
                    1 => pathway_achievement::STATUS_CURRENT,
                    2 => pathway_achievement::STATUS_ARCHIVED,
                    3 => pathway_achievement::STATUS_CURRENT,
                ],
            ],

            [
                'criteria_keys' => [1, 2, 3],
                'achievements' => [
                    1 => pathway_achievement::STATUS_ARCHIVED,
                    2 => pathway_achievement::STATUS_ARCHIVED,
                    3 => pathway_achievement::STATUS_CURRENT,
                ],
                'assigned_users' => [1, 3],
                'expected' => [
                    1 => pathway_achievement::STATUS_ARCHIVED,
                    2 => pathway_achievement::STATUS_ARCHIVED,
                    3 => pathway_achievement::STATUS_CURRENT,
                ],
            ],

            [
                'criteria_keys' => [1, 2, 3],
                'achievements' => [
                    1 => pathway_achievement::STATUS_CURRENT,
                    2 => pathway_achievement::STATUS_CURRENT,
                    3 => pathway_achievement::STATUS_CURRENT,
                ],
                'assigned_users' => [1, 2, 3],
                'expected' => [
                    1 => pathway_achievement::STATUS_CURRENT,
                    2 => pathway_achievement::STATUS_CURRENT,
                    3 => pathway_achievement::STATUS_CURRENT,
                ],
            ],
        ];
    }

    /**
     * Test archive_non_assigned_achievements_from_list
     *
     * @dataProvider data_provider_test_archive_non_assigned_achievements
     */
    public function test_archive_non_assigned_achievements($criteria_keys, $achievements, $assigned_users, $expected) {
        $data = $this->setup_data();
        $competency_generator = $this->getDataGenerator()->get_plugin_generator('totara_competency');

        $params = [
            'comp_id' => $data->competency->id,
            'sortorder' => 1,
            'criteria' => [],
        ];

        foreach ($criteria_keys as $key) {
            $params['criteria'][] = $data->criteria[$key];
        }

        /** @var criteria_group $cg */
        $cg = $competency_generator->create_criteria_group($params);

        $this->create_achievement_records($cg->get_id(), $achievements);
        $user_source = new \totara_competency\pathway_evaluator_user_source_list($assigned_users, true);
        $user_source->archive_non_assigned_achievements($cg, time());
        $this->validate_achievement_records($expected);
    }

    /**
     * Helper function to create a test pathway_achievement record
     *
     * @param int $pathway_id
     * @param array $achievements
     */
    private function create_achievement_records(int $pathway_id, array $achievements) {
        global $DB;

        foreach ($achievements as $user_id => $status) {
            $instance = new pathway_achievement();
            $instance->pathway_id = $pathway_id;
            $instance->user_id = $user_id;
            $instance->status = $status ?? pathway_achievement::STATUS_CURRENT;
            $instance->date_achieved = strtotime("-1 week");
            $instance->save();
        }
    }

    /**
     * Helper function to validate totara_competency_pathway_achievement contains the expected rows
     *
     * @param $expected
     */
    private function validate_achievement_records($expected) {
        global $DB;

        $actual_rows = $DB->get_records('totara_competency_pathway_achievement');
        foreach ($actual_rows as $actual_row) {
            $this->assertTrue(isset($expected[$actual_row->user_id]));

            $this->assertEquals($expected[$actual_row->user_id], $actual_row->status);

            // Unset expected to verify we only get expected rows
            unset($expected[$actual_row->user_id]);
        }

        $this->assertEquals(0, count($expected));
    }

}
