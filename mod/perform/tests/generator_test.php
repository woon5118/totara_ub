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

use mod_perform\models\activity\track_status;
use mod_perform\models\activity\track_assignment_type;

use mod_perform\user_groups\grouping;

/**
 * @coversDefaultClass mod_perform_generator.
 *
 * @group perform
 */
class mod_perform_generator_testcase extends advanced_testcase {
    /**
     * @covers ::create_activity_in_container
     * @covers ::create_activity_tracks
     * @covers ::create_track_assignments
     */
    public function test_create_track_assignments(): void {
        $this->setAdminUser();
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_perform');
        $activity = $generator->create_activity_in_container();
        $activity_id = $activity->get_id();

        $cohort_count = 2;
        $org_count = 3;
        $pos_count = 1;
        $user_count = 0;
        $assignment_count = $cohort_count + $org_count + $pos_count + $user_count;

        $tracks = $generator->create_activity_tracks($activity, 1);
        foreach ($tracks->all() as $track) {
            $track_id = $track->id;

            $this->assertGreaterThan(0, $track_id, 'transient track');
            $this->assertEquals($activity_id, $track->activity_id, 'wrong track activity');
            $this->assertEquals(track_status::ACTIVE, $track->status, 'wrong track status');
            $this->assertEmpty($track->assignments->all(), 'wrong track assignments');

            $actual_cohort_count = 0;
            $actual_org_count = 0;
            $actual_pos_count = 0;
            $actual_user_count = 0;
            $actual_assignment_count = 0;

            $assignments = $generator
                ->create_track_assignments(
                    $track,
                    $cohort_count,
                    $org_count,
                    $pos_count,
                    $user_count
                )
                ->assignments
                ->all();

            foreach ($assignments as $assignment) {
                $this->assertEquals($track_id, $assignment->track_id, 'transient assignment');
                $this->assertEquals(track_assignment_type::ADMIN, $assignment->type, 'wrong type');

                $group = $assignment->group;
                $this->assertGreaterThan(0, $group->get_id(), 'no group id');

                $group_type = $group->get_type();
                if ($group_type === grouping::COHORT) {
                    $actual_cohort_count++;
                } else if ($group_type === grouping::ORG) {
                    $actual_org_count++;
                } else if ($group_type === grouping::POS) {
                    $actual_pos_count++;
                } else if ($group_type === grouping::USER) {
                    $actual_user_count++;
                } else {
                    $this->fail("unknown user grouping type - $group_type");
                }

                $actual_assignment_count++;
            }

            $this->assertEquals($assignment_count, $actual_assignment_count, 'wrong total assignment count');
            $this->assertEquals($cohort_count, $actual_cohort_count, 'wrong cohort assignment count');
            $this->assertEquals($org_count, $actual_org_count, 'wrong org assignment count');
            $this->assertEquals($pos_count, $actual_pos_count, 'wrong pos assignment count');
            $this->assertEquals($user_count, $actual_user_count, 'wrong user assignment count');
        }
    }
}