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
 * @author Brendan Cox <brendan.cox@totaralearning.com>
 * @author Riana Rossouw <riana.rossouw@totaralearning.com>
 * @package totara_criteria
 */

use totara_competency\entities\competency_achievement;
use totara_criteria\competency_item_evaluator;

class totara_criteria_competency_item_evaluator_testcase extends advanced_testcase {

    /**
     * Create a totara_criteria_item row
     * @param  int $comp_id
     * @return int Id of inserted row
     */
    private function create_item(int $comp_id): int {
        global $DB;

        $item = new stdClass();
        $item->criterion_id = 1;
        $item->item_type = 'competency';
        $item->item_id = $comp_id;
        return $DB->insert_record('totara_criteria_item', $item);
    }

    /**
     * Create a totara_criteria_item_record row
     * @param  int $item_id
     * @param  int $user_id
     * @param  int $is_met
     * @return int Id of inserted row
     */
    private function create_item_record(int $item_id, int $user_id, int $is_met = 0): int {
        global $DB;

        $record = new stdClass();
        $record->criterion_item_id = $item_id;
        $record->user_id = $user_id;
        $record->criterion_met = $is_met;
        $record->timeevaluated = time();
        return $DB->insert_record('totara_criteria_item_record', $record);
    }

    /**
     * Create a totara_competency_achieved row
     * @param  int $comp_id
     * @param  int $user_id
     * @param  int $proficient
     * @return int Id of inserted row
     */
    private function create_achievement(int $comp_id, int $user_id, ?int $assignment_id = null, int $proficient = 0, ?int $status = null): int {
        global $DB;

        $record = new stdClass();
        $record->comp_id = $comp_id;
        $record->user_id = $user_id;
        $record->assignment_id = $assignment_id ?? 1;
        $record->proficient = $proficient;
        $record->status = $status ?? competency_achievement::ACTIVE_ASSIGNMENT;
        $record->time_created = time();
        $record->time_status = time();
        $record->last_aggregated = time();

        return $DB->insert_record('totara_competency_achievement', $record);
    }


    /**
     * Test with no row in totara_criterion_item row or competency_achievement
     */
    public function test_update_item_records_no_achievements() {
        global $DB;

        competency_item_evaluator::update_item_records();
        $this->assertSame(0, $DB->count_records('totara_criteria_item_record'));
    }

    /**
     * Data provider for test_update_item_records_competency_item_record_no_achievement
     */
    public function item_record_no_achievement_data_provider() {
        return [
            ['comp_id' => 10, 'user_id' => 100, 'is_met' => 0],
            ['comp_id' => 10, 'user_id' => 101, 'is_met' => 1]
        ];
    }

    /**
     * Test update_item_records with totara_criteria_item_record row, but no totara_competency_achievment row
     * @dataProvider item_record_no_achievement_data_provider
     */
    public function test_update_item_records_competency_item_record_no_achievement($comp_id, $user_id, $is_met) {
        global $DB;

        // No need for an actual competency in this test
        $item_id = $this->create_item($comp_id);
        $record_id = $this->create_item_record($item_id, $user_id, $is_met);

        competency_item_evaluator::update_item_records();

        $record = $DB->get_record('totara_criteria_item_record', ['criterion_item_id' => $item_id, 'user_id' => $user_id]);
        $this->assertEquals(0, $record->criterion_met);
    }

    /**
     * Data provider for test_update_item_records_achievement_no_item_record
     */
    public function achievement_no_item_record_data_provider() {
        return [
            ['comp_id' => 10, 'user_id' => 100, 'achievements' => [
                ['proficient' => 0, 'status' => competency_achievement::ACTIVE_ASSIGNMENT]], 0],
            ['comp_id' => 10, 'user_id' => 101, 'achievements' => [
                ['proficient' => 1, 'status' => competency_achievement::ACTIVE_ASSIGNMENT]], 1],
            ['comp_id' => 10, 'user_id' => 102, 'achievements' => [
                ['proficient' => 0, 'assignment' => 1, 'status' => competency_achievement::ACTIVE_ASSIGNMENT],
                ['proficient' => 1, 'assignment' => 1, 'status' => competency_achievement::SUPERSEDED],
            ], 0],
            ['comp_id' => 10, 'user_id' => 103, 'achievements' => [
                ['proficient' => 0, 'assignment' => 1, 'status' => competency_achievement::SUPERSEDED],
                ['proficient' => 1, 'assignment' => 1, 'status' => competency_achievement::ACTIVE_ASSIGNMENT],
            ], 1],
            ['comp_id' => 10, 'user_id' => 104, 'achievements' => [
                ['proficient' => 1, 'assignment' => 1, 'status' => competency_achievement::ARCHIVED_ASSIGNMENT]], false],
            ['comp_id' => 10, 'user_id' => 105, 'achievements' => [
                ['proficient' => 1, 'assignment' => 1, 'status' => competency_achievement::ACTIVE_ASSIGNMENT],
                ['proficient' => 0, 'assignment' => 2, 'status' => competency_achievement::ACTIVE_ASSIGNMENT],
            ], 1],
            ['comp_id' => 10, 'user_id' => 105, 'achievements' => [
                ['proficient' => 1, 'assignment' => 1, 'status' => competency_achievement::ARCHIVED_ASSIGNMENT],
                ['proficient' => 0, 'assignment' => 2, 'status' => competency_achievement::ACTIVE_ASSIGNMENT],
            ], 0],
        ];
    }

    /**
     * Test update_item_records with totara_competency_achievment row but no totara_criteria_item_record
     * @dataProvider achievement_no_item_record_data_provider
     */
    public function test_update_item_records_achievement_no_item_record($comp_id, $user_id, $achievements, $expected_is_met) {
        global $DB;

        // No need for an actual competency in this test
        $item_id = $this->create_item($comp_id);
        foreach ($achievements as $achievement) {
            $this->create_achievement($comp_id,
                $user_id,
                $achievement['assignment'] ?? null,
                $achievement['proficient'] ?? 0,
                $achievement['status'] ?? null);
        }

        competency_item_evaluator::update_item_records();

        $record = $DB->get_record('totara_criteria_item_record', ['criterion_item_id' => $item_id, 'user_id' => $user_id]);
        if ($expected_is_met === false) {
            $this->assertFalse($record);
        } else {
            $this->assertEquals($expected_is_met, $record->criterion_met);
        }
    }

    /**
     * Data provider for test_update_item_records_achievement_and_item_record
     */
    public function achievement_and_item_record_data_provider() {
        return [
            ['comp_id' => 10, 'user_id' => 100,
                'achievements' => [
                    ['proficient' => 0, 'status' => competency_achievement::ACTIVE_ASSIGNMENT]],
                'criterion_met' => 0,
                'expected_met' => 0
            ],
            ['comp_id' => 10, 'user_id' => 101,
                'achievements' => [
                    ['proficient' => 1, 'status' => competency_achievement::ACTIVE_ASSIGNMENT]],
                'criterion_met' => 0,
                'expected_met' => 1
            ],
            ['comp_id' => 10, 'user_id' => 102,
                'achievements' => [
                    ['proficient' => 0, 'status' => competency_achievement::ACTIVE_ASSIGNMENT]],
                'criterion_met' => 1,
                'expected_met' => 0
            ],
            ['comp_id' => 10, 'user_id' => 103,
                'achievements' => [
                    ['proficient' => 1, 'assignment' => 1, 'status' => competency_achievement::SUPERSEDED],
                    ['proficient' => 0, 'assignment' => 1, 'status' => competency_achievement::ACTIVE_ASSIGNMENT],
                ],
                'criterion_met' => 1,
                'expected_met' => 0
            ],
            ['comp_id' => 10, 'user_id' => 104,
                'achievements' => [
                    ['proficient' => 1, 'assignment' => 1, 'status' => competency_achievement::ACTIVE_ASSIGNMENT],
                    ['proficient' => 0, 'assignment' => 2, 'status' => competency_achievement::ACTIVE_ASSIGNMENT],
                ],
                'criterion_met' => 1,
                'expected_met' => 1
            ],
        ];
    }

    /**
     * Test update_item_records with totara_competency_achievment row as well as totara_criteria_item_record
     * @dataProvider achievement_and_item_record_data_provider
     */
    public function test_update_item_records_achievement_and_item_record($comp_id, $user_id, $achievements, $criterion_met, $expected_is_met) {
        global $DB;

        // No need for an actual competency in this test
        $item_id = $this->create_item($comp_id);
        $record_id = $this->create_item_record($item_id, $user_id, $criterion_met);
        foreach ($achievements as $achievement) {
            $this->create_achievement($comp_id,
                $user_id,
                $achievement['assignment'] ?? null,
                $achievement['proficient'] ?? 0,
                $achievement['status'] ?? null);
        }

        competency_item_evaluator::update_item_records();

        $record = $DB->get_record('totara_criteria_item_record', ['criterion_item_id' => $item_id, 'user_id' => $user_id]);
        if ($expected_is_met === false) {
            $this->assertFalse($record);
        } else {
            $this->assertEquals($expected_is_met, $record->criterion_met);
        }
    }
}