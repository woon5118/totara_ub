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

use core\orm\query\order;
use mod_perform\constants;
use mod_perform\dates\date_offset;
use mod_perform\entity\activity\activity as activity_entity;
use mod_perform\entity\activity\participant_instance;
use mod_perform\entity\activity\subject_instance;
use mod_perform\entity\activity\subject_static_instance as subject_static_instance_entity;
use mod_perform\entity\activity\track as track_entity;
use mod_perform\entity\activity\track_assignment;
use mod_perform\entity\activity\track_user_assignment;
use mod_perform\expand_task;
use mod_perform\hook\subject_instances_created;
use mod_perform\models\activity\activity as activity_model;
use mod_perform\models\activity\track;
use mod_perform\models\activity\track_status;
use mod_perform\models\activity\subject_static_instance as subject_static_instance_model;
use mod_perform\state\activity\draft;
use mod_perform\state\subject_instance\active;
use mod_perform\state\subject_instance\complete;
use mod_perform\state\subject_instance\pending;
use mod_perform\task\service\subject_instance_creation;
use mod_perform\task\service\subject_instance_dto;
use mod_perform\task\service\track_schedule_sync;
use mod_perform\user_groups\grouping;
use totara_core\dates\date_time_setting;
use totara_job\job_assignment;

/**
 * @group perform
 */
class mod_perform_subject_instance_creation_service_testcase extends advanced_testcase {

     /**
     * @return mod_perform_generator|component_generator_base
     */
    protected function perform_generator(): mod_perform_generator {
        return $this->getDataGenerator()->get_plugin_generator('mod_perform');
    }

    /**
     * @dataProvider creation_mode_provider
     * @param bool $expand_per_job_assignment
     */
    public function test_create_subject_instances(bool $expand_per_job_assignment) {
        $data = $this->create_data($expand_per_job_assignment);

        // There should be three user assignments now
        $user_assignments = track_user_assignment::repository()->get();
        $this->assertCount(3, $user_assignments);
        $this->assertEquals(0, subject_instance::repository()->count());

        $this->generate_instances(false);

        $created_instances = subject_instance::repository()->get();
        $this->assertCount(3, $created_instances);
        $this->assertEqualsCanonicalizing(
            array_column($data->users, 'id'),
            $created_instances->pluck('subject_user_id')
        );

        $created_static_instances = subject_static_instance_entity::repository()->get();
        $this->assertEqualsCanonicalizing(
            $user_assignments->pluck('job_assignment_id'),
            $created_static_instances->pluck('job_assignment_id')
        );

        // All subject instances created are marked as active
        $this->assertEquals([active::get_code()], array_unique($created_instances->pluck('status')));

        // Participant instances were created too
        $this->assertEquals(3, participant_instance::repository()->count());

        $this->assertEqualsCanonicalizing(
            $user_assignments->pluck('id'),
            $created_instances->pluck('track_user_assignment_id')
        );

        $this->assertEqualsCanonicalizing(
            $user_assignments->pluck('job_assignment_id'),
            $created_instances->pluck('job_assignment_id')
        );

        if ($expand_per_job_assignment) {
            $this->assertNotCount(0, $created_instances);

            foreach ($created_instances as $created_instance) {
                $this->assertNotNull($created_instance);
            }
        }

        foreach ($created_instances->pluck('created_at') as $created_at) {
            $this->assertGreaterThan(0, $created_at);
        }
        foreach ($created_instances->pluck('updated_at') as $updated_at) {
            $this->assertNull($updated_at);
        }
    }

    public function creation_mode_provider(): array {
        return [
            'Expand one per user mode' => [false],
            'Expand one per job mode' => [true],
        ];
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

        expand_task::create()->expand_all();

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

        expand_task::create()->expand_all();

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
        $activity->status = draft::get_code();
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

    public function test_instances_are_only_created_for_tracks_not_needing_sync() {
        $data = $this->create_data();

        /** @var track_entity $track */
        $track = track_entity::repository()->find($data->track1->get_id());
        $track->schedule_needs_sync = true;
        $track->save();

        // There should be three user assignments
        $user_assignments = track_user_assignment::repository()->get();
        $this->assertCount(3, $user_assignments);
        $this->assertEquals(0, subject_instance::repository()->count());

        $this->generate_instances();

        $this->assertEquals(0, subject_instance::repository()->count());
    }

    public function period_data_provider(): array {
        // Note that while an end date only should not happen in the real system.,
        // having both null start and end date can happen when there the schedule
        // window period is using a custom date resolver which can have unset
        // reference dates.

        $yesterday = time() - 86400;
        $tomorrow = time() + 86400;
        return [
            'No dates (empty reference dates)' => [null, null, false],
            'Closed schedule inside assignment period' => [$yesterday, $tomorrow, true],
            'Open ended inside assignment period' => [$yesterday, null, true],
            'End date only within assignment period (invalid combination)' => [null, $tomorrow, false],
            'Start date only, outside assignment period' => [$tomorrow, null, false],
            'No start, end date outside assignment period (invalid combination)' => [null, $yesterday, false],
        ];
    }

    /**
     * @dataProvider period_data_provider
     * @param int|null $track_assignment_start
     * @param int|null $track_assignment_end
     * @param bool $should_subject_instance_be_created
     */
    public function test_instances_are_only_created_for_correct_periods(
        ?int $track_assignment_start,
        ?int $track_assignment_end,
        bool $should_subject_instance_be_created
    ) {
        $data = $this->create_data();

        /** @var track_user_assignment $user_assignment */
        $user_assignment = track_user_assignment::repository()
            ->order_by('id')
            ->first();

        $user_assignment->period_start_date = $track_assignment_start;
        $user_assignment->period_end_date = $track_assignment_end;
        $user_assignment->save();

        // There should be three user assignments initially.
        $user_assignments = track_user_assignment::repository()->get();
        $this->assertCount(3, $user_assignments);
        $this->assertEquals(0, subject_instance::repository()->count());

        $this->generate_instances();

        $expected_instance_count = $should_subject_instance_be_created ? 3 : 2;
        $this->assertEquals($expected_instance_count, subject_instance::repository()->count());

        $this->assertEquals(
            $should_subject_instance_be_created,
            subject_instance::repository()
            ->where('subject_user_id', $user_assignment->subject_user_id)
            ->exists()
        );
    }

    /**
     * @dataProvider period_data_provider
     * @param int|null $track_assignment_start
     * @param int|null $track_assignment_end
     */
    public function test_instances_are_not_created_for_missing_job_assignments(
        ?int $track_assignment_start,
        ?int $track_assignment_end
    ) {
        $data = $this->create_data();

        /** @var track_user_assignment $user_assignment */
        $user_assignment = track_user_assignment::repository()
            ->order_by('id')
            ->first();

        $user_assignment->job_assignment_id = -200; // Does not link to any job_assignment.
        $user_assignment->period_start_date = $track_assignment_start;
        $user_assignment->period_end_date = $track_assignment_end;
        $user_assignment->save();

        // There should be three user assignments initially.
        $user_assignments = track_user_assignment::repository()->get();
        $this->assertCount(3, $user_assignments);
        $this->assertEquals(0, subject_instance::repository()->count());

        $this->generate_instances();

        $other_assignments_count = 2;
        $this->assertEquals($other_assignments_count, count($data->assignments) - 1);
        $this->assertEquals($other_assignments_count, subject_instance::repository()->count());

        $this->assertFalse(
            subject_instance::repository()
                ->where('subject_user_id', $user_assignment->subject_user_id)
                ->exists()
        );
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

            $this->assertEquals($subject_instance->job_assignment_id, $dto->get_job_assignment_id());
            $this->assertEquals($subject_instance->job_assignment_id, $dto->job_assignment_id);

            $this->assertEquals($subject_instance->created_at, $dto->get_created_at());
            $this->assertEquals($subject_instance->created_at, $dto->created_at);

            $this->assertEquals($subject_instance->updated_at, $dto->get_updated_at());
            $this->assertEquals($subject_instance->updated_at, $dto->updated_at);
        }

        $sink->close();
    }

    public function test_subject_instances_are_set_to_pending_on_manual_relationships() {
        $this->create_data(false, true);

        $this->generate_instances();

        // tests static instance is created for manual relationships as well.
        $created_static_instances = subject_static_instance_entity::repository()->get();
        $user_assignments = track_user_assignment::repository()->get();
        $this->assertEqualsCanonicalizing(
            $user_assignments->pluck('job_assignment_id'),
            $created_static_instances->pluck('job_assignment_id')
        );

        // All subject instances are marked as pending
        $created_instances = subject_instance::repository()->get();
        $this->assertCount(3, $created_instances);
        $this->assertEquals([pending::get_code()], array_unique($created_instances->pluck('status')));

        // No participant instances were created
        $this->assertEquals(0, participant_instance::repository()->count());
    }

    public function test_static_content_does_not_change() {
        // We will create job assignments separately.
        $data = $this->create_data(false);

        /** @var totara_hierarchy_generator $hierarchies */
        $hierarchies = $this->getDataGenerator()->get_plugin_generator('totara_hierarchy');
        $pos_fw_id = ['frameworkid' => $hierarchies->create_pos_frame([])->id];
        $org_fw_id = ['frameworkid' => $hierarchies->create_org_frame([])->id];

        $job_assignments = [];
        foreach ($data->users as $user) {
            $pos_id = $hierarchies->create_pos($pos_fw_id)->id;
            $org_id = $hierarchies->create_org($org_fw_id)->id;
            $job_assignments[] = job_assignment::create([
                'userid' => $user->id,
                'positionid' => $pos_id,
                'organisationid' => $org_id,
                'idnumber' => "for-user-{$user->id}"
            ]);
        }

        $this->generate_instances(false);
        $created_static_instances = subject_static_instance_entity::repository()->get();

        // Confirm that all job assignments mapped correctly.
        foreach ($job_assignments as $job_assignment) {
            $found = false;
            foreach ($created_static_instances as $created_static_instance) {
                if ($job_assignment->id === $created_static_instance->job_assignment_id) {
                    $found = true;

                    $old_pos_id = $job_assignment->positionid;
                    $old_org_id = $job_assignment->organisationid;

                    // Update job assignment.
                    $job_assignment->update([
                        'positionid' => $hierarchies->create_pos($pos_fw_id)->id,
                        'organisationid' => $hierarchies->create_org($org_fw_id)->id,
                    ]);

                    $new_pos_id = $job_assignment->positionid;
                    $new_org_id = $job_assignment->organisationid;

                    // Refresh job assignment.
                    $job_assignment = job_assignment::get_with_id($job_assignment->id);
                    $this->assertEquals($new_pos_id, $job_assignment->positionid);
                    $this->assertEquals($new_org_id, $job_assignment->organisationid);

                    // Get static instances.
                    $model = subject_static_instance_model::load_by_entity($created_static_instance);
                    $static_job_assignment = $model->get_job_assignment();

                    $this->assertEquals($job_assignment->id, $static_job_assignment->id);
                    $this->assertEquals($old_pos_id, $static_job_assignment->positionid);
                    $this->assertEquals($old_org_id, $static_job_assignment->organisationid);

                    break;
                }
            }
            $this->assertEquals(true, $found);
        }
    }

    /**
     * This calls the creation service and returns the hooks sink if $no_hooks is set to true.
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

    /**
     * @param bool $use_per_job_creation
     * @param bool $with_manual_relatioship
     * @return object
     * @throws coding_exception
     * @throws moodle_exception
     */
    protected function create_data(bool $use_per_job_creation = false, bool $with_manual_relatioship = false) {
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

        $data->activity1 = $generator->create_activity_in_container([
            'create_track' => true,
            'create_section' => false
        ]);
        /** @var track $track1 */
        $data->track1 = track::load_by_activity($data->activity1)->first();

        $section1 = $generator->create_section($data->activity1, ['title' => 'Section 1']);
        $section2 = $generator->create_section($data->activity1, ['title' => 'Section 2']);
        $section3 = $generator->create_section($data->activity1, ['title' => 'Section 3']);

        $generator->create_section_relationship($section1, ['relationship' => constants::RELATIONSHIP_MANAGER]);
        $generator->create_section_relationship($section1, ['relationship' => constants::RELATIONSHIP_SUBJECT]);

        $generator->create_section_relationship($section2, ['relationship' => constants::RELATIONSHIP_SUBJECT]);
        if ($with_manual_relatioship) {
            $generator->create_section_relationship($section2, ['relationship' => constants::RELATIONSHIP_PEER]);
        }

        $generator->create_section_relationship($section3, ['relationship' => constants::RELATIONSHIP_MANAGER]);
        if ($with_manual_relatioship) {
            $generator->create_section_relationship($section3, ['relationship' => constants::RELATIONSHIP_PEER]);
        }

        if ($use_per_job_creation) {
            set_config('totara_job_allowmultiplejobs', 1);

            $track = new track_entity($data->track1->id);
            $track->subject_instance_generation = track_entity::SUBJECT_INSTANCE_GENERATION_ONE_PER_JOB;
            $track->save();
        }

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

            if ($use_per_job_creation) {
                job_assignment::create(['userid' => $user->id, 'idnumber' => "for-user-{$user->id}"]);
            }

            cohort_add_member($assignment->user_group_id, $user->id);
        }

        expand_task::create()->expand_all();

        return $data;
    }

    public function test_repeating_type_after_creation() {
        $this->setAdminUser();
        $track = $this->create_single_track_with_assignments(2);

        // Set repeat to one day after creation.
        $offset = new date_offset(
            1,
            date_offset::UNIT_DAY
        );
        $track->set_repeating_enabled(
            track_entity::SCHEDULE_REPEATING_TYPE_AFTER_CREATION,
            $offset
        );
        $track->update();
        $this->assert_subject_instance_count(0);

        // Initial instances should be created.
        (new subject_instance_creation())->generate_instances();
        $this->assert_subject_instance_count(2);

        // Calling instance generation again does not create more instances.
        (new subject_instance_creation())->generate_instances();
        $subject_instances = subject_instance::repository()->get()->all();
        $this->assertCount(2, $subject_instances);

        /** @var subject_instance $subject_instance_1 */
        $subject_instance_1 = $subject_instances[0];
        /** @var subject_instance $subject_instance_2 */
        $subject_instance_2 = $subject_instances[1];

        // Manipulate created_date so that one instance looks more than a day old.
        $subject_instance_1->created_at = time() - (2 * 86400);
        $subject_instance_1->update();

        // Another instance should now have been created.
        (new subject_instance_creation())->generate_instances();
        $this->assert_subject_instance_count(2, $subject_instance_1->subject_user_id);
        $this->assert_subject_instance_count(1, $subject_instance_2->subject_user_id);
    }

    public function test_repeating_type_after_creation_when_complete() {
        $this->setAdminUser();
        $track = $this->create_single_track_with_assignments(2);

        // Set repeat to one day after creation.
        $offset = new date_offset(
            1,
            date_offset::UNIT_DAY
        );
        $track->set_repeating_enabled(
            track_entity::SCHEDULE_REPEATING_TYPE_AFTER_CREATION_WHEN_COMPLETE,
            $offset
        );
        $track->update();

        // Initial subject instances should be created.
        (new subject_instance_creation())->generate_instances();
        $subject_instances = subject_instance::repository()->get()->all();
        $this->assertCount(2, $subject_instances);

        /** @var subject_instance $subject_instance_1 */
        $subject_instance_1 = $subject_instances[0];
        /** @var subject_instance $subject_instance_2 */
        $subject_instance_2 = $subject_instances[1];
        $this->assert_subject_instance_count(1, $subject_instance_1->subject_user_id);
        $this->assert_subject_instance_count(1, $subject_instance_2->subject_user_id);

        // Manipulate created_at date so that instance looks more than a day old.
        $subject_instance_1->created_at = time() - (2 * 86400);
        $subject_instance_1->update();

        // Another instance should not be created because subject instance is not complete.
        (new subject_instance_creation())->generate_instances();
        $this->assert_subject_instance_count(2);

        $subject_instance_1->progress = complete::get_code();
        $subject_instance_1->update();

        // Another instance should now have been created.
        (new subject_instance_creation())->generate_instances();
        $this->assert_subject_instance_count(2, $subject_instance_1->subject_user_id);
        $this->assert_subject_instance_count(1, $subject_instance_2->subject_user_id);
    }

    public function test_repeating_type_after_completion() {
        $this->setAdminUser();
        $track = $this->create_single_track_with_assignments(2);

        // Set repeat to one day after completion.
        $offset = new date_offset(
            1,
            date_offset::UNIT_WEEK
        );
        $track->set_repeating_enabled(
            track_entity::SCHEDULE_REPEATING_TYPE_AFTER_COMPLETION,
            $offset
        );
        $track->update();

        // Initial subject instances should be created.
        (new subject_instance_creation())->generate_instances();
        $subject_instances = subject_instance::repository()->get()->all();
        $this->assertCount(2, $subject_instances);

        /** @var subject_instance $subject_instance_1 */
        $subject_instance_1 = $subject_instances[0];
        /** @var subject_instance $subject_instance_2 */
        $subject_instance_2 = $subject_instances[1];
        $this->assert_subject_instance_count(1, $subject_instance_1->subject_user_id);
        $this->assert_subject_instance_count(1, $subject_instance_2->subject_user_id);

        // Second instance should not be created yet because completion date is less than a day in the past.
        $subject_instance_1->progress = complete::get_code();
        $subject_instance_1->completed_at = time();
        $subject_instance_1->update();
        (new subject_instance_creation())->generate_instances();
        $this->assert_subject_instance_count(2);

        // Second instance should now be created.
        $subject_instance_1->completed_at = time() - (8 * 86400);
        $subject_instance_1->update();
        (new subject_instance_creation())->generate_instances();
        $this->assert_subject_instance_count(2, $subject_instance_1->subject_user_id);
        $this->assert_subject_instance_count(1, $subject_instance_2->subject_user_id);
    }

    public function test_repeating_limit() {
        $this->setAdminUser();
        $track = $this->create_single_track_with_assignments(2);

        // Set repeat to one day after creation, limit 2.
        $offset = new date_offset(
            1,
            date_offset::UNIT_DAY
        );
        $track->set_repeating_enabled(
            track_entity::SCHEDULE_REPEATING_TYPE_AFTER_CREATION,
            $offset,
            3
        );
        $track->update();

        // Initial subject instance should be created.
        (new subject_instance_creation())->generate_instances();
        $subject_instances = subject_instance::repository()->get()->all();
        $this->assertCount(2, $subject_instances);

        /** @var subject_instance $subject_instance_1_1 */
        $subject_instance_1_1 = $subject_instances[0];
        /** @var subject_instance $subject_instance_2 */
        $subject_instance_2 = $subject_instances[1];
        $this->assert_subject_instance_count(1, $subject_instance_1_1->subject_user_id);
        $this->assert_subject_instance_count(1, $subject_instance_2->subject_user_id);

        // Calling instance generation again does not create a second instance.
        (new subject_instance_creation())->generate_instances();
        $this->assert_subject_instance_count(2);

        // Manipulate created_date so that instance looks old enough to create a new one.
        $subject_instance_1_1->created_at = time() - (2 * 86400);
        $subject_instance_1_1->update();

        // Second instance should now be created.
        (new subject_instance_creation())->generate_instances();
        $this->assert_subject_instance_count(2, $subject_instance_1_1->subject_user_id);
        $this->assert_subject_instance_count(1, $subject_instance_2->subject_user_id);

        /** @var subject_instance $subject_instance_1_2 */
        $subject_instance_1_2 = subject_instance::repository()->order_by('id', order::DIRECTION_DESC)->first();
        $this->assertGreaterThan($subject_instance_1_1->id, $subject_instance_1_2->id);
        $this->assertEquals($subject_instance_1_1->subject_user_id, $subject_instance_1_2->subject_user_id);

        // Calling instance generation again does not create an additional instance because the
        // most recent subject instance (with the highest id) is not more than a day old.
        (new subject_instance_creation())->generate_instances();
        $this->assert_subject_instance_count(2, $subject_instance_1_1->subject_user_id);
        $this->assert_subject_instance_count(1, $subject_instance_2->subject_user_id);

        // Manipulate created_date so that latest instance looks old enough to create a new one.
        $subject_instance_1_2->created_at = time() - (2 * 86400);
        $subject_instance_1_2->update();

        // Third instance should now be created.
        (new subject_instance_creation())->generate_instances();
        $this->assert_subject_instance_count(3, $subject_instance_1_1->subject_user_id);
        $this->assert_subject_instance_count(1, $subject_instance_2->subject_user_id);

        /** @var subject_instance $subject_instance_1_3 */
        $subject_instance_1_3 = subject_instance::repository()->order_by('id', order::DIRECTION_DESC)->first();
        $this->assertGreaterThan($subject_instance_1_2->id, $subject_instance_1_3->id);
        $this->assertEquals($subject_instance_1_2->subject_user_id, $subject_instance_1_3->subject_user_id);

        $subject_instance_1_3->created_at = time() - (2 * 86400);
        $subject_instance_1_3->update();

        // No further instances should be created because we have hit the limit.
        (new subject_instance_creation())->generate_instances();
        $this->assert_subject_instance_count(3, $subject_instance_1_1->subject_user_id);
        $this->assert_subject_instance_count(1, $subject_instance_2->subject_user_id);

        // Increase the limit, so additional instance is created.
        $offset = new date_offset(
            1,
            date_offset::UNIT_DAY
        );
        $track->set_repeating_enabled(
            track_entity::SCHEDULE_REPEATING_TYPE_AFTER_CREATION,
            $offset,
            4
        );
        $track->update();
        (new subject_instance_creation())->generate_instances();
        $this->assert_subject_instance_count(4, $subject_instance_1_1->subject_user_id);
        $this->assert_subject_instance_count(1, $subject_instance_2->subject_user_id);
    }

    public function test_due_date_disabled() {
        $this->setAdminUser();
        $track = $this->create_single_track_with_assignments(1);
        $track->set_due_date_disabled();
        $track->update();

        (new subject_instance_creation())->generate_instances();
        /** @var subject_instance $subject_instance */
        $subject_instance = subject_instance::repository()->one();
        $this->assertNull($subject_instance->due_date);
    }

    public function test_due_date_fixed() {
        $this->setAdminUser();
        $track = $this->create_single_track_with_assignments(1);
        $yesterday = new date_time_setting(time() - 86400);
        $tomorrow = new date_time_setting(time() + 86400);
        $day_after_tomorrow = new date_time_setting(time() + (2 * 86400));
        $track->set_schedule_closed_fixed($yesterday, $tomorrow);
        $track->set_due_date_fixed($day_after_tomorrow);
        $track->update();

        // Also need to run schedule sync because we changed creation range.
        (new track_schedule_sync())->sync_all_flagged();
        (new subject_instance_creation())->generate_instances();
        /** @var subject_instance $subject_instance */
        $subject_instance = subject_instance::repository()->one();
        $this->assertEquals($day_after_tomorrow->get_timestamp(), $subject_instance->due_date);
    }

    public function test_due_date_relative() {
        $this->setAdminUser();
        $track = $this->create_single_track_with_assignments(1);
        $day_after_tomorrow = (new \DateTimeImmutable('now', new DateTimeZone('utc')))
            ->modify('+ 2 day')
            ->getTimestamp();
        $offset = new date_offset(
            2,
            date_offset::UNIT_DAY
        );
        $track->set_due_date_relative($offset);
        $track->update();

        (new subject_instance_creation())->generate_instances();
        /** @var subject_instance $subject_instance */
        $subject_instance = subject_instance::repository()->one();
        $this->assertGreaterThanOrEqual($day_after_tomorrow, $subject_instance->due_date);
    }

    /**
     * @param int $num_users
     * @return track
     */
    private function create_single_track_with_assignments(int $num_users): track {
        $generator = $this->perform_generator();
        $config = mod_perform_activity_generator_configuration::new()
            ->disable_subject_instances()
            ->set_number_of_users_per_user_group_type($num_users);
        /** @var activity_model $activity */
        $activity = $generator->create_full_activities($config)->first();
        /** @var track $track */
        return $activity->get_tracks()->first();
    }

    /**
     * @param int $expected_count
     * @param int|null $user_id
     */
    private function assert_subject_instance_count(int $expected_count, ?int $user_id = null) {
        $repository = subject_instance::repository();
        if ($user_id) {
            $repository->where('subject_user_id', $user_id);
        }
        $this->assertCount($expected_count, $repository->get());
    }
}
