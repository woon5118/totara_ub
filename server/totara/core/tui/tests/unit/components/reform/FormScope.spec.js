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
import FormScope from 'totara_core/components/reform/FormScope';
import {
  ReformScopeProvider,
  ReformScopeReceiver,
  pathMethods,
  createMockScope,
} from './util';

function create({ scope, path, validate }) {
  const outerWrapper = mount(ReformScopeProvider, {
    propsData: { scope },
    scopedSlots: {
      default() {
        const h = this.$createElement;
        return h(FormScope, {
          props: { path, validate },
          scopedSlots: {
            default() {
              return h(ReformScopeReceiver);
            },
          },
        });
      },
    },
  });

  const wrapper = outerWrapper.find(FormScope);
  const receiver = outerWrapper.find(ReformScopeReceiver).vm;

  return { wrapper, receiver };
}

describe('Scope', () => {
  it('proxies reformScope calls, prefixing path', () => {
    const scope = createMockScope();
    const { receiver } = create({ scope, path: 'foo' });

    pathMethods.forEach(name => {
      receiver.reformScope[name]('bar', name + ' arg');
      expect(scope[name]).toHaveBeenCalledWith(['foo', 'bar'], name + ' arg');
    });

    receiver.reformScope.register('validator', 'bar', 1);
    expect(scope.register).toHaveBeenCalledWith('validator', ['foo', 'bar'], 1);

    receiver.reformScope.unregister('validator', 'bar', 1, 'baz', 2);
    expect(scope.unregister).toHaveBeenCalledWith(
      'validator',
      ['foo', 'bar'],
      1
    );

    receiver.reformScope.updateRegistration('validator', 'bar', 1, 'baz', 2);
    expect(scope.updateRegistration).toHaveBeenCalledWith(
      'validator',
      ['foo', 'bar'],
      1,
      ['foo', 'baz'],
      2
    );
  });

  it('registers supplied validator', async () => {
    const scope = createMockScope();
    const validate = () => {};
    const { wrapper } = create({ scope, path: 'foo', validate });
    expect(scope.updateRegistration).toHaveBeenCalledWith(
      'validator',
      'foo',
      validate,
      undefined,
      undefined
    );
    scope.updateRegistration.mockReset();

    wrapper.setProps({ path: 'bar' });
    await Vue.nextTick();
    expect(scope.updateRegistration).toHaveBeenCalledWith(
      'validator',
      'bar',
      validate,
      'foo',
      validate
    );
    scope.updateRegistration.mockReset();

    const newValidate = () => {};
    wrapper.setProps({ validate: newValidate });
    await Vue.nextTick();
    expect(scope.updateRegistration).toHaveBeenCalledWith(
      'validator',
      'bar',
      newValidate,
      'bar',
      validate
    );

    wrapper.destroy();

    expect(scope.unregister).toHaveBeenCalledWith(
      'validator',
      'bar',
      newValidate
    );
  });
});
