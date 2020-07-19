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

/**
 * Like css-loader, but doesn't do any processing of the CSS.
 *
 * This is needed as css-loader doesn't like being passed SCSS, and we need
 * css-loader or a loader with similar output to provide to
 * mini-css-extract-plugin.
 */

const {
  getOptions,
  getRemainingRequest,
  getCurrentRequest,
} = require('loader-utils');
const { SourceListMap, SourceNode } = require('source-list-map');

const matchLength = (regex, str) => {
  const newlinesMatch = regex.exec(str);
  return newlinesMatch ? newlinesMatch[0].length : 0;
};

module.exports = function(content) {
  const options = getOptions(this) || {};

  // normalize line endings
  content = content.replace(/\r\n/g, '\n');

  // generate line-based sourcemap
  let map;
  if (options.sourceMap) {
    // count \n
    const leadingNewlines = matchLength(/^\n+/, content);
    const trailingNewlines = matchLength(/\n+$/, content);

    const cssRequest = getRemainingRequest(this);
    const request = getCurrentRequest(this);

    let sourceMap = new SourceListMap();
    sourceMap.add(
      new SourceNode(
        content.substring(leadingNewlines, content.length - trailingNewlines) +
          '\n',
        cssRequest,
        content,
        // lines start at 1
        1 + leadingNewlines
      )
    );
    const res = sourceMap.toStringWithSourceMap({ file: request });
    content = res.source;
    map = JSON.stringify(res.map);
  } else {
    content = content.trim() + '\n';
    map = null;
  }

  this.callback(
    null,
    `module.exports = [[module.id, ${JSON.stringify(
      content
    )}, "", ${JSON.stringify(map)}]];`
  );
};
