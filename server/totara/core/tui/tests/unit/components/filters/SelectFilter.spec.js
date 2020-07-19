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
import component from 'totara_core/components/filters/SelectFilter';
import { axe, toHaveNoViolations } from 'jest-axe';
expect.extend(toHaveNoViolations);
let options;
let wrapper;

describe('SelectFilter.vue', () => {
  beforeAll(() => {
    options = {
      propsData: {
        id: 'tempid',
        dropLabel: false,
        label: 'label text',
        options: [
          {
            id: 'course',
            label: 'Include courses',
          },
        ],
        showLabel: true,
      },
      mocks: {
        $str: function() {
          return 'tempstring';
        },
      },
    };
  });

  describe('with label', () => {
    beforeAll(() => {
      wrapper = mount(component, options);
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

  describe('with dropLabel', () => {
    beforeAll(() => {
      options.propsData.dropLabel = true;
      wrapper = mount(component, options);
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
});
