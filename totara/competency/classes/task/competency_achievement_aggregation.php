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
        global $DB;

        $this->temp_table = new aggregation_users_table('totara_competency_temp_users', 'user_id', 'has_changed',
                                      'process_key', 'update_operation_name');
        $this->temp_table->set_process_key_value('achievement_aggregation_' . time());
        $this->pw_user_id_source = new pathway_evaluator_user_source_table($this->temp_table, true);
        $this->comp_user_id_source = new competency_aggregator_user_source_table($this->temp_table, true);

        // Get competencies with active enabled pathways and assigned users
        // For each competency
        //      Save assigned users in temp table
        //      For each pathway
        //          Aggregate
        //      For each user with changes
        //          Perform overall aggregation
        //          Save competency_achievement

        $pathway_types = plugintypes::get_enabled_plugins('pathway', 'totara_competency');
        [$pathtype_sql, $params] = $DB->get_in_or_equal($pathway_types, $type=SQL_PARAMS_NAMED);

        // Get competencies
        // Although order by may have a slight impact on the query performance, with the possible number of rows
        // that may be returned, it is better to do ordering on the database
        $sql =
            "SELECT tcp.comp_id, tcp.id, tcp.path_type
               FROM {totara_competency_pathway} tcp
              WHERE tcp.path_type {$pathtype_sql}
                AND tcp.status = :activestatus
                AND tcp.comp_id IN (
                    SELECT DISTINCT tacu.competency_id
                      FROM {totara_assignment_competency_users} tacu
                    )
           ORDER BY tcp.comp_id";
        $params['activestatus'] = pathway::PATHWAY_STATUS_ACTIVE;

        $aggregation_time = time();

        $recordset = $DB->get_recordset_sql($sql, $params);

        $cur_comp_id = 0;
        foreach ($recordset as $pathway_record) {
            if ($pathway_record->comp_id != $cur_comp_id) {
                if ($cur_comp_id != 0) {
                    $this->aggregate_competency_achievements($cur_comp_id, $aggregation_time);
                }

                // Save assigned users in the temp table once per competency
                $this->create_temp_users($pathway_record->comp_id);
                $cur_comp_id = $pathway_record->comp_id;
            }

            $pathway = pathway_factory::fetch($pathway_record->path_type, $pathway_record->id);
            $pw_evaluator = pathway_evaluator_factory::create($pathway, $this->pw_user_id_source);
            $pw_evaluator->aggregate($aggregation_time);
        }

        if ($cur_comp_id != 0) {
            $this->aggregate_competency_achievements($cur_comp_id, $aggregation_time);
        }

        $recordset->close();

        $this->temp_table->truncate();
    }

    /**
     * Populate totara_competency_temp_users with the ids of users currently assigned to this competency
     *
     * @param int $comp_id
     */
    private function create_temp_users(int $comp_id) {
        global $DB;

        $this->temp_table->truncate();

        // TODO: Not sure about multiple assignments - select DISTINCT??
        // Currently relying on user_id being the first column in the list
        [$insert_values_sql, $params] = $this->temp_table->get_insert_values_sql_with_params(null, 0);
        $insert_select_values = implode(', ',
            array_map(function ($variable) {
                return ':' . $variable;
            },
            array_keys($params))
        );

        $sql =
            "INSERT INTO {" . $this->temp_table->get_table_name() . "}
            (" . $this->temp_table->get_insert_column_list() . ")
             SELECT user_id, {$insert_select_values}
               FROM {totara_competency_assignment_users} tacu
              WHERE tacu.competency_id = :compid";
        $params['compid'] = $comp_id;

        $DB->execute($sql, $params);
    }

    /**
     * Perform competency achievement aggregation for all users who were marked as having changes
     *
     * @param int $comp_id
     */
    private function aggregate_competency_achievements(int $comp_id, int $aggregation_time) {
        $competency = new competency($comp_id);
        $configuration = new achievement_configuration($competency);
        $competency_aggregator = new competency_achievement_aggregator($configuration, $this->comp_user_id_source);
        $competency_aggregator->aggregate($aggregation_time);
    }


}
