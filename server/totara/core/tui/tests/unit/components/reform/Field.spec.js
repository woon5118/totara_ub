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

import Vue from 'vue';
import { mount } from '@vue/test-utils';
import Field from 'totara_core/components/reform/Field';
import {
  ReformScopeProvider,
  ReformFieldContextProvider,
  createMockScope,
} from './util';
import { fieldValidator } from 'totara_core/validation';

jest.mock('totara_core/validation', () => {
  const fieldValidator = jest.fn(fn => fn);
  return { fieldValidator };
});

function create({ scope, fieldContext, name, props }) {
  const result = {};
  const outerWrapper = mount(ReformScopeProvider, {
    propsData: { scope },
    stubs: {
      Scope: true,
    },
    scopedSlots: {
      default() {
        const h = this.$createElement;
        return h(ReformFieldContextProvider, {
          props: { fieldContext },
          scopedSlots: {
            default() {
              return h(Field, {
                props: { name, ...props },
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
      },
    },
  });

  result.wrapper = outerWrapper.find(Field);

  return result;
}

describe('Field', () => {
  it('provides slot props to interact with Reform state', () => {
    const scope = createMockScope();
    const fieldContext = { getId: () => 4, getLabelId: () => 5 };

    scope.getValue.mockImplementation(path => (path == 'foo' ? 3 : null));
    scope.getError.mockImplementation(path => (path == 'foo' ? 'err' : null));
    scope.getInputName.mockImplementation(x => 'input-' + x);

    const opt = create({ scope, fieldContext, name: 'foo' });

    expect(opt.props.id).toBe(4);
    expect(opt.props.value).toBe(3);
    expect(opt.props.name).toBe('foo');
    expect(opt.props.inputName).toBe('input-foo');
    opt.props.update('val');
    expect(scope.update).toHaveBeenCalledWith('foo', 'val');
    opt.props.blur();
    expect(scope.blur).toHaveBeenCalledWith('foo');
    expect(opt.props.error).toBe('err');
    expect(scope.getError).toHaveBeenCalledWith('foo');
  });

  it('registers validator for field', async () => {
    const scope = createMockScope();
    const fieldContext = { getId: () => 4, getLabelId: () => 5 };
    const validate = jest.fn();

    const opt = create({
      scope,
      fieldContext,
      name: 'foo',
      props: {
        validate,
      },
    });

    expect(scope.updateRegistration).toHaveBeenCalledWith(
      'validator',
      'foo',
      validate,
      undefined,
      undefined
    );

    opt.wrapper.setProps({ validate: null });
    await Vue.nextTick();

    expect(scope.updateRegistration).toHaveBeenCalledWith(
      'validator',
      'foo',
      null,
      'foo',
      validate
    );

    const validationsFn = jest.fn();

    opt.wrapper.setProps({ validations: validationsFn });
    await Vue.nextTick();

    expect(fieldValidator).toHaveBeenCalledWith(validationsFn);
    expect(scope.updateRegistration).toHaveBeenCalledWith(
      'validator',
      'foo',
      validationsFn,
      'foo',
      null
    );
  });
});
