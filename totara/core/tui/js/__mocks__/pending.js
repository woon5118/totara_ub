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

const started = {};
const completed = {};

export default function pending(key) {
  if (!started[key]) started[key] = 0;
  started[key]++;
  let called = false;
  return () => {
    if (called) return;
    called = true;
    if (!completed[key]) completed[key] = 0;
    completed[key]++;
  };
}

const getVal = (obj, key) => obj[key] || 0;
const getTotal = obj => Object.values(obj).reduce((acc, cur) => acc + cur, 0);

pending.__started = key => (key ? getVal(started, key) : getTotal(started));
pending.__completed = key =>
  key ? getVal(completed, key) : getTotal(completed);
pending.__outstanding = key =>
  pending.__started(key) - pending.__completed(key);
