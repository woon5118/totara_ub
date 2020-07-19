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

import { mount, createLocalVue } from '@vue/test-utils';
import component from 'totara_core/components/filters/FilterSidePanel';
import { axe, toHaveNoViolations } from 'jest-axe';
expect.extend(toHaveNoViolations);
const localVue = createLocalVue();
let wrapper;

describe('FilterSidePanel.vue', () => {
  beforeAll(() => {
    localVue.directive('focus-within', {});

    wrapper = mount(component, {
      localVue,
      propsData: {
        title: 'title text',
        value: {
          optionA: 'aa',
          optionB: '',
          optionC: 'cc',
          optionD: '',
        },
      },
      mocks: {
        $str: function() {
          return 'tempstring';
        },
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
