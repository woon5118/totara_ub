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
use core\orm\entity\model;
use core\orm\query\builder;
use mod_perform\entities\activity\participant_instance as participant_instance_entity;
use mod_perform\entities\activity\participant_instance;
use mod_perform\entities\activity\participant_section as participant_section_entity;
use mod_perform\entities\activity\section as section_entity;
use mod_perform\entities\activity\subject_instance as subject_instance_entity;
use mod_perform\state\participant_section\complete;
use mod_perform\state\state;
use mod_perform\state\state_aware;
use mod_perform\state\state_helper;

/**
 * Class participant_section
 *
 * @property-read int $id ID
 * @property-read int $section_id
 * @property-read int $participant_instance_id
 * @property-read int $progress
 * @property-read int $created_at
 * @property-read int $updated_at
 * @property-read participant_instance_entity $participant_instance
 * @property-read section $section
 *
 * @package mod_perform\models\activity
 */
class participant_section extends model {

    use state_aware;

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
        'element_responses',
    ];

    /**
     * @var participant_section_entity
     */
    protected $entity;

    /**
     * @var collection|element_response[]
     */
    protected $element_responses;

    /**
     * @inheritDoc
     */
    public static function get_entity_class(): string {
        return participant_section_entity::class;
    }

    public function get_section(): section {
        return section::load_by_entity($this->entity->section);
    }

    public function get_participant_instance(): participant_instance_entity {
        return $this->entity->participant_instance;
    }

    public function get_context(): context_module {
        /** @var subject_instance_entity $subject_instance_entity */
        $subject_instance_entity = $this->get_participant_instance()->subject_instance()->one();
        $subject_instance = new subject_instance($subject_instance_entity);

        return $subject_instance->get_context();
    }

    /**
     * @param collection|element_response[] $element_responses
     * @return self
     */
    public function set_element_responses(collection $element_responses): self {
        $this->element_responses = $element_responses;
        return $this;
    }

    /**
     * @return collection|element_response[]
     */
    public function get_element_responses(): collection {
        return $this->element_responses;
    }

    /**
     * Try to complete the section (save all answers, and set the progress status to complete).
     * If any element validation rules do not pass no data is saved and the progress status is not updated.
     *
     * Validation errors are accessible on each element response.
     *
     * @return bool true if no validation rules failed and the section was completed, false if any validation rules failed.
     * @see get_element_responses
     * @see element_response::get_validation_errors
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

            $this->get_state()->complete();
        });

        return true;
    }

    public function get_current_state_code(): int {
        return $this->progress;
    }

    protected function update_state_code(state $state): void {
        $this->entity->progress = $state::get_code();
        $this->entity->update();
    }
}
