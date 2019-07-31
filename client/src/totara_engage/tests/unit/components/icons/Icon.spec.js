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
import Icon from 'totara_engage/components/icons/Icon';

describe('totara_engage/components/icons/Icon.vue', () => {
  let wrapper = null;

  beforeAll(() => {
    wrapper = shallowMount(Icon, {
      propsData: {
        icon: 'totara_engage|share',
      },
    });
  });

  afterEach(() => {
    wrapper.setProps({ clickable: false });
  });

  it('Checks snapshot', () => {
    expect(wrapper.element).toMatchSnapshot();
  });

  it('Check clicking event', () => {
    wrapper.vm.$_handleClick();
    expect(wrapper.emitted()).toBeEmpty();

    wrapper.setProps({ clickable: true });
    wrapper.vm.$_handleClick();

    let has = Object.prototype.hasOwnProperty;
    expect(has.call(wrapper.emitted(), 'click')).toBe(true);
  });

  it('Check clickable element', () => {
    expect(wrapper.vm.iconCss).toEqual('');

    wrapper.setProps({ clickable: true });
    expect(wrapper.vm.iconCss).toEqual('tui-totaraEngage-icon--clickable');
  });
});
