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

import tui from './tui';
import Container from 'totara_core/components/notifications/ToastContainer';
import { memoizeLoad } from './util';

/**
 * Get the toast container to add notifications to.
 *
 * One will be created if it does not exist.
 *
 * @private
 * @function
 * @returns {Promise<Vue>}
 */
const getContainer = memoizeLoad(async () => {
  const tmpSpan = document.createElement('span');
  document.body.appendChild(tmpSpan);
  const container = await tui.mount(Container, {}, tmpSpan);
  await new Promise(r => setTimeout(r, 0));
  return container;
});

/**
 * Show a notification toast.
 *
 * @param {object} options
 * @param {string} options.message Message to display.
 * @param {(number|null)} [options.duration] Duration to display the message, or null to display until dismissed.
 * @param {('info' | 'success' | 'warning' | 'error')} [options.type] Importance type of the notification.
 */
export async function notify(options) {
  options = Object.assign(
    {
      duration: 5000,
      message: '...',
      type: 'info',
    },
    options
  );

  const container = await getContainer();
  container.addNotification(options);
}
