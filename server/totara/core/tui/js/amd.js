/**
 * This file is part of Totara Enterprise Extensions.
 *
 * Copyright (C) 2020 onwards Totara Learning Solutions LTD
 *
 * Totara Enterprise Extensions is provided only to Totara
 * Learning Solutions LTD’s customers and partners, pursuant to
 * the terms and conditions of a separate agreement with Totara
 * Learning Solutions LTD or its affiliate.
 *
 * If you do not have an agreement with Totara Learning Solutions
 * LTD, you may not access, use, modify, or distribute this software.
 * Please contact [licensing@totaralearning.com] for more information.
 *
 * @author Simon Chester <simon.chester@totaralearning.com>
 * @module totara_core
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
