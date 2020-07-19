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
import component from 'totara_core/components/buttons/Button.vue';
import { axe, toHaveNoViolations } from 'jest-axe';
expect.extend(toHaveNoViolations);
import Vue from 'vue';

let wrapper;
const clickFunc = jest.fn();
const primaryClass = 'tui-formBtn--prim';
const smallClass = 'tui-formBtn--small';

describe('presentation/form/Button.vue', () => {
  beforeAll(() => {
    wrapper = shallowMount(component, {
      propsData: { text: 'btn text' },
      listeners: {
        click: clickFunc,
      },
    });
  });

  it('Checks button click function is called', () => {
    wrapper.find('button').vm.$emit('click');
    expect(clickFunc).toHaveBeenCalled();
  });

  it('Checks primary button class can be set', async () => {
    expect(wrapper.find('button').classes(primaryClass)).toBeFalsy();

    wrapper.setData({
      styleclass: {
        primary: 'true',
      },
    });
    await Vue.nextTick();
    expect(wrapper.find('button').classes()).toContain(primaryClass);
  });

  it('Checks small button class can be set', async () => {
    expect(wrapper.find('button').classes(smallClass)).toBeFalsy();

    wrapper.setData({
      styleclass: {
        small: 'true',
      },
    });
    await Vue.nextTick();
    expect(wrapper.find('button').classes()).toContain(smallClass);
  });

  it('Checks snapshot', () => {
    expect(wrapper.element).toMatchSnapshot();
  });

  it('should not have any accessibility violations', async () => {
    const results = await axe(wrapper.element, {
      rules: {
        region: { enabled: false },
      },
    });
    expect(results).toHaveNoViolations();
  });
});
