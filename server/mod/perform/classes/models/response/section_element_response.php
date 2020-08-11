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
 * @package mod_perform
 */

namespace mod_perform\models\response;

use coding_exception;
use core\collection;
use core\orm\entity\model;
use mod_perform\entities\activity\section_element as section_element_entity;
use mod_perform\entities\activity\element_response as element_response_entity;
use mod_perform\models\activity\element;
use mod_perform\models\activity\participant_instance;
use mod_perform\models\activity\respondable_element_plugin;
use mod_perform\models\activity\section_element;

/**
 * Represents the response or lack of to a question or other element which
 * can be displayed to users within a performance activity.
 *
 * @property-read int section_element_id Foreign key
 * @property-read section_element $section_element The parent section element
 * @property-read collection|element_validation_error[] $validation_errors A collection of element_validation_errors
 * @property-read string $response_data Raw JSON encoded response data
 * @property-read element $element The element this is a response to
 * @property-read collection|participant_instance[] $visible_to
 * @property-read collection|responder_group[] $other_responder_groups
 *                Other responses grouped by relationship types (Manager/Appraiser)
 * @property-read int $sort_order The order this element should appear in the section
 * @package mod_perform\models\activity
 */
class section_element_response extends model {

    protected $entity_attribute_whitelist = [
        'response_data', // as a JSON encoded string
    ];

    protected $model_accessor_whitelist = [
        'section_element',
        'section_element_id',
        'element',
        'validation_errors',
        'participant_instance',
        'other_responder_groups',
        'visible_to',
        'sort_order',
    ];
    /**
     * @var collection|responder_group[]
     */
    protected $other_responder_groups;

    /**
     * @var participant_instance
     */
    private $participant_instance;

    /**
     * @var element_response_entity
     */
    protected $entity;

    /**
     * @var section_element_entity
     */
    protected $section_element;

    /**
     * @var collection
     */
    protected $validation_errors;

    /**
     * @inheritDoc
     */
    protected static function get_entity_class(): string {
        return element_response_entity::class;
    }

    /**
     * element_response constructor.
     *
     * @param participant_instance $participant_instance
     * @param section_element $section_element
     * @param element_response_entity|null $element_response_entity
     * @param collection|responder_group[] $other_responder_groups
     *
     * @throws coding_exception
     */
    public function __construct(
        participant_instance $participant_instance,
        section_element $section_element,
        ?element_response_entity $element_response_entity,
        collection $other_responder_groups
    ) {
        if ($element_response_entity === null) {
            $element_response_entity = new element_response_entity();
            $element_response_entity->participant_instance_id = $participant_instance->id;
            $element_response_entity->section_element_id = $section_element->id;
        } else {
            $this->verify_participant_instance($element_response_entity, $participant_instance);
            $this->verify_section_element($element_response_entity, $section_element);
        }

        $this->entity = $element_response_entity;
        $this->participant_instance = $participant_instance;
        $this->section_element = $section_element;
        $this->other_responder_groups = $other_responder_groups;
    }

    /**
     * Verifies element response is for the participant instance.
     *
     * @param $element_response_entity
     * @param $participant_instance
     *
     * @throws coding_exception
     */
    private function verify_participant_instance($element_response_entity, $participant_instance) {
        if ((int) $element_response_entity->participant_instance_id !== (int) $participant_instance->id) {
            throw new coding_exception(
                'participant_instance_id of the element response does not match the supplied participant instance'
            );
        }
    }

    /**
     * Verifies section_element is for element_response.
     *
     * @param $element_response_entity
     * @param $section_element_entity
     *
     * @throws coding_exception
     */
    private function verify_section_element($element_response_entity, $section_element_entity) {
        if ((int) $element_response_entity->section_element_id !== (int) $section_element_entity->id) {
            throw new coding_exception(
                'section_element_id of the element response does not match the supplied section element'
            );
        }
    }

    public function get_section_element(): section_element {
        return $this->section_element;
    }

    public function get_section_element_id() {
        return $this->section_element->id;
    }

    /**
     * @return collection|participant_instance[]
     */
    public function get_visible_to(): collection {
        // This is just stubbed for now.
        return new collection();
    }

    /**
     * Get the order this response should appear in the section.
     * @return int
     */
    public function get_sort_order(): int {
        return $this->section_element->sort_order;
    }

    /**
     * Get the main participant responding to this question.
     * @return participant_instance
     */
    public function get_participant_instance(): participant_instance {
        return $this->participant_instance;
    }

    /**
     * Set the raw JSON encoded response data.
     *
     * @param string $encoded_response_data
     * @return self
     */
    public function set_response_data(string $encoded_response_data): self {
        $this->entity->response_data = $encoded_response_data;
        return $this;
    }

    /**
     * Run the element plugin specific validation on the response data.
     * @return bool
     */
    public function validate_response(): bool {
        if (!$this->get_element()->get_element_plugin() instanceof respondable_element_plugin) {
            return true;
        }

        $this->validation_errors = $this->get_element()->get_element_plugin()->validate_response(
            $this->entity->response_data,
            $this->get_element()
        );

        return $this->validation_errors->count() === 0;
    }

    /**
     * Get validation errors produced by calling validate_response.
     * @see validate_response
     * @return collection|element_validation_error[]
     */
    public function get_validation_errors(): collection {
        return $this->validation_errors ?? new collection();
    }

    /**
     * @return $this
     */
    public function save(): self {
        $this->entity->save();

        return $this;
    }

    /**
     * Get the relationship
     * @return string
     */
    public function get_relationship_name(): string {
        return $this->participant_instance->core_relationship->get_name();
    }

    public function get_element(): element {
        $element = null;
        if ($this->section_element->element) {
            $element = $this->section_element->element;
        }

        return $element;
    }

    /**
     * Get the other participants responses grouped by relationship types (Manager/Appraiser).
     * @return collection
     */
    public function get_other_responder_groups(): ?collection {
        return $this->other_responder_groups;
    }
}
