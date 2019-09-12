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
 * @package totara_competency
 */


use totara_competency\aggregation_users_table;

class totara_competency_aggregation_users_table_testcase extends \advanced_testcase {

    private function setup_data() {
        global $DB;

        $data = new class() {
            /** @var aggregation_users_table $tbl */
            public $tbl;
            /** @var array $records */
            public $records;
        };

        $data->tbl = new aggregation_users_table('totara_competency_temp_users',
            'user_id',
            'has_changed',
            'process_key',
            'update_operation_name');

        $data->records = [
            ['user_id' => 1, 'has_changed' => 0, 'process_key' => '', 'update_operation_name' => 'op1'],
            ['user_id' => 2, 'has_changed' => 1, 'process_key' => '', 'update_operation_name' => ''],
            ['user_id' => 3, 'has_changed' => 0, 'process_key' => 'proc2', 'update_operation_name' => ''],
            ['user_id' => 4, 'has_changed' => 1, 'process_key' => 'proc2', 'update_operation_name' => ''],
            ['user_id' => 5, 'has_changed' => 0, 'process_key' => 'proc3', 'update_operation_name' => 'op1'],
            ['user_id' => 6, 'has_changed' => 1, 'process_key' => 'proc3', 'update_operation_name' => 'op1'],
            ['user_id' => 7, 'has_changed' => 0, 'process_key' => 'proc3', 'update_operation_name' => 'op2'],
            ['user_id' => 8, 'has_changed' => 1, 'process_key' => 'proc3', 'update_operation_name' => 'op3'],
            ['user_id' => 9, 'has_changed' => 0, 'process_key' => '', 'update_operation_name' => 'op3'],
            ['user_id' => 10, 'has_changed' => 1, 'process_key' => '', 'update_operation_name' => 'op3'],
        ];

        $DB->insert_records($data->tbl->get_table_name(), $data->records);

        return $data;
    }


    /**
     * Test invalid constructor - no table name
     */
    public function test_constructor_no_tablename() {
        $this->expectException(coding_exception::class);
        $this->expectExceptionMessage('The table name and user id column name must be specified');

        $tbl = new aggregation_users_table('', 'columnname');
    }

    /**
     * Test invalid constructor - no table name
     */
    public function test_constructor_no_user_id_column() {
        $this->expectException(coding_exception::class);
        $this->expectExceptionMessage('The table name and user id column name must be specified');

        $tbl = new aggregation_users_table('tablename', '');
    }

    /**
     * Data provider for test_constructor_getters_setters.
     */
    public function data_provider_test_constructor_getters_setters() {
        return [
            [
                'table_name' => 'a_table_name',
                'user_id_column' => 'a_user_id_column',
            ],
            [
                'table_name' => 'a_table_name',
                'user_id_column' => 'a_user_id_column',
                'has_changed_column' => 'the_has_changed_column',
                'process_key_column' => 'the_process_key_column',
                'process_key_value' => 'process123',
                'update_operation_column' => 'the_update_operation_column',
                'update_operation_value' => 'operation 987',
            ],
        ];
    }

    /**
     * Test constructor, getters and setters
     *
     * @dataProvider data_provider_test_constructor_getters_setters
     */
    public function test_constructor_getters_setters($table_name, $user_id_column, $has_changed_column = '',
                                                     $process_key_column = '', $process_key_value = '',
                                                     $update_operation_column = '', $update_operation_value = '') {
        $tbl = new \totara_competency\aggregation_users_table($table_name, $user_id_column, $has_changed_column, $process_key_column, $update_operation_column);

        $this->assertSame($table_name, $tbl->get_table_name());
        $this->assertSame($user_id_column, $tbl->get_user_id_column());
        $this->assertSame($has_changed_column, $tbl->get_has_changed_column());
        $this->assertSame($process_key_column, $tbl->get_process_key_column());
        $this->assertEmpty($tbl->get_process_key_value());
        $this->assertSame($update_operation_column, $tbl->get_update_operation_column());
        $this->assertEmpty($tbl->get_update_operation_value());

        if (!empty($process_key_value)) {
            $tbl->set_process_key_value($process_key_value);
            $this->assertSame($process_key_value, $tbl->get_process_key_value());
        }

        if (!empty($update_operation_value)) {
            $tbl->set_update_operation_value($update_operation_value);
            $this->assertSame($update_operation_value, $tbl->get_update_operation_value());
        }
    }

    /**
     * Test truncate without process key
     */
    public function test_truncate_no_process_key() {
        global $DB;

        $data = $this->setup_data();
        $this->assertSame(count($data->records), $DB->count_records($data->tbl->get_table_name()));

        // We haven't set a process key - so all rows will be deleted
        $data->tbl->truncate();
        $this->assertSame(0, $DB->count_records($data->tbl->get_table_name()));
    }

    /**
     * Test truncate with process key only
     */
    public function test_truncate_with_process_key() {
        global $DB;

        $data = $this->setup_data();
        $this->assertSame(count($data->records), $DB->count_records($data->tbl->get_table_name()));

        // Set the process key
        $data->tbl->set_process_key_value('proc3');
        $to_delete = array_filter($data->records, function ($record) {
            return $record['process_key'] == 'proc3';
        });

        $expected_record_keys = array_diff(array_keys($data->records), array_keys($to_delete));
        $data->tbl->truncate();
        $this->assertSame(count($expected_record_keys), $DB->count_records($data->tbl->get_table_name()));
    }

    /**
     * Test truncate with update_operation
     */
    public function test_truncate_with_update_operation() {
        global $DB;

        $data = $this->setup_data();
        $this->assertSame(count($data->records), $DB->count_records($data->tbl->get_table_name()));

        // Set the update_operation
        $data->tbl->set_update_operation_value('op3');
        $to_delete = array_filter($data->records, function ($record) {
            return $record['update_operation_name'] == 'op3';
        });

        $expected_record_keys = array_diff(array_keys($data->records), array_keys($to_delete));
        $data->tbl->truncate();
        $this->assertSame(count($expected_record_keys), $DB->count_records($data->tbl->get_table_name()));
    }

    /**
     * Test truncate with process_key and update_operation
     */
    public function test_truncate_with_process_key_and_update_operation() {
        global $DB;

        $data = $this->setup_data();
        $this->assertSame(count($data->records), $DB->count_records($data->tbl->get_table_name()));

        // Set the process key abd update_operation
        $data->tbl->set_process_key_value('proc3');
        $data->tbl->set_update_operation_value('op3');

        $to_delete = array_filter($data->records, function ($record) {
            return $record['process_key'] == 'proc3' && $record['update_operation_name'] == 'op3';
        });

        $expected_record_keys = array_diff(array_keys($data->records), array_keys($to_delete));
        $data->tbl->truncate();
        $this->assertSame(count($expected_record_keys), $DB->count_records($data->tbl->get_table_name()));
    }

    /**
     * Data provider for test_filter
     */
    public function data_provider_test_filter() {
        return [
            [],
            ['process_key' => 'proc1', 'update_operation' => ''],
            ['process_key' => '', 'update_operation' => 'op1'],
            ['process_key' => 'proc3', 'update_operation' => 'op2'],
            ['process_key' => 'proc3', 'update_operation' => 'op2', 'include_update_operation' => false],
        ];
    }

    /**
     * Test get_filter
     *
     * @dataProvider data_provider_test_filter
     */
    public function test_get_filter(string $process_key = '', string $update_operation = '', bool $include_update_operation = true) {
        $data = $this->setup_data();
        $nocolumn_tbl = new aggregation_users_table('tablename', 'useridcolumn');

        $expected = [];
        if (!empty($process_key)) {
            $data->tbl->set_process_key_value($process_key);
            $nocolumn_tbl->set_process_key_value($process_key);

            $expected[$data->tbl->get_process_key_column()] = $process_key;
        }

        if (!empty($update_operation)) {
            $data->tbl->set_update_operation_value($update_operation);
            $nocolumn_tbl->set_update_operation_value($update_operation);

            if ($include_update_operation) {
                $expected[$data->tbl->get_update_operation_column()] = $update_operation;
            }
        }

        $params = $data->tbl->get_filter($include_update_operation);
        $this->assertEqualsCanonicalizing($expected, $params);

        // temp table with no process_key or update_operation columns should not return a filter
        $params = $nocolumn_tbl->get_filter($include_update_operation);
        $this->assertEquals([], $params);
    }

    /**
     * Test get_filter_sql_with_params
     *
     * @dataProvider data_provider_test_filter
     */
    public function test_get_filter_sql_with_params(string $process_key = '', string $update_operation = '', bool $include_update_operation = true) {

        $data = $this->setup_data();
        $nocolumn_tbl = new aggregation_users_table('tablename', 'useridcolumn');

        $table_alias = 'tmp';
        $expected_sql = '';
        $expected_sql_with_alias = '';
        $expected_params = [];
        $connect = '';

        if (!empty($process_key)) {
            $data->tbl->set_process_key_value($process_key);
            $nocolumn_tbl->set_process_key_value($process_key);

            $expected_sql = $data->tbl->get_process_key_column() . ' = :autbl_processkey';
            $expected_sql_with_alias = $table_alias . '.' . $data->tbl->get_process_key_column() . ' = :autbl_processkey';
            $expected_params['autbl_processkey'] = $process_key;
            $connect = ' AND ';
        }

        if (!empty($update_operation)) {
            $data->tbl->set_update_operation_value($update_operation);
            $nocolumn_tbl->set_update_operation_value($update_operation);

            if ($include_update_operation) {
                $expected_sql .= $connect . $data->tbl->get_update_operation_column() . ' = :autbl_updateoperation';
                $expected_sql_with_alias .= $connect . $table_alias . '.' . $data->tbl->get_update_operation_column() . ' = :autbl_updateoperation';
                $expected_params['autbl_updateoperation'] = $update_operation;
            }
        }

        [$sql, $params] = $data->tbl->get_filter_sql_with_params('', $include_update_operation);
        $this->assertEquals($expected_sql, $sql);
        $this->assertEqualsCanonicalizing($expected_params, $params);

        // With a table alias
        [$sql, $params] = $data->tbl->get_filter_sql_with_params($table_alias, $include_update_operation);
        $this->assertEquals($expected_sql_with_alias, $sql);
        $this->assertEquals($expected_params, $params);

        // temp table with no process_key or update_operation columns should not return a filter
        [$sql, $params] = $nocolumn_tbl->get_filter_sql_with_params($table_alias, $include_update_operation);
        $this->assertEquals('', $sql);
        $this->assertEquals([], $params);
    }

    /**
     * Data provider for test_get_set_has_changed_sql_with_params
     */
    public function data_provider_test_get_set_has_changed_sql_with_params() {
        return [
            ['update_operation' => ''],
            ['update_operation' => 'op1'],
        ];
    }

    /**
     * Test get_filter
     *
     * @dataProvider data_provider_test_get_set_has_changed_sql_with_params
     */
    public function test_get_set_has_changed_sql_with_params(string $update_operation = '')
    {
        $data = $this->setup_data();
        $nocolumn_tbl = new aggregation_users_table('tablename', 'useridcolumn');

        $table_alias = 'tmp';
        $expected_sql = $data->tbl->get_has_changed_column() . ' = :agtbl_haschanged';
        $expected_sql_with_alias = $table_alias . '.' . $data->tbl->get_has_changed_column() . ' = :agtbl_haschanged';
        $expected_params = ['agtbl_haschanged' => 1];

        if (!empty($update_operation)) {
            $data->tbl->set_update_operation_value($update_operation);
            $nocolumn_tbl->set_update_operation_value($update_operation);

            $expected_sql .= ', ' . $data->tbl->get_update_operation_column() . ' = :agtbl_updateoperation';
            $expected_sql_with_alias .= ', ' . $table_alias . '.' . $data->tbl->get_update_operation_column() . ' = :agtbl_updateoperation';
            $expected_params['agtbl_updateoperation'] = $update_operation;
        }

        [$sql, $params] = $data->tbl->get_set_has_changed_sql_with_params(1);
        $this->assertEquals($expected_sql, $sql);
        $this->assertEqualsCanonicalizing($expected_params, $params);

        // Now with an alias
        [$sql, $params] = $data->tbl->get_set_has_changed_sql_with_params(1, $table_alias);
        $this->assertEquals($expected_sql_with_alias, $sql);
        $this->assertEqualsCanonicalizing($expected_params, $params);

        // temp table with no process_key or update_operation columns should return empty
        [$sql, $params] = $nocolumn_tbl->get_set_has_changed_sql_with_params(1);
        $this->assertEquals('', $sql);
        $this->assertEquals([], $params);
    }
}
