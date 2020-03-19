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
 * @author Fabian Derschatta <fabian.derschatta@totaralearning.com>
 * @package mod_perform
 */

use mod_perform\entities\activity\activity as activity_entity;
use mod_perform\entities\activity\subject_instance;
use mod_perform\entities\activity\track as track_entity;
use mod_perform\entities\activity\track_assignment;
use mod_perform\entities\activity\track_user_assignment;
use mod_perform\expand_task;
use mod_perform\hook\subject_instances_created;
use mod_perform\models\activity\activity as activity_model;
use mod_perform\models\activity\track;
use mod_perform\models\activity\track_status;
use mod_perform\task\service\subject_instance_creation;
use mod_perform\task\service\subject_instance_dto;
use mod_perform\user_groups\grouping;

/**
 * @group perform
 */
class mod_perform_subject_instance_creation_service_testcase extends advanced_testcase {

    public function test_create_subject_instances() {
        $data = $this->create_data();

        // There should be three user assignments now
        $user_assignments = track_user_assignment::repository()->get();
        $this->assertCount(3, $user_assignments);
        $this->assertEquals(0, subject_instance::repository()->count());

        $this->generate_instances();

        $created_instances = subject_instance::repository()->get();
        $this->assertCount(3, $created_instances);
        $this->assertEqualsCanonicalizing(array_column($data->users, 'id'), $created_instances->pluck('subject_user_id'));
        $this->assertEqualsCanonicalizing($user_assignments->pluck('id'), $created_instances->pluck('track_user_assignment_id'));
        foreach ($created_instances->pluck('created_at') as $created_at) {
            $this->assertGreaterThan(0, $created_at);
        }
        foreach ($created_instances->pluck('updated_at') as $updated_at) {
            $this->assertNull($updated_at);
        }
    }

    public function test_no_new_subject_instances_are_created() {
        $data = $this->create_data();

        $this->generate_instances();

        $this->assertEquals(3, subject_instance::repository()->count());

        // Running this again should not create new instances
        $this->generate_instances();

        $this->assertEquals(3, subject_instance::repository()->count());

        // Create a new user assignment
        $user = $this->getDataGenerator()->create_user();
        $cohort_id = $data->cohort_ids[0];

        cohort_add_member($cohort_id, $user->id);

        (new expand_task())->expand_all();

        // The new user assignment should have resulted in a new subject instance now
        $this->generate_instances();

        $this->assertEquals(4, subject_instance::repository()->count());
    }

    public function test_no_instances_are_created_for_deleted_user_assignments() {
        $data = $this->create_data();

        // Delete the assignment and expanding would mark one user assignment as deleted
        /** @var track_user_assignment $user_assignment */
        $user_assignment = track_user_assignment::repository()
            ->order_by('id')
            ->first();

        $user_id = $user_assignment->subject_user_id;
        $cohort_id = $user_assignment->assignments()
            ->order_by('id')
            ->first()
            ->user_group_id;

        cohort_remove_member($cohort_id, $user_id);

        (new expand_task())->expand_all();

        $deleted_user_assignments = track_user_assignment::repository()
            ->where('deleted', true)
            ->get();

        $this->assertCount(1, $deleted_user_assignments);

        /** @var track_user_assignment $deleted_user_assignment */
        $deleted_user_assignment = $deleted_user_assignments->first();
        $this->assertEquals($user_assignment->id, $deleted_user_assignment->id);

        $this->generate_instances();

        $created_instances = subject_instance::repository()->get();
        $this->assertCount(2, $created_instances);

        // The deleted one was not created
        $this->assertNotContains($user_assignment->subject_user_id, $created_instances->pluck('subject_user_id'));
    }

    public function test_instances_are_only_created_for_active_activities() {
        $data = $this->create_data();

        /** @var activity_entity $activity */
        $activity = activity_entity::repository()->find($data->activity1->get_id());
        $activity->status = activity_model::STATUS_INACTIVE;
        $activity->save();

        // There should be three user assignments now
        $user_assignments = track_user_assignment::repository()->get();
        $this->assertCount(3, $user_assignments);
        $this->assertEquals(0, subject_instance::repository()->count());

        $this->generate_instances();

        $this->assertEquals(0, subject_instance::repository()->count());
    }

    public function test_instances_are_only_created_for_active_tracks() {
        $data = $this->create_data();

        /** @var track_entity $track */
        $track = track_entity::repository()->find($data->track1->get_id());
        $track->status = track_status::PAUSED;
        $track->save();

        // There should be three user assignments now
        $user_assignments = track_user_assignment::repository()->get();
        $this->assertCount(3, $user_assignments);
        $this->assertEquals(0, subject_instance::repository()->count());

        $this->generate_instances();

        $this->assertEquals(0, subject_instance::repository()->count());
    }

    public function test_hook_is_executed_properly() {
        $data = $this->create_data();

        $sink = $this->generate_instances();

        $hooks = $sink->get_hooks();
        $this->assertCount(1, $hooks);
        /** @var subject_instances_created $hook */
        $hook = array_shift($hooks);
        $this->assertInstanceOf(subject_instances_created::class, $hook);
        $dtos = $hook->get_dtos();
        $this->assertCount(3, $dtos);

        $subject_instances = subject_instance::repository()->get();

        $this->assertEqualsCanonicalizing($dtos->pluck('id'), $subject_instances->pluck('id'));

        /** @var subject_instance_dto $dto */
        foreach ($dtos as $dto) {
            $this->assertGreaterThan(0, $dto->get_id());
            $this->assertEquals($dto->get_id(), $dto->id);

            /** @var subject_instance $subject_instance */
            $subject_instance = $subject_instances->find('id', $dto->id);
            $this->assertInstanceOf(subject_instance::class, $subject_instance);

            $this->assertEquals($subject_instance->track_user_assignment_id, $dto->get_track_user_assignment_id());
            $this->assertEquals($subject_instance->track_user_assignment_id, $dto->track_user_assignment_id);

            $this->assertEquals($subject_instance->subject_user_id, $dto->get_subject_user_id());
            $this->assertEquals($subject_instance->subject_user_id, $dto->subject_user_id);

            $this->assertEquals($subject_instance->created_at, $dto->get_created_at());
            $this->assertEquals($subject_instance->created_at, $dto->created_at);

            $this->assertEquals($subject_instance->updated_at, $dto->get_updated_at());
            $this->assertEquals($subject_instance->updated_at, $dto->updated_at);
        }

        $sink->close();
    }

    /**
     * This calls the creation service and returns the hooks sink if $no_hooks is set to false.
     *
     * Passing false for $no_hooks enables testing the service in connection with any watchers for hooks
     *
     * @param bool $no_hooks set to false to let hooks execute
     * @return phpunit_hook_sink|null
     */
    protected function generate_instances(bool $no_hooks = true): ?phpunit_hook_sink {
        // We do not want any side effects, just testing the creation of subject instances
        $sink = $no_hooks ? $this->redirectHooks() : null;

        // The new user assignment should have resulted in a new subject instance now
        $service = new subject_instance_creation();
        $service->generate_instances();

        return $sink;
    }

    protected function create_data() {
        $data = new class {
            public $assignments;
            public $activity1;
            public $track1;
            public $users;
            public $cohort_ids;
        };

        $this->setAdminUser();

        /** @var mod_perform_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_perform');

        $data->activity1 = $generator->create_activity_in_container();
        /** @var track $track1 */
        $tracks = $generator->create_activity_tracks($data->activity1);
        $data->track1 = $tracks->first();

        $generator->create_track_assignments($data->track1, 3, 0, 0, 0);

        $data->assignments = track_assignment::repository()
            ->where('user_group_type', grouping::COHORT)
            ->get();

        $data->users = [];
        $data->cohort_ids = [];
        foreach ($data->assignments as $assignment) {
            $user = $this->getDataGenerator()->create_user();
            $data->users[] = $user;
            $data->cohort_ids[] = $assignment->user_group_id;

            cohort_add_member($assignment->user_group_id, $user->id);
        }

        (new expand_task())->expand_all();

        return $data;
    }

}