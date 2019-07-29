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

import { makeScanner, getInheritedOption } from 'totara_core/vue_requirements';
import Vue from 'vue';

describe('makeScanner', () => {
  it('calls options.extract and mergeSubresult to get values from component and subcomponents', () => {
    const scan = makeScanner({
      extract(comp) {
        return comp.foo;
      },
      mergeSubresult: (a, b) => a + b,
    });

    expect(scan({ foo: 1 })).toBe(1);
    expect(scan({ foo: 1, extends: { foo: 2 } })).toBe(3);
    expect(scan({ foo: 1, components: { a: { foo: 2 }, b: { foo: 4 } } })).toBe(
      7
    );
    expect(
      scan({
        foo: 1,
        extends: { foo: 2 },
        components: { a: { foo: 4 }, c: null },
      })
    ).toBe(7);

    expect(scan(Vue.extend({ foo: 1 }))).toBe(1);
  });

  it('calls postprocess to process extracted value', () => {
    const postprocess = jest.fn(x => x + 10);
    const scan = makeScanner({
      extract(comp) {
        return comp.foo;
      },
      mergeSubresult: (a, b) => a + b,
      postprocess,
    });
    expect(scan({ foo: 1, extends: { foo: 2 } })).toBe(23);
    expect(postprocess).toHaveBeenNthCalledWith(1, 2);
    expect(postprocess).toHaveBeenNthCalledWith(2, 13);
  });

  it('memoizes calls with cache: true', () => {
    const extract = jest.fn(comp => comp.foo);
    const scan = makeScanner({
      extract,
      mergeSubresult: (a, b) => a + b,
      cache: true,
    });

    const comp1 = { foo: 1 };
    const comp2 = { foo: 2, components: { comp1 } };

    scan(comp1);
    scan(comp1);
    expect(extract).toHaveBeenCalledTimes(1);
    scan(comp2);
    scan(comp2);
    expect(extract).toHaveBeenCalledTimes(2);
  });
});

describe('getInheritedOption', () => {
  it('just returns the component value when not extending', () => {
    expect(getInheritedOption({ foo: 1 }, 'foo')).toBe(1);
    expect(getInheritedOption({}, 'foo')).toBe(undefined);
  });

  it('returns the component value when extending if specified otherwise the parent value', () => {
    expect(
      getInheritedOption(
        {
          foo: 1,
          extends: {
            foo: 2,
          },
        },
        'foo'
      )
    ).toBe(1);
    expect(
      getInheritedOption(
        {
          extends: {
            foo: 2,
          },
        },
        'foo'
      )
    ).toBe(2);
  });

  it('allows specifying a custom merge strategy', () => {
    expect(
      getInheritedOption(
        {
          foo: 1,
          extends: {
            foo: 2,
          },
        },
        'foo',
        (parent, current) => `${parent}-${current}`
      )
    ).toBe('2-1');
    expect(
      getInheritedOption(
        {
          extends: {
            foo: 2,
          },
        },
        'foo',
        (parent, current) => `${parent}-${current}`
      )
    ).toBe('2-undefined');
  });
});
