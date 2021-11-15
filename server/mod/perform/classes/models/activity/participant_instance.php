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
use core\orm\entity\model;
use mod_perform\controllers\activity\view_external_participant_activity;
use mod_perform\controllers\activity\view_user_activity;
use mod_perform\entity\activity\participant_instance as participant_instance_entity;
use mod_perform\models\response\participant_section;
use mod_perform\notification\factory;
use mod_perform\state\participant_instance\complete;
use mod_perform\state\participant_instance\open;
use mod_perform\state\participant_instance\participant_instance_availability;
use mod_perform\state\participant_instance\participant_instance_progress;
use mod_perform\state\participant_section\open as participant_section_open;
use mod_perform\state\state;
use mod_perform\state\state_aware;
use mod_perform\state\subject_instance\closed as subject_instance_closed;
use moodle_url;
use totara_core\relationship\relationship as relationship_model;

/**
 * Class participant_instance
 *
 * @package mod_perform\models\activity
 *
 * @property-read int $id
 * @property-read int $progress
 * @property-read int $participant_id
 * @property-read int $participant_source see participant_source model for constants
 * @property-read int $core_relationship_id
 * @property-read subject_instance $subject_instance
 * @property-read int $subject_instance_id
 * @property-read participant $participant
 * @property-read collection|participant_section[] $participant_sections
 * @property-read string $progress_status internal name of current progress state
 * @property-read moodle_url $participation_url
 * @property-read bool $anonymise_responses whether this participant's responses are to be anonymised
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
        'participant_source',
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
        'subject_instance',
        'participation_url',
        'anonymise_responses'
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
     * Get the participant user
     *
     * @return participant|null
     */
    public function get_participant(): ?participant {
        $participant_data = (int)$this->entity->participant_source === participant_source::INTERNAL
            ? $this->entity->participant_user
            : external_participant::load_by_entity($this->entity->external_participant);

        return new participant($participant_data, $this->entity->participant_source);
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
        return !$this->is_complete()
            && $this->subject_instance->is_overdue;
    }

    /**
     * Checks if participant instance is complete.
     *
     * @return bool
     */
    public function is_complete(): bool {
        return $this->get_progress_state() instanceof complete;
    }

    /**
     * Get the core relationship.
     *
     * @return relationship_model|null
     */
    public function get_core_relationship(): ?relationship_model {
        $relationship_entity = $this->entity->core_relationship;
        return $relationship_entity ? (new relationship_model($relationship_entity)) : null;
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

        return $this->is_for_user($USER->id);
    }

    /**
     * Returns true of this participant instance is for the specified user
     *
     * @param int $user_id
     * @return bool
     */
    public function is_for_user(int $user_id): bool {
        return (int) $this->entity->participant_source === participant_source::INTERNAL
            && (int) $this->participant_id === $user_id;
    }

    /**
     * Get url for a user to participate in this instance
     *
     * @return moodle_url
     */
    public function get_participation_url(): moodle_url {
        if ($this->participant_source == participant_source::EXTERNAL) {
            return view_external_participant_activity::get_url(['token' => $this->entity->external_participant->token]);
        } else {
            return view_user_activity::get_url(['participant_instance_id' => $this->id]);
        }
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
            if ($participant_section->get_availability_state() instanceof participant_section_open) {
                $participant_section->manually_close();
            }
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

        $dealer = factory::create_dealer_on_participant_instances([$this]);
        $dealer->dispatch('reopened');
    }

    /**
     * Indicates if this participant's responses are to be anonymised.
     *
     * @return bool true if the responses are to be anonymised.
     */
    public function get_anonymise_responses(): bool {
        return $this->subject_instance->activity->anonymous_responses;
    }

    /**
     * Checks whether the participant user of this instance is deleted
     *
     * @return bool
     */
    public function is_participant_deleted(): bool {
        return $this->participant->is_internal() && $this->participant->get_user()->deleted;
    }

    /**
     * Check if the subject user and the participant are not deleted
     *
     * @return bool
     */
    public function is_subject_or_participant_deleted(): bool {
        return $this->subject_instance->is_subject_user_deleted() || $this->is_participant_deleted();
    }

}
