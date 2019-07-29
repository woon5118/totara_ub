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

/**
 * Register an asynchronous task as pending.
 *
 * The primary purpose of this is enabling behat tests.
 *
 * Code running in microtasks (e.g. Promises) does not need to be registered as
 * that does not involve an event loop break.
 *
 * The preferred approach should be to push this down to the lowest level
 * possible so most code never needs to call this - for example, an AJAX wrapper
 * should call this so users don't have to.
 *
 * Needed for:
 *   * waiting for the result of a direct fetch() call
 *   * waiting for an event before continuing (load, transitionend, etc)
 *   * setTimeout
 *
 * Not needed for:
 *   * GraphQL requests
 *   * Promise callbacks
 *   * async/await
 *
 * Returns a function that should be called when the code is complete.
 *
 * @param {string=} key Optional identifier for this task (used for debugging, does not have to be unique)
 * @return {function}
 *   Function to call when the code is complete.
 *   Calling this function multiple times has no effect, only the first call
 *   will be registered.
 */
export default function pending(key = 'pending') {
  M.util.js_pending(key);
  let called = false;
  return () => {
    if (called) return;
    called = true;
    M.util.js_complete(key);
  };
}
