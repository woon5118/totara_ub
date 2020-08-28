/**
 * This file is part of Totara Enterprise Extensions.
 *
 * Copyright (C) 2020 onwards Totara Learning Solutions LTD
 *
 * Totara Enterprise Extensions is provided only to Totara
 * Learning Solutions LTD's customers and partners, pursuant to
 * the terms and conditions of a separate agreement with Totara
 * Learning Solutions LTD or its affiliate.
 *
 * If you do not have an agreement with Totara Learning Solutions
 * LTD, you may not access, use, modify, or distribute this software.
 * Please contact [licensing@totaralearning.com] for more information.
 *
 * @author Kevin Hottinger <kevin.hottinger@totaralearning.com>
 * @module tui
 */

import { shallowMount } from '@vue/test-utils';
import component from 'tui/components/buttons/Button.vue';
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
