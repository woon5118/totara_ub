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
use core\collection;
use core\orm\lazy_collection;
use core\orm\query\builder;
use core\orm\query\subquery;
use mod_perform\dates\date_offset;
use mod_perform\entities\activity\section;
use mod_perform\entities\activity\section_relationship;
use mod_perform\entities\activity\subject_instance;
use mod_perform\entities\activity\track;
use mod_perform\entities\activity\track_user_assignment;
use mod_perform\hook\subject_instances_created;
use mod_perform\state\subject_instance\complete;
use stdClass;
use totara_core\entities\relationship;

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

        $now = time();

        $user_assignment_ids = [];
        $inserts = [];
        foreach ($user_assignments as $user_assignment) {
            if (!$this->is_it_time_for_a_new_subject_instance($user_assignment)) {
                continue;
            }

            $status = subject_instance::STATUS_ACTIVE;
            if ($user_assignment->manual_relationships !== null) {
                $status = subject_instance::STATUS_PENDING;
            }

            $subject_instance = new stdClass();
            $subject_instance->track_user_assignment_id = $user_assignment->id;
            $subject_instance->subject_user_id = $user_assignment->subject_user_id;
            $subject_instance->job_assignment_id = $user_assignment->job_assignment_id;
            $subject_instance->status = $status;
            $subject_instance->created_at = $now;
            $subject_instance->due_date = $this->calculate_due_date($user_assignment, $now);

            $inserts[] = $subject_instance;
            $user_assignment_ids[] = $user_assignment->id;
        }
        // Leave no reference behind for later easy unsetting
        unset($subject_instance);

        if (!empty($inserts)) {
            $dtos = builder::get_db()->transaction(function () use ($inserts, $user_assignment_ids) {
                // Now insert the records as batch to reduce amount of queries
                builder::get_db()->insert_records_via_batch(subject_instance::TABLE, $inserts);

                // Free up memory
                unset($inserts);

                // Now load all just created subject instance and trigger event for them
                $created_subject_instances = subject_instance::repository()
                    ->where('track_user_assignment_id', $user_assignment_ids)
                    ->get_lazy();

                $dtos = new collection();
                foreach ($created_subject_instances as $created_subject_instance) {
                    $dtos->append(subject_instance_dto::create_from_entity($created_subject_instance));
                }

                return $dtos;
            });

            $hook = new subject_instances_created($dtos);
            $hook->execute();
        }
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
     * @return lazy_collection|track_user_assignment[]
     */
    private function get_user_assignments_potentially_needing_instances(): lazy_collection {
        return track_user_assignment::repository()
            ->as('tua')
            ->select('*')
            // This subquery will return a column with null if there are
            // no manual relationships involved and a value > 0 if there are.
            ->add_select((new subquery(function (builder $builder) {
                $builder->from(section_relationship::TABLE)
                    ->select('MAX(id)')
                    ->join([section::TABLE, 's'], 'section_id', 'id')
                    ->join([relationship::TABLE, 'r'], 'core_relationship_id', 'id')
                    ->where_field('s.activity_id', 't.activity_id')
                    ->where('r.type', relationship::TYPE_MANUAL);
            }))->as('manual_relationships'))
            ->join([track::TABLE, 't'], 'track_id', 'id')
            ->filter_by_possibly_has_subject_instances_to_create()
            ->filter_by_active()
            ->filter_by_active_track_and_activity()
            ->filter_by_time_interval()
            ->filter_by_does_not_need_schedule_sync()
            ->get_lazy();
    }

}