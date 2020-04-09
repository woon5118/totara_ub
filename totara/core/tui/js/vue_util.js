/*
 * This file is part of Totara Learn
 *
 * Copyright (C) 2020 onwards Totara Learning Solutions LTD
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

import Vue from 'vue';

import { baseSet } from './internal/util/object';

export function getPropDefs(component) {
  return Object.assign({}, Vue.extend(component).options.props);
}

/**
 * Reactively set the value at path of object.
 *
 * Arrays are created for missing index properties and objects are created for other missing properties.
 *
 * @param {object} object Object to modify.
 * @param {array} path Path of property to set, e.g. ['a', 2, 'q']
 * @param {*} value
 * @returns {object}
 */
export function set(object, path, value) {
  baseSet(object, path, value, Vue.set);
}
