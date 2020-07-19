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

const { resolveStaticAlias, resolveRequest } = require('../lib/resolution');

module.exports = function tuiExternals() {
  return (context, request, callback) => {
    // non-relative module request, without any loader options
    if (request[0] != '.' && !request.includes('?') && !request.includes('!')) {
      let nativeResolve = false;

      // Only core can use code from node_modules
      if (
        /[/\\]client[/\\]src[/\\](?:totara_tui|tui|tui_\w+)[/\\]/.test(context) &&
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
