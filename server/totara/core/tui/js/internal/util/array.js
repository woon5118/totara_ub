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
 * @author Simon Chester <simon.chester@totaralearning.com>
 * @package totara_core
 */

/**
 * Remove the first instance of the specified item from the array.
 *
 * @param {array} array
 * @param {*} item
 */
export function pull(array, item) {
  const index = array.indexOf(item);
  if (index != -1) {
    array.splice(index, 1);
  }
}

/**
 * Return a copy of array filtered to only contain unique values.
 *
 * @param {array} arr
 * @returns {array}
 */
export function unique(arr) {
  return arr.filter((item, pos) => arr.indexOf(item) === pos);
}

/**
 * Create a new object composed of the selected keys of the provided object.
 *
 * @param {object} object
 * @param {array} keys
 * @return {object}
 */
export function pick(object, keys) {
  if (!Array.isArray(keys)) {
    throw new Error('keys must be an array');
  }
  const newObj = {};
  for (let i = 0; i < keys.length; i++) {
    const key = keys[i];
    if (key in object) {
      newObj[key] = object[key];
    }
  }
  return newObj;
}

/**
 * Splits a collection into sets, group by the result of running each value
 * through `fn`.
 *
 * @param {*} array
 * @param {*} fn
 */
export function groupBy(array, fn) {
  const result = {};
  array.forEach(x => {
    const key = fn(x);
    if (!result[key]) {
      result[key] = [];
    }
    result[key].push(x);
  });
  return result;
}

/**
 * Sort the supplied array using the supplied function to generate a sort key.
 *
 * @param {array} array
 * @param {function} fn
 * @returns {array}
 */
export function orderBy(array, fn) {
  array = array.slice();
  array.sort((a, b) => {
    const aScore = fn(a);
    const bScore = fn(b);
    if (aScore < bScore) {
      return -1;
    } else if (aScore > bScore) {
      return 1;
    } else {
      return 0;
    }
  });
  return array;
}
