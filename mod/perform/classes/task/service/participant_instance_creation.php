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
 * @author Kunle Odusan <kunle.odusan@totaralearning.com>
 * @package mod_perform
 */

namespace mod_perform\task\service;

use core\collection;
use core\orm\query\builder;
use mod_perform\entities\activity\activity_relationship;
use mod_perform\entities\activity\participant_instance as participant_instance_entity;
use mod_perform\hook\participant_instances_created;
use mod_perform\state\participant_instance\not_started;
use stdClass;
use totara_core\relationship\helpers\relationship_collection_manager;

/**
 * Class participation_service used to create participant instances for a collection of subject instances.
 * @package mod_perform\task\service
 */
class participant_instance_creation {

    /**
     * List of participant instances to be created for bulk insert.
     *
     * @var collection | array[]
     */
    private $participation_creation_list = [];

    /**
     * Maximum number of participants aggregated before bulk insert.
     *
     * @var int
     */
    private $buffer_count = BATCH_INSERT_MAX_ROW_COUNT;

    /**
     * Generates participant instances for a collection of subject instances.
     *
     * @param collection|subject_instance_dto[] $subject_instance_dtos
     *
     * @return void
     */
    public function generate_instances(collection $subject_instance_dtos): void {
        builder::get_db()->transaction(
            function () use ($subject_instance_dtos) {
                $this->aggregate_participant_instances($subject_instance_dtos);
                $this->save_data();
            }
        );
    }

    /**
     * Aggregates participant instances to use for bulk insert.
     *
     * @param collection|subject_instance_dto[] $subject_instance_dtos
     *
     * @return void
     */
    private function aggregate_participant_instances(collection $subject_instance_dtos): void {
        $activity_ids = array_unique($subject_instance_dtos->pluck('activity_id'), SORT_NUMERIC);
        $activity_relationships = $this->get_activity_relationships($activity_ids);
        $core_relationship_ids = array_unique($activity_relationships->pluck('core_relationship_id'));

        if (empty($core_relationship_ids)) {
            return;
        }
        $relationship_manager = new relationship_collection_manager($core_relationship_ids);
        $relationships_per_activity = $this->group_relationship_ids_by_activity($activity_relationships);

        foreach ($subject_instance_dtos as $subject_instance) {
            $has_no_relationships_for_activity = !isset($relationships_per_activity[$subject_instance->activity_id]);

            if ($has_no_relationships_for_activity) {
                continue;
            }
            $relationship_arguments = $this->build_relationship_arguments($subject_instance);

            $participant_ids_for_relationships = $relationship_manager->get_users_for_relationships(
                $relationship_arguments,
                array_column($relationships_per_activity[$subject_instance->activity_id], 'core_relationship_id')
            );
            $relationship_data = [
                'activity_relationships' => $relationships_per_activity[$subject_instance->activity_id],
                'subject_instance' => $subject_instance,
                'participant_ids' => $participant_ids_for_relationships,
            ];
            $this->create_participant_instances_for_relationships($relationship_data);
        }
    }

    /**
     * Get relationships for all activities.
     *
     * @param array $activity_ids
     * @return collection
     */
    private function get_activity_relationships(array $activity_ids): collection {
        return activity_relationship::repository()
            ->where_in('activity_id', $activity_ids)
            ->get();
    }

    /**
     * Get activity relationships for subject instance.
     *
     * @param collection $activity_relationships
     * @return array
     */
    private function group_relationship_ids_by_activity(collection $activity_relationships): array {
        $relationships_per_activity = [];

        foreach ($activity_relationships as $activity_relationship) {
            $relationships_per_activity[$activity_relationship->activity_id][] = $activity_relationship;
        }

        return $relationships_per_activity;
    }

    /**
     * Builds relationship class arguments from subject instance id.
     *
     * @param subject_instance_dto $subject_instance
     * @param array $activity_relationships
     * @return array
     */
    private function build_relationship_arguments(
        subject_instance_dto $subject_instance
    ): array {
        $args = [];
        $should_use_subject_user_id = empty($subject_instance->job_assignment_id) && !empty($subject_instance->subject_user_id);

        if ($should_use_subject_user_id) {
            $args['user_id'] = $subject_instance->subject_user_id;
        } else {
            $args['job_assignment_id'] = $subject_instance->job_assignment_id;
        }

        return $args;
    }

    /**
     * Create participant instances for a list of relationships.
     *
     * @param array $relationship_data Contains activity_relationships,activity_id, subject instance and participant ids.
     * @return void
     */
    private function create_participant_instances_for_relationships(array $relationship_data): void {
        $activity_relationships = $relationship_data['activity_relationships'];
        $subject_instance = $relationship_data['subject_instance'];
        $participant_ids = $relationship_data['participant_ids'];

        foreach ($activity_relationships as $activity_relationship) {
            $relationship_participants = $participant_ids[$activity_relationship->core_relationship_id];

            if (!empty($relationship_participants)) {
                $this->create_participant_instances_for_user_list(
                    $this->build_participant_instance_data($activity_relationship->id, $subject_instance),
                    $relationship_participants
                );
            }
        }
    }

    /**
     * Build participant instance data.
     *
     * @param $activity_relationship_id
     * @param $subject_instance
     * @return array
     */
    private function build_participant_instance_data($activity_relationship_id, $subject_instance): array {
        $dto = [];
        $participant_data = new stdClass();
        $participant_data->activity_relationship_id = $activity_relationship_id;
        $participant_data->subject_instance_id = $subject_instance->id;
        $participant_data->status = not_started::get_code();
        $participant_data->created_at = time();
        $dto['participant_data'] = $participant_data;
        $dto['activity_id'] = $subject_instance->activity_id;
        return $dto;
    }

    /**
     * Create participant instances for a list of user ids.
     *
     * @param array $data
     * @param array $participant_user_id_list
     * @return void
     */
    private function create_participant_instances_for_user_list(
        array $data,
        array $participant_user_id_list
    ): void {
        foreach ($participant_user_id_list as $participant_user_id) {
            $data['participant_data']->participant_id = $participant_user_id;
            $this->participation_creation_list[] = $data;

            if (count($this->participation_creation_list) === $this->buffer_count) {
                $this->save_data();
            }
        }
    }

    /**
     * Persists participant instances in database.
     *
     * @return void
     */
    private function save_data(): void {
        if (count($this->participation_creation_list) === 0) {
            return;
        }
        $db = builder::get_db();

        $created_participants_dtos = new collection();
        foreach ($this->participation_creation_list as $participant_instance) {
            $section_data = [];
            $section_data['activity_id'] = $participant_instance['activity_id'];
            $section_data['id'] = $db->insert_record(
                participant_instance_entity::TABLE,
                $participant_instance['participant_data']
            );
            $created_participants_dtos->append(
                participant_instance_dto::create_from_data($section_data)
            );
        }
        (new participant_instances_created($created_participants_dtos))->execute();
        $this->participation_creation_list = [];
    }
}
