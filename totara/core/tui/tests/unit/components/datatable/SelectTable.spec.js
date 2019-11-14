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

import { mount } from '@vue/test-utils';
import component from 'totara_core/components/datatable/SelectTable.vue';
import Vue from 'vue';
let wrapper;

Vue.directive('focus-within', {});

const propsData = {
  value: [],
  data: [
    {
      ready: true,
      the_name_of_the_name_will_vary: 'one',
    },
    {
      ready: true,
      the_name_of_the_name_will_vary: 'two',
    },
    {
      ready: false,
      the_name_of_the_name_will_vary: 'three',
    },
    {
      ready: true,
      the_name_of_the_name_will_vary: 'four',
    },
  ],
  'expandable-rows': true,
  rowLabelKey: 'the_name_of_the_name_will_vary',
};

let i = 0;

describe('presentation/datatable/SelectTabel.vue', () => {
  beforeAll(() => {
    wrapper = mount(component, {
      Vue,
      propsData,
      mocks: {
        $str: function() {
          return `lang string ${i++}`;
        },
      },
    });
  });

  it('Checks snapshot', () => {
    expect(wrapper.html()).toMatchSnapshot();
  });
});
