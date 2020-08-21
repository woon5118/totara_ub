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
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Jaron Steenson <jaron.steenson@totaralearning.com>
 * @package mod_perform
 */

namespace mod_perform\data_providers\response;

use coding_exception;
use core\collection;
use mod_perform\entities\activity\element_response as element_response_entity;
use mod_perform\entities\activity\participant_instance;
use mod_perform\entities\activity\participant_instance as participant_instance_entity;
use mod_perform\entities\activity\participant_section as participant_section_entity;
use mod_perform\models\activity\activity_setting;
use mod_perform\models\activity\participant_instance as participant_instance_model;
use mod_perform\models\activity\section_element;
use mod_perform\models\activity\section_relationship;
use mod_perform\models\activity\settings\visibility_conditions\visibility_manager;
use mod_perform\models\activity\settings\visibility_conditions\visibility_option;
use mod_perform\models\response\participant_section;
use mod_perform\models\response\responder_group;
use mod_perform\models\response\section_element_response;

class participant_section_with_responses {

    /** @var participant_section|null */
    private $participant_section;

    /** @var collection|section_element_response[] */
    private $others_section_element_responses;

    /** @var bool*/
    private $load_responses_for_submission = false;

    /**
     * responses_for_participant_section constructor.
     *
     * @param participant_section $participant_section
     */
    public function __construct(participant_section $participant_section) {
        $this->participant_section = $participant_section;
        $this->others_section_element_responses = new collection();
    }

    /**
     * Process only participant's response. Used for submitting responses.
     *
     * @return $this
     */
    public function process_for_response_submission(): self {
        $this->load_responses_for_submission = true;

        return $this;
    }

    /**
     * Builds the participant section and all child data into memory.
     *
     * @return participant_section
     */
    public function build(): participant_section {
        $action_data = $this->analyze_response_visibility_and_other_participants();

        // Build the the responses from other participants.
        // We do this before building the main participants ($participant_id) responses because
        // these will be injected as child data to the main section_element_response models.
        if ($action_data['include_responder_groups']) {
            $this->others_section_element_responses = $this->create_others_element_responses(
                $action_data['other_participant_instances'],
                $this->participant_section->section->section_elements,
                $action_data['existing_responses']
            );
        }

        // We get the main responses after the "others responses" because
        // the "others responses" are children of the main responses through
        // relationship groups.
        $main_section_element_responses = $this->create_section_element_responses(
            $this->participant_section->participant_instance,
            $this->participant_section->section->section_elements,
            $action_data['existing_responses'],
            $action_data['include_responder_groups']
        );

        // Finally add section_element_responses collection to participant_section.
        return $this->participant_section->set_element_responses($main_section_element_responses);
    }

    /**
     * Analyzes the response visibility and the other participants responses to include.
     *
     * @return array
     */
    private function analyze_response_visibility_and_other_participants(): array {
        $participant_instance_ids = [];

        if ($this->participant_section_relationship_can_answer()) {
            $participant_instance_ids[] = $this->participant_section->participant_instance_id;
        }

        $other_participant_instances = new collection();
        $process_other_responses = false;
        $participant_can_view = $this->participant_section_relationship_can_view();

        if (!$this->load_responses_for_submission && $participant_can_view) {
            $other_participant_instances = $this->fetch_other_participant_instances();
            $process_other_responses = $this->visibility_conditions_pass($other_participant_instances);
            if ($other_participant_instances->count() > 0) {
                array_push($participant_instance_ids, ...$other_participant_instances->pluck('id'));
            }
        }
        $existing_responses = $this->fetch_existing_responses(
            $participant_instance_ids,
            $this->participant_section->section->section_elements->pluck('id')
        );

        return [
            'existing_responses' => $existing_responses,
            'other_participant_instances' => $other_participant_instances->map_to(participant_instance_model::class),
            'participant_relationship_has_can_view_permissions' => $participant_can_view,
            'include_responder_groups' => $process_other_responses && $participant_can_view,
        ];
    }

    /**
     * Fetch the already entered responses for every question for both the main participant ($participant_id)
     * and other responder groups/participants.
     *
     * @param int[] $participant_instance_ids
     * @param int[] $section_element_ids
     * @return collection
     */
    private function fetch_existing_responses(array $participant_instance_ids, array $section_element_ids): collection {
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
    private function fetch_other_participant_instances(): collection {
        $participant_instance = $this->participant_section->participant_instance;

        return participant_instance_entity::repository()
            ->as('pi')
            // Bulk fetch all required related entities.
            ->with('core_relationship.resolvers') // Required for grouping section_element_responses by relationship_name.
            ->with('participant_user') // Required for the eventual output of other responders section_element_response models.
            ->with('participant_sections')
            ->join([participant_section_entity::TABLE, 'ps'], 'id', 'participant_instance_id')
            ->where('subject_instance_id', $participant_instance->subject_instance_id)
            // Guard on the actual participant_instance_id (rather than participant_user_id)
            // because we need to handle the case where the same user is both a manager and appraiser to the subject.
            ->where('pi.id', '!=', $this->participant_section->participant_instance_id)
            ->where('ps.section_id', $this->participant_section->section_id)
            ->get();
    }

    /**
     * @param participant_instance_model $participant_instance
     * @param collection $section_elements
     * @param collection $existing_responses
     * @param bool $include_other_responder_groups
     *
     * @return collection|section_element_response[]
     */
    private function create_section_element_responses(
        participant_instance_model $participant_instance,
        collection $section_elements,
        collection $existing_responses,
        bool $include_other_responder_groups = false
    ): collection {
        if ($this->load_responses_for_submission) {
            $section_elements = $section_elements->filter(function (section_element $section_element_entity) {
                // We are only interested in respondable elements
                return $section_element_entity->element->is_respondable;
            });
        }

        return $section_elements->map(
            function (section_element $section_element) use (
                $participant_instance,
                $existing_responses,
                $include_other_responder_groups
            ) {
                // The element response model will accept missing entities
                // in the case where a question has not yet been answered.
                $element_response_entity = $this->find_existing_response_entity(
                    $existing_responses,
                    $section_element->id,
                    $participant_instance->id
                );

                $other_responder_groups = new collection();

                if ($include_other_responder_groups && $section_element->element->is_respondable) {
                    $other_responder_groups = $this->create_other_responder_groups($section_element->id);
                }

                return new section_element_response(
                    $participant_instance,
                    $section_element,
                    $element_response_entity,
                    $other_responder_groups
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
     * @param collection|participant_instance_model[] $other_participant_instances
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
            $element_responses = $this->create_section_element_responses(
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
     * @return collection|responder_group[]
     */
    private function create_other_responder_groups(int $section_element_id): collection {
        // Always create a group for the other participants relationship types.
        $grouped_by_relationship = [];
        $anonymous_responses = $this->participant_section->section->activity->anonymous_responses;
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
            } else {
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
    private function get_other_participants_relationship_type_names(): array {
        $section_relationships = $this->participant_section->section->section_relationships;

        $names = $section_relationships->map(
            function (section_relationship $section_relationship) {
                return $section_relationship->core_relationship->get_name();
            }
        );

        $main_participant_relationship_name = $this->fetch_main_participant_relationship_name();
        $without_main_participant_relationship = $names->filter(
            function (string $relationship_type_name) use ($main_participant_relationship_name) {
                return $relationship_type_name !== $main_participant_relationship_name;
            }
        );

        return array_unique($without_main_participant_relationship->to_array());
    }

    /**
     * Fetch the relationship to the subject for the main participant ($participant_id).
     *
     * @return string
     * @throws coding_exception
     */
    private function fetch_main_participant_relationship_name(): string {
        return $this->participant_section
            ->participant_instance
            ->core_relationship->get_name();
    }

    /**
     * Checks if section relationship for the participant has can_view permissions.
     * @return bool
     * @throws coding_exception
     */
    private function participant_section_relationship_can_view(): bool {
        return $this->participant_section->can_view_others_responses();
    }

    /**
     * Checks if section relationship for the participant has can_answer permissions.
     *
     * @return bool
     * @throws coding_exception
     */
    private function participant_section_relationship_can_answer(): bool {
        return $this->participant_section->can_answer();
    }

    /**
     * Checks if visibility conditions on activity allow viewing other responses.
     *
     * @param collection|participant_instance[] $participant_instances
     * @return bool
     */
    private function visibility_conditions_pass(collection $participant_instances): bool {
        $visibility_option = $this->get_activity_visibility_option();

        return $visibility_option->show_responses($this->participant_section->participant_instance, $participant_instances);
    }

    /**
     * Get the activity visibility setting.
     *
     * @return visibility_option
     */
    private function get_activity_visibility_option(): visibility_option {
        $activity_settings = $this->participant_section->section->activity->get_settings();
        $visibility_value = $activity_settings->lookup(activity_setting::VISIBILITY_CONDITION) ?? 0;

        return (new visibility_manager())->get_option_with_value($visibility_value);
    }
}
