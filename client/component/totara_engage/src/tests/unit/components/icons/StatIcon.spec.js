/**
 * This file is part of Totara Enterprise Extensions.
 *
 * Copyright (C) 2020 onwards Totara Learning Solutions LTD
 *
 * Totara Enterprise Extensions is provided only to Totara
 * Learning Solutions LTD's customers and partners, pursuant to
 * the terms and conditions of a separate agreement with Totara
 * Learning Solutions LTD or its affiliate.
 *
 * If you do not have an agreement with Totara Learning Solutions
 * LTD, you may not access, use, modify, or distribute this software.
 * Please contact [licensing@totaralearning.com] for more information.
 *
 * @author Kian Nguyen <kian.nguyen@totaralearning.com>
 * @module totara_engage
 */

import { shallowMount } from '@vue/test-utils';
import StatIcon from 'totara_engage/components/icons/StatIcon';

describe('totara_engage/components/icons/StatIcon.vue', () => {
  let wrapper = null;

  beforeAll(() => {
    wrapper = shallowMount(StatIcon, {
      propsData: {
        icon: 'totara_core|like',
        title: '15 likes',
        statNumber: 15,
      },
    });
  });

  it('Checks the stat number', () => {
    let value = wrapper.find('.tui-totaraEngage-statIcon__statNumber').text();
    expect(value).toEqual('15');
  });

  it('Checks snapshot', () => {
    expect(wrapper.element).toMatchSnapshot();
  });
});
