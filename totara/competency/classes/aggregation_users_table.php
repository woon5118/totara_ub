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

    // The key column and value can be used to allow the same table to be re-used in different processes / instances of
    // the aggregation process.
    // If either the key_column or key_value is not set, all user ids found in the table are used
    /** @var ?string $key_column Name of the column containing a unique key. */
    private $key_column = '';

    /** @@var mixed $key Key to used for filtering applicable rows */
    protected $key_value = '';

    /**
     * Constructor.
     */
    final public function __construct(string $table_name, string $user_id_column, ?string $has_changed_column = '', ?string $key_column = '') {
        if (empty($table_name) || empty($user_id_column)) {
            throw new \coding_exception('The table name and user id column name must be specified');
        }

        $this->table_name = $table_name;
        $this->user_id_column = $user_id_column;
        $this->has_changed_column = $has_changed_column;
        $this->key_column = $key_column;
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
    public function get_key_column(): string {
        return $this->key_column;
    }

    /**
     * Set the key value to filter rows on
     * @param mixed $key_value
     */
    public function set_key_value($key_value): aggregation_users_table {
        $this->key_value = (string)$key_value;
        return $this;
    }

    /**
     * Return the key value
     * @return mixed
     */
    public function get_key_value() {
        return $this->key_value;
    }

    /**
     * Remove all rows from the users table associated with the key
     *
     * @return aggregation_users_table
     * @throws \dml_exception
     */
    public function truncate(): aggregation_users_table {
        global $DB;

        $params = !empty($this->key_value) ? [$this->key_column => $this->key_value] : [];
        $DB->delete_records($this->table_name, $params);

        return $this;
    }
}
