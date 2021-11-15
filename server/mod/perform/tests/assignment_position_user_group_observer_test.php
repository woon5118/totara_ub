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
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Johannes Cilliers <johannes.cilliers@totaralearning.com>
 * @package mod_perform
 */

use mod_perform\entity\activity\track_assignment;
use mod_perform\expand_task;
use mod_perform\models\activity\track_assignment_type;
use mod_perform\user_groups\grouping;
use totara_job\job_assignment;

/**
 * Tests covering the user group observer making sure the events do the right thing
 *
 * @group perform
 */
class mod_perform_assignment_position_user_group_observer_testcase extends advanced_testcase {

    public function test_adding_job_assignment_position_sets_expand_flag() {
        $data = $this->prepare_assignments();

        // All expand flags are reset
        $this->assert_assignment_not_marked_for_expansion($data->assignment1->id);
        $this->assert_assignment_not_marked_for_expansion($data->assignment2->id);

        job_assignment::create([
            'userid' => $data->user1->id,
            'idnumber' => 'ja01',
            'positionid' => $data->position1->id
        ]);

        $this->assert_assignment_marked_for_expansion($data->assignment1->id);
        $this->assert_assignment_not_marked_for_expansion($data->assignment2->id);

        job_assignment::create([
            'userid' => $data->user1->id,
            'idnumber' => 'ja02',
            'positionid' => $data->position2->id
        ]);

        $this->assert_assignment_marked_for_expansion($data->assignment1->id);
        $this->assert_assignment_marked_for_expansion($data->assignment2->id);
    }

    public function test_removing_job_assignment_position_sets_expand_flag() {
        $data = $this->prepare_assignments();

        $job_assignment = job_assignment::create([
            'userid' => $data->user1->id,
            'idnumber' => 'ja01',
            'positionid' => $data->position1->id
        ]);

        expand_task::create()->expand_all();

        // All expand flags are reset
        $this->assert_assignment_not_marked_for_expansion($data->assignment1->id);
        $this->assert_assignment_not_marked_for_expansion($data->assignment2->id);

        $job_assignment->update([
            'positionid' => null
        ]);

        $this->assert_assignment_marked_for_expansion($data->assignment1->id);
        $this->assert_assignment_not_marked_for_expansion($data->assignment2->id);
    }

    protected function assert_assignment_not_marked_for_expansion(int $assignment_id) {
        $assignment_exists = track_assignment::repository()
            ->where('expand', false)
            ->where('id', $assignment_id)
            ->exists();

        $this->assertTrue($assignment_exists);
    }

    protected function assert_assignment_marked_for_expansion(int $assignment_id) {
        $assignment_exists = track_assignment::repository()
            ->where('expand', true)
            ->where('id', $assignment_id)
            ->exists();

        $this->assertTrue($assignment_exists);
    }

    private function prepare_assignments() {
        /** @var totara_hierarchy_generator $hierarchy_generator */
        $hierarchy_generator = $this->generator()->get_plugin_generator('totara_hierarchy');

        $test_data = new class() {
            public $user1;
            public $user2;
            public $position1;
            public $position2;
            public $activity1;
            public $track1;
            public $assignment1;
            public $assignment2;
        };

        $test_data->user1 = $this->generator()->create_user();
        $test_data->user2 = $this->generator()->create_user();
        $framework = $hierarchy_generator->create_pos_frame([]);
        $test_data->position1 = $hierarchy_generator->create_pos([
            'frameworkid' => $framework->id
        ]);
        $test_data->position2 = $hierarchy_generator->create_pos([
            'frameworkid' => $framework->id
        ]);

        /** @var mod_perform_generator $perform_generator */
        $perform_generator = $this->getDataGenerator()->get_plugin_generator('mod_perform');

        $this->setAdminUser();

        $test_data->activity1 = $perform_generator->create_activity_in_container();

        $test_data->track1 = $perform_generator
            ->create_activity_tracks($test_data->activity1)
            ->first();

        $test_data->assignment1 = new track_assignment([
            'track_id' => $test_data->track1->id,
            'type' => track_assignment_type::ADMIN,
            'user_group_type' => grouping::POS,
            'user_group_id' => $test_data->position1->id,
            'created_by' => 0,
            'expand' => false
        ]);
        $test_data->assignment1->save();

        $test_data->assignment2 = new track_assignment([
            'track_id' => $test_data->track1->id,
            'type' => track_assignment_type::ADMIN,
            'user_group_type' => grouping::POS,
            'user_group_id' => $test_data->position2->id,
            'created_by' => 0,
            'expand' => false
        ]);
        $test_data->assignment2->save();

        return $test_data;
    }

    /**
     * Date generator shortcut
     *
     * @return testing_data_generator
     */
    protected function generator() {
        return self::getDataGenerator();
    }

    private function get_expandable_track_assignments() {
        return track_assignment::repository()
            ->where('expand', true)
            ->get();
    }

}
