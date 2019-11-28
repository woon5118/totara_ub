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
 * @author Matthias Bonk <matthias.bonk@totaralearning.com>
 * @package totara_core
 */

import { shallowMount } from '@vue/test-utils';
import component from 'pathway_manual/components/RatingComment';
let wrapper;

const props = {
  hasRating: true,
  attachedComment: 'Test comment',
};
const mocks = {
  $str: function() {
    return 'fff';
  },
};

describe('components/RatingComment.vue', () => {
  it('Checks snapshot', () => {
    wrapper = shallowMount(component, { mocks: mocks, propsData: props });
    expect(wrapper.element).toMatchSnapshot();
  });

  it('Checks attachedComment with and without content', () => {
    wrapper = shallowMount(component, { mocks: mocks, propsData: props });
    expect(wrapper.vm.inputComment).toEqual('Test comment');
    expect(wrapper.vm.commentIcon).toEqual('pathway_manual|comment-filled');

    wrapper = shallowMount(component, {
      mocks: mocks,
      propsData: {
        hasRating: true,
        attachedComment: '',
      },
    });
    expect(wrapper.vm.inputComment).toEqual('');
    expect(wrapper.vm.commentIcon).toEqual('pathway_manual|comment');
  });

  it('Checks updateComment method', () => {
    const vueHandler = jest.fn();
    const closeFn = jest.fn();
    const wrapper = shallowMount(component, {
      mocks: mocks,
      propsData: props,
      listeners: {
        'update-comment': vueHandler,
      },
    });
    wrapper.vm.updateComment('  test abc  ', closeFn);
    expect(vueHandler).toHaveBeenCalled();
    expect(vueHandler.mock.calls[0][0]).toBe('test abc');
    expect(closeFn).toHaveBeenCalled();
  });

  it('Checks deleteComment method', () => {
    const vueHandler = jest.fn();
    const closeFn = jest.fn();
    const wrapper = shallowMount(component, {
      mocks: mocks,
      propsData: props,
      listeners: {
        'update-comment': vueHandler,
      },
    });
    wrapper.vm.deleteComment(closeFn);
    expect(vueHandler).toHaveBeenCalled();
    expect(vueHandler.mock.calls[0][0]).toBe('');
    expect(closeFn).toHaveBeenCalled();
  });
});
