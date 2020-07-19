/*
 * This file is part of Totara Learn
 *
 * Copyright (C) 2020 onwards Totara Learning Solutions LTD
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

import { mount } from '@vue/test-utils';
import FieldArray from 'totara_core/components/reform/FieldArray';
import { ReformScopeProvider } from './util';

function create({ scope, path }) {
  const result = {};
  const outerWrapper = mount(ReformScopeProvider, {
    propsData: { scope },
    stubs: {
      Scope: true,
    },
    scopedSlots: {
      default() {
        const h = this.$createElement;
        return h(FieldArray, {
          props: { path },
          scopedSlots: {
            default(props) {
              result.props = props;
              return h('div');
            },
          },
        });
      },
    },
  });

  result.wrapper = outerWrapper.find(FieldArray);

  return result;
}

describe('FieldArray', () => {
  it('updates state using provided functions', () => {
    const slices = {};
    const scope = {
      getValue: jest.fn(() => []),
      updateRegistration: () => {},
      $_internalUpdateSliceState: jest.fn((path, fn) => {
        slices[path] = fn(slices[path] || {});
      }),
    };
    const opt = create({ scope, path: 'foo' });

    opt.props.push('foo');
    expect(slices.foo).toEqual({ values: ['foo'], touched: [null] });

    opt.props.push('bar');
    expect(slices.foo).toEqual({
      values: ['foo', 'bar'],
      touched: [null, null],
    });

    slices.foo.touched[1] = true;
    opt.props.shift();
    expect(slices.foo).toEqual({ values: ['bar'], touched: [true] });

    opt.props.push('baz');
    opt.props.pop();
    expect(slices.foo).toEqual({ values: ['bar'], touched: [true] });

    opt.props.push('baz');
    opt.props.push('qux');
    opt.props.swap(0, 1);
    expect(slices.foo).toEqual({
      values: ['baz', 'bar', 'qux'],
      touched: [null, true, null],
    });

    opt.props.move(2, 0);
    expect(slices.foo).toEqual({
      values: ['qux', 'baz', 'bar'],
      touched: [null, null, true],
    });

    opt.props.insert(1, 'foo');
    expect(slices.foo).toEqual({
      values: ['qux', 'foo', 'baz', 'bar'],
      touched: [null, null, null, true],
    });

    opt.props.remove(0);
    opt.props.remove(1);
    expect(slices.foo).toEqual({
      values: ['foo', 'bar'],
      touched: [null, true],
    });

    opt.props.unshift('baz');
    expect(slices.foo).toEqual({
      values: ['baz', 'foo', 'bar'],
      touched: [null, null, true],
    });

    opt.props.replace(2, 'qux');
    expect(slices.foo).toEqual({
      values: ['baz', 'foo', 'qux'],
      touched: [null, null, true],
    });
  });
});
