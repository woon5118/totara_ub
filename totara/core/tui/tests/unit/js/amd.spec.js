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

import amd from 'totara_core/amd';
import pending from 'totara_core/pending';

jest.mock('totara_core/pending');

global.requirejs = jest.fn((mods, cb) => cb());

describe('amd', () => {
  it('calls requirejs global', async () => {
    await amd('foo');
    expect(global.requirejs).toHaveBeenCalledWith(
      ['foo'],
      expect.any(Function),
      expect.any(Function)
    );
  });

  it('calls pending()', async () => {
    expect(pending.__started('amd:gnidnep')).toBe(0);
    const result = amd('gnidnep');
    expect(pending.__started('amd:gnidnep')).toBe(1);
    await result;
    expect(pending.__completed('amd:gnidnep')).toBe(1);
  });

  it('returns amd module', async () => {
    const mod = {};
    global.requirejs = jest.fn((mods, cb) => {
      cb(mod);
    });
    const result = await amd('foo');
    expect(result).toBe(mod);
  });

  it('caches result', async () => {
    const mod = {};
    global.requirejs = jest.fn((mods, cb) => {
      cb(mod);
    });
    expect(await amd('bar')).toBe(mod);
    expect(await amd('bar')).toBe(mod);
    expect(global.requirejs).toHaveBeenCalledTimes(1);
  });
});
