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
 * @author Murali Nair <murali.nair@totaralearning.com>
 * @package mod_perform
 */

namespace mod_perform\models\activity;

use core\collection;
use core\orm\entity\model;
use mod_perform\entities\activity\track as track_entity;
use mod_perform\user_groups\grouping;

/**
 * Represents a single performance activity track.
 *
 * @property-read collection|track_assignment[] assignments
 */
class track extends model {
    /**
     * {@inheritdoc}
     */
    protected $entity_attribute_whitelist = [
        'id',
        'activity_id',
        'description',
        'status',
        'schedule_type',
    ];

    protected $model_accessor_whitelist = [
        'activity',
        'assignments',
    ];

    /**
     * @var track_entity
     */
    protected $entity;

    /**
     * {@inheritdoc}
     */
    public static function get_entity_class(): string {
        return track_entity::class;
    }

    /**
     * Creates a new track instance.
     *
     * @param activity $parent parent activity.
     * @param string $description track description.
     *
     * @return track the newly created track.
     */
    public static function create(activity $parent, string $description = ''): track {
        if (!$parent->can_manage()) {
            throw new \moodle_exception('nopermissions', '', '', 'create track');
        }

        $entity = new track_entity();
        $entity->activity_id = $parent->get_id();
        $entity->description = $description;
        $entity->status = track_status::ACTIVE;
        $entity->schedule_type = track_entity::SCHEDULE_TYPE_OPEN_FIXED;
        $entity->save();

        return new track($entity);
    }

    /**
     * Retrieves tracks by their parent activity.
     *
     * @param activity $parent parent activity.
     *
     * @return collection retrieved tracks.
     */
    public static function load_by_activity(activity $parent): collection {
        if (!$parent->can_manage()) {
            throw new \moodle_exception('nopermissions', '', '', 'load track by activity');
        }

        return track_entity::repository()
            ->where('activity_id', $parent->get_id())
            ->get()
            ->map_to(track::class);
    }

    /**
     * Get the activity model that this track belongs to
     *
     * @return activity
     */
    public function get_activity(): activity {
        return activity::load_by_entity($this->entity->activity);
    }

    /**
     * Get all assignment models that belong to this track
     *
     * @return collection|track_assignment[]
     */
    public function get_assignments(): collection {
        return $this->entity->assignments->map_to(track_assignment::class);
    }

    /**
     * Activates the track.
     *
     * @return track the activated track.
     */
    public function activate(): track {
        $this->require_manage('change track status to active');

        $allowed_transitions = [
            track_status::PAUSED
        ];

        if (in_array($this->entity->status, $allowed_transitions)) {
            $this->entity->status = track_status::ACTIVE;
            $this->entity->save();
        }

        return $this;
    }

    /**
     * Pauses the track.
     *
     * @return track the paused track.
     */
    public function pause(): track {
        $this->require_manage('change track status to pause');

        $allowed_transitions = [
            track_status::ACTIVE
        ];

        if (in_array($this->entity->status, $allowed_transitions)) {
            $this->entity->status = track_status::PAUSED;
            $this->entity->save();
        }

        return $this;
    }

    /**
     * Returns whether this track has assignments
     *
     * @return bool
     */
    public function has_assignments(): bool {
        return $this->entity->assignments()->exists();
    }

    /**
     * Adds a user group assignment to this track if it doesn't already exist.
     *
     * NB: no check is done to see if this incoming group id really corresponds
     *     to the given user group type. Neither is there a check to see if the
     *     group record really exists.
     *
     * @param int $assignment_type assignment type.
     * @param grouping $group user grouping.
     *
     * @return track this instance.
     */
    public function add_assignment(int $assignment_type, grouping $group): track {
        if (!in_array($assignment_type, track_assignment_type::get_allowed())) {
            throw new \coding_exception("unknown assignment type: '$assignment_type'");
        }

        $this->require_manage('add track assignment');

        $existing = track_assignment::load_by_track($this, $assignment_type, $group);
        if ($existing->count() === 0) {
            track_assignment::create($this, $assignment_type, $group);
        }

        return $this->refresh();
    }

    /**
     * Removes a user group assignment from this track if it exists.
     *
     * @param int $assignment_type assignment type.
     * @param grouping $group user grouping.
     *
     * @return track this instance.
     */
    public function remove_assignment(int $assignment_type, grouping $group): track {
        if (!in_array($assignment_type, track_assignment_type::get_allowed())) {
            throw new \coding_exception("unknown assignment type: '$assignment_type'");
        }

        $this->require_manage('remove track assignment');

        $existing = track_assignment::load_by_track($this, $assignment_type, $group);
        foreach ($existing as $assignment) {
            $assignment->remove();
        }

        return $this->refresh();
    }

    /**
     * Forces the model to reload its data from the repository.
     *
     * @return track this track.
     */
    public function refresh(): track {
        $this->entity->refresh();
        $this->entity->load_relation('assignments');

        return $this;
    }

    /**
     * Checks if the current user has the rights to execute the given operation.
     * Throws a moodle_exception if the user is not allowed.
     *
     * @param string $op target operation.
     */
    public function require_manage(string $op): void {
        $parent = activity::load_by_entity($this->entity->activity);
        if (!$parent->can_manage()) {
            throw new \moodle_exception('nopermissions', '', '', $op);
        }
    }

    /**
     * Set the schedule to be closed with fixed dates
     *
     * TODO add two fixed date params
     */
    public function update_schedule_closed_fixed(): void {
        $entity = $this->entity;

        $entity->schedule_type = track_entity::SCHEDULE_TYPE_CLOSED_FIXED;

        $entity->update();
    }

    /**
     * Set the schedule to be open ended with fixed dates
     *
     * TODO add one fixed date param
     */
    public function update_schedule_open_fixed(): void {
        $entity = $this->entity;

        $entity->schedule_type = track_entity::SCHEDULE_TYPE_OPEN_FIXED;

        $entity->update();
    }
}
