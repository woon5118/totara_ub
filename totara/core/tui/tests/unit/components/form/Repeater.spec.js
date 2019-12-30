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
import Repeater from 'totara_core/components/form/Repeater';

describe('presentation/form/Repeater.vue', () => {
  let wrapper;

  beforeAll(() => {
    wrapper = shallowMount(Repeater, {
      mocks: {
        $str: function() {
          return 'Add';
        },
      },
      propsData: {
        ariaLabel: 'Repeater btn',
        rows: [
          {
            value: 'first value',
            disabled: false,
            label: 'first label',
          },
          {
            value: '',
            disabled: false,
            label: 'second label',
          },
          {
            value: 'third value',
            disabled: false,
            label: 'third label',
          },
        ],
        minRows: 2,
        disabled: false,
        deleteIcon: true,
      },
    });
  });

  it('matches snapshot', () => {
    expect(wrapper.element).toMatchSnapshot();
  });
});
