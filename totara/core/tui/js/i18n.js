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

/* global M */

import amd from './amd';
import pending from './pending';

/**
 * Normalize component name for i18n API.
 *
 * @param {string} component
 * @returns {string}
 */
const normalizeComponent = component => {
  if (!component || component == 'core') {
    return 'moodle';
  } else if (component.startsWith('core_')) {
    return component.slice(5);
  } else if (component.startsWith('mod_')) {
    return component.slice(4);
  }
  return component;
};

/**
 * Get and format a language string.
 *
 * @param {string} key Name of string, e.g. 'cancel'.
 * @param {string} [component] Name of Totara component, e.g. 'core'.
 * @param {*=} [param] Optional variable to populate placeholder with.
 * @return {string}
 */
export function getString(key, component, param) {
  return M.util.get_string(key, component, param);
}

/**
 * Check if the provided language string exists and is loaded.
 *
 * @param {string} key Name of string, e.g. 'cancel'.
 * @param {string} component Name of Totara component, e.g. 'core'.
 * @return {boolean}
 */
export function hasString(key, component) {
  component = normalizeComponent(component);
  return !!(M.str[component] && M.str[component][key]);
}

/**
 * Filter specified language strings to those which are not loaded.
 *
 * @param {array} requests Array of format [{ component: 'foo', key: 'bar' }]
 * @return {array}
 */
export function unloadedStrings(requests) {
  return requests.filter(req => !hasString(req.key, req.component));
}

/**
 * Load all of the specified strings so that they are available on M.str.
 *
 * @param {array} requests Array of format [{ component: 'foo', key: 'bar' }]
 */
export async function loadStrings(requests) {
  const done = pending('i18n-load-strings');
  const str = await amd('core/str');
  await str.get_strings(requests);
  done();
}

let isRtlValue = null;

/**
 * Check if the current language is right-to-left.
 *
 * @returns {boolean}
 */
export function isRtl() {
  if (isRtlValue === null) {
    isRtlValue = document.body.classList.contains('dir-rtl');
  }
  return isRtlValue;
}

/**
 * Convert left-to-right side to the correct side for the current language.
 *
 * 'left' and 'right get swapped for RTL languages, otherwise side is passed
 * through unmodified.
 *
 * @param {string} side 'left', 'right', 'top', or 'bottom'
 * @returns {string}
 */
export function langSide(side) {
  if (!isRtl()) {
    return side;
  }
  switch (side) {
    case 'left':
      return 'right';
    case 'right':
      return 'left';
    default:
      return side;
  }
}

/**
 * Represents a language string that can be loaded.
 */
class LangString {
  constructor(...bits) {
    this.bits = bits;
  }

  loaded() {
    return hasString(...this.bits);
  }

  toRequest() {
    return { component: this.bits[1], key: this.bits[0] };
  }

  toString() {
    return this.loaded()
      ? getString(...this.bits)
      : `[[${this.bits.slice(0, 2)}]]`;
  }
}

/**
 * Create a placeholder for a language string that can be loaded.
 *
 * @param {string} key Name of string, e.g. 'cancel'.
 * @param {string} component Name of Totara component, e.g. 'core'.
 * @param {*=} param Optional variable to populate placeholder with.
 * @returns {LangString}
 */
export function langString(...args) {
  return new LangString(...args);
}

/**
 * Checks if the provided argument is a language string placeholder.
 *
 * @param {*} str
 * @returns {boolean}
 */
export function isLangString(str) {
  return str instanceof LangString;
}

/**
 * Load lang string objects.
 *
 * @param {Array} strings
 * @returns {Promise}
 */
export async function loadLangStrings(strings) {
  return loadStrings(
    strings.filter(x => isLangString(x) && !x.loaded()).map(x => x.toRequest())
  );
}
