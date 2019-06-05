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
 * @package core
 * @group orm
 */

use core\orm\collection;

defined('MOODLE_INTERNAL') || die();

/**
 * Class core_orm_collection_testcase
 *
 * @package core
 * @group orm
 */
class core_orm_collection_testcase extends advanced_testcase {

    public function test_it_returns_collection() {
        $collection = new collection([]);

        $this->assertInstanceOf(collection::class, $collection);
    }

    public function test_it_collects_items() {
        $collection = new collection([]);

        $this->assertEmpty($collection->all(), 'Must return an empty collection');

        $items = $this->get_dummy_unkeyed_items();
        $collection = new collection($items);

        $this->assertIsArray($collection->all());
        $this->assertSameSize($items, $collection->all());
        $this->assertSame($items, $collection->all());
        $this->assertSame($items, $collection->all(true));
    }

    public function test_it_handles_no_items() {
        $collection = new collection();
        $this->assertIsArray($collection->all());
        $this->assertEmpty($collection->all());
        $this->assertSame([], $collection->to_array());
        $this->assertSame([], $collection->keys());
        $this->assertNull($collection->first());
    }

    public function test_it_converts_items_to_array() {
        $items = $this->get_dummy_unkeyed_items();
        $collection = new collection($items);
        $this->assertSame(array_values($items), $collection->to_array());

        $items = $this->get_dummy_keyed_sequential_ordered_items();
        $collection = new collection($items);
        $this->assertSame(array_values($items), $collection->to_array());

        $items = $this->get_dummy_keyed_mismatched_key_items();
        $collection = new collection($items);
        $this->assertSame(array_values($items), $collection->to_array());

        $items = $this->get_dummy_keyed_non_sequential_unordered_items();
        $collection = new collection($items);
        $this->assertSame(array_values($items), $collection->to_array());
    }

    public function test_it_returns_item_keys() {
        $items = $this->get_dummy_unkeyed_items();
        $collection = new collection($items);
        $this->assertSame(array_keys($items), $collection->keys());

        $items = $this->get_dummy_keyed_sequential_ordered_items();
        $collection = new collection($items);
        $this->assertSame(array_keys($items), $collection->keys());

        $items = $this->get_dummy_keyed_mismatched_key_items();
        $collection = new collection($items);
        $this->assertSame(array_keys($items), $collection->keys());

        $items = $this->get_dummy_keyed_non_sequential_unordered_items();
        $collection = new collection($items);
        $this->assertSame(array_keys($items), $collection->keys());
    }

    public function test_it_compacts_array_to_a_single_dimension() {
        $items = $this->get_dummy_unkeyed_items();
        $collection = new collection($items);
        $this->assertSame(array_column($items, 'name'), $collection->pluck('name'));

        $items = $this->get_dummy_keyed_sequential_ordered_items();
        $collection = new collection($items);
        $this->assertSame(array_column($items, 'name'), $collection->pluck('name'));

        $items = $this->get_dummy_keyed_mismatched_key_items();
        $collection = new collection($items);
        $this->assertSame(array_column($items, 'name'), $collection->pluck('name'));

        $items = $this->get_dummy_keyed_non_sequential_unordered_items();
        $collection = new collection($items);
        $this->assertSame(array_column($items, 'name'), $collection->pluck('name'));
    }

    public function test_it_returns_all_items_in_collection() {
        $items = $this->get_dummy_unkeyed_items();
        $collection = new collection($items);
        $this->assertSame($items, $collection->all());
        $this->assertSame($items, $collection->all(true));

        $items = $this->get_dummy_keyed_sequential_ordered_items();
        $collection = new collection($items);
        $this->assertNotSame($items, $collection->all());
        $this->assertSame(array_values($items), $collection->all());
        $this->assertSame($items, $collection->all(true));

        $items = $this->get_dummy_keyed_mismatched_key_items();
        $collection = new collection($items);
        $this->assertNotSame($items, $collection->all());
        $this->assertSame(array_values($items), $collection->all());
        $this->assertSame($items, $collection->all(true));

        $items = $this->get_dummy_keyed_non_sequential_unordered_items();
        $collection = new collection($items);
        $this->assertNotSame($items, $collection->all());
        $this->assertSame(array_values($items), $collection->all());
        $this->assertSame($items, $collection->all(true));
    }

    public function test_it_returns_first_item_in_collection() {
        $items = $this->get_dummy_unkeyed_items();
        $collection = new collection($items);
        $this->assertSame(reset($items), $collection->first());

        $items = $this->get_dummy_keyed_sequential_ordered_items();
        $collection = new collection($items);
        $this->assertSame(reset($items), $collection->first());

        $items = $this->get_dummy_keyed_mismatched_key_items();
        $collection = new collection($items);
        $this->assertSame(reset($items), $collection->first());

        $items = $this->get_dummy_keyed_non_sequential_unordered_items();
        $collection = new collection($items);
        $this->assertSame(reset($items), $collection->first());
    }

    public function test_it_returns_requested_item_in_collection() {
        $items = $this->get_dummy_unkeyed_items();
        $collection = new collection($items);
        $this->assertSame($items[2], $collection->item(2));
        $this->assertSame($items[0], $collection->item(0));
        $this->assertNull($collection->item(13));

        $items = $this->get_dummy_keyed_sequential_ordered_items();
        $collection = new collection($items);
        $this->assertEquals($items[2], $collection->item(2));
        $this->assertNull($collection->item(0));
        $this->assertNull($collection->item(13));

        $items = $this->get_dummy_keyed_mismatched_key_items();
        $collection = new collection($items);
        $this->assertEquals($items[18], $collection->item(18));
        $this->assertNull($collection->item(0));
        $this->assertNull($collection->item(13));

        $items = $this->get_dummy_keyed_non_sequential_unordered_items();
        $collection = new collection($items);
        $this->assertEquals($items[64], $collection->item(64));
        $this->assertNull($collection->item(0));
        $this->assertNull($collection->item(13));
    }

    public function test_transform_to() {

        $callback = function ($item) {
            return new class($item) {
                private $record;
                public function __construct($record) {
                    $this->record = $record;
                }
                public function name() {
                    return $this->record['name'];
                }
            };
        };

        $items = $this->get_dummy_unkeyed_items();
        $collection = (new collection($items))->transform_to($callback);
        $this->assertSameSize($items, $collection->all());
        $this->assertSame(reset($items)['name'], $collection->first()->name());
        $this->assertSame(array_keys($items), $collection->keys());

        $items = $this->get_dummy_keyed_sequential_ordered_items();
        $collection = (new collection($items))->transform_to($callback);
        $this->assertSameSize($items, $collection->all());
        $this->assertSame(reset($items)['name'], $collection->first()->name());
        $this->assertSame(array_keys($items), $collection->keys());

        $items = $this->get_dummy_keyed_non_sequential_unordered_items();
        $collection = (new collection($items))->transform_to($callback);
        $this->assertSameSize($items, $collection->all());
        $this->assertSame(reset($items)['name'], $collection->first()->name());
        $this->assertSame(array_keys($items), $collection->keys());

        $items = $this->get_dummy_keyed_mismatched_key_items();
        $collection = (new collection($items))->transform_to($callback);
        $this->assertSameSize($items, $collection->all());
        $this->assertSame(reset($items)['name'], $collection->first()->name());
        $this->assertSame(array_keys($items), $collection->keys());
    }

    public function test_it_maps_callback_to_collection_items() {
        $items = $this->get_dummy_unkeyed_items();

        $collection = new collection($items);

        $new = $collection->map(function ($item) {
            return $item['id'];
        });

        $this->assertEquals(array_column($items, 'id'), $new->all());

        $mapper = new class(['id' => null]) {
            public $new_mapped_id;

            public function __construct($item) {
                $this->new_mapped_id = $item['id'];
            }
        };

        $new = (new collection($items))
            ->map_to(get_class($mapper))
            ->all();

        $this->assertEquals(array_column($items, 'id'), array_column($new, 'new_mapped_id'));
    }

    public function test_it_transforms_collection_items() {
        $items = $this->get_dummy_unkeyed_items();

        $collection = new collection($items);

        $collection->transform(function ($item) {
            return $item['id'];
        });

        $this->assertEquals(array_column($items, 'id'), $collection->all());

        $mapper = new class(['id' => null]) {
            public $new_mapped_id;

            public function __construct($item) {
                $this->new_mapped_id = $item['id'];
            }
        };

        $collection = (new collection($items))
            ->map_to(get_class($mapper))
            ->all();

        $this->assertEquals(array_column($items, 'id'), array_column($collection, 'new_mapped_id'));
    }

    public function test_it_keys_items_by_a_given_column() {
        $items = $this->get_dummy_unkeyed_items();

        $collection = new collection($items);

        $collection->key_by('name');

        $this->assertEquals(array_column($items, 'name'), $collection->keys());
    }

    public function test_it_appends_an_item_to_collection() {
        // Append sequential item
        $items = $this->get_dummy_unkeyed_items();

        $collection = new collection($items);

        $appended = ['hey' => 'bro'];

        $collection->append($appended);

        $this->assertCount(count($items) + 1, $collection->all());

        $item = $collection->all()[count($collection) - 1];

        $this->assertEquals($appended, $item);

        // Append item with a key
        $items = $this->get_dummy_keyed_sequential_ordered_items();

        $collection = new collection($items);

        $collection->set($appended, 'my_key');

        $this->assertCount(count($items) + 1, $collection->all());

        $item = $collection->all(true)['my_key'];

        $this->assertEquals($appended, $item);
    }

    public function test_it_counts_items_properly() {
        $items = $this->get_dummy_unkeyed_items();

        $collection = new collection($items);

        $this->assertCount(count($items), $collection);
        $this->assertEquals(count($items), $collection->count());
    }

    public function test_it_does_isset_and_unset_properly() {
        $items = $this->get_dummy_keyed_sequential_ordered_items();


        $collection = new collection($items);

        $this->assertTrue(isset($collection->{1}));
        $this->assertFalse(isset($collection->undefined));

        unset($collection->{1});

        $this->assertFalse(isset($collection->{1}));
        $this->assertCount(count($items) - 1, $collection);
    }

    public function test_it_converts_items_to_json() {
        $items = $this->get_dummy_unkeyed_items();

        $json = json_encode($items);

        $collection = new collection($items);

        $this->assertEquals($json, json_encode($collection));
        $this->assertEquals($json, (string) $collection);
    }

    public function test_it_converts_collection_of_entities_to_array() {
        $callback = function ($item) {
            return new class($item) {
                private $record;
                public function __construct($record) {
                    $this->record = $record;
                }
                public function name() {
                    return $this->record['name'];
                }
                public function to_array() {
                    return $this->record;
                }
            };
        };

        $items = $this->get_dummy_keyed_sequential_ordered_items();
        $collection = (new collection($items))->transform($callback);
        $this->assertSameSize($items, $collection->to_array());
        $this->assertSame(array_values($items), $collection->to_array());
    }

    public function get_dummy_unkeyed_items() {
        // It's have non-sequential keys that are out of order to ensure that keys are handled correctly.
        return [
            [
                'id' => 1,
                'name' => 'John',
            ],
            [
                'id' => 2,
                'name' => 'Jane',
            ],
            [
                'id' => 3,
                'name' => 'Ashley',
            ],
        ];
    }

    public function get_dummy_keyed_sequential_ordered_items() {
        // It's have non-sequential keys that are out of order to ensure that keys are handled correctly.
        return [
            1 => [
                'id' => 1,
                'name' => 'John',
            ],
            2 => [
                'id' => 2,
                'name' => 'Jane',
            ],
            3 => [
                'id' => 3,
                'name' => 'Ashley',
            ],
        ];
    }

    public function get_dummy_keyed_mismatched_key_items() {
        // It's have non-sequential keys that are out of order to ensure that keys are handled correctly.
        return [
            1 => [
                'id' => 37,
                'name' => 'John',
            ],
            18 => [
                'id' => 2,
                'name' => 'Jane',
            ],
            69 => [
                'id' => 96,
                'name' => 'Ashley',
            ],
        ];
    }

    public function get_dummy_keyed_non_sequential_unordered_items() {
        // It's have non-sequential keys that are out of order to ensure that keys are handled correctly.
        return [
            57 => [
                'id' => 57,
                'name' => 'John',
            ],
            64 => [
                'id' => 64,
                'name' => 'Jane',
            ],
            59 => [
                'id' => 59,
                'name' => 'Ashley',
            ],
        ];
    }
}
