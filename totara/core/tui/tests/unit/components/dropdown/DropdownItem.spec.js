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
 * @author Alvin Smith <alvin.smith@totaralearning.com>
 * @package totara_core
 */

import { shallowMount } from '@vue/test-utils';
import DropdownItem from 'totara_core/components/dropdown/DropdownItem';
import Vue from 'vue';

let wrapper;
const dropdownSelected = 'val';
const Dropdown = {
  template: '<dropdown></dropdown>',
  data() {
    return {
      selected: dropdownSelected,
    };
  },
};

describe('DropdownItem', () => {
  beforeEach(() => {
    wrapper = shallowMount(DropdownItem, {
      parentComponent: Dropdown,
      propsData: { isDropdown: true },
    });
  });

  it('render correctly', () => {
    expect(wrapper.html()).toMatchSnapshot();
  });

  it('returns item classes accordingly', async () => {
    const value = dropdownSelected;
    const disabled = false;
    const paddingless = true;
    wrapper.setProps({
      value,
      disabled,
      paddingless,
    });
    await Vue.nextTick();
    expect(wrapper.classes()).toContain('tui-dropdownItem--paddingless');
  });
});
