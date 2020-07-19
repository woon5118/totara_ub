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
import RadioGroup from 'totara_core/components/form/RadioGroup';

const PropsProviderStub = {
  props: ['provide'],
  render(h) {
    return h('div', [this.$scopedSlots.default()]);
  },
};

describe('presentation/form/RadioGroup.vue', () => {
  it('passes selected info to children using PropsProvider', () => {
    const handleInput = jest.fn();

    const wrapper = shallowMount(RadioGroup, {
      slots: {
        default: ['test slot content'],
      },
      stubs: {
        PropsProvider: PropsProviderStub,
      },
      propsData: {
        name: 'city',
        value: 'foo',
        disabled: false,
        required: true,
      },
      listeners: {
        input: handleInput,
      },
    });

    const providerProps = wrapper.find(PropsProviderStub).props();
    const info = { props: { value: 'foo' } };
    expect(providerProps.provide(info)).toMatchObject({
      props: {
        name: 'city',
        checked: true,
        disabled: false,
        required: true,
      },
    });

    const handleSelect = providerProps.provide(info).listeners.select;
    expect(handleInput).toHaveBeenCalledTimes(0);
    handleSelect('bar');
    expect(handleInput).toHaveBeenCalledWith('bar');
  });
});
