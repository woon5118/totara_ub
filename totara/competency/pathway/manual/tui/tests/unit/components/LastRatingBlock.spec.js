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
 * @author Mark Metcalfe <mark.metcalfe@totaralearning.com>
 * @package pathway_manual
 */

import component from 'pathway_manual/components/LastRatingBlock';
import { mocks } from './mocks';
import { shallowMount } from '@vue/test-utils';

const props = {
  currentUserId: 2,
};
const todayDate = new Date();

describe('components/LastRatingBlock.vue', () => {
  it('Checks snapshot - for same user', () => {
    let wrapper = shallowMount(component, {
      mocks: mocks,
      propsData: Object.assign(props, {
        latestRating: {
          rater: { id: props.currentUserId.toString(), fullname: 'Same User' },
          date: '30 January 2020',
          date_iso8601: '2020-01-30T16:14:42+1300',
          scale_value: { id: '5', name: 'Not competent' },
        },
      }),
    });
    expect(wrapper.element).toMatchSnapshot('sameUser');
  });
  it('Checks snapshot - for different user', () => {
    let wrapper = shallowMount(component, {
      mocks: mocks,
      propsData: Object.assign(props, {
        latestRating: {
          rater: { id: '3', fullname: 'Different User' },
          date: '30 January 2020',
          date_iso8601: '2020-01-30T16:14:42+1300',
          scale_value: { id: '5', name: 'Not competent' },
        },
      }),
    });
    expect(wrapper.element).toMatchSnapshot('differentUser');
  });
  it('Checks snapshot - for date that is today', () => {
    let wrapper = shallowMount(component, {
      mocks: mocks,
      propsData: Object.assign(props, {
        latestRating: {
          rater: { id: '3', fullname: 'Different User' },
          date: '30 January 2020',
          date_iso8601: todayDate,
          scale_value: { id: '5', name: 'Not competent' },
        },
      }),
    });
    expect(wrapper.element).toMatchSnapshot('dateToday');
  });
  it('Checks snapshot - for empty scale value', () => {
    let wrapper = shallowMount(component, {
      mocks: mocks,
      propsData: Object.assign(props, {
        latestRating: {
          rater: { id: '3', fullname: 'Different User' },
          date: '30 January 2020',
          date_iso8601: todayDate,
          scale_value: null,
        },
      }),
    });
    expect(wrapper.element).toMatchSnapshot('emptyScaleValue');
  });
});
