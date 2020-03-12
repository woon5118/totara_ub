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
use mod_perform\entities\activity\participant_instance;
use mod_perform\entities\activity\subject_instance;
use mod_perform\entities\activity\track_assignment;
use mod_perform\entities\activity\track_user_assignment;
use mod_perform\expand_task;
use mod_perform\task\service\participant_instance_creation;
use mod_perform\task\service\subject_instance_creation;
use mod_perform\task\service\subject_instance_dto;
use mod_perform\user_groups\grouping;
use totara_job\job_assignment;
use totara_job\relationship\resolvers\appraiser;
use totara_job\relationship\resolvers\manager;

/**
 * Class participant_instance_creation_service_test
 */
class participant_instance_creation_service_test extends advanced_testcase {

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
     * Test participant_instances watcher processes on subject_instance_creation hook call.
     *
     * @return void
     */
    public function test_subject_instance_creation_hook(): void {
        $track_user_assignments = $this->get_track_user_assignments();

        $subject_instance_service = new subject_instance_creation();
        $subject_instance_service->generate_instances();

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

        $this->assert_participant_instances_created($track_user_assignments);
    }

    /**
     * Asserts participant instances created.
     *
     * @param collection $track_user_assignments
     * @return void
     */
    private function assert_participant_instances_created(collection $track_user_assignments): void {
        $created_participants = participant_instance::repository()->count();
        $expected_participants_created = $track_user_assignments->count()
            * count($this->activity_relationships)
            * $this->users_per_relationship;
        $this->assertEquals($expected_participants_created, $created_participants);
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
        $activity_tree = $this->setup_activity();
        $this->setup_job_assignments($activity_tree);
    }

    /**
     * Setups configuration values which the test is based upon.
     *
     * @return void
     */
    protected function setup_config_values(): void {
        $this->setAdminUser();
        $this->users_per_relationship = 5;
        $this->activity_relationships = [
            appraiser::class,
            manager::class,
        ];
    }

    /**
     * Setup Activity details.
     *
     * @return stdClass
     */
    protected function setup_activity(): stdClass {
        /** @var mod_perform_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_perform');
        $activity_tree = new stdClass();
        $activity_tree->activity = $generator->create_activity_in_container();

        //create sections and add relationships to activity:
        $activity_tree->section = $generator->create_section($activity_tree->activity, ['title' => 'Test section 1']);

        foreach ($this->activity_relationships as $relationship_class) {
            $generator->create_section_relationship(
                $activity_tree->section,
                ['class_name' => $relationship_class]
            );
        }

        $tracks = $generator->create_activity_tracks($activity_tree->activity);
        $activity_tree->track = $generator->create_track_assignments($tracks->first(), 0, 0, 0, 20);
        $activity_tree->assignments = track_assignment::repository()
            ->where('user_group_type', grouping::USER)
            ->get();

        return $activity_tree;
    }

    /**
     * Setups job assignments for the test cases.
     *
     * @param stdClass $activity_tree
     * @return void
     */
    private function setup_job_assignments(stdClass $activity_tree): void {
        $job_assignment_data = [];

        for ($i = 0; $i < $this->users_per_relationship; $i++) {
            $manager = $this->getDataGenerator()->create_user();
            $job_assignment_data[] = [
                'manager_ja_id' => job_assignment::create_default($manager->id)->id,
                'appraiser_id' => $this->getDataGenerator()->create_user()->id,
            ];
        }

        foreach ($activity_tree->assignments as $assignment) {
            $job_assignments = [];

            foreach ($job_assignment_data as $key => $job_assignment_datum) {
                $job_assignments[] = job_assignment::create(
                    [
                        'userid' => $assignment->user_group_id,
                        'idnumber' => $assignment->id . $key,
                        'managerjaid' => $job_assignment_datum['manager_ja_id'],
                        'appraiserid' => $job_assignment_datum['appraiser_id'],
                    ]
                );
            }
        }
        (new expand_task())->expand_all();
    }

    /**
     * Cleans up test post-conditions.
     */
    protected function tearDown() {
        $this->users_per_relationship = null;
        $this->activity_relationships = null;
    }
}
