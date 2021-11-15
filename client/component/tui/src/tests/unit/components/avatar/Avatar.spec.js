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
 * @module tui
 */

import { shallowMount } from '@vue/test-utils';
import component from 'tui/components/avatar/Avatar';
import { axe, toHaveNoViolations } from 'jest-axe';
expect.extend(toHaveNoViolations);
let wrapper;

describe('Avatar.vue', () => {
  beforeAll(() => {
    wrapper = shallowMount(component, {
      propsData: {
        src: 'sourceUrl',
        alt: 'avatar',
        size: 'small',
      },
    });
  });

  it('matches snapshot', () => {
    expect(wrapper.element).toMatchSnapshot();
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
