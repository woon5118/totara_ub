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

trait item_evaluator {
    abstract public static function update_item_records($user_ids = null, $item_ids = null);

    public static function create_item_records($item_id, $user_ids = []) {
        global $DB;

        $now = time();

        $to_insert = [];

        foreach ($user_ids as $user_id) {
            $record = new \stdClass();
            $record->user_id = $user_id;
            $record->criterion_item_id = $item_id;
            $record->criterion_met = 0; // Todo: Could be null for not evaluated yet, meaning we ignore until it is.
            $record->timeevaluated = time(); // Did have this as 0 if not evaluated. Might actually have a status for that. Problem with putting the actual time is it's not correct if that gets reflect in aggregated values.
            $to_insert[] = $record;

            if (count($to_insert) > BATCH_INSERT_MAX_ROW_COUNT) {
                $DB->insert_records('totara_criteria_item_record', $to_insert);
                $to_insert = [];
            }
        }

        if (count($to_insert) > 0) {
            $DB->insert_records('totara_criteria_item_record', $to_insert);
        }
    }

    public static function update_criterion_met($new_value, $record_ids) {
        global $DB;

        if (empty($record_ids)) {
            return;
        }

        list($insql, $params) = $DB->get_in_or_equal($record_ids, SQL_PARAMS_NAMED);
        $params['newvalue'] = $new_value;
        $params['now'] = time();

        $update_sql = "
                UPDATE {totara_criteria_item_record}
                SET criterion_met = :newvalue, timeevaluated = :now
                WHERE id " . $insql;
        $DB->execute($update_sql, $params);
    }
}