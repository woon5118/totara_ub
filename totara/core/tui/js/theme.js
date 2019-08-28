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

const possiblyIE =
  typeof navigator !== 'undefined' &&
  /MSIE|Trident\//.test(navigator.userAgent);

let rootStyle = null;

if (typeof window !== 'undefined') {
  rootStyle = window.getComputedStyle(document.documentElement);
}

const varCache = new Map();
const arrayVarCache = new Map();

export default {
  /**
   * Get the value of a root-level custom property.
   * Omit the leading '--'.
   *
   * @param {string} name
   * @return {?string}
   */
  getVar(name) {
    if (!rootStyle) {
      return;
    }
    if (varCache.has(name)) {
      return varCache.get(name);
    }
    // this code won't execute until styles in head have loaded so it's okay.
    // HTML5 specifies that JS that comes after CSS waits for the CSS to load
    // before executing
    let value = rootStyle.getPropertyValue('--' + name);
    value = value === '' ? null : value.trim();
    if (value === null && possiblyIE) {
      // can't read custom properties in IE, so they are transformed to have a
      // different name, which we can read by indexing into style
      value = rootStyle['-var--' + name];
      value = value === '' ? null : value.trim();
    }
    varCache.set(name, value);
    return value;
  },

  /**
   * Get the values of a set of sequential root-level custom properties.
   * The properties must be formatted as `--{name}-{index}` where index starts from 1
   * and the rest of the properties must be sequential (no holes).
   *
   * The maximum size of an array is 100 entries.
   *
   * @param {string} name
   * @return {Array<string>}
   */
  getArrayVar(name) {
    if (!rootStyle) {
      return;
    }
    if (arrayVarCache.has(name)) {
      return arrayVarCache.get(name);
    }
    const values = [];
    for (var i = 1; i < 101; i++) {
      const value = this.getVar(name + '-' + i);
      if (value === null) break;
      values.push(value);
    }
    arrayVarCache.set(name, values);
    return values;
  },
};
