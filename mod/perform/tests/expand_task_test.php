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
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Fabian Derschatta <fabian.derschatta@totaralearning.com>
 * @package mod_perform
 * @category test
 */

use core\entities\user;
use core\orm\entity\repository as entity_repository;
use core\orm\query\builder;
use mod_perform\dates\resolvers\dynamic\dynamic_source;
use mod_perform\dates\resolvers\dynamic\user_creation_date;
use mod_perform\dates\resolvers\dynamic\user_custom_field;
use mod_perform\entities\activity\activity;
use mod_perform\entities\activity\track;
use mod_perform\entities\activity\track as track_entity;
use mod_perform\entities\activity\track_assignment;
use mod_perform\entities\activity\track_user_assignment;
use mod_perform\event\track_user_assigned_bulk;
use mod_perform\event\track_user_unassigned;
use mod_perform\expand_task;
use mod_perform\models\activity\activity as activity_model;
use mod_perform\models\activity\track_assignment_type;
use mod_perform\models\activity\track_status;
use mod_perform\state\activity\draft;
use mod_perform\user_groups\grouping;

defined('MOODLE_INTERNAL') || die();

/**
 * @coversDefaultClass expand_task.
 *
 * @group perform
 */
class mod_perform_expand_task_testcase extends advanced_testcase {

    public static function setUpBeforeClass() {
        parent::setUpBeforeClass();
        global $CFG;
        require_once($CFG->dirroot . '/cohort/lib.php');
    }

    /**
     * Date generator shortcut
     *
     * @return testing_data_generator
     */
    protected function generator(): \testing_data_generator {
        return self::getDataGenerator();
    }

    protected function setUp() {
        parent::setUp();
        $this->setAdminUser();
    }

    protected function tearDown() {
        parent::tearDown();
    }

    public function test_expand_single_assignment(): void {
        $test_data = $this->prepare_assignments();

        $track1_id = $test_data->track1->id;

        $this->assert_track_has_no_user_assignments($track1_id, $test_data->user1->id);

        // No user added to cohort so nothing should happen
        (new expand_task())->expand_single($test_data->assignment1->id);

        $this->assert_track_has_no_user_assignments($track1_id, $test_data->user1->id);

        // Add the users to the cohort
        $this->add_user_to_cohort($test_data->cohort1->id, $test_data->user1->id);

        // This should now result in a user assignment
        (new expand_task())->expand_single($test_data->assignment1->id);

        $this->assert_track_has_user_assignments($track1_id, $test_data->user1->id);
        // The other user is not in a cohort yet
        $this->assert_track_has_no_user_assignments($track1_id, $test_data->user2->id);

        // Now add the other one
        $this->add_user_to_cohort($test_data->cohort2->id, $test_data->user2->id);

        (new expand_task())->expand_single($test_data->assignment1->id);

        // Should have been expanded
        $this->assert_track_has_user_assignments($track1_id, $test_data->user1->id);
    }

    public function test_expand_single_assignment_based_on_job_assignment(): void {
        set_config('totara_job_allowmultiplejobs', 1);
        $test_data = $this->prepare_assignments(track_entity::SUBJECT_INSTANCE_GENERATION_ONE_PER_JOB);

        $track1_id = $test_data->track1->id;

        $job_assignment_id = builder::table('job_assignment')->insert([
            'userid' => $test_data->user1->id,
            'timecreated' => time(),
            'timemodified' => time(),
            'usermodified' => time(),
            'positionassignmentdate' => true,
            'sortorder' => 0,
        ]);

        $this->assert_track_has_no_user_assignments($track1_id, $test_data->user1->id);

        // No user added to cohort so nothing should happen
        (new expand_task())->expand_single($test_data->assignment1->id);

        $this->assert_track_has_no_user_assignments($track1_id, $test_data->user1->id);

        // Add the users to the cohort
        $this->add_user_to_cohort($test_data->cohort1->id, $test_data->user1->id);

        // This should now result in a user assignment
        (new expand_task())->expand_single($test_data->assignment1->id);

        $this->assert_track_has_user_assignments($track1_id, $test_data->user1->id, false, $job_assignment_id);
        // The other user is not in a cohort yet
        $this->assert_track_has_no_user_assignments($track1_id, $test_data->user2->id);

        // Now add the other one
        $this->add_user_to_cohort($test_data->cohort2->id, $test_data->user2->id);

        (new expand_task())->expand_single($test_data->assignment1->id);

        // Should have been expanded
        $this->assert_track_has_user_assignments($track1_id, $test_data->user1->id, false, $job_assignment_id);
    }

    public function test_job_assignment_is_not_used_without_multiple_job_assignments_enabled(): void {
        set_config('totara_job_allowmultiplejobs', 0);
        $test_data = $this->prepare_assignments(track_entity::SUBJECT_INSTANCE_GENERATION_ONE_PER_JOB);

        $track1_id = $test_data->track1->id;

        $job_assignment_id = builder::table('job_assignment')->insert([
            'userid' => $test_data->user1->id,
            'timecreated' => time(),
            'timemodified' => time(),
            'usermodified' => time(),
            'positionassignmentdate' => true,
            'sortorder' => 0,
        ]);

        $this->assert_track_has_no_user_assignments($track1_id, $test_data->user1->id);

        // No user added to cohort so nothing should happen
        (new expand_task())->expand_single($test_data->assignment1->id);

        // Add the users to the cohort
        $this->add_user_to_cohort($test_data->cohort1->id, $test_data->user1->id);

        // Now add the other one
        $this->add_user_to_cohort($test_data->cohort2->id, $test_data->user2->id);

        (new expand_task())->expand_single($test_data->assignment1->id);

        // Should have been expanded, but not with a job assignment id
        $this->assert_track_has_user_assignments($track1_id, $test_data->user1->id, false, null);
    }

    public function test_expand_single_assignment_based_on_job_with_no_job_assignments(): void {
        set_config('totara_job_allowmultiplejobs', 1);
        $test_data = $this->prepare_assignments(track_entity::SUBJECT_INSTANCE_GENERATION_ONE_PER_JOB);

        $track1_id = $test_data->track1->id;

        $this->assert_track_has_no_user_assignments($track1_id, $test_data->user1->id);

        // Add the users to the cohort
        $this->add_user_to_cohort($test_data->cohort1->id, $test_data->user1->id);

        // Now add the other one
        $this->add_user_to_cohort($test_data->cohort2->id, $test_data->user2->id);

        (new expand_task())->expand_single($test_data->assignment1->id);

        // Neither user should have been expanded
        $this->assert_track_has_no_user_assignments($track1_id, $test_data->user1->id);
        $this->assert_track_has_no_user_assignments($track1_id, $test_data->user2->id);
    }

    public function test_expand_single_assignment_based_on_multiple_job_assignment(): void {
        set_config('totara_job_allowmultiplejobs', 1);
        $test_data = $this->prepare_assignments(track_entity::SUBJECT_INSTANCE_GENERATION_ONE_PER_JOB);

        $track1_id = $test_data->track1->id;

        $job_assignment_id1 = builder::table('job_assignment')->insert([
            'userid' => $test_data->user1->id,
            'timecreated' => time(),
            'timemodified' => time(),
            'usermodified' => time(),
            'positionassignmentdate' => true,
            'sortorder' => 0,
            'idnumber' => 1,
        ]);

        $job_assignment_id2 = builder::table('job_assignment')->insert([
            'userid' => $test_data->user1->id,
            'timecreated' => time(),
            'timemodified' => time(),
            'usermodified' => time(),
            'positionassignmentdate' => true,
            'sortorder' => 1,
            'idnumber' => 2,
        ]);

        $this->assert_track_has_no_user_assignments($track1_id, $test_data->user1->id);

        // No user added to cohort so nothing should happen
        (new expand_task())->expand_single($test_data->assignment1->id);

        $this->assert_track_has_no_user_assignments($track1_id, $test_data->user1->id);

        // Add the users to the cohort
        $this->add_user_to_cohort($test_data->cohort1->id, $test_data->user1->id);

        // This should now result in a user assignment
        (new expand_task())->expand_single($test_data->assignment1->id);

        $this->assert_track_has_user_assignments($track1_id, $test_data->user1->id, false, $job_assignment_id1);
        $this->assert_track_has_user_assignments($track1_id, $test_data->user1->id, false, $job_assignment_id2);
        // The other user is not in a cohort yet
        $this->assert_track_has_no_user_assignments($track1_id, $test_data->user2->id);

        // Now add the other one
        $this->add_user_to_cohort($test_data->cohort2->id, $test_data->user2->id);

        (new expand_task())->expand_single($test_data->assignment1->id);

        // Should have been expanded
        $this->assert_track_has_user_assignments($track1_id, $test_data->user1->id, false, $job_assignment_id1);
        $this->assert_track_has_user_assignments($track1_id, $test_data->user1->id, false, $job_assignment_id2);
    }

    public function test_user_gets_unassigned(): void {
        $test_data = $this->prepare_assignments();

        $track1_id = $test_data->track1->id;

        $this->assert_track_has_no_user_assignments($track1_id, $test_data->user1->id);

        // Add the users to the cohort
        $this->add_user_to_cohort($test_data->cohort1->id, $test_data->user1->id);

        // This should now result in a user assignment
        (new expand_task())->expand_single($test_data->assignment1->id);

        $this->assert_track_has_user_assignments($track1_id, $test_data->user1->id, false);

        $this->remove_user_from_cohort($test_data->cohort1->id, $test_data->user1->id);

        (new expand_task())->expand_single($test_data->assignment1->id);

        // User is now marked as deleted
        $this->assert_track_has_user_assignments($track1_id, $test_data->user1->id, true);
    }

    public function test_user_assignment_gets_swapped_when_changing_generation_method(): void {
        set_config('totara_job_allowmultiplejobs', 1);
        $test_data = $this->prepare_assignments(track_entity::SUBJECT_INSTANCE_GENERATION_ONE_PER_JOB);

        $track1_id = $test_data->track1->id;

        $job_assignment_id1 = builder::table('job_assignment')->insert([
            'userid' => $test_data->user1->id,
            'timecreated' => time(),
            'timemodified' => time(),
            'usermodified' => time(),
            'positionassignmentdate' => true,
            'sortorder' => 0,
            'idnumber' => 1,
        ]);

        $this->assert_track_has_no_user_assignments($track1_id, $test_data->user1->id, false, $job_assignment_id1);
        $this->assert_track_has_no_user_assignments($track1_id, $test_data->user1->id, false, null);

        // Add the users to the cohort
        $this->add_user_to_cohort($test_data->cohort1->id, $test_data->user1->id);

        // This should now result in a user assignment
        (new expand_task())->expand_single($test_data->assignment1->id);

        $this->assert_track_has_user_assignments($track1_id, $test_data->user1->id, false, $job_assignment_id1);
        $this->assert_track_has_no_user_assignments($track1_id, $test_data->user1->id, false, null);

        // Change the track to per subject generation
        $track1 = new track_entity($track1_id);
        $track1->subject_instance_generation = track_entity::SUBJECT_INSTANCE_GENERATION_ONE_PER_SUBJECT;
        $track1->save();

        // Trigger re-expansion
        $test_data->assignment1->expand = true;
        $test_data->assignment1->save();

        (new expand_task())->expand_single($test_data->assignment1->id);

        // Assignment should now be swapped to be based on job assignment
        $this->assert_track_has_user_assignments($track1_id, $test_data->user1->id, false, null);
        $this->assert_track_has_no_user_assignments($track1_id, $test_data->user1->id, false, $job_assignment_id1);

        // Change the track BACK to per job generation
        $track1->subject_instance_generation = track_entity::SUBJECT_INSTANCE_GENERATION_ONE_PER_JOB;
        $track1->save();

        // Trigger re-expansion
        $test_data->assignment1->expand = true;
        $test_data->assignment1->save();

        (new expand_task())->expand_single($test_data->assignment1->id);

        // Assignment should now be swapped to be based on job assignment
        $this->assert_track_has_user_assignments($track1_id, $test_data->user1->id, false, $job_assignment_id1);
        $this->assert_track_has_no_user_assignments($track1_id, $test_data->user1->id, false, null);
    }

    public function test_user_gets_unassigned_based_on_job_assignment(): void {
        set_config('totara_job_allowmultiplejobs', 1);
        $test_data = $this->prepare_assignments(track_entity::SUBJECT_INSTANCE_GENERATION_ONE_PER_JOB);

        $track1_id = $test_data->track1->id;

        $job_assignment_id1 = builder::table('job_assignment')->insert([
            'userid' => $test_data->user1->id,
            'timecreated' => time(),
            'timemodified' => time(),
            'usermodified' => time(),
            'positionassignmentdate' => true,
            'sortorder' => 0,
            'idnumber' => 1,
        ]);

        $this->assert_track_has_no_user_assignments($track1_id, $test_data->user1->id);

        // Add the users to the cohort
        $this->add_user_to_cohort($test_data->cohort1->id, $test_data->user1->id);

        // This should now result in a user assignment
        (new expand_task())->expand_single($test_data->assignment1->id);

        $this->assert_track_has_user_assignments($track1_id, $test_data->user1->id, false, $job_assignment_id1);

        $this->remove_user_from_cohort($test_data->cohort1->id, $test_data->user1->id);

        (new expand_task())->expand_single($test_data->assignment1->id);

        // User is now marked as deleted
        $this->assert_track_has_user_assignments($track1_id, $test_data->user1->id, true, $job_assignment_id1);
    }

    public function test_events_are_fired(): void {
        $test_data = $this->prepare_assignments();

        $track1_id = $test_data->track1->id;

        // Add the users to the cohort
        $this->add_user_to_cohort($test_data->cohort1->id, $test_data->user1->id);
        $this->add_user_to_cohort($test_data->cohort1->id, $test_data->user2->id);

        $sink = $this->redirectEvents();

        // This should now result in a user assignment
        (new expand_task())->expand_single($test_data->assignment1->id);

        $events = $sink->get_events();
        $sink->close();

        $this->assertCount(1, $events);
        $this->assertContainsOnlyInstancesOf(track_user_assigned_bulk::class, $events);
        /** @var track_user_assigned_bulk $event */
        $event = $events[0];

        $this->assertEquals($track1_id, $event->objectid);
        $this->assertEqualsCanonicalizing([$test_data->user1->id, $test_data->user2->id], $event->get_user_ids());

        $this->remove_user_from_cohort($test_data->cohort1->id, $test_data->user1->id);
        $this->remove_user_from_cohort($test_data->cohort1->id, $test_data->user2->id);

        $sink = $this->redirectEvents();

        (new expand_task())->expand_single($test_data->assignment1->id);

        $events = $sink->get_events();
        $sink->close();

        $this->assertCount(2, $events);
        $this->assertContainsOnlyInstancesOf(track_user_unassigned::class, $events);
        $this->assertEquals([$track1_id, $track1_id], array_column($events, 'objectid'));
        $this->assertEqualsCanonicalizing(
            [$test_data->user1->id, $test_data->user2->id],
            array_map(function (track_user_unassigned $event) {
                return $event->get_user_id();
            }, $events)
        );
    }

    public function test_user_assignment_gets_reactivated(): void {
        $test_data = $this->prepare_assignments();

        $track1_id = $test_data->track1->id;

        $this->assert_track_has_no_user_assignments($track1_id, $test_data->user1->id);

        // Add the users to the cohort
        $this->add_user_to_cohort($test_data->cohort1->id, $test_data->user1->id);

        // This should now result in a user assignment
        (new expand_task())->expand_single($test_data->assignment1->id);

        $this->remove_user_from_cohort($test_data->cohort1->id, $test_data->user1->id);

        (new expand_task())->expand_single($test_data->assignment1->id);

        // User is now marked as deleted
        $this->assert_track_has_user_assignments($track1_id, $test_data->user1->id, true);

        // Readd to group
        $this->add_user_to_cohort($test_data->cohort1->id, $test_data->user1->id);

        (new expand_task())->expand_single($test_data->assignment1->id);

        $this->assert_track_has_user_assignments($track1_id, $test_data->user1->id, false);
    }

    public function test_user_assignment_gets_reactivated_based_on_job_assignment(): void {
        set_config('totara_job_allowmultiplejobs', 1);
        $test_data = $this->prepare_assignments(track_entity::SUBJECT_INSTANCE_GENERATION_ONE_PER_JOB);

        $track1_id = $test_data->track1->id;

        $job_assignment_id1 = builder::table('job_assignment')->insert([
            'userid' => $test_data->user1->id,
            'timecreated' => time(),
            'timemodified' => time(),
            'usermodified' => time(),
            'positionassignmentdate' => true,
            'sortorder' => 0,
            'idnumber' => 1,
        ]);

        $this->assert_track_has_no_user_assignments($track1_id, $test_data->user1->id, false, $job_assignment_id1);

        // Add the users to the cohort
        $this->add_user_to_cohort($test_data->cohort1->id, $test_data->user1->id);

        // This should now result in a user assignment
        (new expand_task())->expand_single($test_data->assignment1->id);

        $this->remove_user_from_cohort($test_data->cohort1->id, $test_data->user1->id);

        (new expand_task())->expand_single($test_data->assignment1->id);

        // User is now marked as deleted
        $this->assert_track_has_user_assignments($track1_id, $test_data->user1->id, true, $job_assignment_id1);

        // Readd to group
        $this->add_user_to_cohort($test_data->cohort1->id, $test_data->user1->id);

        (new expand_task())->expand_single($test_data->assignment1->id);

        $this->assert_track_has_user_assignments($track1_id, $test_data->user1->id, false, $job_assignment_id1);
    }

    public function test_reactivated_user_assignment_gets_period_updated(): void {
        $test_data = $this->prepare_assignments();
        $track_id = $test_data->track1->id;
        $user_id = $test_data->user1->id;

        $now = time();
        $test_data->track1->set_schedule_open_fixed($now);
        $test_data->track1->update();

        // Add user to the cohort - we expect a user assignment with period according to track schedule settings.
        $this->add_user_to_cohort($test_data->cohort1->id, $user_id);
        (new expand_task())->expand_single($test_data->assignment1->id);
        $this->assert_user_assignment_period($track_id, $user_id, $now, null);

        // Remove user from cohort - period values should not be affected
        $this->remove_user_from_cohort($test_data->cohort1->id, $user_id);
        (new expand_task())->expand_single($test_data->assignment1->id);
        $this->assert_user_assignment_period($track_id, $user_id, $now, null);

        // User assignment is now marked as deleted
        $this->assert_track_has_user_assignments($track_id, $user_id, true);

        // Change the schedule for the track
        $tomorrow = time() + 86400;
        $yesterday = time() - 86400;
        $test_data->track1->set_schedule_closed_fixed($yesterday, $tomorrow);
        $test_data->track1->update();

        // Re-add user to cohort - we expect the reactivated user assignment gets the updated period settings
        $this->add_user_to_cohort($test_data->cohort1->id, $user_id);
        (new expand_task())->expand_single($test_data->assignment1->id);
        $this->assert_user_assignment_period($track_id, $user_id, $yesterday, $tomorrow);
    }

    private function assert_user_assignment_period(int $track_id, int $user_id, ?int $start, ?int $end): void {
        /** @var track_user_assignment $user_assignment */
        $user_assignment = track_user_assignment::repository()
            ->where('track_id', $track_id)
            ->where('subject_user_id', $user_id)
            ->one();
        $this->assertEquals($start, $user_assignment->period_start_date);
        $this->assertEquals($end, $user_assignment->period_end_date);
    }

    public function test_user_assignment_is_only_created_once(): void {
        $test_data = $this->prepare_assignments();

        $track1_id = $test_data->track1->id;

        // Add the user to two cohorts
        $this->add_user_to_cohort($test_data->cohort1->id, $test_data->user1->id);
        $this->add_user_to_cohort($test_data->cohort2->id, $test_data->user1->id);

        // This should now result in only one user assignment even if the user is in two cohorts
        (new expand_task())->expand_multiple([$test_data->assignment1->id, $test_data->assignment2->id]);

        $this->assert_track_has_user_assignments($track1_id, $test_data->user1->id, false);
    }

    public function test_assign_multiple(): void {
        $test_data = $this->prepare_assignments();

        $track1_id = $test_data->track1->id;

        // Add the user to two cohorts
        $this->add_user_to_cohort($test_data->cohort1->id, $test_data->user1->id);
        $this->add_user_to_cohort($test_data->cohort2->id, $test_data->user2->id);

        (new expand_task())->expand_multiple([$test_data->assignment1->id, $test_data->assignment2->id]);

        $this->assert_track_has_user_assignments($track1_id, $test_data->user1->id, false);
        $this->assert_track_has_user_assignments($track1_id, $test_data->user2->id, false);
    }

    public function test_assign_all(): void {
        $test_data = $this->prepare_assignments();

        $track1_id = $test_data->track1->id;

        // Add the user to two cohorts
        $this->add_user_to_cohort($test_data->cohort1->id, $test_data->user1->id);
        $this->add_user_to_cohort($test_data->cohort2->id, $test_data->user2->id);

        (new expand_task())->expand_all();

        $this->assert_track_has_user_assignments($track1_id, $test_data->user1->id, false);
        $this->assert_track_has_user_assignments($track1_id, $test_data->user2->id, false);
    }

    public function test_assignments_of_draft_activity_are_not_expanded(): void {
        $test_data = $this->prepare_assignments();

        /** @var activity_model $activity */
        $activity = $test_data->activity1;

        /** @var activity $activity_entity */
        $activity_entity = activity::repository()->find($activity->get_id());
        $activity_entity->status = draft::get_code();
        $activity_entity->save();

        $track1_id = $test_data->track1->id;

        // Add the user to two cohorts
        $this->add_user_to_cohort($test_data->cohort1->id, $test_data->user1->id);
        $this->add_user_to_cohort($test_data->cohort2->id, $test_data->user2->id);

        (new expand_task())->expand_all();

        $this->assert_track_has_no_user_assignments($track1_id, $test_data->user1->id, false);
        $this->assert_track_has_no_user_assignments($track1_id, $test_data->user2->id, false);
    }

    public function test_assignments_of_paused_track_are_not_expanded(): void {
        $test_data = $this->prepare_assignments();

        $track1_id = $test_data->track1->id;

        /** @var track_entity $track */
        $track = track_entity::repository()->find($track1_id);
        $track->status = track_status::PAUSED;
        $track->save();

        // Add the user to two cohorts
        $this->add_user_to_cohort($test_data->cohort1->id, $test_data->user1->id);
        $this->add_user_to_cohort($test_data->cohort2->id, $test_data->user2->id);

        (new expand_task())->expand_all();

        $this->assert_track_has_no_user_assignments($track1_id, $test_data->user1->id, false);
        $this->assert_track_has_no_user_assignments($track1_id, $test_data->user2->id, false);
    }

    public function test_fixed_assignment_period_dates_are_populated(): void {
        $test_data = $this->prepare_assignments();

        $track1_id = $test_data->track1->id;

        /** @var track_entity $track */
        $track = track_entity::repository()->find($track1_id);
        $start_time = time() - 1000;
        $end_time = time() + 1000;
        $track->schedule_is_open = false;
        $track->schedule_is_fixed = true;
        $track->schedule_fixed_from = $start_time;
        $track->schedule_fixed_to = $end_time;
        $track->save();

        $this->add_user_to_cohort($test_data->cohort1->id, $test_data->user1->id);

        (new expand_task())->expand_all();

        /** @var track_user_assignment $track_user_assignment */
        $track_user_assignment = track_user_assignment::repository()
            ->where('track_id', $track1_id)
            ->where('subject_user_id', $test_data->user1->id)
            ->one();
        $this->assertEquals($start_time, $track_user_assignment->period_start_date);
        $this->assertEquals($end_time, $track_user_assignment->period_end_date);

        // Change to open_fixed and expand for a new user.
        $track->schedule_is_open = true;
        $track->schedule_is_fixed = true;
        $track->save();

        $this->add_user_to_cohort($test_data->cohort1->id, $test_data->user2->id);

        (new expand_task())->expand_all();
        /** @var track_user_assignment $track_user_assignment */
        $track_user_assignment = track_user_assignment::repository()
            ->where('track_id', $track1_id)
            ->where('subject_user_id', $test_data->user2->id)
            ->one();
        $this->assertEquals($start_time, $track_user_assignment->period_start_date);
        $this->assertEquals(null, $track_user_assignment->period_end_date);
    }

    public function test_dynamic_assignment_period_dates_are_populated(): void {
        $test_data = $this->prepare_assignments();

        $track1_id = $test_data->track1->id;

        $dynamic_source = (new user_creation_date())->get_options()->first();

        /** @var track_entity $track */
        $track = track_entity::repository()->find($track1_id);
        $track->schedule_is_open = false;
        $track->schedule_is_fixed = false;
        $track->schedule_dynamic_direction = track_entity::SCHEDULE_DYNAMIC_DIRECTION_BEFORE;
        $track->schedule_dynamic_source = $dynamic_source;
        $track->schedule_dynamic_count_from = 7;
        $track->schedule_dynamic_count_to = 0;
        $track->schedule_dynamic_unit = track_entity::SCHEDULE_DYNAMIC_UNIT_DAY;
        $track->save();

        $create_date = 1589932800;
        $seven_days_before_create_date = 1589328000;

        // Set the users created date.
        $user1 = new user($test_data->user1->id);
        $user1->timecreated = $create_date;
        $user1->save();

        $this->add_user_to_cohort($test_data->cohort1->id, $test_data->user1->id);

        (new expand_task())->expand_all();

        /** @var track_user_assignment $track_user_assignment */
        $track_user_assignment = track_user_assignment::repository()
            ->where('track_id', $track1_id)
            ->where('subject_user_id', $test_data->user1->id)
            ->one();

        $this->assertEquals($seven_days_before_create_date, $track_user_assignment->period_start_date);
        $this->assertEquals($create_date, $track_user_assignment->period_end_date);

        // Change to open_fixed and expand for a new user.
        $track->schedule_is_open = true;
        $track->schedule_is_fixed = false;
        $track->save();

        // Set the users created date.
        $user2 = new user($test_data->user2->id);
        $user2->timecreated = $create_date;
        $user2->save();

        $this->add_user_to_cohort($test_data->cohort1->id, $test_data->user2->id);

        (new expand_task())->expand_all();
        /** @var track_user_assignment $track_user_assignment */
        $track_user_assignment = track_user_assignment::repository()
            ->where('track_id', $track1_id)
            ->where('subject_user_id', $test_data->user2->id)
            ->one();

        $this->assertEquals($seven_days_before_create_date, $track_user_assignment->period_start_date);
        $this->assertEquals(null, $track_user_assignment->period_end_date);
    }

    public function test_null_dynamic_assignment_period_dates_are_populated(): void {
        $test_data = $this->prepare_assignments();

        // No user_info_data is inserted, this is how we get the null data.
        builder::get_db()->insert_record(
            'user_info_field',
            (object)['shortname' => 'datetime-custom-field-1', 'name' => 'time 2', 'categoryid' => 1, 'datatype' => 'datetime']
        );

        $track1_id = $test_data->track1->id;

        $dynamic_source = (new user_custom_field())->get_options()->find(function (dynamic_source $source) {
            return $source->get_option_key() === 'datetime-custom-field-1';
        });

        /** @var track_entity $track */
        $track = track_entity::repository()->find($track1_id);
        $track->schedule_is_open = false;
        $track->schedule_is_fixed = false;
        $track->schedule_dynamic_source = $dynamic_source;
        $track->schedule_dynamic_count_from = 1;
        $track->schedule_dynamic_count_to = 2;
        $track->schedule_dynamic_unit = track_entity::SCHEDULE_DYNAMIC_UNIT_DAY;
        $track->schedule_dynamic_direction = track_entity::SCHEDULE_DYNAMIC_DIRECTION_AFTER;
        $track->save();

        $this->add_user_to_cohort($test_data->cohort1->id, $test_data->user1->id);

        (new expand_task())->expand_all();

        /** @var track_user_assignment $track_user_assignment */
        $track_user_assignment = track_user_assignment::repository()
            ->where('track_id', $track1_id)
            ->where('subject_user_id', $test_data->user1->id)
            ->one();

        $this->assertNull($track_user_assignment->period_start_date);
        $this->assertNull($track_user_assignment->period_end_date);

        // Change to open_fixed and expand for a new user.
        $track->schedule_is_open = true;
        $track->schedule_is_fixed = false;
        $track->save();

        $this->add_user_to_cohort($test_data->cohort1->id, $test_data->user2->id);

        (new expand_task())->expand_all();
        /** @var track_user_assignment $track_user_assignment */
        $track_user_assignment = track_user_assignment::repository()
            ->where('track_id', $track1_id)
            ->where('subject_user_id', $test_data->user2->id)
            ->one();

        $this->assertNull($track_user_assignment->period_start_date);
        $this->assertNull($track_user_assignment->period_end_date);
    }

    /**
     * Assert that the track does have any assignments, optionally can check individual user
     *
     * @param int $track_id
     * @param int|null $user_id
     * @param bool|null $deleted
     * @param null $job_assignment_id
     */
    private function assert_track_has_user_assignments(
        int $track_id,
        ?int $user_id = null,
        ?bool $deleted = null,
        $job_assignment_id = null
    ): void {
        $this->assertTrue(
            $this->track_has_user_assignments($track_id, $user_id, $deleted, $job_assignment_id),
            'Track should have user assignments'
        );
    }

    /**
     * Assert that the track does not have any assignments, optionally can check individual user
     *
     * @param int $track_id
     * @param int|null $user_id
     * @param bool|null $deleted
     * @param null $job_assignment_id
     */
    private function assert_track_has_no_user_assignments(
        int $track_id,
        ?int $user_id = null,
        ?bool $deleted = null,
        $job_assignment_id = null
    ): void {
        $this->assertFalse(
            $this->track_has_user_assignments($track_id, $user_id, $deleted, $job_assignment_id),
            'Track should not have user assignments'
        );
    }

    /**
     * Does the track and (optional) user combination have any assignments?
     *
     * @param int $track_id
     * @param int|null $user_id
     * @param bool|null $deleted
     * @param null $job_assignment_id
     * @return bool
     */
    private function track_has_user_assignments(
        int $track_id,
        ?int $user_id = null,
        ?bool $deleted = null,
        $job_assignment_id = null
    ): bool {
        $repo = track_user_assignment::repository()
            ->where('track_id', $track_id)
            ->when($user_id, function (entity_repository $repository) use ($user_id, $job_assignment_id) {
                $repository->where('subject_user_id', $user_id);
                $repository->where('job_assignment_id', $job_assignment_id);
            })
            ->when($deleted !== null, function (entity_repository $repository) use ($deleted) {
                $repository->where('deleted', $deleted);
            });

        // There can only be one assignment per track and user
        if ($user_id) {
            $result = $repo->one();
            return !empty($result);
        }

        // Otherwise there could be multiple
        return $repo->exists();
    }

    private function add_user_to_cohort(int $cohort_id, int $user_id): void {
        cohort_add_member($cohort_id, $user_id);
    }

    private function remove_user_from_cohort(int $cohort_id, int $user_id): void {
        cohort_remove_member($cohort_id, $user_id);
    }

    private function prepare_assignments(
        $subject_instance_generation = track_entity::SUBJECT_INSTANCE_GENERATION_ONE_PER_SUBJECT
    ) {
        $test_data = new class() {
            public $user1;
            public $user2;
            public $cohort1;
            public $cohort2;
            public $activity1;

            /** @var $track1 track */
            public $track1;

            /** @var $assignment1 track_assignment */
            public $assignment1;

            /** @var $assignment1 track_assignment */
            public $assignment2;
        };

        $test_data->user1 = $this->generator()->create_user();
        $test_data->user2 = $this->generator()->create_user();
        $test_data->cohort1 = $this->generator()->create_cohort();
        $test_data->cohort2 = $this->generator()->create_cohort();

        /** @var mod_perform_generator $perform_generator */
        $perform_generator = $this->getDataGenerator()->get_plugin_generator('mod_perform');

        $test_data->activity1 = $perform_generator->create_activity_in_container();

        $test_data->track1 = $perform_generator
            ->create_activity_tracks($test_data->activity1)
            ->first();

        $track1 = new track_entity($test_data->track1->id);
        $track1->subject_instance_generation = $subject_instance_generation;
        $track1->save();

        $test_data->assignment1 = new track_assignment([
            'track_id' => $test_data->track1->id,
            'type' => track_assignment_type::ADMIN,
            'user_group_type' => grouping::COHORT,
            'user_group_id' => $test_data->cohort1->id,
            'created_by' => 0,
            'expand' => true,
        ]);
        $test_data->assignment1->save();

        $test_data->assignment2 = new track_assignment([
            'track_id' => $test_data->track1->id,
            'type' => track_assignment_type::ADMIN,
            'user_group_type' => grouping::COHORT,
            'user_group_id' => $test_data->cohort2->id,
            'created_by' => 0,
            'expand' => false
        ]);
        $test_data->assignment2->save();

        return $test_data;
    }

}
