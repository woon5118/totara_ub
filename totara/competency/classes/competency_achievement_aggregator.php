<?php
/*
 * This file is part of Totara Learn
 *
 * Copyright (C) 2018 onwards Totara Learning Solutions LTD
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
 * @author Brendan Cox <brendan.cox@totaralearning.com>
 * @package totara_competency
 */

namespace totara_competency;


use core\orm\collection;
use totara_competency\entities\achievement_via;
use totara_competency\entities\competency_achievement;
use totara_competency\entities\scale_value;
use totara_competency\event\competency_achievement_updated;

/**
 * Class aggregator
 *
 * Aggregates the competency values for users based on the given achievement configuration.
 */
final class competency_achievement_aggregator {

    /** @var achievement_configuration */
    private $achievement_configuration;

    /** @var overall_aggregation */
    private $aggregation_instance;

    /** @var null|int[] */
    private $proficient_scale_value_ids;

    /** @var competency_aggregator_user_source $user_id_source */
    protected $user_id_source = null;


    /**
     * aggregator constructor.
     * @param achievement_configuration $achievement_configuration
     * @param competency_aggregator_user_source $user_id_source
     */
    public function __construct(
        achievement_configuration $achievement_configuration,
        competency_aggregator_user_source $user_id_source
    ) {
        $this->achievement_configuration = $achievement_configuration;
        $this->user_id_source = $user_id_source;
    }

    /**
     * @return achievement_configuration
     */
    public function get_achievement_configuration(): achievement_configuration {
        return $this->achievement_configuration;
    }

    private function get_aggregation_instance(): overall_aggregation {
        if (is_null($this->aggregation_instance)) {
            $type = $this->get_achievement_configuration()->get_aggregation_type();
            $this->aggregation_instance = overall_aggregation_factory::create($type);
            $this->aggregation_instance->set_pathways($this->get_achievement_configuration()->get_active_pathways());
        }

        return $this->aggregation_instance;
    }

    public function set_aggregation_instance(overall_aggregation $aggregation_instance): competency_achievement_aggregator {
        $this->aggregation_instance = $aggregation_instance;
        return $this;
    }

    /**
     * Aggregate the competency values for users marked as having changes
     * Results will be used to add or update the applicable records in totara_competency_achievement
     *
     * @param int|null $aggregation_time
     */
    public function aggregate(?int $aggregation_time = null) {
        $competency_id = $this->get_achievement_configuration()->get_competency()->id;

        if (is_null($aggregation_time)) {
            $aggregation_time = time();
        }

        $this->user_id_source->set_competency_id_value($competency_id);
        $this->user_id_source->archive_non_assigned_achievements($competency_id, $aggregation_time);
        $user_assignment_records = $this->user_id_source->get_users_to_reaggregate($competency_id);

        foreach ($user_assignment_records as $user_assignment_record) {
            $user_id = $user_assignment_record->user_id;
            $user_achievement = $this->get_aggregation_instance()->aggregate_for_user($user_id);

            if (is_null($user_assignment_record->comp_achievement_id)) {
                $previous_comp_achievement = null;
            } else {
                $previous_comp_achievement = new competency_achievement($user_assignment_record->comp_achievement_id);
            }

            $is_proficient = (int) $this->is_proficient($user_achievement['scale_value_id']);

            // If the scale value changed or the proficiency value then we supersede the old record and create a new one
            if (is_null($previous_comp_achievement)
                || $user_assignment_record->scale_value_id != $user_achievement['scale_value_id']
                || $user_assignment_record->proficient != $is_proficient
            ) {
                // New achieved value
                if (!is_null($previous_comp_achievement)) {
                    $previous_comp_achievement->status = competency_achievement::SUPERSEDED;
                    $previous_comp_achievement->time_status = $aggregation_time;
                    $previous_comp_achievement->save();
                }

                $new_comp_achievement = new competency_achievement();
                $new_comp_achievement->comp_id = $competency_id;
                $new_comp_achievement->user_id = $user_id;
                $new_comp_achievement->assignment_id = $user_assignment_record->assignment_id;
                $new_comp_achievement->scale_value_id = $user_achievement['scale_value_id'];
                $new_comp_achievement->proficient = $is_proficient;
                $new_comp_achievement->status = competency_achievement::ACTIVE_ASSIGNMENT;
                $new_comp_achievement->time_created = $aggregation_time;
                $new_comp_achievement->time_status = $aggregation_time;
                $new_comp_achievement->time_proficient = $aggregation_time;
                $new_comp_achievement->time_scale_value = $aggregation_time;
                $new_comp_achievement->last_aggregated = $aggregation_time;
                $new_comp_achievement->save();

                foreach ($user_achievement['achieved_via'] as $pathway_achievement) {
                    $via = new achievement_via();
                    $via->comp_achievement_id = $new_comp_achievement->id;
                    $via->pathway_achievement_id = $pathway_achievement->id;
                    $via->save();
                }

                $achieved_via_ids = collection::new($user_achievement['achieved_via'])->pluck('id');

                competency_achievement_updated::create([
                    'context' => \context_system::instance(),
                    'objectid' => $new_comp_achievement->id,
                    'relateduserid' => $user_id,
                    'other' => [
                        'competency_id' => $competency_id,
                        'achieved_via_ids' => $achieved_via_ids
                    ],
                ])->trigger();
            } else {
                // No change.
                $previous_comp_achievement->last_aggregated = $aggregation_time;
                $previous_comp_achievement->save();
            }
        }

        $user_assignment_records->close();
    }

    /**
     * Checks whether a given scale value is considered proficient.
     *
     * @param int $value_id
     * @return bool True if the scale value is proficient.
     */
    private function is_proficient($value_id): bool {
        if (!isset($this->proficient_scale_value_ids[$value_id])) {
            $value = new scale_value($value_id);
            $this->proficient_scale_value_ids[$value_id] = (bool) $value->proficient;
        }

        return $this->proficient_scale_value_ids[$value_id];
    }
}
