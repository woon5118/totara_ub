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
 * @author Alvin Smith <alvin.smith@totaralearning.com>
 * @module totara_core
 */

import { mount } from '@vue/test-utils';
import Dropdown from 'tui/components/dropdown/Dropdown';
import { axe, toHaveNoViolations } from 'jest-axe';
expect.extend(toHaveNoViolations);

let wrapper;

describe('Dropdown', () => {
  beforeEach(() => {
    wrapper = mount(Dropdown, {
      mocks: {
        $id: x => 'id-' + x,
      },
    });
  });

  it('render correctly', () => {
    expect(wrapper.html()).toMatchSnapshot();
  });

  it('manage clicking outside accordingly', () => {
    const el = document.createElement('div');
    const event = {
      target: el,
    };

    wrapper.vm.triggerOpen = true;
    wrapper.vm.$_clickedOutside({
      target: wrapper.vm.$refs.trigger,
    });
    expect(wrapper.vm.triggerOpen).toBeTruthy();

    wrapper.vm.triggerOpen = true;
    wrapper.setProps({ canClose: false });
    wrapper.vm.$_clickedOutside(event);
    expect(wrapper.vm.triggerOpen).toBeTruthy();
  });

  it('should not have any accessibility violations', async () => {
    const results = await axe(wrapper.element, {
      rules: {
        region: { enabled: false },
      },
    });
    expect(results).toHaveNoViolations();
  });
});
