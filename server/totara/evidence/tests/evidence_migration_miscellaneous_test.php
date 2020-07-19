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
use totara_evidence\entities\evidence_item;
use totara_evidence\entities\evidence_type;
use totara_evidence\entities\evidence_type_field;

require_once(__DIR__ . '/evidence_migration_test.php');

/**
 * @group totara_evidence
 */
class totara_evidence_migration_miscellaneous_testcase extends totara_evidence_migration_testcase {

    /**
     * Make sure that evidence that is used in learning plans is updated with the new evidence records
     */
    public function test_migrate_learning_plans(): void {
        $user1 = $this->generator()->create_evidence_user();
        $user2 = $this->generator()->create_evidence_user();
        $user1_plan = $this->plan_generator()->create_learning_plan(['userid' => $user1->id]);
        $user2_plan = $this->plan_generator()->create_learning_plan(['userid' => $user2->id]);
        $user1_objective = builder::table('dp_plan_objective')->insert([
            'planid' => $user1_plan->id,
            'fullname' => 'user1_objective',
            'scalevalueid' => 0,
        ]);
        $user2_objective = builder::table('dp_plan_objective')->insert([
            'planid' => $user2_plan->id,
            'fullname' => 'user2_objective',
            'scalevalueid' => 0,
        ]);

        $user1_evidence = [];
        $user2_evidence = [];
        for ($i = 1; $i < 5; $i++) {
            $user1_evidence[] = $this->generator()->create_evidence_item(['userid' => $user1->id, 'name' => "user1_evidence$i"]);
            $user2_evidence[] = $this->generator()->create_evidence_item(['userid' => $user2->id, 'name' => "user2_evidence$i"]);
        }

        $this->generator()->create_evidence_relation($user1_evidence[1]->id, $user1_plan->id, $user1_objective);
        $this->generator()->create_evidence_relation($user1_evidence[3]->id, $user1_plan->id, $user1_objective);
        $this->generator()->create_evidence_relation($user2_evidence[1]->id, $user2_plan->id, $user2_objective);
        $this->generator()->create_evidence_relation($user2_evidence[3]->id, $user2_plan->id, $user2_objective);

        totara_evidence_migrate();

        /** @var evidence_item[] $user1_evidence_relations */
        $user1_evidence_relations = evidence_item::repository()
            ->join(['dp_plan_evidence_relation', 'relation'], 'id', 'evidenceid')
            ->where('relation.planid', $user1_plan->id)
            ->order_by('name')
            ->get()->all();

        /** @var evidence_item[] $user2_evidence_relations */
        $user2_evidence_relations = evidence_item::repository()
            ->join(['dp_plan_evidence_relation', 'relation'], 'id', 'evidenceid')
            ->where('relation.planid', $user2_plan->id)
            ->order_by('name')
            ->get()->all();

        $this->assertEquals('user1_evidence2', $user1_evidence_relations[0]->name);
        $this->assertEquals('user1_evidence4', $user1_evidence_relations[1]->name);
        $this->assertEquals('user2_evidence2', $user2_evidence_relations[0]->name);
        $this->assertEquals('user2_evidence4', $user2_evidence_relations[1]->name);
    }

    /**
     * Make sure that custom fields are copied to each evidence type
     */
    public function test_migrate_types_and_fields(): void {
        $old_fields = $this->create_fields();
        $old_types = $this->create_types();

        $this->assertEquals(3, builder::table('dp_evidence_type')->count());
        $this->assertEquals(0, evidence_type::repository()->count());

        totara_evidence_migrate();

        $this->assertEquals(0, builder::table('dp_evidence_type')->count());
        $this->assertEquals(3, evidence_type::repository()->count());

        // Make sure the types have been migrated in order of what they are sorted not ID
        $old_types = [$old_types[2], $old_types[0], $old_types[1]];

        // Order fields by sortorder
        $old_fields = array_reverse($old_fields);

        /** @var evidence_type[] $new_types */
        $new_types = evidence_type::repository()->order_by('id')->get()->all();

        for ($i = 0, $type_count = count($old_types); $i < $type_count; $i++) {
            $this->assertEquals($old_types[$i]->name, $new_types[$i]->name);
            $this->assertEquals($old_types[$i]->description, $new_types[$i]->description);
            $this->assertEquals($old_types[$i]->timemodified, $new_types[$i]->created_at);
            $this->assertEquals($old_types[$i]->timemodified, $new_types[$i]->modified_at);
            $this->assertEquals($old_types[$i]->usermodified, $new_types[$i]->created_by);
            $this->assertEquals($old_types[$i]->usermodified, $new_types[$i]->modified_by);

            /** @var evidence_type_field[] $new_fields */
            $new_fields = $new_types[$i]->fields->all();
            for ($j = 0, $field_count = count($new_fields); $j < $field_count; $j++) {
                $old_field = $old_fields[$j];
                $new_field = (object) $new_fields[$j]->to_array();
                unset($old_field->id, $new_field->id, $new_field->typeid);
                $this->assertEquals($old_field, $new_field);
            }
        }
    }

    /**
     * Make sure that the default set of custom fields are migrated even if no evidence types are defined
     */
    public function test_migrate_fields_with_no_types(): void {
        $old_fields = $this->create_fields();

        $this->assertEquals(4, builder::table('dp_plan_evidence_info_field')->count());
        $this->assertEquals(0, evidence_type_field::repository()->count());
        $this->assertEquals(0, builder::table('dp_evidence_type')->count());
        $this->assertEquals(0, evidence_type::repository()->count());

        totara_evidence_migrate();

        $this->assertEquals(0, builder::table('dp_plan_evidence_info_field')->count());
        $this->assertEquals(4, evidence_type_field::repository()->count());
        $this->assertEquals(0, builder::table('dp_evidence_type')->count());
        $this->assertEquals(1, evidence_type::repository()->count());

        /** @var evidence_type_field[] $new_fields */
        $new_fields = evidence_type_field::repository()->order_by('sortorder', 'desc')->get()->all();

        for ($i = 0, $field_count = count($old_fields); $i < $field_count; $i++) {
            $old_field = $old_fields[$i];
            $new_field = (object) $new_fields[$i]->to_array();
            unset($old_field->id, $new_field->id, $new_field->typeid);
            $this->assertEquals($old_field, $new_field);
        }
    }

    /**
     * Make sure the learning plan evidence tables are deleted after installation as they aren't used anymore
     */
    public function test_tables_dropped_after_installation(): void {
        global $CFG, $DB;
        require_once($CFG->dirroot . '/totara/plan/db/upgradelib.php');
        $dbman = $DB->get_manager();

        $old_tables = [
            new xmldb_table('dp_plan_evidence'),
            new xmldb_table('dp_plan_evidence_info_field'),
            new xmldb_table('dp_plan_evidence_info_data'),
            new xmldb_table('dp_plan_evidence_info_data_param'),
            new xmldb_table('dp_evidence_type'),
        ];

        foreach ($old_tables as $table) {
            $this->assertTrue($dbman->table_exists($table));
        }

        xmldb_totara_evidence_install();

        // The totara_evidence install step doesn't delete the tables - that is the responsibility of totara_plan.
        foreach ($old_tables as $table) {
            $this->assertTrue($dbman->table_exists($table));
        }

        totara_plan_upgrade_remove_evidence_tables();

        foreach ($old_tables as $table) {
            $this->assertFalse($dbman->table_exists($table));
        }
        // dp_plan_evidence_relation is still needed for evidence integration in learning plans.
        $this->assertTrue($dbman->table_exists('dp_plan_evidence_relation'));
    }

}
