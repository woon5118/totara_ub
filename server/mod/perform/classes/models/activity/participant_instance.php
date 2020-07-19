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

use coding_exception;
use context_module;
use core\collection;
use core\entities\user;
use core\orm\entity\model;
use mod_perform\entities\activity\participant_instance as participant_instance_entity;
use mod_perform\models\response\participant_section;
use mod_perform\state\participant_instance\closed;
use mod_perform\state\participant_instance\complete;
use mod_perform\state\participant_instance\open;
use mod_perform\state\participant_instance\participant_instance_availability;
use mod_perform\state\participant_instance\participant_instance_progress;
use mod_perform\state\state;
use mod_perform\state\state_aware;
use mod_perform\state\subject_instance\closed as subject_instance_closed;
use totara_core\relationship\relationship as relationship_model;

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
 * @property-read user $participant
 * @property-read collection|participant_section[] $participant_sections
 * @property-read string $progress_status internal name of current progress state
 * @property-read participant_instance_progress|state $progress_state Current progress state
 * @property-read participant_instance_availability|state $availability_state Current availability state
 * @property-read relationship_model $core_relationship The core relationship
 * @property-read bool $is_overdue
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
        'subject_instance_id',
        'core_relationship_id',
        'created_at',
    ];

    protected $model_accessor_whitelist = [
        'progress_status',
        'availability_status',
        'progress_state',
        'availability_state',
        'subject_instance',
        'participant',
        'core_relationship',
        'participant_sections',
        'is_for_current_user',
        'is_overdue',
        'subject_instance'
    ];

    protected static function get_entity_class(): string {
        return participant_instance_entity::class;
    }

    public function get_current_state_code(string $state_type): int {
        return $this->{$state_type};
    }

    public function should_anonymise(): bool {
        if ($this->get_is_for_current_user()) {
            return false;
        }

        return $this->entity
            ->subject_instance
            ->track
            ->activity
            ->anonymous_responses;
    }

    protected function update_state_code(state $state): void {
        $this->entity->{$state::get_type()} = $state::get_code();
        $this->entity->update();
    }

    public function get_subject_instance(): subject_instance {
        return subject_instance::load_by_entity($this->entity->subject_instance);
    }

    /**
     * Gets collection of participant sections.
     *
     * @return collection
     */
    public function get_participant_sections(): collection {
        return $this->entity->participant_sections->map_to(participant_section::class);
    }

    /**
     * Get the participant entity, for now always an internal user.
     */
    public function get_participant(): ?user {
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
     * Get internal name of current availability state.
     *
     * @return string
     */
    public function get_availability_status(): string {
        return $this->get_availability_state()->get_name();
    }

    /**
     * Checks if overdue
     *
     * @return bool
     */
    public function get_is_overdue(): bool {
        return !$this->is_completed()
            && $this->subject_instance->is_overdue;
    }

    /**
     * Checks if participant instance is completed.
     *
     * @return bool
     */
    private function is_completed(): bool {
        return $this->get_progress_state() instanceof complete;
    }

    /**
     * Get the core relationship.
     *
     * @return relationship_model
     */
    public function get_core_relationship(): ?relationship_model {
        $relationship_entity = $this->entity->core_relationship;
        return (new relationship_model($relationship_entity));
    }

    /**
     * Get progress state class.
     *
     * @return participant_instance_progress
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

    /**
     * Returns true of this participant instance is for the current user
     *
     * @return bool
     */
    public function get_is_for_current_user(): bool {
        global $USER;

        return $this->participant_id == $USER->id;
    }

    /**
     * Manually close the participant instance
     *
     * Related participant sections may be affected by this action.
     *
     * The following changes are applied, in this order:
     * - Change availability to "Closed"
     * - If progress is "Not yet started" or "In progress" then set progress to "Not submitted"
     * - Change participant sections availability to "Closed"
     * - If participant sections progress is "Not yet started" or "In progress" then set progress to "Not submitted"
     */
    public function manually_close(): void {
        if (!$this->get_availability_state() instanceof open) {
            throw new coding_exception('This function can only be called if the participant instance is open');
        }

        $this->get_availability_state()->close();
        // This will trigger an event which will end up calling $this->subject_instance->update_progress_status!
        $this->get_progress_state()->manually_complete();

        foreach ($this->participant_sections as $participant_section) {
            // This will trigger an event which will end up calling $this->update_progress_status!
            $participant_section->manually_close();
        }
    }

    /**
     * Manually open the participant instance
     *
     * Related participant sections and the subject instance may be affected by this action.
     *
     * The following changes are applied, in this order:
     * - Change participant sections availability to "Open"
     * - Recalculate participant sections progress, either "Not yet started" or "In progress"
     * - Change availability to "Open"
     * - Recalculate progress, either "Not yet started" or "In progress"
     * - Change subject instance availability to "Open"
     * - Recalculate subject instance progress, either "Not yet started" or "In progress"
     *
     * @param bool $open_parent
     * @param bool $open_children
     */
    public function manually_open(bool $open_parent = true, bool $open_children = true): void {
        if (!$this->get_availability_state() instanceof closed) {
            throw new coding_exception('This function can only be called if the participant instance is closed');
        }

        if ($open_children) {
            foreach ($this->participant_sections as $participant_section) {
                // This will trigger an event which will end up calling $this->update_progress_status!
                $participant_section->manually_open(false);
            }
        }

        $this->get_availability_state()->open();
        // This will trigger an event which will end up calling $this->subject_instance->update_progress_status!
        $this->get_progress_state()->manually_uncomplete();

        if ($open_parent) {
            $subject_instance = $this->subject_instance;
            if ($subject_instance->get_availability_state() instanceof subject_instance_closed) {
                $subject_instance->manually_open(false);
            }
        }
    }

}
