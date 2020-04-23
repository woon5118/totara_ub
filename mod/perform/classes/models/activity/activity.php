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
use core\orm\collection;
use core\orm\entity\model;
use mod_perform\entities\activity\activity as activity_entity;
use mod_perform\state\activity\active;
use mod_perform\state\activity\draft;
use mod_perform\state\state;
use mod_perform\state\state_aware;
use mod_perform\util;
use totara_core\relationship\relationship;

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
 * @property-read int $created_at
 * @property-read int $updated_at
 * @property-read collection|section[] $sections
 * @property-read collection|relationship[] $relationships
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
        'relationships',
        'can_potentially_activate',
        'status_name'
    ];

    public const NAME_MAX_LENGTH = 255;

    /**
     * @var activity_entity
     */
    protected $entity;

    protected static function get_entity_class(): string {
        return activity_entity::class;
    }

    public static function load_by_container_id(int $container_id): self {
        $entity = activity_entity::repository()
            ->where('course', $container_id)
            ->get()->first();

        return self::load_by_entity($entity);
    }

    /**
     * Checks whether the logged in (or given) user has the capability to create the activity.
     *
     * @param int|null                $userid
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
        // TODO TL-24773 leaving this as active for now as we do not have the interface
        //      to activate for now. Once this is there we need to change the default to draft
        $entity->status = $status ?? active::get_code();
        $entity->type_id = $type->id;

        return $DB->transaction(function () use ($entity, $modinfo, $container) {
            global $CFG, $USER;

            $entity->save();

            $modinfo->instanceid = $entity->id;
            $container->add_module($modinfo);

            $container_context = $container->get_context();
            if (!empty($CFG->performanceactivitycreatornewroleid) and !is_viewing($container_context)) {
                role_assign($CFG->performanceactivitycreatornewroleid, $USER->id, $container_context);
            }

            $activity = self::load_by_entity($entity);
            track::create($activity);

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
            if ($this->entity->relation_loaded('sections')) {
                $this->entity->load_relation('sections');
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
     * @param activity_entity $entity
     * @return string[]
     */
    protected static function get_validation_problems(activity_entity $entity): array {
        $problems = [];

        if (empty($entity->name)) {
            $problems[] = 'Name is required';
        }

        if (\core_text::strlen($entity->name) > self::NAME_MAX_LENGTH) {
            $problems[] = 'Name must be less than ' . self::NAME_MAX_LENGTH . ' characters';
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
        return $this->entity->sections->map_to(section::class);
    }

    /**
     * Get the relationships for this activity.
     *
     * @return collection|relationship[]
     */
    public function get_relationships(): collection {
        return $this->entity->relationships->map_to(relationship::class);
    }

    /**
     * Checks whether the current user can view the participation reporting
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
     * Returns whether this activity is still in draft state
     *
     * @return bool
     */
    public function is_draft(): bool {
        return $this->get_state()::get_code() === draft::get_code();
    }

    /**
     * Returns whether this activity is in active state
     *
     * @return bool
     */
    public function is_active(): bool {
        return $this->get_state()::get_code() === active::get_code();
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
        return $this->can_manage($user_id) && $this->get_state()->can_potentially_activate();
    }

    /**
     * Getter method for @see can_potentially_activate
     * @return bool
     */
    public function get_can_potentially_activate(): bool {
        return $this->can_potentially_activate();
    }

    /**
     * Can this activity be activated. This checks the capability,
     * the status and the conditions to activate an activity.
     *
     * @return bool
     */
    public function can_activate(): bool {
        return $this->can_potentially_activate() && $this->get_state()->can_activate();
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
        $this->get_state()->activate();
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function get_current_state_code(): int {
        return $this->entity->status;
    }

    /**
     * Get name representation of current status
     *
     * @return string
     */
    public function get_status_name(): string {
        return $this->get_state()->get_name();
    }

    /**
     * @inheritDoc
     */
    protected function update_state_code(state $state): void {
        $this->entity->status = $state::get_code();
        $this->entity->update();
    }

}