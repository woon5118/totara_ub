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
 * @package totara_competency
 * @subpackage test
 */

use core\orm\query\builder;
use pathway_manual\manual;
use totara_competency\entities\achievement_via;
use totara_competency\entities\competency_achievement;
use totara_competency\entities\competency_assignment_user;
use totara_competency\entities\competency_assignment_user_log;
use totara_competency\entities\scale_value;
use totara_competency\expand_task;
use totara_competency\settings;

class totara_competency_user_unassigned_testcase extends advanced_testcase {

    /**
     * @test_it_unassigns_users_correctly_with_records_deletion
     */
    public function test_it_unassigns_users_correctly_with_records_deletion() {
        $data = $this->create_data();

        // Set tracking setting
        settings::unassign_delete_records();

        $user = $data['users'][0];
        $pos = $data['positions'][0];
        $ass = $data['assignments'][0];

        // We need to remove user from a position and check that the event has been fired
        // To remove user from a position we need to remove the related job assignment record.
        builder::table('job_assignment')
            ->where('userid', $user->id)
            ->where('positionid', $pos->id)
            ->delete();

        $achievement = competency_achievement::repository()
            ->where('user_id', $user->id)
            ->where('assignment_id', $ass->id)
            ->one(true);

        // And that the data is gone...
        (new expand_task(builder::get_db()))->expand_single($ass->id);

        // Achievement
        $this->assertEquals(
            0,
            competency_achievement::repository()
                ->where('user_id', $user->id)
                ->where('assignment_id', $ass->id)
                ->count()
        );

        // Let's check that cascading kicked in and we don't have records keyed with the given achievement_id

        $this->assertEquals(
            0,
            achievement_via::repository()
            ->where('comp_achievement_id', $achievement->id)
            ->count()
        );

        // Log and user
        $this->assertEquals(
            0,
            competency_assignment_user::repository()
            ->where('assignment_id', $ass->id)
            ->where('user_id', $user->id)
            ->count()
        );

        // Log and user
        $this->assertEquals(
            0,
            competency_assignment_user_log::repository()
            ->where('assignment_id', $ass->id)
            ->where('user_id', $user->id)
            ->count()
        );
    }

    /**
     * @test_it_unassigns_users_correctly_with_records_keeping
     */
    public function test_it_unassigns_users_correctly_with_records_keeping() {
        $data = $this->create_data();

        // Set tracking setting
        settings::unassign_keep_records();

        $user = $data['users'][0];
        $pos = $data['positions'][0];
        $ass = $data['assignments'][0];

        $log_count = competency_assignment_user_log::repository()
            ->where('assignment_id', $ass->id)
            ->where('user_id', $user->id)
            ->count();

        $this->assertGreaterThan(0, $log_count);

        // We need to remove user from a position and check that the event has been fired
        // To remove user from a position we need to remove the related job assignment record.
        builder::table('job_assignment')
            ->where('userid', $user->id)
            ->where('positionid', $pos->id)
            ->delete();

        $achievement = competency_achievement::repository()
            ->where('user_id', $user->id)
            ->where('assignment_id', $ass->id)
            ->one(true);

        // And that the data is gone...
        (new expand_task(builder::get_db()))->expand_single($ass->id);

        // Achievement
        $this->assertEquals(
            1,
            competency_achievement::repository()
                ->where('user_id', $user->id)
                ->where('assignment_id', $ass->id)
                ->count()
        );

        // Let's check that cascading kicked in and we don't have records keyed with the given achievement_id
        $this->assertEquals(
            1,
            achievement_via::repository()
                ->where('comp_achievement_id', $achievement->id)
                ->count()
        );

        // Expand task worked correctly and the user has actually been unassigned
        $this->assertEquals(
            0,
            competency_assignment_user::repository()
                ->where('assignment_id', $ass->id)
                ->where('user_id', $user->id)
                ->count()
        );

        // We'd have more entries created by events
        $this->assertGreaterThan(
            $log_count,
            competency_assignment_user_log::repository()
                ->where('assignment_id', $ass->id)
                ->where('user_id', $user->id)
                ->count()
        );
    }

    /**
     * @test_it_unassigns_users_correctly_with_achieved_records_keeping
     */
    public function test_it_unassigns_users_correctly_with_achieved_records_keeping() {
        $data = $this->create_data();

        // Set tracking setting
        settings::unassign_delete_empty_records();

        // TODO we should create the learning plan empty thing here to check that it behaves correctly
        // For this case it should be the same as keep

        $user = $data['users'][0];
        $pos = $data['positions'][0];
        $ass = $data['assignments'][0];

        $log_count = competency_assignment_user_log::repository()
            ->where('assignment_id', $ass->id)
            ->where('user_id', $user->id)
            ->count();

        $this->assertGreaterThan(0, $log_count);

        // We need to remove user from a position and check that the event has been fired
        // To remove user from a position we need to remove the related job assignment record.
        builder::table('job_assignment')
            ->where('userid', $user->id)
            ->where('positionid', $pos->id)
            ->delete();

        $achievement = competency_achievement::repository()
            ->where('user_id', $user->id)
            ->where('assignment_id', $ass->id)
            ->one(true);

        // And that the data is gone...
        (new expand_task(builder::get_db()))->expand_single($ass->id);

        // Achievement
        $this->assertEquals(
            1,
            competency_achievement::repository()
                ->where('user_id', $user->id)
                ->where('assignment_id', $ass->id)
                ->count()
        );

        // Let's check that cascading kicked in and we don't have records keyed with the given achievement_id
        $this->assertEquals(
            1,
            achievement_via::repository()
                ->where('comp_achievement_id', $achievement->id)
                ->count()
        );

        // Expand task worked correctly and the user has actually been unassigned
        $this->assertEquals(
            0,
            competency_assignment_user::repository()
                ->where('assignment_id', $ass->id)
                ->where('user_id', $user->id)
                ->count()
        );

        // We'd have more entries created by events
        $this->assertGreaterThan(
            $log_count,
            competency_assignment_user_log::repository()
                ->where('assignment_id', $ass->id)
                ->where('user_id', $user->id)
                ->count()
        );
    }

    /**
     * Create some data to check that the things we want to remove are removed and things that shouldn't be touched
     * are not touched.
     *
     * @return array
     */
    protected function create_data() {
        $comp = $this->generator()->create_competency();
        $another_comp = $this->generator()->create_competency();

        $user = $this->getDataGenerator()->create_user();
        $another_user = $this->getDataGenerator()->create_user();

        $pos = $this->generator()->assignment_generator()
            ->create_position_and_add_members([$user, $another_user]);
        $another_pos = $this->generator()->assignment_generator()
            ->create_position_and_add_members([$user, $another_user]);

        $ass = $this->generator()
            ->assignment_generator()
            ->create_position_assignment($comp->id, $pos->id);

        $another_ass = $this->generator()
            ->assignment_generator()
            ->create_position_assignment($comp->id, $pos->id);

        (new expand_task(builder::get_db()))->expand_single($ass->id);

        $manual_pathway = $this->generator()->create_manual($comp);

        $value = scale_value::repository()
            ->where('name', 'Competent')
            ->one();

        // assign user to competency
        $this->generator()->create_manual_rating($manual_pathway, $user, $user, manual::ROLE_SELF, $value);
        (new totara_competency\task\competency_achievement_aggregation())->execute();

        return [
            'users' => [
                $user,
                $another_user,
            ],
            'competencies' => [
                $comp,
                $another_comp,
            ],
            'positions' => [
                $pos,
                $another_pos
            ],
            'organisations' => [

            ],
            'assignments' => [
                $ass,
                $another_ass
            ]
        ];
    }

    /**
     * Totara competency generator shortcut
     *
     * @return totara_competency_generator
     */
    protected function generator() {
        return $this->getDataGenerator()->get_plugin_generator('totara_competency');
    }
}
