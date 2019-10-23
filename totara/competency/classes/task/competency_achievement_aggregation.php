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

namespace totara_competency\task;

use core\task\scheduled_task;
use totara_competency\achievement_configuration;
use totara_competency\aggregation_users_table;
use totara_competency\competency_achievement_aggregator;
use totara_competency\competency_aggregator_user_source_table;
use totara_competency\pathway;
use totara_competency\entities\competency;
use totara_competency\pathway_evaluator_factory;
use totara_competency\pathway_evaluator_user_source_table;
use totara_competency\pathway_factory;
use totara_competency\plugintypes;
use xmldb_table;

/**
 * Class competency_achievement_aggregation
 *
 * Aggregates values for users competency achievements based on their pathway achievement values.
 */
class competency_achievement_aggregation extends scheduled_task {

    /** @var aggregation_users_table $temp_table */
    private $temp_table;

    /** @var pathway_evaluator_user_source_table $pw_user_id_source */
    private $pw_user_id_source;

    /** @var competency__aggregator_user_source_table $comp_user_id_source */
    private $comp_user_id_source;

    public function get_name() {
        return get_string('updatecompachievements', 'totara_competency');
    }

    public function execute() {
        $this->temp_table = new aggregation_users_table('totara_competency_aggregation_temp', true);

        // With using a temp table this is probably not necessary
        $this->pw_user_id_source = new pathway_evaluator_user_source_table($this->temp_table, true);
        $this->comp_user_id_source = new competency_aggregator_user_source_table($this->temp_table, true);

        $this->fill_temp_table();

        $aggregation_time = time();

        // Get competencies with active enabled pathways and assigned users
        //      For each pathway
        //          Aggregate
        //      For each user with changes
        //          Perform overall aggregation
        //          Save competency_achievement
        //      For each competency
        //          Aggregate achievement

        $pathways = $this->get_active_pathways_for_assigned_users();

        $reaggregate_competencies = [];
        foreach ($pathways as $pathway) {
            $reaggregate_competencies[$pathway->comp_id] = true;

            // TODO this will trigger additional queries, think of how to optimise,
            //      maybe use the record instead because we mostly need the id or the comp_id
            $pathway = pathway_factory::fetch($pathway->path_type, $pathway->id);
            $pw_evaluator = pathway_evaluator_factory::create($pathway, $this->pw_user_id_source);
            $pw_evaluator->aggregate($aggregation_time);
        }

        foreach ($reaggregate_competencies as $competency_id => $value) {
            $this->aggregate_competency_achievements($competency_id, $aggregation_time);
        }
    }

    private function fill_temp_table() {
        global $DB;

        [$insert_values_sql, $params] = $this->temp_table->get_insert_values_sql_with_params(null, null, 0);

        $insert_columns = "user_id, competency_id, ".implode(", ", array_keys($params));

        $sql =
            "INSERT INTO {" . $this->temp_table->get_table_name() . "}
            (" . $insert_columns . ")
             SELECT user_id, competency_id, {$insert_values_sql}
              FROM {totara_competency_assignment_users} tacu
              GROUP BY user_id, competency_id";

        $DB->execute($sql, $params);
    }

    private function get_active_pathways_for_assigned_users(): \moodle_recordset {
        global $DB;

        $pathway_types = plugintypes::get_enabled_plugins('pathway', 'totara_competency');
        [$pathtype_sql, $params] = $DB->get_in_or_equal($pathway_types, SQL_PARAMS_NAMED);

        // Get active pathways for assigned users
        // Although order by may have a slight impact on the query performance, with the possible number of rows
        // that may be returned, it is better to do ordering on the database
        $sql = "
            SELECT tcp.comp_id, tcp.id, tcp.path_type
            FROM {totara_competency_pathway} tcp
            WHERE tcp.path_type {$pathtype_sql}
                AND tcp.status = :activestatus
                AND tcp.comp_id IN (
                    SELECT DISTINCT competency_id
                    FROM {{$this->temp_table->get_table_name()}}
                )
            ORDER BY tcp.comp_id";
        $params['activestatus'] = pathway::PATHWAY_STATUS_ACTIVE;

        $aggregation_time = time();

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
