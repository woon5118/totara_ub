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

namespace totara_competency\models;

use coding_exception;
use core\orm\collection;
use core\orm\entity\entity;
use core\orm\query\builder;
use totara_competency\assignment_create_exception;
use totara_competency\entities\assignment as assignment_entity;
use totara_competency\entities\competency;
use totara_competency\entities\competency_assignment_user;
use totara_competency\entities\competency_assignment_user_log;
use totara_competency\event\assignment_activated;
use totara_competency\event\assignment_archived;
use totara_competency\event\assignment_created;
use totara_competency\event\assignment_deleted;
use totara_competency\event\assignment_user_archived;
use totara_assignment\entities\hierarchy_item;
use totara_assignment\entities\user;
use totara_assignment\filter\hierarchy_item_visible;
use totara_assignment\user_groups;
use totara_competency\models\profile\proficiency_value;

class assignment {

    /**
     * @var assignment_entity
     */
    protected $entity;

    /**
     * @var competency
     */
    private $competency;

    private function __construct(assignment_entity $entity) {
        $this->entity = $entity;
    }

    public static function load_by_id(int $id): self {
        $entity = new assignment_entity($id);
        return new static($entity);
    }

    public static function load_by_entity(assignment_entity $entity): self {
        if (!$entity->exists()) {
            throw new coding_exception('Can load only existing entities');
        }
        return new static($entity);
    }

    public function get_id(): int {
        return $this->entity->id;
    }

    /**
     * Archives the assignment. Only possible for active assignments.
     * Deletes the user records and triggers the events. Keeps the logs.
     *
     * @param bool $continue_tracking
     * @throws coding_exception
     */
    public function archive(bool $continue_tracking = false) {
        if ($this->entity->status !== assignment_entity::STATUS_ACTIVE) {
            throw new coding_exception('Only active assignments can be archived.');
        }

        $system_assignments = [];
        $events = [];
        $assigned_users = competency_assignment_user::repository()
            ->where('assignment_id', $this->entity->id)
            ->get_lazy();

        /** @var competency_assignment_user $assignment_user */
        foreach ($assigned_users as $assignment_user) {
            $events[] = assignment_user_archived::create_from_assignment_user($assignment_user);

            // if tracking should be continued create new system
            // assignments for each user before archiving them
            // We only ever create new tracking assignments for users in group assignments
            if ($continue_tracking && $this->entity->user_group_type !== user_groups::USER) {
                $system_assignments[] = $assignment_user;
            }
        }

        $events[] = assignment_archived::create_from_assignment($this->entity);

        $this->entity->status = assignment_entity::STATUS_ARCHIVED;
        $this->entity->archived_at = time();
        $this->entity->save();

        // Delete all user records for those assignments
        competency_assignment_user::repository()
            ->where('assignment_id', $this->entity->id)
            ->delete();

        // Create system assignments for continuous tracking
        foreach ($system_assignments as $assignment_user) {
            (new assignment_user($assignment_user->user_id))
                ->create_system_assignment($assignment_user->competency_id);
        }

        foreach ($events as $event) {
            $event->trigger();
        }
    }

    /**
     * Deletes the assignment and all associated records like user relation and logs entries
     * Deleting their assignment is only possible if it's a DRAFT or ARCHIVED assignment.
     * Triggers deletion events.
     *
     * @throws coding_exception
     */
    public function delete() {
        if ($this->entity->status !== assignment_entity::STATUS_DRAFT
            && $this->entity->status !== assignment_entity::STATUS_ARCHIVED
        ) {
            throw new coding_exception('Only draft or archived assignments can be deleted.');
        }

        $this->delete_assignment();
    }

    /**
     * Deletes the assignment without validating current status. Use with care.
     */
    public function force_delete() {
        $this->delete_assignment();
    }

    /**
     * Deletes the assignment and related records and trigger events
     */
    protected function delete_assignment() {
        // Create the event as long as we still have an id in the instance
        $event = assignment_deleted::create_from_assignment($this->entity);

        // TODO Change once real foreign keys are there as we don't need to delete related records manually
        builder::get_db()->transaction(function () {
            // Delete all user records for those assignments
            competency_assignment_user::repository()
                ->where('assignment_id', $this->get_id())
                ->delete();

            // Delete all log records for those assignments
            competency_assignment_user_log::repository()
                ->where('assignment_id', $this->get_id())
                ->delete();

            $this->entity->delete();
        });

        // TODO Delete related competency records?

        $event->trigger();
    }

    /**
     * Activation is only possible if assignment is a DRAFT
     *
     * @throws coding_exception
     * @return void
     */
    public function activate() {
        if ($this->entity->status !== assignment_entity::STATUS_DRAFT) {
            throw new coding_exception('Only draft assignments can be activated.');
        }

        $event = assignment_activated::create_from_assignment($this->entity);

        $this->entity->status = assignment_entity::STATUS_ACTIVE;
        $this->entity->save();

        $event->trigger();
    }

    /**
     * Validates and creates new assignment with given parameters. Returns a new model.
     *
     * @param int $competency_id
     * @param string $type
     * @param string $user_group_type
     * @param int $user_group_id
     * @param int $status
     * @return assignment|null
     * @throws assignment_create_exception
     */
    public static function create(
        int $competency_id,
        string $type,
        string $user_group_type,
        int $user_group_id,
        int $status = assignment_entity::STATUS_DRAFT
    ): ?self {
        // Validate assignment status
        if (!in_array($status, [assignment_entity::STATUS_DRAFT, assignment_entity::STATUS_ACTIVE], true)) {
            throw new assignment_create_exception('Invalid assignment status supplied');
        }

        // Validate assignment type
        if (!in_array($type, assignment_entity::get_available_types(), true)) {
            throw new assignment_create_exception('Invalid assignment type supplied');
        }

        /** @var competency $competency */
        $competency = competency::repository()->find($competency_id);
        if (!$competency || !$competency->visible) {
            throw new assignment_create_exception('Non-existent or invisible competency id given.');
        }

        if (!in_array($user_group_type, user_groups::get_available_types(), true)) {
            throw new assignment_create_exception('Invalid user group has been passed');
        }

        $allowed_user_only_types = [assignment_entity::TYPE_OTHER, assignment_entity::TYPE_SYSTEM, assignment_entity::TYPE_SELF];
        if ($user_group_type !== user_groups::USER && in_array($type, $allowed_user_only_types, true)) {
            throw new assignment_create_exception('Invalid combination of type and user_group_type given');
        }

        if (!$competency->can_assign($type)) {
            throw new assignment_create_exception('Competency cannot be be assigned by given type');
        }

        $class = "totara_assignment\\entities\\{$user_group_type}";
        if (!class_exists($class)) {
            throw new assignment_create_exception('Invalid user group has been passed');
        }

        /** @var entity|user|hierarchy_item $class */
        $repo = $repo = $class::repository()
            ->where('id', $user_group_id);

        // TODO: Cover the following condition with tests
        if ($class == user::class) {
            $repo->filter_by_not_deleted();
        } else if (is_subclass_of($class, hierarchy_item::class)) {
            $repo->set_filter((new hierarchy_item_visible())->set_value(true));
        }

        $user_group = $repo->one();
        if (empty($user_group)) {
            throw new assignment_create_exception('User group not found');
        }

        $user = user::logged_in();

        // do not create duplicates
        if (!self::assignment_exists(
            $type,
            $competency->id,
            $user_group_type,
            $user_group->id,
            $user->id
        )) {
            /** @var assignment_entity $assignment */
            $assignment = new assignment_entity();
            $assignment->type = $type;
            $assignment->competency_id = $competency->id;
            $assignment->user_group_type = $user_group_type;
            $assignment->user_group_id = $user_group->id;
            $assignment->optional = 0;
            $assignment->status = $status;
            $assignment->created_by = $user->id;
            $assignment->created_at = time();
            $assignment->updated_at = time();
            $assignment->archived_at = null;
            $assignment->save();

            assignment_created::create_from_assignment($assignment)->trigger();

            return new static($assignment);
        }

        return null;
    }

    /**
     * @param string $type
     * @param int $competency_id
     * @param string $user_group_type
     * @param int $user_group_id
     * @param int $user_id
     * @return bool
     */
    private static function assignment_exists(
        string $type,
        int $competency_id,
        string $user_group_type,
        int $user_group_id,
        int $user_id
    ): bool {
        // Check for duplicate
        $assignment = assignment_entity::repository()
            ->where('type', $type)
            ->where('competency_id', $competency_id)
            ->where('user_group_type', $user_group_type)
            ->where('user_group_id', $user_group_id);
        // There can be multiple other assignments from different creators
        if ($type === assignment_entity::TYPE_OTHER) {
            $assignment->where('created_by', $user_id);
        }
        return $assignment->count() > 0;
    }

    /**
     * Get assigned users
     *
     * @return collection
     */
    public function get_assigned_users(): collection {
        return $this->entity->assignment_users;
    }

    public function is_active(): bool {
        return $this->entity->status == assignment_entity::STATUS_ACTIVE;
    }

    public function is_draft(): bool {
        return $this->entity->status == assignment_entity::STATUS_DRAFT;
    }

    public function is_archived(): bool {
        return $this->entity->status == assignment_entity::STATUS_ARCHIVED;
    }

    public function get_status(): int {
        return $this->entity->status;
    }

    /**
     * Returns the associated competency entity
     *
     * TODO return model instead of entity?
     *
     * @return competency
     */
    public function get_competency(): competency {
        return $this->entity->competency;
    }

    /**
     * @return string
     */
    public function get_type(): string {
        return $this->entity->type;
    }

    /**
     * Get human readable type name which depends on the type and the user_group_type
     *
     * @return string
     */
    public function get_type_name(): string {
        // For all non user admin assignments use the appropriate group name
        if ($this->entity->type === assignment_entity::TYPE_ADMIN &&
            $this->entity->user_group_type !== user_groups::USER) {
            switch ($this->entity->user_group_type) {
                case user_groups::POSITION:
                case user_groups::ORGANISATION:
                    $type_name = get_string($this->entity->user_group_type, 'totara_hierarchy');
                    break;
                case user_groups::COHORT:
                    $type_name = get_string('cohort', 'totara_cohort');
                    break;
                default:
                    $type_name = get_string('user', 'moodle');
                    break;
            }
        } else {
            $type_name = get_string('assignment_type:' . $this->entity->type, 'tassign_competency');
        }

        return $type_name;
    }

    public function get_my_value(): ?proficiency_value {
        return proficiency_value::my_value($this->entity);
    }

    public function get_min_value(): proficiency_value {
        return proficiency_value::min_value($this->entity);
    }

    /**
     * Returns human readable status name
     *
     * @return string
     */
    public function get_status_name(): string {
        return get_string('status:'.$this->entity->status_name, 'tassign_competency');
    }

    /**
     * Returns user group model
     *
     * @return user_group
     */
    public function get_user_group(): user_group {
        return user_group_factory::create($this->entity);
    }

    /**
     * @return string
     */
    public function get_user_group_name(): string {
        return $this->get_user_group()->get_name();
    }

    /**
     * Get human readable name which does only return the actual group name for position, organisation and cohorts.
     * For self, other and system assignments it will return a fixed string.
     *
     * @return string
     */
    public function get_progress_name(): string {
        switch ($this->entity->type ?? null) {
            case assignment_entity::TYPE_ADMIN:
                if ($this->entity->user_group_type !== user_groups::USER) {
                    return $this->get_user_group_name();
                }
                return get_string('directly_assigned', 'tassign_competency');
            case assignment_entity::TYPE_OTHER:
                return get_string('directly_assigned', 'tassign_competency');
            case assignment_entity::TYPE_SYSTEM:
                return get_string('continuous_tracking', 'tassign_competency');
            case assignment_entity::TYPE_SELF:
                return get_string('assignment_type:self', 'tassign_competency');
            case assignment_entity::TYPE_LEGACY:
                return get_string('assignment_type:legacy', 'tassign_competency');
            default:
                return $this->get_user_group_name();
        }
    }

    /**
     * Gets human readable reason for assignment, we show
     * - the fullname of the assigner and role
     * - a fixed string for self and other
     * - the actual name of the user group + type for position, organisation and audience
     *
     * @return string
     * @throws coding_exception
     */
    public function get_reason_assigned(): string {
        $type = $this->entity->type;
        $user_group_type = $this->entity->user_group_type;

        switch (true) {
            case ($type === assignment_entity::TYPE_ADMIN && $user_group_type === user_groups::USER):
            case $type === assignment_entity::TYPE_OTHER:
                $assigner = $this->get_assigner();
                $name = fullname((object)$assigner->to_array());
                $role = $type === assignment_entity::TYPE_ADMIN ? 'admin' : 'manager';
                $role_string = get_string('assigner_role:'.$role, 'tassign_competency');
                $name .= " ({$role_string})";
                break;
            case $type === assignment_entity::TYPE_SYSTEM:
            case $type === assignment_entity::TYPE_SELF:
                $name = get_string('assignment_reason:'.$type, 'tassign_competency');
                break;
            default:
                $name = $this->get_user_group_name();
                $name .= " ({$this->get_type_name()})";
                break;
        }

        return $name;
    }

    public function get_assigner(): ?user {
        return $this->entity->assigner;
    }

    /**
     * Returns the value of the given field, throws exception if fields doesn't exist
     *
     * @param string $field
     * @return mixed|string|competency|user_group|entity|null
     */
    public function get_field(string $field) {
        switch ($field) {
            case 'status_name':
                return $this->get_status_name();

            case 'type_name':
                return $this->get_type_name();

            case 'user_group':
                return $this->get_user_group();

            case 'progress_name':
                return $this->get_progress_name();

            case 'reason_assigned':
                return $this->get_reason_assigned();

            case 'competency':
                return $this->get_competency();

            case 'assignment':
                return $this;

            case 'my_value':
                return $this->get_my_value();

            case 'min_value':
                return $this->get_min_value();

            // We fall back to the default if it's not there intentionally
            case 'assigned_at':
                if ($this->entity->relation_loaded('assignment_user')) {
                    // The relation might be loaded, but the related model does not always exist,
                    // for example there is no assignment user for archived assignments...
                    return $this->entity->assignment_user->created_at ?? null;
                }
            default:
                if (isset($this->entity->{$field})) {
                    return $this->entity->$field;
                }
                return null;
        }
    }

    /**
     * Get underlying entity
     *
     * @return assignment_entity
     */
    public function get_entity(): assignment_entity {
        return $this->entity;
    }

    /**
     * Check whether the model has a field
     *
     * @param string $field
     * @return bool
     */
    public function has_field(string $field): bool {
        $extra_fields = [
            'user_group',
            'competency',
            'status_name',
            'type_name',
            'progress_name',
            'reason_assigned',
            'assignment',
            'my_value',
            'min_value',
            'proficient',
            'archived_at',
            'assigned_at'
        ];

        return in_array($field, $extra_fields) || isset($this->entity->{$field});
    }

    /**
     * @param $name
     * @return mixed
     */
    public function __get($name) {
        return $this->get_field($name);
    }

    public function __isset($name) {
        return $this->has_field($name);
    }

    /**
     * Convert model to array, this currently converts only an entity to array
     * TODO consider wrapping it into whatever that might want to convert it to array differently.
     *
     * @return array
     */
    public function to_array(): array {
        return $this->entity->to_array();
    }

}