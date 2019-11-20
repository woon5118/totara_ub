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

import ArrayKeyedMap from '../../../../../js/internal/util/ArrayKeyedMap';

describe('ArrayKeyedMap', () => {
  let map;
  beforeEach(() => {
    map = new ArrayKeyedMap();
  });

  it('allows you to get and set items', () => {
    expect(map.get(['a', 'b', 3])).toBe(undefined);
    map.set(['a', 'b', 3], 12);
    map.set(['a', 'b', 3, 4], 13);
    map.set(['a', 'b', 4], 14);
    expect(map.get(['a', 'b', 3])).toBe(12);
    expect(map.get(['a', 'b', 3, 4])).toBe(13);
    expect(map.get(['a', 'b', 4])).toBe(14);
    map.set(['a', 'b', 3], 'w');
    expect(map.get(['a', 'b', 3])).toBe('w');
  });

  it('can check whether an item exists', () => {
    expect(map.has([1])).toBeFalse();
    map.set([1], 2);
    expect(map.has([1])).toBeTrue();
    expect(map.delete([1])).toBeTrue();
    expect(map.delete([9])).toBeFalse();
    expect(map.has([1])).toBeFalse();
  });

  it('compares arrays on a per item basis, using SameValueZero', () => {
    const obj = {};
    map.set(['a', 'b'], 1);
    map.set([obj], 13);
    map.set([], 14);
    map.set([NaN], 15);
    map.set([-0], 16);
    expect(map.get(['a', 'b'])).toBe(1);
    expect(map.get([obj])).toBe(13);
    expect(map.get([])).toBe(14);
    expect(map.get([NaN])).toBe(15);
    expect(map.get([-0])).toBe(16);
  });

  it('only allows arrays as values', () => {
    map.set(['a', 'b', 3], 2);
    expect(() => map.set(null, 1)).toThrow();
    expect(() => map.set(undefined, 1)).toThrow();
    expect(() => map.set({}, 1)).toThrow();
    expect(() => map.set(3, 1)).toThrow();
    expect(() => map.set('hello', 1)).toThrow();
    expect(map.get(['a', 'b', 3])).toBe(2);
    expect(() => map.get('hello')).toThrow();
  });
});
