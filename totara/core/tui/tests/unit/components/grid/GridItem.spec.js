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
 * @author Dave Wallace <dave.wallace@totaralearning.com>
 * @package totara_core
 */

import { shallowMount } from '@vue/test-utils';
import component from 'totara_core/components/grid/GridItem';
let wrapper;

const props = {
  units: 2,
  order: 1,
  grows: true,
  shrinks: false,
  overflows: true,
  hyphens: false,
  sizeData: {
    gutterSize: '12px',
    maxGridUnits: '16',
    numberOfSuppliedGridItems: 8,
  },
};

describe('presentation/grid/GridItem.vue', () => {
  beforeAll(() => {
    wrapper = shallowMount(component, {
      propsData: props,
    });
  });

  it('Props can be set', () => {
    expect(wrapper.props()).toMatchObject(props);
  });

  it('Checks snapshot', () => {
    expect(wrapper.element).toMatchSnapshot();
  });
});
