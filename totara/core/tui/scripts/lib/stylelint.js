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

const stylelint = require('stylelint');
const common = require('./common');
const patterns = require('./patterns');

async function run(opts) {
  const filePatterns = opts.paths
    ? common.filterByGlobs(opts.paths, patterns.stylelint)
    : patterns.stylelint;

  // filter to only changed files if requested
  const finalFilePatterns = opts.onlyChanged
    ? common.filterByGlobs(common.listChangedFiles(), filePatterns)
    : filePatterns;

  const data = await stylelint.lint({
    files: finalFilePatterns,
    formatter: 'string',
    fix: opts.fix,
    allowEmptyInput: true,
  });

  if (data.output != '') {
    console.log(data.output);
  }
  return data.output == '';
}

module.exports = {
  run,
};
