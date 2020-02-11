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
 * @package totara_competency
 */

import { shallowMount } from '@vue/test-utils';
import component from 'totara_competency/components/RatingScaleOverview';
import { mocks } from './mocks';

const props = {
  reverseValues: false,
  scale: {
    values: [
      {
        id: '6',
        name: 'Extremely competent',
        proficient: true,
        description: '<b>No doubt this fella is competent</b>',
      },
      {
        id: '7',
        name: 'Competent',
        proficient: true,
        description: '<i>There is some merit co call it competent</i>',
      },
      {
        id: '8',
        name: 'Competent on Tuesdays',
        proficient: true,
        description: 'Competent, but only on Tuesdays, do not ask why.',
      },
      {
        id: '9',
        name: 'Not competent on Tuesdays',
        proficient: true,
        description: 'Not competent, but only on Tuesdays, do not ask why.',
      },
      {
        id: '10',
        name: 'Below average',
        proficient: false,
        description:
          'We can not call it competent for just yet, maybe wait for Tuesday.',
      },
      {
        id: '11',
        name: 'Not competent',
        proficient: false,
        description: 'Why does this value even exist?',
      },
    ],
  },
};

describe('components/RatingScaleOverview.vue', () => {
  it('Checks snapshot - with descriptions', () => {
    let wrapper = shallowMount(component, {
      mocks: mocks,
      propsData: Object.assign(props, {
        showDescriptions: true,
      }),
    });
    expect(wrapper.element).toMatchSnapshot('withDescriptions');
  });
  it('Checks snapshot - without descriptions', () => {
    let wrapper = shallowMount(component, {
      mocks: mocks,
      propsData: Object.assign(props, {
        showDescriptions: false,
      }),
    });
    expect(wrapper.element).toMatchSnapshot('withoutDescriptions');
  });
});
