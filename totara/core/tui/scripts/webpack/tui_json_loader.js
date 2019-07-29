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

const path = require('path');
const fs = require('fs');
const { stringifyRequest, getOptions } = require('loader-utils');
const { dirMaps } = require('../lib/resolution');

// exclude __mocks__ dir and /internal/ dir
const bundleExclusionsRegex = /__[a-z]*__|[/\\]internal[/\\]/;
const bundleInclusionsRegex = new RegExp(
  '^(?:(?!' + bundleExclusionsRegex.source + ').)*$'
);

/**
 * Process TUI entrypoint (tui.json)
 *
 * Generates code to implement TUI bundle for core/theme/etc.
 *
 * - Automatically detect defined folders (see lib/resolution.js) next to
 *   tui.json and add their contents to the module store
 * - Execute any entrypoints specified in tui.json (entry and preEntry)
 */
module.exports = function(jsonSource, map) {
  const self = this;

  const options = getOptions(this);

  const config = JSON.parse(jsonSource);
  if (!config || !config.component) {
    throw new Error(
      'tui.json must be an object containing a frankenstyle "component" property'
    );
  }

  if (!config.vendor && !options.silent) {
    console.warn(
      `[tui.json loader] Configuration error in tui.json for ` +
        `${config.component}: "vendor" key is missing. Vendor should be set ` +
        `to a unique string for each organisation producing Totara components.`
    );
  }

  const compStr = JSON.stringify(config.component);

  let source = '!function() {\n"use strict";\n\n';

  // bail out if this bundle is already loaded
  source += `if (typeof tui !== 'undefined' && tui._bundle.isLoaded(${compStr})) {
  console.warn(
    '[tui bundle] The bundle "' + ${compStr} +
    '" is already loaded, skipping initialisation.'
  );
  return;
};
`;

  // execute pre-entry code
  // this is used by totara_core to set up the module store etc before we add to it below
  if (config.preEntry) {
    source += 'require(' + stringifyRequest(this, config.preEntry) + ');\n';
  }

  // register bundle
  source += `tui._bundle.register(${compStr})\n`;

  // auto import certain folders
  dirMaps.forEach(function(dirMap) {
    if (fs.existsSync(path.resolve(self.context, dirMap.path))) {
      source +=
        'tui._bundle.addModulesFromContext(' +
        JSON.stringify(config.component + dirMap.idBaseSuffix) +
        ', require.context(' +
        JSON.stringify(dirMap.path) +
        ', true, /' +
        bundleInclusionsRegex.source +
        '/));\n';
    }
  });

  // expose specified modules from node_modules in the module store
  source += genExposeCode(config);

  // import specified entry point (this can be used for e.g. any code you want to run on page load)
  if (config.entry) {
    // use require here as import is hoisted
    source += 'require(' + stringifyRequest(this, config.entry) + ');\n';
  }

  source += '}();';

  return this.callback(null, source, map);
};

/**
 * Generate code for exposing the specified node modules in the module store.
 *
 * @param {object} config Parsed tui.json
 * @returns {string}
 */
function genExposeCode(config, options) {
  let code = '';

  if (!config.exposeNodeModules) {
    return code;
  }

  if (config.component != 'totara_core') {
    if (!options.silent) {
      console.warn(
        `[tui.json loader] Configuration error in tui.json for ` +
          `${config.component}: exposeNodeModules is only supported in ` +
          `totara_core.`
      );
    }
    return code;
  }

  config.exposeNodeModules.forEach(function(item) {
    code +=
      `tui._bundle.addModule(${JSON.stringify(item)}, function() { ` +
      `return require(${JSON.stringify(item)}); });\n`;
  });

  return code;
}
