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

use coding_exception;
use core\orm\collection;
use core\orm\entity\model;
use mod_perform\dates\resolvers\date_resolver;
use mod_perform\dates\resolvers\fixed_range_resolver;
use mod_perform\dates\resolvers\user_creation_date_resolver;
use mod_perform\dates\schedule_constants;
use mod_perform\entities\activity\track as track_entity;
use mod_perform\models\activity\track as track_model;
use mod_perform\state\activity\active;
use mod_perform\user_groups\grouping;
use moodle_exception;

/**
 * Represents a single performance activity track.
 *
 * @property-read int $id
 * @property-read int $activity_id
 * @property-read string $description
 * @property-read int $status
 * @property-read int $subject_instance_generation
 * @property-read bool $schedule_is_open
 * @property-read bool $schedule_is_fixed
 * @property-read int $schedule_fixed_from
 * @property-read int $schedule_fixed_to
 * @property-read int $schedule_dynamic_count_from
 * @property-read int $schedule_dynamic_count_to
 * @property-read string $schedule_dynamic_unit
 * @property-read string $schedule_dynamic_direction
 * @property-read bool $due_date_is_enabled
 * @property-read bool $due_date_is_fixed
 * @property-read int $due_date_fixed
 * @property-read int $due_date_relative_count
 * @property-read int $due_date_relative_unit
 * @property-read bool $repeating_is_enabled
 * @property-read int $created_at
 * @property-read int $updated_at
 *
 * @property-read activity $activity
 * @property-read collection|track_assignment[] $assignments
 */
class track extends model {

    protected $entity_attribute_whitelist = [
        'id',
        'activity_id',
        'description',
        'status',
        'schedule_is_open',
        'schedule_is_fixed',
        'schedule_fixed_from',
        'schedule_fixed_to',
        'schedule_dynamic_count_from',
        'schedule_dynamic_count_to',
        'due_date_is_enabled',
        'due_date_is_fixed',
        'due_date_fixed',
        'due_date_relative_count',
        'repeating_is_enabled',
        'created_at',
        'updated_at',
    ];

    protected $model_accessor_whitelist = [
        'activity',
        'assignments',
        'subject_instance_generation',
        'schedule_dynamic_direction',
        'schedule_dynamic_unit',
        'due_date_relative_unit',
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
            throw new moodle_exception('nopermissions', '', '', 'create track');
        }

        $entity = new track_entity();
        $entity->activity_id = $parent->get_id();
        $entity->description = $description;
        $entity->status = track_status::ACTIVE;
        $entity->subject_instance_generation = track_entity::SUBJECT_INSTANCE_GENERATION_ONE_PER_SUBJECT;
        $entity->schedule_is_open = true;
        $entity->schedule_is_fixed = true;
        $entity->schedule_fixed_from = time();
        $entity->schedule_fixed_to = null;
        $entity->schedule_dynamic_count_from = null;
        $entity->schedule_dynamic_count_to = null;
        $entity->schedule_dynamic_unit = null;
        $entity->schedule_dynamic_direction = null;
        $entity->due_date_is_enabled = false;
        $entity->due_date_is_fixed = null;
        $entity->due_date_fixed = null;
        $entity->due_date_relative_count = null;
        $entity->due_date_relative_unit = null;
        $entity->repeating_is_enabled = false;
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
            throw new moodle_exception('nopermissions', '', '', 'load track by activity');
        }

        return track_entity::repository()
            ->where('activity_id', $parent->get_id())
            ->get()
            ->map_to(static::class);
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
        if (!in_array($assignment_type, track_assignment_type::get_allowed(), true)) {
            throw new coding_exception("unknown assignment type: '$assignment_type'");
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
        if (!in_array($assignment_type, track_assignment_type::get_allowed(), true)) {
            throw new coding_exception("unknown assignment type: '$assignment_type'");
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
            throw new moodle_exception('nopermissions', '', '', $op);
        }
    }

    /**
     * Set the schedule to be closed with fixed dates
     *
     * After calling, use track::update to save the changes to the DB
     *
     * @param int $from
     * @param int $to
     */
    public function set_schedule_closed_fixed(int $from, int $to): void {
        if ($to < $from) {
            throw new moodle_exception('fixed_date_selector_error_range', 'mod_perform');
        }

        $properties_to_update = [
            'schedule_is_open' => false,
            'schedule_is_fixed' => true,
            'schedule_fixed_from' => $from,
            'schedule_fixed_to' => $to,
        ];

        $this->set_schedule_properties($properties_to_update);
    }

    /**
     * Set the schedule to be open ended with fixed dates
     *
     * After calling, use track::update to save the changes to the DB
     *
     * @param int $from
     */
    public function set_schedule_open_fixed(int $from): void {
        $properties_to_update = [
            'schedule_is_open' => true,
            'schedule_is_fixed' => true,
            'schedule_fixed_from' => $from,
        ];

        $this->set_schedule_properties($properties_to_update);
    }

    /**
     * Set the schedule to be closed with dynamic dates
     *
     * After calling, use track::update to save the changes to the DB
     *
     * @param int $count_from
     * @param int $count_to
     * @param int $unit
     * @param int $direction
     */
    public function set_schedule_closed_dynamic(int $count_from, int $count_to, int $unit, int $direction): void {
        if ($count_from < 0) {
            throw new coding_exception('Count from must be a positive integer');
        }

        if (!isset(self::get_dynamic_schedule_units()[$unit])) {
            throw new coding_exception('Invalid dynamic schedule unit');
        }

        if (!isset(self::get_dynamic_schedule_directions()[$direction])) {
            throw new coding_exception('Invalid dynamic schedule direction');
        }

        if ($direction === track_entity::SCHEDULE_DYNAMIC_DIRECTION_AFTER) {
            if ($count_from > $count_to) {
                throw new coding_exception('"count_from" must not be after "count_to" when dynamic schedule direction is "AFTER"');
            }
        } else if ($count_from < $count_to) {
            throw new coding_exception('"count_from" must not be before "count_to" when dynamic schedule direction is "BEFORE"');
        }

        $properties_to_update = [
            'schedule_is_open' => false,
            'schedule_is_fixed' => false,
            'schedule_dynamic_count_from' => $count_from,
            'schedule_dynamic_count_to' => $count_to,
            'schedule_dynamic_unit' => $unit,
            'schedule_dynamic_direction' => $direction,
        ];

        $this->set_schedule_properties($properties_to_update);
    }

    /**
     * Set the schedule to be open ended with dynamic dates
     *
     * After calling, use track::update to save the changes to the DB
     *
     * @param int $count_from
     * @param int $unit
     * @param int $direction
     */
    public function set_schedule_open_dynamic(int $count_from, int $unit, int $direction): void {
        if (!isset(self::get_dynamic_schedule_units()[$unit])) {
            throw new coding_exception('Invalid dynamic schedule unit');
        }

        if ($count_from < 0) {
            throw new coding_exception('Count from must be a positive integer');
        }

        if (!isset(self::get_dynamic_schedule_directions()[$direction])) {
            throw new coding_exception('Invalid dynamic schedule direction');
        }

        $properties_to_update = [
            'schedule_is_open' => true,
            'schedule_is_fixed' => false,
            'schedule_dynamic_count_from' => $count_from,
            'schedule_dynamic_unit' => $unit,
            'schedule_dynamic_direction' => $direction,
        ];

        $this->set_schedule_properties($properties_to_update);
    }

    /**
     * Set the subject instance generation method
     *
     * @param int $method
     */
    public function set_subject_instace_generation(int $method): void {
        if (!isset(self::get_subject_instance_generation_methods()[$method])) {
            throw new coding_exception('Invalid subject instance generation method');
        }

        $entity = $this->entity;

        $entity->subject_instance_generation = $method;
    }

    /**
     * Set the creation range properties.
     * This function resets scheduling properties that are not provided.
     * After calling, use track::update to save the changes to the DB
     *
     * @param array $properties containing at least schedule_is_open and schedule_is_fixed
     */
    private function set_schedule_properties(array $properties): void {
        $entity = $this->entity;

        $entity->schedule_is_open = $properties['schedule_is_open'];
        $entity->schedule_is_fixed = $properties['schedule_is_fixed'];
        $entity->schedule_fixed_from = $properties['schedule_fixed_from'] ?? null;
        $entity->schedule_fixed_to = $properties['schedule_fixed_to'] ?? null;
        $entity->schedule_dynamic_count_from = $properties['schedule_dynamic_count_from'] ?? null;
        $entity->schedule_dynamic_count_to = $properties['schedule_dynamic_count_to'] ?? null;
        $entity->schedule_dynamic_unit = $properties['schedule_dynamic_unit'] ?? null;
        $entity->schedule_dynamic_direction = $properties['schedule_dynamic_direction'] ?? null;

        if ($this->get_activity()->get_status_state() instanceof active) {
            $entity->schedule_needs_sync = true;
        }

        $this->entity->update();
    }

    /**
     * Disable repeating
     *
     * Clears all repeating related fields.
     * After calling, use track::update to save the changes to the DB
     */
    public function set_repeating_disabled(): void {
        $this->set_repeating_properties(['repeating_is_enabled' => false]);
    }

    /**
     * Set repeating to ...
     *
     * After calling, use track::update to save the changes to the DB
     *
     * TODO add params and/or split function
     */
    public function set_repeating_enabled(): void {
        $properties_to_update = [
            'repeating_is_enabled' => true,
        ];

        $this->set_repeating_properties($properties_to_update);
    }

    /**
     * Set the due date properties.
     * This function resets repeating properties that are not provided.
     * After calling, use track::update to save the changes to the DB
     *
     * @param array $properties containing at least repeating_is_enabled
     */
    private function set_repeating_properties(array $properties): void {
        $entity = $this->entity;

        $entity->repeating_is_enabled = $properties['repeating_is_enabled'];

        $this->entity->update();
    }

    /**
     * Disable the due date
     *
     * Clears all due date related fields.
     */
    public function set_due_date_disabled(): void {
        $this->set_due_date_properties(['due_date_is_enabled' => false]);
    }

    /**
     * Update the due date to fixed (and enabled)
     *
     * After calling, use track::update to save the changes to the DB
     *
     * @param int $fixed
     */
    public function set_due_date_fixed(int $fixed): void {
        $properties_to_update = [
            'due_date_is_enabled' => true,
            'due_date_is_fixed' => true,
            'due_date_fixed' => $fixed,
        ];

        $this->set_due_date_properties($properties_to_update);
    }

    /**
     * Set the due date to relative (and enabled)
     *
     * After calling, use track::update to save the changes to the DB
     *
     * @param int $count
     * @param int $unit
     */
    public function set_due_date_relative(int $count, int $unit): void {
        $properties_to_update = [
            'due_date_is_enabled' => true,
            'due_date_is_fixed' => false,
            'due_date_relative_count' => $count,
            'due_date_relative_unit' => $unit,
        ];

        $this->set_due_date_properties($properties_to_update);
    }

    /**
     * Set the due date properties.
     * This function resets due date properties that are not provided.
     * After calling, use track::update to save the changes to the DB
     *
     * @param array $properties containing at least due_date_is_enabled
     */
    private function set_due_date_properties(array $properties): void {
        $entity = $this->entity;

        $entity->due_date_is_enabled = $properties['due_date_is_enabled'];
        $entity->due_date_is_fixed = $properties['due_date_is_fixed'] ?? null;
        $entity->due_date_fixed = $properties['due_date_fixed'] ?? null;
        $entity->due_date_relative_count = $properties['due_date_relative_count'] ?? null;
        $entity->due_date_relative_unit = $properties['due_date_relative_unit'] ?? null;

        $this->entity->update();
    }

    public static function get_dynamic_schedule_directions(): array {
        return [
            track_entity::SCHEDULE_DYNAMIC_DIRECTION_AFTER => schedule_constants::AFTER,
            track_entity::SCHEDULE_DYNAMIC_DIRECTION_BEFORE => schedule_constants::BEFORE,
        ];
    }

    public static function get_dynamic_schedule_units(): array {
        return [
            track_entity::SCHEDULE_DYNAMIC_UNIT_DAY => schedule_constants::DAY,
            track_entity::SCHEDULE_DYNAMIC_UNIT_WEEK => schedule_constants::WEEK,
            track_entity::SCHEDULE_DYNAMIC_UNIT_MONTH => schedule_constants::MONTH,
        ];
    }

    public static function get_subject_instance_generation_methods(): array {
        return [
            track_entity::SUBJECT_INSTANCE_GENERATION_ONE_PER_SUBJECT => schedule_constants::ONE_PER_SUBJECT,
            track_entity::SUBJECT_INSTANCE_GENERATION_ONE_PER_JOB => schedule_constants::ONE_PER_JOB,
        ];
    }

    /**
     * Get the string representation of the subject instance generation method.
     *
     * @return string
     */
    protected function get_subject_instance_generation(): string {
        return $this->map_from_entity(
            $this->entity->subject_instance_generation,
            track_model::get_subject_instance_generation_methods(),
            'Unknown subject instance generation method: %s'
        );
    }

    /**
     * Get the string representation of the dynamic schedule direction.
     *
     * @return string|null
     */
    protected function get_schedule_dynamic_direction(): ?string {
        return $this->map_from_entity(
            $this->entity->schedule_dynamic_direction,
            track_model::get_dynamic_schedule_directions(),
            'Unknown dynamic schedule direction: %s'
        );
    }

    /**
     * Get the string representation of the dynamic schedule unit.
     *
     * @return string|null
     */
    protected function get_schedule_dynamic_unit(): ?string {
        return $this->map_from_entity(
            $this->entity->schedule_dynamic_unit,
            track_model::get_dynamic_schedule_units(),
            'Unknown dynamic schedule unit: %s'
        );
    }

    /**
     * Get the string representation of the relative due date unit.
     *
     * @return string|null
     */
    protected function get_due_date_relative_unit(): ?string {
        return $this->map_from_entity(
            $this->entity->due_date_relative_unit,
            track_model::get_dynamic_schedule_units(),
            'Unknown dynamic due date unit: %s'
        );
    }

    /**
     * Maps an entity/db int constant to the string representation.
     *
     * @param int $entity_value
     * @param array $map
     * @param string $exception_message
     * @return string
     */
    protected function map_from_entity(?int $entity_value, array $map,  string $exception_message): ?string {
        if ($entity_value === null) {
            return null;
        }

        if (!array_key_exists($entity_value, $map)) {
            throw new coding_exception(sprintf($exception_message, $entity_value));
        }

        return $map[$entity_value];
    }

    /**
     * Get the date resolver for this track and a given set of users.
     *
     * @param array $user_ids
     * @return date_resolver
     */
    public function get_date_resolver(array $user_ids): date_resolver {
        if ($this->schedule_is_fixed) {
            $to = $this->schedule_is_open ? null : $this->schedule_fixed_to;

            return new fixed_range_resolver($this->schedule_fixed_from, $to);
        }

        $to = $this->schedule_is_open ? null : $this->schedule_dynamic_count_to;

        return new user_creation_date_resolver(
            $this->schedule_dynamic_count_from,
            $to,
            $this->get_schedule_dynamic_unit(),
            $this->get_schedule_dynamic_direction(),
            $user_ids
        );
    }

    /**
     * Checks that the properties of this model are valid
     *
     * If validation fails, an exception is thrown.
     *
     * Schedule, due date and repeating field validation is not required, because schedules can only be
     * set using the methods provided in this class. We only need to check the interdependencies between
     * these sets of properties.
     */
    public function validate(): void {
        $entity = $this->entity;

        if ($entity->due_date_is_fixed) {
            // Check that due date type is valid given schedule open/fixed.
            if ($entity->schedule_is_open || !$entity->schedule_is_fixed) {
                throw new coding_exception('Cannot set due date to fixed except when schedule is not open and fixed');
            }

            // Check that due date is not before schedule end date.
            if ($entity->due_date_fixed <= $entity->schedule_fixed_to) {
                throw new coding_exception('Cannot set fixed due date earlier than the schedule end date');
            }
        }
    }

    /**
     * Saves changes to this model to the database
     *
     * Validation is performed before saving occurs. If validation fails, an exception is thrown.
     */
    public function update(): void {
        $this->validate();

        $this->entity->update();
    }

}
