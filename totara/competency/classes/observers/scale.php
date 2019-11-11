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
 * @package totara_userstatus
 */

namespace totara_competency\observers;

use hierarchy_competency\event\scale_min_proficient_value_updated;
use hierarchy_competency\event\scale_updated;
use hierarchy_competency\event\scale_value_created;
use hierarchy_competency\event\scale_value_deleted;
use totara_competency\aggregation_users_table;
use totara_competency\entities\configuration_change;

class scale {

    /**
     * When a scale is updated we need to queue reaggregation for
     * all users assigned to any competency with that scale
     *
     * @param scale_updated $event
     */
    public static function updated(scale_updated $event) {
        self::queue_for_reaggregation($event->objectid);
    }

    /**
     * When a scale value gets deleted we need to queue all related records for reaggregation
     *
     * @param scale_value_deleted $event
     */
    public static function value_deleted(scale_value_deleted $event) {
        self::queue_for_reaggregation($event->other['scaleid']);
    }

    /**
     * When a scale value gets added we need to queue all related records for reaggregation
     *
     * @param scale_value_created $event
     */
    public static function value_created(scale_value_created $event) {
        self::queue_for_reaggregation($event->other['scaleid']);
    }

    /**
     * Load and add all users assigned to competencies with the given scale to the queue
     *
     * @param int $scale_id
     */
    private static function queue_for_reaggregation(int $scale_id) {
        global $DB;

        $table = new aggregation_users_table();

        if ($table->get_process_key_column()) {
            $process_key_cond = "AND q.{$table->get_process_key_column()} IS NULL";
        }

        // For performance reasons we use one query to query and insert new rows
        // We do not insert values which are already in the queue table
        // to avoid rows being processed twice
        $sql = "
            INSERT INTO {{$table->get_table_name()}}
                ({$table->get_user_id_column()}, {$table->get_competency_id_column()})
            SELECT tcau.user_id, tcau.competency_id 
            FROM {totara_competency_assignment_users} tcau 
            JOIN {comp} c ON tcau.competency_id = c.id
            JOIN {comp_scale_assignments} csa ON c.frameworkid = csa.frameworkid
            LEFT JOIN {{$table->get_table_name()}} q 
                ON tcau.competency_id = q.{$table->get_competency_id_column()}
                 AND tcau.user_id = q.{$table->get_user_id_column()}
                 {$process_key_cond}
            WHERE csa.scaleid = :sale_id AND q.id IS NULL 
        ";

        $DB->execute($sql, ['scale_id' => $scale_id]);
    }

    /**
     * React if the minimum proficient value change
     *
     * @param scale_min_proficient_value_updated $event
     */
    public static function min_proficient_value_updated(scale_min_proficient_value_updated $event) {
        $scale_id = $event->objectid;
        $min_prof_id = $event->other['minproficiencyid'];

        configuration_change::min_proficiency_change($scale_id, $min_prof_id);
    }

}