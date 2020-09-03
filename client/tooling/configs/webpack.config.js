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
 * @module tui
 */

require('../lib/environment_patch');
const path = require('path');
const fs = require('fs');
const webpack = require('webpack');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const VueLoaderPlugin = require('vue-loader/lib/plugin');
const FixStyleOnlyEntriesPlugin = require('webpack-fix-style-only-entries');
const scanTuiJson = require('../webpack/scan_tui_json');
const { rootDir, arrayUnique } = require('../lib/common');
const tuiExternals = require('../webpack/tui_externals');
const TuiAliasPlugin = require('../webpack/TuiAliasPlugin');
const babelConfigs = require('./babel');
const globSync = require('tiny-glob/sync');
const cssVarExtract = require('./webpack/css_var_extract');

const isCoreBundleChunk = function(x) {
  return x.name === 'tui' || x.name.startsWith('tui.');
};

const separateNameAndSuffix = function(name) {
  let suffix = '';
  let dotIndex = name.indexOf('.');
  if (dotIndex !== -1) {
    suffix = name.substr(dotIndex);
    name = name.substr(0, dotIndex);
  }
  return {
    name: name,
    suffix: suffix,
  };
};

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
  variant,
  mode,
  watch,
  define,
  analyze,
  primary,
  bundleTag,
  tuiComponent,
  tuiJson,
}) {
  // update output filenames based on config options
  const tagSuffix = bundleTag ? '.' + bundleTag : '';
  const modeSuffix = mode == 'production' ? '' : '.' + mode;
  const entry = {
    [tuiComponent + tagSuffix + modeSuffix]: tuiJson,
  };

  const plugins = [
    new VueLoaderPlugin(),
    new MiniCssExtractPlugin({
      moduleFilename: function({ name }) {
        let bits = separateNameAndSuffix(name);
        name = bits.name + '/build/tui_bundle' + bits.suffix + '.scss';
        return name;
      },
    }),
    new webpack.DefinePlugin({
      'process.env.LEGACY_BUNDLE': 'false',
      ...define,
    }),
    // provide source mapping for JS files, equivalent to `devtool: 'eval'`
    mode != 'production' &&
      new webpack.EvalDevToolModulePlugin({
        sourceUrlComment: '\n//# sourceURL=[url]',
        moduleFilenameTemplate:
          'webpack://[namespace]/[resource-path]?[loaders]',
      }),
    // provide source mapping for (S)CSS files
    mode != 'production' &&
      new webpack.SourceMapDevToolPlugin({
        test: /\.(s?css)($|\?)/i,
      }),
    mode == 'production' && new webpack.HashedModuleIdsPlugin(),
    analyze == tuiComponent + '_' + variant &&
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
        babelConfigs[variant] && {
          loader: 'babel-loader',
          options: babelConfigs[variant],
        },
      ].filter(Boolean),
    },
    {
      test: /\.vue$/,
      use: [
        require.resolve('../webpack/tui_vue_loader'),
        {
          loader: 'vue-loader',
          options: {
            productionMode: mode === 'production',
            // having prettify enabled increases build time around 40%
            prettify: false,
          },
        },
      ],
    },
    {
      test: /\.s?css$/,
      use: primary
        ? [
            MiniCssExtractPlugin.loader,
            // css-loader cannot parse SCSS
            {
              loader: require.resolve('../webpack/css_raw_loader'),
              options: { sourceMap: true },
            },
            {
              loader: 'postcss-loader',
              options: {
                parser: 'postcss-scss',
                plugins: [require('autoprefixer')],
                sourceMap: true,
              },
            },
          ]
        : ['null-loader'],
    },
    {
      test: /\.(graphql|gql)$/,
      exclude: /node_modules/,
      loader: require.resolve('../webpack/graphql_loader'),
    },
    {
      test: /icons[/\\]internal[/\\]obj[/\\].*\.svg/,
      type: 'javascript/auto',
      loader: require.resolve('../webpack/icons_svg_loader'),
    },
    {
      resourceQuery: /blockType=lang-strings/,
      loader: require.resolve('../webpack/tui_lang_strings_loader'),
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
        let vendorName = 'tui/build/vendors';
        if (bundleChunk.name !== 'tui') {
          // '' or '.legacy' or '.development' or '.legacy.development'
          const index = bundleChunk.name.indexOf('.');
          if (index === -1) {
            throw new Error('Unexpected chunk name ' + bundleChunk.name);
          }
          vendorName = 'tui/build/vendors' + bundleChunk.name.slice(index);
        }
        return vendorName;
      },
    },
  };

  return {
    name: tuiComponent + '_' + variant,
    entry,
    mode,
    watch,

    // regular output is excessive
    stats: 'minimal',

    // disable automatic sourcemap support as we're adding the plugins manually above
    devtool: false,

    // stop webpack warning for file size
    performance: { hints: false },

    output: {
      path: path.resolve(rootDir, 'client/component'),
      filename: pathData => {
        let bits = separateNameAndSuffix(pathData.chunk.name);
        variant = bits.name + '/build/tui_bundle' + bits.suffix + '.js';
        return variant;
      },
      chunkFilename: '[name].js',
    },

    resolve: {
      extensions: ['.mjs', '.js', '.json', '.vue', '.graphql'],
      plugins: [new TuiAliasPlugin()],
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
 * @return {object}
 */
function modernConfig(opts) {
  return createConfig({ ...opts, variant: 'modern', primary: true });
}

/**
 * Legacy webpack config
 *
 * @param {object} opts
 * @return {object}
 */
function legacyConfig(opts) {
  return createConfig({
    ...opts,
    variant: 'legacy',
    bundleTag: 'legacy',
    define: { ...opts.define, 'process.env.LEGACY_BUNDLE': 'true' },
  });
}

/**
 * Transform SCSS to improved SCSS (by running autoprefixer)
 *
 * @param {object} opts
 * @return {object}
 */
function scssToScssConfig({ mode, watch }, { getTuiDirs }) {
  const tuiDirs = getTuiDirs();

  const scssEntry = tuiDirs.reduce((acc, dir) => {
    if (fs.existsSync(path.join(rootDir, dir, 'global_styles'))) {
      globSync('global_styles/**/*.scss', { cwd: dir }).forEach(x => {
        if (x == 'global_styles/static.scss') {
          // already included in bundle (see tui_json_loader)
          return;
        }
        const out = path.join(
          dir.replace(
            /^\.[/\\]client[/\\]component[/\\]([^/\\]+)[/\\]src/,
            './client/component/$1/build'
          ),
          'global_styles',
          x.slice('global_styles/'.length).replace(/\.scss$/, '')
        );
        const modeSuffix = mode == 'production' ? '' : '.' + mode;
        acc[out + modeSuffix] = './' + path.join(dir, x);
      });
    }
    return acc;
  }, {});

  if (Object.keys(scssEntry).length == 0) {
    return;
  }

  const plugins = [
    // this plugin can be removed once Webpack 5 is released
    new FixStyleOnlyEntriesPlugin({
      silent: true,
    }),
    new MiniCssExtractPlugin({
      filename: '[name].scss',
    }),
    mode != 'production' &&
      new webpack.SourceMapDevToolPlugin({
        test: /\.(s?css)($|\?)/i,
      }),
  ].filter(Boolean);

  const rules = [
    {
      test: /\.scss$/,
      use: [
        MiniCssExtractPlugin.loader,
        // css-loader cannot parse SCSS
        {
          loader: require.resolve('../webpack/css_raw_loader'),
          options: { sourceMap: true },
        },
        {
          loader: 'postcss-loader',
          options: {
            parser: 'postcss-scss',
            plugins: [require('autoprefixer')],
            sourceMap: true,
          },
        },
      ],
    },
  ];

  return {
    name: 'scss-to-scss',
    entry: scssEntry,
    mode,
    watch,
    stats: 'minimal',
    devtool: false,
    output: { path: rootDir },
    module: { rules },
    plugins,
  };
}

module.exports = function(opts) {
  if (!opts.mode) {
    opts = { ...opts, mode: 'production' };
  }

  let configOpts = {
    mode: opts.mode,
    analyze: opts.analyze,
  };

  let entry = scanTuiJson({ rootDir });

  const getTuiDirs = () => {
    const tuiDirs = arrayUnique(
      ...Object.values(entry).map(x => (Array.isArray(x) ? x : [x]))
    ).map(x => path.dirname(x));
    return tuiDirs;
  };

  const configs = [
    scssToScssConfig(configOpts, { getTuiDirs }),
    cssVarExtract(configOpts, { getTuiDirs }),
  ];

  Object.keys(entry).forEach(tuiComponent => {
    if (opts.tuiComponents && !opts.tuiComponents.includes(tuiComponent)) {
      return;
    }
    if (opts.vendor) {
      const tuiJson = JSON.parse(fs.readFileSync(entry[tuiComponent]));
      if (tuiJson.vendor != opts.vendor) {
        return;
      }
    }
    configs.push(
      modernConfig({
        ...configOpts,
        tuiComponent,
        tuiJson: entry[tuiComponent],
      })
    );
    if (opts.legacy !== false) {
      configs.push(
        legacyConfig({
          ...configOpts,
          tuiComponent,
          tuiJson: entry[tuiComponent],
        })
      );
    }
  });
  return configs.filter(Boolean);
};
