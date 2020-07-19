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

import Vue from 'vue';
import { shallowMount } from '@vue/test-utils';
import PopoverTrigger from 'totara_core/components/popover/PopoverTrigger';

describe('PopoverTrigger', () => {
  it('triggers events', async () => {
    const changed = jest.fn();
    const wrapper = shallowMount(PopoverTrigger, {
      propsData: {
        triggers: ['click'],
      },
      scopedSlots: {
        default() {
          return this.$createElement('button');
        },
      },
      listeners: {
        'open-changed': changed,
      },
    });
    const button = wrapper.find('button');
    expect(changed).not.toHaveBeenCalled();

    button.trigger('click');
    await Vue.nextTick();
    expect(changed).toHaveBeenCalledTimes(1);
    expect(changed.mock.calls[0][0]).toBe(true);

    button.trigger('click');
    await Vue.nextTick();
    expect(changed).toHaveBeenCalledTimes(2);
    expect(changed.mock.calls[1][0]).toBe(false);
  });
});
