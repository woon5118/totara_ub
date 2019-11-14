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

import Vue from 'vue';
import { shallowMount, createWrapper } from '@vue/test-utils';
import Modal from 'totara_core/components/modal/Modal';

let wrapper;
let modalWrapper;

describe('Modal', () => {
  beforeEach(() => {
    wrapper = shallowMount(Modal, {
      slots: {
        default: ['content'],
        buttons: ['buttons'],
      },
      mocks: {
        $id: x => 'id-' + x,
        $str: (x, y) => `[[${x}, ${y}]]`,
      },
      propsData: {
        title: 'Title',
      },
    });
    modalWrapper = createWrapper(wrapper.vm.$refs.modal);
  });

  it('adds modal to body', async () => {
    expect(document.body.contains(modalWrapper.element)).toBeFalse();
    wrapper.setProps({ open: true });
    await Vue.nextTick();
    expect(document.body.contains(modalWrapper.element)).toBeTrue();
  });

  it('checks snapshot', () => {
    expect(modalWrapper.element).toMatchSnapshot();
  });
});
