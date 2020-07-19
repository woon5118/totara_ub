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

const path = require('path');
const fs = require('fs');
const { rootDir } = require('./common');
const componentMap = require(rootDir + 'component_map.json');

const defaultFolder = 'js';

// vue mapped folders
const vueFolders = ['components', 'containers', 'presentation', 'pages'];

// all mapped folders
const subfolders = [].concat(vueFolders);

const dirMaps = [{ idBaseSuffix: '', path: './' + defaultFolder }].concat(
  subfolders.map(x => ({ idBaseSuffix: '/' + x, path: './' + x }))
);

const subfolderStaticAliases = [[/^graphql\/(.*)$/, 'webapi/ajax/$1']];

const cachedComponentDirs = new Map();
const cachedClientDirs = new Map();

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
  if (component === '') {
    // Yeah nah.
    return null;
  }
  if (componentMap.components[component]) {
    return componentMap.components[component];
  }
  return null;
}

function getClientDir(component) {
  if (cachedClientDirs.has(component)) {
    return cachedClientDirs.get(component);
  }
  const result = getClientDirInternal(component);
  cachedClientDirs.set(component, result);
  return result;
}

function getClientDirInternal(component) {
  if (component === '') {
    // Yeah nah.
    return null;
  }
  if (componentMap.components[component]) {
    return 'client/src/' + component;
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

  // First check if this is a static alias.
  result = resolveStaticAlias(req);
  if (result) return path.resolve(result);

  // Check if this is one of ours.
  const parsedReq = parseComponentRequest(req);
  if (!parsedReq) return;
  const { serverdir, clientdir, rest, restParts } = parsedReq;

  // subfolders get mapped directly under tui/, everything else goes in tui/js/ (defaultFolder = 'js')
  const prefix = subfolders.some(x => restParts[0] == x)
    ? ''
    : defaultFolder + '/';

  let extensions = [''];
  if (!rest.match(/\.[a-z]+$/)) {
    extensions = ['.mjs', '.js', '.json', '.vue', '.graphql', ''];
  }

  let i = 0;
  for (i in extensions) {
    if (!extensions.hasOwnProperty(i)) {
      continue;
    }
    let ext = extensions[i];
    const clientfile = path.resolve(`${clientdir}/${prefix}${rest}${ext}`);
    const serverfile = path.resolve(`${serverdir}/tui/${prefix}${rest}${ext}`);
    if (fs.existsSync(serverfile)) {
      return serverfile;
    }
    if (fs.existsSync(clientfile)) {
      return clientfile;
    }
  }

  return null;
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
  const { serverdir, clientdir, rest } = parsedReq;
  for (const [pattern, replacement] of subfolderStaticAliases) {
    if (serverdir && pattern.test(rest)) {
      return path.join(serverdir, rest.replace(pattern, replacement));
    }
    if (clientdir && pattern.test(rest)) {
      return path.join(clientdir, rest.replace(pattern, replacement));
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
  const serverdir = getComponentDir(component);
  const clientdir = getClientDir(component);

  if (!serverdir && !clientdir) {
    return null;
  }
  return { serverdir, clientdir, rest, restParts };
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
  getClientDir,
  resolveRequest,
  resolveStaticAlias,
  getDirComponent,
};
