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
 * @author Arshad Anwer <arshad.anwer@totaralearning.com>
 * @package totara_core
 */

import { mount } from '@vue/test-utils';
import component from 'totara_core/components/virtualscroll/VirtualScroll';
import { axe, toHaveNoViolations } from 'jest-axe';
expect.extend(toHaveNoViolations);

let wrapper;

const props = {
  isLoading: false,
  dataList: [
    {
      id: '1',
      name: 'test 1',
    },
    {
      id: '2',
      name: 'test 2',
    },
  ],
  dataKey: 'id',
  ariaLabel: 'List',
};

describe('VirtualScroll', () => {
  beforeAll(() => {
    wrapper = mount(component, {
      propsData: props,
      scopedSlots: {
        item: function(props) {
          return this.$createElement(
            'article',
            {
              attrs: {
                'aria-labelledby': `article-${props.item.id}`,
                'aria-setsize': props.setSize,
                'aria-posinset': props.posInSet,
                tabindex: '0',
              },
            },
            [
              this.$createElement(
                'div',
                { attrs: { id: `article-${props.item.id}` } },
                'hello'
              ),
            ]
          );
        },

        footer() {
          return this.$createElement('div', { class: 'loader' }, '');
        },
      },
    });
  });

  it('Checks snapshot', () => {
    expect(wrapper.element).toMatchSnapshot();
  });

  it('should not have any accessibility violations', async () => {
    const results = await axe(wrapper.element, {
      rules: {
        region: { enabled: false },
      },
    });
    expect(results).toHaveNoViolations();
  });
});
