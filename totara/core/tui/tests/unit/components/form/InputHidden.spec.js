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
 * @author Kevin Hottinger <kevin.hottinger@totaralearning.com>
 * @package totara_core
 */

import { shallowMount } from '@vue/test-utils';
import component from 'totara_core/components/form/InputHidden.vue';
let wrapper;

describe('presentation/form/InputHidden.vue', () => {
  beforeAll(() => {
    wrapper = shallowMount(component, {
      propsData: {
        autocomplete: 'on',
        disabled: true,
        id: 'inputid',
        name: 'hiddeninput',
        readonly: true,
        value: 'hidden value',
      },
    });
  });

  it('autocomplete can be set', () => {
    let propValue = wrapper.find('#inputid').props().autocomplete;
    expect(propValue).toEqual('on');
  });

  it('disabled can be set', () => {
    let propValue = wrapper.find('#inputid').props().disabled;
    expect(propValue).toBeTruthy();
  });

  it('name can be set', () => {
    let propValue = wrapper.find('#inputid').props().name;
    expect(propValue).toEqual('hiddeninput');
  });

  it('readonly can be set', () => {
    let propValue = wrapper.find('#inputid').props().readonly;
    expect(propValue).toBeTruthy();
  });

  it('Value can be set', () => {
    let propValue = wrapper.find('#inputid').props().value;
    expect(propValue).toEqual('hidden value');
  });

  it('Checks snapshot has not changed', () => {
    expect(wrapper.element).toMatchSnapshot();
  });
});
