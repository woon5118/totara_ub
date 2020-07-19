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
import component from 'totara_core/components/form/InputSearch.vue';
let wrapper;
const inputEventFunc = jest.fn();
const submitEventFunc = jest.fn();

describe('presentation/form/InputSearch.vue', () => {
  beforeAll(() => {
    wrapper = shallowMount(component, {
      propsData: {
        ariaDescribedby: '#ariaDescription',
        ariaLabel: 'aria label',
        ariaLabelledby: '#ariaLabel',
        autocomplete: 'on',
        autofocus: true,
        disabled: true,
        id: 'searchinput',
        maxlength: 10,
        minlength: 1,
        name: 'searchinput',
        pattern: '[A-Za-z]',
        placeholder: 'search here',
        readonly: true,
        required: true,
        size: 20,
        spellcheck: true,
        value: 'foo',
      },
      listeners: {
        input: inputEventFunc,
        submit: submitEventFunc,
      },
    });
  });

  it('ariaDescribedby can be set', () => {
    let propValue = wrapper.find('#searchinput').props().ariaDescribedby;
    expect(propValue).toEqual('#ariaDescription');
  });

  it('ariaLabel can be set', () => {
    let propValue = wrapper.find('#searchinput').props().ariaLabel;
    expect(propValue).toEqual('aria label');
  });

  it('ariaLabelledby can be set', () => {
    let propValue = wrapper.find('#searchinput').props().ariaLabelledby;
    expect(propValue).toEqual('#ariaLabel');
  });

  it('autocomplete can be set', () => {
    let propValue = wrapper.find('#searchinput').props().autocomplete;
    expect(propValue).toEqual('on');
  });

  it('autofocus can be set', () => {
    let propValue = wrapper.find('#searchinput').props().autofocus;
    expect(propValue).toBeTruthy();
  });

  it('disabled can be set', () => {
    let propValue = wrapper.find('#searchinput').props().disabled;
    expect(propValue).toBeTruthy();
  });

  it('maxlength can be set', () => {
    let propValue = wrapper.find('#searchinput').props().maxlength;
    expect(propValue).toEqual(10);
  });

  it('minlength can be set', () => {
    let propValue = wrapper.find('#searchinput').props().minlength;
    expect(propValue).toEqual(1);
  });

  it('name can be set', () => {
    let propValue = wrapper.find('#searchinput').props().name;
    expect(propValue).toEqual('searchinput');
  });

  it('pattern can be set', () => {
    let propValue = wrapper.find('#searchinput').props().pattern;
    expect(propValue).toEqual('[A-Za-z]');
  });

  it('placeholder can be set', () => {
    let propValue = wrapper.find('#searchinput').props().placeholder;
    expect(propValue).toEqual('search here');
  });

  it('readonly can be set', () => {
    let propValue = wrapper.find('#searchinput').props().readonly;
    expect(propValue).toBeTruthy();
  });

  it('required can be set', () => {
    let propValue = wrapper.find('#searchinput').props().required;
    expect(propValue).toBeTruthy();
  });

  it('size can be set', () => {
    let propValue = wrapper.find('#searchinput').props().size;
    expect(propValue).toEqual(20);
  });

  it('spellcheck can be set', () => {
    let propValue = wrapper.find('#searchinput').props().spellcheck;
    expect(propValue).toBeTruthy();
  });

  it('Value can be set', () => {
    let propValue = wrapper.find('#searchinput').props().value;
    expect(propValue).toEqual('foo');
  });

  it('Input event can be triggered', () => {
    let input = wrapper.find('#searchinput');
    input.vm.$emit('input', 'some value');
    expect(inputEventFunc).toHaveBeenCalled();
  });

  it('Submit event can be triggered', () => {
    let input = wrapper.find('#searchinput');
    input.vm.$emit('submit');
    expect(submitEventFunc).toHaveBeenCalled();
  });

  it('Checks snapshot has not changed', () => {
    expect(wrapper.element).toMatchSnapshot();
  });
});
