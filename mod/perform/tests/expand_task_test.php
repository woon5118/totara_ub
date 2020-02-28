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

use core\orm\entity\repository as entity_repository;
use mod_perform\entities\activity\activity;
use mod_perform\entities\activity\track as track_entity;
use mod_perform\entities\activity\track_assignment;
use mod_perform\entities\activity\track_user_assignment;
use mod_perform\event\track_user_assigned_bulk;
use mod_perform\event\track_user_unassigned;
use mod_perform\expand_task;
use mod_perform\models\activity\activity as activity_model;
use mod_perform\models\activity\track_assignment_type;
use mod_perform\models\activity\track_status;
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
        require_once($CFG->dirroot.'/cohort/lib.php');
    }

    /**
     * Date generator shortcut
     *
     * @return testing_data_generator
     */
    protected function generator() {
        return self::getDataGenerator();
    }

    protected function setUp() {
        parent::setUp();
        $this->setAdminUser();
    }

    protected function tearDown() {
        parent::tearDown();
    }

    public function test_expand_single_assignment() {
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

    public function test_user_gets_unassigned() {
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

    public function test_events_are_fired() {
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
            array_map(function ($event) {
                return $event->get_user_id();
            }, $events)
        );
    }

    public function test_user_assignment_gets_reactivated() {
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

    public function test_user_assignment_is_only_created_once() {
        $test_data = $this->prepare_assignments();

        $track1_id = $test_data->track1->id;

        // Add the user to two cohorts
        $this->add_user_to_cohort($test_data->cohort1->id, $test_data->user1->id);
        $this->add_user_to_cohort($test_data->cohort2->id, $test_data->user1->id);

        // This should now result in only one user assignment even if the user is in two cohorts
        (new expand_task())->expand_multiple([$test_data->assignment1->id, $test_data->assignment2->id]);

        $this->assert_track_has_user_assignments($track1_id, $test_data->user1->id, false);
    }

    public function test_assign_multiple() {
        $test_data = $this->prepare_assignments();

        $track1_id = $test_data->track1->id;

        // Add the user to two cohorts
        $this->add_user_to_cohort($test_data->cohort1->id, $test_data->user1->id);
        $this->add_user_to_cohort($test_data->cohort2->id, $test_data->user2->id);

        (new expand_task())->expand_multiple([$test_data->assignment1->id, $test_data->assignment2->id]);

        $this->assert_track_has_user_assignments($track1_id, $test_data->user1->id, false);
        $this->assert_track_has_user_assignments($track1_id, $test_data->user2->id, false);
    }

    public function test_assign_all() {
        $test_data = $this->prepare_assignments();

        $track1_id = $test_data->track1->id;

        // Add the user to two cohorts
        $this->add_user_to_cohort($test_data->cohort1->id, $test_data->user1->id);
        $this->add_user_to_cohort($test_data->cohort2->id, $test_data->user2->id);

        (new expand_task())->expand_all();

        $this->assert_track_has_user_assignments($track1_id, $test_data->user1->id, false);
        $this->assert_track_has_user_assignments($track1_id, $test_data->user2->id, false);
    }

    public function test_assignments_of_draft_activity_are_not_expanded() {
        $test_data = $this->prepare_assignments();

        /** @var activity_model $activity */
        $activity = $test_data->activity1;

        /** @var activity $activity_entity */
        $activity_entity = activity::repository()->find($activity->get_id());
        $activity_entity->status = activity_model::STATUS_INACTIVE;
        $activity_entity->save();

        $track1_id = $test_data->track1->id;

        // Add the user to two cohorts
        $this->add_user_to_cohort($test_data->cohort1->id, $test_data->user1->id);
        $this->add_user_to_cohort($test_data->cohort2->id, $test_data->user2->id);

        (new expand_task())->expand_all();

        $this->assert_track_has_no_user_assignments($track1_id, $test_data->user1->id, false);
        $this->assert_track_has_no_user_assignments($track1_id, $test_data->user2->id, false);
    }

    public function test_assignments_of_paused_track_are_not_expanded() {
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

    /**
     * Assert that the track does have any assignments, optionally can check individual user
     *
     * @param int $track_id
     * @param int|null $user_id
     * @param bool|null $deleted
     */
    private function assert_track_has_user_assignments(int $track_id, ?int $user_id = null, ?bool $deleted = null) {
        $this->assertTrue(
            $this->track_has_user_assignments($track_id, $user_id, $deleted),
            'Track should have user assignments'
        );
    }

    /**
     * Assert that the track does not have any assignments, optionally can check individual user
     *
     * @param int $track_id
     * @param int|null $user_id
     * @param bool|null $deleted
     */
    private function assert_track_has_no_user_assignments(int $track_id, ?int $user_id = null, ?bool $deleted = null) {
        $this->assertFalse(
            $this->track_has_user_assignments($track_id, $user_id, $deleted),
            'Track should not have user assignments'
        );
    }

    /**
     * Does the track and (optional) user combination has any assignments?
     *
     * @param int $track_id
     * @param int|null $user_id
     * @param bool|null $deleted
     * @return bool
     */
    private function track_has_user_assignments(int $track_id, ?int $user_id = null, ?bool $deleted = null): bool {
        $repo = track_user_assignment::repository()
            ->where('track_id', $track_id)
            ->when($user_id, function (entity_repository $repository) use ($user_id) {
                $repository->where('subject_user_id', $user_id);
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

    private function add_user_to_cohort(int $cohort_id, int $user_id) {
        cohort_add_member($cohort_id, $user_id);
    }

    private function remove_user_from_cohort(int $cohort_id, int $user_id) {
        cohort_remove_member($cohort_id, $user_id);
    }

    private function prepare_assignments() {
        /** @var totara_hierarchy_generator $hierarchy_generator */
        $hierarchy_generator = $this->generator()->get_plugin_generator('totara_hierarchy');

        $test_data = new class() {
            public $user1;
            public $user2;
            public $cohort1;
            public $cohort2;
            public $activity1;
            public $track1;
            public $assignment1;
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

        $test_data->assignment1 = new track_assignment([
            'track_id' => $test_data->track1->id,
            'type' => track_assignment_type::ADMIN,
            'user_group_type' => grouping::COHORT,
            'user_group_id' => $test_data->cohort1->id,
            'created_by' => 0,
            'expand' => true
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