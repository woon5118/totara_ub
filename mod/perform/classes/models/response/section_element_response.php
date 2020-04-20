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
use mod_perform\entities\activity\participant_instance as participant_instance_entity;
use mod_perform\models\activity\element;
use mod_perform\models\activity\element_plugin;
use mod_perform\models\activity\participant_instance;
use mod_perform\models\activity\section_element;
use totara_core\relationship\relationship;

/**
 * Class element
 *
 * Represents the answer to a question or other intractable element which can be displayed to users within a performance activity.
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
        'section_element_id',
    ];

    protected $model_accessor_whitelist = [
        'section_element',
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
     * @var participant_instance_entity
     */
    private $participant_instance_entity;

    /**
     * @var element_response_entity
     */
    protected $entity;

    /**
     * @var section_element_entity
     */
    protected $section_element_entity;

    /**
     * @var collection
     */
    protected $validation_errors;

    /**
     * @var element_plugin
     */
    private $element_plugin;

    /**
     * @inheritDoc
     */
    protected static function get_entity_class(): string {
        return element_response_entity::class;
    }

    /**
     * element_response constructor.
     *
     * @param participant_instance_entity $participant_instance_entity
     * @param section_element_entity $section_element_entity
     * @param element_response_entity|null $element_response_entity
     * @param collection|responder_group[] $other_responder_groups
     * @param element_plugin|null $element_plugin
     * @throws coding_exception
     */
    public function __construct(
        participant_instance_entity $participant_instance_entity,
        section_element_entity $section_element_entity,
        ?element_response_entity $element_response_entity,
        collection $other_responder_groups = null,
        element_plugin $element_plugin = null
    ) {
        if ($element_response_entity === null) {
            $element_response_entity = new element_response_entity();
            $element_response_entity->participant_instance_id = $participant_instance_entity->id;
            $element_response_entity->section_element_id = $section_element_entity->id;
        } else if ((int) $element_response_entity->participant_instance_id !== (int) $participant_instance_entity->id) {
            throw new coding_exception(
                'participant_instance_id of the element response does not match the supplied participant instance'
            );
        } else if ((int) $element_response_entity->section_element_id !== (int) $section_element_entity->id) {
            throw new coding_exception(
                'section_element_id of the element response does not match the supplied section element'
            );
        }

        if ($element_plugin === null) {
            $element_plugin = (new element($section_element_entity->element))->get_element_plugin();
        }

        $this->entity = $element_response_entity;
        $this->participant_instance_entity = $participant_instance_entity;
        $this->section_element_entity = $section_element_entity;
        $this->element_plugin = $element_plugin;
        $this->other_responder_groups = $other_responder_groups;
    }

    public function get_section_element(): section_element {
        return new section_element($this->section_element_entity);
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
        return $this->section_element_entity->sort_order;
    }

    /**
     * Get the main participant responding to this question.
     * @return participant_instance
     */
    public function get_participant_instance(): participant_instance {
        return new participant_instance($this->participant_instance_entity);
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
        $this->validation_errors = $this->element_plugin->validate_response($this->entity->response_data);

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
        $relationship_entity = $this->participant_instance_entity->activity_relationship->relationship;
        return (new relationship($relationship_entity))->get_name();
    }

    public function get_element(): element {
        return (new element($this->section_element_entity->element));
    }

    /**
     * Get the other participants responses grouped by relationship types (Manager/Appraiser).
     * @return collection
     */
    public function get_other_responder_groups(): collection {
        return $this->other_responder_groups;
    }

}
