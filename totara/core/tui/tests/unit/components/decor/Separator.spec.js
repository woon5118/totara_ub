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
import component from 'totara_core/components/decor/Separator';
let wrapperDefault, wrapperSlottedContent;

describe('Separator.vue', () => {
  //
  beforeAll(() => {
    wrapperDefault = shallowMount(component, {
      propsData: {
        id: 'separator',
        thick: true,
        spread: true,
      },
    });

    wrapperSlottedContent = shallowMount(component, {
      slots: {
        default: '<span>ok</span>',
      },
    });
  });

  // test default <hr /> output
  it('thick can be set', () => {
    let propValue = wrapperDefault.find('#separator').props().thick;
    expect(propValue).toBeTruthy();
  });

  it('spread can be set', () => {
    let propValue = wrapperDefault.find('#separator').props().spread;
    expect(propValue).toBeTruthy();
  });

  it('Checks v-else output exists', () => {
    expect(wrapperDefault.findAll('span').length).toBe(0);
  });

  it('Checks v-else snapshot', () => {
    expect(wrapperDefault.element).toMatchSnapshot();
  });

  // test slot-provided output
  it('Checks v-if output exists', () => {
    let div = wrapperSlottedContent.find('div');
    expect(div.contains('span')).toBe(true);
  });

  it('Checks v-if snapshot', () => {
    expect(wrapperSlottedContent.element).toMatchSnapshot();
  });
});
