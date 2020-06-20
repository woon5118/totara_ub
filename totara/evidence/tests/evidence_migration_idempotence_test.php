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
use totara_evidence\entities\evidence_field_data;
use totara_evidence\entities\evidence_item;
use totara_evidence\entities\evidence_type;

require_once(__DIR__ . '/evidence_migration_test.php');

/**
 * @group totara_evidence
 */
class totara_evidence_migration_idempotence_testcase extends totara_evidence_migration_testcase {

    /**
     * Test that the installation process can be run multiple times and still end up with the same result.
     * This is important for error recovery.
     */
    public function test_migration_idempotence(): void {
        $types = [];
        $evidence = [];
        $this->create_fields();
        $this->create_types_and_evidence(0, $types, $evidence);

        // We want to deliberately cause an error in migration.
        // We do this by setting some invalid data for a field
        $invalid_manual_evidence = end($evidence);
        $old_field = builder::table('dp_plan_evidence_info_data')
            ->where('evidenceid', $invalid_manual_evidence->id)
            ->order_by('id')
            ->first();
        builder::table('dp_plan_evidence_info_data')->update_record([
            'id' => $old_field->id,
            'fieldid' => 0,
        ]);
        try {
            totara_evidence_migrate();
            self::fail("Migration should have thrown an error here but it didn't");
        } catch (Exception $exception) {
            $this->assertEquals('Undefined offset: 0', $exception->getMessage());
            // Return the invalid record to its original valid value
            builder::table('dp_plan_evidence_info_data')->update_record([
                'id' => $old_field->id,
                'fieldid' => $old_field->fieldid,
            ]);
        }

        // Assert that every evidence type and item records were migrated successfully, apart from the last ones
        $this->assert_types_exist(array_slice($types, 0, -1));
        $this->assert_items_and_data_exist(array_slice($evidence, 0, -1));
        $this->assertEquals(0, evidence_item::repository()->where('name', end($types)->name)->count());
        $this->assertEquals(0, evidence_type::repository()->where('name', $invalid_manual_evidence->name)->count());

        $this->create_types_and_evidence(3, $types, $evidence);

        totara_evidence_migrate();

        // The invalid evidence (and it's type) should now also be migrated
        $this->assert_types_exist($types);
        $this->assert_items_and_data_exist($evidence);

        totara_evidence_migrate();

        // Nothing should have changed
        $this->assert_types_exist($types);
        $this->assert_items_and_data_exist($evidence);
    }

    protected function create_fields(): array {
        $this->generator()->create_evidence_field([
            'fullname' => 'Field 1', 'shortname' => 'field1', 'datatype' => 'checkbox',
        ]);
        $this->generator()->create_evidence_field([
            'fullname' => 'Field 2', 'shortname' => 'field2', 'datatype' => 'checkbox',
        ]);
        return [];
    }

    private function create_types_and_evidence(int $start_index, array &$types, array &$evidence): void {
        for ($i = $start_index; $i < $start_index + 3; $i++) {
            $types[] = $this->generator()->create_evidence_type([
                'name' => "Type $i",
                'description' => "Type $i Description",
                'timemodified' => $i,
                'usermodified' => $i,
                'sortorder' => $i,
            ]);
            $evidence[] = $this->generator()->create_evidence_item([
                'name' => "Completion Evidence $i",
                'readonly' => 1,
                'evidencetypeid' => $types[$i]->id,
                'timecreated' => $i,
                'timemodified' => $i,
                'usermodified' => $i,
                'userid' => $i,
            ]);
            $evidence[] = $this->generator()->create_evidence_item([
                'name' => "Manual Evidence $i",
                'readonly' => 0,
                'evidencetypeid' => $types[$i]->id,
                'timecreated' => $i,
                'timemodified' => $i,
                'usermodified' => $i,
                'userid' => $i,
            ]);
        }
    }

    private function assert_types_exist(array $types): void {
        foreach ($types as $type) {
            $this->assertEquals(1, evidence_type::repository()->where('name', $type->name)->count());
        }
    }

    private function assert_items_and_data_exist(array $evidence): void {
        foreach ($evidence as $item) {
            $this->assertEquals(1, evidence_item::repository()->where('name', $item->name)->count());

            // Completion (readonly) evidence has an extra field
            $field_count = 2 + $item->readonly;
            $this->assertEquals($field_count, evidence_field_data::repository()
                ->join([evidence_item::TABLE, 'item'], 'evidenceid', 'id')
                ->where('item.name', $item->name)
                ->count()
            );
        }
    }

}
