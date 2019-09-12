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
 * @author Riana Rossouw <riana.rossouw@totaralearning.com>
 * @package totara_criteria
 */

namespace totara_criteria;

class item_evaluator_user_source_list implements item_evaluator_user_source{

    /** @var int[] $user_id_list */
    protected $user_id_list = null;

    /** @var bool $full_user_set */
    protected $full_user_set;

    /**
     * Constructor.
     * @param array $user_id_list Source containing the user ids
     * @param bool $full_user_set Does this source contain all users?
     */
    final public function __construct(array $user_id_list, bool $full_user_set = false) {
        $this->user_id_list = $user_id_list;
        $this->full_user_set = $full_user_set;
    }

    /**
     * Return the source type
     *@return string
     */
    public function get_source_type(): string {
        return 'list';
    }

    /**
     * Return the user_id_source
     * @return mixed Source used for obtaining currently assigned users
     */
    public function get_source() {
        return $this->user_id_list;
    }

    /**
     * Does the source hold a full set of user ids?
     * @return bool
     */
    public function is_full_user_set(): bool {
        return $this->full_user_set;
    }


    /**
     * Create item records for all users in the user_id_list who doesn't have a item_record
     * @param int $criterion_id
     * @param int $criteria_met Criterion met value to use when creating new item records
     * @param ?int $timeevaluated
     */
   public function create_item_records(int $criterion_id, int $criteria_met = 0, ?int $timeevaluated = null) {
        global $DB;

        if (empty($this->user_id_list)) {
            return;
        }

        if (is_null($timeevaluated)) {
            $timeevaluated = time();
        }

        $params = [];
        // As the user_id_list is an array, we can't complete this via a single sql statement.
        // We first need to find the current user ids, and manually determine which records to add.
        // As lists will mostly be used for single users and testing, performance should be ok
        [$users_in_sql, $params] = $DB->get_in_or_equal($this->user_id_list, SQL_PARAMS_NAMED);

        $sql = "SELECT tci.id AS item_id, tcir.user_id
                  FROM {totara_criteria_item} tci
              LEFT JOIN {totara_criteria_item_record} tcir
                   ON tcir.criterion_item_id = tci.id
                  AND tcir.user_id {$users_in_sql}
                 WHERE tci.criterion_id = :criterionid";
        $params['criterionid'] = $criterion_id;

        $rows = $DB->get_recordset_sql($sql, $params);

        $item_users = [];
        foreach ($rows as $row) {
            if (!isset ($item_users[$row->item_id])) {
                $item_users[$row->item_id] = [];
            }
            if (!is_null($row->user_id)) {
                $item_users[$row->item_id][] = $row->user_id;
            }
        }
        $rows->close();

        $to_add = [];
        foreach ($item_users as $item_id => $user_ids) {
            $users_to_add = array_diff($this->user_id_list, $user_ids);
            $to_add += array_map(function ($user_id) use ($item_id, $criteria_met, $timeevaluated) {
                    return [
                        'user_id' => $user_id,
                        'criterion_item_id' => $item_id,
                        'criterion_met' => $criteria_met,
                        'timeevaluated' => $timeevaluated,
                    ];
                },
                $users_to_add);
        }

        while (count($to_add) > 0) {
            $to_insert = array_splice($to_add, 0, BATCH_INSERT_MAX_ROW_COUNT);
            $DB->insert_records('totara_criteria_item_record', $to_insert);
        }
    }

    /**
     * Delete item records for all users not found in the user_id_list if the source contains the full set
     * @param int $criterion_id
     */
    public function delete_item_records(int $criterion_id) {
        global $DB;

        if (!$this->full_user_set) {
            return;
        }

        $sql = "DELETE FROM {totara_criteria_item_record}
                      WHERE criterion_item_id IN (
                            SELECT tci.id
                              FROM {totara_criteria_item} tci
                             WHERE tci.criterion_id = :criterionid)";

        $params = [];
        if (!empty($this->user_id_list)) {
            [$users_in_sql, $params] = $DB->get_in_or_equal($this->user_id_list, SQL_PARAMS_NAMED, 'param', false);
            $sql .= " AND user_id {$users_in_sql}";
        }
        $params['criterionid'] = $criterion_id;

        $DB->execute($sql, $params);
    }

    /**
     * Mark users in the users_source whose item_record was updated since the specified time
     * @param int $criterion_id
     * @param int $checkfrom Time to use as start when checking updates of item records
     */
    public function mark_updated_assigned_users(int $criterion_id, int $checkfrom) {
        // No marking used with lists
        // All users are considered
    }

}
