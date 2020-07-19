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

import { mount } from '@vue/test-utils';
import component from 'totara_core/components/form/Radio.vue';

let wrapper;
const selectHandler = jest.fn();

const passthroughProps = {
  id: 'a',
  autocomplete: true,
  autofocus: true,
  checked: false,
  disabled: true,
  name: 'n',
  readonly: true,
  required: true,
  value: 'v',
};

describe('presentation/form/Radio.vue', () => {
  beforeAll(() => {
    wrapper = mount(component, {
      propsData: Object.assign({ label: 'Some radio' }, passthroughProps),
      listeners: {
        select: selectHandler,
      },
    });
    selectHandler.mockClear();
  });

  it('checks snapshot', () => {
    expect(wrapper.element).toMatchSnapshot();
  });
});
