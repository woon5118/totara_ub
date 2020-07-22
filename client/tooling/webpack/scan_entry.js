/**
 * This file is part of Totara Enterprise Extensions.
 *
 * Copyright (C) 2020 onwards Totara Learning Solutions LTD
 *
 * Totara Enterprise Extensions is provided only to Totara
 * Learning Solutions LTD's customers and partners, pursuant to
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

const path = require('path');
const fs = require('fs');
const globSync = require('tiny-glob/sync');

/**
 * Scan for tui.json and return the entry points for Webpack
 */
module.exports = function scanEntry({ rootDir }) {
  // scan for tui.json files
  // NOTE: for some reason, double asterisk wildcard (**) is required to be able to run on Windows
  const tuiConfigFiles = globSync('client/src/**/tui.json', { cwd: rootDir });
  let entryData = [];

  // parse config file and determine output location
  tuiConfigFiles.forEach(configFile => {
    configFile = configFile.replace(/\\/g, '/');
    const config = JSON.parse(
      fs.readFileSync(path.resolve(rootDir, configFile))
    );
    if (!config || !config.component) {
      throw new Error(
        `${configFile} must contain an object containing a ` +
          `frankenstyle "component" property`
      );
    }

    const outFile = config.component + '/tui_bundle';

    entryData[outFile] = './' + configFile;
  });

  return entryData;
};
