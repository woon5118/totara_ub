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

use totara_competency\entities\competency;

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

        $pathway_rows = $this->get_active_pathways_for_assigned_users();

        $reaggregate_competencies = [];
        foreach ($pathway_rows as $row) {
            $reaggregate_competencies[$row->comp_id] = true;

            $pathway = pathway_factory::from_record($row);
            $pw_evaluator = pathway_evaluator_factory::create($pathway, $this->pw_user_id_source);
            $pw_evaluator->aggregate($aggregation_time);
        }

        foreach ($reaggregate_competencies as $competency_id => $value) {
            $this->aggregate_competency_achievements($competency_id, $aggregation_time);
        }
    }

    private function get_active_pathways_for_assigned_users(): \moodle_recordset {
        global $DB;

        $pathway_types = plugintypes::get_enabled_plugins('pathway', 'totara_competency');
        [$pathtype_sql, $params] = $DB->get_in_or_equal($pathway_types, SQL_PARAMS_NAMED);

        // Get active pathways for assigned users
        // Although order by may have a slight impact on the query performance, with the possible number of rows
        // that may be returned, it is better to do ordering on the database
        $sql = "
            SELECT *
            FROM {totara_competency_pathway} tcp
            WHERE tcp.path_type {$pathtype_sql}
                AND tcp.status = :activestatus
                AND tcp.comp_id IN (
                    SELECT DISTINCT competency_id
                    FROM {{$this->table->get_table_name()}}
                )
            ORDER BY tcp.comp_id";
        $params['activestatus'] = pathway::PATHWAY_STATUS_ACTIVE;

        return $DB->get_recordset_sql($sql, $params);
    }

    /**
     * Perform competency achievement aggregation for all users who were marked as having changes
     *
     * @param int $comp_id
     * @param int $aggregation_time
     */
    private function aggregate_competency_achievements(int $comp_id, int $aggregation_time) {
        $competency = new competency($comp_id);
        $configuration = new achievement_configuration($competency);
        $competency_aggregator = new competency_achievement_aggregator($configuration, $this->comp_user_id_source);
        $competency_aggregator->aggregate($aggregation_time);
    }

}
