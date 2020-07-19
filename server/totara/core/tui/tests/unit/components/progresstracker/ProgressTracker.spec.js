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
 * @author Kevin Hottinger <kevin.hottinger@totaralearning.com>
 * @package totara_core
 */

import { mount } from '@vue/test-utils';
import component from 'totara_core/components/progresstracker/ProgressTracker';
import { axe, toHaveNoViolations } from 'jest-axe';
expect.extend(toHaveNoViolations);
let wrapper;

const props = {
  items: [
    {
      id: 1,
      description: 'Basic knowledge description',
      label: 'Basic knowledge',
    },
    {
      id: 2,
      description: 'Competent with supervision description',
      label: 'Competent with supervision',
    },
  ],
  gap: 'medium',
  currentId: 1,
  targetId: 2,
};

describe('ProgressTracker', () => {
  beforeAll(() => {
    wrapper = mount(component, {
      propsData: props,
      mocks: {
        $str: function() {
          return 'tempstring';
        },
      },
      stubs: ['CloseButton'],
    });
  });

  it('should check snapshot', () => {
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
