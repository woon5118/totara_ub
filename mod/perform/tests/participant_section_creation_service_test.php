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

use mod_perform\entities\activity\participant_section;
use mod_perform\entities\activity\section;
use mod_perform\models\activity\section_relationship;
use mod_perform\state\participant_instance\not_started;
use mod_perform\task\service\participant_instance_creation;
use mod_perform\task\service\participant_instance_dto;
use mod_perform\task\service\participant_section_creation;
use core\collection;
use mod_perform\entities\activity\participant_instance;
use mod_perform\entities\activity\subject_instance;
use mod_perform\entities\activity\track_assignment;
use mod_perform\entities\activity\track_user_assignment;
use mod_perform\expand_task;
use mod_perform\models\activity\track;
use mod_perform\task\service\subject_instance_creation;
use mod_perform\task\service\subject_instance_dto;
use mod_perform\user_groups\grouping;
use totara_core\relationship\relationship;
use totara_job\job_assignment;
use totara_job\relationship\resolvers\appraiser;
use totara_job\relationship\resolvers\manager;

/**
 * Class participant_section_creation_service_test
 */
class mod_perform_participant_section_creation_service_testcase extends advanced_testcase {

    /**
     * Data of Activities used.
     * @var array
     */
    private $activity_trees;

    /**
     * Test participant_sections are created when the subject instance service generates subject instances.
     *
     * @return void
     */
    public function test_participant_sections_are_created_from_subject_instance_creation(): void {
        $subject_instance_service = new subject_instance_creation();
        $subject_instance_service->generate_instances();

        $this->assert_participant_sections_created_count();
    }

    /**
     * Test participant_sections are created when the participant instance service generates participant instances.
     *
     * @return void
     */
    public function test_participant_sections_are_created_from_participant_instance_creation(): void {
        $subject_instance_collection = $this->create_subject_instances();
        $participant_instance_service = new participant_instance_creation();
        $participant_instance_service->generate_instances($subject_instance_collection);

        $this->assert_participant_sections_created_count();
    }

    /**
     * Asserts count of participant sections created.
     *
     * @return void
     */
    private function assert_participant_sections_created_count(): void {
        $participant_instance_ids = participant_instance::repository()->get()->pluck('id');
        $created_participant_sections = participant_section::repository()->get();
        $sections = section::repository()->get();
        $activity_sections = [];
        foreach ($sections as $section) {
            if (!isset($activity_sections[$section->activity_id])) {
                $activity_sections[$section->activity_id] = [];
            }
            $activity_sections[$section->activity_id][] = $section->id;
        }

        foreach ($created_participant_sections as $participant_section) {
            $this->assertContains(
                $participant_section->participant_instance_id,
                $participant_instance_ids
            );
            $activity_id = $sections->find('id', $participant_section->section_id)->activity_id;
            $sections_created_for_participant = $created_participant_sections->filter('participant_instance_id', $participant_section->participant_instance_id)->pluck('section_id');
            $this->assertEqualsCanonicalizing($sections_created_for_participant, $activity_sections[$activity_id]);
        }
    }

    /**
     * Tests participants are created successfully.
     *
     * @return void
     */
    public function test_create_participant_sections(): void {
        $participant_section_service = new participant_section_creation();
        $participant_instance_dto_collection = $this->setup_participant_instances();
        $participant_section_service->generate_sections($participant_instance_dto_collection);

        $sections = section::repository()->get();
        $created_participant_sections = participant_section::repository()->get();
        $expected_participant_sections_created = 0;

        foreach ($participant_instance_dto_collection as $participant_instance_dto) {
            $this->assertContains(
                $participant_instance_dto->id,
                $created_participant_sections->pluck('participant_instance_id')
            );
            $activity_sections = $sections->filter('activity_id', $participant_instance_dto->activity_id);
            $this->assertEqualsCanonicalizing(
                $activity_sections->pluck('id'),
                $created_participant_sections->filter('participant_instance_id', $participant_instance_dto->id)->pluck('section_id')
            );
            $expected_participant_sections_created = $expected_participant_sections_created + $activity_sections->count();
        }
        $this->assertEquals($expected_participant_sections_created, $created_participant_sections->count());
    }

    /**
     * Setup participant_data.
     *
     * @param stdClass $activity_tree
     * @return collection
     */
    private function setup_participant_instances(): collection {
        $user_assignments = $this->get_track_user_assignments();
        $subject_instances = new collection();
        $participant_instance_dto_list = new collection();

        foreach ($user_assignments as $user_assignment) {
            $subject_instance = new subject_instance();
            $subject_instance->track_user_assignment_id = $user_assignment->id;
            $subject_instance->subject_user_id = $user_assignment->subject_user_id;
            $subject_instance->save();
            $subject_instances->append(subject_instance_dto::create_from_entity($subject_instance));
        }

        //create participant instances.
        /** @var section_relationship $section_relationship*/
        foreach ($this->activity_trees as $activity_tree) {
            foreach ($activity_tree->section_relationships as $section_relationship) {
                /** @var relationship $relationship*/
                $relationship = $section_relationship->get_relationship();

                foreach ($subject_instances as $subject_instance) {
                    $participants = $relationship->get_users(
                        [
                            'user_id' => $subject_instance->subject_user_id,
                        ]
                    );

                    foreach ($participants as $participant) {
                        $participant_instance = new participant_instance();
                        $participant_instance->subject_instance_id = $subject_instance->id;
                        $participant_instance->participant_id = $participant;
                        $participant_instance->activity_relationship_id = $relationship->get_id();
                        $participant_instance->progress = not_started::get_code();
                        $participant_instance->save();
                        $participant_instance_dto = participant_instance_dto::create_from_data(
                            [
                                'activity_id' => $activity_tree->activity->id,
                                'id' => $participant_instance->id,
                            ]
                        );
                        $participant_instance_dto_list->append($participant_instance_dto);
                    }
                }
            }
        }

        return $participant_instance_dto_list;
    }

    /**
     * Setup test pre-conditions.
     */
    protected function setUp() {
        $this->setAdminUser();
        $this->activity_trees = [];

        $activity_count = 4;
        for ($i = 0; $i < $activity_count; $i++) {
            $activity_tree = $this->setup_activity($i);
            $activity_tree->identifier = $i;
            $this->setup_job_assignments($activity_tree);
            $this->activity_trees[] = $activity_tree;
        }
    }

    /**
     * Setup activity details.
     *
     * @param int $identifier For number of activities to create.
     * @return stdClass
     */
    private function setup_activity(int $identifier): stdClass {
        /** @var mod_perform_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_perform');

        $activity_tree = new stdClass();
        $activity_tree->activity = $generator->create_activity_in_container();

        //create sections and add relationships to activity:
        $activity_tree->section = $generator->create_section($activity_tree->activity, ['title' => 'Test activity section '. $identifier]);
        $activity_tree->section_relationships = [];
        $activity_tree->section_relationships[] = $generator->create_section_relationship(
            $activity_tree->section,
            ['class_name' => appraiser::class]
        );
        $activity_tree->section_relationships[] = $generator->create_section_relationship(
            $activity_tree->section,
            ['class_name' => manager::class]
        );

        /** @var track $track */
        $tracks = $generator->create_activity_tracks($activity_tree->activity);
        $activity_tree->track = $generator->create_track_assignments($tracks->first(), 0, 0, 0, 4);
        $activity_tree->assignments = $activity_tree->track->assignments;

        return $activity_tree;
    }

    /**
     * Sets up job assignments and returns stats of job assignments created.
     *
     * @param stdClass $activity_tree
     * @return void
     */
    private function setup_job_assignments($activity_tree): void {
        $users_per_relationship = 2;
        $job_assignment_data = [];

        for ($i = 0; $i < $users_per_relationship; $i++) {
            $manager = $this->getDataGenerator()->create_user();
            $appraiser = $this->getDataGenerator()->create_user();

            $job_assignment_data[] = [
                'manager_ja_id' => job_assignment::create_default($manager->id)->id,
                'appraiser_id' => $appraiser->id,
            ];
        }

        foreach ($activity_tree->track->assignments as $assignment) {
            foreach ($job_assignment_data as $key => $job_assignment_datum) {
                job_assignment::create(
                    [
                        'userid' => $assignment->user_group_id,
                        'idnumber' => $assignment->id . $key . $activity_tree->identifier,
                        'managerjaid' => $job_assignment_datum['manager_ja_id'],
                        'appraiserid' => $job_assignment_datum['appraiser_id'],
                    ]
                );
            }
        }
        (new expand_task())->expand_all();
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
     * Create subject instances for the user assignments.
     *
     * @return collection
     */
    private function create_subject_instances(): collection {
        $user_assignments = $this->get_track_user_assignments();
        $subject_instances = new collection();

        foreach ($user_assignments as $user_assignment) {
            $subject_instance = new subject_instance();
            $subject_instance->track_user_assignment_id = $user_assignment->id;
            $subject_instance->subject_user_id = $user_assignment->subject_user_id;
            $subject_instance->save();
            $subject_instances->append(subject_instance_dto::create_from_entity($subject_instance));
        }
        return $subject_instances;
    }

    /**
     * Cleans class properties.
     */
    protected function tearDown() {
        $this->activity_trees = null;
    }
}
