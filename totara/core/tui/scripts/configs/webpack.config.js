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
const webpack = require('webpack');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const VueLoaderPlugin = require('vue-loader/lib/plugin');
const scanEntry = require('../webpack/scan_entry');
const { rootDir } = require('../lib/common');
const tuiExternals = require('../webpack/tui_externals');
const TuiAliasPlugin = require('../webpack/TuiAliasPlugin');
const babelConfigs = require('./babel');

const coreBundle = 'totara/core/tui/build/tui_bundle';
const isCoreBundleChunk = x =>
  x.name.replace(/\\/g, '/').startsWith(coreBundle);

/**
 * Create a webpack config with the specified options.
 *
 * @param {object} opts
 * @param {string} opts.name
 *     Name used to identify this config, e.g. 'modern' or 'legacy'.
 * @param {string} opts.mode 'development'/'production'
 * @param {boolean} opts.watch Watch for changes and rebuild?
 * @param {object} opts.define Definitions to pass to DefinePlugin.
 * @param {string} opts.analyze Load analyze plugin if name matches this string.
 * @param {string} opts.primary
 *     Is this the primary config? Some functionality will be disabled in
 *     non-primary configs to avoid performing the same work twice.
 * @param {boolean} opts.bundleTag
 *     If present, appended to the name of output files with a dot.
 */
function createConfig({
  name,
  mode,
  watch,
  define,
  analyze,
  primary,
  bundleTag,
}) {
  let entry = scanEntry({ rootDir });

  // update output filenames base on config options
  entry = Object.entries(entry).reduce((acc, [key, val]) => {
    const tagSuffix = bundleTag ? '.' + bundleTag : '';
    const modeSuffix = mode == 'production' ? '' : '.' + mode;
    acc[key + tagSuffix + modeSuffix] = val;
    return acc;
  }, {});

  const plugins = [
    new VueLoaderPlugin(),
    new MiniCssExtractPlugin(),
    new webpack.DefinePlugin({
      'process.env.NODE_ENV': webpack.DefinePlugin.runtimeValue(
        ({ module }) => {
          // 'graphql' package needs NODE_ENV to be 'production' when minified
          if (/node_modules\/graphql\//.test(module.resource)) {
            return JSON.stringify(mode);
          }
          return 'M.cfg.NODE_ENV';
        }
      ),
      'process.env.LEGACY_BUNDLE': 'false',
      ...define,
    }),
    mode == 'production' && new webpack.HashedModuleIdsPlugin(),
    analyze == name &&
      new (require('webpack-bundle-analyzer').BundleAnalyzerPlugin)(),
  ].filter(Boolean);

  const rules = [
    {
      test: /[/\\]tui\.json$/,
      type: 'javascript/auto',
      use: {
        loader: require.resolve('../webpack/tui_json_loader'),
        options: { silent: false },
      },
    },
    {
      test: /\.js$/,
      exclude: /node_modules/,
      use: [
        babelConfigs[name] && {
          loader: 'babel-loader',
          options: babelConfigs[name],
        },
      ].filter(Boolean),
    },
    {
      test: /\.vue$/,
      use: [require.resolve('../webpack/tui_vue_loader'), 'vue-loader'],
    },
    {
      test: /\.css$/,
      use: primary
        ? [MiniCssExtractPlugin.loader, 'css-loader']
        : ['null-loader'],
    },
    {
      test: /\.(graphql|gql)$/,
      exclude: /node_modules/,
      loader: require.resolve('../webpack/graphql_loader'),
    },
    primary && {
      enforce: 'pre',
      test: /\.(js|vue)$/,
      loader: 'eslint-loader',
      exclude: /[/\\](?:node_modules|thirdparty)[/\\]/,
      options: {
        configFile: path.join(__dirname, './.eslintrc_tui.js'),
      },
    },
  ].filter(Boolean);

  // code splitting configuration
  const cacheGroups = {
    vendors: {
      test(module, chunks) {
        // only split code used by the core bundle
        if (!chunks.some(isCoreBundleChunk)) {
          return false;
        }
        if (
          module.nameForCondition &&
          /[\\/]node_modules[\\/]/.test(module.nameForCondition())
        ) {
          return true;
        }
        return false;
      },
      name(module, chunks) {
        const bundleChunk = chunks.find(isCoreBundleChunk);
        if (!bundleChunk) {
          return false;
        }
        const index = bundleChunk.name.indexOf('tui_bundle');
        if (index === -1) throw new Error('Unexpected chunk name');
        // '' or '.legacy'
        const bundleSuffix = bundleChunk.name.slice(
          index + 'tui_bundle'.length
        );
        return 'totara/core/tui/build/vendors' + bundleSuffix;
      },
    },
  };

  return {
    name,
    entry,
    mode,
    watch,

    // regular output is excessive
    stats: 'minimal',

    // stop webpack warning for file size
    performance: { hints: false },

    output: {
      path: rootDir,
    },

    resolve: {
      extensions: ['.mjs', '.js', '.json', '.vue', '.graphql'],
      plugins: [new TuiAliasPlugin()],
      // only used for JetBrains IDE support at the moment
      alias: require('../generated/webpack_aliases'),
    },

    // used to implement importing frankenstyle paths
    externals: [tuiExternals()],

    module: {
      rules,
    },

    plugins,

    optimization: {
      splitChunks: {
        chunks: 'all',
        cacheGroups,
      },
    },
  };
}

/**
 * Modern webpack config
 *
 * @param {object} opts
 */
function modernConfig(opts) {
  return createConfig({ ...opts, name: 'modern', primary: true });
}

/**
 * Legacy webpack config
 *
 * @param {object} opts
 */
function legacyConfig(opts) {
  return createConfig({
    ...opts,
    name: 'legacy',
    bundleTag: 'legacy',
    define: { ...opts.define, 'process.env.LEGACY_BUNDLE': 'true' },
  });
}

module.exports = function(opts) {
  if (!opts.mode) {
    opts = { ...opts, mode: 'production' };
  }
  return [modernConfig(opts), legacyConfig(opts)];
};
