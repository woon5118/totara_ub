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
 * @author Kevin Hottinger <kevin.hottinger@totaralearning.com>
 * @package totara_core
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
