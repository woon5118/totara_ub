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
 * @author Kevin Hottinger <kevin.hottinger@totaralearning.com>
 * @module totara_core
 */

import { shallowMount } from '@vue/test-utils';
import component from 'totara_core/components/datatable/SelectRowCell.vue';
let wrapper;

describe('presentation/datatable/SelectRowCell.vue', () => {
  beforeAll(() => {
    wrapper = shallowMount(component, {
      mocks: {
        $str: function() {
          return 'fff';
        },
      },
      propsData: {
        selectrow: 'select row',
        rowLabel: ' the display name of the row',
      },
    });
  });

  it('Checks snapshot', () => {
    expect(wrapper.element).toMatchSnapshot();
  });
});
