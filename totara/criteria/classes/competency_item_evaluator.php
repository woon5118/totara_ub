<?php
/*
 * This file is part of Totara Learn
 *
 * Copyright (C) 2019 onwards Totara Learning Solutions LTD
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
 * @author Riana Rossouw <riana.rossouw@totaralearning.com>
 * @package totara_criteria
 */

namespace totara_criteria;

use totara_competency\entities\competency_achievement;

class competency_item_evaluator {
    use item_evaluator;

    public static function update_item_records($user_ids = null, $item_ids = null) {
        global $CFG, $DB;

        /******************************************************************************************
         * NOTE: We do NOT take assignments into consideration!!!
         *       Proficiency in child competencies is connsidered "outside" of an assignment,
         *       i.e. proficiency via any assignment is considered a valid fulfillment of criteria
         ******************************************************************************************/

        // Todo: if item_ids and user_ids are null, allow all. If they are arrays. We restrict by those ids.

        // Find all existing item_records where tcir.criterion_met differs from tca.proficient
        // Not using timestamps - if these values differ now, we need to update the item_records

        $userinsql = $iteminsql = '';
        $userparams = $itemparams = [];

        if (!empty($user_ids)) {
            [$usersql, $userparams] = $DB->get_in_or_equal($user_ids, SQL_PARAMS_NAMED, 'puser');
            $userinsql = " AND tcir.user_id $usersql";
        }
        if (!empty($item_ids)) {
            [$itemsql, $itemparams] = $DB->get_in_or_equal($item_ids, SQL_PARAMS_NAMED, 'pitem');
            $iteminsql = " AND tci.item_id $itemsql";
        }

        $sql = "
            SELECT tcir.id, tcir.criterion_met, COALESCE(p.proficient, 0) as proficient
              FROM {totara_criteria_item} tci
              JOIN {totara_criteria_item_record} tcir
                ON tcir.criterion_item_id = tci.id
         LEFT JOIN (
                   SELECT tca.comp_id, tca.user_id, MAX(tca.proficient) AS proficient
                     FROM {totara_competency_achievement} tca
                    WHERE tca.status = :achievementstatus
                    GROUP BY tca.comp_id, tca.user_id) p 
                ON tci.item_id = p.comp_id
               AND tcir.user_id = p.user_id     
             WHERE tci.item_type = 'competency'
                {$userinsql}{$iteminsql}";

        $params = array_merge(
            $userparams,
            $itemparams,
            ['achievementstatus' => competency_achievement::ACTIVE_ASSIGNMENT]);

        $rows = $DB->get_records_sql($sql, $params);

        // Now update item_records with the correct proficient value
        $notmet = array_filter($rows, function ($v) {
            return $v->proficient == 0 && $v->criterion_met != $v->proficient;
        });
        if (!empty($notmet)) {
            static::update_criterion_met(0, array_keys($notmet));
        }

        $nowmet = array_filter($rows, function ($v) {
            return $v->proficient == 1 && $v->criterion_met != $v->proficient;
        });
        if (!empty($nowmet)) {
            static::update_criterion_met(1, array_keys($nowmet));
        }


        // Find all items WITHOUT an item_record but with an achievement record
        // and create item_records for them
        if (!empty($user_ids)) {
            $userinsql = " AND tca.user_id $usersql";
        }

        $now = time();

        $sql = "
            SELECT tca.user_id,
                   tci.id AS criterion_item_id,
                   MAX(tca.proficient) AS criterion_met,
                   {$now} AS timeevaluated
              FROM {totara_criteria_item} tci
              JOIN {totara_competency_achievement} tca
                ON tca.comp_id = tci.item_id
               AND tca.status = :achievementstatus
         LEFT JOIN {totara_criteria_item_record} tcir
                ON tcir.criterion_item_id = tci.id
               AND tcir.user_id = tca.user_id
             WHERE tci.item_type = 'competency'
               AND tcir.id IS NULL
                {$userinsql}{$iteminsql}
          GROUP BY tca.user_id, tci.id";

        $params = array_merge(
            $userparams,
            $itemparams,
            ['achievementstatus' => competency_achievement::ACTIVE_ASSIGNMENT]);

        $to_insert_records = $DB->get_records_sql($sql, $params);

        while (count($to_insert_records) > 0) {
            $to_insert = array_splice($to_insert_records, 0, BATCH_INSERT_MAX_ROW_COUNT);
            $DB->insert_records('totara_criteria_item_record', $to_insert);
        }
    }
}
