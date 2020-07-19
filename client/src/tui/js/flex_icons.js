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

import { memoizeLoad } from './util';
import pending from './pending';
import { globalConfig as config } from './config';
import { cacheGet, cacheSet } from './internal/persistent_cache';
import { totaraUrl } from './url';

let hasLoaded = false;
let flexIconData;

/**
 * Load flex icon data.
 *
 * @returns {Promise}
 */
export const load = memoizeLoad(async () => {
  const cacheKey = `core_flex_icon/${config.theme}/cache`;
  const cachedVal = cacheGet(cacheKey);
  if (cachedVal) {
    flexIconData = cachedVal;
    hasLoaded = true;
    return;
  }

  const data = await loadFlexData();
  flexIconData = data;
  hasLoaded = true;
  cacheSet(cacheKey, data);
});

/**
 * Check if flex icons have loaded.
 *
 * @returns {bool}
 */
export function loaded() {
  return hasLoaded;
}

/**
 * Get data matching icon definition in flex_icons.php.
 *
 * `load()` must have been called before using this function.
 *
 * @param {string} identifier
 * @return {object} Object with 'data', 'template', etc keys.
 * @throws {Error} if flex icons have not been loaded.
 */
export function getFlexData(identifier) {
  if (!hasLoaded) {
    throw new Error(
      'Requesting flex data when flex icons have not loaded yet: ' + identifier
    );
  }
  const indexes = flexIconData.icons[identifier];
  if (!indexes) {
    return null;
  }
  return {
    data: Object.assign({}, flexIconData.datas[indexes[1]], { identifier }),
    template: flexIconData.templates[indexes[0]],
  };
}

/**
 * Load flex icon data from service.
 *
 * @returns {Promise<object>}
 */
async function loadFlexData() {
  const done = pending('flex-icon-load');
  let result;
  try {
    // TODO: replace with a GraphQL query
    const response = await fetch(
      totaraUrl('/lib/ajax/service-nologin.php', {
        sesskey: config.sesskey,
        info: 'core_output_get_flex_icons',
      }),
      {
        body: JSON.stringify([
          {
            index: 0,
            methodname: 'core_output_get_flex_icons',
            args: { themename: config.theme },
          },
        ]),
        method: 'POST',
        credentials: 'same-origin',
      }
    );
    result = await response.json();
    result = result[0];
  } finally {
    done();
  }
  if (!result || result.error) {
    throw new Error('Error loading flex data');
  }
  return result.data;
}
