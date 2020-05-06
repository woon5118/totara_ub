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

namespace mod_perform\models\response;

use coding_exception;
use context_module;
use core\collection;
use core\orm\entity\model;
use core\orm\query\builder;
use mod_perform\entities\activity\participant_section as participant_section_entity;
use mod_perform\models\activity\participant_instance;
use mod_perform\models\activity\section;
use mod_perform\state\participant_section\participant_section_progress;
use mod_perform\state\state;
use mod_perform\state\state_aware;

/**
 * Class participant_section
 *
 * @property-read int $id ID
 * @property-read int $section_id
 * @property-read int $participant_instance_id
 * @property-read int $progress
 * @property-read int $created_at
 * @property-read int $updated_at
 * @property-read string $progress_status
 * @property-read participant_instance $participant_instance
 * @property-read section $section
 * @property-read section_element_response[] $section_element_responses
 * @property-read participant_instance[] answerable_participant_instances
 *
 * @package mod_perform\models\activity
 */
class participant_section extends model {

    use state_aware;

    /**
     * @var participant_section_entity
     */
    protected $entity;

    /**
     * @var collection|section_element_response[]
     */
    protected $element_responses;

    protected $entity_attribute_whitelist = [
        'id',
        'section_id',
        'participant_instance_id',
        'progress',
        'created_at',
        'updated_at',
        'participant_instance',
    ];

    protected $model_accessor_whitelist = [
        'section',
        'section_element_responses',
        'progress_status',
        'participant_instance',
        'section_element_responses',
        'progress_status',
        'participant_instance',
        'answerable_participant_instances',
    ];

    /**
     * participant_section constructor.
     *
     * @param participant_section_entity $entity
     * @param collection $element_responses
     * @throws coding_exception
     */
    public function __construct(participant_section_entity $entity, collection $element_responses = null) {
        parent::__construct($entity);
        $this->element_responses = $element_responses ?? new collection();
    }

    /**
     * @inheritDoc
     */
    public static function get_entity_class(): string {
        return participant_section_entity::class;
    }

    public function get_section(): section {
        return section::load_by_entity($this->entity->section);
    }

    public function get_participant_instance(): participant_instance {
        return participant_instance::load_by_entity($this->entity->participant_instance);
    }

    /**
     * Get all participant instances (same end user, different relationship),
     * that this section can be answered by the linked participant instance.
     *
     * @return collection|participant_instance
     */
    public function get_answerable_participant_instances(): collection {
        return $this->entity->get_answerable_participant_instances()->map_to(participant_instance::class);
    }

    public function get_context(): context_module {
        return $this->get_participant_instance()->get_subject_instance()->get_context();
    }

    /**
     * @param collection|section_element_response[] $element_responses
     * @return self
     */
    public function set_element_responses(collection $element_responses): self {
        $this->element_responses = $element_responses;
        return $this;
    }

    /**
     * @return collection|section_element_response[]
     */
    public function get_section_element_responses(): collection {
        return $this->element_responses;
    }

    /**
     * Update the response data held in memory on this instance.
     *
     * @param array $update_request_payload
     * @return $this
     * @throws coding_exception
     */
    public function set_responses_data_from_request(array $update_request_payload): self {
        foreach ($update_request_payload as $update) {
            $section_element_id = $update['section_element_id'];
            $updated_response_data = $update['response_data'];

            /** @var section_element_response $existing_element_response */
            $existing_element_response = $this->find_element_response($section_element_id);

            if ($existing_element_response === null) {
                throw new coding_exception(sprintf('Section element not found for id %d', $section_element_id));
            }

            $existing_element_response->set_response_data($updated_response_data);
        }

        return $this;
    }

    /**
     * Find an element response based on section element id.
     *
     * @param int $section_element_id
     * @return mixed|null
     */
    public function find_element_response(int $section_element_id): ?section_element_response {
        return $this->element_responses->find(
            function (section_element_response $element_response) use ($section_element_id) {
                return (int) $element_response->section_element_id === $section_element_id;
            }
        );
    }

    /**
     * Try to complete the section (save all answers, and set the progress status to complete).
     * If any element validation rules do not pass no data is saved and the progress status is not updated.
     *
     * Validation errors are accessible on each element response.
     *
     * @return bool true if no validation rules failed and the section was completed, false if any validation rules failed.
     * @see get_section_element_responses
     * @see section_element_response::get_validation_errors
     * @see set_element_responses
     */
    public function complete(): bool {
        $has_validation_error = false;

        foreach ($this->element_responses as $element_response) {
            $has_validation_error = !$element_response->validate_response();
        }

        // There was at least one validation problem, don't save anything.
        if ($has_validation_error) {
            return false;
        }

        // Validation has passed save all the responses, and ensure the progress status is set to complete.
        builder::get_db()->transaction(function () {
            foreach ($this->element_responses as $element_response) {
                $element_response->save();
            }

            /** @var participant_section_progress $state */
            $state = $this->get_state();
            $state->complete();
        });

        return true;
    }

    /**
     * Called when a participant has accessed this section.
     */
    public function on_participant_access() {
        /** @var participant_section_progress $state */
        $state = $this->get_state();
        $state->on_participant_access();
    }

    public function get_current_state_code(): int {
        return $this->progress;
    }

    protected function update_state_code(state $state): void {
        $this->entity->progress = $state::get_code();
        $this->entity->update();
    }

    /**
     * Get internal name of current progress state.
     *
     * @return string
     */
    public function get_progress_status(): string {
        return $this->get_state()->get_name();
    }

}
