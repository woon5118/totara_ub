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
 * @author Jaron Steenson <jaron.steenson@totaralearning.com>
 * @author Nathan Lewis <nathan.lewis@totaralearning.com>
 * @package mod_perform
 */

namespace mod_perform\models\activity;

use context_module;
use core\collection;
use core\entities\user;
use core\orm\entity\model;
use mod_perform\entities\activity\subject_instance as subject_instance_entity;
use mod_perform\state\state;
use mod_perform\state\state_aware;
use mod_perform\state\subject_instance\subject_instance_availability;
use mod_perform\state\subject_instance\subject_instance_progress;

/**
 * Class subject_instance
 *
 * This class represents a specific activity about a specific person (subject_instance)
 *
 * @property-read int $id
 * @property-read user $subject_user The user that this activity is about
 * @property-read int $subject_user_id The user id for the user this instance is about
 * @property-read int $progress The progress status code
 * @property-read int $availability The availability status code
 * @property-read activity $activity The top level perform activity this is an instance of
 * @property-read collection|participant_instance[] $participant_instances models created from participant_instance entities
 * @property-read string $progress_status internal name of current progress state
 * @property-read subject_instance_progress|state $progress_state Current progress state
 * @property-read subject_instance_availability|state $availability_state Current availability state
 *
 * @package mod_perform\models\activity
 */
class subject_instance extends model {

    use state_aware;

    protected $entity_attribute_whitelist = [
        'id',
        'subject_user',
        'subject_user_id',
        'progress',
        'availability',
        'created_at',
    ];

    protected $model_accessor_whitelist = [
        'activity',
        'participant_instances',
        'progress_status',
        'availability_status',
        'progress_state',
        'availability_state',
    ];

    /** @var subject_instance_entity */
    protected $entity;

    public function __construct(subject_instance_entity $subject_instance) {
        parent::__construct($subject_instance);
    }

    /**
     * @inheritDoc
     */
    protected static function get_entity_class(): string {
        return subject_instance_entity::class;
    }

    /**
     * @return activity The abstract perform activity that this user activity is an instance of
     */
    public function get_activity(): activity {
        $activity_entity = $this->entity->activity();

        return activity::load_by_entity($activity_entity);
    }

    /**
     * Get the context object for the overarching abstract perform activity (perform in the database).
     *
     * @return context_module
     */
    public function get_context(): context_module {
        return $this->get_activity()->get_context();
    }

    /**
     * Get internal name of current progress state.
     *
     * @return string
     */
    public function get_progress_status(): string {
        return $this->get_progress_state()->get_name();
    }

    /**
     * Get internal name of current availability state.
     *
     * @return string
     */
    public function get_availability_status(): string {
        return $this->get_availability_state()->get_name();
    }

    /**
     * Update progress status.
     *
     * Must be called when something happened that can affect the progress status.
     */
    public function update_progress_status() {
        /** @var subject_instance_progress $state */
        $state = $this->get_progress_state();
        $state->update_progress();
    }

    public function get_current_state_code(string $state_type): int {
        return $this->{$state_type};
    }

    protected function update_state_code(state $state): void {
        $this->entity->{$state::get_type()} = $state::get_code();
        $this->entity->update();
    }

    /**
     * @return participant_instance[]|collection
     */
    public function get_participant_instances(): collection {
        return $this->entity->participant_instances->map_to(participant_instance::class);
    }

    /**
     * Get progress state class.
     *
     * @return state
     */
    public function get_progress_state(): state {
        return $this->get_state(subject_instance_progress::get_type());
    }

    /**
     * Get the current availability state.
     *
     * @return subject_instance_availability|state
     */
    public function get_availability_state(): state {
        return $this->get_state(subject_instance_availability::get_type());
    }

}
