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

const lint = require('../lib/lint');

const args = require('yargs')
  .usage('Usage: $0 [options] [globs]')
  .help()
  .version(false)
  .boolean('fix')
  .describe('fix', 'Automatically fix problems and format files')
  .boolean('formatting')
  .describe('formatting', 'Additionally check if source files need formatting')
  .boolean('changed')
  .describe('changed', 'Only process uncommitted files').argv;

const { fix, formatting, changed } = args;
const paths = args._.length ? args._ : undefined;

lint
  .lintFiles({ paths, fix, formatting, changed })
  .then(success => {
    if (success) {
      console.log('Lint passed.');
    } else {
      console.log('Lint failed.');
      process.exit(1);
    }
  })
  .catch(err => {
    console.error(err.stack);
    process.exit(1);
  });
