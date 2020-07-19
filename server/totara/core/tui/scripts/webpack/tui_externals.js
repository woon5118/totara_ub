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

const { resolveStaticAlias, resolveRequest } = require('../lib/resolution');

module.exports = function tuiExternals() {
  return (context, request, callback) => {
    // non-relative module request, without any loader options
    if (request[0] != '.' && !request.includes('?') && !request.includes('!')) {
      let nativeResolve = false;

      // only core can use code from node_modules
      if (
        /[/\\]totara[/\\]core[/\\]tui/.test(context) &&
        !resolveRequest(request)
      ) {
        nativeResolve = true;
      }

      // code in node_modules or thirdparty always uses native resolver
      if (/[/\\](?:node_modules|thirdparty)[/\\]/.test(context)) {
        nativeResolve = true;
      }

      if (resolveStaticAlias(request)) {
        nativeResolve = true;
      }

      // if not resolving natively, transform to a tui.require() call
      // this supports our bundle exports plus `exposeNodeModules`
      if (!nativeResolve) {
        return callback(null, `root tui.require(${JSON.stringify(request)})`);
      }
    }

    callback();
  };
};
