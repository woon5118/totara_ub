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

const CLIEngine = require('eslint').CLIEngine;
const common = require('./common');
const patterns = require('./patterns');

const ignoreMessage =
  'File ignored because of a matching ignore pattern. Use "--no-ignore" to override.';

async function run(opts) {
  const filePatterns = opts.paths
    ? common.filterByGlobs(opts.paths, patterns.eslint)
    : patterns.eslint;

  const cli = new CLIEngine({
    baseConfig: require('../configs/.eslintrc_tui.js'),
    fix: opts.fix,
    cwd: require('../lib/common').rootDir,
  });
  const formatter = cli.getFormatter();

  // filter to only changed files if requested
  const finalFilePatterns = opts.onlyChanged
    ? common.filterByGlobs(common.listChangedFiles(), filePatterns)
    : filePatterns;
  const report = cli.executeOnFiles(finalFilePatterns);

  // suppress "File ignored" messages, which happen when we pass in files
  // that are ignored by an ignore pattern
  const results = report.results.filter(
    item => !(item.messages[0] && item.messages[0].message === ignoreMessage)
  );

  if (opts && opts.fix) {
    CLIEngine.outputFixes(report);
  }

  const ignoredCount = report.results.length - results.length;
  const output = formatter(results);
  const errorCount = report.errorCount;
  const warningCount = report.warningCount - ignoredCount;

  if (output != '') {
    console.log(output);
  }

  return errorCount === 0 && warningCount === 0;
}

module.exports = {
  run,
};
