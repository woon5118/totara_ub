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
 * @author Kunle Odusan <kunle.odusan@totaralearning.com>
 * @package mod_perform
 */

use core\collection;
use mod_perform\constants;
use mod_perform\entities\activity\participant_instance;
use mod_perform\entities\activity\subject_instance;
use mod_perform\entities\activity\track_user_assignment;
use mod_perform\entities\activity\track as track_entity;
use mod_perform\expand_task;
use mod_perform\task\service\participant_instance_creation;
use mod_perform\task\service\subject_instance_creation;
use mod_perform\task\service\subject_instance_dto;
use totara_job\job_assignment;

/**
 * Class participant_instance_creation_service_test
 *
 * @group perform
 */
class mod_perform_participant_instance_creation_service_testcase extends advanced_testcase {

    private const JOB_ASSIGNMENTS_PER_USER = 2;

    /**
     * Number of users per relationship.
     *
     * @var int
     */
    private $users_per_relationship;

    /**
     * Array of activity relationships.
     * @var array
     */
    private $core_relationships;

    /**
     * Data of Activities used.
     *
     * @var array
     */
    private $activity_trees;

    /**
     * Test participant_instances watcher processes on subject_instance_creation hook call.
     *
     * @dataProvider creation_mode_provider
     * @param bool $expand_per_job_assignment
     * @return void
     */
    public function test_subject_instance_creation_hook(bool $expand_per_job_assignment): void {
        $this->setup_assignments($expand_per_job_assignment);

        $track_user_assignments = $this->get_track_user_assignments();

        $subject_instance_service = new subject_instance_creation();
        $subject_instance_service->generate_instances();

        $this->assert_participant_instances_created($track_user_assignments, $expand_per_job_assignment);
    }

    /**
     * Tests participants are created successfully.
     *
     * @dataProvider creation_mode_provider
     * @param bool $expand_per_job_assignment
     * @return void
     */
    public function test_create_participant_instances(bool $expand_per_job_assignment): void {
        $this->setup_assignments($expand_per_job_assignment);

        $track_user_assignments = $this->get_track_user_assignments();
        $subject_instance_dto_collection = $this->create_subject_instances($track_user_assignments);

        $participant_instance_service = new participant_instance_creation();
        $participant_instance_service->generate_instances($subject_instance_dto_collection);

        $this->assert_participant_instances_created($track_user_assignments, $expand_per_job_assignment);
    }

    public function creation_mode_provider(): array {
        return [
            'Expand one per user mode' => [true],
            'Expand one per job mode' => [false],
        ];
    }

    /**
     * Asserts participant instances created.
     *
     * @param collection $track_user_assignments
     * @param bool $expand_per_job_assignment
     * @return void
     */
    private function assert_participant_instances_created(
        collection $track_user_assignments,
        $expand_per_job_assignment = false
    ): void {
        $created_participants = participant_instance::repository()->get();
        $expected_participants_created = $track_user_assignments->count()
            * count($this->core_relationships)
            * $this->users_per_relationship;

        if ($expand_per_job_assignment) {
            $expected_participants_created /= self::JOB_ASSIGNMENTS_PER_USER;
        }

        $this->assertEquals($expected_participants_created, $created_participants->count());

        $this->assert_each_participant_created_with_correct_data($created_participants, $expand_per_job_assignment);
    }

    /**
     * Asserts each participant instance is created with the right data.
     *
     * @param collection $created_participants
     * @param bool $expand_per_job_assignment
     * @return void
     */
    private function assert_each_participant_created_with_correct_data(
        collection $created_participants,
        $expand_per_job_assignment = false
    ): void {
        $relationship_ids = [];
        $subject_instances = subject_instance::repository()->get();
        $subject_instance_ids = $subject_instances->pluck('id');
        $managers_and_appraisers_list = $this->group_participant_job_assignments();
        $subject_instances_counter = [];

        foreach ($created_participants as $created_participant) {
            $activity_id = $subject_instances->find('id', $created_participant->subject_instance_id)->activity()->id;
            $this->assertArrayHasKey(
                $created_participant->participant_id,
                $managers_and_appraisers_list[$activity_id],
                'Unknown participant stored.'
            );
            $managers_and_appraisers_list[$activity_id][$created_participant->participant_id]['count']++;

            $this->assertContains(
                $created_participant->subject_instance_id,
                $subject_instance_ids,
                'unknown subject instance id stored'
            );
            if (!isset($subject_instances_counter[$created_participant->subject_instance_id])) {
                $subject_instances_counter[$created_participant->subject_instance_id] = 0;
            }
            $subject_instances_counter[$created_participant->subject_instance_id]++;

            $participant_relationship =
                $managers_and_appraisers_list[$activity_id][$created_participant->participant_id]['relationship'];
            if (!isset($relationship_ids[$activity_id])) {
                $relationship_ids[$activity_id] = [
                    'manager' => null,
                    'appraiser' => null,
                ];
            }
            if (is_null($relationship_ids[$activity_id][$participant_relationship])) {
                $relationship_ids[$activity_id][$participant_relationship] = $created_participant->core_relationship_id;
            }
            $this->assertEquals(
                $relationship_ids[$activity_id][$participant_relationship],
                $created_participant->core_relationship_id
            );
        }

        $expected_participant_count = count($subject_instance_ids) / $this->users_per_relationship;

        if ($expand_per_job_assignment) {
            $expected_participant_count /= self::JOB_ASSIGNMENTS_PER_USER;
        }

        foreach ($managers_and_appraisers_list as $activity_participants) {
            foreach ($activity_participants as $actual_participant) {
                $this->assertEquals($expected_participant_count, $actual_participant['count']);
            }
        }

        $expected_subject_instance_count = count($this->core_relationships) * $this->users_per_relationship;

        if ($expand_per_job_assignment) {
            $expected_subject_instance_count /= self::JOB_ASSIGNMENTS_PER_USER;
        }

        foreach ($subject_instances_counter as $subject_instance_count) {
            $this->assertEquals($expected_subject_instance_count, $subject_instance_count);
        }
    }

    /**
     * Group participant instance job assignments
     *
     * @return array
     */
    private function group_participant_job_assignments(): array {
        $expected_participants = [];

        foreach ($this->activity_trees as $activity_id => $activity_tree) {
            foreach ($activity_tree->participant_job_assignments as $participant_job_assignment) {
                $expected_participants[$activity_id][$participant_job_assignment['manager']->get_data()->userid] = [
                    'relationship' => 'manager',
                    'count' => 0,
                ];
                $expected_participants[$activity_id][$participant_job_assignment['appraiser']] = [
                    'relationship' => 'appraiser',
                    'count' => 0,
                ];
            }
        }

        return $expected_participants;
    }

    /**
     * Gets loaded user assignments before subject instances are created.
     *
     * @return collection
     */
    private function get_track_user_assignments(): collection {
        return track_user_assignment::repository()
            ->filter_by_no_subject_instances()
            ->filter_by_active()
            ->filter_by_active_track_and_activity()
            ->with('track')
            ->get();
    }

    /**
     * Create subject instances for the track user assignments.
     *
     * @param collection | track_user_assignment[] $track_user_assignments
     * @return collection
     */
    private function create_subject_instances(collection $track_user_assignments): collection {
        $subject_instances = new collection();

        foreach ($track_user_assignments as $user_assignment) {
            $subject_instance = new subject_instance();
            $subject_instance->track_user_assignment_id = $user_assignment->id;
            $subject_instance->subject_user_id = $user_assignment->subject_user_id;
            $subject_instance->job_assignment_id = $user_assignment->job_assignment_id;
            $subject_instance->save();
            $subject_instances->append(subject_instance_dto::create_from_entity($subject_instance));
        }
        return $subject_instances;
    }

    /**
     * Sets up test pre-conditions.
     *
     * @param bool $expand_per_job_assignment
     */
    protected function setup_assignments($expand_per_job_assignment = false): void {
        $this->setup_config_values();
        $this->activity_trees = [];
        $activity_count = 2;
        for ($i = 0; $i < $activity_count; $i++) {
            $activity_tree = $this->setup_activity($i, $expand_per_job_assignment);
            $activity_tree->identifier = $i;
            $this->activity_trees[$activity_tree->activity->id] = $this->setup_job_assignments($activity_tree);
        }
    }

    /**
     * Setups configuration values which the test is based upon.
     *
     * @return void
     */
    protected function setup_config_values(): void {
        $this->setAdminUser();
        $this->users_per_relationship = 2;
        $this->core_relationships = [
            constants::RELATIONSHIP_APPRAISER,
            constants::RELATIONSHIP_MANAGER,
        ];
    }

    /**
     * Setup Activity details.
     *
     * @param int $identifier For number of activities to create.
     * @param bool $expand_per_job_assignment
     * @return stdClass
     * @throws coding_exception
     */
    protected function setup_activity(int $identifier, bool $expand_per_job_assignment = false): stdClass {
        /** @var mod_perform_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_perform');
        $activity_tree = new stdClass();
        $activity_tree->activity = $generator->create_activity_in_container();

        //create sections and add relationships to activity:
        $activity_tree->section = $generator->create_section(
            $activity_tree->activity,
            ['title' => 'Test section for activity ' . $identifier]
        );

        foreach ($this->core_relationships as $relationship) {
            $generator->create_section_relationship(
                $activity_tree->section,
                ['relationship' => $relationship]
            );
        }

        $tracks = $generator->create_activity_tracks($activity_tree->activity);

        if ($expand_per_job_assignment) {
            set_config('totara_job_allowmultiplejobs', 1);

            foreach ($tracks as $track) {
                $track_entity = (new track_entity($track->id));
                $track_entity->subject_instance_generation = track_entity::SUBJECT_INSTANCE_GENERATION_ONE_PER_JOB;
                $track_entity->save();
            }
        }

        $activity_tree->track = $generator->create_track_assignments($tracks->first(), 0, 0, 0, 3);

        return $activity_tree;
    }

    /**
     * Setups job assignments and updates details in activity_tree for the test cases.
     *
     * @param stdClass $activity_tree
     * @return stdClass
     */
    private function setup_job_assignments(stdClass $activity_tree): stdClass {
        $participant_job_assignments = [];
        $data_generator = $this->getDataGenerator();

        for ($i = 0; $i < $this->users_per_relationship; $i++) {
            $participant_job_assignments[] = [
                'manager' => job_assignment::create_default($data_generator->create_user()->id),
                'appraiser' => $data_generator->create_user()->id,
            ];
        }
        $activity_tree->participant_job_assignments = $participant_job_assignments;

        $activity_user_job_assignments = [];
        foreach ($activity_tree->track->assignments as $assignment) {
            foreach ($participant_job_assignments as $key => $job_assignments) {
                $activity_user_job_assignments[] = job_assignment::create(
                    [
                        'userid' => $assignment->user_group_id,
                        'idnumber' => $assignment->id . $key . $activity_tree->identifier,
                        'managerjaid' => $job_assignments['manager']->id,
                        'appraiserid' => $job_assignments['appraiser'],
                    ]
                );
            }
        }

        (new expand_task())->expand_all();

        return $activity_tree;
    }

    /**
     * Cleans up test post-conditions.
     */
    protected function tearDown(): void {
        $this->users_per_relationship = null;
        $this->core_relationships = null;
        $this->activity_trees = null;
    }
}
