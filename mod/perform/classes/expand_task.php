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
 */

namespace mod_perform;

use core\entities\expandable;
use core\orm\collection;
use core\orm\entity\entity;
use core\orm\query\builder;
use mod_perform\models\activity\track;
use mod_perform\entities\activity\track_assignment;
use mod_perform\entities\activity\track_assignment_repository;
use mod_perform\entities\activity\track_user_assignment;
use mod_perform\entities\activity\track_user_assignment_via;
use mod_perform\event\track_user_assigned_bulk;
use mod_perform\event\track_user_unassigned;
use mod_perform\task\expand_task\assignment_parameters;
use mod_perform\task\expand_task\assignment_parameters_collection;
use mod_perform\user_groups\grouping;

class expand_task {

    private $cache = [];

    private $assign_buffer = [];

    /**
     * Expand all active assignments
     */
    public function expand_all() {
        $assignments = $this->get_repository()->get();
        foreach ($assignments as $assignment) {
            $this->expand_assignment($assignment);
        }

        $this->delete_orphaned_user_assignments();
    }

    /**
     * Expand only given assignments. Non existing or inactive assignments will be ignored.
     *
     * @param array $assignment_ids
     */
    public function expand_multiple(array $assignment_ids) {
        $assignment_ids = $this->sanitise_ids($assignment_ids);
        if (!empty($assignment_ids)) {
            $assignments = $this->get_repository()
                ->filter_by_ids($assignment_ids)
                ->get();

            foreach ($assignments as $assignment) {
                $this->expand_assignment($assignment);
            }
        }

        $this->delete_orphaned_user_assignments();
    }

    /**
     * Expand a single assignment with the given id. Missing assignment is ignored.
     *
     * @param int $assignment_id
     */
    public function expand_single(int $assignment_id) {
        /** @var track_assignment $assignment */
        $assignment = $this->get_repository()
            ->where('id', $assignment_id)
            ->one();

        if ($assignment) {
            $this->expand_assignment($assignment);
        }

        $this->delete_orphaned_user_assignments();
    }

    private function get_repository(): track_assignment_repository {
        return track_assignment::repository()
            ->filter_by_expand()
            ->filter_by_active_track_and_activity()
            ->with('track');
    }

    /**
     * Expand the given assignment.
     *
     * @param track_assignment $assignment
     */
    private function expand_assignment(track_assignment $assignment): void {
        // We ignore missing assignments to not cause failing tasks.
        // It can happen that an assignment was deleted before the task is run.
        // On the next run the assignments will be picked up again.
        if (!$assignment->exists()) {
            return;
        }

        // Reset the expand flag early as something could change it in between
        $assignment->expand = false;
        $assignment->save();

        // load all current source targets relations of the assignment
        // to avoid more requests to the database when checking if entry
        // already exists.
        $existing_user_assignments = $this->load_current_entries($assignment);

        $expanded_user_ids = $this->get_expanded_users($assignment->user_group_type, $assignment->user_group_id);

        $track = track::load_by_entity($assignment->track);
        $assignment_parameter_collection = $this->get_assignment_parameters_collection($track, $expanded_user_ids);

        $create_assignment_parameters = $assignment_parameter_collection->remove_matching_user_assignments(
            $existing_user_assignments
        );

        if ($create_assignment_parameters->count() > 0) {
            builder::get_db()->transaction(function () use ($expanded_user_ids, $assignment, $create_assignment_parameters) {
                // Get all user assignments of the track to be able to check if we just need to link or actually create
                $existing_track_user_assignments = $this->get_existing_user_assignments_for_track($assignment, $expanded_user_ids);

                foreach ($create_assignment_parameters as $assignment_parameters) {
                    // If there's already a user assignment for the current assignment just link it
                    // otherwise create a new assignment
                    $existing_user_assignment = $existing_track_user_assignments->find(
                        function (track_user_assignment $track_user_assignment) use ($assignment_parameters) {
                            return $assignment_parameters->matches_track_user_assignment($track_user_assignment);
                        }
                    );

                    if ($existing_user_assignment) {
                        $this->link_assignment($existing_user_assignment, $assignment);
                        if ($existing_user_assignment->deleted) {
                            $this->reactivate_user_assignment($existing_user_assignment, new track($assignment->track));
                        }
                    } else {
                        $this->add_to_assign_buffer($assignment, $assignment_parameters);
                    }
                }

                // Make sure that the buffer got flushed
                $this->flush_assign_buffer($assignment);
            });
        }

        // Unlink all records which are not in the current assignment anymore from it
        $this->unlink_users($assignment, $existing_user_assignments, $assignment_parameter_collection);

        // Restore user assignments which previously have been deleted
        $this->reactivate_user_assignments($assignment, $assignment_parameter_collection);
    }

    /**
     * Load all current entries for the given assignment, key uniquely identifies the source target
     *
     * @param track_assignment $assignment
     * @return collection|track_user_assignment[]
     */
    private function load_current_entries(track_assignment $assignment): collection {
        return $assignment->user_assignments;
    }

    /**
     * Expand the user group. We cache the result of the user groups to speed up subsequent assignments for
     * the same user group (but with a different track)
     *
     * @param string $user_group_type one of the grouping enums.
     * @param int $user_group_id
     * @return array
     */
    private function get_expanded_users(int $user_group_type, int $user_group_id): array {
        if (!$expanded_records = $this->get_cache_entry($user_group_type, $user_group_id)) {
            $expanded_records = $this->expand_entity($user_group_type, $user_group_id);
            $this->add_cache_entry($user_group_type, $user_group_id, $expanded_records);
        }
        return $expanded_records;
    }

    /**
     * Get an array with entries consisting of user_id and job_assignment_id.
     *
     * If we are running in per job assignment mode there will be one entry
     * for each users job assignment.
     *
     * If we are running in per user mode there will be an entry for each
     * user, and the job_assignment_id value will be null.
     *
     * @param track $track
     * @param array $users_ids
     * @return assignment_parameters_collection
     */
    private function get_assignment_parameters_collection(
        track $track,
        array $users_ids
    ): assignment_parameters_collection {
        global $CFG;

        if (!empty($CFG->totara_job_allowmultiplejobs) && $track->is_per_job_subject_instance_generation()) {
            $job_assignments = builder::table('job_assignment')
                ->select(['id', 'userid'])
                ->where('userid', $users_ids)
                ->get();

            return assignment_parameters_collection::create_from_job_assignments($job_assignments);
        }

        return assignment_parameters_collection::create_from_user_ids($users_ids);
    }

    /**
     * Add this record to the cache
     *
     * @param string $cache_type
     * @param int $id
     * @param array $record
     */
    private function add_cache_entry(string $cache_type, int $id, array $record) {
        $this->cache[$cache_type][$id] = $record;
    }

    /**
     * Get entry from cache
     *
     * @param string $cache_type
     * @param int $id
     * @return array
     */
    private function get_cache_entry(string $cache_type, int $id): array {
        return $this->cache[$cache_type][$id] ?? [];
    }

    /**
     * Expand the entity, if possible, to individual entries. for example expand a cohort to it's members
     *
     * @param string $type
     * @param int $target_id
     * @return array
     */
    private function expand_entity(string $type, int $target_id): array {
        /** @var entity $class_name */
        $class_name = grouping::get_entity_class_by_user_group_type($type);
        if (is_subclass_of($class_name, expandable::class)) {
            if (!is_subclass_of($class_name, entity::class)) {
                throw new \coding_exception('Currently only entities can be expanded');
            }
            // Try to load the user group entity to make sure it's there
            /** @var expandable $entity */
            $entity = $class_name::repository()->find($target_id);
            if (!$entity) {
                // User group got probably deleted
                // in this case return empty result which will trigger unassigning
                // of all users still assigned to that group
                return [];
            }
            return $entity->expand();
        }
        return [$target_id];
    }

    /**
     * @param track_assignment $assignment
     * @param array $user_ids
     * @return collection|track_user_assignment[]
     */
    private function get_existing_user_assignments_for_track(track_assignment $assignment, array $user_ids): collection {
        return track_user_assignment::repository()
            ->where('track_id', $assignment->track_id)
            ->where('subject_user_id', $user_ids)
            ->get();
    }

    /**
     * Link an user assignment to an existing assignment
     *
     * @param $existing_user_assignment
     * @param track_assignment $assignment
     */
    private function link_assignment($existing_user_assignment, track_assignment $assignment) {
        $via = new track_user_assignment_via();
        $via->track_user_assignment_id = $existing_user_assignment->id;
        $via->track_assignment_id = $assignment->id;
        $via->save();
    }

    /**
     * Link multiple user assignments to an existing assignment
     *
     * @param collection $existing_user_assignments
     * @param track_assignment $assignment
     */
    private function link_assignments(collection $existing_user_assignments, track_assignment $assignment) {
        foreach ($existing_user_assignments as $user_assignment) {
            $this->link_assignment($user_assignment, $assignment);
        }
    }

    /**
     * Add to assign buffer, also flushes the buffer if max amount per buffer is reached
     *
     * @param track_assignment $assignment
     * @param assignment_parameters $assignment_parameters
     */
    private function add_to_assign_buffer(track_assignment $assignment, assignment_parameters $assignment_parameters): void {
        $this->assign_buffer[] = [
            'track_id' => $assignment->track_id,
            'subject_user_id' => $assignment_parameters->get_user_id(),
            'created_at' => time(),
            'deleted' => false,
            'job_assignment_id' => $assignment_parameters->get_job_assignment_id()
        ];

        $count = count($this->assign_buffer);
        // Make sure we inset multiple rows but only to a certain limit
        // to limit memory consumption and prevent leaks
        if ($count >= BATCH_INSERT_MAX_ROW_COUNT) {
            $this->flush_assign_buffer($assignment);
        }
    }

    /**
     * Flush buffer and assign all in bulk (only if buffer count is reached)
     *
     * @param track_assignment $assignment
     */
    private function flush_assign_buffer(track_assignment $assignment) {
        if (!empty($this->assign_buffer)) {
            $this->assign_bulk($assignment, $this->assign_buffer);
            $this->assign_buffer = [];
        }
    }

    /**
     * Create multiple assignments with the least amount of queries
     *
     * @param track_assignment $assignment
     * @param array $to_create
     */
    private function assign_bulk(track_assignment $assignment, array $to_create): void {
        // Bulk fetch all the start and end reference dates.
        $user_ids = array_column($to_create, 'subject_user_id');
        $date_resolver = (new track($assignment->track))->get_date_resolver($user_ids);

        // Add the dates to the assignments.
        foreach ($to_create as $index => $row) {
            $to_create[$index]['period_start_date'] = $date_resolver->get_start_for($row['subject_user_id']);
            $to_create[$index]['period_end_date'] = $date_resolver->get_end_for($row['subject_user_id']);
        }

        // Insert the assignments.
        builder::get_db()->insert_records('perform_track_user_assignment', $to_create);

        // Get all those newly created assignments from the table.
        // We get all user assignments with the same user ids we used above which do not have any links to the assignment yet
        // and link them
        $new_assignments = track_user_assignment::repository()
            ->as('ua')
            ->where('track_id', $assignment->track_id)
            ->where('subject_user_id', array_column($to_create, 'subject_user_id'))
            ->left_join([track_user_assignment_via::TABLE, 'via'], function (builder $builder) use ($assignment) {
                $builder->where_field('ua.id', 'via.track_user_assignment_id')
                    ->where('via.track_assignment_id', $assignment->id);
            })
            ->where('via.id', null)
            ->get();

        $to_link = [];
        foreach ($new_assignments as $new_assignment) {
            $to_link[] = [
                'track_user_assignment_id' => $new_assignment->id,
                'track_assignment_id' => $assignment->id,
                'created_at' => time(),
            ];
        }
        builder::get_db()->insert_records('perform_track_user_assignment_via', $to_link);

        // Trigger an event with all just newly created user assignments
        track_user_assigned_bulk::create_from_user_assignments(
            $assignment->track_id,
            array_column($to_create, 'subject_user_id'),
            $assignment->type
        )->trigger();
    }

    /**
     * Restore user assignments which previously have been deleted
     *
     * @param track_assignment $assignment
     * @param assignment_parameters_collection $assignment_parameter_collection
     */
    private function reactivate_user_assignments(
        track_assignment $assignment,
        assignment_parameters_collection $assignment_parameter_collection
    ): void {
        if ($assignment_parameter_collection->count() === 0) {
            // Nothing to reactivate
            return;
        }

        $user_ids = $assignment_parameter_collection->pluck_user_ids();

        // Load all user assignments which are marked as deleted but
        // do not have any link to the assignment
        $possible_deleted_user_assignments = track_user_assignment::repository()
            ->as('ua')
            ->where('track_id', $assignment->track_id)
            ->where('subject_user_id', $user_ids)
            ->where('deleted', true)
            ->left_join([track_user_assignment_via::TABLE, 'via'], function (builder $builder) use ($assignment) {
                $builder->where_field('ua.id', 'via.track_user_assignment_id')
                    ->where('via.track_assignment_id', $assignment->id);
            })
            ->where('via.id', null)
            ->get();

        // Also need to filter to matching job_assignment_ids too (not just user_id).
        $deleted_user_assignments = $possible_deleted_user_assignments->filter(
            function (track_user_assignment $track_user_assignment) use ($assignment_parameter_collection) {
                return $assignment_parameter_collection->find_from_track_user_assignment($track_user_assignment);
            }
        );

        if ($deleted_user_assignments->count() > 0) {
            // Link them to the assignment and reactivate them
            $this->link_assignments($possible_deleted_user_assignments, $assignment);

            track_user_assignment::repository()
                ->where('deleted', true)
                ->where('id', $possible_deleted_user_assignments->pluck('id'))
                ->update([
                    'deleted' => false,
                    'updated_at' => time()
                ]);

            // Update the user assignment period according to track schedule settings.
            $this->sync_schedule_for_user_assignments($possible_deleted_user_assignments, $user_ids);

            // Trigger an event with all just newly reactivated user assignments
            track_user_assigned_bulk::create_from_user_assignments(
                $assignment->track_id,
                $possible_deleted_user_assignments->pluck('subject_user_id'),
                $assignment->type
            )->trigger();
        }
    }

    /**
     * @param collection|track_user_assignment[] $user_assignments
     * @param int[] $user_ids
     */
    private function sync_schedule_for_user_assignments(collection $user_assignments, array $user_ids): void {
        /** @var track_assignment $first_assignment */
        $first_assignment = $user_assignments->first();
        $track = new track($first_assignment->track);

        // Bulk fetch all the start and end reference dates.;
        $date_resolver = $track->get_date_resolver($user_ids);

        foreach ($user_assignments as $assignment) {
            $assignment->period_start_date = $date_resolver->get_start_for($assignment->subject_user_id);
            $assignment->period_end_date = $date_resolver->get_end_for($assignment->subject_user_id);

            $assignment->save();
        }
    }

    /**
     * Restore single user assignment
     *
     * @param track_user_assignment $user_assignment
     * @param track $track
     */
    private function reactivate_user_assignment(track_user_assignment $user_assignment, track $track): void {
        $user_id = $user_assignment->subject_user_id;

        $date_resolver = $track->get_date_resolver([$user_id]);

        $user_assignment->period_start_date = $date_resolver->get_start_for($user_id);
        $user_assignment->period_end_date = $date_resolver->get_end_for($user_id);

        $user_assignment->deleted = false;
        $user_assignment->save();
    }

    /**
     * Unlink all user assignments from the assignment which are not in the user group anymore
     *
     * @param track_assignment $assignment
     * @param collection $current_records keys are identifier, values are entities
     * @param assignment_parameters_collection $assignment_parameter_collection newly determined user ids job
     *                                         assignment combinations which should be in the group now
     */
    private function unlink_users(
        track_assignment $assignment,
        collection $current_records,
        assignment_parameters_collection $assignment_parameter_collection
    ): void {
        $records_to_delete = $current_records->filter(
            function (track_user_assignment $track_user_assignment) use ($assignment_parameter_collection) {
                $found = $assignment_parameter_collection->find_from_track_user_assignment($track_user_assignment);

                return !$found;
            }
        );

        if ($records_to_delete->count() > 0) {
            track_user_assignment_via::repository()
                ->where('track_assignment_id', $assignment->id)
                ->where('track_user_assignment_id', $records_to_delete->pluck('id'))
                ->delete();
        }
    }

    /**
     * Mark all user assignments which are not linked to any assignment anymore as deleted
     */
    private function delete_orphaned_user_assignments(): void {
        $orphaned_user_assignments = track_user_assignment::repository()
            ->left_join([track_user_assignment_via::TABLE, 'via'], 'id', 'track_user_assignment_id')
            ->where_null('via.id')
            ->get();

        if ($orphaned_user_assignments->count() > 0) {
            track_user_assignment::repository()
                ->where('id', $orphaned_user_assignments->pluck('id'))
                ->update([
                    'deleted' => true,
                    'updated_at' => time()
                ]);

            /** @var track_user_assignment $assignment_user */
            foreach ($orphaned_user_assignments as $assignment_user) {
                // Trigger event for all affected entries
                $event = track_user_unassigned::create_from_user_assignment($assignment_user);
                $event->trigger();
            }
        }
    }

    /**
     * Sanitize given ids, leave only proper positive numbers
     *
     * @param array $ids
     * @return array
     */
    private function sanitise_ids(array $ids): array {
        $ids = array_filter(
            $ids,
            function ($id) {
                return is_numeric($id) && $id > 0;
            }
        );
        return array_map('intval', $ids);
    }

}