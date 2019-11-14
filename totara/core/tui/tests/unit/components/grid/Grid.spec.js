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
import Grid from 'totara_core/components/grid/Grid';
import GridItem from 'totara_core/components/grid/GridItem';
let wrapper;
global.ResizeObserver = class {
  observe() {
    return;
  }
  unobserve() {
    return;
  }
};

describe('presentation/grid/Grid.vue', () => {
  beforeAll(() => {
    wrapper = shallowMount(Grid, {
      propsData: {
        id: 'grid',
        direction: 'vertical',
        maxUnits: '16',
        stackAt: 960,
      },
      scopedSlots: {
        default: function() {
          return this.$createElement(GridItem, {
            props: {
              units: 8,
              order: 2,
              grows: true,
              shrinks: false,
              overflows: true,
              hyphens: false,
              sizeData: {
                gutterSize: '12px',
                maxGridUnits: 16,
                numberOfSuppliedGridItems: 8,
              },
            },
          });
        },
      },
    });
  });

  it('direction can be set', () => {
    let propValue = wrapper.find('#grid').props().direction;
    expect(propValue).toBeTruthy();
  });

  it('maxUnits can be set', () => {
    let propValue = wrapper.find('#grid').props().maxUnits;
    expect(propValue).toBeTruthy();
  });

  it('stackAt can be set', () => {
    let propValue = wrapper.find('#grid').props().stackAt;
    expect(propValue).toBeTruthy();
  });

  it('gridClasses method returns Array of default classes', () => {
    expect(Array.isArray(wrapper.vm.gridClasses())).toBe(true);
  });

  it('gridClasses method returns Array of default and additional classes', () => {
    let defaultCount = wrapper.vm.gridClasses().length,
      additionalClasses = ['customClass', 'anotherClass'];
    expect(wrapper.vm.gridClasses(additionalClasses).length).toBe(
      defaultCount + additionalClasses.length
    );
  });

  it('Checks snapshot', () => {
    expect(wrapper.element).toMatchSnapshot();
  });
});
