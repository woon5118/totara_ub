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
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Samantha Jayasinghe <samantha.jayasinghe@totaralearning.com>
 * @author Simon Coggins <simon.coggins@totaralearning.com>
 * @package mod_perform
 */

namespace mod_perform\models\activity;

use container_perform\perform as perform_container;
use core\entities\expandable;
use core\entities\user;
use core\orm\collection;
use core\orm\entity\model;
use mod_perform\data_providers\activity\activity_settings;
use mod_perform\entities\activity\activity as activity_entity;
use mod_perform\event\activity_deleted;
use mod_perform\models\activity\helpers\activity_clone;
use mod_perform\models\activity\helpers\activity_deletion;
use mod_perform\entities\activity\track as track_entity;
use mod_perform\entities\activity\track_assignment;
use mod_perform\state\activity\active;
use mod_perform\state\activity\draft;
use mod_perform\state\state;
use mod_perform\state\state_aware;
use mod_perform\user_groups\grouping;
use mod_perform\util;
use mod_perform\webapi\resolver\type\activity_state;
use totara_core\relationship\relationship;
use mod_perform\state\activity\activity_state as activity_status;

/**
 * Class activity
 *
 * The core performance activity object. It defines questions and users who can answer them.
 *
 * @property-read int $id ID
 * @property-read int $course
 * @property-read string $name
 * @property-read string $description
 * @property-read int $status
 * @property-read activity_state $state
 * @property-read int $type
 * @property-read int $created_at
 * @property-read int $updated_at
 * @property-read collection|section[] $sections
 * @property-read collection|relationship[] $relationships
 * @property-read collection|track[] $tracks
 * @property-read activity_settings $settings
 *
 * @package mod_perform\models\activity
 */
class activity extends model {

    use state_aware;

    protected $entity_attribute_whitelist = [
        'id',
        'course',
        'name',
        'description',
        'status',
        'created_at',
        'updated_at',
    ];

    protected $model_accessor_whitelist = [
        'type',
        'sections',
        'settings',
        'relationships',
        'tracks',
        'can_activate',
        'can_potentially_activate',
        'state_details'
    ];

    public const NAME_MAX_LENGTH = 1024;

    /**
     * @var activity_entity
     */
    protected $entity;

    protected static function get_entity_class(): string {
        return activity_entity::class;
    }

    /**
     * Gets a model object based on the mod perform container (course) id.
     *
     * @param int $container_id
     * @return static
     */
    public static function load_by_container_id(int $container_id): self {
        $entity = activity_entity::repository()
            ->where('course', $container_id)
            ->one(true);

        return self::load_by_entity($entity);
    }

    /**
     * Checks whether the logged in (or given) user has the capability to create the activity.
     *
     * @param int|null $userid
     * @param \context_coursecat|null $context
     *
     * @return bool
     */
    public static function can_create(int $userid = null, \context_coursecat $context = null): bool {
        global $USER;

        if (null == $userid) {
            // Including zero check
            $userid = $USER->id;
        }

        if (null == $context) {
            $categoryid = util::get_default_category_id();
            if (0 == $categoryid) {
                // Nope, this user is not able to add a performance activity.
                return false;
            }

            $context = \context_coursecat::instance($categoryid);
        }

        return has_capability('mod/perform:create_activity', $context, $userid) &&
            perform_container::can_create_instance($userid, $context);
    }

    /**
     * Checks whether the logged in (or given) user has the capability to manage this activity.
     *
     * @param int|null $userid
     *
     * @return bool
     */
    public function can_manage(int $userid = null): bool {
        global $USER;

        $userid = $userid ?? $USER->id;
        return has_capability('mod/perform:manage_activity', $this->get_context(), $userid);
    }

    /**
     * Create activity on perform container
     *
     * @param perform_container $container
     * @param string            $name
     * @param activity_type     $type
     * @param string|null       $description
     * @param int               $status
     *
     * @return static
     */
    public static function create(
        perform_container $container,
        string $name,
        activity_type $type,
        string $description = null,
        int $status = null
    ): self {
        global $DB;

        if ($status && $status !== draft::get_code() && $status != active::get_code()) {
            throw new \coding_exception('Invalid activity status given');
        }

        $modinfo = new \stdClass();
        $modinfo->modulename = 'perform';
        $modinfo->course = $container->id;
        $modinfo->name = $name;
        $modinfo->timemodified = time();
        $modinfo->visible = true;
        $modinfo->section = 0;
        $modinfo->groupmode = 0;
        $modinfo->groupingid = 0;

        $entity = new activity_entity();

        $entity->course = $container->id;
        $entity->name = $name;
        $entity->description = $description;
        $entity->status = $status ?? draft::get_code();
        $entity->type_id = $type->id;

        return $DB->transaction(function () use ($entity, $modinfo, $container) {
            global $CFG, $USER;

            $entity->save();

            $modinfo->instanceid = $entity->id;
            $container->add_module($modinfo);

            $container_context = $container->get_context();
            // If the user does not have the manage capability from a higher context already
            // assign him the role so that he can manage the activity in the future.
            if (!empty($CFG->performanceactivitycreatornewroleid)
                && !is_viewing($container_context, null, 'mod/perform:manage_activity')
            ) {
                role_assign($CFG->performanceactivitycreatornewroleid, $USER->id, $container_context);
            }

            $activity = self::load_by_entity($entity);

            return $activity;
        });
    }

    /**
     * Forces the model to reload its data from the repository.
     *
     * @param bool $with_relationships defaults to false, use with care as it triggers additional queries
     * @return activity
     */
    public function refresh(bool $with_relationships = false): self {
        $this->entity->refresh();
        if ($with_relationships) {
            if ($this->entity->relation_loaded('relationships')) {
                $this->entity->load_relation('relationships');
            }
            if ($this->entity->relation_loaded('sections_ordered')) {
                $this->entity->load_relation('sections_ordered');
            }
            if ($this->entity->relation_loaded('tracks')) {
                $this->entity->load_relation('tracks');
            }
        }
        return $this;
    }

    /**
     * Return the context object for this activity.
     */
    public function get_context(): \context_module {
        $cm = get_coursemodule_from_instance(
            'perform',
            $this->entity->id,
            $this->entity->course,
            false,
            MUST_EXIST
        );
        return \context_module::instance($cm->id);
    }

    public function update_general_info(string $name, ?string $description): self {
        $entity = $this->entity;
        $entity->name = $name;
        $entity->description = $description;

        self::validate($entity);

        $entity->update();

        return $this;
    }

    /**
     * @param activity_entity $entity
     * @return void
     * @throws \coding_exception
     */
    protected static function validate(activity_entity $entity): void {
        $problems = self::get_validation_problems($entity);

        if (count($problems) === 0) {
            return;
        }

        $formatted_problems = self::format_validation_problems($problems);

        throw new \coding_exception('The following errors need to be fixed: ' . $formatted_problems);
    }

    /**
     * TODO use/write a library or make this generic, or at least move this to it's own class.
     *
     * @param activity_entity $entity
     * @return string[]
     */
    protected static function get_validation_problems(activity_entity $entity): array {
        $problems = [];

        if (empty($entity->name) || ctype_space($entity->name)) {
            $problems[] = 'Name is required';
        }

        if (\core_text::strlen($entity->name) > self::NAME_MAX_LENGTH) {
            $problems[] = 'Name cannot be more than ' . self::NAME_MAX_LENGTH . ' characters';
        }

        return $problems;
    }

    /**
     * @param string[] $problems
     * @return string
     */
    protected static function format_validation_problems(array $problems): string {
        return '"' . implode('", "', $problems) . '"';
    }

    /**
     * Get the sections for this activity.
     *
     * @return collection|section[]
     */
    public function get_sections(): collection {
        return $this->entity->sections_ordered->map_to(section::class);
    }

    /**
     * Checks whether the current user can view the participation reporting
     *
     * @return bool
     */
    public function can_view_participation_reporting(): bool {
        return has_capability('mod/perform:view_participation_reporting', $this->get_context());
    }

    /**
     * Returns the activity type.
     *
     * @return activity_type the type.
     */
    public function get_type(): activity_type {
        return activity_type::load_by_entity($this->entity->type);
    }

    /**
     * Get the tracks of this activity.
     *
     * @return collection|track[]
     */
    public function get_tracks(): collection {
        return $this->entity->tracks->map_to(track::class);
    }

    /**
     * Returns whether this activity is still in draft state
     *
     * @return bool
     */
    public function is_draft(): bool {
        return $this->get_status_state()::get_code() === draft::get_code();
    }

    /**
     * Returns whether this activity is in active state
     *
     * @return bool
     */
    public function is_active(): bool {
        return $this->get_status_state()::get_code() === active::get_code();
    }

    /**
     * Can this activity be potentially activated, meaning that the user has
     * the correct capability and it is in the right state.
     * This does not check the conditions to make the change.
     *
     * @param int|null $user_id defaults to current user
     * @return bool
     */
    public function can_potentially_activate(int $user_id = null): bool {
        return $this->can_manage($user_id) && $this->get_status_state()->can_potentially_activate();
    }

    /**
     * Getter method for @see can_potentially_activate
     * @return bool
     */
    public function get_can_potentially_activate(): bool {
        return $this->can_potentially_activate();
    }

    public function get_state_details() {
        return $this->get_status_state();
    }

    /**
     * Can this activity be activated. This checks the capability,
     * the status and the conditions to activate an activity.
     *
     * @return bool
     */
    public function can_activate(): bool {
        return $this->can_potentially_activate() && $this->get_status_state()->can_activate();
    }

    /**
     * Getter method for @see can_activate
     * @return bool
     */
    public function get_can_activate(): bool {
        return $this->can_activate();
    }

    /**
     * Activate this activity if possible
     *
     * @return $this
     */
    public function activate(): self {
        $this->get_status_state()->activate();
        return $this;
    }

    /**
     * Get the number of users that will be assigned to this activity upon activation.
     *
     * @return int
     * @throws \coding_exception if the activity has already been activated
     */
    public function get_users_to_assign_count(): int {
        if (!$this->get_status_state()->can_activate()) {
            throw new \coding_exception("Activity {$this->id} can't be activated");
        }

        // Get all the assignments for this activity.
        /** @var track_assignment $track_assignments */
        $track_assignments = track_assignment::repository()
            ->select(['id', 'user_group_type', 'user_group_id'])
            ->join([track_entity::TABLE, 'track'], 'track_id', 'id')
            ->where('track.activity_id', $this->id)
            ->get();

        // Separate the user group types in order to query the users associated with the group.
        $user_groups = [];
        foreach ($track_assignments as $assignment) {
            $user_group_entity = grouping::get_entity_class_by_user_group_type($assignment->user_group_type);
            $user_groups[$user_group_entity][] = $assignment->user_group_id;
        }

        // Build the total user count by counting the expanded user group records.
        // User group id for users is just a user id, so it can't be expanded.
        $user_ids = [];
        if (isset($user_groups[user::class])) {
            $user_ids = [$user_groups[user::class]];
            unset($user_groups[user::class]);
        }
        // Expand each user group for the total user count.
        foreach ($user_groups as $user_group_type => $user_group_ids) {
            /** @var expandable $user_group_type */
            $user_ids[] = $user_group_type::expand_multiple($user_group_ids);
        }
        $user_ids = array_unique(array_merge(...$user_ids));

        return count($user_ids);
    }

    /**
     * @inheritDoc
     */
    public function get_current_state_code(string $state_type): int {
        return $this->entity->{$state_type};
    }

    /**
     * @inheritDoc
     */
    protected function update_state_code(state $state): void {
        $this->entity->{$state::get_type()} = $state::get_code();
        $this->entity->update();
    }

    /**
     * Checks whether the current user can delete this perform activity.
     *
     * @return bool
     */
    public function can_delete(): bool {
        return has_capability('mod/perform:manage_activity', $this->get_context());
    }

    /**
     * Delete the activity and the associated child models.
     * Will only delete elements that are not used by any other perform activities.
     *
     * An activity_deleted event will be triggered on successful deletions.
     * @see activity_deleted
     */
    public function delete(): void {
        (new activity_deletion($this))->delete();
    }

    /**
     * Get status state class.
     *
     * @return state
     */
    public function get_status_state(): state {
        return $this->get_state(activity_status::get_type());
    }

    /**
     * Returns the settings belonging to this activity.
     *
     * @return activity_settings the settings.
     */
    public function get_settings(): activity_settings {
        return new activity_settings($this);
    }

    /**
     * Clone the activity and the associated child models.
     * @see activity_clone
     *
     * @return activity
     */
    public function clone(): activity {
        return (new activity_clone($this))->clone();
    }
}