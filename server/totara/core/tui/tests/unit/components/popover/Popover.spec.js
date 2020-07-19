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
 * @author Simon Chester <simon.chester@totaralearning.com>
 * @module totara_core
 */

import { shallowMount } from '@vue/test-utils';
import Popover from 'totara_core/components/popover/Popover';

describe('Popover', () => {
  it('matches snapshot', () => {
    const wrapper = shallowMount(Popover, {
      scopedSlots: {
        trigger() {
          return this.$createElement('button');
        },
        default() {
          return this.$createElement('div', {}, ['hello']);
        },
      },
    });
    expect(wrapper.element).toMatchSnapshot();
  });
});
