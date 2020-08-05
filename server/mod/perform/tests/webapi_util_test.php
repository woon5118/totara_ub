<?php
/*
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
 * @author Jaron Steenson <jaron.steenson@totaralearning.com>
 * @package mod_perform
 */

use core\entities\user;
use core\orm\query\builder;
use mod_perform\models\activity\activity;
use mod_perform\util;
use totara_job\job_assignment;

/**
 * @coversDefaultClass \mod_perform\util.
 *
 * @group perform
 */
class mod_perform_webapi_util_testcase extends advanced_testcase {

    public function test_admin_can_manage_participation_of_all_activities(): void {
        self::setAdminUser();

        $names = ['Mid year performance', 'End year performance'];
        $this->create_activity_data($names);

        $activities = util::get_participant_manageable_activities(user::logged_in()->id);
        $activities = $activities->pluck('name');
        $names[] = 'hidden-activity'; // Admin can see "hidden" activities.

        $this->assertCount(count($names), $activities);
        $this->assertEqualsCanonicalizing($names, $activities);
    }

    public function test_manager_can_manage_participation_of_activities_about_his_employees(): void {
        $manager = self::getDataGenerator()->create_user();
        $employee = self::getDataGenerator()->create_user();

        $manager_job_assignment = job_assignment::create(
            [
                'userid' => $manager->id,
                'idnumber' => $manager->id,
            ]
        );

        job_assignment::create(
            [
                'userid' => $employee->id,
                'idnumber' => $employee->id,
                'managerjaid' => $manager_job_assignment->id,
            ]
        );

        $employee_context = context_user::instance($employee->id);
        assign_capability('mod/perform:manage_subject_user_participation', CAP_ALLOW, $manager->id, $employee_context);

        self::setAdminUser();

        /** @var mod_perform_generator $perform_generator */
        $perform_generator = self::getDataGenerator()->get_plugin_generator('mod_perform');
        $subject_instance = $perform_generator->create_subject_instance(['subject_user_id' => $employee->id]);
        $expected_activity = $subject_instance->activity();

        // Add some other activities too.
        $names = ['Mid year performance', 'End year performance'];
        $this->create_activity_data($names);

        self::setUser($manager);

        $activities =  util::get_participant_manageable_activities(user::logged_in()->id);

        /** @var activity $actual_activity */
        $actual_activity = $activities->first();

        $this->assertCount(1, $activities);
        $this->assertEquals($expected_activity->id, $actual_activity->id);
    }

    public function test_full_participation_manger_can_manage_all_activities(): void {
        $full_participation_manager = self::getDataGenerator()->create_user();

        // Add some other activities too.
        $names = ['Mid year performance', 'End year performance'];
        $this->create_activity_data($names);

        self::setUser($full_participation_manager);

        $activities = util::get_participant_manageable_activities(user::logged_in()->id);
        $this->assertCount(0, $activities);

        $manager_role = builder::get_db()->get_record('role', ['shortname' => 'manager'], '*', MUST_EXIST);
        $manager_context = context_user::instance($full_participation_manager->id);
        role_assign($manager_role->id, $full_participation_manager->id, $manager_context);

        $activities =  util::get_participant_manageable_activities(user::logged_in()->id);
        $activities = $activities->pluck('name');

        $this->assertCount(count($names), $activities);
        $this->assertEqualsCanonicalizing($names, $activities);
    }

    private function create_activity_data(array $activity_names): void {
        $user = user::logged_in();

        self::setAdminUser();

        /** @var mod_perform_generator $perform_generator */
        $perform_generator = self::getDataGenerator()->get_plugin_generator('mod_perform');

        $activity_names[] = 'hidden-activity';

        foreach ($activity_names as $name) {
            $perform_generator->create_activity_in_container(['activity_name' => $name]);
        }

        /** @var activity $hidden_activity */
        $hidden_activity = mod_perform\entities\activity\activity::repository()
            ->where('name', 'hidden-activity')
            ->order_by('id')
            ->first(true);

        // Set "hidden-activity" to be hidden, as all queries under test should
        // be applying filter_by_visible  filter.
        builder::table('course')
            ->where('id', $hidden_activity->course)
            ->update([
                'visible' => 0,
                'visibleold' => 0
            ]);

        if ($user) {
            self::setUser($user->id);
        }
    }
}