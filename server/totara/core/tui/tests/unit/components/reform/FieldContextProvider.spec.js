/*
 * This file is part of Totara Learn
 *
 * Copyright (C) 2020 onwards Totara Learning Solutions LTD
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
import FieldContextProvider from 'totara_core/components/reform/FieldContextProvider';

const FieldContextReceiver = {
  inject: ['reformFieldContext'],
  render: () => null,
};

describe('FieldContextProvider', () => {
  it('provides context to field', () => {
    const wrapper = mount(FieldContextProvider, {
      propsData: {
        id: 'test-id',
        labelId: 'test-label-id',
      },
      scopedSlots: {
        default() {
          return this.$createElement(FieldContextReceiver);
        },
      },
    });

    const receiver = wrapper.find(FieldContextReceiver).vm;

    expect(receiver.reformFieldContext.getId()).toBe('test-id');
    expect(receiver.reformFieldContext.getLabelId()).toBe('test-label-id');
  });
});
