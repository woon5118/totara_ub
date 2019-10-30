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
 * @author Fabian Derschatta <fabian.derschatta@totaralearning.com>
 * @package totara_competency
 */

namespace totara_competency;


use xmldb_table;

/**
 * Class containing information on the table and columns to use to obtain the users to perform achievement aggregation for
 * TODO - naming of the class
 */
class aggregation_users_table {

    /** @var string $table_name Name of the table containing information on the users to consider for aggregation */
    private $table_name = 'totara_competency_aggregation_queue';

    /** @var string $user_id_column Name of the column containing the ids of users to consider for aggregation */
    private $user_id_column = 'user_id';

    /** @var string $competency_id_column Name of the column containing the ids of competencies to consider for aggregation */
    private $competency_id_column = 'competency_id';

    /** @var string $has_changed_column Name of the column to set for users that have changes. */
    private $has_changed_column = 'has_changed';

    // The process key column and value can be used to allow the same table to be re-used in different processes / instances of
    // the aggregation process.
    // If either the process_key_column or process_key_value is not set, all user ids found in the table are used
    /** @var ?string $process_key_column Name of the column containing the process key. */
    private $process_key_column = 'process_key';

    /** @@var mixed $process_key Key to used for filtering applicable rows */
    protected $process_key_value = '';

    // The update operation column and value can be used to set the name of the operation responsible for the last change
    // Filtering on the update_operation is always secondary to the process_key
    /** @var ?string $update_operation_column Name of the column containing the update operation value. */
    private $update_operation_column = 'update_operation_name';

    /** @@var mixed $update_operation_value Value to used for filtering applicable rows */
    protected $update_operation_value = '';

    /**
     * @param string $table_name
     * @param bool $is_temporary
     * @param string $user_id_column
     * @param string|null $competency_id_column
     * @param string|null $has_changed_column
     * @param string|null $process_key_column
     * @param string|null $update_operation_column
     */
    final public function __construct(
        string $table_name = '',
        bool $is_temporary = false,
        string $user_id_column = '',
        string $competency_id_column = '',
        ?string $has_changed_column = '',
        ?string $process_key_column = '',
        ?string $update_operation_column = ''
    ) {
        // Set defaults for this table
        if (!empty($table_name)) {
            $this->table_name = $table_name;
        }
        if (!empty($user_id_column)) {
            $this->user_id_column = $user_id_column;
        }
        if (!empty($competency_id_column)) {
            $this->competency_id_column = $competency_id_column;
        }
        if (!empty($has_changed_column) || is_null($has_changed_column)) {
            $this->has_changed_column = $has_changed_column;
        }
        if (!empty($process_key_column) || is_null($process_key_column)) {
            $this->process_key_column = $process_key_column;
        }
        if (!empty($update_operation_column) || is_null($update_operation_column)) {
            $this->update_operation_column = $update_operation_column;
        }

        if ($is_temporary) {
            $this->create_temp_table();
        }
    }

    /**
     * Generate a temporary table or if it already exists truncate it
     */
    private function create_temp_table() {
        global $CFG, $DB;

        require_once($CFG->dirroot . '/lib/ddllib.php');

        $dbman = $DB->get_manager();
        $table = new xmldb_table($this->table_name);

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE);
        $table->add_field($this->competency_id_column, XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL);
        $table->add_field($this->user_id_column, XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL);
        if ($this->has_changed_column) {
            $table->add_field($this->has_changed_column, XMLDB_TYPE_INTEGER, '1');
        }
        if ($this->process_key_column) {
            $table->add_field($this->process_key_column, XMLDB_TYPE_CHAR, '255');
        }
        if ($this->update_operation_column) {
            $table->add_field($this->update_operation_column, XMLDB_TYPE_CHAR, '255');
        }

        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        $table->add_index('competency_id', XMLDB_INDEX_NOTUNIQUE, ['competency_id']);
        $table->add_index('user_id', XMLDB_INDEX_NOTUNIQUE, [$this->user_id_column]);
        if ($this->process_key_column) {
            $table->add_index('process_key_column', XMLDB_INDEX_NOTUNIQUE, [$this->process_key_column]);
        }
        if ($this->update_operation_column) {
            $table->add_index('update_operation_column', XMLDB_INDEX_NOTUNIQUE, [$this->update_operation_column]);
        }

        if ($dbman->table_exists($table)) {
            $this->truncate();
        } else {
            $dbman->create_temp_table($table);
        }
    }

    /**
     * @return string
     */
    public function get_table_name(): string {
        return $this->table_name;
    }

    /**
     * @return string
     */
    public function get_user_id_column(): string {
        return $this->user_id_column;
    }

    /**
     * @return string
     */
    public function get_competency_id_column(): string {
        return $this->competency_id_column;
    }

    /**
     * @return string
     */
    public function get_has_changed_column(): ?string {
        return $this->has_changed_column;
    }

    /**
     * @return string
     */
    public function get_process_key_column(): ?string {
        return $this->process_key_column;
    }

    /**
     * Set the process_key value to filter rows on
     *
     * @param mixed $process_key_value
     * @return self
     */
    public function set_process_key_value($process_key_value): self {
        $this->process_key_value = (string)$process_key_value;
        return $this;
    }

    /**
     * Return the key value
     * @return mixed
     */
    public function get_process_key_value() {
        return $this->process_key_value;
    }

    /**
     * @return string
     */
    public function get_update_operation_column(): ?string {
        return $this->update_operation_column;
    }

    /**
     * Set the update_operation value to filter rows on
     *
     * @param mixed $update_operation_value
     * @return self
     */
    public function set_update_operation_value($update_operation_value): self {
        $this->update_operation_value = $update_operation_value;
        return $this;
    }

    /**
     * Return the currently set update operation value
     * @return mixed
     */
    public function get_update_operation_value() {
        return $this->update_operation_value;
    }

    /**
     * Remove all rows from the users table associated with the key
     *
     * @return self
     */
    public function truncate(): self {
        global $DB;
        $DB->execute("TRUNCATE TABLE {{$this->table_name}}");
        return $this;
    }

    /**
     * Reset the has_changed flag for all users
     * @param mixed $new_has_changed_value Value to set the has_changed_column to
     * @return aggregation_users_table
     * @throws \dml_exception
     */
    public function reset_has_changed($new_has_changed_value): aggregation_users_table {
        global $DB;

        if (empty($this->has_changed_column)) {
            return $this;
        }

        $DB->execute(
            'UPDATE {' . $this->table_name . '} SET ' . $this->get_has_changed_column() . ' = :newvalue',
            ['newvalue' => $new_has_changed_value]
        );

        return $this;
    }

    /**
     * Return sql snippet to update the has_changed_column.
     * If an update operation column and value is set, these are also included
     *
     * @param mixed $new_has_changed_value Value to set the has_changed_column to
     * @param  string $table_alias
     * @return [string, array] Array containing the column(s) and parameters
     */
    public function get_set_has_changed_sql_with_params($new_has_changed_value, string $table_alias = ''): array {
        $sql = '';
        $params = [];

        if (empty($this->has_changed_column)) {
            return [$sql, $params];
        }

        $table_alias = !empty($table_alias) ? $table_alias . '.' : $table_alias;

        $sql = "{$table_alias}{$this->has_changed_column} = :agtbl_haschanged";
        $params['agtbl_haschanged'] = $new_has_changed_value;

        if (!empty($this->update_operation_column) && !empty($this->update_operation_value)) {
            $sql .= ", {$table_alias}{$this->update_operation_column} = :agtbl_updateoperation";
            $params['agtbl_updateoperation'] = $this->update_operation_value;
        }

        return [$sql, $params];
    }

    /**
     * Return array of column values to use in insert
     * If an update operation column and value is set, these are also included
     *
     * @param int $user_id_value Value to insert in the user_id column
     * @param int $competency_id_value Value to insert in the competency_id column
     * @param mixed|null $has_changed_value Value to insert in the has_changed column
     * @return array
     */
    public function get_insert_record(int $user_id_value, int $competency_id_value, $has_changed_value = null): array {
        $record = [
            $this->get_user_id_column() => $user_id_value,
            $this->get_competency_id_column() => $competency_id_value
        ];

        if (!is_null($has_changed_value) && empty($this->has_changed_column)) {
            $record[$this->get_has_changed_column()] = $has_changed_value;
        }

        if (!empty($this->process_key_column) && !empty($this->process_key_value)) {
            $record[$this->process_key_column] = $this->process_key_value;
        }

        if (!empty($this->update_operation_column) && !empty($this->update_operation_value)) {
            $record[$this->update_operation_column] = $this->update_operation_value;
        }

        return $record;
    }

    /**
     * Return the SQL snippet and parameters for inserting
     *
     * @param int|null $user_id_value
     * @param int|null $comp_id_value
     * @param null $has_changed_value
     * @return array [string, array]
     */
    public function get_insert_values_sql_with_params(
        ?int $user_id_value = null,
        ?int $comp_id_value = null,
        $has_changed_value = null
    ): array {
        $sql = [];
        $params = [];

        if (!is_null($user_id_value)) {
            $sql[] = ":user_id";
            $params['user_id'] = $user_id_value;
        }

        if (!is_null($comp_id_value)) {
            $sql[] = ":competency_id";
            $params['competency_id'] = $comp_id_value;
        }

        if (!is_null($has_changed_value) && !empty($this->has_changed_column)) {
            $sql[] = ":has_changed";
            $params['has_changed'] = $has_changed_value;
        }

        if (!empty($this->process_key_column) && !empty($this->process_key_value)) {
            $sql[] =  ":process_key";
            $params['process_key'] = $this->process_key_value;
        }

        return [implode(", ", $sql), $params];
    }

    /**
     * Return the parameters for filtering on the process_key and update_operation
     *
     * @param  bool $include_update_operation Include filtering on the update_operation?
     * @return array
     */
    public function get_filter(bool $include_update_operation = true) {
        $filter = [];

        if (!empty($this->process_key_column) && !empty($this->process_key_value)) {
            $filter[$this->process_key_column] = $this->process_key_value;
        }

        if ($include_update_operation && !empty($this->update_operation_column) && !empty($this->update_operation_value)) {
            $filter[$this->update_operation_column] = $this->update_operation_value;
        }

        return $filter;
    }

    /**
     * Return the SQL snippet and parameters for filtering on the process_key
     *
     * @param string $table_alias
     * @param bool $include_update_operation Include filtering on the update_operation?
     * @param mixed|null $has_change_value Include filtering with this has_changed_value
     * @param int|null $competency_id_value
     * @param string $param_prefix Prefix to use for the parameters
     * @return array [string, array]
     */
    public function get_filter_sql_with_params(
        string $table_alias = '',
        bool $include_update_operation = true,
        $has_change_value = null,
        ?int $competency_id_value = null,
        string $param_prefix = 'autbl'
    ) {
        $sql_parts = [];
        $params = [];

        $table_alias = !empty($table_alias) ? $table_alias . '.' : $table_alias;

        if (!is_null($competency_id_value)) {
            $sql_parts[] = "{$table_alias}{$this->competency_id_column} = :{$param_prefix}_competency_id";
            $params[$param_prefix . '_competency_id'] = $competency_id_value;
        }

        if (!empty($this->process_key_value) && !empty($this->process_key_column)) {
            $sql_parts[] = "{$table_alias}{$this->process_key_column} = :{$param_prefix}_processkey";
            $params[$param_prefix . '_processkey'] = $this->process_key_value;
        }

        if ($include_update_operation && !empty($this->update_operation_column) && !empty($this->update_operation_value)) {
            $sql_parts[] = "{$table_alias}{$this->update_operation_column} = :{$param_prefix}_updateoperation";
            $params[$param_prefix . '_updateoperation'] = $this->update_operation_value;
        }

        if (!is_null($has_change_value) && !empty($this->has_changed_column)) {
            $sql_parts[] = "{$table_alias}{$this->has_changed_column} = :{$param_prefix}_haschanged";
            $params[$param_prefix . '_haschanged'] = $has_change_value;
        }

        return [implode(' AND ', $sql_parts), $params];
    }

    /**
     * Claim all rows with current process key which are not claimed yet
     *
     * @return $this
     */
    public function claim_process() {
        global $DB;

        $DB->set_field(
            $this->table_name,
            $this->process_key_column,
            $this->process_key_value,
            [ $this->process_key_column => null ]
        );

        return $this;
    }

    /**
     * Queue aggregation to be processed in the background, only if no record is already queued
     *
     * @param int $user_id
     * @param int $competency_id
     */
    public function queue_for_aggregation(int $user_id, int $competency_id): void {
        global $DB;

        $exists = $DB->record_exists(
            $this->table_name,
            [
                $this->user_id_column => $user_id,
                $this->competency_id_column => $competency_id,
                $this->process_key_column => null
            ]
        );

        if (!$exists) {
            $record = $this->get_insert_record($user_id, $competency_id);
            $DB->insert_record($this->table_name, $record);
        }
    }

    /**
     * Queue aggregation to be processed in the background for all assigned users
     *
     * @param int $competency_id
     */
    public function queue_all_assigned_users_for_aggregation(int $competency_id): void {
        global $DB;

        if (!empty($this->process_key_column)) {
            $process_key_wh = " AND agg_queue.{$this->process_key_column} IS NULL";
        }
        $sql =
            "SELECT tacu.user_id
               FROM {totara_assignment_competency_users} tacu
          LEFT JOIN {{$this->get_table_name()}} agg_queue
                 ON agg_queue.{$this->competency_id_column} = tacu.competency_id
                AND agg_queue.{$this->user_id_column} = tacu.user_id
                    {$process_key_wh}
              WHERE tacu.competency_id = :compid
                AND agg_queue.id IS NULL";

        $to_add = $DB->get_fieldset_sql($sql, ['compid' => $competency_id]);
        $to_add = array_map(
            function ($user_id) use ($competency_id) {
                return ['competency_id' => $competency_id, 'user_id' => $user_id, 'process_key' => null];
            },
            $to_add
        );

        $batches = array_chunk($to_add, BATCH_INSERT_MAX_ROW_COUNT);
        foreach ($batches as $rows) {
            $DB->insert_records($this->table_name, $rows);
        }
    }

    /**
     * delete all rows belonging to the current process
     */
    public function delete(): void {
        global $DB;

        $params = [];
        if ($this->process_key_value) {
            $params[$this->process_key_column] = $this->process_key_value;
        }

        if ($this->update_operation_value) {
            $params[$this->update_operation_column] = $this->update_operation_value;
        }

        $DB->delete_records($this->table_name, $params);
    }

}
