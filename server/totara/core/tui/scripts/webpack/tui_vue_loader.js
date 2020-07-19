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

const { parseQuery } = require('loader-utils');

const { escapeRegExp } = require('../lib/common');
const { vueFolders } = require('../lib/resolution');

const themeOverrideRegex = new RegExp(
  'theme\\/[^/]+\\/tui\\/(' +
    vueFolders.map(x => escapeRegExp(x)).join('|') +
    ')\\/overrides\\/(.*).vue$'
);

/**
 * TUI-specific Vue transforms
 *
 * - Add `__hasBlocks` property to component options
 * - Add `tui.processOverride()` calls to components in `(vue folder)/overrides` folders
 *
 * @param {string} source
 * @param {?SourceMap} map
 */
module.exports = function(source, map) {
  const params =
    this.resourceQuery && this.resourceQuery[0] == '?'
      ? parseQuery(this.resourceQuery)
      : {};

  // if the query has a type field, this is a language block request
  if (!params.type) {
    const hasBlocks = {
      script: /import script from/.test(source),
      template: /import { render, staticRenderFns } from/.test(source),
    };

    let code =
      `\ncomponent.options.__hasBlocks = ` + `${JSON.stringify(hasBlocks)};`;

    // process theme overrides
    const themeOverrideMatch = themeOverrideRegex.exec(
      this.resourcePath.replace(/\\/g, '/')
    );
    // skip if we're not loading an override component or if the file is not in a subfolder
    if (themeOverrideMatch && themeOverrideMatch[2].indexOf('/') !== -1) {
      const folder = themeOverrideMatch[1];
      let parentName = themeOverrideMatch[2];
      const sepIndex = parentName.indexOf('/');
      parentName =
        parentName.substring(0, sepIndex) +
        '/' +
        folder +
        parentName.substring(sepIndex);

      code +=
        '\ntui._processOverride(component.exports, ' +
        JSON.stringify(parentName) +
        ');';
    }

    let pos = source.indexOf('\n/* hot reload */');
    if (pos === -1) {
      pos = source.lastIndexOf('\nexport default');
    }

    if (pos !== -1) {
      source = source.substring(0, pos) + code + source.substring(pos);
    }
  }
  this.callback(null, source, map);
};
