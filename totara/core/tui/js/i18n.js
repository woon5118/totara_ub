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

/**
 * Get and format a language string.
 *
 * @param {string} key Name of string, e.g. 'cancel'.
 * @param {string} component Name of Totara component, e.g. 'core'.
 * @param {*=} param Optional variable to populate placeholder with.
 * @return {string}
 */
export function getString(...args) {
  return M.util.get_string(...args);
}

/**
 * Check if the provided language string exists and is loaded.
 *
 * @param {string} key Name of string, e.g. 'cancel'.
 * @param {string} component Name of Totara component, e.g. 'core'.
 * @return {boolean}
 */
export function hasString(key, component) {
  return !!(M.str[component] && M.str[component][key]);
}

/**
 * Filter specified language strings to those which are not loaded.
 *
 * @param {array} requests Array of format [{ component: 'foo', key: 'bar' }]
 * @return {array}
 */
export function unloadedStrings(requests) {
  return requests.filter(
    req => !M.str[req.component] || !M.str[req.component][req.key]
  );
}

/**
 * Load all of the specified strings so that they are available on M.str.
 *
 * @param {array} requests Array of format [{ component: 'foo', key: 'bar' }]
 */
export async function loadStrings(requests) {
  const str = await amd('core/str');
  await str.get_strings(requests);
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
