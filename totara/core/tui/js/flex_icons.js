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

import amd from './amd';
import { memoizeLoad } from './util';

let hasLoaded = false;
let flexIcon;

/**
 * Load flex icon data.
 *
 * @returns {Promise}
 */
export const load = memoizeLoad(async () => {
  flexIcon = await amd('core/flex_icon');
  await flexIcon.load();
  hasLoaded = true;
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
