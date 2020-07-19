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

namespace mod_perform\models\activity\helpers;

use core\orm\query\builder;
use mod_perform\entities\activity\activity as activity_entity;
use mod_perform\entities\activity\element as element_entity;
use mod_perform\entities\activity\element_response as element_response_entity;
use mod_perform\entities\activity\participant_section as participant_section_entity;
use mod_perform\entities\activity\section as section_entity;
use mod_perform\entities\activity\section_element as section_element_entity;
use mod_perform\entities\activity\section_relationship as section_relationship_entity;
use mod_perform\entities\activity\track as track_entity;
use mod_perform\entities\activity\track_user_assignment;
use mod_perform\entities\activity\track_user_assignment_via;
use mod_perform\event\activity_deleted;
use mod_perform\models\activity\activity;

/**
 * Class activity_deletion
 * Responsible for handling the deletion of a perform activity and it's child records.
 *
 * This class is not responsible for deleting the associated perform container and contexts.
 *
 * @see \container_perform\perform::delete()
 */
class activity_deletion {

    /**
     * @var activity
     */
    protected $activity;

    public function __construct(activity $activity) {
        $this->activity = $activity;
    }

    /**
     * Delete the activity and the associated child models.
     * Will only delete elements that are not used by any other perform activities.
     *
     * An activity_deleted event will be triggered on successful deletions.
     *
     * @return activity_deletion
     * @see activity_deleted
     */
    public function delete(): self {
        builder::get_db()->transaction(function () {
            $delete_event = activity_deleted::create_from_activity($this->activity);

            // Fetch the response ids and track ids in one round trip (orm doesn't support joins in deletes).
            [
                $response_ids,
                $track_ids,
                $participant_section_ids,
                $section_relationship_ids
            ] = $this->fetch_ids();

            // Must be deleted first due to foreign key constraints.
            $this->delete_section_relationships($section_relationship_ids);
            $this->delete_participant_sections($participant_section_ids);
            $this->delete_responses($response_ids); // Not linked so must be manually deleted.
            $this->delete_user_assignments($track_ids); // Must be deleted first due to foreign key constraints.

            // Delete any elements that are directly owned by this activity (through shared context).
            $this->delete_own_elements();

            // Cascading delete will also delete rows fom the following tables:
            // - perform
            // - perform_track
            // - perform_subject_instance
            // - perform_section
            // - perform_section_element
            activity_entity::repository()->where('id', $this->activity->get_id())->delete();

            $delete_event->trigger();
        });

        return $this;
    }

    /**
     * Fetch ids of child records eligible for delete in one query.
     *
     * @return array
     */
    protected function fetch_ids(): array {
        $ids = builder::create()
            ->select(
                [
                    'section_element.element_id',
                    'response.id as response_id',
                    'track.id as track_id',
                    'participant_section.id as participant_section_id',
                    'section_relationship.id as section_relationship_id'
                ]
            )
            ->from(activity_entity::TABLE, 'activity')
            ->left_join([track_entity::TABLE, 'track'], 'track.activity_id', 'activity.id')
            ->left_join([section_entity::TABLE, 'section'], 'section.activity_id', 'activity.id')
            ->left_join([section_element_entity::TABLE, 'section_element'], 'section_element.section_id', 'section.id')
            ->left_join([section_relationship_entity::TABLE, 'section_relationship'], 'section_relationship.section_id', 'section.id')
            ->left_join([participant_section_entity::TABLE, 'participant_section'], 'participant_section.section_id', 'section.id')
            ->left_join([element_entity::TABLE, 'element'], 'section_element.element_id', 'element.id')
            ->left_join([element_response_entity::TABLE, 'response'], 'response.section_element_id', 'section_element.id')
            ->where('activity.id', $this->activity->id)
            ->get(true);

        // Remove nulls and pluck ids.
        $response_ids = $ids->filter('response_id', true, false)->pluck('response_id');
        $track_ids = $ids->filter('track_id', true, false)->pluck('track_id');
        $participant_section_ids = $ids->filter('participant_section_id', true, false)->pluck('participant_section_id');
        $section_relationship_ids = $ids->filter('section_relationship_id', true, false)->pluck('section_relationship_id');

        return [
            array_unique($response_ids),
            array_unique($track_ids),
            array_unique($participant_section_ids),
            array_unique($section_relationship_ids)
        ];
    }

    /**
     * Delete a list of response records records.
     *
     * @param array $response_ids
     */
    protected function delete_responses(array $response_ids): void {
        if (count($response_ids) === 0) {
            return;
        }

        // The orm/builder doesn't support joins in deletes,
        // so we just pull out all the ids with a query and delete in the next.
        builder::create()
            ->from(element_response_entity::TABLE, 'response')
            ->where('id', $response_ids)
            ->delete();
    }

    /**
     * Delete a list of user assignments based on track ids.
     *
     * @param array $track_ids
     */
    protected function delete_user_assignments(array $track_ids): void {
        if (count($track_ids) === 0) {
            return;
        }

        $this->delete_user_assignments_via($track_ids);

        // The orm/builder doesn't support joins in deletes,
        // so we just pull out all the ids with a query and delete in the next.
        builder::create()
            ->from(track_user_assignment::TABLE, 'track_user_assignment')
            ->where('track_id', $track_ids)
            ->delete();
    }

    /**
     * Delete a list of user assignment via based on track ids.
     *
     * @param array $track_ids
     */
    protected function delete_user_assignments_via(array $track_ids): void {
        $ids = builder::create()
            ->select(
                [
                    'track_user_assignment.id as track_user_assignment_id'
                ]
            )
            ->from(track_user_assignment::TABLE, 'track_user_assignment')
            ->where('track_id', $track_ids)
            ->get(true);

        // Remove nulls and pluck ids.
        $track_user_assignment_ids = array_unique(
            $ids->filter('track_user_assignment_id', true, false)
                ->pluck('track_user_assignment_id')
        );
        if (count($track_user_assignment_ids) === 0) {
            return;
        }
        // The orm/builder doesn't support joins in deletes,
        // so we just pull out all the ids with a query and delete in the next.
        builder::create()
            ->from(track_user_assignment_via::TABLE)
            ->where('track_user_assignment_id', $track_user_assignment_ids)
            ->delete();
    }

    /**
     * Delete elements that share the same context as this element.
     */
    protected function delete_own_elements(): void {
        builder::create()
            ->from(element_entity::TABLE, 'element')
            ->where('context_id', $this->activity->get_context()->id)
            ->delete();
    }

    /**
     * Delete a list of participant section records.
     *
     * @param array $participant_section_ids
     */
    protected function delete_participant_sections(array $participant_section_ids): void {
        if (count($participant_section_ids) === 0) {
            return;
        }

        // The orm/builder doesn't support joins in deletes,
        // so we just pull out all the ids with a query and delete in the next.
        builder::create()
            ->from(participant_section_entity::TABLE, 'participant_section')
            ->where('id', $participant_section_ids)
            ->delete();
    }

    /**
     * Delete a list of section relationship records.
     *
     * @param array $section_relationship_ids
     */
    protected function delete_section_relationships(array $section_relationship_ids): void {
        if (count($section_relationship_ids) === 0) {
            return;
        }

        // The orm/builder doesn't support joins in deletes,
        // so we just pull out all the ids with a query and delete in the next.
        builder::create()
            ->from(section_relationship_entity::TABLE, 'section_relationship')
            ->where('id', $section_relationship_ids)
            ->delete();
    }
}
