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

// glob patterns used for linters/scripts
const patterns = {
  eslint: ['**/tui/**/*.{js,vue}'],
  stylelint: ['**/tui/**/*.{css,scss,vue}'],
  prettier: [
    '**/tui/**/*.{js,css,scss,vue}',
    '**/webapi/*.graphqls',
    '**/webapi/**/*.graphql',
  ],
  // single pattern that will match all of the files in the above patterns, even
  // if some of them will get filtered out later
  allGreedyPattern: '**/*.{js,css,scss,vue,graphql,graphqls}',
};

module.exports = patterns;
