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

import { shallowMount, mount } from '@vue/test-utils';
import Arrow from 'totara_core/components/decor/Arrow';
import { isRtl } from 'totara_core/i18n';

jest.mock('totara_core/i18n');

describe('Arrow', () => {
  beforeAll(() => {
    isRtl.mockReset().mockReturnValue(false);
  });

  it('matches snapshot', () => {
    const wrapper = shallowMount(Arrow, { propsData: { side: 'top' } });
    expect(wrapper.element).toMatchSnapshot();
  });

  it('RTL works as expected', () => {
    let wrapper = mount(Arrow, { propsData: { side: 'top', distance: 12 } });
    let props = wrapper.find('.tui-arrow').attributes().style;
    expect(props).toEqual('left: 12px;');

    wrapper = mount(Arrow, { propsData: { side: 'bottom', distance: 13 } });
    props = wrapper.find('.tui-arrow').attributes().style;
    expect(props).toEqual('left: 13px;');

    wrapper = mount(Arrow, { propsData: { side: 'left', distance: 14 } });
    props = wrapper.find('.tui-arrow').attributes().style;
    expect(props).toEqual('top: 14px;');

    wrapper = mount(Arrow, { propsData: { side: 'right', distance: 15 } });
    props = wrapper.find('.tui-arrow').attributes().style;
    expect(props).toEqual('top: 15px;');

    // And now Rtl
    isRtl.mockImplementation(() => true);
    wrapper = shallowMount(Arrow, { propsData: { side: 'top', distance: 16 } });
    props = wrapper.find('.tui-arrow').attributes().style;
    expect(props).toEqual('right: 16px;');

    wrapper = mount(Arrow, { propsData: { side: 'bottom', distance: 17 } });
    props = wrapper.find('.tui-arrow').attributes().style;
    expect(props).toEqual('right: 17px;');

    wrapper = mount(Arrow, { propsData: { side: 'left', distance: 18 } });
    props = wrapper.find('.tui-arrow').attributes().style;
    expect(props).toEqual('top: 18px;');

    wrapper = mount(Arrow, { propsData: { side: 'right', distance: 19 } });
    props = wrapper.find('.tui-arrow').attributes().style;
    expect(props).toEqual('top: 19px;');
  });
});
