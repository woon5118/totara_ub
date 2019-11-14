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
import component from 'totara_core/components/datatable/Table.vue';
let wrapper;

const propsData = {
  data: [
    {
      ready: true,
      title: 'aaa',
    },
    {
      ready: true,
      title: 'bbb',
    },
    {
      ready: false,
      title: 'ccc',
    },
    {
      ready: true,
      title: 'ddd',
    },
  ],
  'expandable-rows': true,
};

describe('presentation/datatable/Table.vue', () => {
  it('Checks snapshot', () => {
    wrapper = shallowMount(component, {
      propsData,
      mocks: {
        $str() {
          return 'No items to display';
        },
      },
    });

    expect(wrapper.element).toMatchSnapshot();
  });

  it('Checks snapshot with no row data supplied', () => {
    wrapper = shallowMount(component, {
      propsData: { 'expandable-rows': true, data: [] },
      mocks: {
        $str() {
          return 'No items to display';
        },
      },
    });

    expect(wrapper.element).toMatchSnapshot();
  });
});
