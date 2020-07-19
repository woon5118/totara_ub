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

const eslint = require('./eslint');
const stylelint = require('./stylelint');
const prettier = require('./prettier');

async function lintFiles({ changed, fix, paths, formatting }) {
  const eslintSuccess = await eslint.run({
    onlyChanged: changed,
    fix,
    paths,
  });

  const stylelintSuccess = await stylelint.run({
    onlyChanged: changed,
    fix,
    paths,
  });

  const prettierSuccess =
    !formatting ||
    (await prettier.run({
      onlyChanged: changed,
      write: fix,
      paths,
    }));

  return eslintSuccess && stylelintSuccess && prettierSuccess;
}

module.exports = {
  lintFiles,
};
