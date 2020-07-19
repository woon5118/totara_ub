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
import { mount, shallowMount } from '@vue/test-utils';
import Basket from 'totara_core/components/basket/Basket';
import { axe, toHaveNoViolations } from 'jest-axe';
expect.extend(toHaveNoViolations);

describe('Basket', () => {
  it('disables actions when no items', async () => {
    const wrapper = mount(Basket, {
      propsData: {
        items: [1, 2, 3],
        bulkActions: [
          { label: 'Action', action: () => {} },
          { label: 'Action', action: () => {} },
        ],
      },
      mocks: {
        $str: (x, y) => `[[${x}, ${y}]]`,
        $id: x => 'id' + x,
      },
      scopedSlots: {
        actions({ empty }) {
          return (
            !empty &&
            this.$createElement('div', { class: 'test-action' }, 'hello')
          );
        },
      },
    });

    expect(wrapper.find('.tui-basket__selectedCount').text()).toBe('3');
    expect(wrapper.find('button').attributes('disabled')).toBeFalsy();
    expect(wrapper.find('.test-action').exists()).toBeTrue();

    wrapper.setProps({ items: [] });
    await Vue.nextTick();

    expect(wrapper.find('.tui-basket__selectedCount').text()).toBe('0');
    expect(wrapper.find('button').attributes('disabled')).toBeTruthy();
    expect(wrapper.find('.test-action').exists()).toBeFalse();
  });

  it('calls action when single bulk action clicked', async () => {
    const action = jest.fn();

    const wrapper = mount(Basket, {
      propsData: {
        items: [1, 2, 3],
        bulkActions: [{ label: 'Action', action: action }],
      },
      mocks: {
        $str: (x, y) => `[[${x}, ${y}]]`,
        $id: x => 'id' + x,
      },
    });

    expect(action).not.toHaveBeenCalled();

    wrapper.find('button').trigger('click');

    expect(action).toHaveBeenCalledTimes(1);
  });

  it('calls action when multiple bulk action clicked', async () => {
    const action = jest.fn();

    const wrapper = mount(Basket, {
      propsData: {
        items: [1, 2, 3],
        bulkActions: [
          { label: 'Action', action: action },
          { label: 'Other', action: () => {} },
        ],
      },
      mocks: {
        $str: (x, y) => `[[${x}, ${y}]]`,
        $id: x => 'id' + x,
      },
    });

    expect(action).not.toHaveBeenCalled();

    wrapper.find('a.tui-dropdownItem').trigger('click');

    expect(action).toHaveBeenCalledTimes(1);
  });

  it('matches snapshot', () => {
    const wrapper = shallowMount(Basket, {
      propsData: {
        items: [1, 2, 3],
        bulkActions: [
          { label: 'Action', action: () => {} },
          { label: 'Action', action: () => {} },
        ],
      },
      mocks: {
        $str: (x, y) => `[[${x}, ${y}]]`,
        $id: x => 'id' + x,
      },
    });

    expect(wrapper.element).toMatchSnapshot();
  });

  it('should not have any accessibility violations', async () => {
    const action = jest.fn();

    const wrapper = mount(Basket, {
      propsData: {
        items: [1, 2, 3],
        bulkActions: [{ label: 'Action', action: action }],
      },
      mocks: {
        $str: (x, y) => `[[${x}, ${y}]]`,
        $id: x => 'id' + x,
      },
    });

    const results = await axe(wrapper.element, {
      rules: {
        region: { enabled: false },
      },
    });
    expect(results).toHaveNoViolations();
  });
});
