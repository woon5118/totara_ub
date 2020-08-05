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

namespace mod_perform\data_providers\response;

use coding_exception;
use core\collection;
use mod_perform\entities\activity\activity;
use mod_perform\entities\activity\element_response as element_response_entity;
use mod_perform\entities\activity\participant_instance;
use mod_perform\entities\activity\participant_instance as participant_instance_entity;
use mod_perform\entities\activity\participant_section as participant_section_entity;
use mod_perform\entities\activity as activity_entity;
use mod_perform\entities\activity\section_element as section_element_entity;
use mod_perform\models\activity\element;
use mod_perform\models\response\participant_section;
use mod_perform\models\response\responder_group;
use mod_perform\models\response\section_element_response;
use totara_core\entities\relationship as core_relationship_entity;
use totara_core\relationship\relationship as core_relationship_model;

class participant_section_with_responses {

    /** @var int */
    protected $participant_section_id;

    /** @var int */
    protected $user_id;

    /** @var collection|section_element_response[] */
    protected $main_section_element_responses;

    /** @var participant_section_entity|null */
    protected $participant_section_entity;

    /** @var participant_section|null */
    protected $participant_section;

    /** @var collection|section_element_response[] */
    protected $others_section_element_responses;

    /**@var string[] */
    private $other_participant_relationship_type_names;

    /**
     * responses_for_participant_section constructor.
     *
     * @param int $participant_id The id of the user who wants to view or answer the section.
     * @param int $participant_section_id The id of the participant section you want to fetch responses for.
     */
    public function __construct(int $participant_id, int $participant_section_id) {
        $this->user_id = $participant_id;
        $this->participant_section_id = $participant_section_id;
    }

    /**
     * Load the participant section and all child data into memory.
     *
     * @return $this
     */
    public function fetch(): self {
        $this->participant_section_entity = $this->fetch_participant_section();

        // The participant section either doesn't exist or does not belong to the supplied participant id.
        // The front end or calling code will handle these cases the same way,
        // the participant section does not exist as far as the user is concerned.
        if ($this->participant_section_entity === null) {
            return $this;
        }

        $other_participant_instances = $this->fetch_other_participant_instances();

        $participant_instance_ids = array_merge(
            [$this->participant_section_entity->participant_instance_id],
            $other_participant_instances->pluck('id')
        );

        $section_element_ids = $this->participant_section_entity->section_elements->pluck('id');

        $existing_responses = $this->fetch_existing_responses($participant_instance_ids, $section_element_ids);

        // Build the the responses from other participants.
        // We do this before building the main participants ($participant_id) responses because
        // these will be injected as child data to the main section_element_response models.
        $this->others_section_element_responses = $this->create_others_element_responses(
            $other_participant_instances,
            $this->participant_section_entity->section_elements,
            $existing_responses
        );

        // We get the main responses after the "others responses" because
        // the "others responses" are children of the main responses through
        // relationship groups.
        $this->main_section_element_responses = $this->create_section_elements(
            $this->participant_section_entity->participant_instance,
            $this->participant_section_entity->section_elements,
            $existing_responses,
            true,
            $this->is_anonymous_responses($this->participant_section_entity)
        );

        // Finally create the top level model and inject all the child models.
        $this->participant_section = new participant_section(
            $this->participant_section_entity,
            $this->main_section_element_responses
        );

        return $this;
    }

    /**
     * Get the fetched participant_section.
     *
     * @return participant_section|null
     */
    public function get(): ?participant_section {
        return $this->participant_section;
    }

    /**
     * Fetch the top level participant section entity with required relationships eagerly loaded.
     *
     * @return participant_section_entity|null
     */
    protected function fetch_participant_section(): ?participant_section_entity {
        return participant_section_entity::repository()
            ->as('ps')
            // Bulk fetch all related entities that are required to build the domain models.
            ->with('section_elements.element') // Used in section element_response class (element is for validation).
            ->with('participant_instance') // For section element response class.
            ->with('section.core_relationships.resolvers') // To create other responder groups.
            ->with('participant_instance.core_relationship.resolvers') // For excluding main participant in other responder groups.
            // Ensure the user we are fetching responses for is a participant for the section they belong to.
            ->join([participant_instance_entity::TABLE, 'pi'], 'ps.participant_instance_id', 'pi.id')
            ->where('ps.id', $this->participant_section_id)
            ->where('pi.participant_id', $this->user_id)
            ->one(false);
    }

    /**
     * Fetch the already entered responses for every question for both the main participant ($participant_id)
     * and other responder groups/participants.
     *
     * @param int[] $participant_instance_ids
     * @param int[] $section_element_ids
     * @return collection
     */
    protected function fetch_existing_responses(array $participant_instance_ids, array $section_element_ids): collection {
        return element_response_entity::repository()
            ->where_in('section_element_id', $section_element_ids)
            ->where_in('participant_instance_id', $participant_instance_ids)
            ->get();
    }

    /**
     * Fetch participant instances related to this participant section that are not the main participant ($participant_id).
     *
     * @return collection|participant_instance_entity[]
     */
    protected function fetch_other_participant_instances(): collection {
        $participant_instance = $this->participant_section_entity->participant_instance;

        return participant_instance_entity::repository()
            ->as('pi')
            // Bulk fetch all required related entities.
            ->with('core_relationship.resolvers') // Required for grouping section_element_responses by relationship_name.
            ->with('participant_user') // Required for the eventual output of other responders section_element_response models.
            ->join([participant_section_entity::TABLE, 'ps'], 'id', 'participant_instance_id')
            ->where('subject_instance_id', $participant_instance->subject_instance_id)
            // Guard on the actual participant_instance_id (rather than participant_user_id)
            // because we need to handle the case where the same user is both a manager and appraiser to the subject.
            ->where('pi.id', '!=', $this->participant_section_entity->participant_instance_id)
            ->where('ps.section_id', $this->participant_section_entity->section_id)
            ->get();
    }

    /**
     * @param participant_instance_entity $participant_instance_entity
     * @param collection $section_elements
     * @param collection $existing_responses
     * @param bool $include_other_responder_groups
     * @param bool $anonymous_responses
     * @return collection
     */
    protected function create_section_elements(
        participant_instance_entity $participant_instance_entity,
        collection $section_elements,
        collection $existing_responses,
        bool $include_other_responder_groups = false,
        bool $anonymous_responses = false
    ): collection {
        return $section_elements
            ->map(
                function (section_element_entity $section_element_entity) use (
                    $participant_instance_entity,
                    $existing_responses,
                    $include_other_responder_groups,
                    $anonymous_responses
                ) {
                    // The element response model will accept missing entities
                    // in the case where a question has not yet been answered.
                    $element_response_entity = $this->find_existing_response_entity(
                        $existing_responses,
                        $section_element_entity->id,
                        $participant_instance_entity->id
                    );

                    $is_respondable =  (new element($section_element_entity->element))->get_is_respondable();
                    $other_responder_groups = new collection();

                    if ($is_respondable &&
                        $include_other_responder_groups &&
                        $this->user_can_view_others_responses()
                    ) {
                        $other_responder_groups = $this->create_other_responder_groups($section_element_entity->id, $anonymous_responses);
                    }

                    return new section_element_response(
                        $participant_instance_entity,
                        $section_element_entity,
                        $element_response_entity,
                        $other_responder_groups,
                        null
                    );
                }
            );
    }

    /**
     * Find an element response based on it's candidate key (section_element_id and participant_instance_id).
     *
     * @param collection $existing_responses
     * @param int $section_element_id
     * @param int $participant_instance_id
     * @return element_response_entity|null
     */
    protected function find_existing_response_entity(
        collection $existing_responses,
        int $section_element_id,
        int $participant_instance_id
    ): ?element_response_entity {
        return $existing_responses->find(
            function (
                element_response_entity $existing_element_response
            ) use ($section_element_id, $participant_instance_id) {
                return (int) $existing_element_response->section_element_id === $section_element_id &&
                    (int) $existing_element_response->participant_instance_id === $participant_instance_id;
            }
        );
    }

    /**
     * Create the responses by other participants (not $participant_id) for a particular section element.
     *
     * @param collection|participant_instance_entity[] $other_participant_instances
     * @param collection $section_elements
     * @param collection $existing_responses
     * @return collection
     */
    protected function create_others_element_responses(
        collection $other_participant_instances,
        collection $section_elements,
        collection $existing_responses
    ): collection {
        if ($other_participant_instances->count() <= 0) {
            return new collection();
        }

        $others_responses = [];
        foreach ($other_participant_instances as $other_participant_instance) {
            $element_responses = $this->create_section_elements(
                $other_participant_instance,
                $section_elements,
                $existing_responses,
                false
            );

            array_push($others_responses, ...$element_responses);
        }

        return new collection($others_responses);
    }

    /**
     * Creates the other responder groups for a section element.
     * If a relationship is not resolved, i.e. a user does not have a manger,
     * a responder group for the type is still created but it will be empty.
     *
     * Note: this is not yet filtering based on "perform_section_relationship.can_answer".
     *
     * @param int $section_element_id
     * @param bool $anonymous_responses
     * @return collection|responder_group[]
     */
    protected function create_other_responder_groups(int $section_element_id, bool $anonymous_responses): collection {
        // Always create a group for the other participants relationship types.
        $grouped_by_relationship = [];
        if (!$anonymous_responses) {
            foreach ($this->get_other_participants_relationship_type_names() as $relationship_type_name) {
                $grouped_by_relationship[$relationship_type_name] = [];
            }
        }

        /** @var collection|section_element_response[] $others_responses */
        $others_responses = $this->others_section_element_responses->filter(
            function (section_element_response $other_response) use ($section_element_id) {
                return (int) $other_response->section_element_id === $section_element_id;
            }
        );

        foreach ($others_responses as $other_response) {
            $relationship_name = $other_response->get_relationship_name();

            // There is an edge case where the subject has more than one job assignment
            // and hence more than one manager or appraiser.
            // If we are fetching for one of the many manager/appraisers their relationship type name
            // will not have a group, as the relationship type name of the $participant_id
            // is excluded from the $this->other_participant_relationship_type_names array.

            if (!$anonymous_responses) {
                if (!array_key_exists($relationship_name, $grouped_by_relationship)) {
                    $grouped_by_relationship[$relationship_name] = [];
                }
                $grouped_by_relationship[$relationship_name][] = $other_response;
            }
            else {
                $grouped_by_relationship['anonymous'][] = $other_response;
            }

        }

        $other_responder_groups = new collection();
        foreach ($grouped_by_relationship as $relationship_name => $responses) {
            $other_responder_groups->append(new responder_group($relationship_name, new collection($responses)));
        }

        return $other_responder_groups;
    }

    /**
     * Lazily fetch all the relationship type names of the other participants.
     * Any double ups are removed.
     *
     * @return string[]
     */
    protected function get_other_participants_relationship_type_names(): array {
        if ($this->other_participant_relationship_type_names === null) {
            $core_relationship_entities = $this->participant_section_entity->section->core_relationships;

            $names = $core_relationship_entities->map(
                function (core_relationship_entity $core_relationship_entity) {
                    return (new core_relationship_model($core_relationship_entity))->get_name();
                }
            );

            $main_participant_relationship_name = $this->fetch_main_participant_relationship_name();

            $without_main_participant_relationship = $names->filter(
                function (string $relationship_type_name) use ($main_participant_relationship_name) {
                    return $relationship_type_name !== $main_participant_relationship_name;
                }
            );

            $this->other_participant_relationship_type_names = array_unique($without_main_participant_relationship->to_array());
        }

        return $this->other_participant_relationship_type_names;
    }

    /**
     * Fetch the relationship to the subject for the main participant ($participant_id).
     *
     * @return string
     * @throws coding_exception
     */
    protected function fetch_main_participant_relationship_name(): string {
        $main_participant_relationship_entity = $this->participant_section_entity
            ->participant_instance
            ->core_relationship;

        return (new core_relationship_model($main_participant_relationship_entity))->get_name();
    }

    /**
     * Can the user we are fetching the section view others' responses for this section.
     *
     * @return bool
     */
    protected function user_can_view_others_responses(): bool {
        return (new participant_section($this->participant_section_entity))->can_view_others_responses();
    }

    /**
     * Check is anonymous responses
     * @param participant_section_entity|null $participant_section_entity
     *
     * @return bool
     */
    protected function is_anonymous_responses(?participant_section_entity $participant_section_entity): bool {
        if ($participant_section_entity) {
            /**
             * @var activity
             */
            $activity = $participant_section_entity->section->activity;

            return $activity->anonymous_responses ?? false;
        }

        return false;
    }

}
