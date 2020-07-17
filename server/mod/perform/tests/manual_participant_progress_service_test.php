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

use mod_perform\entities\activity\manual_relationship_selection;
use mod_perform\entities\activity\manual_relationship_selection_progress;
use mod_perform\entities\activity\manual_relationship_selector;
use mod_perform\entities\activity\subject_instance;
use mod_perform\entities\activity\track as track_entity;
use mod_perform\expand_task;
use mod_perform\models\activity\track;
use mod_perform\relationship\resolvers\peer;
use mod_perform\task\service\manual_participant_progress;
use mod_perform\task\service\subject_instance_creation;
use totara_core\entities\relationship;
use totara_core\relationship\resolvers\subject;
use totara_job\job_assignment;
use totara_job\relationship\resolvers\manager;

/**
 * @group perform
 */
class mod_perform_manual_participant_progress_service_testcase extends advanced_testcase {

     /**
     * @return mod_perform_generator|component_generator_base
     */
    protected function perform_generator(): mod_perform_generator {
        return $this->getDataGenerator()->get_plugin_generator('mod_perform');
    }

    public function test_generate_without_manual_relationships() {
        $this->create_data(false, false);

        $progress_service = new manual_participant_progress();
        $progress_service->generate();

        $this->assertEquals(0, manual_relationship_selection_progress::repository()->count());
        $this->assertEquals(0, manual_relationship_selector::repository()->count());
    }

    public function test_generate_throws_exception_if_manual_selection_entries_not_present() {
        $data = $this->create_data();

        manual_relationship_selection::repository()->delete();

        $this->expectException(coding_exception::class);
        $this->expectExceptionMessage(
            'Missing manual relationship selection records for activity ' . $data->activity1->id
        );

        $progress_service = new manual_participant_progress();
        $progress_service->generate();
    }

    public function test_generate_throws_exception_if_peer_relationship_is_not_set_for_manual_selection() {
        $data = $this->create_data();

        $peer_relationship = relationship::repository()->where('idnumber', 'perform_peer')->one();

        // Delete just the one entry
        manual_relationship_selection::repository()->where('manual_relationship_id', $peer_relationship->id)->delete();

        $this->expectException(coding_exception::class);
        $this->expectExceptionMessage(sprintf(
            'No manual_relation_selection record found for relationship id %d in activity %d',
            $peer_relationship->id,
            $data->activity1->id
        ));

        $progress_service = new manual_participant_progress();
        $progress_service->generate();
    }

    public function test_generate_without_multiple_jobs() {
        $data = $this->create_data();

        /** @var subject_instance $subject_instance1 */
        $subject_instance1 = subject_instance::repository()
            ->where('subject_user_id', $data->user1->id)
            ->one();

        /** @var subject_instance $subject_instance2 */
        $subject_instance2 = subject_instance::repository()
            ->where('subject_user_id', $data->user2->id)
            ->one();

        $progress_service = new manual_participant_progress();
        $progress_service->generate();

        $this->assertEquals(2, manual_relationship_selection_progress::repository()->count());

        $expected = [
            $subject_instance1->id => [
                $data->manager1->id
            ],
            $subject_instance2->id => [
                $data->manager2->id,
                $data->manager3->id,
            ],
        ];

        $this->assert_selectors_are_present($expected);

        // Now the manager changes, make sure the syncing works
        $new_manager = $this->getDataGenerator()->create_user();
        $new_manager_job = job_assignment::create(['userid' => $new_manager->id, 'idnumber' => 'nmj']);

        /** @var job_assignment $job2 */
        $job2 = $data->job2;
        $job2->update(['managerjaid' => $new_manager_job->id]);

        $progress_service = new manual_participant_progress();
        $progress_service->generate();

        // The new manager should be in the table along with the already existing ones
        $expected = [
            $subject_instance1->id => [
                $data->manager1->id
            ],
            $subject_instance2->id => [
                $new_manager->id,
                $data->manager2->id,
                $data->manager3->id,
            ],
        ];

        $this->assert_selectors_are_present($expected);

        // Now change the selector_relationship and check whether the sync picks it up.
        // This won't happen as we do not allow changing the data on active activities
        // but we want to be sure the logic would handle this case.

        /** @var manual_relationship_selection $selector_relationship */
        $selector_relationship = manual_relationship_selection::repository()
            ->where('manual_relationship_id', $this->generator()->get_core_relationship(peer::class)->id)
            ->where('activity_id', $data->activity1->id)
            ->one();

        $selector_relationship->selector_relationship_id = $this->generator()->get_core_relationship(subject::class)->id;
        $selector_relationship->save();

        $progress_service = new manual_participant_progress();
        $progress_service->generate();

        // The subjet now should also be part of the list
        $expected = [
            $subject_instance1->id => [
                $subject_instance1->subject_user_id,
                $data->manager1->id
            ],
            $subject_instance2->id => [
                $subject_instance2->subject_user_id,
                $new_manager->id,
                $data->manager2->id,
                $data->manager3->id,
            ],
        ];

        $this->assert_selectors_are_present($expected);


        // Now mark the progress as done and make sure nothing gets synced
        manual_relationship_selection_progress::repository()
            ->update(['status' => 1]);

        // Now the manager changes, make sure the syncing works
        $new_manager2 = $this->getDataGenerator()->create_user();
        $new_manager_job2 = job_assignment::create(['userid' => $new_manager2->id, 'idnumber' => 'nmj']);

        /** @var job_assignment $job2 */
        $job2 = $data->job2;
        $job2->update(['managerjaid' => $new_manager_job2->id]);

        $progress_service = new manual_participant_progress();
        $progress_service->generate();

        // Nothing changes since last run
        $this->assert_selectors_are_present($expected);
    }

    public function test_generate_with_multiple_jobs() {
        $data = $this->create_data(true);

        $subject_instance1 = subject_instance::repository()
            ->where('subject_user_id', $data->user1->id)
            ->where('job_assignment_id', $data->job1->id)
            ->one();

        $subject_instance2 = subject_instance::repository()
            ->where('subject_user_id', $data->user2->id)
            ->where('job_assignment_id', $data->job2->id)
            ->one();

        $subject_instance3 = subject_instance::repository()
            ->where('subject_user_id', $data->user2->id)
            ->where('job_assignment_id', $data->job3->id)
            ->one();

        $progress_service = new manual_participant_progress();
        $progress_service->generate();

        $this->assertEquals(3, manual_relationship_selection_progress::repository()->count());

        $expected = [
            $subject_instance1->id => [
                $data->manager1->id
            ],
            $subject_instance2->id => [
                $data->manager2->id,
            ],
            $subject_instance3->id => [
                $data->manager3->id,
            ],
        ];

        $this->assert_selectors_are_present($expected);
    }

    /**
     * @param bool $use_per_job_creation
     * @param bool $with_manual_relatioship
     * @return object
     * @throws coding_exception
     * @throws moodle_exception
     */
    protected function create_data(bool $use_per_job_creation = false, bool $with_manual_relatioship = true) {
        $data = new class {
            public $activity1;
            public $track1;
            public $manager1;
            public $manager2;
            public $manager3;
            public $user1;
            public $user2;
            public $job1;
            public $job2;
            public $job3;
        };

        $this->setAdminUser();

        $generator = $this->generator();

        $data->activity1 = $generator->create_activity_in_container([
            'create_track' => true,
            'create_section' => false
        ]);

        /** @var track $track1 */
        $data->track1 = track::load_by_activity($data->activity1)->first();

        $section1 = $generator->create_section($data->activity1, ['title' => 'Section 1']);
        $section2 = $generator->create_section($data->activity1, ['title' => 'Section 2']);
        $section3 = $generator->create_section($data->activity1, ['title' => 'Section 3']);

        $generator->create_section_relationship($section1, ['class_name' => manager::class]);
        $generator->create_section_relationship($section1, ['class_name' => subject::class]);

        $generator->create_section_relationship($section2, ['class_name' => subject::class]);
        if ($with_manual_relatioship) {
            $generator->create_section_relationship($section2, ['class_name' => peer::class]);
        }

        $generator->create_section_relationship($section3, ['class_name' => manager::class]);
        if ($with_manual_relatioship) {
            $generator->create_section_relationship($section3, ['class_name' => peer::class]);
        }

        if ($use_per_job_creation) {
            set_config('totara_job_allowmultiplejobs', 1);

            $track = new track_entity($data->track1->id);
            $track->subject_instance_generation = track_entity::SUBJECT_INSTANCE_GENERATION_ONE_PER_JOB;
            $track->save();
        }

        $data->manager1 = $this->getDataGenerator()->create_user();
        $manager_job1 = job_assignment::create(['userid' => $data->manager1->id, 'idnumber' => 'jm1']);
        $data->manager2 = $this->getDataGenerator()->create_user();
        $manager_job2 = job_assignment::create(['userid' => $data->manager2->id, 'idnumber' => 'jm2']);
        $data->manager3 = $this->getDataGenerator()->create_user();
        $manager_job3 = job_assignment::create(['userid' => $data->manager3->id, 'idnumber' => 'jm3']);

        $data->user1 = $this->getDataGenerator()->create_user();
        $data->job1 = job_assignment::create(
            [
                'userid' => $data->user1->id,
                'idnumber' => "for-user-{$data->user1->id}",
                'managerjaid' => $manager_job1->id
            ]
        );

        // User two has two job assignments with different managers
        $data->user2 = $this->getDataGenerator()->create_user();
        $data->job2 = job_assignment::create(
            [
                'userid' => $data->user2->id,
                'idnumber' => "for-user-{$data->user2->id}",
                'managerjaid' => $manager_job2->id
            ]
        );
        $data->job3 = job_assignment::create(
            [
                'userid' => $data->user2->id,
                'idnumber' => "for-user-{$data->user2->id}-2",
                'managerjaid' => $manager_job3->id
            ]
        );

        $cohort = $generator->create_cohort_with_users([$data->user1->id, $data->user2->id]);

        $generator->create_track_assignments_with_existing_groups($data->track1, [$cohort->id]);

        (new expand_task())->expand_all();

        // Generate the subject instances first, they now should be pending
        $subject_instance_service = new subject_instance_creation();
        $subject_instance_service->generate_instances();

        return $data;
    }

    protected function generator(): mod_perform_generator {
        return $this->getDataGenerator()->get_plugin_generator('mod_perform');
    }

    private function assert_selectors_are_present(array $expected) {
        $selectors = manual_relationship_selector::repository()->get();

        $expected_count = 0;
        foreach ($expected as $instance) {
            $expected_count = $expected_count + count($instance);
        }

        $this->assertCount($expected_count, $selectors);

        /** @var manual_relationship_selector $selector */
        foreach ($selectors as $selector) {
            $subject_instance = $selector->subject_instance;
            $this->assertContains($selector->user_id, $expected[$subject_instance->id]);
            unset($expected[$subject_instance->id][array_search($selector->user_id, $expected[$subject_instance->id])]);
            if (empty($expected[$subject_instance->id])) {
                unset($expected[$subject_instance->id]);
            }
        }

        // All expected ones should be tackled
        $this->assertEmpty($expected, 'Discrepancy found: Selectors found which should not be there');
    }

}