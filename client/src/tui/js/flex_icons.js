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

import amd from './amd';
import { memoizeLoad } from './util';
import pending from './pending';

let hasLoaded = false;
let flexIcon;

/**
 * Load flex icon data.
 *
 * @returns {Promise}
 */
export const load = memoizeLoad(async () => {
  const done = pending('flex-icon-load');
  flexIcon = await amd('core/flex_icon');
  await flexIcon.load();
  hasLoaded = true;
  done();
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
  return flexIcon.getFlexTemplateDataSync(identifier);
}
