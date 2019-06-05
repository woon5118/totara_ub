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
 * @author Fabian Derschatta <fabian.derschatta@totaralearning.com>
 * @package core
 */

namespace core\orm;

use Countable;
use Iterator;
use JsonSerializable;
use stdClass;

/**
 * This is a generic collection accepting an array of items. It provides a set of convenience method to work with the data.
 *
 * It acts as an iterator, is countable and is easily encoded to json.
 *
 * Examples:
 *
 * $collection = new collection([...]);
 * // Convert to array
 * $items = $collection->to_array();
 * // Convert to JSON
 * $items = json_encode($collection);
 * // Count
 * $count = $collection->count();
 * $count = count($collection);
 * // Iterate over items
 * foreach ($collection as $item) {
 *     // do something with the item
 * }
 * // Get values of a single key
 * $ids = $collection->pluck('id');
 *
 * For more examples check out the collection documentation in Confluence.
 */
class collection implements Iterator, JsonSerializable, Countable {

    /**
     * Items in the collection
     *
     * @var array
     */
    protected $items;

    /**
     * Glorified constructor
     *
     * @param array $items
     * @return collection
     */
    public static function new(array $items) {
        return new static($items);
    }


    /**
     * collection constructor.
     *
     * @param array $items Items to add to the collection
     */
    public function __construct(array $items = []) {
        $this->items = $items;
    }

    /**
     * Append item to the collection
     *
     * @param mixed $item Item to add to collection
     * @return $this
     */
    public function append($item) {
        $this->items[] = $item;

        return $this;
    }

    /**
     * Set collection item by key
     *
     * @param mixed $item Item to add to collection
     * @param string $key Key to use supplying an existing key will override an existing item!
     * @return $this
     */
    public function set($item, string $key) {
        $this->items[$key] = $item;

        return $this;
    }

    /**
     * Get the keys for the collection items
     *
     * @return array
     */
    public function keys() {
        return array_keys($this->items);
    }

    /**
     * Pass items in the collection through a given callback and return a fresh copy
     *
     * @param callable $callable Callback
     * @return collection
     */
    public function map(callable $callable) {
        return new static(array_map($callable, $this->items));
    }

    /**
     * Pass items in the collection through a given callback or map to a given class name
     *
     * @param callable|string $what A callable or class name to map collection items to
     * @return collection
     */
    public function map_to($what) {
        $map_to = $what;

        if (is_string($what) && class_exists($what)) {
            $map_to = function ($item) use ($what) {
                return new $what($item);
            };
        }

        return $this->map($map_to);
    }

    /**
     * Transform collection items by passing through a callable
     *
     * @param callable $callable
     * @return $this
     */
    public function transform(callable $callable) {
        $this->items = $this->map($callable)->all(true);

        return $this;
    }

    /**
     * Transform collection items into a class or callable
     *
     * @param string|callable $what
     * @return $this
     */
    public function transform_to($what) {
        $this->items = $this->map_to($what)->all(true);

        return $this;
    }

    /**
     * Key collection items by a column
     *
     * @param string $column Column to key items by
     * @return $this
     */
    public function key_by(string $column) {
        $this->items = array_combine(array_column($this->items, $column), $this->items);

        return $this;
    }

    /**
     * Get values of a single column
     *
     * @param string $name Column name
     * @return array
     */
    public function pluck($name) {
        return array_column($this->items, $name);
    }

    /**
     * Return all items in the collection
     *
     * @param bool $with_keys Preserve original keys
     * @return array
     */
    public function all(bool $with_keys = false): array {
        return $with_keys ? $this->items : array_values($this->items);
    }

    /**
     * Return first item in the collection
     *
     * @return array|stdClass|null
     */
    public function first() {
        return $this->all()[0] ?? null;
    }

    /**
     * Get a single element identified by key from the collection
     *
     * @param string|int $key Item key
     * @return mixed|null
     */
    public function item($key) {
        return $this->items[$key] ?? null;
    }

    /**
     * Returns an array representation of this array with all first level objects normalised to arrays.
     * WARNING: key associations will not be maintained.
     *
     * @return array
     */
    public function to_array(): array {
        $output = [];

        foreach ($this->items as $item) {
            if (method_exists($item, 'to_array')) {
                $output[] = $item->to_array();
            } else {
                $output[] = is_object($item) ? (array) $item : $item;
            }
        }

        return $output;
    }

    /**
     * Check whether an item in the collection is set by key
     *
     * @param string|int $name
     * @return bool
     */
    public function __isset($name) {
        return isset($this->items[$name]);
    }

    /**
     * Unset item in the collection by key
     *
     * @param string|int $name
     */
    public function __unset($name) {
        unset($this->items[$name]);
    }

    /**
     * Return the current element
     *
     * @return mixed Can return any type.
     */
    public function current() {
        return current($this->items);
    }

    /**
     * Move forward to next element
     */
    public function next() {
        next($this->items);
    }

    /**
     * Return the key of the current element
     *
     * @return mixed scalar on success, or null on failure.
     */
    public function key() {
        return key($this->items);
    }

    /**
     * Checks if current position is valid
     *
     * @return boolean The return value will be casted to boolean and then evaluated.
     */
    public function valid() {
        $key = $this->key();

        return $key !== false && !is_null($key);
    }

    /**
     * Rewind the Iterator to the first element
     *
     * @return void Any returned value is ignored.
     */
    public function rewind() {
        reset($this->items);
    }

    /**
     * Specify data which should be serialized to JSON
     *
     * @return mixed data which can be serialized by <b>json_encode</b>,
     */
    public function jsonSerialize() {
        return $this->to_array();
    }

    /**
     * Convert the collection to string will result in JSON representation of the collection
     *
     * @return false|string
     */
    public function __toString() {
        return json_encode($this, JSON_PRESERVE_ZERO_FRACTION | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    /**
     * Count elements of an object
     *
     * @return int The custom count as an integer.
     */
    public function count() {
        return count($this->items);
    }
}