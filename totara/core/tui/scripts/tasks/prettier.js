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

const prettier = require('../lib/prettier');

const args = require('yargs')
  .usage('Usage: $0 [options] [globs]')
  .help()
  .version(false)
  .boolean('write')
  .describe('write', 'Write formatted file back')
  .boolean('check')
  .describe('check', 'Report files that need formatting')
  .conflicts('write', 'check')
  .describe('changed', 'Only process uncommitted files').argv;

if (!args.write && !args.check) {
  console.log(
    'Error: need one of --write or --check. Pass --help for details.'
  );
  process.exit(1);
}

const paths = args._.length ? args._ : undefined;

prettier
  .run({
    paths,
    write: args.write,
    onlyChanged: args.changed,
  })
  .then(success => {
    if (success) {
      console.log(
        args.write
          ? 'Formatted successfully.'
          : 'Files are correctly formatted.'
      );
    } else {
      console.log(
        args.write ? 'Formatting failed.' : 'Files are not correctly formatted.'
      );
      process.exit(1);
    }
  })
  .catch(err => {
    console.error(err.stack);
    process.exit(1);
  });
