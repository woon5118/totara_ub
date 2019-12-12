<?php
/*
 * This file is part of Totara Learn
 *
 * Copyright (C) 2018 onwards Totara Learning Solutions LTD
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
 * @package totara_competency
 */

namespace totara_competency;

use totara_competency\entities\assignment;
use totara_competency\entities\competency_assignment_user;
use totara_competency\entities\competency_assignment_user_repository;
use totara_competency\event\assignment_user_assigned;
use totara_competency\event\assignment_user_unassigned;
use totara_competency\models\assignment as assignment_model;
use core\entities\expandable;

class expand_task {

    /**
     * @var \moodle_database
     */
    protected $db;

    private $cache = [];

    public function __construct(\moodle_database $db) {
        $this->db = $db;
    }

    /**
     * Expand all active assignments
     */
    public function expand_all() {
        // clean up all orphaned records (archived or deleted assignments)
        competency_assignment_user_repository::remove_orphaned_records();

        $assignments = assignment::repository()
            ->filter_by_active()
            ->get_lazy();
        // TODO performance - maybe load with user assignments relation?
        //      or even load the related user rows?
        //      memory usage!!
        foreach ($assignments as $assignment) {
            $this->expand_assignment($assignment);
        }
    }

    /**
     * Expand only given assignments. Non existing or inactive assignments will be ignored.
     *
     * @param array $assignment_ids
     */
    public function expand_multiple(array $assignment_ids) {
        // clean up all orphaned records (archived or deleted assignments)
        competency_assignment_user_repository::remove_orphaned_records();

        $assignment_ids = $this->sanitise_ids($assignment_ids);
        if (!empty($assignment_ids)) {
            $assignments = assignment::repository()
                ->filter_by_ids($assignment_ids)
                ->filter_by_active()
                ->get();
            foreach ($assignments as $assignment) {
                $this->expand_assignment($assignment);
            }
        }
    }

    /**
     * Expand a single assignment with the given id. Missing assignment is ignored.
     *
     * @param int $assignment_id
     */
    public function expand_single(int $assignment_id) {
        // clean up all orphaned records (archived or deleted assignments)
        competency_assignment_user_repository::remove_orphaned_records();

        /** @var assignment $assignment */
        $assignment = assignment::repository()
            ->where('id', $assignment_id)
            ->where('status', assignment::STATUS_ACTIVE)
            ->one();

        if ($assignment) {
            $this->expand_assignment($assignment);
        }
    }

    /**
     * Expand the given assignment.
     *
     * @param assignment $assignment
     */
    private function expand_assignment(assignment $assignment) {
        // We ignore missing assignments to not cause failing tasks.
        // It can happen that an assignment was deleted before the task is run.
        // On the next run the assignments will be picked up again.
        if (!$assignment->id) {
            return;
        }
        $assignment_id = (int) $assignment->id;

        // load all current source targets relations of the assignment
        // to avoid more requests to the database when checking if entry
        // already exists.
        $current_entries = $this->load_current_entries($assignment_id);
        $added_entries_ids = [];

        $user_ids = $this->get_expanded_users($assignment->user_group_type, $assignment->user_group_id);

        foreach ($user_ids as $user_id) {
            // TODO move unique identifier gegneration out of the entity
            //      to avoid creating objects for skipped users
            //      also maybe avoid md5(), just concatenate
            $competency_user = new competency_assignment_user();
            $competency_user->assignment_id = $assignment_id;
            $competency_user->competency_id = $assignment->competency_id;
            $competency_user->user_id = $user_id;

            $identifier = $competency_user->unique_identifier;
            $added_entries_ids[] = $identifier;
            // If the entry does not exist yet, create it now otherwise just ignore it
            if (!isset($current_entries[$identifier])) {
                // TODO performance - use insert_records_via_batch()
                // TODO performance use transaction?
                $competency_user->save();
                assignment_user_assigned::create_from_assignment_user($competency_user, $assignment->type)->trigger();
            }
            unset($competency_user);
        }

        // Delete all records which were not processed within this run, means they were removed from the source or targets
        $this->unassign_users($current_entries, $added_entries_ids);
    }

    /**
     * Load all current entries for the given assignment, key uniquely identifies the source target
     *
     * @param int $assignment_id
     * @return array|competency_assignment_user[]
     */
    private function load_current_entries(int $assignment_id): array {
        $current_entries = [];
        /** @var competency_assignment_user[] $competencies_users */
        $competencies_users = competency_assignment_user::repository()
            ->filter_by_assignment_id($assignment_id)
            ->get();
        foreach ($competencies_users as $competency_user) {
            $current_entries[$competency_user->unique_identifier] = $competency_user;
        }

        return $current_entries;
    }

    /**
     * Expand the user group
     *
     * @param string $user_group_type something like 'cohort', 'position', 'organisation', 'user'
     * @param int $user_group_id
     * @return array
     */
    private function get_expanded_users(string $user_group_type, int $user_group_id): array {
        // TODO performance - just have get_cache_entry, otherwise it's called twice
        if (!$this->has_cache_entry($user_group_type, $user_group_id)) {
            $expanded_records = $this->expand_entity($user_group_type, $user_group_id);
            $this->add_cache_entry($user_group_type, $user_group_id, $expanded_records);
        } else {
            $expanded_records = $this->get_cache_entry($user_group_type, $user_group_id);
        }
        return $expanded_records;
    }

    private function has_cache_entry(string $cache_type, int $id): bool {
        return isset($this->cache[$cache_type][$id]);
    }

    private function add_cache_entry(string $cache_type, int $id, array $record) {
        if (!isset($this->cache[$cache_type])) {
            $this->cache[$cache_type] = [];
        }
        $this->cache[$cache_type][$id] = $record;
    }

    private function get_cache_entry(string $cache_type, int $id): array {
        $cache_entry = [];
        if ($this->has_cache_entry($cache_type, $id)) {
            $cache_entry = $this->cache[$cache_type][$id];
        }
        return $cache_entry;
    }

    /**
     * Expand the entity, if possible, to individual entries. for example expand a cohort to it's members
     *
     * @param string $type
     * @param int $target_id
     * @return array
     */
    private function expand_entity(string $type, int $target_id): array {
        $class_name = assignment_model::get_entity_class_by_user_group_type($type);
        if (is_subclass_of($class_name, expandable::class)) {
            /** @var expandable $entity */
            // TODO performance - check if that could be loaded via relationship, might require polymorphic relationship
            $entity = new $class_name($target_id);
            if ($entity) {
                return $entity->expand();
            }
            return [];
        }
        return [$target_id];
    }

    /**
     * Unassign all records which are not in any user group anymore.
     *
     * @param array $current_records keys are identifier, values are records
     * @param array $added_records_identifiers identifiers of the records added during this run
     */
    private function unassign_users(array $current_records, array $added_records_identifiers) {
        // Delete all records which were not processed by the previous loop
        $current_records_identifiers = array_keys($current_records);
        $delete_records = array_diff($current_records_identifiers, $added_records_identifiers);

        $records_to_delete = [];
        foreach ($delete_records as $delete_record_identifier) {
            if (isset($current_records[$delete_record_identifier])) {
                $records_to_delete[] = $current_records[$delete_record_identifier];
            }
        }

        /** @var competency_assignment_user $assignment_user */
        foreach ($records_to_delete as $assignment_user) {
            // Trigger event for all affected entries
            $event = assignment_user_unassigned::create_from_assignment_user($assignment_user);
            // TODO performance - delete in one query
            $assignment_user->delete();
            // TODO performance use transaction?
            $event->trigger();
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