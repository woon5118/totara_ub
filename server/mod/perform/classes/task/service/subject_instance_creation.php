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

namespace mod_perform\task\service;

use coding_exception;
use core\orm\collection;
use core\orm\query\builder;
use mod_perform\dates\date_offset;
use mod_perform\entities\activity\manual_relationship_selection;
use mod_perform\entities\activity\manual_relationship_selection_progress;
use mod_perform\entities\activity\manual_relationship_selector;
use mod_perform\entities\activity\section;
use mod_perform\entities\activity\section_relationship;
use mod_perform\entities\activity\subject_instance;
use mod_perform\entities\activity\track;
use mod_perform\entities\activity\track_user_assignment;
use mod_perform\hook\subject_instances_created;
use mod_perform\state\subject_instance\complete;
use stdClass;
use totara_core\entities\relationship;
use totara_core\relationship\helpers\relationship_collection_manager as core_relationship_collection_manager;
use totara_core\relationship\relationship as relationship_model;

/**
 * This class is responsible for creating new subject instances for users who
 * are assigned to a track.
 *
 * It creates new instance for every assignment which does not have a
 * subject instance yet and meets time interval restrictions.
 * It also creates repeating subject instances, if the track is configured that way.
 */
class subject_instance_creation {

    public function generate_instances() {
        // Get all user assignments that potentially should have a subject instance created.
        $user_assignments = $this->get_user_assignments_potentially_needing_instances();

        $dtos = new \core\collection();

        foreach ($user_assignments as $user_assignment) {
            if (!$this->is_it_time_for_a_new_subject_instance($user_assignment)) {
                continue;
            }

            $status = subject_instance::STATUS_ACTIVE;
            $manual_relationships = $this->get_manual_relationships($user_assignment);
            if ($manual_relationships->count() > 0) {
                $status = subject_instance::STATUS_PENDING;
            }

            $now = time();
            $subject_instance = new subject_instance();
            $subject_instance->track_user_assignment_id = $user_assignment->id;
            $subject_instance->subject_user_id = $user_assignment->subject_user_id;
            $subject_instance->job_assignment_id = $user_assignment->job_assignment_id;
            $subject_instance->status = $status;
            $subject_instance->created_at = $now;
            $subject_instance->due_date = $this->calculate_due_date($user_assignment, $now);
            $subject_instance->save();

            $this->create_progress_for_manual_relationships($subject_instance, $manual_relationships);

            $dtos->append(subject_instance_dto::create_from_entity($subject_instance));
        }

        $hook = new subject_instances_created($dtos);
        $hook->execute();
    }

    /**
     * @param track_user_assignment $user_assignment
     * @param int $reference_date
     * @return int|null
     */
    private function calculate_due_date(track_user_assignment $user_assignment, int $reference_date): ?int {
        if (!$user_assignment->due_date_is_enabled) {
            return null;
        }

        if ($user_assignment->due_date_is_fixed) {
            return $user_assignment->due_date_fixed;
        }

        $offset = date_offset::create_from_json($user_assignment->due_date_offset);
        return $offset->apply($reference_date);
    }

    /**
     * Check if the track user assignment should have a new subject instance created according to repeat settings.
     * Note this is not checking if the repeat limit is reached. That should be checked before calling this method.
     *
     * @param track_user_assignment $user_assignment
     * @return bool
     */
    private function is_it_time_for_a_new_subject_instance(track_user_assignment $user_assignment): bool {
        if (is_null($user_assignment->instance_count)) {
            // Does not have a subject instance yet.
            return true;
        }

        if (!$user_assignment->repeating_is_enabled) {
            // Already has at least one subject instance and repeating is off.
            return false;
        }

        // Check if repeat settings require to create a new subject instance.
        $reference_date = null;
        $is_latest_instance_complete = ((int)$user_assignment->instance_progress === complete::get_code());
        switch ($user_assignment->repeating_type) {
            case track::SCHEDULE_REPEATING_TYPE_AFTER_CREATION:
                $reference_date = $user_assignment->instance_created_at;
                break;
            case track::SCHEDULE_REPEATING_TYPE_AFTER_CREATION_WHEN_COMPLETE:
                if (!$is_latest_instance_complete) {
                    return false;
                }
                $reference_date = $user_assignment->instance_created_at;
                break;
            case track::SCHEDULE_REPEATING_TYPE_AFTER_COMPLETION:
                if (!$is_latest_instance_complete) {
                    return false;
                }
                $reference_date = $user_assignment->instance_completed_at;
                break;
            default:
                throw new coding_exception("Bad repeating_type: {$user_assignment->repeating_type}");
        }

        $offset = date_offset::create_from_json($user_assignment->repeating_offset);
        $threshold = $offset->apply($reference_date);
        return (time() > $threshold);
    }

    /**
     * Get user assignments that potentially need a subject instance created.
     * We check several conditions:
     *  - assignment, track and activity have to be active
     *  - period settings must match
     *  - track is not flagged for schedule synchronisation because that should happen before we create instances
     *  - assignment either doesn't have any instances or the repeat config is such that it potentially can have more
     *
     * @return collection|track_user_assignment[]
     */
    private function get_user_assignments_potentially_needing_instances(): collection {
        return track_user_assignment::repository()
            ->select('*')
            ->filter_by_possibly_has_subject_instances_to_create()
            ->filter_by_active()
            ->filter_by_active_track_and_activity()
            ->filter_by_time_interval()
            ->filter_by_does_not_need_schedule_sync()
            ->get();
    }

    /**
     * Get manual relationships if this activity has them
     *
     * @param track_user_assignment $user_assignment
     * @return collection
     */
    private function get_manual_relationships(track_user_assignment $user_assignment): collection {
        $relationships = relationship::repository()
            ->join([section_relationship::TABLE, 'sr'], 'id', 'core_relationship_id')
            ->join([section::TABLE, 's'], 'sr.section_id', 'id')
            ->where('s.activity_id', $user_assignment->track->activity_id)
            ->where('type', relationship::TYPE_MANUAL)
            ->get(true);

        // Make the result unique
        $manual_relationships = [];
        foreach ($relationships as $relationship) {
            $manual_relationships[$relationship->id] = $relationship;
        }

        return collection::new($manual_relationships);
    }

    /**
     * Create progress records for given manual relationships
     *
     * @param subject_instance $subject_instance
     * @param collection|relationship[] $manual_relationships
     */
    private function create_progress_for_manual_relationships(
        subject_instance $subject_instance,
        collection $manual_relationships
    ): void {
        if ($manual_relationships->count() === 0) {
            return;
        }

        // Get the manual_relation_selection records for this activity
        $manual_relation_selections = manual_relationship_selection::repository()
            ->where('activity_id', $subject_instance->track->activity_id)
            ->get();

        $relationship_manager = $this->create_relationship_manager($manual_relation_selections);
        $relationship_args = $this->get_args_for_resolving_relationships($subject_instance);

        $selectors = [];
        $created_at = time();
        foreach ($manual_relationships as $manual_relationship) {
            // Make sure it is really a manual one
            if ($manual_relationship->type !== 1) {
                continue;
            }

            // Get the id of the manual_relation_selection record related to this relationship
            /** @var manual_relationship_selection $manual_relation_selection */
            $manual_relation_selection = $manual_relation_selections->find('manual_relationship_id', $manual_relationship->id);
            if (!$manual_relation_selection) {
                throw new coding_exception(sprintf(
                    'No manual_relation_selection record found for relationship id %d in activity %d',
                    $manual_relationship->id,
                    $subject_instance->track->activity_id
                ));
            }

            // Create one progress record for this relationship / subject_instance combo
            $progress = new manual_relationship_selection_progress();
            $progress->subject_instance_id = $subject_instance->id;
            $progress->manual_relation_selection_id = $manual_relation_selection->id;
            $progress->status = 0;
            $progress->save();

            // Get the users for this relationship and create one selector record for each user
            $user_ids = $relationship_manager->get_users_for_relationships(
                $relationship_args,
                [$manual_relation_selection->selector_relationship_id]
            );

            foreach ($user_ids as $user_id) {
                $selector = new stdClass();
                $selector->manual_relation_select_progress_id = $progress->id;
                $selector->user_id = $user_id;
                $selector->created_at = $created_at;

                $selectors[] = $selector;
            }

        }

        // At the end add all the selectors determined before with the least amount of queries
        if (!empty($selectors)) {
            builder::get_db()->insert_records(manual_relationship_selector::TABLE, $selectors);
        }
    }

    /**
     * @param collection|manual_relationship_selection[] $manual_relation_selection
     * @return core_relationship_collection_manager
     * @throws coding_exception
     */
    private function create_relationship_manager(collection $manual_relation_selection): core_relationship_collection_manager {
        return new core_relationship_collection_manager($manual_relation_selection->pluck('selector_relationship_id'));
    }

    /**
     * @param subject_instance $subject_instance
     * @return array
     */
    private function get_args_for_resolving_relationships(subject_instance $subject_instance): array {
        if (empty($subject_instance->job_assignment_id) && !empty($subject_instance->subject_user_id)) {
            $args['user_id'] = $subject_instance->subject_user_id;
        } else {
            $args['user_id'] = $subject_instance->subject_user_id; // Always required for subject resolver.
            $args['job_assignment_id'] = $subject_instance->job_assignment_id;
        }
        return $args;
    }

}