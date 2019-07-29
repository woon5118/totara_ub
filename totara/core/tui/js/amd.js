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

import pending from './pending';

// maintain our own cache so we can return results with Promise.resolve() (which will queue a microtask)
// rather than relying on requirejs, which won't call the callback until at least 4 ms later even if the
// result is immediately available:
// https://github.com/requirejs/requirejs/blob/fdf4186d3e68df06a04bd71cb6ea0f24eb1600d1/require.js#L1449
// https://github.com/requirejs/requirejs/blob/fdf4186d3e68df06a04bd71cb6ea0f24eb1600d1/require.js#L1814
const amdCache = {};

/**
 * Load an AMD module
 *
 * @param {string} name
 * @return {Promise}
 */
export default function amd(name) {
  if (amdCache[name]) {
    return Promise.resolve(amdCache[name]);
  }

  const done = pending('amd:' + name);

  return new Promise((resolve, reject) => {
    /* global requirejs */
    requirejs(
      [name],
      result => {
        done();
        amdCache[name] = result;
        resolve(result);
      },
      err => {
        done();
        reject(err);
      }
    );
  });
}
