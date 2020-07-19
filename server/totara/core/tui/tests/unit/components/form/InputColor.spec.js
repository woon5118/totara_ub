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
 * @author Dave Wallace <dave.wallace@totaralearning.com>
 * @package totara_core
 */

import { shallowMount } from '@vue/test-utils';
import component from 'totara_core/components/form/InputColor.vue';

const { axe, toHaveNoViolations } = require('jest-axe');
expect.extend(toHaveNoViolations);

let wrapper;
const inputEventFunc = jest.fn();
const badValue = 'red';
const goodValue = '#FF0000';

describe('InputColor', () => {
  beforeAll(() => {
    wrapper = shallowMount(component, {
      propsData: {
        ariaDescribedby: '#ariaDescription',
        ariaLabel: 'aria label',
        ariaLabelledby: '#ariaLabel',
        disabled: true,
        id: 'inputid',
        maxlength: 7,
        name: 'colorinput',
        readonly: true,
        required: true,
        value: goodValue,
      },
      listeners: {
        input: inputEventFunc,
      },
      mocks: {
        $id: () => 'inputid',
        $str: (x, y) => `[[${x}, ${y}]]`,
      },
    });
  });

  it('disabled can be set', () => {
    let propValue = wrapper.find('#inputid').props().disabled;
    expect(propValue).toBeTruthy();
  });

  it('maxlength can be set', () => {
    let propValue = wrapper.find('#inputid').props().maxlength;
    expect(propValue).toEqual(7);
  });

  it('name can be set', () => {
    let propValue = wrapper.find('#inputid').props().name;
    expect(propValue).toEqual('colorinput');
  });

  it('readonly can be set', () => {
    let propValue = wrapper.find('#inputid').props().readonly;
    expect(propValue).toBeTruthy();
  });

  it('required can be set', () => {
    let propValue = wrapper.find('#inputid').props().required;
    expect(propValue).toBeTruthy();
  });

  it('Valid value can be set', () => {
    let propValue = wrapper.find('#inputid').props().value;
    expect(propValue).toEqual(goodValue);
  });

  it('Invalid value cannot be set', () => {
    wrapper.setProps({ value: badValue });
    let propValue = wrapper.find('#inputid').props().value;
    expect(propValue).toEqual(goodValue);
  });

  it('Input event can be triggered', () => {
    let input = wrapper.find('#inputid');
    input.vm.$emit('input', goodValue);
    expect(inputEventFunc).toHaveBeenCalled();
  });

  it('should not have an a11y violations', async () => {
    const results = await axe(wrapper.element);
    expect(results).toHaveNoViolations();
  });

  it('Checks snapshot has not changed', () => {
    expect(wrapper.element).toMatchSnapshot();
  });
});
