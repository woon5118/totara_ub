<?php

namespace totara_criteria;

use totara_competency\aggregation_users_table;

abstract class item_combined {

    /** @var aggregation_users_table $temp_user_table */
    protected $temp_user_table = null;

    /** @var array $user_ids */
    protected $user_ids = null;

    /**
     * Constructor.
     * The source for user_ids can be either a aggregation_users_table or a list of user ids
     *
     * @param aggregation_users_table|array $user_id_source
     */
    final public function __construct($user_id_source) {
        if ($user_id_source instanceof aggregation_users_table) {
            $this->temp_user_table = $user_id_source;
        } else if (is_array($user_id_source)) {
            $this->user_ids = $user_id_source;
        } else {
            throw new \coding_exception('User ids can only be passed via a table or a list of ids');
        }
    }

    /**
     * Evaluate criteria completion / satisfaction for all assigned users
     * and save the last time the
     *
     * @param int $criterion_id
     */
    final public function update_completion(criterion $criterion) {
        $this->update_criterion_completion($criterion);

        // Getting the time before marking updated users to use as last_evaluated time to
        // ensure we don't miss items updating via observers during the marking process
        $now = time();

        // Now we mark all the users that changed since the last time the criterion was evaluated
        $last_evaluated = $criterion->get_last_evaluated() ?? 0;
        $this->mark_temp_users_updated($criterion->get_id(), $last_evaluated);

        $criterion->set_last_evaluated($now)
            ->save_last_evalated();
    }

    abstract protected function update_criterion_completion(criterion $criterion);

    /**
     * Default value to insert into criterion_met for new item records
     * Todo: Could be null for not evaluated yet, meaning we ignore until it is.
     *
     * @return ?int
     */
    protected function get_default_criteria_met(): ?int {
        return 0;
    }


    /**
     * For this and other methods below, I've kept them as there will be code that will be reused, but have made them protected
     * rather than public as the update_completion method is the public API for this.
     *
     * This optionally can be called internally from the update_completion method. You don't have to if for instance, you are
     * not making records in the table used here.
     *
     * @param int $criterion_id
     * @param ?int $timeevaluated
     * @param ?aggregation_users_table $temp_users_table
     */
    protected function create_item_records(int $criterion_id, ?int $timeevaluated = null)
    {
        if (is_null($timeevaluated)) {
            $timeevaluated = time();
        }

        if (!is_null($this->temp_user_table)) {
            $this->create_item_records_from_table($criterion_id, $timeevaluated);
        } else {
            $this->create_item_records_from_list($criterion_id, $timeevaluated);
        }
    }

    /**
     * Create item records for all users in the temp_user_table who doesn't have a item_record
     *
     * @param int $criterion_id
     * @param int $timeevaluated
     * @throws \dml_exception
     */
    private function create_item_records_from_table(int $criterion_id, int $timeevaluated) {
        global $DB;

        if (is_null($this->temp_user_table)) {
            return;
        }

        $temp_table_name = $this->temp_user_table->get_table_name();
        $temp_user_id_column = $this->temp_user_table->get_user_id_column();
        $temp_key_column = $this->temp_user_table->get_key_column();
        $temp_key_value = !empty($temp_key_column) ? $this->temp_user_table->get_key_value() : '';

        $sql = "INSERT INTO {totara_criteria_item_record}
                    (user_id, criterion_item_id, criterion_met, timeevaluated)
                    SELECT tmp." . $temp_user_id_column . ", tci.id, :criterionmet, :timeevaluated
                         FROM {" . $temp_table_name . "} tmp
                         JOIN {totara_criteria_item} tci
                           ON tci.criterion_id = :criterionid
                    LEFT JOIN {totara_criteria_item_record} tcir
                           ON tcir.criterion_item_id = tci.id
                          AND tcir.user_id = tmp." . $temp_user_id_column . "
                        WHERE tcir.id IS NULL";

        $params = [
            'criterionid' => $criterion_id,
            'criterionmet' => $this->get_default_criteria_met(),
            'timeevaluated' => $timeevaluated
        ];

        if (!empty($temp_key_value)) {
            $sql .= " AND tmp." . $temp_key_column . " = :keyvalue";
            $params['keyvalue'] = $temp_key_value;
        }

        $DB->execute($sql, $params);
    }

    /**
     * Create item records for all users in the user_ids list who doesn't have a item_record
     *
     * @param int $criterion_id
     * @param int $timeevaluated
     * @throws \dml_exception
     */
    private function create_item_records_from_list(int $criterion_id, int $timeevaluated)
    {
        global $DB;

        // No need to distinguishe here between not using a list, or having an empty list
        if (empty($this->user_ids)) {
            return;
        }

        $params = [];
        // We first need to find the current user ids, then insert records for missing ones
        [$users_in_sql, $params] = $DB->get_in_or_equal($this->user_ids, SQL_PARAMS_NAMED);

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
            $users_to_add = array_diff($this->user_ids, $user_ids);
            $to_add += array_map(function ($user_id) use ($item_id, $timeevaluated) {
                    return [
                        'user_id' => $user_id,
                        'criterion_item_id' => $item_id,
                        'criterion_met' => $this->get_default_criteria_met(),
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
     * Delete item_records of all users not found in the user id source
     *
     * @param int $criterion_id
     */
    protected function delete_item_records(int $criterion_id)
    {
        if (!is_null($this->temp_user_table)) {
            $this->delete_item_records_from_table($criterion_id);
        } else {
            $this->delete_item_records_from_list($criterion_id);
        }
    }

    /**
     * Delete item records for all users not found in the temp_users table
     *
     * @param int $criterion_id
     */
    private function delete_item_records_from_table(int $criterion_id) {
        global $DB;

        if (is_null($this->temp_user_table)) {
            return;
        }

        $temp_table_name = $this->temp_user_table->get_table_name();
        $temp_user_id_column = $this->temp_user_table->get_user_id_column();
        $temp_key_column = $this->temp_user_table->get_key_column();
        $temp_key_value = !empty($temp_key_column) ? $this->temp_user_table->get_key_value() : '';

        $sql = "DELETE FROM {totara_criteria_item_record}
                      WHERE id IN (
                            SELECT tcir.id
                              FROM {totara_criteria_item} tci
                              JOIN {totara_criteria_item_record} tcir
                                ON tcir.criterion_item_id = tci.id
                             WHERE tci.criterion_id = :criterionid
                               AND tcir.user_id NOT IN (
                                   SELECT $temp_user_id_column
                                     FROM {" . $temp_table_name . "}";
        $params = ['criterionid' => $criterion_id];

        if (!empty($temp_key_value)) {
            $sql .= " WHERE " . $temp_key_column . " = :keyvalue";
            $params['keyvalue'] = $temp_key_value;
        }

        $sql .= " ) )";

        $DB->execute($sql, $params);
    }

    /**
     * Delete item records for all users not found in list of user_ids
     *
     * @param int $criterion_id
     */
    private function delete_item_records_from_list(int $criterion_id) {
        global $DB;

        // If we are not using a list - don't do anything
        // TODO: Should we throw exceptions??
        if (is_null($this->user_ids)) {
            return;
        }

        $sql = "DELETE FROM {totara_criteria_item_record}
                      WHERE id IN (
                            SELECT tcir.id
                              FROM {totara_criteria_item} tci
                              JOIN {totara_criteria_item_record} tcir
                                ON tcir.criterion_item_id = tci.id
                             WHERE tci.criterion_id = :criterionid";

        $params = [];
        if (!empty($this->user)) {
            [$users_in_sql, $params] = $DB->get_in_or_equal($this->user_ids, SQL_PARAMS_NAMED);
            $sql .= " AND tcir.user_id NOT " . $users_in_sql;
        }

        $sql .= ')';
        $params['criterionid'] = $criterion_id;

        $DB->execute($sql, $params);
    }

    /**
     * Update has_changed column for the users whose item_record was updated since the specified time
     *
     * @param int $criterion_id
     * @param int $checkfrom Time to use as start when checking updates of item records
     */
    protected function mark_temp_users_updated(int $criterion_id, int $checkfrom) {
        global $DB;

        if (empty($this->temp_user_table)) {
            return;
        }

        $temp_has_changed_column = $this->temp_user_table->get_has_changed_column();
        if (empty($temp_has_changed_column)) {
            // Not specified - so probably not required
            return;
        }

        $temp_table_name = $this->temp_user_table->get_table_name();
        $temp_user_id_column = $this->temp_user_table->get_user_id_column();
        $temp_key_column = $this->temp_user_table->get_key_column();
        $temp_key_value = !empty($temp_key_column) ? $this->temp_user_table->get_key_value() : '';

        $params = [
            'haschanged' => 1,
            'criterionid' => $criterion_id,
            'checkfrom' => $checkfrom,
        ];

        // We are not clearing the has_changed flag to get the union of users that were updated via any of the criteria
        $sql =
            "UPDATE {" . $temp_table_name . "}
                SET " . $temp_has_changed_column . " = :haschanged
              WHERE ";

        if (!empty($temp_key_value)) {
            $sql .= $temp_key_column . " = :keyvalue AND ";
            $params['keyvalue'] = $temp_key_value;
        }

        $sql .= $temp_user_id_column . " IN (
                    SELECT DISTINCT tcir.user_id
                      FROM {totara_criteria_item} tci
                      JOIN {totara_criteria_item_record} tcir
                        ON tcir.criterion_item_id = tci.id
                       AND tcir.timeevaluated > :checkfrom
                     WHERE tci.criterion_id = :criterionid
                    )";

        $DB->execute($sql, $params);
    }

}
