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
 * @author Aleksandr Baishev <aleksandr.baishev@totaralearning.com>
 * @author Fabian Derschatta <fabian.derschata@totaralearning.com>
 * @package totara_competency
 */

use core\collection;
use core\entities\user;
use totara_competency\entities\assignment;
use totara_competency\models\assignment as assignment_model;
use totara_competency\models\profile\competency_progress;
use totara_competency\models\profile\filter;
use totara_competency\models\profile\item;
use totara_competency\models\profile\progress;
use totara_competency\models\profile\unassigned_competency_progress;

global $CFG;

require_once($CFG->dirroot . '/totara/competency/tests/totara_competency_testcase.php');

/**
 * Class totara_competency_model_scale_testcase
 *
 * @coversDefaultClass \totara_competency\models\scale
 */
class totara_competency_model_profile_progress_testcase extends totara_competency_testcase {

    /**
     * @covers ::find_by_id
     * @covers ::find_by_ids
     * @covers ::__construct
     */
    public function test_it_loads_scales_using_ids(): void {
        $data = $this->create_sorting_testing_data(true);

        // Let's build data for a user
        /** @var user $user */
        $user = $data['users']->first()->add_extra_attribute('fullname');

        $progress = progress::for($user->id);

        $this->assertInstanceOf(progress::class, $progress);

        // Let's check that it has required objects

        // User
        $this->assertInstanceOf(stdClass::class, $progress->user);
        $this->assertEquals($user->to_array(), (array) $progress->user);

        // Individual progress items
        $this->assertInstanceOf(collection::class, $progress->items);

        $this->assertGreaterThan(0, count($progress->items));

        $progress->items->map(function (item $item) {
            // Well having type-hint will already assert that the item is of the correct type

            // TODO Let's quickly assert items for the correct structure and content
        });

        // Filters
        $this->assertIsArray($progress->filters);
        $this->assertGreaterThan(0, count($progress->filters));

        foreach ($progress->filters as $filter) {
            $this->assertInstanceOf(filter::class, $filter);
        }

        // Latest achievement
        $this->assertEquals($data['competencies']->first()->fullname, $progress->latest_achievement);
    }

    public function test_build_progress_items_from_assignments(): void {
        $fw = $this->generator()->create_framework();
        $comp1 = $this->generator()->create_competency(null, $fw);
        $comp2 = $this->generator()->create_competency(null, $fw);
        $comp3 = $this->generator()->create_competency(null, $fw);

        // Create users
        $user1 = $this->create_user();
        $user2 = $this->create_user();

        $ass_gen = $this->generator()->assignment_generator();

        $pos1 = $ass_gen->create_position_and_add_members([$user1->id, $user2->id]);
        $pos2 = $ass_gen->create_position_and_add_members([$user1->id, $user2->id]);

        $pos_ass1 = new assignment($this->generator()->assignment_generator()->create_position_assignment($comp1->id, $pos1->id));
        $pos_ass2 = new assignment($this->generator()->assignment_generator()->create_position_assignment($comp2->id, $pos1->id));
        $pos_ass3 = new assignment($this->generator()->assignment_generator()->create_position_assignment($comp1->id, $pos2->id));
        $pos_ass4 = new assignment($this->generator()->assignment_generator()->create_position_assignment($comp2->id, $pos2->id));

        $user_ass1 = new assignment($ass_gen->create_user_assignment($comp2->id, $user1->id));
        $user_ass2 = new assignment($ass_gen->create_user_assignment($comp1->id, $user1->id));

        $self_ass1 = new assignment($ass_gen->create_self_assignment($comp2->id, $user1->id));
        $self_ass2 = new assignment($ass_gen->create_self_assignment($comp1->id, $user1->id));
        $self_ass3 = new assignment($ass_gen->create_self_assignment($comp1->id, $user2->id));
        $self_ass4 = new assignment($ass_gen->create_self_assignment($comp1->id, $user2->id, ['status' => assignment::STATUS_ARCHIVED]));

        $assignments = new \core\orm\collection([
            $pos_ass1,
            $pos_ass2,
            $pos_ass3,
            $pos_ass4,
            $user_ass1,
            $user_ass2,
            $self_ass1,
            $self_ass2,
            $self_ass3,
            $self_ass4,
        ]);
        $collection = competency_progress::build_from_assignments($assignments);

        // Expecting 2 items as there are two competencies
        $this->assertCount(2, $collection);

        // Only those assignments were added which differ in user_group (type/id) and status
        $expected_assignemts[$comp1->id] = collection::new([
            $pos_ass1,
            $pos_ass3,
            $user_ass2,
            $self_ass3,
            $self_ass4 // status is different so it should still shows up
        ]);

        // Only those assignments were added which differ in user_group (type/id) and status
        $expected_assignemts[$comp2->id] = collection::new([
            $pos_ass2,
            $pos_ass4,
            $user_ass1,
        ]);

        /** @var competency_progress $item */
        foreach ($collection as $key => $item) {
            $this->assertEqualsCanonicalizing(
                $expected_assignemts[$key]->pluck('id'),
                $item->get_assignments()->map(function (assignment_model $assignment_model) {
                    return $assignment_model->get_entity();
                })->pluck('id')
            );
        }
    }

    /**
     * When building for a competency, we want a null object with the competency attached rather than literal null.
     */
    public function test_build_for_competency_no_assignments(): void {
        $competency_framework = $this->generator()->create_framework();
        $competency = $this->generator()->create_competency(null, $competency_framework);

        $user1 = $this->create_user();

        $progress = competency_progress::build_for_competency($user1, $competency->id);

        self::assertInstanceOf(unassigned_competency_progress::class, $progress);
        self::assertCount(0, $progress->assignments);
        self::assertEquals($competency->id, $progress->competency->id);
    }

}
