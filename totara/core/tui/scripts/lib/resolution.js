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
const componentMap = require('../generated/component_map.json');

const defaultFolder = 'js';

// vue mapped folders
const vueFolders = ['containers', 'presentation', 'pages'];

// all mapped folders
const subfolders = [].concat(vueFolders);

const dirMaps = [{ idBaseSuffix: '', path: './' + defaultFolder }].concat(
  subfolders.map(x => ({ idBaseSuffix: '/' + x, path: './' + x }))
);

const subfolderStaticAliases = [[/^graphql\/(.*)$/, 'webapi/ajax/$1']];

const cachedComponentDirs = new Map();

/**
 * Get the directory a totara component lives in, relative to the root directory.
 *
 * e.g.
 *   - core -> lib
 *   - totara_core -> totara/core
 *   - theme_foo -> theme/foo
 *
 * @param {string} component
 * @returns {string}
 */
function getComponentDir(component) {
  if (cachedComponentDirs.has(component)) {
    return cachedComponentDirs.get(component);
  }
  const result = getComponentDirInternal(component);
  cachedComponentDirs.set(component, result);
  return result;
}

/**
 * Internal implementation of getComponentDir()
 *
 * @param {string} component
 * @returns {string}
 */
function getComponentDirInternal(component) {
  if (component == 'core') {
    return 'lib';
  }
  const parts = component.split('_');
  const type = parts[0];
  const plugin = parts.slice(1).join('_');
  if (!type || !plugin) {
    return null;
  }
  if (type == 'core' && componentMap.subsystems[plugin]) {
    return componentMap.subsystems[plugin];
  }
  if (componentMap.plugintypes[type]) {
    return componentMap.plugintypes[type] + '/' + plugin;
  }
  return null;
}

/**
 * Resolve an import request to a file.
 *
 * @param {string} req
 * @returns {?string}
 *   Path to file to include (may be missing file extension), or null if not a TUI-resolved request.
 *   Relative to root dir.
 */
function resolveRequest(req) {
  let result;

  if (req[0] == '.') return;

  result = resolveStaticAlias(req);
  if (result) return result;

  const parsedReq = parseComponentRequest(req);
  if (!parsedReq) return;
  const { dir, rest, restParts } = parsedReq;

  // subfolders get mapped directly under tui/, everything else goes in tui/js/ (defaultFolder = 'js')
  const prefix = subfolders.some(x => restParts[0] == x)
    ? ''
    : defaultFolder + '/';

  return `${dir}/tui/${prefix}${rest}`;
}

/**
 * Resolve an import request to a filename, if it has a static alias.
 *
 * Imports with static aliases are resolved at build time rather than at runtime.
 *
 * @param {string} req
 * @returns {?string}
 *   Path to file to include, or null if no static alias was found.
 *   Relative to root dir.
 */
function resolveStaticAlias(req) {
  if (req[0] == '.') return;
  const parsedReq = parseComponentRequest(req);
  if (!parsedReq) return;
  const { dir, rest } = parsedReq;
  for (const [pattern, replacement] of subfolderStaticAliases) {
    if (dir && pattern.test(rest)) {
      return path.join(dir, rest.replace(pattern, replacement));
    }
  }
}

/**
 * Parse a request for a file inside a component and return parts
 *
 * e.g.
 *   - totara_core/foo/bar -> { dir: 'totara/core', rest: 'foo/bar', restParts: ['foo', 'bar'] }
 *
 * @param {string} req
 * @returns {object}
 */
function parseComponentRequest(req) {
  const [component, ...restParts] = req.split('/');
  if (!restParts) return null;
  const rest = restParts.join('/');
  const dir = getComponentDir(component);
  if (!dir) return null;
  return { dir, rest, restParts };
}

const cachedDirComponents = new Map();

/**
 * Get the Totara component the specified directory belongs to.
 *
 * Only works if one of the directory or one of its parent directories is named
 * 'tui' and contains 'tui.json'.
 *
 * @param {string} dir Directory
 * @returns {?string}
 */
function getDirComponent(dir) {
  if (cachedDirComponents.has(dir)) {
    return cachedDirComponents.get(dir);
  }

  if (path.basename(dir) == 'tui') {
    const file = path.join(dir, 'tui.json');
    if (fs.existsSync(file)) {
      const info = JSON.parse(fs.readFileSync(file, 'utf8'));
      cachedDirComponents.set(dir, info.component);
      return info.component;
    }
  }

  const parentDir = path.dirname(dir);
  if (parentDir == dir) {
    // at root dir
    cachedDirComponents.set(dir, null);
    return null;
  }

  const parentResult = getDirComponent(parentDir);
  cachedDirComponents.set(dir, parentResult);
  return parentResult;
}

module.exports = {
  subfolders,
  defaultFolder,
  dirMaps,
  vueFolders,
  getComponentDir,
  resolveRequest,
  resolveStaticAlias,
  getDirComponent,
};
