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

import {
  getString,
  hasString,
  unloadedStrings,
  loadStrings,
} from 'totara_core/i18n';
import amd from 'totara_core/amd';

jest.mock('totara_core/amd');

const M = (global.M = {
  util: {
    get_string: jest.fn((...args) => args.join(',')),
  },
  str: {
    foo: {
      bar: 'baz',
    },
  },
});

const coreStr = {
  get_strings: jest.fn(x => x),
};

amd.__setMock('core/str', coreStr);

describe('getString', () => {
  it('wraps M.util.get_string', () => {
    expect(getString('a', 'b', 'c')).toBe('a,b,c');
    expect(M.util.get_string).toHaveBeenCalledWith('a', 'b', 'c');
    expect(getString('d', 'e')).toBe('d,e,');
    expect(M.util.get_string).toHaveBeenCalledWith('d', 'e', undefined);
    expect(getString('f')).toBe('f,,');
    expect(M.util.get_string).toHaveBeenCalledWith('f', undefined, undefined);
  });
});

describe('hasString', () => {
  it('wraps checks for presence in M.str', () => {
    expect(hasString('bar', 'foo')).toBe(true);
    expect(hasString('bar', 'a')).toBe(false);
    expect(hasString('b', 'foo')).toBe(false);
  });
});

describe('unloadedStrings', () => {
  it('filters out strings which are present in M.str', () => {
    expect(
      unloadedStrings([
        {
          component: 'foo',
          key: 'bar',
        },
        {
          component: 'a',
          key: 'b',
        },
      ])
    ).toEqual([
      {
        component: 'a',
        key: 'b',
      },
    ]);
  });
});

describe('loadStrings', () => {
  it("calls amd('core/str').get_strings with the provided strings", async () => {
    const requests = [{ component: 'a', key: 'b' }];
    expect(await loadStrings(requests)).toBe(undefined);
    expect(coreStr.get_strings).toHaveBeenCalledWith(requests);
  });
});
