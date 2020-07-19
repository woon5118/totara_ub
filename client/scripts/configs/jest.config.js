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
 * @author Kevin Hottinger <kevin.hottinger@totaralearning.com>
 * @module totara_core
 */

const path = require('path');

module.exports = {
  moduleFileExtensions: ['js', 'vue', 'graphql'],
  transform: {
    '^.+\\.vue$': 'vue-jest',
    '^.+\\.graphql': path.resolve(__dirname, '../jest/transform_graphql.js'),
    '.+\\.(css|styl|less|sass|scss|svg|png|jpg|ttf|woff|woff2)$':
      'jest-transform-stub',
    '^.+\\.js$': 'babel-jest',
  },
  transformIgnorePatterns: ['/node_modules/(?!babel-plugin-)'],
  resolver: path.resolve(__dirname, '../jest/resolver'),
  setupFilesAfterEnv: ['jest-extended', 'jest-canvas-mock'],
  snapshotSerializers: ['jest-serializer-vue'],
  testMatch: [
    '**/tui/tests/unit/**/*.spec.(js|jsx|ts|tsx)',
    '**/__tests__/*.(js|jsx|ts|tsx)',
  ],
  watchPlugins: [
    'jest-watch-typeahead/filename',
    'jest-watch-typeahead/testname',
  ],
};
