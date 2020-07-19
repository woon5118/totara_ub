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

import { mount } from '@vue/test-utils';
import Progress from 'totara_core/components/progress/Progress';

const factory = propsData => {
  return mount(Progress, {
    propsData: {
      ...propsData,
    },
    mocks: {
      $str: x => x,
    },
  });
};

describe('Progress.vue', () => {
  it('render correctly', () => {
    const wrapper = factory({
      small: false,
      value: 500,
      min: 100,
      max: 1000,
      format: 'percent',
      completedText: true,
      hideBackground: true,
      showEmptyState: true,
    });
    expect(wrapper.element).toMatchSnapshot();
  });

  it('render completed context correctly', () => {
    const wrapper = factory({ value: 100, completedText: true });
    expect(wrapper.find('span').text()).toBe('completed');
  });
});
