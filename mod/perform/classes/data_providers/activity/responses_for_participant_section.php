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
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Jaron Steenson <jaron.steenson@totaralearning.com>
 * @package mod_perform
 */

namespace mod_perform\data_providers\activity;

use core\orm\collection;
use mod_perform\entities\activity\element_response as element_response_entity;
use mod_perform\models\activity\element_response;
use mod_perform\entities\activity\participant_section as participant_section_entity;
use mod_perform\entities\activity\section_element as section_element_entity;
use mod_perform\entities\activity\participant_instance as participant_instance_entity;
use mod_perform\models\activity\participant_section;

class responses_for_participant_section {

    /** @var int */
    protected $participant_section_id;

    /** @var int */
    protected $user_id;

    /** @var collection|element_response[] */
    protected $items;

    /** @var participant_section_entity|null */
    protected $participant_section_entity;

    /**
     * responses_for_participant_section constructor.
     *
     * @param int $user_id The id of the user who wants to view or answer the section
     * @param int $participant_section_id The id of the participant section you want to fetch responses for
     */
    public function __construct(int $user_id, int $participant_section_id) {
        $this->user_id = $user_id;
        $this->participant_section_id = $participant_section_id;
    }

    public function fetch(): self {
        $this->participant_section_entity = $this->fetch_participant_section();

        // The participant section either doesn't exist or does not belong to the supplied participant id.
        // The front end or calling code will handle these cases the same way.
        if ($this->participant_section_entity === null) {
            $this->items = $this->no_items();
            return $this;
        }

        $participant_instance_id = $this->participant_section_entity->participant_instance->id;
        $section_element_ids = $this->participant_section_entity->section_elements->pluck('id');

        $existing_responses = $this->fetch_existing_responses($participant_instance_id, $section_element_ids);

        $this->items = $this->create_element_responses(
            $this->participant_section_entity->participant_instance,
            $this->participant_section_entity->section_elements,
            $existing_responses
        );

        return $this;
    }

    protected function fetch_participant_section(): ?participant_section_entity {
        return participant_section_entity::repository()
            ->as('ps')
            ->with(['section_elements', 'participant_instance'])
            // Ensure the user we are fetching responses for is a participant for the section they belong to.
            ->join([participant_instance_entity::TABLE, 'pi'], 'participant_instance_id', 'id')
            ->where('ps.id', $this->participant_section_id)
            ->where('pi.participant_id', $this->user_id)
            ->order_by('ps.id', 'desc')
            ->first();
    }

    /**
     * @param int $participant_instance_id
     * @param int[] $section_element_ids
     * @return collection
     */
    protected function fetch_existing_responses(int $participant_instance_id, array $section_element_ids): collection {
        return element_response_entity::repository()
            ->where_in('section_element_id', $section_element_ids)
            ->where('participant_instance_id', $participant_instance_id)
            ->get()
            ->key_by('section_element_id');
    }

    protected function no_items(): collection {
        return new collection();
    }

    /**
     * @return collection|element_response[]
     */
    public function get_responses(): collection {
        return $this->items;
    }

    public function get_participant_section(): ?participant_section {
        return $this->participant_section_entity ? new participant_section($this->participant_section_entity) : null;
    }

    private function create_element_responses(
        participant_instance_entity $participant_instance_entity,
        collection $section_elements,
        collection $existing_responses
    ): collection {
        return $section_elements->map(
            function (section_element_entity $section_element) use ($participant_instance_entity, $existing_responses) {
                // The element response model will accept missing entities in the case where a question has not yet been answered.
                $element_response_entity = $existing_responses->item($section_element->id);

                return new element_response($participant_instance_entity, $section_element, $element_response_entity);
            }
        );
    }

}