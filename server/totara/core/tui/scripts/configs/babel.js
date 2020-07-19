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
  legacy: {
    presets: ['@babel/preset-env'],
    plugins: [
      require('../babel/disallow-syntax'),
      [
        'transform-async-to-promises',
        {
          inlineHelpers: true,
        },
      ],
    ],
  },

  test: {
    plugins: ['@babel/plugin-transform-modules-commonjs'],
  },
};
