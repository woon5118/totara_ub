<?php
/**
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
 * @author Mark Metcalfe <mark.metcalfe@totaralearning.com>
 * @package totara_evidence
 */

use core\orm\query\builder;
use totara_evidence\entities\evidence_field_data_param;
use totara_evidence\entities\evidence_item;
use totara_evidence\entities\evidence_type;
use totara_evidence\entities\evidence_type_field;

require_once(__DIR__ . '/evidence_migration_test.php');

/**
 * @group totara_evidence
 */
class totara_evidence_migration_items_testcase extends totara_evidence_migration_testcase {

    /**
     * Make sure evidence that was manually uploaded by users is migrated correctly
     */
    public function test_migrate_manually_created_evidence(): void {
        $this->create_fields();
        $old_types = $this->create_types();

        $data_count = 0;
        $old_evidence = [];
        for ($i = 1; $i < 5; $i++) {
            $evidence = $this->generator()->create_evidence_item([
                'name' => "Manual Evidence $i",
                'readonly' => 0,
                'evidencetypeid' => 0,
                'timecreated' => $i,
                'timemodified' => $i,
                'usermodified' => $i,
                'userid' => $i,
            ]);
            $this->create_field_data($evidence->id, $data_count);
            $old_evidence[] = $evidence;
        }
        builder::table('dp_plan_evidence')->update_record(['id' => $old_evidence[1]->id, 'evidencetypeid' => $old_types[2]->id]);
        builder::table('dp_plan_evidence')->update_record(['id' => $old_evidence[2]->id, 'evidencetypeid' => $old_types[0]->id]);
        builder::table('dp_plan_evidence')->update_record(['id' => $old_evidence[3]->id, 'evidencetypeid' => $old_types[1]->id]);

        $this->assertEquals(3, builder::table('dp_evidence_type')->count());
        $this->assertEquals(0, evidence_type::repository()->count());
        $this->assertEquals(4, builder::table('dp_plan_evidence')->count());
        $this->assertEquals(0, evidence_item::repository()->count());

        totara_evidence_migrate();

        $this->assertEquals(0, builder::table('dp_evidence_type')->count());
        $this->assertEquals(4, evidence_type::repository()->count());
        $this->assertEquals(0, builder::table('dp_plan_evidence')->count());
        $this->assertEquals(4, evidence_item::repository()->count());

        /**
         * @var evidence_type[] $new_types
         * @var evidence_item[] $new_evidence
         */
        $new_types = evidence_type::repository()->order_by('id')->get()->all();
        $new_evidence = evidence_item::repository()->order_by('id')->get()->all();

        // Make sure the migrated evidence has the correct attributes
        $this->assert_evidence_attributes_are_equal($old_evidence, $new_evidence);

        // Make sure that the field data for each migrated evidence item is correct
        $types_count = count($new_types);
        for ($evidence_num = 0, $total_data_num = 0; $evidence_num < $types_count; $evidence_num++) {
            // Make sure the typeid was migrated
            $this->assertEquals($new_types[$evidence_num]->id, $new_evidence[$evidence_num]->typeid);

            // Make sure the evidence data was correctly migrated to the new evidence.
            // Reverse the data array as the data fields sort order is in reverse.
            $this->assert_evidence_data_is_equal(array_reverse($new_evidence[$evidence_num]->data->all()), $total_data_num);
        }

        // The old tables MUST be empty - otherwise that means everything wasn't migrated correctly
        $this->assert_tables_empty();
    }

    /**
     * Make sure evidence uploaded using the course/certification completion import tool are migrated correctly
     */
    public function test_migrate_completion_evidence(): void {
        $old_fields = $this->create_fields();
        $old_types = $this->create_types();

        $data_count = 0;
        $old_evidence = [];
        for ($i = 1; $i < 4; $i++) {
            $evidence = $this->generator()->create_evidence_item([
                'name' => "Completion Evidence $i",
                'readonly' => 1,
                'evidencetypeid' => 0,
                'timecreated' => $i,
                'timemodified' => $i,
                'usermodified' => $i,
                'userid' => $i,
            ]);
            $this->create_field_data($evidence->id, $data_count);
            $old_evidence[] = $evidence;
        }
        builder::table('dp_plan_evidence')->update_record(['id' => $old_evidence[1]->id, 'evidencetypeid' => $old_types[0]->id]);
        builder::table('dp_plan_evidence')->update_record(['id' => $old_evidence[2]->id, 'evidencetypeid' => $old_types[1]->id]);

        $this->assertEquals(3, builder::table('dp_evidence_type')->count());
        $this->assertEquals(0, evidence_type::repository()->count());
        $this->assertEquals(3, builder::table('dp_plan_evidence')->count());
        $this->assertEquals(0, evidence_item::repository()->count());

        totara_evidence_migrate();

        $this->assertEquals(0, builder::table('dp_evidence_type')->count());
        $this->assertEquals(4, evidence_type::repository()->count());
        $this->assertEquals(0, builder::table('dp_plan_evidence')->count());
        $this->assertEquals(3, evidence_item::repository()->count());

        /** @var evidence_type $legacy_type */
        $legacy_type = evidence_type::repository()->where('idnumber', 'legacycompletionimport')->one();

        // Put the fields in the correct order and shift up one (due to extra type name field)
        $old_fields = [1 => $old_fields[3], 2 => $old_fields[2], 3 => $old_fields[1], 4 => $old_fields[0]];

        // Make sure we store a multi select dropdown field of the old types that used to exist
        /** @var evidence_type_field[] $legacy_type_fields */
        $legacy_type_fields = $legacy_type->fields->all();
        $this->assertEquals('oldtypename3', $legacy_type_fields[0]->shortname);
        $this->assertStringContainsString($old_types[0]->name, $legacy_type_fields[0]->param1);
        $this->assertStringContainsString($old_types[1]->name, $legacy_type_fields[0]->param1);
        for ($i = 1, $field_count = count($legacy_type_fields); $i < $field_count; $i++) {
            $old_field = $old_fields[$i];
            $new_field = (object) $legacy_type_fields[$i]->to_array();
            unset($old_field->id, $new_field->id, $new_field->typeid);
            $this->assertEquals($old_fields[$i], $new_field);
        }

        /** @var evidence_item[] $new_evidence */
        $new_evidence = evidence_item::repository()->order_by('id')->get()->all();

        // Make sure the migrated evidence has the correct attributes
        $this->assert_evidence_attributes_are_equal($old_evidence, $new_evidence);

        // Completion Evidence 1 didn't have a type so it doesn't have a type name field
        $new_data = $new_evidence[0]->data->all();
        $this->assertEquals(0, $new_data[3]->data);
        $this->assertEquals(1, $new_data[2]->data);
        $this->assertEquals(2, $new_data[1]->data);
        $this->assertEquals(3, $new_data[0]->data);
        unset($new_data);

        // Completion Evidence 2 had the type 'Type 1' so it should be in the top field
        $new_data = $new_evidence[1]->data->all();
        $this->assertEquals('Type 1', $new_data[0]->data);
        $this->assertEquals(4, $new_data[4]->data);
        $this->assertEquals(5, $new_data[3]->data);
        $this->assertEquals(6, $new_data[2]->data);
        $this->assertEquals(7, $new_data[1]->data);
        unset($new_data);

        // Completion Evidence 3 had the type 'Type 2' so it should be in the top field
        $new_data = $new_evidence[2]->data->all();
        $this->assertEquals('Type 2', $new_data[0]->data);
        $this->assertEquals(8, $new_data[4]->data);
        $this->assertEquals(9, $new_data[3]->data);
        $this->assertEquals(10, $new_data[2]->data);
        $this->assertEquals(11, $new_data[1]->data);

        unset($new_evidence, $new_data);

        // The old tables MUST be empty - otherwise that means everything wasn't migrated correctly
        $this->assert_tables_empty();
    }

    /**
     * Create evidence field data, storing a unique number per data record.
     * This is so we can assert the evidence has the correct data numbers after migration.
     *
     * @param int $evidence_id
     * @param int $data_num Unique num for the data
     */
    private function create_field_data(int $evidence_id, int &$data_num): void {
        $completion_field_data = builder::table('dp_plan_evidence_info_data')->where('evidenceid', $evidence_id)->get();
        foreach ($completion_field_data as $data) {
            builder::table('dp_plan_evidence_info_data')->update_record(['id' => $data->id, 'data' => $data_num++]);
            for ($param_num = 0; $param_num < 3; $param_num++) {
                builder::table('dp_plan_evidence_info_data_param')->insert(['dataid' => $data->id, 'value' => $param_num]);
            }
        }
    }

    /**
     * Each evidence data record stores a unique number.
     * Make sure that new evidence's data records have the same numbers as before migration.
     *
     * @param array $evidence_data
     * @param int   $total_data_num The total amount of data records that were migrated
     */
    private function assert_evidence_data_is_equal(array $evidence_data, int &$total_data_num): void {
        for ($data_num = 0, $data_count = count($evidence_data); $data_num < $data_count; $data_num++, $total_data_num++) {
            $this->assertEquals($total_data_num, $evidence_data[$data_num]->data);

            // Also verify the data param records
            /** @var evidence_field_data_param[] $params */
            $params = $evidence_data[$data_num]->params->all();
            foreach ($params as $index => $param) {
                $this->assertEquals($index, $params[$index]->value);
            }
        }
    }

    private function assert_evidence_attributes_are_equal(array $old_evidence, array $new_evidence): void {
        for ($i = 0, $evidence_count = count($old_evidence); $i < $evidence_count; $i++) {
            $this->assertEquals($old_evidence[$i]->userid, $new_evidence[$i]->user_id);
            $this->assertEquals($old_evidence[$i]->usermodified, $new_evidence[$i]->created_by);
            $this->assertEquals($old_evidence[$i]->usermodified, $new_evidence[$i]->modified_by);
            $this->assertEquals($old_evidence[$i]->timemodified, $new_evidence[$i]->created_at);
            $this->assertEquals($old_evidence[$i]->timemodified, $new_evidence[$i]->modified_at);
        }
    }

    private function assert_tables_empty(): void {
        $this->assertEquals(0, builder::table('dp_evidence_type')->count());
        $this->assertEquals(0, builder::table('dp_plan_evidence')->count());
        $this->assertEquals(0, builder::table('dp_plan_evidence_info_field')->count());
        $this->assertEquals(0, builder::table('dp_plan_evidence_info_data')->count());
        $this->assertEquals(0, builder::table('dp_plan_evidence_info_data_param')->count());
    }

}
