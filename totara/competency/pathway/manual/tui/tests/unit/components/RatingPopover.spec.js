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
 * @author Matthias Bonk <matthias.bonk@totaralearning.com>
 * @package pathway_manual
 */

import { shallowMount } from '@vue/test-utils';
import component from 'pathway_manual/components/RatingPopover';
let wrapper;

const props = {
  scale: {
    values: [
      {
        id: '123',
        name: 'Competent',
      },
    ],
  },
  compId: '321',
  scaleValueId: '1',
  comment: 'Test comment',
};
const mocks = {
  $str: function() {
    return 'fff';
  },
};

describe('components/RatingPopover.vue', () => {
  it('Checks snapshot', () => {
    wrapper = shallowMount(component, { mocks: mocks, propsData: props });
    expect(wrapper.element).toMatchSnapshot();
  });

  it('Checks updateRating method', () => {
    const vueHandler = jest.fn();
    const closeFn = jest.fn();
    const wrapper = shallowMount(component, {
      mocks: mocks,
      propsData: props,
      listeners: {
        'update-rating': vueHandler,
      },
    });
    wrapper.vm.updateRating('321', '  test abc  ', closeFn);
    expect(vueHandler).toHaveBeenCalled();
    expect(vueHandler.mock.calls[0][0]).toEqual({
      scale_value_id: '321',
      comment: 'test abc',
    });
    expect(closeFn).toHaveBeenCalled();
  });

  it('Checks deleteRating method', () => {
    const vueHandler = jest.fn();
    const closeFn = jest.fn();
    const wrapper = shallowMount(component, {
      mocks: mocks,
      propsData: props,
      listeners: {
        'delete-rating': vueHandler,
      },
    });
    wrapper.vm.deleteRating(closeFn);
    expect(vueHandler).toHaveBeenCalled();
    expect(closeFn).toHaveBeenCalled();
  });
});
