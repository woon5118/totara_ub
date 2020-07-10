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
import component from 'totara_core/components/toggle/ToggleSwitch.vue';
import Vue from 'vue';
import { axe, toHaveNoViolations } from 'jest-axe';

expect.extend(toHaveNoViolations);

const inputEventFunc = jest.fn();
let wrapper;
let propsData;
let listeners;
let button;

describe('ToggleButton.vue', () => {
  beforeEach(() => {
    propsData = {
      value: false,
      disabled: false,
      text: 'toggle button text',
      id: 'toggle',
    };
    listeners = {
      input: inputEventFunc,
    };
  });

  inputEventFunc.mockClear();

  describe('Toggles aria-pressed on click', () => {
    it('adds aria-pressed', async () => {
      wrapper = shallowMount(component, { propsData, listeners });
      button = wrapper.find('button');

      let propValue = wrapper.props().value;
      expect(propValue).toBeFalsy();

      expect(button.attributes('aria-pressed')).toBeUndefined();
      button.trigger('click');
      expect(inputEventFunc).toHaveBeenCalled();

      wrapper.setProps({
        value: true,
      });
      await Vue.nextTick();

      propValue = wrapper.props().value;
      expect(propValue).toBeTruthy();

      expect(button.attributes('aria-pressed')).toBe('true');
    });

    it('does nothing if the button is disabled', async () => {
      wrapper = shallowMount(component, { propsData, listeners });
      button = wrapper.find('button');

      expect(button.attributes('aria-pressed')).toBeUndefined();

      wrapper.setProps({
        disabled: true,
      });

      button.trigger('click');
      await Vue.nextTick();
      expect(button.attributes('aria-pressed')).toBeUndefined();
    });

    it('Renders correctly with something in the slot', () => {
      wrapper = shallowMount(component, {
        propsData,
        $mocks: { pressed: false },
        scopedSlots: {
          icon() {
            return this.$createElement('div', {}, ['icon button']);
          },
        },
      });

      expect(wrapper.element).toMatchSnapshot();
    });

    it('Renders correctly with an empty slot', () => {
      wrapper = shallowMount(component, {
        propsData,
        $mocks: { pressed: false },
      });

      expect(wrapper.element).toMatchSnapshot();
    });

    it('Renders correctly with something in the slot and an aria-label', () => {
      propsData = { ...propsData, ariaLabel: 'Different button text' };
      wrapper = shallowMount(component, {
        propsData,
        $mocks: { pressed: false },
        scopedSlots: {
          icon() {
            return this.$createElement('div', {}, ['icon button']);
          },
        },
      });

      expect(wrapper.element).toMatchSnapshot();
    });

    it('should not have any accessibility violations with something in the slot', async () => {
      wrapper = shallowMount(component, {
        propsData,
        $mocks: { pressed: false },
        scopedSlots: {
          icon() {
            return this.$createElement('div', {}, ['icon button']);
          },
        },
      });

      const results = await axe(wrapper.element, {
        rules: {
          region: { enabled: false },
        },
      });

      expect(results).toHaveNoViolations();
    });

    it('should not have any accessibility violations with an empty slot', async () => {
      wrapper = shallowMount(component, {
        propsData,
        $mocks: { pressed: false },
      });

      const results = await axe(wrapper.element, {
        rules: {
          region: { enabled: false },
        },
      });

      expect(results).toHaveNoViolations();
    });
  });
});
