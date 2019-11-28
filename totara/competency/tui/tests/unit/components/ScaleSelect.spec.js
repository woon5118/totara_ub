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
 * @author Matthias Bonk <matthias.bonk@totaralearning.com>
 * @package totara_competency
 */

import { shallowMount } from '@vue/test-utils';
import component from 'totara_competency/components/ScaleSelect';
let wrapper;

describe('components/ScaleSelect.vue', () => {
  beforeAll(() => {
    wrapper = shallowMount(component, {
      mocks: {
        $str: function() {
          return 'fff';
        },
      },
      propsData: {
        competencyId: 999,
        scale: {
          values: [
            { id: 7, name: 'test 7' },
            { id: 8, name: 'test 8' },
          ],
        },
      },
    });
  });

  it('Checks snapshot', () => {
    expect(wrapper.element).toMatchSnapshot();
  });

  it('Checks making select options', () => {
    expect(
      wrapper.vm.makeSelectOptions([
        { id: 7, name: 'test 7' },
        { id: 8, name: 'test 8' },
      ])
    ).toEqual([
      { label: '', id: -2 },
      { label: 'test 7', id: 7 },
      { label: 'test 8', id: 8 },
      { label: '------------------', id: -2, disabled: true },
      { label: 'fff', id: -1 },
    ]);
  });
});
