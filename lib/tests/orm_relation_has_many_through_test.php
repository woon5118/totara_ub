<?php
/*
 * This file is part of Totara Learn
 *
 * Copyright (C) 2018 onwards Totara Learning Solutions LTD
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
 * @author Aleksandr Baishev <aleksandr.baishev@totaralearning.com>
 * @package core_orm
 * @category test
 */

use core\orm\collection;
use core\orm\entity\repository;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/tests/orm_entity_relation_testcase.php');

/**
 * Class core_orm_relation_has_many_test
 *
 * @package core
 * @group orm
 */
class core_orm_relation_has_many_through_test extends orm_entity_relation_testcase {

    public function test_it_eager_loads_has_many() {
        $records = $this->create_sample_records();

        $children = $records[sample_child_entity::class];

        $collection = sample_passport_entity::repository()
            ->with('children')
            ->order_by('id')
            ->get();

        $queries_count = $this->db()->perf_get_reads();

        foreach ($collection as $item) {
            // Let's get all the children items for this record.
            $children_items = array_filter(
                $children, function ($ci) use ($item) {
                    return $ci['parent_id'] === $item->parent->id;
                }
            );

            $this->assertEquals($item->children->pluck('id'), array_column($children_items, 'id'));
            $this->assertInstanceOf(collection::class, $item->children);

            foreach ($item->children as $child) {
                $this->assertFalse(isset($child->{$item->children()->get_intermediate_key_name()}));
            }
        }

        // Let's make sure that children were eager loaded and no additional query was executed...
        $this->assertEquals(
            $queries_count, $this->db()->perf_get_reads(), 'Eager loading wasn\'t so eager...'
        );
    }

    public function test_it_lazy_loads_has_many() {
        $records = $this->create_sample_records();

        $result = sample_passport_entity::repository()
            ->order_by('id')
            ->first();

        $expected_children = (new collection($records[sample_child_entity::class]))
            ->filter('parent_id', $result->parent->id, true)->to_array();

        $this->assertFalse($result->relation_loaded('children'));

        $children = $result->children;

        $queries_count = $this->db()->perf_get_reads();

        // Let's make sure that fetched related models get cached and will not trigger extra
        // database queries on subsequent calls.
        $this->assertSame($children, $result->children);
        $this->assertEquals($queries_count, $this->db()->perf_get_reads());

        // Let's assert that children have been fetched correctly
        $this->assertEquals($children->pluck('name'), array_column($expected_children, 'name'));
    }

    public function test_it_correctly_handles_empty_sets() {
        $this->create_sample_records();

        // Let's make sure there are records.
        $this->assertGreaterThan(
            0, $this->db()->count_records(sample_child_entity::TABLE)
        );

        $empty = new sample_passport_entity([]);

        $this->assertEmpty($empty->children);
        $this->assertEmpty($empty->children()->get());

        // Let's make sure we have an item without related things
        $entity = sample_passport_entity::repository()
            ->where('name', 'Quality passport')
            ->update(['parent_id' => -1])
            ->with('children')
            ->one();

        $this->assertTrue($entity->relation_loaded('children'));
        $this->assertEmpty($entity->children->all());
        $this->assertEmpty($entity->children()->get());
    }

    public function test_it_handles_lazy_dynamic_conditions() {
        $this->create_sample_records();

        // Let's load the one that doesn't have any items in it.
        $entity = sample_passport_entity::repository()
            ->where('name', 'Precision passport')
            ->one();

        $this->assertEquals(
            'Modern scientific calculator',
            $entity->children()->where('type', 7)->one()->name
        );
    }

    public function test_it_handles_eager_dynamic_conditions() {
        $this->create_sample_records();

        // Let's load the one that doesn't have any items in it.
        $entity = sample_passport_entity::repository()
            ->with([
                'children' => function (repository $repository) {
                    $repository->where('created_at', '633679200');
                }
            ])
            ->where('name', 'Performance passport')
            ->one();

        $this->assertEquals(
            'Apple iMac PRO',
            $entity->children->first()->name
        );
    }

    public function test_it_cant_filter_selected_columns_on_eager_load() {
        $this->create_sample_records();

        // Let's load the one that doesn't have any items in it.
        $entity = sample_passport_entity::repository()
            ->with([
                'children:name,created_at' => function (repository $repository) {
                    $repository->where('created_at', '946684799');
                }
            ])
            ->where('name', 'Quality passport')
            ->one();

        $this->assertDebuggingCalled('Specifying columns is not currently supported for has_many_through relations');

        // Let's make sure there is no extra queries again...
        $queries_count = $this->db()->perf_get_reads();

        // Id will be automatically prepended if not specified.
        $this->assertGreaterThan(0, $entity->children->first()->id);
        $this->assertEquals('Samsung Galaxy Note', $entity->children->first()->name);
        $this->assertEquals('946684799', $entity->children->first()->created_at);

        // Key will be automatically appended if not specified.
        $this->assertEquals($entity->parent->id, $entity->children->first()->parent_id);

        // Let's check that fields we didn't want to select are not present.
        $this->assertTrue(isset($entity->children->first()->type));
        $this->assertTrue(isset($entity->children->first()->description));
        $this->assertTrue(isset($entity->children->first()->updated_at));

        $this->assertEquals($queries_count, $this->db()->perf_get_reads());
    }

    public function test_it_cant_save_related_models() {
        $this->create_tables();

        $passport = new sample_passport_entity([
            'name' => 'Sample parent',
            'parent_id' => 0,
        ]);

        $passport->save();

        $this->expectException(coding_exception::class);
        $this->expectExceptionMessage('This relation does not allow saving models...');

        $passport->children()->save(new sample_child_entity(['name' => 'Sample child']));
    }

    public function test_it_can_bulk_delete_related_models() {
        $this->create_sample_records();

        $passport = sample_passport_entity::repository()
            ->order_by('id')
            ->with('children')
            ->first();

        $total = sample_child_entity::repository()
            ->count();

        $passport->children()
            ->where('name', '!=', 'Casio vintage calculator')
            ->delete();

        $this->assertEquals(
            $total - 2,
            sample_child_entity::repository()->count()
        );

        $child = sample_child_entity::repository()
            ->where('parent_id', $passport->parent->id)
            ->one();

        $this->assertNotNull($child);
        $this->assertEquals('Casio vintage calculator', $child->name);
    }

    public function test_it_can_bulk_update_related_models() {
        $this->create_sample_records();

        $passport = sample_passport_entity::repository()
            ->order_by('parent_id')
            ->with('children')
            ->first();

        $this->assertEmpty(
            sample_child_entity::repository()
                ->where('type', '96')
                ->get()
        );

        $passport->children()
            ->where('name', '!=', 'Samsung Galaxy 6')
            ->update([
                'type' => 96
            ]);

        $this->assertEquals(
            ['Apple iPhone X', 'Samsung Galaxy Note'],
            sample_child_entity::repository()
                ->where('type', 96)
                ->order_by('name')
                ->get()
                ->pluck('name')
        );
    }

    public function test_it_doesnt_update_related_model_on_null_key() {
        $this->create_sample_records();

        $passport = new sample_passport_entity();

        $this->assertEmpty(
            sample_child_entity::repository()
                ->where('type', '96')
                ->get()
        );

        $passport->children()
            ->update([
                'type' => 96
            ]);

        $this->assertEmpty(
            sample_child_entity::repository()
                ->where('type', '96')
                ->get()
        );
    }

    public function test_it_doesnt_delete_related_model_on_null_key() {
        $this->create_sample_records();

        $passport = new sample_passport_entity();

        $count = sample_child_entity::repository()->count();

        $passport->children()
            ->delete();

        $this->assertEquals($count, sample_child_entity::repository()->count());
    }
}
