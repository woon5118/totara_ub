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
use \mod_perform\entities\activity\activity_relationship;
use mod_perform\entities\activity\participant_instance;
use mod_perform\entities\activity\subject_instance;
use mod_perform\entities\activity\track_user_assignment;
use mod_perform\expand_task;
use mod_perform\task\service\participant_instance_creation;
use mod_perform\task\service\subject_instance_creation;
use mod_perform\task\service\subject_instance_dto;
use totara_job\job_assignment;
use totara_job\relationship\resolvers\appraiser;
use totara_job\relationship\resolvers\manager;

/**
 * Class participant_instance_creation_service_test
 */
class mod_perform_participant_instance_creation_service_testcase extends advanced_testcase {

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
    private $activity_relationships;

    /**
     * Data of Activities used.
     *
     * @var array
     */
    private $activity_trees;

    /**
     * Test participant_instances watcher processes on subject_instance_creation hook call.
     *
     * @return void
     */
    public function test_subject_instance_creation_hook(): void {
        $track_user_assignments = $this->get_track_user_assignments();

        $subject_instance_service = new subject_instance_creation();
        $subject_instance_service->generate_instances();

        $this->assert_activity_relationship_id_is_saved();
        $this->assert_participant_instances_created($track_user_assignments);
    }

    /**
     * Tests participants are created successfully.
     *
     * @return void
     */
    public function test_create_participant_instances(): void {
        $track_user_assignments = $this->get_track_user_assignments();
        $subject_instance_dto_collection = $this->create_subject_instances($track_user_assignments);

        $participant_instance_service = new participant_instance_creation();
        $participant_instance_service->generate_instances($subject_instance_dto_collection);

        $this->assert_activity_relationship_id_is_saved();
        $this->assert_participant_instances_created($track_user_assignments);
    }

    /**
     * Asserts participant instances created.
     *
     * @param collection $track_user_assignments
     * @return void
     */
    private function assert_participant_instances_created(collection $track_user_assignments): void {
        $created_participants = participant_instance::repository()->get();
        $expected_participants_created = $track_user_assignments->count()
            * count($this->activity_relationships)
            * $this->users_per_relationship;
        $this->assertEquals($expected_participants_created, $created_participants->count());

        $this->assert_each_participant_created_with_correct_data($created_participants);
    }

    /**
     * Asserts each participant instance is created with the right data.
     *
     * @param collection $created_participants
     * @return void
     */
    private function assert_each_participant_created_with_correct_data(collection $created_participants): void {
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
                $relationship_ids[$activity_id][$participant_relationship] = $created_participant->activity_relationship_id;
            }
            $this->assertEquals(
                $relationship_ids[$activity_id][$participant_relationship],
                $created_participant->activity_relationship_id
            );
        }

        $expected_participant_count = count($subject_instance_ids) / $this->users_per_relationship;
        foreach ($managers_and_appraisers_list as $activity_participants) {
            foreach ($activity_participants as $actual_participant) {
                $this->assertEquals($expected_participant_count, $actual_participant['count']);
            }
        }

        $expected_subject_instance_count = count($this->activity_relationships) * $this->users_per_relationship;
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
     * Asserts the relationship ids saved in participant instances are the activity_relationship_id.
     *
     * @return void
     */
    private function assert_activity_relationship_id_is_saved(): void {
        $activity_relationships = activity_relationship::repository()
            ->select('id')
            ->get()
            ->pluck('id');
        $participant_instance_relationships = participant_instance::repository()
            ->select(['id', 'activity_relationship_id'])
            ->get()
            ->pluck('activity_relationship_id');

        $this->assertEqualsCanonicalizing(array_unique($participant_instance_relationships), $activity_relationships);
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
     * @param collection $track_user_assignments
     * @return collection
     */
    private function create_subject_instances(collection $track_user_assignments): collection {
        $subject_instances = new collection();

        foreach ($track_user_assignments as $user_assignment) {
            $subject_instance = new subject_instance();
            $subject_instance->track_user_assignment_id = $user_assignment->id;
            $subject_instance->subject_user_id = $user_assignment->subject_user_id;
            $subject_instance->save();
            $subject_instances->append(subject_instance_dto::create_from_entity($subject_instance));
        }
        return $subject_instances;
    }

    /**
     * Sets up test pre-conditions.
     */
    protected function setUp() {
        $this->setup_config_values();
        $this->activity_trees = [];
        $activity_count = 2;
        for ($i = 0; $i < $activity_count; $i++) {
            $activity_tree = $this->setup_activity($i);
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
        $this->activity_relationships = [
            appraiser::class,
            manager::class,
        ];
    }

    /**
     * Setup Activity details.
     *
     * @param int $identifier For number of activities to create.
     * @return stdClass
     */
    protected function setup_activity(int $identifier): stdClass {
        /** @var mod_perform_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_perform');
        $activity_tree = new stdClass();
        $activity_tree->activity = $generator->create_activity_in_container();

        //create sections and add relationships to activity:
        $activity_tree->section = $generator->create_section(
            $activity_tree->activity,
            ['title' => 'Test section for activity ' . $identifier]
        );

        foreach ($this->activity_relationships as $relationship_class) {
            $generator->create_section_relationship(
                $activity_tree->section,
                ['class_name' => $relationship_class]
            );
        }

        $tracks = $generator->create_activity_tracks($activity_tree->activity);
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
    protected function tearDown() {
        $this->users_per_relationship = null;
        $this->activity_relationships = null;
        $this->activity_trees = null;
    }
}
