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

import pending from './pending';

/**
 * Announce a message to screen readers using an aria-live area.
 *
 * @param {object} options
 * @param {string} options.message
 * @param {'polite'|'assertive'} [options.type=polite]
 */
export function announce({ type = 'polite', message }) {
  if (process.env.NODE_ENV === 'development') {
    console.log(`screen reader (${type}): ${message}`);
  }
  getAnnounceRegion(type).then(region => {
    region.textContent = message;
  });
}

const typeRegions = {};

function getAnnounceRegion(type) {
  if (typeRegions[type]) return typeRegions[type];
  const region = document.createElement('div');
  typeRegions[type] = new Promise(r => {
    const done = pending();
    setTimeout(() => {
      done();
      r(region);
    }, 0);
  });
  region.className = 'sr-only';
  region.setAttribute('role', 'status');
  region.setAttribute('aria-live', 'polite');

  document.body.appendChild(region);
  return typeRegions[type];
}
