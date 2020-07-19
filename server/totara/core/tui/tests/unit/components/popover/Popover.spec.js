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
 * @author Simon Chester <simon.chester@totaralearning.com>
 * @package totara_core
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
