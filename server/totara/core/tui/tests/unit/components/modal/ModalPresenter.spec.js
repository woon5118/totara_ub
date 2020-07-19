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
import { mount } from '@vue/test-utils';
import ModalPresenter from 'totara_core/components/modal/ModalPresenter';

const ModalStub = {
  inject: ['modal-presenter-interface'],
  render(h) {
    return h('div', ["i'm a modal"]);
  },
};

let wrapper;
let handleRequestClose;

const stubWrapper = () => wrapper.find(ModalStub);
const mpi = () => wrapper.find(ModalStub).vm['modal-presenter-interface'];

describe('ModalPresenter', () => {
  beforeEach(() => {
    handleRequestClose = jest.fn();
    wrapper = mount(ModalPresenter, {
      slots: {
        default: [ModalStub],
      },
      listeners: {
        'request-close': handleRequestClose,
      },
    });
  });

  it('mounts and unmounts slot content', async () => {
    expect(wrapper.find(ModalStub).exists()).toBeFalse();
    wrapper.setProps({ open: true });
    await Vue.nextTick();
    expect(wrapper.find(ModalStub).exists()).toBeTrue();
    wrapper.setProps({ open: false });
    await Vue.nextTick();
    expect(wrapper.find(ModalStub).exists()).toBeFalse();
  });

  it('waits until modal has closed before unmounting it', async () => {
    wrapper.setProps({ open: true });
    await Vue.nextTick();
    expect(wrapper.find(ModalStub).exists()).toBeTrue();
    mpi().setIsOpen(true);
    wrapper.setProps({ open: false });
    await Vue.nextTick();
    expect(wrapper.find(ModalStub).exists()).toBeTrue();
    mpi().setIsOpen(false);
    await Vue.nextTick();
    expect(wrapper.find(ModalStub).exists()).toBeFalse();
  });

  it('passes request close event from interface through', async () => {
    wrapper.setProps({ open: true });
    expect(handleRequestClose).toHaveBeenCalledTimes(0);

    await Vue.nextTick();
    mpi().requestClose();
    expect(handleRequestClose).toHaveBeenCalledTimes(1);

    mpi().requestClose({ result: 3 });
    expect(handleRequestClose).toHaveBeenCalledWith({ result: 3 });
  });

  it('re-emits request close event from slot content', async () => {
    wrapper.setProps({ open: true });
    await Vue.nextTick();
    expect(handleRequestClose).toHaveBeenCalledTimes(0);

    stubWrapper().vm.$emit('request-close');

    expect(handleRequestClose).toHaveBeenCalledTimes(1);

    stubWrapper().vm.$emit('request-close', { result: 5 });
    expect(handleRequestClose).toHaveBeenCalledWith({ result: 5 });
  });
});
