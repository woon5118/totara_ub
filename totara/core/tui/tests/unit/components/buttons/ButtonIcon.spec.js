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
 * @author Alvin Smith <alvin.smith@totaralearning.com>
 * @package totara_core
 */

import { shallowMount } from '@vue/test-utils';
import component from 'totara_core/components/buttons/ButtonIcon.vue';
import { axe, toHaveNoViolations } from 'jest-axe';
expect.extend(toHaveNoViolations);
let wrapper;

describe('presentation/form/ButtonIcon.vue', () => {
  beforeAll(() => {
    wrapper = shallowMount(component, {
      propsData: {
        ariaLabel: 'btn icon',
        id: 'buttonicon',
        text: 'Magic',
      },
    });
  });

  it('props can be set', () => {
    expect(wrapper.find('#buttonicon').props()).toMatchObject({
      text: 'Magic',
    });
  });

  it('Checks snapshot', () => {
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
