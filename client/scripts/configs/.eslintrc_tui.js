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

module.exports = {
  root: true,
  env: {
    browser: true,
    commonjs: true,
    node: true,
    // some ES6 globals are emulated in IE11 via polyfill - see lib/javascript_polyfill/
    es6: true,
  },
  plugins: ['tui'],
  extends: [
    'eslint:recommended',
    'plugin:jest/recommended',
    'plugin:vue/recommended',
    // disable rules that would conflict with prettier
    'prettier',
    'prettier/vue',
  ],
  globals: {
    // tui global interface
    tui: true,
  },
  rules: {
    // we use console for error reporting
    'no-console': 'off',
    'vue/no-v-html': 'off',
    'vue/require-default-prop': 'off',
    'vue/html-self-closing': ['warn', { html: { void: 'any' } }],
    // generators compile to large (regenerator-runtime) and slow code with
    // babel for IE 11, so disallow them
    'tui/no-generators': 'error',
    'tui/no-export-vue-extend': 'error',
    // Edge does not support object spread
    'tui/no-object-spread': 'error',
    'tui/no-tui-internal': 'error',
    'tui/no-for-of': 'error',
  },
};
