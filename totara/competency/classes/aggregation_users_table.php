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
 * @package totara_competency
 */

namespace totara_competency;


use totara_competency\achievement_configuration;
use totara_competency\entities\competency;
use totara_competency\entities\scale_value;

/**
 * Class containing information on the table and columns to use to obtain the users to perform achievement aggregation for
 * TODO - naming of the class
 */
class aggregation_users_table {

    /** @var string $table_name Name of the table containing information on the users to consider for aggregation */
    private $table_name = '';

    /** @var string $user_id_column Name of the column containing the ids of users to consider for aggregation */
    private $user_id_column = '';

    /** @var string $has_changed_column Name of the column to set for users that have changes. */
    private $has_changed_column = '';

    // The process key column and value can be used to allow the same table to be re-used in different processes / instances of
    // the aggregation process.
    // If either the process_key_column or process_key_value is not set, all user ids found in the table are used
    /** @var ?string $process_key_column Name of the column containing the process key. */
    private $process_key_column = '';

    /** @@var mixed $process_key Key to used for filtering applicable rows */
    protected $process_key_value = '';

    // The update operation column and value can be used to set the name of the operation responsible for the last change
    // Filtering on the update_operation is always secondary to the process_key
    /** @var ?string $update_operation_column Name of the column containing the update operation value. */
    private $update_operation_column = '';

    /** @@var mixed $update_operation_value Value to used for filtering applicable rows */
    protected $update_operation_value = '';

    /**
     * Constructor.
     */
    final public function __construct(string $table_name, string $user_id_column, ?string $has_changed_column = '',
                                      ?string $process_key_column = '', ?string $update_operation_column = '') {
        if (empty($table_name) || empty($user_id_column)) {
            throw new \coding_exception('The table name and user id column name must be specified');
        }

        $this->table_name = $table_name;
        $this->user_id_column = $user_id_column;
        $this->has_changed_column = $has_changed_column;
        $this->process_key_column = $process_key_column;
        $this->update_operation_column = $update_operation_column;
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
    public function get_has_changed_column(): string {
        return $this->has_changed_column;
    }

    /**
     * @return string
     */
    public function get_process_key_column(): string {
        return $this->process_key_column;
    }

    /**
     * Set the process_key value to filter rows on
     * @param mixed $process_key_value
     */
    public function set_process_key_value($process_key_value): aggregation_users_table {
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
    public function get_update_operation_column(): string {
        return $this->update_operation_column;
    }

    /**
     * Set the update_operation value to filter rows on
     * @param mixed $update_operation_value
     */
    public function set_update_operation_value($update_operation_value): aggregation_users_table {
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


    /************************************************************************************
     * SQL snippets
     ************************************************************************************/

    /**
     * Remove all rows from the users table associated with the key
     *
     * @return aggregation_users_table
     * @throws \dml_exception
     */
    public function truncate(): aggregation_users_table {
        global $DB;

        $DB->delete_records($this->table_name, $this->get_filter());
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
     * @param mixed|null $has_changed_value Value to insert in the has_changed column
     * @return array
     */
    public function get_insert_record(int $user_id_value, $has_changed_value = null): array {
        $record = [$this->get_user_id_column() => $user_id_value];

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
     * Return the column list to use in an insert sql statement
     *
     * @param array $exclude List of columns to exclude in the list
     * @return string
     */
    public function get_insert_column_list(array $exclude_columns=[]): string {
        $column_list = [];
        if (!isset($exclude_columns['user_id'])) {
            $column_list[] = $this->user_id_column;
        };

        if (!empty($this->has_changed_column) && !isset($exclude_columns['has_changed'])) {
            $column_list[] = $this->has_changed_column;
        }

        if (!empty($this->process_key_column) && !empty($this->process_key_value)) {
            $column_list[] = $this->process_key_column;
        }

        return implode(', ', $column_list);
    }

    /**
     * Return the SQL snippet and parameters for inserting
     *
     * @param int|null $user_id_value Value to insert in the user_id column
     * @param mixed|null $has_changed_value Value to insert in the has_changed column
     * @return array [string, array]
     */
    public function get_insert_values_sql_with_params(?int $user_id_value = null, $has_changed_value = null): array {
        $sql = '';
        $params = [];

        $connect = '';
        if (!is_null($user_id_value)) {
            $sql .= "{$this->user_id_column} = :autbl_userid";
            $params['autbl_userid'] = $user_id_value;
            $connect = ', ';
        }

        if (!is_null($has_changed_value) && !empty($this->has_changed_column)) {
            $sql .= "{$connect}{$this->has_changed_column} = :autbl_haschanged";
            $params['autbl_haschanged'] = $has_changed_value;
            $connect = ', ';
        }

        if (!empty($this->process_key_column) && !empty($this->process_key_value)) {
            $sql .= "{$connect}{$this->process_key_column} = :autbl_processkey";
            $params['autbl_processkey'] = $this->process_key_value;
        }

        return [$sql, $params];
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
     * @param string $param_prefix Prefix to use for the parameters
     * @return array [string, array]
     */
    public function get_filter_sql_with_params(string $table_alias = '', bool $include_update_operation = true, $has_change_value = null, string $param_prefix = 'autbl') {
        $sql = '';
        $params = [];

        $table_alias = !empty($table_alias) ? $table_alias . '.' : $table_alias;
        $connect = '';

        if (!empty($this->process_key_value) && !empty($this->process_key_column)) {
            $sql = "{$table_alias}{$this->process_key_column} = :{$param_prefix}_processkey";
            $params[$param_prefix . '_processkey'] = $this->process_key_value;
            $connect = ' AND ';
        }

        if ($include_update_operation && !empty($this->update_operation_column) && !empty($this->update_operation_value)) {
            $sql .= "{$connect}{$table_alias}{$this->update_operation_column} = :{$param_prefix}_updateoperation";
            $params[$param_prefix . '_updateoperation'] = $this->update_operation_value;
            $connect = ' AND ';
        }

        if (!is_null($has_change_value) && !empty($this->has_changed_column)) {
            $sql .= "{$connect}{$table_alias}{$this->has_changed_column} = :{$param_prefix}_haschanged";
            $params[$param_prefix . '_haschanged'] = $has_change_value;
            $connect = ' AND ';
        }

        return [$sql, $params];
    }

}
