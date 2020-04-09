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

let counter = 1;

/**
 * Generates an incrementing unique ID.
 *
 * This number is only unique within a page, and is not unique across page
 * loads.
 *
 * @return {number}
 */
export function uniqueId() {
  return counter++;
}

/**
 * Get a result from a value.
 *
 * If value is a function it will be called to obtain the result, otherwise
 * value will be used as-is.
 *
 * @param {*} value
 * @return {*}
 */
export function result(value) {
  if (value instanceof Function) {
    return value();
  }
  return value;
}
