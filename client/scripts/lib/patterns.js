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
