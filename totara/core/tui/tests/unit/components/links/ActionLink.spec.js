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
 * @author Simon Chester <simon.chester@totaralearning.com>
 * @package totara_core
 */

import { shallowMount } from '@vue/test-utils';
import ActionLink from 'totara_core/components/links/ActionLink';

const props = {
  styleclass: { primary: true, small: true },
  href: 'https://www.google.com/',
  text: 'Text',
};

describe('ActionLink.vue', () => {
  it('passes click event through', () => {
    const directHandler = jest.fn();
    const vueHandler = jest.fn();

    const wrapper = shallowMount(ActionLink, {
      propsData: props,
      listeners: {
        click: vueHandler,
      },
    });

    wrapper.element.addEventListener('click', directHandler);

    wrapper.trigger('click');

    expect(directHandler).toHaveBeenCalled();
    expect(vueHandler).toHaveBeenCalled();
    expect(wrapper.element.href).toBe(props.href);
  });

  it('supresses clicks when disabled', () => {
    const vueHandler = jest.fn();

    const wrapper = shallowMount(ActionLink, {
      propsData: { ...props, disabled: true },
      listeners: {
        click: vueHandler,
      },
    });

    wrapper.trigger('click');

    expect(vueHandler).not.toHaveBeenCalled();
    expect(wrapper.element.href).toBeEmpty();
  });

  it('matches snapshot', () => {
    let wrapper;

    wrapper = shallowMount(ActionLink, { propsData: props });
    expect(wrapper.element).toMatchSnapshot();

    wrapper = shallowMount(ActionLink, {
      propsData: {
        ...props,
        disabled: true,
      },
    });
    expect(wrapper.element).toMatchSnapshot();
  });
});
