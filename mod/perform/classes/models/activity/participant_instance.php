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
 * @author Matthias Bonk <matthias.bonk@totaralearning.com>
 * @package mod_perform
 */

namespace mod_perform\models\activity;

use context_module;
use core\collection;
use core\entities\user;
use core\orm\entity\model;
use mod_perform\entities\activity\participant_instance as participant_instance_entity;
use mod_perform\entities\activity\participant_section as participant_section_entity;
use mod_perform\state\participant_instance\participant_instance_availability;
use mod_perform\state\participant_instance\participant_instance_progress;
use mod_perform\state\state;
use mod_perform\state\state_aware;
use totara_core\relationship\relationship;

/**
 * Class participant_instance
 *
 * @package mod_perform\models\activity
 *
 * @property-read int $id
 * @property-read int $progress
 * @property-read int $participant_id
 * @property-read subject_instance $subject_instance
 * @property-read int $subject_instance_id
 * @property-read collection|participant_section_entity[] $participant_sections
 * @property-read string $progress_status internal name of current progress state
 * @property-read participant_instance_progress|state $progress_state Current progress state
 * @property-read participant_instance_availability|state $availability_state Current availability state
 * @property-read string $relationship_name internal name of the participant instance's activity relationship
 */
class participant_instance extends model {

    use state_aware;

    /**
     * @var participant_instance_entity
     */
    protected $entity;

    protected $entity_attribute_whitelist = [
        'id',
        'progress',
        'availability',
        'participant_id',
        'participant_sections',
        'subject_instance_id',
    ];

    protected $model_accessor_whitelist = [
        'progress_status',
        'progress_state',
        'availability_state',
        'subject_instance',
        'participant',
        'relationship_name'
    ];

    public static function get_entity_class(): string {
        return participant_instance_entity::class;
    }

    public function get_current_state_code(string $state_type): int {
        return $this->{$state_type};
    }

    protected function update_state_code(state $state): void {
        $this->entity->{$state::get_type()} = $state::get_code();
        $this->entity->update();
    }

    public function get_subject_instance(): subject_instance {
        return subject_instance::load_by_entity($this->entity->subject_instance);
    }

    /**
     * Get the participant entity, for now always an internal user.
     */
    public function get_participant(): user {
        return $this->entity->participant_user;
    }

    /**
     * Get the context object for the overarching abstract perform activity (perform in the database).
     *
     * @return context_module
     */
    public function get_context(): context_module {
        return $this->get_subject_instance()->get_context();
    }

    /**
     * Update progress status according to section progress.
     */
    public function update_progress_status() {
        /** @var participant_instance_progress $state */
        $this->get_progress_state()->update_progress();
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
     * Get relationship name
     *
     * @return string
     */
    public function get_relationship_name(): string {
        $relationship_entity = $this->entity->activity_relationship->relationship;
        return (new relationship($relationship_entity))->get_name();
    }

    /**
     * Get progress state class.
     *
     * @return state
     */
    public function get_progress_state(): state {
        return $this->get_state(participant_instance_progress::get_type());
    }

    /**
     * Get the current availability state.
     *
     * @return participant_instance_availability|state
     */
    public function get_availability_state(): state {
        return $this->get_state(participant_instance_availability::get_type());
    }

}
