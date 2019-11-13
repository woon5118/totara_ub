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
 * @author Fabian Derschatta <fabian.derschatta@totaralearning.com>
 * @package totara_competency
 */

namespace totara_competency;

use core\orm\collection;
use core\orm\query\builder;
use totara_competency\entities\competency;
use totara_competency\entities\pathway as pathway_entity;

/**
 * Class to run the aggregation on a list of users and competencies.
 *
 * Which users and competencies to run on is determined by the table passed in.
 *
 * The task uses all records in the table provided and run aggregation on them.
 */
class aggregation_task {

    /**
     * @var aggregation_users_table
     */
    private $table;

    /**
     * @var bool
     */
    private $full_user_set;

    /**
     * @var pathway_evaluator_user_source
     */
    private $pw_user_id_source;

    /**
     * @var competency_aggregator_user_source
     */
    private $comp_user_id_source;

    /**
     * @param aggregation_users_table $table
     * @param bool $full_user_set set it to true if all users and competencies are in the table,
     *                            false if only a subset of rows is in the table
     */
    public function __construct(aggregation_users_table $table, bool $full_user_set) {
        $this->table = $table;
        $this->full_user_set = $full_user_set;
    }

    /**
     * @param int|null $aggregation_time if needed you can specify the time, defaults to current unix timestamp
     */
    public function execute(?int $aggregation_time = null) {
        $this->pw_user_id_source = new pathway_evaluator_user_source($this->table, $this->full_user_set);
        $this->comp_user_id_source = new competency_aggregator_user_source($this->table, $this->full_user_set);

        $aggregation_time = $aggregation_time ?? time();

        // Get competencies with active enabled pathways and assigned users
        //      For each pathway
        //          Aggregate
        //      For each user with changes
        //          Perform overall aggregation
        //          Save competency_achievement
        //      For each competency
        //          Aggregate achievement

        $pathways = $this->get_active_pathways_for_assigned_users();

        /** @var competency $curr_competency */
        $curr_competency = null;
        foreach ($pathways as $pathway_entity) {
            if (!empty($curr_competency) && $pathway_entity->comp_id != $curr_competency->id) {
                $this->aggregate_competency_achievements($curr_competency, $aggregation_time);
            }

            $curr_competency = $pathway_entity->competency;

            $pathway = pathway_factory::from_entity($pathway_entity);

            $pw_evaluator = pathway_evaluator_factory::create($pathway, $this->pw_user_id_source);
            $pw_evaluator->aggregate($aggregation_time);
        }

        if (!empty($curr_competency)) {
            $this->aggregate_competency_achievements($curr_competency, $aggregation_time);
        }
    }

    /**
     * @return collection|pathway_entity[]
     */
    private function get_active_pathways_for_assigned_users(): collection {
        $pathway_types = plugin_types::get_enabled_plugins('pathway', 'totara_competency');

        return pathway_entity::repository()
            ->join(['comp', 'c'], 'comp_id', 'id')
            ->where('path_type', $pathway_types)
            ->where('status', pathway::PATHWAY_STATUS_ACTIVE)
            ->where_exists(function (builder $builder) {
                $uses_process_key = $this->table->get_process_key_column() && $this->table->get_process_key_value();

                $builder->from($this->table->get_table_name())
                    ->where_field($this->table->get_competency_id_column(), "c.id")
                    ->when($uses_process_key, function (builder $builder) {
                        $builder->where($this->table->get_process_key_column(), $this->table->get_process_key_value());
                    });
            })
            ->order_by('c.depthlevel', 'desc')
            ->order_by('comp_id', 'asc')
            ->with('competency')
            ->get();
    }

    /**
     * Perform competency achievement aggregation for all users who were marked as having changes
     *
     * @param competency $competency
     * @param int $aggregation_time
     */
    private function aggregate_competency_achievements(competency $competency, int $aggregation_time) {
        $configuration = new achievement_configuration($competency);
        $competency_aggregator = new competency_achievement_aggregator($configuration, $this->comp_user_id_source);
        $competency_aggregator->aggregate($aggregation_time);
    }

}
