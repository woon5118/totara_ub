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
use core\entities\user;
use core\orm\query\builder;
use mod_perform\constants;
use mod_perform\entities\activity\manual_relationship_selection_progress;
use mod_perform\entities\activity\participant_instance as participant_instance_entity;
use mod_perform\entities\activity\section;
use mod_perform\entities\activity\section_relationship;
use mod_perform\entities\activity\subject_instance;
use mod_perform\entities\activity\subject_instance_manual_participant;
use mod_perform\entities\activity\track;
use mod_perform\entities\activity\track_user_assignment;
use mod_perform\hook\participant_instances_created;
use mod_perform\models\activity\participant_instance as participant_instance_model;
use mod_perform\models\activity\subject_instance as subject_instance_model;
use mod_perform\state\activity\active;
use mod_perform\state\participant_instance\not_started;
use mod_perform\state\subject_instance\closed;
use mod_perform\state\subject_instance\pending;
use totara_core\relationship\helpers\relationship_collection_manager as core_relationship_collection_manager;
use totara_core\relationship\relationship as core_relationship;

/**
 * Class participation_service
 * Used to initially generate participant instances for a collection of new subject instances.
 * Also used to add participant instances to existing subject instances.
 *
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
     * Add a list of participants to all given subject instances.
     *
     * No permission checks here. Calling code must take care of that.
     * The subject instances must belong to the same activity, otherwise an exception is thrown.
     *
     * @param array $subject_instance_ids
     * @param array $participant_relationship_map array of arrays with participant_id/core_relationship_id tuples
     * @return collection|participant_instance_model[] returns added participant instances
     */
    public function add_instances(array $subject_instance_ids, array $participant_relationship_map): collection {
        $subject_instance_ids = array_map('intval', array_unique($subject_instance_ids));

        $activity_id = $this->validate_add_instances($subject_instance_ids, $participant_relationship_map);

        // Main transaction to add all the new participant instances.
        $added_instances_data = builder::get_db()->transaction(
            function () use ($activity_id, $subject_instance_ids, $participant_relationship_map) {
                $added_instance_groups = [];
                foreach ($subject_instance_ids as $subject_instance_id) {
                    $added_instance_groups[] = $this->add_instances_for_subject_instance(
                        $activity_id,
                        $subject_instance_id,
                        $participant_relationship_map
                    );
                }
                return array_merge([], ...$added_instance_groups);
            }
        );
        return $this->build_models_from_participant_instance_data($added_instances_data);
    }

    /**
     * Build a collection of participant instance models from given tuples of
     * [subject_instance_id, core_relationship_id, participant_id].
     *
     * @param array $participant_instances_data
     * @return collection|participant_instance_model[]
     */
    private function build_models_from_participant_instance_data(array $participant_instances_data): collection {
        if (count($participant_instances_data) === 0) {
            return new collection();
        }
        $repository = participant_instance_entity::repository();
        foreach ($participant_instances_data as $participant_instances_datum) {
            $repository->or_where(function (builder $builder) use ($participant_instances_datum) {
                $builder->where('subject_instance_id', $participant_instances_datum['subject_instance_id'])
                    ->where('core_relationship_id', $participant_instances_datum['core_relationship_id'])
                    ->where('participant_id', $participant_instances_datum['participant_id']);
            });
        }
        return $repository->get()->map_to(participant_instance_model::class);
    }

    /**
     * @param int $activity_id
     * @param int $subject_instance_id
     * @param array $participant_relationship_map
     * @return array
     */
    private function add_instances_for_subject_instance(
        int $activity_id,
        int $subject_instance_id,
        array $participant_relationship_map
    ): array {
        $added_instances_data = [];
        $subject_instance_has_new_participant_instance = false;
        foreach ($participant_relationship_map as $participant_relationship) {
            // Ignore if such a participant instance already exists.
            if ($this->participant_instance_exists(
                $subject_instance_id,
                $participant_relationship['core_relationship_id'],
                $participant_relationship['participant_id']
            )) {
                continue;
            }

            $subject_instance_has_new_participant_instance = true;
            $subject_instance_data = (object)[
                'id' => $subject_instance_id,
                'activity_id' => $activity_id,
            ];
            $this->create_participant_instances_for_user_list(
                $this->build_participant_instance_data(
                    $participant_relationship['core_relationship_id'],
                    $subject_instance_data
                ),
                [$participant_relationship['participant_id']]
            );
            $added_instances_data[] = [
                'subject_instance_id' => $subject_instance_id,
                'core_relationship_id' => $participant_relationship['core_relationship_id'],
                'participant_id' => $participant_relationship['participant_id'],
            ];
        }
        if ($subject_instance_has_new_participant_instance) {
            $subject_instance = subject_instance_model::load_by_id($subject_instance_id);
            if ($subject_instance->get_availability_state() instanceof closed) {
                // Re-open subject instance if it was closed. Also takes care of progress state.
                $subject_instance->manually_open(false);
            } else {
                // Otherwise just make sure progress state is correct.
                $subject_instance->update_progress_status();
            }
        }
        return $added_instances_data;
    }

    /**
     * Validate data for processing by add_instances().
     *
     * @param array $subject_instance_ids
     * @param array $participant_data
     * @return int the single activity_id that the subject_instances have in common
     */
    private function validate_add_instances(array $subject_instance_ids, array $participant_data): int {
        // First get the activity id from one of the subject instances.
        /** @var subject_instance $subject_instance */
        $subject_instance = subject_instance::repository()
            ->where('id', $subject_instance_ids)
            ->with('track.activity')
            ->order_by('id')
            ->first();
        if (!$subject_instance) {
            throw new \coding_exception('Invalid subject_instance_ids detected');
        }
        $activity_id = $subject_instance->activity()->id;

        // Activity must be active.
        if ((int)$subject_instance->activity()->status !== active::get_code()) {
            throw new \coding_exception('Cannot add participant instances for inactive activity.');
        }

        // Subject instances must be for the same activity.
        $this->validate_subject_instances_for_activity($subject_instance_ids, $activity_id);

        // Relationships must be involved in the activity and cannot be 'subject' relationship.
        $relationship_ids = array_unique(array_column($participant_data, 'core_relationship_id'));
        $this->validate_relationships_for_activity($relationship_ids, $activity_id);

        // Participant ids must be valid user ids.
        $user_ids = array_unique(array_column($participant_data, 'participant_id'));
        $this->validate_user_ids($user_ids);

        return $activity_id;
    }

    /**
     * Validate that the given subject_instance_ids belong to the given activity id.
     *
     * @param array $subject_instance_ids
     * @param int $activity_id
     */
    private function validate_subject_instances_for_activity(array $subject_instance_ids, int $activity_id) {
        $subject_instance_ids_filtered = subject_instance::repository()
            ->join([track_user_assignment::TABLE, 'tua'], 'track_user_assignment_id', 'id')
            ->join([track::TABLE, 'tr'], 'tua.track_id', 'id')
            ->where('tr.activity_id', $activity_id)
            ->where_in('id', $subject_instance_ids)
            ->get()
            ->pluck('id');
        $bad_subject_instance_ids = array_diff($subject_instance_ids, $subject_instance_ids_filtered);
        if (count($bad_subject_instance_ids) > 0) {
            $bad_subject_instance_ids = implode(',', $bad_subject_instance_ids);
            throw new \coding_exception(
                "Subject instances with these ids do not belong to activity {$activity_id}: {$bad_subject_instance_ids}"
            );
        }
    }

    /**
     * Validate that the given relationships can be added to the given activity, meaning they must be involved in the
     * activity but cannot be 'subject' relationship.
     *
     * @param array $relationship_ids_to_be_added
     * @param int $activity_id
     */
    private function validate_relationships_for_activity(array $relationship_ids_to_be_added, int $activity_id) {

        $subject_relationship = core_relationship::load_by_idnumber(constants::RELATIONSHIP_SUBJECT);
        $involved_valid_relationship_ids = section_relationship::repository()
            ->join([section::TABLE, 'sctn'], 'section_id', 'id')
            ->where('sctn.activity_id', $activity_id)
            ->where('core_relationship_id', '!=', $subject_relationship->id)
            ->get(true)
            ->pluck('core_relationship_id');

        $bad_relationship_ids = array_diff($relationship_ids_to_be_added, array_unique($involved_valid_relationship_ids));

        if (count($bad_relationship_ids) > 0) {
            sort($bad_relationship_ids);
            $bad_relationship_ids = implode(',', $bad_relationship_ids);
            throw new \coding_exception(
                "Relationships with these ids cannot be used in activity {$activity_id}: {$bad_relationship_ids}"
            );
        }
    }

    /**
     * Validate that the given user ids exist.
     *
     * @param array $user_ids
     */
    private function validate_user_ids(array $user_ids) {
        $user_ids_filtered_by_existing = user::repository()
            ->where_in('id', $user_ids)
            ->get()
            ->pluck('id');

        $bad_user_ids = array_diff($user_ids, $user_ids_filtered_by_existing);
        if (count($bad_user_ids) > 0) {
            $bad_user_ids = implode(',', $bad_user_ids);
            throw new \coding_exception("Users with these ids do not exist: {$bad_user_ids}");
        }
    }

    /**
     * Find out whether a participant instance exists for the given data.
     *
     * @param int $subject_instance_id
     * @param int $core_relationship_id
     * @param int $participant_id
     * @return bool
     */
    private function participant_instance_exists(
        int $subject_instance_id,
        int $core_relationship_id,
        int $participant_id
    ): bool {
        return participant_instance_entity::repository()
            ->where('subject_instance_id', $subject_instance_id)
            ->where('core_relationship_id', $core_relationship_id)
            ->where('participant_id', $participant_id)
            ->exists();
    }

    /**
     * Aggregates participant instances to use for bulk insert.
     *
     * @param collection|subject_instance_dto[] $subject_instance_dtos
     *
     * @return void
     */
    private function aggregate_participant_instances(collection $subject_instance_dtos): void {
        $subject_instance_dtos = $this->filter_out_pending_instances($subject_instance_dtos);

        // Find all the activities that are related to the subject instances.
        $activity_ids = array_unique($subject_instance_dtos->pluck('activity_id'), SORT_NUMERIC);
        $activity_relationships = $this->get_activity_relationships($activity_ids);
        $core_relationship_ids = array_unique($activity_relationships->pluck('core_relationship_id'));

        if (empty($core_relationship_ids)) {
            return;
        }

        // Initialise the core relationship manager with the core relationships that we will be using.
        $relationship_manager = new core_relationship_collection_manager($core_relationship_ids);
        $relationships_per_activity = $this->group_relationship_ids_by_activity($activity_relationships);

        // Process each subject instance, one at a time.
        foreach ($subject_instance_dtos as $subject_instance) {
            // If there are no relationships defined for the activity then there is nothing to do.
            $has_no_relationships_for_activity = !isset($relationships_per_activity[$subject_instance->activity_id]);
            if ($has_no_relationships_for_activity) {
                continue;
            }

            $relationship_arguments = $this->build_relationship_arguments($subject_instance);

            $participant_ids_for_relationships = $relationship_manager->get_users_for_relationships(
                $relationship_arguments,
                $relationships_per_activity[$subject_instance->activity_id]
            );
            $relationship_data = [
                'core_relationship_ids' => $relationships_per_activity[$subject_instance->activity_id],
                'subject_instance' => $subject_instance,
                'participant_ids' => $participant_ids_for_relationships,
            ];

            $this->create_participant_instances_for_relationships($relationship_data);
        }
    }

    /**
     * Get core_relationships for all activities.
     *
     * @param array $activity_ids
     * @return collection
     */
    private function get_activity_relationships(array $activity_ids): collection {
        return section_relationship::repository()
            ->select(['id', 'section.activity_id', 'core_relationship_id'])
            ->join([section::TABLE, 'section'], 'section_id', 'id')
            ->where_in('section.activity_id', $activity_ids)
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
            if (!isset($relationships_per_activity[$activity_relationship->activity_id]) ||
                !in_array(
                    $activity_relationship->core_relationship_id,
                    $relationships_per_activity[$activity_relationship->activity_id]
                )
            ) {
                $relationships_per_activity[$activity_relationship->activity_id][] = $activity_relationship->core_relationship_id;
            }
        }

        return $relationships_per_activity;
    }

    /**
     * Builds relationship class arguments from subject instance id.
     *
     * @param subject_instance_dto $subject_instance
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
            $args['user_id'] = $subject_instance->subject_user_id; // Always required for subject resolver.
            $args['job_assignment_id'] = $subject_instance->job_assignment_id;
        }

        // Always include subject instance id in order to support manual relationships.
        $args['subject_instance_id'] = $subject_instance->id;

        return $args;
    }

    /**
     * Create participant instances for a list of relationships.
     *
     * @param array $relationship_data Contains core_relationships, activity_id, subject instance and participant ids.
     * @return void
     */
    private function create_participant_instances_for_relationships(array $relationship_data): void {
        $core_relationship_ids = $relationship_data['core_relationship_ids'];
        $subject_instance = $relationship_data['subject_instance'];
        $participant_ids = $relationship_data['participant_ids'];

        foreach ($core_relationship_ids as $core_relationship_id) {
            $relationship_participants = $participant_ids[$core_relationship_id] ?? null;

            if (!empty($relationship_participants)) {
                $this->create_participant_instances_for_user_list(
                    $this->build_participant_instance_data($core_relationship_id, $subject_instance),
                    $relationship_participants
                );
            }
        }

        $this->delete_manual_participant_selection_data($subject_instance->id);
    }

    /**
     * Build participant instance data.
     *
     * @param $core_relationship_id
     * @param $subject_instance
     * @return array
     */
    private function build_participant_instance_data($core_relationship_id, $subject_instance): array {
        return [
            'participant_data' => [
                'core_relationship_id' => $core_relationship_id,
                'subject_instance_id' => $subject_instance->id,
                'status' => not_started::get_code(),
                'created_at' => time(),
            ],
            'activity_id' => $subject_instance->activity_id,
        ];
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
            $data['participant_data']['participant_id'] = $participant_user_id;
            $this->participation_creation_list[] = $data;

            if (count($this->participation_creation_list) === $this->buffer_count) {
                $this->save_data();
            }
        }
        $this->save_data();
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
            $section_data['core_relationship_id'] = $participant_instance['participant_data']['core_relationship_id'];
            $section_data['id'] = $db->insert_record(
                participant_instance_entity::TABLE,
                (object) $participant_instance['participant_data']
            );
            $created_participants_dtos->append(
                participant_instance_dto::create_from_data($section_data)
            );
        }
        (new participant_instances_created($created_participants_dtos))->execute();
        $this->participation_creation_list = [];
    }

    /**
     * Filter out all subject instances which are pending, we do not need to create
     * participant instances for them at this point
     *
     * @param collection $subject_instance_dtos
     */
    private function filter_out_pending_instances(collection $subject_instance_dtos): collection {
        return $subject_instance_dtos->filter(function (subject_instance_dto $subject_instance) {
            return $subject_instance->get_status() != pending::get_code();
        });
    }

    /**
     * Deletes the users that were manually selected to participate.
     * After generating participant instances based upon relationships, we no longer need to store the users.
     *
     * @param int $subject_instance_id
     */
    private function delete_manual_participant_selection_data(int $subject_instance_id): void {
        subject_instance_manual_participant::repository()
            ->where('subject_instance_id', $subject_instance_id)
            ->delete();

        // The perform_manual_relationship_selector table has a cascading delete foreign key on
        // perform_manual_relationship_selector_progress, so this deletes records from both of the tables.
        manual_relationship_selection_progress::repository()
            ->where('subject_instance_id', $subject_instance_id)
            ->delete();
    }

}
