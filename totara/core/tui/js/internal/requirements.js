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

import { unloadedStrings, loadStrings } from '../i18n';
import * as flexIcons from '../flex_icons';
import { collectStrings } from '../i18n_vue_plugin';
import tui from '../tui';

let loadedOneOffs = false;

/**
 * Component requirement loading
 *
 * @private
 */
export default {
  /**
   * Get requirements from component
   *
   * @private
   * @param {object} component Component definition
   * @returns {object}
   *     Requirements object.
   *     `.any` property can be checked to determine if there are any.
   */
  get(component) {
    const compUnloadedStrings = unloadedStrings(collectStrings(component));
    return {
      unloadedStrings: compUnloadedStrings,
      oneOffs: !loadedOneOffs,
      any: compUnloadedStrings.length > 0 || !loadedOneOffs,
    };
  },

  /**
   * Load the specified requirements
   *
   * @private
   * @param {object} reqs
   * @returns {Promise}
   *     Promise resolving when loading has finished
   *     (whether successful or not)
   */
  load(reqs) {
    const promises = [];
    if (reqs.unloadedStrings) {
      promises.push(wrapReqTry(() => loadStrings(reqs.unloadedStrings)));
    }
    if (reqs.oneOffs) {
      const ErrorBoundary = tui.defaultExport(
        tui.require('totara_core/components/errors/ErrorBoundary')
      );
      const ErrorPageRender = tui.defaultExport(
        tui.require('totara_core/components/errors/ErrorPageRender')
      );
      promises.push(
        Promise.all([
          // load icons
          wrapReqTry(() => flexIcons.load()),
          // load error strings
          wrapReqTry(() =>
            loadStrings(
              collectStrings({
                components: {
                  ErrorBoundary,
                  ErrorPageRender,
                },
              })
            )
          ),
        ]).then(() => {
          loadedOneOffs = true;
        })
      );
    }
    if (promises.length) {
      return Promise.all(promises);
    }
    return Promise.resolve();
  },
};

function logLoadError(e) {
  console.error('Error while loading requirements:', e);
}

// error handling:
// if a requirement fails, try and load it a second time, but if it still fails keep rendering anyway.
function wrapReqTry(fn) {
  return fn().catch(() =>
    // try again one more time
    fn().catch(logLoadError)
  );
}
