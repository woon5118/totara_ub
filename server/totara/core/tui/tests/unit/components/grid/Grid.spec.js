/**
 * This file is part of Totara Enterprise Extensions.
 *
 * Copyright (C) 2020 onwards Totara Learning Solutions LTD
 *
 * Totara Enterprise Extensions is provided only to Totara
 * Learning Solutions LTD’s customers and partners, pursuant to
 * the terms and conditions of a separate agreement with Totara
 * Learning Solutions LTD or its affiliate.
 *
 * If you do not have an agreement with Totara Learning Solutions
 * LTD, you may not access, use, modify, or distribute this software.
 * Please contact [licensing@totaralearning.com] for more information.
 *
 * @author Dave Wallace <dave.wallace@totaralearning.com>
 * @module totara_core
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
