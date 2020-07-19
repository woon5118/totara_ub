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
const globSync = require('tiny-glob/sync');

/**
 * Scan for tui.json and return the entry points for Webpack
 */
module.exports = function scanEntry({ rootDir }) {
  // scan for tui.json files
  const tuiConfigFiles = globSync('**/tui/tui.json', { cwd: rootDir });
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

    const tuiDir = path.dirname(configFile).replace(/\\/g, '/');
    const outFile = path
      .relative(rootDir, path.resolve(tuiDir, './build/tui_bundle'))
      .replace(/\\/g, '/');

    entryData[outFile] = './' + configFile;
  });

  return entryData;
};
